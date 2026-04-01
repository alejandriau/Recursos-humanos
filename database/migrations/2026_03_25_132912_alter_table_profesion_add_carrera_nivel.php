<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profesion', function (Blueprint $table) {

            $table->foreignId('idNivelEstudiado')
                  ->nullable()
                  ->after('id_carrera')
                  ->constrained('niveles_academicos');

            // Estado del estudio
            $table->enum('estadoEstudio', ['en_curso', 'incompleto', 'egresado', 'titulado'])
                  ->default('en_curso')
                  ->after('idNivelEstudiado');
        });
    }

    public function down(): void
    {
        Schema::table('profesion', function (Blueprint $table) {
            $table->dropForeign(['idNivelEstudiado']);
            $table->dropColumn(['id_carrera', 'idNivelEstudiado', 'estadoEstudio']);
        });
    }
};
