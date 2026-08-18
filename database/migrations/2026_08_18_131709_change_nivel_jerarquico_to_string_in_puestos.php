<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar de ENUM a VARCHAR(255)
        Schema::table('puestos', function (Blueprint $table) {
            $table->string('nivelJerarquico', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Obtener todos los valores distintos que existen actualmente en la tabla
        $existingValues = DB::table('puestos')
            ->select('nivelJerarquico')
            ->distinct()
            ->whereNotNull('nivelJerarquico')
            ->pluck('nivelJerarquico')
            ->toArray();

        // Lista base de valores que queremos en el ENUM (incluye el nuevo nivel)
        $baseValues = [
            'GOBERNADOR (A)',
            'SECRETARIA (O) DEPARTAMENTAL',
            'DIRECTORA (OR)/DIR. SERV. DPTAL./VOCERA (O) GUB.',
            'ASESORA (OR) / DIRECTORA (OR) / DIR. SERV. DPTAL.',
            'JEFA (E) DE UNIDAD',
            'PROFESIONAL I',
            'PROFESIONAL II',
            'ADMINISTRATIVO I',
            'ADMINISTRATIVO II',
            'APOYO ADMINISTRATIVO I',
            'APOYO ADMINISTRATIVO II',
            'ASISTENTE',
            'APOYO ADMINISTRATIVO', // Nuevo nivel agregado
        ];

        // Combinar y eliminar duplicados
        $finalValues = array_unique(array_merge($baseValues, $existingValues));
        sort($finalValues); // Ordenar alfabéticamente para consistencia

        // Construir la sentencia SQL
        $enumString = "'" . implode("','", $finalValues) . "'";
        DB::statement("ALTER TABLE puestos MODIFY nivelJerarquico ENUM({$enumString}) NULL");
    }
};