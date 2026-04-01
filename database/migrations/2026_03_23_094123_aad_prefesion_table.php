<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profesion', function (Blueprint $table) {
            // Agregar nuevas columnas (nullable para mantener compatibilidad)
            $table->foreignId('id_carrera')->nullable()->after('idPersona')->constrained('carreras');
            $table->boolean('esPrincipal')->default(false)->after('observacion');
            $table->date('fechaTitulo')->nullable()->after('diploma');
            
            // Cambiar id a bigInteger si era integer (opcional)
            // Si tu tabla actual usa 'id' como integer, esto ya está bien
        });
    }

    public function down(): void
    {
        Schema::table('profesion', function (Blueprint $table) {
            $table->dropForeign(['id_carrera']);
            $table->dropColumn(['id_carrera', 'esPrincipal', 'fechaTitulo']);
        });
    }
};