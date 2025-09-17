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
        Schema::create('seleccions', function (Blueprint $table) {
            $table->id();
            $table->integer('idPasivodos');
            $table->string('registro', 250)->nullable();
            $table->timestamps();

            // Clave foránea con referencia a la tabla pasivodos
            $table->foreign('idPasivodos')->references('id')->on('pasivodos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seleccions');
    }
};
