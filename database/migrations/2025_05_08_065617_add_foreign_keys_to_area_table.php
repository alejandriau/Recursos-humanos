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
        Schema::table('area', function (Blueprint $table) {
            $table->foreign(['idDireccion'], 'fk_drieee')->references(['id'])->on('direccion')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['idSecretaria'], 'fk_secreee')->references(['id'])->on('secretarias')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['idUnidad'], 'fk_unid')->references(['id'])->on('unidad')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area', function (Blueprint $table) {
            $table->dropForeign('fk_drieee');
            $table->dropForeign('fk_secreee');
            $table->dropForeign('fk_unid');
        });
    }
};
