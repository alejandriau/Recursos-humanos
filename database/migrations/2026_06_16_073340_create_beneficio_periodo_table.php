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
        Schema::create('beneficio_periodo', function (Blueprint $table) {
            $table->id();
            $table->integer("persona_id");
            $table->unsignedBigInteger('tiposalida_id');
            $table->unsignedBigInteger('gestion_id');
            $table->unsignedTinyInteger('mes')->nullable(); // 1-12, solo si periodicidad=mensual; null si anual/evento

            $table->enum('unidad', ['dias', 'horas','mixto']);
            $table->decimal('cantidad_asignada', 6, 2)->default(0);
            $table->decimal('cantidad_usada', 6, 2)->default(0);
            $table->decimal('cantidad_vencida', 6, 2)->default(0);
            $table->decimal('arrastre', 6, 2)->default(0);
            $table->decimal('saldo_disponible', 6, 2);
            $table->date('fecha_habilitacion')->nullable();
            $table->enum('estado', ['pendiente', 'activo', 'vencido', 'agotado'])->default('pendiente');

            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('cascade');
            $table->foreign('tiposalida_id')->references('id')->on('tiposalidas');
            $table->foreign('gestion_id')->references('id')->on('gestions');

            // único por mes si es mensual, único por gestión si es anual
            $table->unique(['persona_id', 'tiposalida_id', 'gestion_id', 'mes']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficio_periodo');
    }
};
