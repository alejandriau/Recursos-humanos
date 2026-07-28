<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispositivos_biometricos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');           // ej. "Entrada Principal", "Planta Baja RRHH"
            $table->string('ip');
            $table->integer('puerto')->default(4370);
            $table->string('ubicacion')->nullable();
            $table->integer('timeout')->default(60);
            $table->boolean('activo')->default(true);
            $table->timestamp('ultima_sincronizacion')->nullable();
            $table->string('ultimo_estado')->nullable(); // exito, error, etc.
            $table->timestamps();

            $table->unique(['ip', 'puerto']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositivos_biometricos');
    }
};
