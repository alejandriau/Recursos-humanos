<?php
// database/seeders/MigrarAymaRojasSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use App\Models\Gestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MigrarAymaRojasSeeder extends Seeder
{
    // ¡CAMBIA ESTE ID POR EL DE TU PERSONA!
    protected $personaId = 83; // <--- PON AQUÍ EL ID CORRECTO

    public function run()
    {
        // Buscar por ID específico
        $persona = Persona::find($this->personaId);

        if (!$persona) {
            $this->command->error("Empleado con ID {$this->personaId} no encontrado");
            $this->command->info("Empleados disponibles con nombre similar:");

            // Mostrar opciones disponibles
            $opciones = Persona::where('nombre', 'LIKE', '%AYMA%')
                ->orWhere('apellidoPat', 'LIKE', '%AYMA%')
                ->get(['id', 'nombre', 'apellidoPat', 'ci']);

            foreach ($opciones as $p) {
                $this->command->info("  ID: {$p->id} - {$p->nombre} {$p->apellidoPat} - CI: {$p->ci}");
            }

            return;
        }

        $this->command->info("Procesando: {$persona->nombre} {$persona->apellidoPat} (ID: {$persona->id})");
        $this->command->info("CI: {$persona->ci}");

        // Confirmar antes de continuar
        if (!$this->command->confirm("¿Migrar vacaciones para este empleado?")) {
            $this->command->info("Migración cancelada");
            return;
        }

        // Actualizar fecha de ingreso (01/03/2016)
        $persona->fechaIngreso = '2016-03-01';
        $persona->save();

        DB::transaction(function() use ($persona) {
            // 1. PERÍODO 2016-2017 (15 días)
            $this->crearPeriodo($persona, 1, '2016-03-01', '2017-02-28', 15, [
                ['inicio' => '2017-02-14', 'dias' => 1],
                ['inicio' => '2017-03-16', 'dias' => 1],
                ['inicio' => '2017-04-19', 'dias' => 1],
                ['inicio' => '2017-12-19', 'fin' => '2017-12-26', 'dias' => 5],
                ['inicio' => '2018-04-02', 'fin' => '2018-04-03', 'dias' => 2],
                ['inicio' => '2018-08-16', 'fin' => '2018-08-17', 'dias' => 2],
                ['inicio' => '2018-09-21', 'dias' => 1],
                ['inicio' => '2018-12-20', 'fin' => '2018-12-31', 'dias' => 7],
            ]);

            // 2. PERÍODO 2017-2018 (15 días)
            $this->crearPeriodo($persona, 2, '2017-03-01', '2018-02-28', 15, [
                ['inicio' => '2019-02-07', 'dias' => 1],
                ['inicio' => '2019-02-18', 'dias' => 1],
                ['inicio' => '2019-03-01', 'dias' => 1],
                ['inicio' => '2019-05-16', 'dias' => 1],
                ['inicio' => '2019-05-27', 'dias' => 1],
                ['inicio' => '2019-06-11', 'fin' => '2019-06-13', 'dias' => 3],
                ['inicio' => '2019-06-24', 'dias' => 0.5],
                ['inicio' => '2019-10-15', 'fin' => '2019-10-18', 'dias' => 4],
            ]);

            // 3. PERÍODO 2018-2019 (15 días)
            $this->crearPeriodo($persona, 3, '2018-03-01', '2019-02-28', 15, [
                ['inicio' => '2019-11-11', 'dias' => 1],
                ['inicio' => '2019-11-13', 'dias' => 1],
                ['inicio' => '2019-12-18', 'dias' => 1],
                ['inicio' => '2020-01-02', 'fin' => '2020-01-03', 'dias' => 2],
                ['inicio' => '2020-01-17', 'dias' => 1],
                ['inicio' => '2020-01-29', 'dias' => 1],
                ['inicio' => '2020-02-26', 'fin' => '2020-02-28', 'dias' => 3],
                ['inicio' => '2020-10-13', 'dias' => 1],
            ]);

            // 4. PERÍODO 2019-2020 (15 días)
            $this->crearPeriodo($persona, 4, '2019-03-01', '2020-02-29', 15, [
                ['inicio' => '2020-11-09', 'fin' => '2020-11-10', 'dias' => 2],
            ]);

            // 5. PERÍODO 2020-2021 (15 días)
            $this->crearPeriodo($persona, 5, '2020-03-01', '2021-02-28', 15, [
                ['inicio' => '2020-12-21', 'fin' => '2020-12-22', 'dias' => 2],
                ['inicio' => '2021-01-07', 'dias' => 1],
                ['inicio' => '2021-02-04', 'dias' => 1],
                ['inicio' => '2021-03-27', 'dias' => 1],
                ['inicio' => '2021-03-30', 'dias' => 1],
                ['inicio' => '2021-07-16', 'dias' => 1],
                ['inicio' => '2021-07-30', 'dias' => 1],
                ['inicio' => '2021-08-10', 'dias' => 1],
                ['inicio' => '2021-08-13', 'dias' => 0.5],
                ['inicio' => '2021-09-27', 'dias' => 0.5],
                ['inicio' => '2021-09-28', 'dias' => 0.5],
                ['inicio' => '2021-10-13', 'dias' => 0.5],
                ['inicio' => '2021-10-29', 'dias' => 1],
                ['inicio' => '2021-12-06', 'dias' => 0.5],
                ['inicio' => '2021-12-09', 'dias' => 0.5],
                ['inicio' => '2021-12-21', 'fin' => '2021-12-23', 'dias' => 3],
            ]);

            // 6. PERÍODO 2021-2022 (15 días)
            $this->crearPeriodo($persona, 6, '2021-03-01', '2022-02-28', 15, [
                ['inicio' => '2021-12-24', 'dias' => 0.5],
                ['inicio' => '2022-01-06', 'dias' => 1],
                ['inicio' => '2022-02-01', 'dias' => 0.5],
                ['inicio' => '2022-04-07', 'dias' => 0.5],
                ['inicio' => '2022-08-11', 'dias' => 0.5],
                ['inicio' => '2022-08-30', 'dias' => 0.5],
                ['inicio' => '2022-09-16', 'dias' => 1],
                ['inicio' => '2022-10-26', 'dias' => 1],
                ['inicio' => '2022-11-27', 'fin' => '2023-01-04', 'dias' => 6],
                ['inicio' => '2023-01-10', 'dias' => 1],
                ['inicio' => '2023-01-19', 'dias' => 1],
            ]);

            // 7. PERÍODO 2022-2023 (20 días - ya tiene 5 años)
            $this->crearPeriodo($persona, 7, '2022-03-01', '2023-02-28', 20, [
                ['inicio' => '2023-02-10', 'dias' => 1],
                ['inicio' => '2023-04-10', 'dias' => 1],
                ['inicio' => '2023-04-25', 'dias' => 0.5],
                ['inicio' => '2023-06-06', 'fin' => '2023-06-07', 'dias' => 2],
                ['inicio' => '2023-06-12', 'dias' => 1],
                ['inicio' => '2023-06-14', 'fin' => '2023-06-15', 'dias' => 1.5],
                ['inicio' => '2023-06-20', 'dias' => 0.5],
                ['inicio' => '2023-06-22', 'dias' => 1],
                ['inicio' => '2023-08-21', 'dias' => 0.5],
                ['inicio' => '2023-08-31', 'dias' => 0.5],
                ['inicio' => '2023-09-20', 'dias' => 1],
                ['inicio' => '2023-10-02', 'dias' => 0.5],
                ['inicio' => '2023-10-25', 'dias' => 0.5],
                ['inicio' => '2023-10-26', 'dias' => 0.5],
                ['inicio' => '2023-11-24', 'dias' => 1],
                ['inicio' => '2023-12-15', 'dias' => 0.5],
                ['inicio' => '2023-12-21', 'fin' => '2023-12-28', 'dias' => 5],
                ['inicio' => '2024-02-14', 'fin' => '2024-02-16', 'dias' => 3],
            ]);

            // 8. PERÍODO 2023-2024 (20 días)
            $this->crearPeriodo($persona, 8, '2023-03-01', '2024-02-29', 20, [
                ['inicio' => '2024-03-04', 'dias' => 0.5],
                ['inicio' => '2024-05-28', 'dias' => 1],
                ['inicio' => '2024-06-06', 'dias' => 0.5],
                ['inicio' => '2024-06-11', 'dias' => 1],
                ['inicio' => '2024-07-03', 'dias' => 0.5],
                ['inicio' => '2024-07-15', 'dias' => 1],
                ['inicio' => '2024-07-26', 'dias' => 1],
                ['inicio' => '2024-08-16', 'dias' => 0.5],
                ['inicio' => '2024-09-20', 'dias' => 1],
                ['inicio' => '2024-10-23', 'dias' => 1],
                ['inicio' => '2024-11-25', 'fin' => '2024-11-26', 'dias' => 2],
                ['inicio' => '2024-12-23', 'fin' => '2024-12-27', 'dias' => 4],
                ['inicio' => '2025-01-23', 'dias' => 1],
            ]);

            // 9. PERÍODO 2024-2025 (20 días)
            $this->crearPeriodo($persona, 9, '2024-03-01', '2025-02-28', 20, [
                ['inicio' => '2025-03-14', 'dias' => 1],
                ['inicio' => '2025-03-31', 'fin' => '2025-04-01', 'dias' => 2],
                ['inicio' => '2025-04-17', 'dias' => 0.5],
                ['inicio' => '2025-08-04', 'dias' => 1],
                ['inicio' => '2025-08-25', 'dias' => 0.5],
                ['inicio' => '2025-09-22', 'dias' => 0.5],
                ['inicio' => '2025-10-02', 'dias' => 1],
                ['inicio' => '2025-10-13', 'dias' => 0.5],
                ['inicio' => '2025-10-27', 'dias' => 1],
                ['inicio' => '2025-11-06', 'dias' => 0.5],
                ['inicio' => '2026-01-05', 'fin' => '2026-01-07', 'dias' => 3],
                ['inicio' => '2026-01-19', 'dias' => 1],
                ['inicio' => '2026-01-26', 'dias' => 1],
                ['inicio' => '2026-02-13', 'dias' => 1],
                ['inicio' => '2026-02-23', 'dias' => 0.5],
            ]);

            // 10. PERÍODO 2025-2026 (20 días)
            $this->crearPeriodo($persona, 10, '2025-03-01', '2026-02-28', 20, [
                ['inicio' => '2026-03-05', 'dias' => 0.5],
                ['inicio' => '2026-03-10', 'dias' => 1],
                ['inicio' => '2026-04-08', 'dias' => 0.5],
                ['inicio' => '2026-04-29', 'dias' => 0.5],
                ['inicio' => '2026-05-05', 'dias' => 1],
                ['inicio' => '2026-05-18', 'dias' => 1],
                ['inicio' => '2026-05-28', 'dias' => 1],
            ]);

            // 11. PERÍODO 2026-2027 (20 días) - no tiene movimientos aún
            $this->crearPeriodo($persona, 11, '2026-03-01', '2027-02-28', 20, []);
        });

        $this->command->info("✅ Migración completada para {$persona->nombre} {$persona->apellidoPat}");
        $this->command->info("Total períodos migrados: 11");
    }

    protected function crearPeriodo($persona, $numero, $fechaInicio, $fechaFin, $diasAsignados, $movimientos)
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
            return;
        }

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
            'estado' => $fechaInicio <= now() ? 'activo' : 'pendiente',
            'observacion' => "Período {$numero}: {$fechaInicio->format('d/m/Y')} - {$fechaFin->format('d/m/Y')}"
        ]);

        // Procesar movimientos
        $diasUsados = 0;
        foreach ($movimientos as $mov) {
            $fechaMov = Carbon::parse($mov['inicio']);

            // Verificar que la fecha esté dentro del período
            if (!$fechaMov->between($fechaInicio, $fechaFin)) {
                continue;
            }

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
        }

        // Actualizar saldo
        $periodo->dias_usados = $diasUsados;
        $periodo->saldo_disponible = $diasAsignados - $diasUsados;

        // Verificar si está vencido
        if ($fechaFin < now() && $periodo->saldo_disponible > 0) {
            $periodo->periodo_vencido = true;
            $periodo->estado = 'vencido';
            $periodo->dias_vencidos = $periodo->saldo_disponible;
        }

        $periodo->save();
        $this->command->info("  ✅ Período {$numero} creado con {$diasUsados} días usados");
    }

    protected function getGestionId($year)
    {
        return Gestion::firstOrCreate(
        ['anio' => $year],
        [
            'fecha'  => $year . '-01-01',
            'estado' => 'cerrado',
        ]
        )->id;
    }
}
