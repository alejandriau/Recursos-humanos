<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // "Horario General", "Turno Noche Seguridad", etc.
            $table->string('descripcion')->nullable();
            $table->integer('tolerancia_entrada_minutos')->default(5);
            $table->integer('tolerancia_salida_minutos')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Un registro por día de la semana que aplica dentro de un horario.
        // Si no existe registro para un día, ese día no es laborable en ese horario.
        // hora_entrada/hora_salida = jornada continua (mañana o turno único)
        // hora_entrada_tarde/hora_salida_tarde = si están llenos, la jornada es partida ese día
        Schema::create('horario_dias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_id')->constrained('horarios')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana'); // 0=Domingo ... 6=Sábado (igual que Carbon::dayOfWeek)

            $table->time('hora_entrada');
            $table->time('hora_salida');
            $table->time('hora_entrada_tarde')->nullable();
            $table->time('hora_salida_tarde')->nullable();

            $table->timestamps();

            $table->unique(['horario_id', 'dia_semana']);
        });

        // Asignación de horario por persona, con vigencia (permite turnos rotativos
        // o cambios de horario en el tiempo sin perder el histórico)
        Schema::create('persona_horarios', function (Blueprint $table) {
            $table->id();
            $table->integer('persona_id');
            $table->foreignId('horario_id')->constrained('horarios');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable(); // null = vigente indefinidamente
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('cascade');
            $table->index(['persona_id', 'fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona_horarios');
        Schema::dropIfExists('horario_dias');
        Schema::dropIfExists('horarios');
    }
};
