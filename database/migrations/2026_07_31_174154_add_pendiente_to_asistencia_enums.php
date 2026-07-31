<?php
// database/migrations/2026_07_31_180000_add_pendiente_to_asistencia_enums.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla asistencia_diaria (ya debería tenerlo si corriste la migration anterior, pero por si acaso)
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

        // Tabla asistencia_marcas (ESTA ES LA QUE FALTABA)
        DB::statement("ALTER TABLE asistencia_marcas 
            MODIFY COLUMN estado ENUM(
                'pendiente',
                'puntual',
                'tardanza',
                'faltante_justificada',
                'faltante_injustificada'
            ) DEFAULT 'faltante_injustificada'");
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

        DB::statement("ALTER TABLE asistencia_marcas 
            MODIFY COLUMN estado ENUM(
                'puntual',
                'tardanza',
                'faltante_justificada',
                'faltante_injustificada'
            ) DEFAULT 'faltante_injustificada'");
    }
};