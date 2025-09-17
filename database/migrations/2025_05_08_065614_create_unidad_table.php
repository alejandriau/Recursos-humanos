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
        Schema::create('unidad', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('denominacion', 800)->nullable();
            $table->string('codigo', 45)->nullable();
            $table->string('encargado', 45)->nullable();
            $table->string('nivel', 45)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->integer('idSecretaria')->nullable()->index('kf_secre_idx');
            $table->integer('idDireccion')->nullable()->index('kf_direcc');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad');
    }
};
