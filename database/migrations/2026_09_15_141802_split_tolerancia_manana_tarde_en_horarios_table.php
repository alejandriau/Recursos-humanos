<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // renameColumn requiere doctrine/dbal:
        //   composer require doctrine/dbal
        Schema::table('horarios', function (Blueprint $table) {
            $table->renameColumn('tolerancia_entrada_minutos', 'tolerancia_entrada_manana_minutos');
            $table->renameColumn('tolerancia_salida_minutos', 'tolerancia_salida_manana_minutos');
        });

        Schema::table('horarios', function (Blueprint $table) {
            // Por ahora en 0 (sin tolerancia en la tarde). El día que se
            // necesite, se activa por horario desde el admin, sin tocar código.
            $table->integer('tolerancia_entrada_tarde_minutos')
                ->default(0)
                ->after('tolerancia_entrada_manana_minutos');

            $table->integer('tolerancia_salida_tarde_minutos')
                ->default(0)
                ->after('tolerancia_salida_manana_minutos');
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn(['tolerancia_entrada_tarde_minutos', 'tolerancia_salida_tarde_minutos']);
        });

        Schema::table('horarios', function (Blueprint $table) {
            $table->renameColumn('tolerancia_entrada_manana_minutos', 'tolerancia_entrada_minutos');
            $table->renameColumn('tolerancia_salida_manana_minutos', 'tolerancia_salida_minutos');
        });
    }
};