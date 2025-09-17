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
        Schema::table('unidad', function (Blueprint $table) {
            $table->foreign(['idDireccion'], 'kf_direcc')->references(['id'])->on('direccion')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['idSecretaria'], 'kf_secre')->references(['id'])->on('secretarias')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidad', function (Blueprint $table) {
            $table->dropForeign('kf_direcc');
            $table->dropForeign('kf_secre');
        });
    }
};
