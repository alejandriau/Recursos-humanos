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
        Schema::create('cas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('anios', 45)->nullable();
            $table->string('meses', 45)->nullable();
            $table->string('dias', 45)->nullable();
            $table->date('fechaEmision');
            $table->date('fechaTiempo');
            $table->string('pdfcas', 250)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->integer('idPersona')->index('fk_cas_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cas');
    }
};
