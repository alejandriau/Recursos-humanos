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
        Schema::create('vacacion_movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('periodo_id');         // qué período se afecta
            $table->enum('tipo', ['credito', 'debito', 'vencimiento', 'arrastre']);
            $table->date('fecha');
            $table->date('fecha_inicio')->nullable();          // para débitos (inicio de vacación)
            $table->date('fecha_fin')->nullable();             // para débitos (fin de vacación)
            $table->decimal('cantidad', 5, 1);
            $table->decimal('saldo_anterior', 5, 1);
            $table->decimal('saldo_posterior', 5, 1);
            $table->unsignedBigInteger('salida_id')->nullable(); // FK a salidas cuando es débito
            $table->string('descripcion')->nullable();
            $table->unsignedBigInteger('registrado_por')->nullable();

            $table->foreign('periodo_id')->references('id')->on('vacacion_periodos');
            $table->foreign('salida_id')->references('id')->on('salidas');
            $table->foreign('registrado_por')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacacion_movimientos');
    }
};
