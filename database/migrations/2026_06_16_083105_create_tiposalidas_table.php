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
        Schema::create('tiposalidas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('sustLegal')->nullable();
            $table->string('expresa')->nullable();
            $table->foreignId("id_padre")
                ->nullable()
                ->constrained("tiposalidas")
                ->nullOnDelete(); // o cascadeOnDelete(), restrictOnDelete(), etc.

            // NUEVO
            // Si el tipo tiene un cupo controlado o es libre (comisión, salud, particular)
            $table->boolean('tiene_cupo')->default(false);

            $table->enum('unidad', ['dias', 'horas', 'mixto'])->nullable(); // null si tiene_cupo=false

            // Cómo se renueva el cupo
            $table->enum('periodicidad', ['ninguna', 'mensual', 'anual', 'evento'])->default('ninguna');
            // ninguna = cupo único que no se renueva (raro, pero por si acaso)
            // mensual = se renueva cada mes (ej: 2 HORAS)
            // anual   = se renueva cada gestión (ej: 2 DIAS, VACACION)
            // evento  = ligado a una fecha/condición específica (CUMPLEAÑOS, FALLECIMIENTO)

            $table->decimal('cantidad_default', 6, 2)->nullable(); // 2 horas, 2 dias — null si usa_tabla_antiguedad
            $table->boolean('permite_arrastre')->default(false);    // si el saldo no usado pasa al siguiente periodo
            $table->integer('max_veces_periodo')->nullable();       // ej: cumpleaños max 1 vez al año

            // Solo aplica a VACACION
            $table->boolean('usa_tabla_antiguedad')->default(false);

            $table->boolean('requiere_aprobacion_jefe')->default(true);
            $table->boolean('requiere_aprobacion_rrhh')->default(true);
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiposalidas');
    }
};
