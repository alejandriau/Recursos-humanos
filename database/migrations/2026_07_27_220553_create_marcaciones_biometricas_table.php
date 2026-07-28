<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marcaciones_biometricas', function (Blueprint $table) {
            $table->foreignId('dispositivo_id')->nullable()->after('id')
                ->constrained('dispositivos_biometricos')->nullOnDelete();
        });

        Schema::table('sincronizacion_logs', function (Blueprint $table) {
            $table->foreignId('dispositivo_id')->nullable()->after('id')
                ->constrained('dispositivos_biometricos')->nullOnDelete();
            // el mensaje de error puede ser largo (incluye SQL), ampliamos la columna
            $table->text('mensaje')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('marcaciones_biometricas', function (Blueprint $table) {
            $table->dropForeign(['dispositivo_id']);
            $table->dropColumn('dispositivo_id');
        });

        Schema::table('sincronizacion_logs', function (Blueprint $table) {
            $table->dropForeign(['dispositivo_id']);
            $table->dropColumn('dispositivo_id');
        });
    }
};
