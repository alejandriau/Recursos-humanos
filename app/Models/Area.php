<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Area;
use App\Models\Direccion;
use App\Models\Secretaria;
use App\Models\Unidad;

class Area extends Model
{


    // Nombre de la tabla (opcional si el nombre es plural de la clase)
    protected $table = 'area';

    // Clave primaria
    protected $primaryKey = 'id';

    // Si no usas timestamps convencionales (created_at, updated_at)
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'denominacion',
        'codigo',
        'encargado',
        'nivel',
        'idUnidad',
        'idDireccion',
        'idSecretaria',
        'estado',
        'fechaRegistro',
        'fechaActualizar',
    ];

    // Opcional: si quieres que Laravel trate ciertas fechas como Carbon instances
    protected $dates = [
        'fechaRegistro',
        'fechaActualizar',
    ];

public function area()
{
    return $this->belongsTo(Area::class, 'idArea');
}

public function unidad()
{
    return $this->belongsTo(Unidad::class, 'idUnidad');
}

public function direccion()
{
    return $this->belongsTo(Direccion::class, 'idDireccion');
}

public function secretaria()
{
    return $this->belongsTo(Secretaria::class, 'idSecretaria');
}


}
