<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero limpiar la tabla (opcional)
        // DB::table('asistencias')->truncate();

        $asistencias = [];
        $idPersona = 85;

        // Generar asistencias de enero a septiembre 2025 (días laborables)
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2025, 9, 30);

        $diasGenerados = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Solo días laborables (lunes a viernes) y excluir días de vacaciones aprobadas
            if ($date->isWeekday() && !$this->esDiaVacaciones($date)) {

                $estado = $this->determinarEstado($date);

                // Si está ausente o de vacaciones, no generar horarios
                if ($estado === 'ausente' || $estado === 'vacaciones') {
                    $asistencias[] = [
                        'idPersona' => $idPersona,
                        'fecha' => $date->format('Y-m-d'),
                        'hora_entrada' => null,
                        'hora_salida' => null,
                        'minutos_retraso' => 0,
                        'horas_extras' => 0,
                        'tipo_registro' => 'manual',
                        'observaciones' => $this->generarObservaciones($estado, 0, 0),
                        'estado' => $estado,
                        'latitud' => null,
                        'longitud' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    // Para días normales, generar horarios
                    $horaEntrada = $this->generarHoraEntrada();
                    $horaSalida = $this->generarHoraSalida($horaEntrada);
                    $minutosRetraso = $this->calcularRetraso($horaEntrada);
                    $horasExtras = $this->calcularHorasExtras($horaSalida);
                    $tipoRegistro = $this->generarTipoRegistro();

                    $asistencias[] = [
                        'idPersona' => $idPersona,
                        'fecha' => $date->format('Y-m-d'),
                        'hora_entrada' => $horaEntrada,
                        'hora_salida' => $horaSalida,
                        'minutos_retraso' => $minutosRetraso,
                        'horas_extras' => $horasExtras,
                        'tipo_registro' => $tipoRegistro,
                        'observaciones' => $this->generarObservaciones($estado, $minutosRetraso, $horasExtras),
                        'estado' => $estado,
                        'latitud' => $this->generarCoordenada(-12.046374, 0.01),
                        'longitud' => $this->generarCoordenada(-77.042793, 0.01),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $diasGenerados++;

                // Insertar cada 50 registros para no sobrecargar la memoria
                if (count($asistencias) >= 50) {
                    DB::table('asistencias')->insert($asistencias);
                    $asistencias = []; // Limpiar el array
                }
            }
        }

        // Insertar los registros restantes
        if (!empty($asistencias)) {
            DB::table('asistencias')->insert($asistencias);
        }

        $this->command->info("✅ {$diasGenerados} registros de asistencias creados para la persona ID 85");
    }

    private function esDiaVacaciones($date)
    {
        // Períodos de vacaciones aprobadas (donde no debe haber asistencias)
        $vacacionesAprobadas = [
            ['inicio' => Carbon::create(2025, 1, 15), 'fin' => Carbon::create(2025, 1, 22)],
            ['inicio' => Carbon::create(2025, 3, 10), 'fin' => Carbon::create(2025, 3, 14)],
        ];

        foreach ($vacacionesAprobadas as $vacacion) {
            if ($date->between($vacacion['inicio'], $vacacion['fin'])) {
                return true;
            }
        }

        return false;
    }

    private function generarHoraEntrada()
    {
        // Hora de entrada base: 8:00 AM
        $horaBase = Carbon::createFromTime(8, 0, 0);

        // Probabilidades: 70% puntual, 20% retraso leve, 10% retraso considerable
        $probabilidad = rand(1, 100);

        if ($probabilidad <= 70) {
            // Puntual (8:00 - 8:10)
            $minutos = rand(0, 10);
        } elseif ($probabilidad <= 90) {
            // Retraso leve (8:11 - 8:30)
            $minutos = rand(11, 30);
        } else {
            // Retraso considerable (8:31 - 9:30)
            $minutos = rand(31, 90);
        }

        return $horaBase->addMinutes($minutos)->format('H:i:s');
    }

    private function generarHoraSalida($horaEntrada)
    {
        $entrada = Carbon::createFromFormat('H:i:s', $horaEntrada);

        // Jornada base de 8 horas (480 minutos)
        $jornadaBase = 480;

        // Probabilidades: 60% salida normal, 30% horas extras leve, 10% horas extras considerables
        $probabilidad = rand(1, 100);

        if ($probabilidad <= 60) {
            $minutosExtra = rand(-10, 10); // Variación pequeña
        } elseif ($probabilidad <= 90) {
            $minutosExtra = rand(30, 90); // 0.5 a 1.5 horas extras
        } else {
            $minutosExtra = rand(120, 240); // 2 a 4 horas extras
        }

        $minutosTrabajados = $jornadaBase + $minutosExtra;

        return $entrada->addMinutes($minutosTrabajados)->format('H:i:s');
    }

    private function calcularRetraso($horaEntrada)
    {
        $entrada = Carbon::createFromFormat('H:i:s', $horaEntrada);
        $horaEsperada = Carbon::createFromTime(8, 10, 0); // Tolerancia hasta 8:10

        return $entrada->diffInMinutes($horaEsperada, false); // false para diferencia negativa si es antes
    }

    private function calcularHorasExtras($horaSalida)
    {
        $salida = Carbon::createFromFormat('H:i:s', $horaSalida);
        $horaBaseSalida = Carbon::createFromTime(17, 0, 0); // Salida normal 5:00 PM

        if ($salida->gt($horaBaseSalida)) {
            $minutosExtras = $salida->diffInMinutes($horaBaseSalida);
            return round($minutosExtras / 60, 2);
        }

        return 0;
    }

    private function determinarEstado($date)
    {
        // Verificar si es día de vacaciones
        if ($this->esDiaVacaciones($date)) {
            return 'vacaciones';
        }

        // Días específicos con permisos
        if (($date->month == 3 && $date->day == 15) ||
            ($date->month == 5 && $date->day == 1)) {
            return 'permiso';
        }

        // 3% de probabilidad de ausencia aleatoria
        if (rand(1, 100) <= 3) {
            return 'ausente';
        }

        // Para los días restantes, determinar si es puntual o con tardanza
        // Esto se calculará basado en la hora de entrada generada
        return 'presente'; // El retraso se calcula después
    }

    private function generarTipoRegistro()
    {
        $random = rand(1, 100);
        if ($random <= 60) {
            return 'biometrico';
        } elseif ($random <= 80) {
            return 'manual';
        } else {
            return 'web';
        }
    }

    private function generarObservaciones($estado, $minutosRetraso, $horasExtras)
    {
        $observaciones = [];

        if ($estado === 'vacaciones') {
            return 'Período vacacional';
        }

        if ($estado === 'permiso') {
            $razones = ['cita médica', 'trámite personal', 'asunto familiar'];
            return 'Permiso por ' . $razones[array_rand($razones)];
        }

        if ($estado === 'ausente') {
            return 'Ausencia justificada';
        }

        // Para estado presente
        if ($minutosRetraso > 0) {
            $observaciones[] = "Retraso de {$minutosRetraso} minutos";
        } else {
            $observaciones[] = "Puntual";
        }

        if ($horasExtras > 0) {
            $observaciones[] = "Horas extras: {$horasExtras} horas";
        }

        return implode('. ', $observaciones);
    }

    private function generarCoordenada($centro, $variacion)
    {
        return round($centro + (rand(-100, 100) / 100 * $variacion), 6);
    }
}
