<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasivouno', function (Blueprint $table) {
            $table->string('letra', 1)->nullable()->after('id');
        });

        // llenar la columna con la primera letra de nombrecompleto
        DB::statement("UPDATE pasivouno SET letra = LEFT(nombrecompleto,1) WHERE nombrecompleto IS NOT NULL");
    }

    public function down(): void
    {
        Schema::table('pasivouno', function (Blueprint $table) {
            $table->dropColumn('letra');
        });
    }
};