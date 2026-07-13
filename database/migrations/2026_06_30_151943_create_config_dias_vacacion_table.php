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
        Schema::create('config_dias_vacacion', function (Blueprint $table) {
            $table->id();
            $table->integer('anios_desde');   // 1
            $table->integer('anios_hasta')->nullable(); // 4, o null para "en adelante"
            $table->integer('dias');          // 15, 20, 30
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_dias_vacacion');
    }
};
