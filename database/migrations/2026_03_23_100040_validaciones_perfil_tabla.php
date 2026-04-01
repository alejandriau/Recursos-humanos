<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validaciones_perfil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_puesto')->constrained('puestos');
            $table->integer('id_persona')->constrained('persona');
            $table->foreignId('id_usuario')->constrained('users');
            $table->boolean('resultado');
            $table->json('detalleValidacion')->nullable();
            $table->date('fechaValidacion');
            $table->timestamps();

            $table->index(['id_puesto', 'id_persona']);
            $table->index('fechaValidacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validaciones_perfil');
    }
};