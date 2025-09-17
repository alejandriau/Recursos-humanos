<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Area;
use App\Models\Direccion;
use App\Models\Secretaria;

class Secretaria extends Model
{
        protected $table = 'secretarias';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'denominacion',
        'encargado',
        'codigo',
        'nivel',
        'estado',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    protected $dates = [
        'fechaRegistro',
        'fechaActualizacion',
    ];

    // Relaciones
    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'idSecretaria');
    }

    public function unidades()
    {
        return $this->hasMany(Unidad::class, 'idSecretaria');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'idSecretaria');
    }
}
