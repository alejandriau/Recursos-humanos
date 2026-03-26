<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('perfil_puesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_puesto')->constrained('puestos');
            // 🔥 NIVEL REQUERIDO (ej: Licenciatura)
            $table->foreignId('idNivelAcademico')->nullable()->constrained('niveles_academicos');
            $table->foreignId('idAreaConocimiento')->nullable()->constrained('areas_conocimiento');
            $table->foreignId('idCarrera')->nullable()->constrained('carreras');
            $table->integer('aniosExperienciaMinimos')->nullable();
            $table->boolean('requiereTituloEnProvisionNacional')->default(true);
            $table->text('conocimientoTexto')->nullable();
            $table->text('objetivo')->nullable();

            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfil_puesto');
    }
};