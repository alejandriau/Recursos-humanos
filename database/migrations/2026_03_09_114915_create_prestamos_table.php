<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            // Relación polimórfica para cualquier tipo de carpeta (pasivosuno, pasivosdos, personal)
            $table->string('carpeta_type'); // 'App\Models\Pasivosuno', 'App\Models\Pasivosdos', 'App\Models\Personal'
            $table->unsignedBigInteger('carpeta_id');
            
            $table->unsignedBigInteger('solicitante_id'); // Quién pide la carpeta
            $table->unsignedBigInteger('archivero_id')->nullable(); // Quién procesa el préstamo
            $table->unsignedBigInteger('user_id'); // Usuario que registra/crea el préstamo (puede ser el mismo solicitante)
            
            $table->enum('estado', [
                'pendiente',      // Solicitud inicial
                'aprobado',       // Aprobado por archivero
                'rechazado',      // Rechazado por archivero
                'prestado',       // Entregado físicamente
                'devuelto',       // Devuelto
                'vencido'         // No devuelto en fecha
            ])->default('pendiente');
            
            $table->date('fecha_solicitud');
            $table->date('fecha_prestamo')->nullable();   // Cuándo se entregó
            $table->date('fecha_devolucion_estimada')->nullable();
            $table->date('fecha_devolucion_real')->nullable();
            
            $table->text('motivo_solicitud')->nullable();  // Para qué necesita la carpeta
            $table->text('notas_archivero')->nullable();    // Notas internas del archivero
            $table->text('motivo_rechazo')->nullable();     // Si fue rechazado
            
            // Préstamo verbal (sin solicitud por sistema)
            $table->boolean('es_verbal')->default(false);
            
            $table->timestamps();
            $table->softDeletes(); // Para mantener historial
            
            // Índices compuestos
            $table->index(['carpeta_type', 'carpeta_id']);
            $table->index('estado');
            $table->index('fecha_devolucion_estimada');
            
            // Claves foráneas
            $table->foreign('solicitante_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('archivero_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};