<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();

            // Relación con persona
            $table->integer('idPersona');
            $table->foreign('idPersona')
                ->references('id')
                ->on('persona')
                ->onDelete('cascade');

            // Campos principales
            $table->date('fecha');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->integer('minutos_retraso')->default(0);
            $table->decimal('horas_extras', 5, 2)->default(0);

            // Atributos y estado
            $table->enum('tipo_registro', ['manual', 'biometrico', 'web'])->default('manual');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['presente', 'ausente', 'tardanza', 'permiso', 'vacaciones'])->default('presente');

            // Coordenadas opcionales
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
