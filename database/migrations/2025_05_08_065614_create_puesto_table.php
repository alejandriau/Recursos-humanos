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
        Schema::create('puesto', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('denominacion', 500)->nullable();
            $table->string('nivelgerarquico', 500);
            $table->string('item', 45)->nullable();
            $table->string('maual', 45)->nullable();
            $table->string('perfil', 800)->nullable();
            $table->double('haber')->nullable();
            $table->integer('nivel')->nullable();
            $table->string('puestocol', 45)->nullable();
            $table->string('tipo', 400)->nullable();
            $table->integer('idArea')->nullable()->index('fk_area_idx');
            $table->integer('idUnidad')->nullable()->index('fk_unidada_idx');
            $table->integer('idDireccion')->nullable()->index('fk_direccion_idx');
            $table->integer('idSecretaria')->nullable()->index('fk_secretaria_idx');
            $table->integer('idContrato')->nullable()->index('fk_contrato_idx');
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puesto');
    }
};
