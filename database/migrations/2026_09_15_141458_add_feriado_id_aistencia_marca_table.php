<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencia_marcas', function (Blueprint $table) {
            $table->foreignId('feriado_id')
                ->nullable()
                ->after('salida_id')
                ->constrained('feriados')
                ->nullOnDelete();
        });

        // Si la columna 'estado' de asistencia_marcas y asistencia_diaria es un ENUM
        // (Schema::enum / SQL ENUM), hay que agregar 'feriado' como valor permitido.
        // Ejemplo para MySQL (ajusta el nombre de columna/valores a tu enum real):
        //
        // DB::statement("ALTER TABLE asistencia_marcas MODIFY estado
        //     ENUM('puntual','tardanza','pendiente','faltante_justificada','faltante_injustificada','feriado') NOT NULL");
        //
        // DB::statement("ALTER TABLE asistencia_diaria MODIFY estado
        //     ENUM('completo','tardanza','incompleto','falta_justificada','falta_injustificada','pendiente','no_laborable','feriado') NOT NULL");
        //
        // Si 'estado' es un simple string/varchar, no necesitas hacer nada aquí.
    }

    public function down(): void
    {
        Schema::table('asistencia_marcas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('feriado_id');
        });
    }
};