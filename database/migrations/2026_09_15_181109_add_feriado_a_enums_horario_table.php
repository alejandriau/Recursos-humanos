<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Asume MySQL/MariaDB (ENUM nativo). Si usas PostgreSQL, el ALTER de
     * un enum es distinto (no se puede hacer con MODIFY); avísame y te
     * paso la versión para ese driver.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE asistencia_diaria MODIFY estado ENUM(
            'pendiente','completo','tardanza','falta_justificada','falta_injustificada','incompleto','no_laborable','feriado'
        ) NOT NULL DEFAULT 'pendiente'");

        DB::statement("ALTER TABLE asistencia_marcas MODIFY estado ENUM(
            'pendiente','puntual','tardanza','faltante_justificada','faltante_injustificada','feriado'
        ) NOT NULL DEFAULT 'faltante_injustificada'");

        // El fallback de feriado sin horario resoluble no tiene una hora
        // real que esperar, así que hora_esperada debe poder ser NULL.
        DB::statement("ALTER TABLE asistencia_marcas MODIFY hora_esperada TIME NULL");
    }

    public function down(): void
    {
        // Nota: si ya existen filas con estado='feriado' o hora_esperada=NULL,
        // este down() va a fallar hasta que esas filas se limpien/migren.
        DB::statement("ALTER TABLE asistencia_marcas MODIFY hora_esperada TIME NOT NULL");

        DB::statement("ALTER TABLE asistencia_marcas MODIFY estado ENUM(
            'pendiente','puntual','tardanza','faltante_justificada','faltante_injustificada'
        ) NOT NULL DEFAULT 'faltante_injustificada'");

        DB::statement("ALTER TABLE asistencia_diaria MODIFY estado ENUM(
            'pendiente','completo','tardanza','falta_justificada','falta_injustificada','incompleto','no_laborable'
        ) NOT NULL DEFAULT 'pendiente'");
    }
};