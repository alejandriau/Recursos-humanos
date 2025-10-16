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
        Schema::create('planillas_empleados', function (Blueprint $table) {
            $table->id();
            $table->string('cedula')->unique();
            $table->string('nombre_completo');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('nacionalidad', 10)->default('BO');
            $table->string('puesto')->nullable();
            $table->string('departamento')->nullable();
            $table->string('cuenta_bancaria')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->timestamps();

            $table->index('cedula');
            $table->index('nombre_completo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planillas_empleados');
    }
};
