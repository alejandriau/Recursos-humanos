<?php
// database/seeders/MigrarObandoGonzalesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use App\Models\Gestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MigrarObandoGonzalesSeeder extends Seeder
{
    protected $personaId = 65; // ID CORRECTO DE JOSE MIGUEL OBANDO

    public function run()
    {
        $persona = Persona::find($this->personaId);

        if (!$persona) {
            $this->command->error("Empleado con ID {$this->personaId} no encontrado");
            return;
        }

        $this->command->info("Procesando: {$persona->nombre} {$persona->apellidoPat} (ID: {$persona->id})");
        $this->command->info("CI: {$persona->ci}");
        $this->command->info("Fecha de ingreso: " . Carbon::parse($persona->fechaIngreso)->format('d/m/Y'));

        if (!$this->command->confirm("¿Migrar vacaciones para este empleado?")) {
            $this->command->info("Migración cancelada");
            return;
        }

        $persona->fechaIngreso = '2016-06-17';
        $persona->save();

        DB::transaction(function() use ($persona) {
            // 1. PERÍODO 2016-2017 (0 días) - ESPERA
            $this->crearPeriodo($persona, 1, '2016-06-17', '2017-06-16', 0, [],
                'PERIODO DE ESPERA - PRIMER AÑO SIN DERECHO A VACACIONES');

            // 2. PERÍODO 2017-2018 (20 días) - CAS 29/05/2018
            $this->crearPeriodo($persona, 2, '2017-06-17', '2018-06-16', 20, [
                ['inicio' => '2019-07-11', 'fin' => '2019-07-12', 'dias' => 2],
                ['inicio' => '2020-01-27', 'dias' => 1],
                ['inicio' => '2020-06-05', 'dias' => 1, 'observacion' => 'PERDIO'],
            ], 'CAS 29/05/2018 POR 20 DÍAS');

            // 3. PERÍODO 2018-2019 (20 días)
            $this->crearPeriodo($persona, 3, '2018-06-17', '2019-06-16', 20, [
                ['inicio' => '2020-06-26', 'dias' => 1],
                ['inicio' => '2020-12-03', 'dias' => 1],
                ['inicio' => '2020-12-10', 'dias' => 1],
                ['inicio' => '2021-01-26', 'dias' => 1, 'observacion' => 'PERDIO'],
            ]);

            // 4. PERÍODO 2019-2020 (20 días)
            $this->crearPeriodo($persona, 4, '2019-06-17', '2020-06-16', 20, [
                ['inicio' => '2022-05-13', 'dias' => 1],
                ['inicio' => '2022-05-20', 'dias' => 1],
                ['inicio' => '2022-05-26', 'fin' => '2022-05-27', 'dias' => 2],
                ['inicio' => '2022-05-30', 'dias' => 0.5],
                ['inicio' => '2022-06-08', 'dias' => 1],
                ['inicio' => '2022-06-09', 'dias' => 1, 'observacion' => 'PERDIO'],
            ]);

            // 5. PERÍODO 2020-2021 (20 días)
            $this->crearPeriodo($persona, 5, '2020-06-17', '2021-06-16', 20, [
                ['inicio' => '2022-06-24', 'dias' => 1],
                ['inicio' => '2022-07-04', 'dias' => 1],
                ['inicio' => '2022-08-05', 'dias' => 1],
            ]);

            // 6. PERÍODO 2021-2022 (20 días)
            $this->crearPeriodo($persona, 6, '2021-06-17', '2022-06-16', 20, [
                ['inicio' => '2023-07-06', 'fin' => '2023-07-07', 'dias' => 2],
                ['inicio' => '2023-07-13', 'fin' => '2023-07-14', 'dias' => 2],
                ['inicio' => '2023-07-21', 'dias' => 0.5],
                ['inicio' => '2023-10-03', 'dias' => 0.5],
                ['inicio' => '2023-10-04', 'dias' => 0.5],
                ['inicio' => '2023-09-27', 'dias' => 1],
                ['inicio' => '2023-10-06', 'dias' => 0.5],
                ['inicio' => '2023-12-01', 'dias' => 1],
                ['inicio' => '2023-12-15', 'dias' => 1],
                ['inicio' => '2023-12-19', 'fin' => '2023-12-20', 'dias' => 1.5],
                ['inicio' => '2024-01-11', 'dias' => 1],
                ['inicio' => '2024-01-24', 'fin' => '2024-01-26', 'dias' => 3],
                ['inicio' => '2024-04-12', 'dias' => 0.5],
                ['inicio' => '2024-05-08', 'dias' => 1],
                ['inicio' => '2024-05-27', 'fin' => '2024-05-28', 'dias' => 2],
            ]);

            // 7. PERÍODO 2022-2023 (30 días) - 10 AÑOS
            $this->crearPeriodo($persona, 7, '2022-06-17', '2023-06-16', 30, [
                ['inicio' => '2024-06-26', 'dias' => 0.5],
                ['inicio' => '2024-07-08', 'fin' => '2024-07-12', 'dias' => 5],
                ['inicio' => '2024-09-16', 'dias' => 1],
                ['inicio' => '2024-09-27', 'dias' => 1],
                ['inicio' => '2024-10-03', 'dias' => 1],
                ['inicio' => '2024-12-11', 'fin' => '2024-12-13', 'dias' => 3],
                ['inicio' => '2025-01-23', 'fin' => '2025-01-24', 'dias' => 2],
                ['inicio' => '2025-02-27', 'dias' => 1],
                ['inicio' => '2025-03-19', 'dias' => 0.5],
                ['inicio' => '2025-05-27', 'dias' => 1, 'observacion' => 'PERDIO'],
            ], 'GD-UGRH-065-2023 (10 AÑOS)');

            // 8. PERÍODO 2023-2024 (30 días)
            $this->crearPeriodo($persona, 8, '2023-06-17', '2024-06-16', 30, [
                ['inicio' => '2025-07-17', 'fin' => '2025-07-18', 'dias' => 2],
                ['inicio' => '2025-07-29', 'dias' => 0.5],
                ['inicio' => '2025-10-23', 'dias' => 1],
                ['inicio' => '2025-11-20', 'fin' => '2025-11-21', 'dias' => 2],
                ['inicio' => '2026-05-27', 'dias' => 0.5],
            ]);

            // 9. PERÍODO 2024-2025 (30 días)
            $this->crearPeriodo($persona, 9, '2024-06-17', '2025-06-16', 30, []);

            // 10. PERÍODO 2025-2026 (30 días)
            $this->crearPeriodo($persona, 10, '2025-06-17', '2026-06-16', 30, []);
        });

        $this->command->info("✅ Migración completada");
    }

    protected function crearPeriodo($persona, $numero, $fechaInicio, $fechaFin, $diasAsignados, $movimientos, $observacion = null)
    {
        $fechaInicio = Carbon::parse($fechaInicio);
        $fechaFin = Carbon::parse($fechaFin);

        // Calcular antigüedad
        $antiguedad = Carbon::parse($persona->fechaIngreso)->diffInYears($fechaInicio);

        // Verificar si ya existe el período
        $periodoExistente = VacacionPeriodo::where('persona_id', $persona->id)
            ->where('numero_periodo', $numero)
            ->first();

        if ($periodoExistente) {
            $this->command->warn("  Período {$numero} ya existe, saltando...");
            return $periodoExistente;
        }

        // Si es período de espera (días 0)
        $esEspera = ($diasAsignados == 0);

        // Crear período
        $periodo = VacacionPeriodo::create([
            'persona_id' => $persona->id,
            'gestion_id' => $this->getGestionId($fechaInicio->year),
            'numero_periodo' => $numero,
            'fecha_habilitacion' => $fechaInicio,
            'anios_antiguedad' => $antiguedad,
            'dias_asignados' => $diasAsignados,
            'dias_usados' => 0,
            'dias_vencidos' => 0,
            'saldo_disponible' => $diasAsignados,
            'dias_arrastre' => 0,
            'estado' => $esEspera ? 'espera' : ($fechaInicio <= now() ? 'activo' : 'pendiente'),
            'observacion' => $observacion ?? "Período {$numero}"
        ]);

        if ($esEspera) {
            $this->command->info("  ⏳ Período {$numero} (ESPERA) creado");
            return $periodo;
        }

        // PROCESAR MOVIMIENTOS - SIN VALIDAR FECHA
        $diasUsados = 0;
        foreach ($movimientos as $mov) {
            $fechaMov = Carbon::parse($mov['inicio']);
            $dias = floatval($mov['dias']);
            $fechaFinMov = isset($mov['fin']) ? Carbon::parse($mov['fin']) : null;

            VacacionMovimiento::create([
                'periodo_id' => $periodo->id,
                'tipo' => 'debito',
                'fecha' => $fechaMov,
                'fecha_inicio' => $fechaMov,
                'fecha_fin' => $fechaFinMov,
                'cantidad' => $dias,
                'saldo_anterior' => $diasAsignados - $diasUsados,
                'saldo_posterior' => $diasAsignados - $diasUsados - $dias,
                'descripcion' => $mov['observacion'] ?? 'Uso de vacaciones',
                'registrado_por' => 1
            ]);

            $diasUsados += $dias;
            $this->command->line("    📝 Movimiento {$fechaMov->format('d/m/Y')} + {$dias} días");
        }

        // Actualizar saldo
        $periodo->dias_usados = $diasUsados;
        $periodo->saldo_disponible = $diasAsignados - $diasUsados;

        // Verificar si está vencido (solo si pasó más de 2 períodos)
        $periodosVencimiento = $numero + 2; // Se puede usar hasta 2 períodos después
        $fechaLimite = Carbon::parse($persona->fechaIngreso)->addYears($periodosVencimiento);

        if (now()->gt($fechaLimite) && $periodo->saldo_disponible > 0) {
            $periodo->periodo_vencido = true;
            $periodo->estado = 'vencido';
            $periodo->dias_vencidos = $periodo->saldo_disponible;
            $this->command->warn("    ⚠️ Período {$numero} VENCIDO (pasaron 2 períodos)");
        }

        $periodo->save();
        $this->command->info("  ✅ Período {$numero} creado con {$diasUsados} días usados, saldo: {$periodo->saldo_disponible}");
    }

    protected function getGestionId($year)
    {
        return Gestion::firstOrCreate(
            ['anio' => $year],
            [
                'fecha'  => $year . '-01-01',
                'estado' => $year <= date('Y') ? 'cerrado' : 'activo',
            ]
        )->id;
    }
}
