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
        Schema::create('cmatrimonio', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha')->nullable();
            $table->string('descripcion', 250)->nullable();
            $table->string('pdfmatrimonio', 250)->nullable();
            $table->integer('idPersona')->index('fk_matrimonio_idx');
            $table->tinyInteger('estado')->default(1);
            $table->string('fechaRegistro', 45)->default('CURRENT_TIMESTAMP');
            $table->string('fechaActualizacion', 45)->nullable()->default('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmatrimonio');
    }
};
