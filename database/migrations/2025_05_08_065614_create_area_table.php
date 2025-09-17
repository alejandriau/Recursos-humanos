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
        Schema::create('area', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('denominacion', 800);
            $table->string('codigo', 45)->nullable();
            $table->string('encargado', 200)->nullable();
            $table->string('nivel', 45)->nullable();
            $table->integer('idUnidad')->nullable()->index('fk_unid');
            $table->integer('idDireccion')->nullable()->index('fk_drieee');
            $table->integer('idSecretaria')->nullable()->index('fk_secreee_idx');
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizar')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area');
    }
};
