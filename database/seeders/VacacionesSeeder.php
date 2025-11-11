<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VacacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vacaciones = [
            // Vacaciones de enero
            [
                'idPersona' => 85,
                'fecha_inicio' => '2025-01-15',
                'fecha_fin' => '2025-01-22',
                'dias_tomados' => 6,
                'estado' => 'aprobado',
                'motivo_rechazo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Vacaciones de marzo
            [
                'idPersona' => 85,
                'fecha_inicio' => '2025-03-10',
                'fecha_fin' => '2025-03-14',
                'dias_tomados' => 5,
                'estado' => 'aprobado',
                'motivo_rechazo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Vacaciones pendientes de julio
            [
                'idPersona' => 85,
                'fecha_inicio' => '2025-07-20',
                'fecha_fin' => '2025-07-31',
                'dias_tomados' => 10,
                'estado' => 'pendiente',
                'motivo_rechazo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Vacaciones rechazadas de septiembre
            [
                'idPersona' => 85,
                'fecha_inicio' => '2025-09-05',
                'fecha_fin' => '2025-09-12',
                'dias_tomados' => 6,
                'estado' => 'rechazado',
                'motivo_rechazo' => 'No hay suficiente personal disponible en esa fecha',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('vacaciones')->insert($vacaciones);
    }
}
