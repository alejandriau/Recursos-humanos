<?php
// database/seeders/MigrarAlbaGutierrezSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use App\Models\Gestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MigrarAlbaGutierrezSeeder extends Seeder
{
    // ¡CAMBIA ESTE ID POR EL DE TU PERSONA!
    protected $personaId = 191; // <--- PON AQUÍ EL ID CORRECTO

    public function run()
    {
        // Buscar por ID específico
        $persona = Persona::find($this->personaId);

        if (!$persona) {
            $this->command->error("Empleado con ID {$this->personaId} no encontrado");
            $this->command->info("Empleados disponibles con nombre similar:");

            // Mostrar opciones disponibles
            $opciones = Persona::where('nombre', 'LIKE', '%ALBA%')
                ->orWhere('apellidoPat', 'LIKE', '%GUTIERREZ%')
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

        // Actualizar fecha de ingreso (09/02/2001)
        $persona->fechaIngreso = '2001-02-09';
        $persona->save();

        DB::transaction(function() use ($persona) {
            // 1. PERÍODO 2015-2016 (30 días) - PERDIDO según Excel
            $this->crearPeriodo($persona, 1, '2015-02-09', '2016-02-08', 30, [], 'SALDO PERIODO 2015-2016 PERDIO');

            // 2. PERÍODO 2016-2017 (30 días)
            $this->crearPeriodo($persona, 2, '2016-02-09', '2017-02-08', 30, [
                ['inicio' => '2018-02-01', 'fin' => '2018-02-02', 'dias' => 2],
                ['inicio' => '2018-03-07', 'fin' => '2018-03-08', 'dias' => 2],
                ['inicio' => '2018-03-13', 'dias' => 1],
                ['inicio' => '2018-08-08', 'dias' => 1],
                ['inicio' => '2018-08-14', 'dias' => 0.5],
                ['inicio' => '2018-08-16', 'fin' => '2018-08-31', 'dias' => 12],
                ['inicio' => '2019-01-04', 'dias' => 0.5],
                ['inicio' => '2019-01-31', 'fin' => '2019-02-20', 'dias' => 15],
            ]);

            // 3. PERÍODO 2017-2018 (30 días)
            $this->crearPeriodo($persona, 3, '2017-02-09', '2018-02-08', 30, [
                ['inicio' => '2019-08-08', 'dias' => 1],
                ['inicio' => '2019-08-16', 'fin' => '2019-09-06', 'dias' => 16],
                ['inicio' => '2019-10-15', 'dias' => 1],
                ['inicio' => '2020-01-09', 'fin' => '2020-01-10', 'dias' => 1.5],
            ]);

            // 4. PERÍODO 2018-2019 (30 días)
            $this->crearPeriodo($persona, 4, '2018-02-09', '2019-02-08', 30, [
                ['inicio' => '2020-02-17', 'fin' => '2020-03-17', 'dias' => 20],
                ['inicio' => '2020-06-24', 'fin' => '2020-06-26', 'dias' => 3],
                ['inicio' => '2020-10-13', 'dias' => 1],
                ['inicio' => '2020-11-12', 'fin' => '2020-11-25', 'dias' => 10],
            ]);

            // 5. PERÍODO 2019-2020 (30 días)
            $this->crearPeriodo($persona, 5, '2019-02-09', '2020-02-08', 30, [
                ['inicio' => '2020-12-24', 'dias' => 1],
                ['inicio' => '2021-02-03', 'fin' => '2021-02-04', 'dias' => 2],
                ['inicio' => '2021-04-13', 'dias' => 1],
                ['inicio' => '2021-06-17', 'dias' => 1],
                ['inicio' => '2021-07-29', 'dias' => 1],
                ['inicio' => '2021-09-03', 'dias' => 1],
                ['inicio' => '2021-10-08', 'fin' => '2021-10-11', 'dias' => 2],
                ['inicio' => '2021-10-18', 'dias' => 1],
                ['inicio' => '2021-12-23', 'fin' => '2021-12-24', 'dias' => 2],
                ['inicio' => '2022-01-31', 'fin' => '2022-02-01', 'dias' => 2],
            ]);

            // 6. PERÍODO 2020-2021 (30 días)
            $this->crearPeriodo($persona, 6, '2020-02-09', '2021-02-08', 30, [
                ['inicio' => '2022-05-26', 'fin' => '2022-05-27', 'dias' => 2],
                ['inicio' => '2022-06-14', 'fin' => '2022-06-20', 'dias' => 5],
                ['inicio' => '2022-08-08', 'fin' => '2022-08-12', 'dias' => 5],
                ['inicio' => '2022-10-03', 'fin' => '2022-10-07', 'dias' => 5],
                ['inicio' => '2023-01-05', 'fin' => '2023-01-10', 'dias' => 4],
            ]);

            // 7. PERÍODO 2021-2022 (30 días)
            $this->crearPeriodo($persona, 7, '2021-02-09', '2022-02-08', 30, [
                ['inicio' => '2023-04-12', 'fin' => '2023-04-13', 'dias' => 2],
                ['inicio' => '2023-05-03', 'fin' => '2023-05-04', 'dias' => 2],
                ['inicio' => '2023-07-03', 'fin' => '2023-07-07', 'dias' => 5],
                ['inicio' => '2023-08-28', 'fin' => '2023-09-04', 'dias' => 5],
                ['inicio' => '2023-09-25', 'fin' => '2023-09-29', 'dias' => 5],
                ['inicio' => '2023-12-26', 'fin' => '2023-12-27', 'dias' => 2],
                ['inicio' => '2024-01-31', 'fin' => '2024-02-01', 'dias' => 2],
            ]);

            // 8. PERÍODO 2022-2023 (30 días)
            $this->crearPeriodo($persona, 8, '2022-02-09', '2023-02-08', 30, [
                ['inicio' => '2024-02-29', 'fin' => '2024-03-01', 'dias' => 2],
                ['inicio' => '2024-04-10', 'fin' => '2024-04-12', 'dias' => 3],
                ['inicio' => '2024-05-03', 'dias' => 1],
                ['inicio' => '2024-06-07', 'fin' => '2024-06-10', 'dias' => 2],
                ['inicio' => '2024-07-15', 'fin' => '2024-07-19', 'dias' => 5],
                ['inicio' => '2024-08-08', 'dias' => 1],
                ['inicio' => '2024-10-14', 'fin' => '2024-10-18', 'dias' => 5],
                ['inicio' => '2025-01-13', 'fin' => '2025-01-17', 'dias' => 5],
            ]);

            // 9. PERÍODO 2023-2024 (30 días)
            $this->crearPeriodo($persona, 9, '2023-02-09', '2024-02-08', 30, [
                ['inicio' => '2025-03-24', 'fin' => '2025-03-26', 'dias' => 2],
                ['inicio' => '2025-08-08', 'dias' => 1],
                ['inicio' => '2025-09-18', 'fin' => '2025-09-24', 'dias' => 5],
                ['inicio' => '2026-01-14', 'dias' => 1],
            ]);

            // 10. PERÍODO 2024-2025 (30 días)
            $this->crearPeriodo($persona, 10, '2024-02-09', '2025-02-08', 30, [
                ['inicio' => '2026-04-22', 'fin' => '2026-04-24', 'dias' => 3],
            ]);

            // 11. PERÍODO 2025-2026 (30 días) - sin movimientos aún
            $this->crearPeriodo($persona, 11, '2025-02-09', '2026-02-08', 30, []);

            // 12. PERÍODO 2026-2027 (30 días) - sin movimientos aún
            $this->crearPeriodo($persona, 12, '2026-02-09', '2027-02-08', 30, []);
        });

        $this->command->info("✅ Migración completada para {$persona->nombre} {$persona->apellidoPat}");
        $this->command->info("Total períodos migrados: 12");
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
            'observacion' => $observacion ?? "Período {$numero}: {$fechaInicio->format('d/m/Y')} - {$fechaFin->format('d/m/Y')}"
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
                'estado' => $year <= date('Y') ? 'cerrado' : 'activo',
            ]
        )->id;
    }
}
