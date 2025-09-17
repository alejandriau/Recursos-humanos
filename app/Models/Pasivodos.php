<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Seleccion;

class Pasivodos extends Model
{
    protected $table = 'pasivodos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombrecompleto',
        'letra',
        'observacion',
        'estado',
        'fechaRegistro',
        'fechaActualizacion'
    ];

    protected $casts = [
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
    ];
    // Relación: un pasivo tiene muchas selecciones
    public function seleccions()
    {
        return $this->hasMany(Seleccion::class, 'idPasivodos');
    }
}
