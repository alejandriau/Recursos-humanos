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
        Schema::create('bajasaltas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha');
            $table->string('motivo', 600);
            $table->string('observacion', 800)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->string('pdfbaja', 250)->nullable();
            $table->integer('idPersona')->index('fk_bajas_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bajasaltas');
    }
};
