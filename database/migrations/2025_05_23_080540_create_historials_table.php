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
        Schema::create('historials', function (Blueprint $table) {

            $table->id();
            $table->integer('idPersona');
            $table->integer('idPuesto');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamps();

            // Relaciones
            $table->foreign('idPersona')->references('id')->on('persona')->onDelete('cascade');
            $table->foreign('idPuesto')->references('id')->on('puesto')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historials');
    }
};
