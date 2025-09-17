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
        Schema::create('cajacordes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha');
            $table->string('codigo', 45)->nullable();
            $table->string('otros', 45)->nullable();
            $table->string('pdfcaja', 250)->nullable();
            $table->integer('idPersona')->index('fk_cajadordes_idx');
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
        Schema::dropIfExists('cajacordes');
    }
};
