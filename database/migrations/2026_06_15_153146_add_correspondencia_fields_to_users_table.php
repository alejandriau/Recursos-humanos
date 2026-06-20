<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('usuario')
                ->nullable()
                ->unique()
                ->after('email');

            $table->string('ci', 20)
                ->nullable()
                ->index()
                ->after('usuario');

            $table->enum('origen', ['local', 'correspondencia'])
                ->default('local')
                ->after('ci');

            $table->string('email')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropUnique(['usuario']);
            $table->dropIndex(['ci']);

            $table->dropColumn([
                'usuario',
                'ci',
                'origen'
            ]);

            $table->string('email')
                ->nullable(false)
                ->change();
        });
    }
};