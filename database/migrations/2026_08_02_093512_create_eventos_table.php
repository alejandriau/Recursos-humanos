<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');           // Ej: "Día de la Independencia"
            $table->string('tipo');             // nacional, amistad, navidad, año_nuevo
            $table->date('fecha');              // Se guarda con año cualquiera, solo importa mes-día
            $table->boolean('es_recurrente')->default(true); // Se repite todos los años
            $table->string('mensaje');          // "¡Feliz 6 de Agosto! 🇧🇴"
            $table->string('color_primario')->default('#3B82F6');
            $table->string('color_secundario')->default('#1E40AF');
            $table->string('icono')->nullable(); // Emoji: 🇧🇴 🎄 🤝
            $table->string('efecto')->default('confeti'); // confeti, nieve, corazones, banderas
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
