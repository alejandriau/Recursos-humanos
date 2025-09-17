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
        Schema::create('memopuesto', function (Blueprint $table) {
            $table->integer('idmemopuesto', true);
            $table->string('cargo', 500);
            $table->string('dependenciaSecr', 600);
            $table->string('nivelGerarquico', 800);
            $table->string('item', 45)->unique('item_unique');
            $table->double('haber');
            $table->tinyInteger('estado')->default(1);
            $table->date('fecha');
            $table->string('memoPdf', 250)->nullable();
            $table->integer('id_persona')->index('id_persona');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memopuesto');
    }
};
