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
        // 1️⃣ Crear tabla sin FK autoreferencial
        Schema::create('unidad_organizacionals', function (Blueprint $table) {
            $table->id();
            $table->string('denominacion', 800);
            $table->string('codigo', 45)->nullable()->unique();
            $table->string('sigla', 20)->nullable();
            $table->enum('tipo', ['SECRETARIA', 'SERVICIO','DIRECCION','UNIDAD','AREA','PROGRAMA','PROYECTO']);
            $table->unsignedBigInteger('idPadre')->nullable(); // solo columna
            $table->boolean('esActivo')->default(true);
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->index(['tipo']);
            $table->index(['esActivo']);
            $table->index(['idPadre']);
        });

        // 2️⃣ Agregar FK autoreferencial en otra instrucción
        Schema::table('unidad_organizacionals', function (Blueprint $table) {
            $table->foreign('idPadre')
                  ->references('id')
                  ->on('unidad_organizacionals')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero eliminar la FK si existe
        Schema::table('unidad_organizacionals', function (Blueprint $table) {
            $table->dropForeign(['idPadre']);
        });

        Schema::dropIfExists('unidad_organizacionals');
    }
};
