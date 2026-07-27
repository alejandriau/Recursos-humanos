<?php
// database/migrations/2026_07_27_create_marcaciones_biometricas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marcaciones_biometricas', function (Blueprint $table) {
            $table->id();

            // Datos del empleado (lo que viene del biométrico)
            $table->string('ci'); // CI/NIT del empleado
            $table->string('nombre_completo')->nullable();
            $table->string('uid_biometrico'); // UID del usuario en el dispositivo

            // Datos de la marcación
            $table->dateTime('fecha_hora');
            $table->string('tipo')->nullable(); // '0'=Entrada, '1'=Salida
            $table->integer('estado_verificacion')->nullable(); // 1=Huella, 15=Face, etc.
            $table->string('sn')->nullable(); // Número de serie del registro

            // Control de importación
            $table->boolean('importada')->default(false);
            $table->timestamp('fecha_importacion')->nullable();
            $table->string('hash_unique')->nullable()->unique(); // Para evitar duplicados

            // Relación con tu tabla persona (opcional por ahora)
            $table->integer('persona_id')->nullable();

            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index(['ci', 'fecha_hora']);
            $table->index('uid_biometrico');
            $table->index('fecha_hora');
            $table->index('importada');

            // Foreign key (opcional)
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcaciones_biometricas');
    }
};
