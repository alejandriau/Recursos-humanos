<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cenvi', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('fecha');

            $table->string('observacion', 100)->nullable();

            $table->string('pdf_cenvi', 250)->nullable();

            $table->integer('persona_id');
            $table->foreign('persona_id')
                ->references('id')
                ->on('persona')
                ->onDelete('cascade');

            $table->tinyInteger('estado')->default(1);

            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cenvi');
    }
};
