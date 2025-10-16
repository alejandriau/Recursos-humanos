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
            $table->unsignedBigInteger('user_id'); // Nuevo campo para el usuario
            $table->timestamps();

            // Clave foránea con referencia a la tabla pasivodos
            $table->foreign('idPasivodos')->references('id')->on('pasivodos')->onDelete('cascade');
            // Nueva clave foránea para users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
