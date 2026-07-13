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
        Schema::create('vacacion_periodos', function (Blueprint $table) {
            $table->id();
            $table->integer('persona_id');
            $table->unsignedBigInteger('gestion_id');
            $table->unsignedBigInteger('cas_id')->nullable(); // si aplica calificación externa
            $table->integer('numero_periodo')->unsigned();
            $table->date('fecha_habilitacion');               // ingreso + 1 año + 1 día
            $table->integer('anios_antiguedad');              // calculado al momento de habilitar
            $table->integer('dias_asignados');                // 15, 20, o 30 según antigüedad
            $table->decimal('dias_usados', 5, 1)->default(0);
            $table->decimal('dias_vencidos', 5, 1)->default(0);
            $table->decimal('saldo_disponible', 5, 1);       // dias_asignados - usados - vencidos + arrastre
            $table->decimal('dias_arrastre', 5, 1)->default(0); // saldo que viene del período anterior
            $table->boolean('periodo_vencido')->default(false);
            $table->string('estado')->default('pendiente');  // pendiente, activo, vencido, agotado
            $table->text('observacion')->nullable();

            $table->foreign('persona_id')->references('id')->on('persona');
            $table->foreign('gestion_id')->references('id')->on('gestions');
            $table->foreign('cas_id')->references('id')->on('cas');
            $table->unique(['persona_id', 'numero_periodo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacacion_periodos');
    }
};
