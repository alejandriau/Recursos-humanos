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
        Schema::create('cas_historial_bonos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cas');

            // Campos de bono - porcentaje
            $table->decimal('porcentaje_bono_anterior', 5, 2)->nullable();
            $table->decimal('porcentaje_bono_nuevo', 5, 2)->nullable();

            // Campos de bono - monto
            $table->decimal('monto_bono_anterior', 10, 2)->nullable();
            $table->decimal('monto_bono_nuevo', 10, 2)->nullable();

            // Campos de salario mínimo
            $table->unsignedBigInteger('id_salario_minimo_anterior')->nullable();
            $table->unsignedBigInteger('id_salario_minimo_nuevo')->nullable();

            // Campos de antigüedad
            $table->integer('anios_servicio_anterior')->nullable();
            $table->integer('anios_servicio_nuevo')->nullable();
            $table->integer('meses_servicio_anterior')->nullable();
            $table->integer('meses_servicio_nuevo')->nullable();
            $table->integer('dias_servicio_anterior')->nullable();
            $table->integer('dias_servicio_nuevo')->nullable();

            // Información del cambio
            $table->string('tipo_cambio', 50); // 'antiguedad', 'salario', 'ambos', 'inicial', 'ajuste'
            $table->text('observacion')->nullable();
            $table->timestamp('fecha_cambio')->useCurrent();

            // Usuario que realizó el cambio (si aplica)
            $table->unsignedBigInteger('id_usuario')->nullable();

            // Claves foráneas
            $table->foreign('id_cas')
                  ->references('id')->on('cas')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('id_salario_minimo_anterior')
                  ->references('id')->on('configuracion_salario_minimo')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreign('id_salario_minimo_nuevo')
                  ->references('id')->on('configuracion_salario_minimo')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreign('id_usuario')
                  ->references('id')->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Índices para optimizar consultas
            $table->index(['id_cas', 'fecha_cambio'], 'idx_historial_bonos_cas_fecha');
            $table->index('tipo_cambio', 'idx_historial_bonos_tipo');
            $table->index('id_salario_minimo_nuevo', 'idx_historial_bonos_salario');
            $table->index('fecha_cambio', 'idx_historial_bonos_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cas_historial_bonos');
    }
};
