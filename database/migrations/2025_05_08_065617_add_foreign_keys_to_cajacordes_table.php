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
        Schema::table('cajacordes', function (Blueprint $table) {
            $table->foreign(['idPersona'], 'fk_cajadordes')->references(['id'])->on('persona')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajacordes', function (Blueprint $table) {
            $table->dropForeign('fk_cajadordes');
        });
    }
};
