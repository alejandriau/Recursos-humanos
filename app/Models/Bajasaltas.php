<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bajasaltas extends Model
{
        // Nombre de la tabla
    protected $table = 'bajasaltas';

    // Nombre de la clave primaria
    protected $primaryKey = 'id';

    // Indicamos que la clave primaria no es autoincremental si no lo fuera (opcional aquí)
    public $incrementing = true;

    // Desactivar timestamps si no estás usando `created_at` y `updated_at`
    public $timestamps = false;

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'fecha',
        'motivo',
        'observacion',
        'estado',
        'pdfbaja',
        'idPersona',
        'fechaRegistro',
        'fechaActualizacion',
    ];


    // Relación con el modelo Persona (si existe)
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }
}
