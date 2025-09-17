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
        Schema::create('formulario1', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha')->nullable();
            $table->string('observacion', 45)->nullable();
            $table->string('pdfform1', 45)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->integer('idPersona')->index('kf_formulario1_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulario1');
    }
};
