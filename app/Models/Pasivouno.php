<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasivouno extends Model
{
    protected $table = 'pasivouno';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombrecompleto',
        'observacion',
        'estado',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    protected $casts = [
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
    ];
}
