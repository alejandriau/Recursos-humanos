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
        Schema::create('feriados', function (Blueprint $table) {
            $table->id();
            $table->date("fechaf");
            $table->string("descripcion");
            $table->bigInteger("gestion_id")->unsigned();
            $table->foreign("gestion_id")->references("id")->on("gestions");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feriados');
    }
};
