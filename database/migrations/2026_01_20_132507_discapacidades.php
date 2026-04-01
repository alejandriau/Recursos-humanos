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
        Schema::create('discapacidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('situacion_id')
                ->constrained('situaciones_especiales')
                ->onDelete('cascade');

            // DATOS BÁSICOS DE DISCAPACIDAD
            $table->string('tipo', 100); // Física, Visual, Auditiva, Intelectual, Psicosocial
            $table->enum('grado', ['leve', 'moderado', 'severa']);
            $table->decimal('porcentaje', 5, 2)->nullable(); // 40.50%

            // CERTIFICACIÓN OFICIAL
            $table->string('codigo_certificado', 100)->nullable();
            $table->string('entidad_certificadora', 200);
            $table->date('fecha_certificacion');
            $table->date('fecha_vencimiento')->nullable();

            // TUTOR (si aplica)
            $table->integer('tutor_id')->nullable();

            $table->foreign('tutor_id')
                ->references('id')
                ->on('persona')
                ->nullOnDelete();

            $table->string('parentesco_tutor', 100)->nullable();

            // DOCUMENTOS
            $table->string('certificado_medico_path', 500)->nullable();
            $table->string('resolucion_conadis_path', 500)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discapacidades');
    }
};
