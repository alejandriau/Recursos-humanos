<?php
// database/migrations/2026_07_27_create_sincronizacion_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sincronizacion_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_biometrico');
            $table->integer('puerto')->default(4370);
            $table->string('dispositivo_serial')->nullable();
            $table->enum('estado', ['iniciado', 'exito', 'parcial', 'error'])->default('iniciado');
            $table->text('mensaje')->nullable();
            $table->integer('total_obtenidas')->default(0);
            $table->integer('nuevas_importadas')->default(0);
            $table->integer('duplicadas')->default(0);
            $table->integer('con_error')->default(0);
            $table->json('detalles')->nullable();
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sincronizacion_logs');
    }
};
