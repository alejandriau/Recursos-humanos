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
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('codigo')->nullable()->unique();
            $table->integer("persona_id");
            $table->unsignedBigInteger('tiposalida_id');
            $table->unsignedBigInteger('periodo_id')->nullable();
            $table->string('periodo_type')->nullable(); // App\Models\BeneficioPeriodo o App\Models\VacacionPeriodo

            $table->date('fechasal');
            $table->time('horasal')->nullable();
            $table->date('fecharet');
            $table->time('horaret')->nullable();
            $table->decimal('cantidad', 6, 2)->nullable(); // calculado: dias u horas según tiposalida
            $table->string('motivo')->nullable();
            $table->date('fechasol');
            $table->string('img')->nullable();

            // Aprobación jefe inmediato
            $table->enum('estado_jefe', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->integer('jefe_id')->nullable();
            $table->timestamp('fecha_aprobacion_jefe')->nullable();
            $table->string('observacion_jefe')->nullable();

            // Aprobación RRHH
            $table->enum('estado_rrhh', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->integer('rrhh_id')->nullable();
            $table->timestamp('fecha_aprobacion_rrhh')->nullable();
            $table->string('observacion_rrhh')->nullable();

            $table->index(['created_at', 'codigo']); // índice compuesto para rapidez
            // Estado consolidado para consultas rápidas
            $table->enum('estado', ['pendiente_jefe', 'pendiente_rrhh', 'aprobado', 'rechazado'])
                ->default('pendiente_jefe');

            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('cascade');
            $table->foreign('tiposalida_id')->references('id')->on('tiposalidas');
            $table->index(['periodo_id', 'periodo_type']);
            $table->foreign('jefe_id')->references('id')->on('persona');
            $table->foreign('rrhh_id')->references('id')->on('persona');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
