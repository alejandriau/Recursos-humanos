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
        Schema::create('dependientes_discapacitados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('situacion_id')
                ->constrained('situaciones_especiales')
                ->onDelete('cascade');

            // DATOS DEL DEPENDIENTE
            $table->string('nombre_dependiente', 200);
            $table->string('parentesco', 100); // Hijo/a, Cónyuge, Padre, Madre
            $table->date('fecha_nacimiento')->nullable();

            // DISCAPACIDAD DEL DEPENDIENTE
            $table->string('tipo_discapacidad', 100);
            $table->decimal('porcentaje_discapacidad', 5, 2);
            $table->string('codigo_certificado_dependiente', 100)->nullable();

            // DOCUMENTOS
            $table->string('certificado_discapacidad_path', 500);
            $table->string('partida_nacimiento_path', 500)->nullable();
            $table->string('declaracion_jurada_path', 500)->nullable();

            // GRADO DE DEPENDENCIA
            $table->enum('grado_dependencia', ['total', 'parcial']);
            $table->text('necesidades_especiales')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependientes_discapacitados');
    }
};
