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
        Schema::create('bachiller', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha')->nullable();
            $table->string('observacion', 250)->nullable();
            $table->string('otros', 250)->nullable();
            $table->integer('idPersona')->index('fk_bachiller_persona_idx');
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
        Schema::dropIfExists('bachiller');
    }
};
