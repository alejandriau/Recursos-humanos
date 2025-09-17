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
        Schema::create('cenvi', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha');
            $table->string('observacion', 100)->nullable();
            $table->string('pdfcenvi', 250)->nullable();
            $table->integer('idPersona')->index('fk_cenvi_idx');
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('FechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cenvi');
    }
};
