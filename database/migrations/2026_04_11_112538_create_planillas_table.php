<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('planillas', function (Blueprint $table) {
            $table->id();
            $table->integer('persona_id');

            $table->foreign('persona_id')
                ->references('id')
                ->on('persona')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->enum('tipo', ['planta', 'eventual'])->nullable();
            
            // Campos originales del Excel/DBF (adaptados a snake_case)
            $table->unsignedInteger('num')->nullable();
            $table->string('expedido', 10)->nullable();      // EXP
            $table->string('partida', 20)->nullable();
            $table->string('indi', 5)->nullable();
            $table->string('cargo', 150)->nullable();
            $table->decimal('h_basico', 12, 2)->nullable();
            $table->decimal('h_basejec', 12, 2)->nullable();
            $table->decimal('viatico', 12, 2)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_vencimiento')->nullable();   // F_VENCMTO
            $table->decimal('categ', 12, 2)->nullable();
            $table->decimal('categejec', 12, 2)->nullable();
            $table->decimal('tot_gan', 12, 2)->nullable();
            $table->unsignedTinyInteger('dia_trab')->nullable();
            $table->decimal('neto', 12, 2)->nullable();
            $table->date('f_cap_i')->nullable();
            $table->decimal('r_comun', 12, 2)->nullable();
            $table->decimal('c_afp', 12, 2)->nullable();
            $table->decimal('a_sol', 12, 2)->nullable();
            $table->string('s', 2)->nullable();
            $table->decimal('t_afp', 12, 2)->nullable();
            $table->decimal('bbv', 12, 2)->nullable();
            $table->string('futuro', 20)->nullable();
            $table->string('gestora', 20)->nullable();       // GESTORA
            $table->decimal('cuot_mor', 12, 2)->nullable();
            $table->decimal('ret_jud', 12, 2)->nullable();
            $table->decimal('falt_atr', 12, 2)->nullable();
            $table->decimal('fom_101', 12, 2)->nullable();
            $table->decimal('pa_iva', 12, 2)->nullable();
            $table->decimal('sal_iva', 12, 2)->nullable();
            $table->decimal('tot_deo', 12, 2)->nullable();
            $table->decimal('otros', 12, 2)->nullable();
            $table->decimal('otros_des', 12, 2)->nullable();
            $table->decimal('tot_des', 12, 2)->nullable();
            $table->decimal('tot_par', 12, 2)->nullable();
            $table->decimal('tot_parcom', 12, 2)->nullable();
            $table->decimal('liq_pag', 12, 2)->nullable();
            $table->string('cuenta', 30)->nullable();
            $table->decimal('sol_p', 12, 2)->nullable();
            $table->decimal('afp_p', 12, 2)->nullable();
            $table->decimal('fonvi_p', 12, 2)->nullable();
            $table->decimal('cns_p', 12, 2)->nullable();
            $table->decimal('t_labor', 12, 2)->nullable();
            $table->decimal('t_patro', 12, 2)->nullable();
            $table->decimal('t_carga', 12, 2)->nullable();
            $table->string('financia', 100)->nullable();
            $table->string('separa2', 20)->nullable();
            $table->string('cga', 20)->nullable();
            $table->string('cua', 20)->nullable();
            $table->text('des')->nullable();
            
            $table->timestamps();
            
            $table->index(['anio', 'mes']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('planillas');
    }
};