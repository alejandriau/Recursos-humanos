<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historials', function (Blueprint $table) {
            // Campos originales modificados
            $table->id();
            $table->unsignedBigInteger('persona_id');
            $table->bigInteger('puesto_id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            // Estado cambiado a enum
            $table->enum('estado', ['activo', 'concluido', 'suspendido'])
                  ->default('activo');

            // Nuevos campos para tipo de movimiento y contrato
            $table->enum('tipo_movimiento', [
                'designacion_inicial',
                'movilidad',
                'reasignacion',
                'ascenso',
                'comision',
                'interinato',
                'encargo_funciones',
                'recontratacion'
            ])->default('designacion_inicial');

            $table->enum('tipo_contrato', [
                'permanente',
                'contrato_administrativo',
                'contrato_plazo_fijo',
                'contrato_obra',
                'honorarios'
            ])->default('permanente');

            // Campos para memo/documento
            $table->string('numero_memo')->nullable();
            $table->date('fecha_memo')->nullable();
            $table->string('archivo_memo')->nullable();

            // Campos para relaciones con registros anteriores
            $table->unsignedBigInteger('historial_anterior_id')->nullable();
            $table->unsignedBigInteger('puesto_anterior_id')->nullable();

            // Campos para comisiones e interinatos
            $table->boolean('conserva_puesto_original')->default(false);
            $table->unsignedBigInteger('puesto_original_id')->nullable();

            // Información adicional
            $table->text('motivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('salario', 10, 2)->nullable();
            $table->string('jornada_laboral')->default('completa');

            // Campos para contratos temporales
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('renovacion_automatica')->default(false);
            $table->integer('porcentaje_dedicacion')->default(100);

            // Timestamps y soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Relaciones
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');
            $table->foreign('historial_anterior_id')->references('id')->on('historials')->onDelete('set null');
            $table->foreign('puesto_anterior_id')->references('id')->on('puestos')->onDelete('set null');
            $table->foreign('puesto_original_id')->references('id')->on('puestos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historials');
    }
};
