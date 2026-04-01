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
        Schema::create('inmovilidades_laborales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('situacion_id')
                ->constrained('situaciones_especiales')
                ->onDelete('cascade');

            // TIPO DE INMOVILIDAD
            $table->enum('tipo_inmovilidad', [
                'por_discapacidad',
                'por_tutor_discapacitado',
                'por_dependiente_discapacitado',
                'por_embarazo',
                'por_lactancia',
                'por_paternidad'
            ]);

            // PERIODO DE INMOVILIDAD
            $table->date('fecha_inicio_inmovilidad');
            $table->date('fecha_fin_inmovilidad');
            $table->boolean('renovable')->default(false);

            // BASE LEGAL
            $table->string('norma_legal', 200); // Ej: "Ley 29973 - Ley General de la Persona con Discapacidad"
            $table->string('articulo', 50);

            // RESOLUCIÓN RRHH
            $table->string('numero_resolucion_rrhh', 100);
            $table->date('fecha_resolucion_rrhh');

            // DOCUMENTOS
            $table->string('resolucion_path', 500);
            $table->string('solicitud_path', 500)->nullable();

            // CONTROL
            $table->foreignId('aprobado_por')->nullable()->constrained('users');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'finalizado'])->default('pendiente');

            $table->timestamps();

            // Índice para seguimiento
            $table->index(['estado', 'fecha_fin_inmovilidad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmovilidades_laborales');
    }
};
