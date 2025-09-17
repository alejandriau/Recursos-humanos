<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cas extends Model
{
    protected $table = 'cas';
    protected $primaryKey = 'id';
    public $timestamps = false; // porque usas campos personalizados para timestamps

    protected $fillable = [
        'anios',
        'meses',
        'dias',
        'fechaEmision',
        'fechaTiempo',
        'pdfcas',
        'estado',
        'idPersona',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }
}
