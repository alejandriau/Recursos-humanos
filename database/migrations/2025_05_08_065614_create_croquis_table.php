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
        Schema::create('croquis', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('direccion', 500);
            $table->string('descripcion', 500)->nullable();
            $table->string('longetud', 100);
            $table->string('latitud', 100);
            $table->tinyInteger('estado')->default(1);
            $table->integer('idPersona')->index('fk_croquis_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('croquis');
    }
};
