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
        Schema::create('cedulaidentidad', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('ci', 45)->nullable();
            $table->date('fechanacimiento')->nullable();
            $table->date('fechaVencimiento')->nullable();
            $table->string('expedido', 100)->nullable();
            $table->string('nacido', 1500)->nullable();
            $table->string('domicilio', 1500)->nullable();
            $table->integer('estado')->nullable()->default(1);
            $table->string('pdfcedula', 80)->nullable();
            $table->string('observacion', 300)->nullable();
            $table->integer('idPersona')->index('kf_cedula_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cedulaidentidad');
    }
};
