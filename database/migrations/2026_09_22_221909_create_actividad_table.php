// database/migrations/xxxx_xx_xx_xxxxxx_create_actividad_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad', function (Blueprint $table) {
            $table->integer('id', true);

            $table->string('nombre', 150);                  // "Actividad en Plaza Principal"
            $table->longText('descripcion')->nullable();
            $table->string('lugar', 200)->nullable();       // "Plaza 14 de Septiembre"

            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            // Después de esta hora se marca como "tardanza" automáticamente
            $table->time('hora_limite_puntual')->nullable();

            // 1 = activo (recibiendo asistencias), 0 = cerrado
            $table->tinyInteger('estado')->default(1);

            // Token único para generar el QR del evento (modo autoservicio, opcional)
            $table->string('token_qr', 64)->nullable()->unique('actividad_token_qr_unique');

            // ¿Se permite marcar asistencia a mano? (por si alguien no trajo QR)
            $table->tinyInteger('permite_manual')->default(1);

            // Quién creó la actividad
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();

            // Índice para búsquedas por fecha y estado
            $table->index(['fecha', 'estado'], 'actividad_fecha_estado_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad');
    }
};
