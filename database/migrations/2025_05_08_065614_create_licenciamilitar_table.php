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
        Schema::create('licenciamilitar', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('codigo', 45)->nullable();
            $table->date('fecha')->nullable();
            $table->string('serie', 45)->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->string('pdflic', 80)->nullable();
            $table->integer('idPersona')->index('fk_cermilitar_idx');
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
        Schema::dropIfExists('licenciamilitar');
    }
};
