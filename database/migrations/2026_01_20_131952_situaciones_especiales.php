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
        Schema::create('situaciones_especiales', function (Blueprint $table) {
            $table->id();
            $table->integer('persona_id');

            $table->foreign('persona_id')
                ->references('id')
                ->on('persona')
                ->onDelete('cascade');

            // TIPO DE SITUACIÓN
            $table->enum('tipo_situacion', [
                'discapacidad',
                'tutor_discapacitado',
                'dependiente_discapacitado',
                'embarazo',
                'lactancia',
                'paternidad'
            ]);

            // ESTADO Y VIGENCIA
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable(); // Para situaciones temporales
            $table->boolean('vigente')->default(true);

            // DOCUMENTACIÓN PRINCIPAL
            $table->string('documento_soporte_path', 500)->nullable();
            $table->string('numero_resolucion', 100)->nullable(); // Para RRHH público
            $table->date('fecha_resolucion')->nullable();

            // BENEFICIOS APLICABLES
            $table->boolean('tiene_inmovilidad')->default(false);
            $table->text('observaciones_rrhh')->nullable();

            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index(['persona_id', 'vigente']);
            $table->index('tipo_situacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('situaciones_especiales');
    }
};
