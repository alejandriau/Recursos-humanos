<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('niveles_academicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100); // "Bachiller Técnico", "Técnico Superior", "Licenciatura", "Ingeniería", "Diplomado", "Maestría", "Doctorado"
            $table->integer('orden')->default(0); // Para ordenar jerárquicamente
            $table->boolean('esTituloUniversitario')->default(false); // Para diferenciar títulos que cuentan para experiencia
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // Insertar datos iniciales
        DB::table('niveles_academicos')->insert([
            ['nombre' => 'Bachiller', 'orden' => 1, 'esTituloUniversitario' => false],
            ['nombre' => 'Técnico basico', 'orden' => 2, 'esTituloUniversitario' => false],
            ['nombre' => 'Técnico Superior', 'orden' => 3, 'esTituloUniversitario' => false],
            ['nombre' => 'Licenciatura', 'orden' => 4, 'esTituloUniversitario' => true],
            ['nombre' => 'Ingeniería', 'orden' => 5, 'esTituloUniversitario' => true],
            ['nombre' => 'Diplomado', 'orden' => 6, 'esTituloUniversitario' => false],
            ['nombre' => 'Maestría', 'orden' => 7, 'esTituloUniversitario' => false],
            ['nombre' => 'Doctorado', 'orden' => 8, 'esTituloUniversitario' => false],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('niveles_academicos');
    }
};