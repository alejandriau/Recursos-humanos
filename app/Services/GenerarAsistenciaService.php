<?php
// app/Services/GenerarAsistenciaService.php

namespace App\Services;

use App\Models\AsistenciaDiaria;
use App\Models\AsistenciaMarca;
use App\Models\MarcacionBiometrica;
use App\Models\Persona;
use App\Models\PersonaHorario;
use App\Models\Salida;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerarAsistenciaService
{
    /**
     * Ventana máxima (en minutos) para considerar que una marcación
     * corresponde a un checkpoint esperado. Evita que una marcación de
     * las 8:05am se le asigne por error a un checkpoint de las 2pm.
     */
    protected int $ventanaMaximaMinutos = 240;

    /**
     * Genera/regenera la asistencia de TODAS las personas activas para una fecha.
     */
    public function generarParaFecha(string $fecha): array
    {
        $fecha = Carbon::parse($fecha)->startOfDay();
        $personas = Persona::where('estado', true)->get();

        $procesadas = 0;
        $errores = 0;

        foreach ($personas as $persona) {
            try {
                $this->procesarPersonaFecha($persona, $fecha->copy());
                $procesadas++;
            } catch (\Exception $e) {
                $errores++;
                Log::error("Error generando asistencia persona {$persona->id} fecha {$fecha->toDateString()}: " . $e->getMessage());
            }
        }

        return ['fecha' => $fecha->toDateString(), 'procesadas' => $procesadas, 'errores' => $errores];
    }

    /**
     * Genera/regenera la asistencia de un rango de fechas (útil para
     * reprocesar después de importar marcaciones atrasadas).
     */
    public function generarParaRango(string $fechaInicio, string $fechaFin): array
    {
        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin = Carbon::parse($fechaFin)->startOfDay();
        $resumen = [];

        while ($inicio->lte($fin)) {
            $resumen[] = $this->generarParaFecha($inicio->toDateString());
            $inicio->addDay();
        }

        return $resumen;
    }

    /**
     * Procesa un solo día de una sola persona. Es idempotente: se puede
     * volver a correr (ej. tras importar marcaciones nuevas o aprobar una
     * salida) y va a recalcular desde cero ese día.
     */
    public function procesarPersonaFecha(Persona $persona, Carbon $fecha): ?AsistenciaDiaria
    {
        $horario = $this->getHorarioVigente($persona, $fecha);

        if (!$horario) {
            // Sin horario asignado: no se puede evaluar, se omite.
            return null;
        }

        $horarioDia = $horario->dias->firstWhere('dia_semana', $fecha->dayOfWeek);

        return DB::transaction(function () use ($persona, $fecha, $horario, $horarioDia) {

            // Recalcular desde cero: borra el resultado previo de ese día
            $asistencia = AsistenciaDiaria::updateOrCreate(
                ['persona_id' => $persona->id, 'fecha' => $fecha->toDateString()],
                ['horario_id' => $horario->id]
            );
            $asistencia->marcas()->delete();

            if (!$horarioDia) {
                // No le corresponde marcar este día (ej. fin de semana)
                $asistencia->update([
                    'estado' => 'no_laborable',
                    'minutos_tardanza' => 0,
                    'total_marcas_esperadas' => 0,
                    'total_marcas_cumplidas' => 0,
                    'total_marcas_justificadas' => 0,
                    'total_marcas_injustificadas' => 0,
                    'procesado_en' => now(),
                ]);
                return $asistencia;
            }

            $checkpoints = $horarioDia->checkpoints();

            // Todas las marcaciones biométricas de la persona ese día (de
            // cualquier dispositivo), ordenadas cronológicamente.
            $marcasDisponibles = MarcacionBiometrica::where('persona_id', $persona->id)
                ->whereDate('fecha_hora', $fecha->toDateString())
                ->orderBy('fecha_hora')
                ->get()
                ->map(fn($m) => ['modelo' => $m, 'usada' => false])
                ->values();

            $minutosTardanzaTotal = 0;
            $cumplidas = 0;
            $justificadas = 0;
            $injustificadas = 0;

            foreach ($checkpoints as $tipoMarca => $horaEsperada) {
                $esperadaDT = Carbon::parse($fecha->toDateString() . ' ' . $horaEsperada);

                $indexMejor = null;
                $mejorDiff = null;

                foreach ($marcasDisponibles as $idx => $item) {
                    if ($item['usada']) continue;

                    $diff = abs($item['modelo']->fecha_hora->diffInMinutes($esperadaDT));
                    if ($mejorDiff === null || $diff < $mejorDiff) {
                        $mejorDiff = $diff;
                        $indexMejor = $idx;
                    }
                }

                if ($indexMejor !== null && $mejorDiff <= $this->ventanaMaximaMinutos) {
                    // Encontramos una marcación real para este checkpoint
                    $marcasDisponibles[$indexMejor]['usada'] = true;
                    $marcacion = $marcasDisponibles[$indexMejor]['modelo'];

                    $esEntrada = str_starts_with($tipoMarca, 'entrada');
                    $tolerancia = $esEntrada
                        ? $horario->tolerancia_entrada_minutos
                        : $horario->tolerancia_salida_minutos;

                    $diferenciaReal = $marcacion->fecha_hora->diffInMinutes($esperadaDT, false) * -1;
                    // diferenciaReal > 0 => llegó/salió después de lo esperado

                    $esTardanza = $esEntrada
                        ? $diferenciaReal > $tolerancia
                        : $diferenciaReal < -$tolerancia; // salió antes de tiempo

                    if ($esTardanza) {
                        $minutosTardanzaTotal += abs($diferenciaReal);
                    }

                    AsistenciaMarca::create([
                        'asistencia_diaria_id' => $asistencia->id,
                        'tipo_marca' => $tipoMarca,
                        'hora_esperada' => $horaEsperada,
                        'hora_real' => $marcacion->fecha_hora->format('H:i:s'),
                        'marcacion_id' => $marcacion->id,
                        'estado' => $esTardanza ? 'tardanza' : 'puntual',
                        'diferencia_minutos' => $diferenciaReal,
                    ]);

                    $cumplidas++;
                    continue;
                }

                // No hay marcación para este checkpoint: buscar si una
                // salida aprobada (comisión, licencia, médica, vacación...)
                // cubre este momento específico del día.
                $salidaQueCubre = $this->buscarSalidaQueCubre($persona->id, $esperadaDT);

                AsistenciaMarca::create([
                    'asistencia_diaria_id' => $asistencia->id,
                    'tipo_marca' => $tipoMarca,
                    'hora_esperada' => $horaEsperada,
                    'hora_real' => null,
                    'marcacion_id' => null,
                    'estado' => $salidaQueCubre ? 'faltante_justificada' : 'faltante_injustificada',
                    'diferencia_minutos' => null,
                    'salida_id' => $salidaQueCubre?->id,
                ]);

                if ($salidaQueCubre) {
                    $justificadas++;
                } else {
                    $injustificadas++;
                }
            }

            $estadoFinal = match (true) {
                $injustificadas > 0 && $justificadas > 0 => 'incompleto',
                $injustificadas > 0 => 'falta_injustificada',
                $justificadas > 0 => 'falta_justificada',
                $minutosTardanzaTotal > 0 => 'tardanza',
                default => 'completo',
            };

            $asistencia->update([
                'estado' => $estadoFinal,
                'minutos_tardanza' => $minutosTardanzaTotal,
                'total_marcas_esperadas' => count($checkpoints),
                'total_marcas_cumplidas' => $cumplidas,
                'total_marcas_justificadas' => $justificadas,
                'total_marcas_injustificadas' => $injustificadas,
                'procesado_en' => now(),
            ]);

            return $asistencia;
        });
    }

    /**
     * Busca una salida (comisión, licencia, médica, vacación, etc.) que:
     * - esté aprobada por jefe Y RRHH (o el campo 'estado' consolidado = aprobado)
     * - cubra el instante exacto del checkpoint que falta marcar
     *
     * Soporta tanto salidas de día completo (sin horasal/horaret, cantidad
     * en días) como salidas de horas específicas (con horasal/horaret) —
     * por ejemplo "sale a comisión a las 14:00 y no vuelve" solo justifica
     * el checkpoint de salida_tarde, no el de entrada_manana.
     */
    protected function buscarSalidaQueCubre(int $personaId, Carbon $momentoEsperado): ?Salida
    {
        $candidatas = Salida::where('persona_id', $personaId)
            ->where('estado', 'aprobado')
            ->whereDate('fechasal', '<=', $momentoEsperado->toDateString())
            ->whereDate('fecharet', '>=', $momentoEsperado->toDateString())
            ->get();

        foreach ($candidatas as $salida) {
            $inicio = $salida->horasal
                ? Carbon::parse($salida->fechasal->toDateString() . ' ' . $salida->horasal)
                : Carbon::parse($salida->fechasal)->startOfDay();

            $fin = $salida->horaret
                ? Carbon::parse($salida->fecharet->toDateString() . ' ' . $salida->horaret)
                : Carbon::parse($salida->fecharet)->endOfDay();

            if ($momentoEsperado->betweenIncluded($inicio, $fin)) {
                return $salida;
            }
        }

        return null;
    }

    protected function getHorarioVigente(Persona $persona, Carbon $fecha)
    {
        $asignacion = PersonaHorario::where('persona_id', $persona->id)
            ->where('activo', true)
            ->whereDate('fecha_inicio', '<=', $fecha->toDateString())
            ->where(function ($q) use ($fecha) {
                $q->whereNull('fecha_fin')
                  ->orWhereDate('fecha_fin', '>=', $fecha->toDateString());
            })
            ->orderByDesc('fecha_inicio')
            ->with('horario.dias')
            ->first();

        return $asignacion?->horario;
    }
}
