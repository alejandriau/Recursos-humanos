<?php
// app/Models/SincronizacionLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SincronizacionLog extends Model
{
    protected $fillable = [
        'ip_biometrico',
        'puerto',
        'dispositivo_serial',
        'estado',
        'mensaje',
        'total_obtenidas',
        'nuevas_importadas',
        'duplicadas',
        'con_error',
        'detalles',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'detalles' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];
}
