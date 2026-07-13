<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ConfigDiasVacacion extends Model
{
     protected $table = 'config_dias_vacacion';
    protected $fillable = ['anios_desde', 'anios_hasta', 'dias', 'descripcion', 'activo'];
}