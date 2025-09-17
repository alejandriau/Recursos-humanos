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
        Schema::table('puesto', function (Blueprint $table) {
            $table->foreign(['idArea'], 'fk_area')->references(['id'])->on('area')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['idContrato'], 'fk_contrato')->references(['id'])->on('contrato')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['idDireccion'], 'fk_direccion')->references(['id'])->on('direccion')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['idSecretaria'], 'fk_secretaria')->references(['id'])->on('secretarias')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['idUnidad'], 'fk_unidada')->references(['id'])->on('unidad')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('puesto', function (Blueprint $table) {
            $table->dropForeign('fk_area');
            $table->dropForeign('fk_contrato');
            $table->dropForeign('fk_direccion');
            $table->dropForeign('fk_secretaria');
            $table->dropForeign('fk_unidada');
        });
    }
};
