<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas_conocimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150); // "Ciencias Económicas", "Ciencias Financieras", "Administración", "Contabilidad", etc.
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // Datos iniciales de ejemplo
        DB::table('areas_conocimiento')->insert([
            ['nombre' => 'Ciencias Económicas'],
            ['nombre' => 'Ciencias Financieras'],
            ['nombre' => 'Administración'],
            ['nombre' => 'Contabilidad'],
            ['nombre' => 'Auditoría'],
            ['nombre' => 'Gestión Pública'],
            ['nombre' => 'Economía'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('areas_conocimiento');
    }
};