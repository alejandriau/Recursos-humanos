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
        Schema::create('compromiso', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('compromiso1', 255)->nullable();
            $table->string('pdfcomp1', 255)->nullable();
            $table->string('compromiso2', 255)->nullable();
            $table->string('pdfcomp2', 255)->nullable();
            $table->string('compromiso3', 255)->nullable();
            $table->string('pdfcomp3', 255)->nullable();
            $table->integer('idPersona')->index('fk_compromisos_idx');
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compromiso');
    }
};
