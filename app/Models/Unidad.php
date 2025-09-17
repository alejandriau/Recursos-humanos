<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Area;
use App\Models\Direccion;
use App\Models\Secretaria;
use App\Models\Unidad;

class Unidad extends Model
{
    protected $table = 'unidad'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria

    public $timestamps = false; // No usa created_at y updated_at convencionales

    protected $fillable = [
        'denominacion',
        'codigo',
        'encargado',
        'nivel',
        'estado',
        'idSecretaria',
        'idDireccion',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    protected $dates = [
        'fechaRegistro',
        'fechaActualizacion',
    ];

    // Relaciones opcionales
    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'idSecretaria');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'idDireccion');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'idUnidad');
    }
}
