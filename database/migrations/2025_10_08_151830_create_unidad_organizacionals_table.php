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
        Schema::create('unidad_organizacionals', function (Blueprint $table) {
            $table->id();
            $table->string('denominacion', 800);
            $table->string('codigo', 45)->nullable()->unique();
            $table->string('sigla', 20)->nullable();
            $table->enum('tipo', ['SECRETARIA', 'SERVICIO','DIRECCION','UNIDAD','AREA','PROGRAMA','PROYECTO']);
            $table->foreignId('idPadre')->nullable()->constrained('unidad_organizacionals')->onDelete('cascade');
            $table->boolean('esActivo')->default(true);
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->index(['tipo']);
            $table->index(['esActivo']);
            $table->index(['idPadre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_organizacionals');
    }
};
