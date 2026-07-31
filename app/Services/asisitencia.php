<?php

namespace App\Services;

use App\Models\AsistenciaDiaria;
use App\Models\AsistenciaMarca;
use App\Models\HorarioDia;
use App\Models\MarcacionBiometrica;
use App\Models\Persona;
use App\Models\PersonaHorario;
use App\Models\Salida;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerarAsistenciaService
{
    protected int $ventanaMaximaMinutos = 240; // 4 horas

    /**
     * Genera asistencia de TODAS las personas activas para una fecha.
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

        return [
            'fecha' => $fecha->toDateString(),
            'procesadas' => $procesadas,
            'errores' => $errores,
        ];
    }

    /**
     * Genera asistencia para un rango de fechas.
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
     * Procesa un solo día de una sola persona. Es idempotente.
     */
    public function procesarPersonaFecha(Persona $persona, Carbon $fecha): ?AsistenciaDiaria
    {
        $horario = $this->getHorarioVigente($persona, $fecha);

        if (!$horario) {
            Log::warning("Sin horario vigente: persona {$persona->id} fecha {$fecha->toDateString()}");
            return null;
        }

        /** @var HorarioDia|null $horarioDia */
        $horarioDia = $horario->dias->firstWhere('dia_semana', $fecha->dayOfWeek);

        return DB::transaction(function () use ($persona, $fecha, $horario, $horarioDia) {

            $asistencia = AsistenciaDiaria::updateOrCreate(
                ['persona_id' => $persona->id, 'fecha' => $fecha->toDateString()],
                ['horario_id' => $horario->id]
            );

            // Borrar marcas anteriores para recalcular desde cero
            $asistencia->marcas()->delete();

            if (!$horarioDia || empty($horarioDia->checkpoints())) {
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

            // ============================================================
            // CORRECCIÓN CRÍTICA: Usar whereBetween en lugar de whereDate
            // y convertir a objetos stdClass mutables para que 'usada' persista
            // ============================================================
            $inicioDia = $fecha->copy()->startOfDay();
            $finDia = $fecha->copy()->endOfDay();

            $marcasRaw = MarcacionBiometrica::where('persona_id', $persona->id)
                ->whereBetween('fecha_hora', [$inicioDia, $finDia])
                ->orderBy('fecha_hora')
                ->get();

            // Log de debug (temporal, podés sacarlo después)
            if ($marcasRaw->isEmpty()) {
                Log::warning("DEBUG: Sin marcaciones para persona {$persona->id} en {$fecha->toDateString()}");
            } else {
                Log::info("DEBUG: Persona {$persona->id} en {$fecha->toDateString()} tiene {$marcasRaw->count()} marcaciones");
            }

            // Convertir a stdClass mutables (NO arrays) para que 'usada' persista
            $marcasDisponibles = $marcasRaw->map(function ($m) {
                $obj = new \stdClass();
                $obj->modelo = $m;
                $obj->usada = false;
                return $obj;
            })->values();

            $minutosTardanzaTotal = 0;
            $cumplidas = 0;
            $justificadas = 0;
            $injustificadas = 0;

            foreach ($checkpoints as $tipoMarca => $horaEsperada) {
                // CORRECCIÓN: Crear la fecha esperada en timezone Bolivia
                $esperadaDT = Carbon::parse($fecha->toDateString() . ' ' . $horaEsperada, 'America/La_Paz');

                $indexMejor = null;
                $mejorDiff = null;

                foreach ($marcasDisponibles as $idx => $item) {
                    if ($item->usada) continue;

                    // CORRECCIÓN: Asegurar que ambas fechas estén comparables
                    $diff = abs($item->modelo->fecha_hora->diffInMinutes($esperadaDT));
                    
                    if ($mejorDiff === null || $diff < $mejorDiff) {
                        $mejorDiff = $diff;
                        $indexMejor = $idx;
                    }
                }

                if ($indexMejor !== null && $mejorDiff <= $this->ventanaMaximaMinutos) {
                    $marcasDisponibles[$indexMejor]->usada = true; // ← AHORA SÍ PERSISTE
                    $marcacion = $marcasDisponibles[$indexMejor]->modelo;

                    $esEntrada = str_starts_with($tipoMarca, 'entrada');
                    $tolerancia = $esEntrada
                        ? ($horario->tolerancia_entrada_minutos ?? 0)
                        : ($horario->tolerancia_salida_minutos ?? 0);

                    // CORRECCIÓN: Calcular diferencia correctamente
                    // Positivo = llegó/salió DESPUÉS de lo esperado
                    // Negativo = llegó/salió ANTES de lo esperado
                    // ✅ CORREGIDO: desde lo esperado hasta lo real
                    $diferenciaReal = $esperadaDT->diffInMinutes($marcacion->fecha_hora, false);

                    $esTardanza = false;

                    if ($esEntrada) {
                        // Entrada: positivo = llegó DESPUÉS de la esperada = TARDANZA
                        $esTardanza = $diferenciaReal > $tolerancia;
                        if ($esTardanza) {
                            $minutosTardanzaTotal += $diferenciaReal;
                        }
                    } else {
                        // Salida: negativo = salió ANTES de la esperada = SALIDA ANTICIPADA
                        $esTardanza = $diferenciaReal < -$tolerancia;
                        if ($esTardanza) {
                            $minutosTardanzaTotal += abs($diferenciaReal);
                        }
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

                // No hay marcación dentro de la ventana: buscar salida justificada
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

            // Determinar estado final del día
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

            // Log resumen del día
            Log::info("DEBUG: Persona {$persona->id} {$fecha->toDateString()} | Estado: {$estadoFinal} | Cumplidas: {$cumplidas} | Injustificadas: {$injustificadas} | Tardanza: {$minutosTardanzaTotal} min");

            return $asistencia;
        });
    }

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

    protected function getHorarioVigente(Persona $persona, Carbon $fecha): ?\App\Models\Horario
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