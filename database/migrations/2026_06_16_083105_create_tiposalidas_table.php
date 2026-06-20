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
        Schema::create('tiposalidas', function (Blueprint $table) {
            $table->id();
            $table->string("descripcion");
            $table->string("sustLegal")->nullable();
            $table->string("expresa")->nullable();
            $table->integer("id_padre")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiposalidas');
    }
};
