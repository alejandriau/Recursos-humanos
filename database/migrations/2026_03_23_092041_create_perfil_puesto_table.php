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
            $table->integer('aniosExperienciaMinimos')->nullable(); // 3 años
            $table->string('nivelAcademicoRequerido', 50)->nullable(); // "Licenciatura", "Ingeniería"
            $table->json('areasConocimientoPermitidas')->nullable(); // ["Ciencias Económicas", "Ciencias Financieras"]
            $table->json('carrerasEspecificas')->nullable(); // Si requiere carrera específica
            $table->boolean('requiereTituloEnProvisionNacional')->default(true);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfil_puesto');
    }
};