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
        Schema::create('certificados', function (Blueprint $table) {
            $table->id(); // Cambia a id autoincremental estándar
            $table->string('nombre', 500);
            $table->string('tipo', 80)->nullable();
            $table->string('categoria', 50)->nullable(); // Nuevo campo
            $table->date('fecha')->nullable();
            $table->date('fecha_vencimiento')->nullable(); // Nuevo campo
            $table->string('instituto', 80)->nullable();
            $table->string('pdfcerts', 80)->nullable();

            // Cambiar a convención estándar
            $table->integer('idPersona');
            $table->foreign('idPersona')
                ->references('id')
                ->on('persona') // Debe ser plural
                ->onDelete('cascade');

            $table->tinyInteger('estado')->default(1);
            $table->timestamps(); // Usar timestamps estándar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
