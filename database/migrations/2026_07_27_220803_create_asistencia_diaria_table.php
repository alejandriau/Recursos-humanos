<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Un registro por persona por día. Es el resumen/agregado.
        Schema::create('asistencia_diaria', function (Blueprint $table) {
            $table->id();
            $table->integer('persona_id');
            $table->date('fecha');
            $table->foreignId('horario_id')->nullable()->constrained('horarios')->nullOnDelete();

            // Resumen consolidado del día
            $table->enum('estado', [
                'completo',            // todas las marcas OK
                'tardanza',            // llegó tarde pero marcó todo
                'falta_justificada',   // faltó una o más marcas, cubierta por una salida aprobada
                'falta_injustificada', // faltó una o más marcas, sin cobertura
                'incompleto',          // combinación de justificado + injustificado en el mismo día
                'no_laborable',        // no le corresponde marcar ese día según su horario
            ])->default('completo');

            $table->integer('minutos_tardanza')->default(0);
            $table->integer('total_marcas_esperadas')->default(0);
            $table->integer('total_marcas_cumplidas')->default(0);
            $table->integer('total_marcas_justificadas')->default(0);
            $table->integer('total_marcas_injustificadas')->default(0);

            $table->timestamp('procesado_en')->nullable();
            $table->timestamps();

            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('cascade');
            $table->unique(['persona_id', 'fecha']);
            $table->index('fecha');
            $table->index('estado');
        });

        // Detalle: una fila por cada "checkpoint" esperado del día
        // (entrada_manana, salida_manana, entrada_tarde, salida_tarde)
        Schema::create('asistencia_marcas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asistencia_diaria_id')->constrained('asistencia_diaria')->cascadeOnDelete();

            $table->enum('tipo_marca', ['entrada_manana', 'salida_manana', 'entrada_tarde', 'salida_tarde', 'entrada', 'salida']);
            $table->time('hora_esperada');
            $table->time('hora_real')->nullable();
            $table->foreignId('marcacion_id')->nullable()->constrained('marcaciones_biometricas')->nullOnDelete();

            $table->enum('estado', ['puntual', 'tardanza', 'faltante_justificada', 'faltante_injustificada'])
                ->default('faltante_injustificada');
            $table->integer('diferencia_minutos')->nullable(); // + tarde, - temprano

            // Si estado = faltante_justificada, referencia a la salida que cubre este checkpoint
            $table->foreignId('salida_id')->nullable()->constrained('salidas')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencia_marcas');
        Schema::dropIfExists('asistencia_diaria');
    }
};
