<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puestos', function (Blueprint $table) {
            // 1. Renombrar 'nivel' a 'nivel_salarial'
            $table->renameColumn('nivel', 'nivel_salarial');

            // 2. Agregar nivel_clase (entero pequeño, igual que los ejemplos: 1,3,7,8,5)
            $table->unsignedSmallInteger('nivel_clase')->nullable()->after('nivel_salarial');

            // 3. Agregar categoría (Superior, Ejecutivo, Operativo)
            $table->enum('categoria', ['SUPERIOR', 'EJECUTIVO', 'OPERATIVO'])
                  ->nullable()
                  ->after('nivelJerarquico');

            // 4. Agregar descripción larga del puesto
            $table->text('descripcion_puesto')->nullable()->after('denominacion');
        });
    }

    public function down(): void
    {
        Schema::table('puestos', function (Blueprint $table) {
            // Revertir renombre
            $table->renameColumn('nivel_salarial', 'nivel');

            // Eliminar columnas agregadas
            $table->dropColumn(['nivel_clase', 'categoria', 'descripcion_puesto']);
        });
    }
};