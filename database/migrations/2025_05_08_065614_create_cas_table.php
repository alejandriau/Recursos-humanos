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
        Schema::create('escala_bono_antiguedad', function (Blueprint $table) {
            $table->id();
            $table->integer('anio_inicio');
            $table->integer('anio_fin')->nullable();
            $table->decimal('porcentaje_bono', 5, 2);
            $table->string('rango_texto', 50);
            $table->text('base_legal')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['anio_inicio', 'anio_fin'], 'idx_rango_anios');
        });

        Schema::create('configuracion_salario_minimo', function (Blueprint $table) {
            $table->id();
            $table->integer('gestion');
            $table->decimal('monto_salario_minimo', 10, 2);
            $table->date('fecha_vigencia');
            $table->boolean('vigente')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['gestion', 'vigente'], 'idx_gestion_vigente');
        });

        Schema::create('cas', function (Blueprint $table) {
            $table->id();
            $table->integer('anios_servicio')->nullable();
            $table->integer('meses_servicio')->nullable();
            $table->integer('dias_servicio')->nullable();
            $table->decimal('porcentaje_bono', 5, 2)->nullable();
            $table->decimal('monto_bono', 10, 2)->nullable();
            $table->date('fecha_ingreso_institucion');
            $table->date('fecha_emision_cas');
            $table->date('fecha_presentacion_rrhh');
            $table->date('fecha_calculo_antiguedad');
            $table->string('periodo_calificacion', 100)->nullable();
            $table->string('meses_calificacion', 100)->nullable();
            $table->string('archivo_cas', 250)->nullable();
            $table->enum('estado_cas', ['vigente', 'vencido', 'procesado'])->default('vigente');
            $table->enum('nivel_alerta', ['normal', 'advertencia', 'urgente'])->default('normal');
            $table->boolean('aplica_bono_antiguedad')->default(false);
            $table->string('rango_antiguedad', 50)->nullable();
            $table->text('observaciones')->nullable();

            // Claves foráneas
            $table->integer('id_persona');
            $table->unsignedBigInteger('id_usuario_registro')->nullable();
            $table->unsignedBigInteger('id_escala_bono')->nullable();
            $table->unsignedBigInteger('id_salario_minimo')->nullable();

            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();

            // Claves foráneas constraints
            $table->foreign('id_persona')
                  ->references('id')->on('persona')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('id_usuario_registro')
                  ->references('id')->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreign('id_escala_bono')
                  ->references('id')->on('escala_bono_antiguedad')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreign('id_salario_minimo')
                  ->references('id')->on('configuracion_salario_minimo')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Índices
            $table->index(['id_persona', 'estado_cas'], 'idx_cas_persona_estado');
            $table->index(['fecha_calculo_antiguedad', 'nivel_alerta'], 'idx_cas_fecha_alerta');
            $table->index('aplica_bono_antiguedad', 'idx_cas_aplica_bono');
            $table->index('id_escala_bono', 'idx_cas_escala_bono');
            $table->index('id_salario_minimo', 'idx_cas_salario_minimo');
        });

        Schema::create('cas_historial', function (Blueprint $table) {
            $table->id();

            // Claves foráneas - CORREGIDO: usar unsignedBigInteger para consistencia
            $table->unsignedBigInteger('id_cas'); // Cambiado de integer a unsignedBigInteger
            $table->unsignedBigInteger('id_usuario')->nullable();

            // Campos del historial
            $table->enum('estado_anterior', ['vigente', 'vencido', 'procesado'])->nullable();
            $table->enum('estado_nuevo', ['vigente', 'vencido', 'procesado']);
            $table->enum('alerta_anterior', ['normal', 'advertencia', 'urgente'])->nullable();
            $table->enum('alerta_nuevo', ['normal', 'advertencia', 'urgente']);
            $table->text('observacion')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();

            // Claves foráneas constraints
            $table->foreign('id_cas')
                  ->references('id')->on('cas')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('id_usuario')
                  ->references('id')->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Índices
            $table->index('id_cas', 'idx_historial_cas');
            $table->index('id_usuario', 'idx_historial_usuario');
            $table->index('fecha_registro', 'idx_historial_fecha');
        });

        // Insertar datos iniciales de la escala de bonos
        DB::table('escala_bono_antiguedad')->insert([
            [
                'anio_inicio' => 2,
                'anio_fin' => 4,
                'porcentaje_bono' => 5.00,
                'rango_texto' => '2 - 4',
                'base_legal' => 'D.S. N° 20862 de 10/06/1985, D.S. N° 21060 de 29/08/1985, D.S. N° 21137 de 30/08/1985'
            ],
            [
                'anio_inicio' => 5,
                'anio_fin' => 7,
                'porcentaje_bono' => 11.00,
                'rango_texto' => '5 - 7',
                'base_legal' => 'D.S. N° 20862 de 10/06/1985, D.S. N° 21060 de 29/08/1985, D.S. N° 21137 de 30/08/1985'
            ],
            [
                'anio_inicio' => 8,
                'anio_fin' => 10,
                'porcentaje_bono' => 18.00,
                'rango_texto' => '8 - 10',
                'base_legal' => 'D.S. N° 20862 de 10/06/1985, D.S. N° 21060 de 29/08/1985, D.S. N° 21137 de 30/08/1985'
            ],
            [
                'anio_inicio' => 11,
                'anio_fin' => 14,
                'porcentaje_bono' => 26.00,
                'rango_texto' => '11 - 14',
                'base_legal' => 'D.S. N° 20862 de 10/06/1985, D.S. N° 21060 de 29/08/1985, D.S. N° 21137 de 30/08/1985'
            ],
            [
                'anio_inicio' => 15,
                'anio_fin' => 19,
                'porcentaje_bono' => 34.00,
                'rango_texto' => '15 - 19',
                'base_legal' => 'D.S. N° 20862 de 10/06/1985, D.S. N° 21060 de 29/08/1985, D.S. N° 21137 de 30/08/1985'
            ],
            [
                'anio_inicio' => 20,
                'anio_fin' => 24,
                'porcentaje_bono' => 42.00,
                'rango_texto' => '20 - 24',
                'base_legal' => 'D.S. N° 20862 de 10/06/1985, D.S. N° 21060 de 29/08/1985, D.S. N° 21137 de 30/08/1985'
            ],
            [
                'anio_inicio' => 25,
                'anio_fin' => null,
                'porcentaje_bono' => 50.00,
                'rango_texto' => '25 - adelante',
                'base_legal' => 'D.S. N° 20862 de 10/06/1985, D.S. N° 21060 de 29/08/1985, D.S. N° 21137 de 30/08/1985'
            ]
        ]);

        // Insertar datos iniciales de salario mínimo (ejemplo)
        DB::table('configuracion_salario_minimo')->insert([
            [
                'gestion' => 2024,
                'monto_salario_minimo' => 2400.00,
                'fecha_vigencia' => '2024-01-01',
                'vigente' => true,
                'observaciones' => 'Salario mínimo nacional vigente 2024'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cas_historial');
        Schema::dropIfExists('cas');
        Schema::dropIfExists('configuracion_salario_minimo');
        Schema::dropIfExists('escala_bono_antiguedad');
    }
};
