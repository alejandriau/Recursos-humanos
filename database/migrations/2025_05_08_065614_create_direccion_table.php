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
        Schema::create('direccion', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('denominacion', 500);
            $table->string('codigo', 45)->nullable();
            $table->string('encargado', 45)->nullable();
            $table->string('nivel', 45)->nullable();
            $table->tinyInteger('estado');
            $table->integer('idSecretaria')->nullable()->index('fk_secretaria1_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direccion');
    }
};
