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
        Schema::create('afps', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('cua', 45);
            $table->string('observacion', 500)->nullable();
            $table->string('pdfafps', 250)->nullable();
            $table->integer('idPersona')->index('fk_afpes_idx');
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
        Schema::dropIfExists('afps');
    }
};
