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
        Schema::create('otroscertific', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('tipocertificado', 45)->nullable();
            $table->date('fecha')->nullable();
            $table->string('descripcion', 45)->nullable();
            $table->string('pdfotrosc', 45)->nullable();
            $table->integer('idPersona')->nullable()->index('fk_hijos_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otroscertific');
    }
};
