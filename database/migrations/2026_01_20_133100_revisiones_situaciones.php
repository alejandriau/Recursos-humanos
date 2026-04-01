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
        Schema::create('revisiones_situaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('situacion_id')
                ->constrained('situaciones_especiales')
                ->onDelete('cascade');

            $table->foreignId('revisado_por')->constrained('users');
            $table->text('observaciones');
            $table->enum('resultado', ['aprobado', 'observado', 'rechazado']);
            $table->date('proxima_revision')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisiones_situaciones');
    }
};
