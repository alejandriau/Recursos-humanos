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
        Schema::create('djbrenta', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha');
            $table->string('pdfrenta', 250)->nullable();
            $table->string('tipo', 600)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->integer('idPersona')->index('fk_djbrenrta_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('djbrenta');
    }
};
