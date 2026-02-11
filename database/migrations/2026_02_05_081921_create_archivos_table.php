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
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->string('extension');
            $table->string('tipo'); // word, excel, pdf, etc.
            $table->string('tema'); // categoría/tema del archivo
            $table->text('descripcion')->nullable();
            $table->integer('tamano');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
