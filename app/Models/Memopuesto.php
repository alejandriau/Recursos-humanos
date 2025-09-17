<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Persona;
use App\Models\HasFactory;

class Memopuesto extends Model
{
    protected $table = 'memopuesto';
    protected $primaryKey = 'idmemopuesto';
    public $timestamps = false;

    protected $fillable = [
        'cargo',
        'dependenciaSecr',
        'nivelGerarquico',
        'item',
        'haber',
        'estado',
        'fecha',
        'memoPdf',
        'id_persona',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    // Relación con la persona (si existe el modelo Persona)

}
