<?php
// database/migrations/2026_07_30_000000_add_pendiente_to_asistencia_estados.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: modificar el enum directamente
        DB::statement("ALTER TABLE asistencia_diaria 
            MODIFY COLUMN estado ENUM(
                'pendiente',
                'completo',
                'tardanza',
                'falta_justificada',
                'falta_injustificada',
                'incompleto',
                'no_laborable'
            ) DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE asistencia_diaria 
            MODIFY COLUMN estado ENUM(
                'completo',
                'tardanza',
                'falta_justificada',
                'falta_injustificada',
                'incompleto',
                'no_laborable'
            ) DEFAULT 'completo'");
    }
};