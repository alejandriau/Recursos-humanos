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
        Schema::table('historials', function (Blueprint $table) {
            // Cambiar nombres de columnas existentes para seguir convenciones Laravel
            //$table->renameColumn('idPersona', 'persona_id');
            //$table->renameColumn('idPuesto', 'puesto_id');

            // Agregar nuevos campos para tipo de movimiento
            $table->enum('tipo_movimiento', [
                'designacion_inicial',
                'movilidad',
                'reasignacion',
                'ascenso',
                'comision',
                'interinato',
                'encargo_funciones',
                'recontratacion'
            ])->default('designacion_inicial')->after('puesto_id');

            // Tipo de contrato
            $table->enum('tipo_contrato', [
                'permanente',
                'contrato_administrativo',
                'contrato_plazo_fijo',
                'contrato_obra',
                'honorarios'
            ])->default('permanente')->after('tipo_movimiento');

            // Cambiar tipo de estado de boolean a enum
            $table->enum('estado', ['activo', 'concluido', 'suspendido'])
                  ->default('activo')
                  ->change();

            // Campos para memo/documento
            $table->string('numero_memo')->nullable()->after('estado');
            $table->date('fecha_memo')->nullable()->after('numero_memo');
            $table->string('archivo_memo')->nullable()->after('fecha_memo');

            // Campos para relaciones con registros anteriores
            $table->unsignedBigInteger('historial_anterior_id')->nullable()->after('archivo_memo');
            $table->unsignedBigInteger('puesto_anterior_id')->nullable()->after('historial_anterior_id');

            // Campos para comisiones e interinatos
            $table->boolean('conserva_puesto_original')->default(false)->after('puesto_anterior_id');
            $table->unsignedBigInteger('puesto_original_id')->nullable()->after('conserva_puesto_original');

            // Información adicional
            $table->text('motivo')->nullable()->after('puesto_original_id');
            $table->text('observaciones')->nullable()->after('motivo');
            $table->decimal('salario', 10, 2)->nullable()->after('observaciones');
            $table->string('jornada_laboral')->default('completa')->after('salario');

            // Campos para contratos temporales
            $table->date('fecha_vencimiento')->nullable()->after('jornada_laboral');
            $table->boolean('renovacion_automatica')->default(false)->after('fecha_vencimiento');
            $table->integer('porcentaje_dedicacion')->default(100)->after('renovacion_automatica');

            // Soft deletes
            $table->softDeletes();

            // Agregar nuevas foreign keys
            $table->foreign('historial_anterior_id')->references('id')->on('historials')->onDelete('set null');
            $table->foreign('puesto_anterior_id')->references('id')->on('puesto')->onDelete('set null');
            $table->foreign('puesto_original_id')->references('id')->on('puesto')->onDelete('set null');
        });

        // Actualizar los registros existentes para que sean designaciones iniciales
        DB::table('historials')->update([
            'tipo_movimiento' => 'designacion_inicial',
            'estado' => DB::raw("CASE WHEN estado = 1 THEN 'activo' ELSE 'concluido' END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historials', function (Blueprint $table) {
            // Revertir cambios
            $table->renameColumn('persona_id', 'idPersona');
            $table->renameColumn('puesto_id', 'idPuesto');

            // Cambiar estado de vuelta a boolean
            $table->boolean('estado')->default(1)->change();

            // Eliminar foreign keys
            $table->dropForeign(['historial_anterior_id']);
            $table->dropForeign(['puesto_anterior_id']);
            $table->dropForeign(['puesto_original_id']);

            // Eliminar columnas agregadas
            $table->dropColumn([
                'tipo_movimiento',
                'tipo_contrato',
                'numero_memo',
                'fecha_memo',
                'archivo_memo',
                'historial_anterior_id',
                'puesto_anterior_id',
                'conserva_puesto_original',
                'puesto_original_id',
                'motivo',
                'observaciones',
                'salario',
                'jornada_laboral',
                'fecha_vencimiento',
                'renovacion_automatica',
                'porcentaje_dedicacion',
                'deleted_at'
            ]);
        });
    }
};
