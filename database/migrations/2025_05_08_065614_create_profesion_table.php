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
        Schema::create('profesion', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('diploma', 200)->nullable();
            $table->date('fechaDiploma')->nullable();
            $table->string('provisionN', 800)->nullable();
            $table->date('fechaProvision')->nullable();
            $table->string('universidad', 150)->nullable();
            $table->string('registro', 45)->nullable();
            $table->string('pdfDiploma', 400)->nullable();
            $table->string('pdfProvision', 400)->nullable();
            $table->string('cedulaProfesion', 45)->nullable();
            $table->string('pdfcedulap', 100)->nullable();
            $table->string('observacion', 500)->nullable();
            $table->integer('idPersona')->index('_idx');
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesion');
    }
};
