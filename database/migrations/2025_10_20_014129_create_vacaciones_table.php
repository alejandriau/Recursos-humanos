<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->integer('idPersona'); // Relación con la tabla personas
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('dias_tomados')->default(0);
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();

            // Clave foránea
            $table->foreign('idPersona')->references('id')->on('persona')->onDelete('cascade');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
