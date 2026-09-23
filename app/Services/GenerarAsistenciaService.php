<?php

namespace App\Services;

use App\Models\AsistenciaDiaria;
use App\Models\AsistenciaMarca;
use App\Models\Feriado;
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

        // Se busca el feriado una sola vez por fecha, no por cada persona.
        $feriado = $this->getFeriado($fecha);

        $procesadas = 0;
        $omitidas = 0;
        $errores = 0;

        foreach ($personas as $persona) {
            try {
                $asistencia = $this->procesarPersonaFecha($persona, $fecha->copy(), $feriado);
                $asistencia ? $procesadas++ : $omitidas++;
            } catch (\Exception $e) {
                $errores++;
                Log::error("Error generando asistencia persona {$persona->id} fecha {$fecha->toDateString()}: " . $e->getMessage());
            }
        }

        return [
            'fecha' => $fecha->toDateString(),
            'procesadas' => $procesadas,
            'omitidas' => $omitidas, // días fuera del horario de la persona: no se genera registro
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
     *
     * Si la persona no tiene horario vigente para la fecha, o el horario no
     * define checkpoints para ese día de la semana (día no laborable según
     * su horario), NO se genera ningún registro de asistencia (y se borra
     * uno previo si existiera, por si el horario cambió).
     */
    public function procesarPersonaFecha(Persona $persona, Carbon $fecha, ?Feriado $feriado = null): ?AsistenciaDiaria
    {
        $horario = $this->getHorarioVigente($persona, $fecha);
        $feriado ??= $this->getFeriado($fecha);

        // ============================================================
        // FERIADO: SIEMPRE se genera asistencia, con o sin horario.
        // ============================================================
        if ($feriado) {
            return $this->procesarFeriado($persona, $fecha, $horario, $feriado);
        }

        // ----- Día normal -----
        $horarioDia = $horario?->dias->firstWhere('dia_semana', $fecha->dayOfWeek);
        $checkpoints = ($horarioDia && !empty($horarioDia->checkpoints()))
            ? $horarioDia->checkpoints()
            : [];

        if (empty($checkpoints)) {
            // Día no laborable normal: no se genera registro.
            $this->eliminarAsistenciaSiExiste($persona->id, $fecha);
            return null;
        }

        return DB::transaction(function () use ($persona, $fecha, $horario, $checkpoints) {

            $asistencia = AsistenciaDiaria::updateOrCreate(
                ['persona_id' => $persona->id, 'fecha' => $fecha->toDateString()],
                ['horario_id' => $horario?->id]
            );

            $asistencia->marcas()->delete();

            // ============================================================
            // DESCARGA DE MARCACIONES DEL DÍA
            // ============================================================
            $inicioDia = $fecha->copy()->startOfDay();
            $finDia    = $fecha->copy()->endOfDay();

            $marcasRaw = MarcacionBiometrica::where('persona_id', $persona->id)
                ->whereBetween('fecha_hora', [$inicioDia, $finDia])
                ->orderBy('fecha_hora')
                ->get();

            $marcasDisponibles = $marcasRaw->map(function ($m) {
                $obj = new \stdClass();
                $obj->modelo = $m;
                $obj->usada = false;
                return $obj;
            })->values();

            $esDiaEnCurso = $fecha->isToday();
            $ultimaHoraEsperada = collect($checkpoints)->last();
            $horaCierreCalculada = Carbon::parse(
                $fecha->toDateString() . ' ' . $ultimaHoraEsperada,
                'America/La_Paz'
            )->addMinutes($this->ventanaMaximaMinutos + 60);

            $diaYaCerrado = !$esDiaEnCurso || now()->gte($horaCierreCalculada);

            // Tipo de checkpoint que representa la salida final del día
            // (la última en orden cronológico). Solo ahí cuenta el "tiempo
            // trabajado después de la salida esperada" como hora extra.
            $ultimoTipoMarca = array_key_last($checkpoints);

            $minutosTardanzaTotal = 0;
            $cumplidas = 0;
            $justificadas = 0;
            $injustificadas = 0;
            $pendientes = 0;

            foreach ($checkpoints as $tipoMarca => $horaEsperada) {

                $esperadaDT = Carbon::parse(
                    $fecha->toDateString() . ' ' . $horaEsperada,
                    'America/La_Paz'
                );

                $indexMejor = null;
                $mejorDiff = null;

                foreach ($marcasDisponibles as $idx => $item) {
                    if ($item->usada) continue;
                    $diff = abs($item->modelo->fecha_hora->diffInMinutes($esperadaDT));
                    if ($mejorDiff === null || $diff < $mejorDiff) {
                        $mejorDiff = $diff;
                        $indexMejor = $idx;
                    }
                }

                if ($indexMejor !== null && $mejorDiff <= $this->ventanaMaximaMinutos) {
                    $marcasDisponibles[$indexMejor]->usada = true;
                    $marcacion = $marcasDisponibles[$indexMejor]->modelo;

                    $esEntrada  = str_starts_with($tipoMarca, 'entrada');
                    $tolerancia = $this->getTolerancia($horario, $tipoMarca);

                    $diferenciaReal = $esperadaDT->diffInMinutes($marcacion->fecha_hora, false);

                    $esTardanza = false;
                    $diferenciaEfectiva = 0;

                    if ($esEntrada) {
                        $exceso = $diferenciaReal - $tolerancia;
                        if ($exceso > 0) {
                            $esTardanza = true;
                            $minutosTardanzaTotal += $exceso;
                            $diferenciaEfectiva = $exceso;
                        }
                    } else {
                        if ($diferenciaReal < 0) {
                            // Salió antes de lo esperado: tardanza (como antes)
                            $exceso = abs($diferenciaReal) - $tolerancia;
                            if ($exceso > 0) {
                                $esTardanza = true;
                                $minutosTardanzaTotal += $exceso;
                                $diferenciaEfectiva = -$exceso;
                            }
                        } elseif ($diferenciaReal > 0 && $tipoMarca === $ultimoTipoMarca) {
                            // Se quedó después de la última salida del día: hora extra.
                            // No se acumula en ningún total; queda solo en
                            // diferencia_minutos de esta marca (positivo = extra).
                            $diferenciaEfectiva = $diferenciaReal;
                        }
                        // Si sale tarde en un checkpoint que NO es el último
                        // (ej. salida_manana antes del almuerzo) no se
                        // considera ni tardanza ni extra.
                    }

                    AsistenciaMarca::create([
                        'asistencia_diaria_id' => $asistencia->id,
                        'tipo_marca' => $tipoMarca,
                        'hora_esperada' => $horaEsperada,
                        'hora_real' => $marcacion->fecha_hora->format('H:i:s'),
                        'marcacion_id' => $marcacion->id,
                        'estado' => $esTardanza ? 'tardanza' : 'puntual',
                        'diferencia_minutos' => $diferenciaEfectiva,
                    ]);

                    $cumplidas++;
                    continue;
                }

                if (!$diaYaCerrado && $esDiaEnCurso) {
                    AsistenciaMarca::create([
                        'asistencia_diaria_id' => $asistencia->id,
                        'tipo_marca' => $tipoMarca,
                        'hora_esperada' => $horaEsperada,
                        'hora_real' => null,
                        'marcacion_id' => null,
                        'estado' => 'pendiente',
                        'diferencia_minutos' => null,
                        'salida_id' => null,
                    ]);
                    $pendientes++;
                } else {
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

                    if ($salidaQueCubre) $justificadas++;
                    else $injustificadas++;
                }
            }

            $estadoFinal = match (true) {
                $pendientes > 0 => 'pendiente',
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
     * Procesa un día feriado para una persona. SIEMPRE genera AsistenciaDiaria.
     *
     * Orden de resolución de checkpoints (2 o 4 marcas):
     *   1) Los del día exacto según su horario vigente.
     *   2) Los de cualquier otro día del mismo horario.
     *   3) Fallback mínimo: 2 marcas genéricas de feriado (sin hora).
     */
    protected function procesarFeriado(
            Persona $persona,
            Carbon $fecha,
            ?\App\Models\Horario $horario,
            Feriado $feriado
        ): AsistenciaDiaria {

        $horarioDia = $horario?->dias->firstWhere('dia_semana', $fecha->dayOfWeek);
        $checkpoints = ($horarioDia && !empty($horarioDia->checkpoints()))
            ? $horarioDia->checkpoints()
            : $this->resolverCheckpointsFeriado($horario);

        if (empty($checkpoints)) {
            // Fallback: no hay horario o no tiene días con checkpoints.
            // Igual se generan 2 marcas genéricas para que la asistencia quede
            // registrada como feriado y sume en justificaciones.
            $checkpoints = [
                'feriado_entrada' => null,
                'feriado_salida'  => null,
            ];

            Log::warning("Feriado sin checkpoints resolubles, usando fallback genérico", [
                'persona_id' => $persona->id,
                'fecha'      => $fecha->toDateString(),
                'feriado_id' => $feriado->id,
                'horario_id' => $horario?->id,
            ]);
        }

        return DB::transaction(function () use ($persona, $fecha, $horario, $checkpoints, $feriado) {

            $asistencia = AsistenciaDiaria::updateOrCreate(
                ['persona_id' => $persona->id, 'fecha' => $fecha->toDateString()],
                ['horario_id' => $horario?->id]
            );

            $asistencia->marcas()->delete();

            return $this->registrarFeriado($asistencia, $checkpoints, $feriado);
        });
    }
    /**
     * Cuando es feriado pero el día concreto no tiene checkpoints en el horario
     * (porque no es día laborable para esa persona), busca en el mismo horario
     * algún otro día que sí tenga checkpoints y usa esos.
     *
     * Así respeta el horario real vigente: continuo (2 marcas) o partido (4),
     * y si mañana cambia, cambia solo, sin tocar código.
     *
     * Devuelve [] si no hay horario o el horario no define ningún checkpoint.
     */
    protected function resolverCheckpointsFeriado(?\App\Models\Horario $horario): array
    {
        if (!$horario) return [];

        foreach ($horario->dias as $dia) {
            $cp = $dia->checkpoints();
            if (!empty($cp)) return $cp;
        }

        return [];
    }

    /**
     * Marca todos los checkpoints del día como justificados por feriado,
     * sin cruzar contra marcaciones biométricas.
     */
    protected function registrarFeriado(AsistenciaDiaria $asistencia, array $checkpoints, Feriado $feriado): AsistenciaDiaria
    {
        foreach ($checkpoints as $tipoMarca => $horaEsperada) {
            AsistenciaMarca::create([
                'asistencia_diaria_id' => $asistencia->id,
                'tipo_marca' => $tipoMarca,
                'hora_esperada' => $horaEsperada,
                'hora_real' => null,
                'marcacion_id' => null,
                'estado' => 'feriado',
                'diferencia_minutos' => null,
                'salida_id' => null,
                'feriado_id' => $feriado->id,
            ]);
        }

        $asistencia->update([
            'estado' => 'feriado',
            'minutos_tardanza' => 0,
            'total_marcas_esperadas' => count($checkpoints),
            'total_marcas_cumplidas' => 0,
            'total_marcas_justificadas' => count($checkpoints),
            'total_marcas_injustificadas' => 0,
            'procesado_en' => now(),
        ]);

        return $asistencia;
    }

    /**
     * Elimina un registro de asistencia (y sus marcas) si existiera, para
     * los casos en que el día ya no corresponde generar asistencia (p. ej.
     * cambió el horario y ese día dejó de ser laborable).
     */
    protected function eliminarAsistenciaSiExiste(int $personaId, Carbon $fecha): void
    {
        $asistencia = AsistenciaDiaria::where('persona_id', $personaId)
            ->where('fecha', $fecha->toDateString())
            ->first();

        if ($asistencia) {
            $asistencia->marcas()->delete();
            $asistencia->delete();
        }
    }

    /**
     * Resuelve la tolerancia (en minutos) a aplicar según el checkpoint
     * exacto del día, no solo si es "entrada" o "salida" en general.
     * Así, si mañana se activa tolerancia en la tarde, solo se configura
     * en el horario (tolerancia_entrada_tarde_minutos /
     * tolerancia_salida_tarde_minutos) y este método ya la toma en cuenta,
     * sin tocar código.
     */
    protected function getTolerancia(?\App\Models\Horario $horario, string $tipoMarca): int
    {
        if (!$horario) return 0;

        return match ($tipoMarca) {
            'entrada_manana', 'entrada' => $horario->tolerancia_entrada_manana_minutos ?? 0,
            'salida_manana' => $horario->tolerancia_salida_manana_minutos ?? 0,
            'entrada_tarde' => $horario->tolerancia_entrada_tarde_minutos ?? 0,
            'salida_tarde', 'salida' => $horario->tolerancia_salida_tarde_minutos ?? 0,
            default => 0,
        };
    }

    protected function getFeriado(Carbon $fecha): ?Feriado
    {
        return Feriado::whereDate('fechaf', $fecha->toDateString())->first();
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