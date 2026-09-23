// database/migrations/xxxx_xx_xx_xxxxxx_create_actividad_asistencia_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad_asistencia', function (Blueprint $table) {
            $table->integer('id', true);

            // FK a la actividad
            $table->integer('actividad_id');
            $table->foreign('actividad_id')
                  ->references('id')->on('actividad')
                  ->onDelete('cascade');

            // FK a la persona (usa tu tabla persona)
            $table->integer('persona_id');
            $table->foreign('persona_id')
                  ->references('id')->on('persona')
                  ->onDelete('cascade');

            // Momento exacto del escaneo/registro
            $table->timestamp('hora_registro')->useCurrent();

            // 1 = presente, 2 = tardanza, 3 = justificado, 4 = ausente, 0 = anulado
            $table->tinyInteger('estado')->default(1);

            // 1 = operador escaneó el QR del empleado
            // 2 = empleado escaneó el QR del evento (autoservicio)
            // 3 = registro manual
            $table->tinyInteger('metodo_registro')->default(1);

            // Texto crudo del QR (para auditoría y depuración)
            $table->longText('qr_raw')->nullable();

            // Quién registró (operador logueado). Null si fue autoservicio.
            $table->foreignId('registrado_por')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->longText('observaciones')->nullable();

            $table->timestamp('fechaRegistro')->useCurrent();
            $table->timestamp('fechaActualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();

            // ⛔ Un empleado solo puede tener UNA asistencia por actividad
            $table->unique(['actividad_id', 'persona_id'], 'asistencia_unica_por_actividad');

            // Índices para reportes
            $table->index(['actividad_id', 'estado'], 'asistencia_actividad_estado_index');
            $table->index(['persona_id', 'hora_registro'], 'asistencia_persona_hora_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_asistencia');
    }
};
