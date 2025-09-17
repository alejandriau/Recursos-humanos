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
        Schema::create('persona', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('ci', 45)->unique('ci_unique');
            $table->string('nombre', 100);
            $table->string('apellidoPat', 70)->nullable();
            $table->string('apellidoMat', 70)->nullable();
            $table->date('fechaIngreso')->nullable();
            $table->date('fechaNacimiento')->nullable();
            $table->string('sexo', 45);
            $table->integer('telefono')->nullable();
            $table->longText('observaciones')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->string('foto', 100)->nullable();
            $table->string('tipo', 100)->nullable();
            $table->integer('idPuesto')->nullable()->index('fk_idpuesto_idx');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
