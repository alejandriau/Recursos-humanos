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
        Schema::create('contrato', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('numero', 45)->nullable();
            $table->date('fechaInicio');
            $table->date('fechaFin');
            $table->string('resulucion', 45)->nullable();
            $table->date('fechaarenda')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->integer('idPersona')->index('fk_contrato_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato');
    }
};
