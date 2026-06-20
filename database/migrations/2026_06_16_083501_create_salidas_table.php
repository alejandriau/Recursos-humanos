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
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->date("fechasal");
            $table->time("horasal")->nullable();
            $table->date("fecharet");
            $table->time("horaret")->nullable();
            $table->decimal("cantidad")->nullable();
            $table->string("motivo")->nullable();
            $table->date("fechasol");
            $table->string("vobo");
            $table->integer("id_vobo");
            $table->string("estado");
            $table->string("observacion")->nullable();
            $table->string("img")->nullable();
            $table->integer("persona_id");
            $table->bigInteger("tiposalida_id")->unsigned();
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('cascade');
            $table->foreign("tiposalida_id")->references("id")->on("tiposalidas");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
