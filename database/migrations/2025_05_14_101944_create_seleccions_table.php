<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        
        // 2. Crear la nueva tabla seleccions con la estructura correcta
        Schema::create('seleccions', function (Blueprint $table) {
            $table->id();
            
            // Relación polimórfica para cualquier tipo de carpeta
            $table->string('carpeta_type'); // 'pasivosuno', 'pasivosdos', 'personal'
            $table->unsignedBigInteger('carpeta_id');
            
            $table->string('registro', 250)->nullable();
            $table->enum('tipo_seleccion', ['temporal', 'prestamo_directo'])->default('temporal');
            $table->unsignedBigInteger('user_id'); // Usuario que selecciona
            $table->timestamps();
            
            // Índices
            $table->index(['carpeta_type', 'carpeta_id']);
            $table->index('user_id');
            
            // Claves foráneas - Nota: No podemos poner foreign key directa porque carpeta_type varía
            // Esto se manejará a nivel de aplicación
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

};