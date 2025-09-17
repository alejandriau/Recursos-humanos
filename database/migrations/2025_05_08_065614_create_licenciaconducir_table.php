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
        Schema::create('licenciaconducir', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fechavencimiento');
            $table->char('categoria', 1);
            $table->string('descripcion', 500)->nullable();
            $table->string('pdflicc', 250)->nullable();
            $table->integer('idPersona')->index('fk_licenciac_idx');
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenciaconducir');
    }
};
