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
// En el modelo PasivosUno (y similar en PasivosDos y Personal)
// En el modelo PasivosUno (y similar en PasivosDos y Personal)
    public function selecciones()
    {
        return $this->morphMany(Seleccion::class, 'carpeta');
    }
    
}
