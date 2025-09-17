<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Afps extends Model
{

    protected $table = 'afps';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'cua',
        'observacion',
        'pdfafps',
        'idPersona',
        'estado',
        'fechaRegistro',
        'FechaActualizacion'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'FechaActualizacion' => 'datetime',
    ];

    // Relación con Persona (ajusta si tienes el modelo)
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }
}
