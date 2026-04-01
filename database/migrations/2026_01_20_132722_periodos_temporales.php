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
        Schema::create('periodos_temporales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('situacion_id')
                ->constrained('situaciones_especiales')
                ->onDelete('cascade');

            // DATOS ESPECÍFICOS
            $table->date('fecha_probable_parto')->nullable(); // Para embarazo
            $table->date('fecha_nacimiento')->nullable(); // Para paternidad/lactancia
            $table->date('fecha_inicio_lactancia')->nullable();
            $table->date('fecha_fin_lactancia')->nullable();

            // PERMISOS LEGALES
            $table->integer('dias_prenatales')->default(0);
            $table->integer('dias_postnatales')->default(0);
            $table->integer('dias_lactancia')->default(0);
            $table->integer('dias_paternidad')->default(0);

            // DOCUMENTOS MÉDICOS
            $table->string('certificado_embarazo_path', 500)->nullable();
            $table->string('certificado_nacimiento_path', 500)->nullable();
            $table->string('certificado_lactancia_path', 500)->nullable();

            // CONTROL DE TIEMPOS
            $table->integer('dias_usados')->default(0);
            $table->integer('dias_restantes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodos_temporales');
    }
};
