<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carreras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200); // "Ingeniería Financiera", "Licenciatura en Economía", etc.
            $table->foreignId('idAreaConocimiento')->constrained('areas_conocimiento');
            $table->foreignId('idNivelAcademico')->constrained('niveles_academicos');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // Datos iniciales de ejemplo
        DB::table('carreras')->insert([
            ['nombre' => 'Licenciatura en Economía', 'idAreaConocimiento' => 1, 'idNivelAcademico' => 3],
            ['nombre' => 'Licenciatura en Finanzas', 'idAreaConocimiento' => 2, 'idNivelAcademico' => 3],
            ['nombre' => 'Ingeniería Financiera', 'idAreaConocimiento' => 2, 'idNivelAcademico' => 4],
            ['nombre' => 'Licenciatura en Administración', 'idAreaConocimiento' => 3, 'idNivelAcademico' => 3],
            ['nombre' => 'Contaduría Pública', 'idAreaConocimiento' => 4, 'idNivelAcademico' => 3],
            ['nombre' => 'Técnico Superior en Contabilidad', 'idAreaConocimiento' => 4, 'idNivelAcademico' => 2],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};