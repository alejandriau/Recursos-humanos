<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cajacordes extends Model
{
    protected $table = 'cajacordes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'codigo',
        'otros',
        'pdfcaja',
        'idPersona',
        'estado',
        'fechaRegistro',
        'fechaActualizacion',
    ];
    protected $casts = [
        'fecha' => 'date',
        'fechaRegistro' => 'date',
        'fechaActualizacion' => 'date',
    ];

    // Relación con persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }
}
