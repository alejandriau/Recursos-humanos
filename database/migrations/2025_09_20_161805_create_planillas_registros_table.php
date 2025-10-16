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
        Schema::create('planillas_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('planillas_empleados')->onDelete('cascade');
            $table->string('mes', 2);
            $table->year('ano');
            $table->integer('dias_trabajados')->default(30);
            $table->decimal('haber_basico', 12, 2)->default(0);
            $table->decimal('bono_antiguedad', 12, 2)->default(0);
            $table->decimal('otros_ingresos', 12, 2)->default(0);
            $table->decimal('total_ingresos', 12, 2)->default(0);
            $table->decimal('rc_iva', 12, 2)->default(0);
            $table->decimal('afp', 12, 2)->default(0);
            $table->decimal('otros_descuentos', 12, 2)->default(0);
            $table->decimal('total_descuentos', 12, 2)->default(0);
            $table->decimal('liquido_pagable', 12, 2)->default(0);
            $table->string('item')->nullable();
            $table->string('cuenta_bancaria')->nullable();
            $table->string('archivo_origen');
            $table->timestamps();

            $table->unique(['empleado_id', 'mes', 'ano']);
            $table->index(['mes', 'ano']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planillas_registros');
    }
};
