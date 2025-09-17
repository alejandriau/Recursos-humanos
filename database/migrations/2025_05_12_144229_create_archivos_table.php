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
        Schema::create('archivos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('idPersona'); // ❗ NO UNSIGNED
            $table->string('tipoDocumento');
            $table->string('rutaArchivo');
            $table->string('nombreOriginal');
            $table->text('observaciones')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('FechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->foreign('idPersona')->references('id')->on('persona')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
