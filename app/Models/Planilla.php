<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    use HasFactory;
    
    protected $table = 'planillas';
    protected $fillable = [
        'persona_id', 'anio', 'mes', 'num', 'tipo', 'expedido', 'partida', 'indi', 'cargo',
        'h_basico', 'h_basejec', 'viatico', 'fecha_ingreso', 'fecha_vencimiento', 'categ', 'categejec',
        'tot_gan', 'dia_trab', 'neto', 'f_cap_i', 'r_comun', 'c_afp', 'a_sol', 's',
        't_afp', 'bbv', 'futuro', 'gestora', 'cuot_mor', 'ret_jud', 'falt_atr',
        'fom_101', 'pa_iva', 'sal_iva', 'tot_deo', 'otros', 'otros_des', 'tot_des',
        'tot_par', 'tot_parcom', 'liq_pag', 'cuenta', 'sol_p', 'afp_p', 'fonvi_p',
        'cns_p', 't_labor', 't_patro', 't_carga', 'financia', 'separa2', 'cga', 'cua', 'des'
    ];
}