<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Area;
use App\Models\Direccion;
use App\Models\Secretaria;

class Direccion extends Model
{
        protected $table = 'direccion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'denominacion',
        'codigo',
        'encargado',
        'nivel',
        'estado',
        'idSecretaria',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    protected $dates = [
        'fechaRegistro',
        'fechaActualizacion',
    ];

    // Relaciones
    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'idSecretaria');
    }

    public function unidades()
    {
        return $this->hasMany(Unidad::class, 'idDireccion');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'idDireccion');
    }
}
