<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = 'certificados';
    protected $primaryKey = 'id';
    public $timestamps = false; // Ya manejas las fechas manualmente

    protected $fillable = [
        'nombre',
        'tipo',
        'fecha',
        'instituto',
        'pdfcerts',
        'idPersona',
        'estado',
        'fechaRegistro',
        'fechaActualizacion'
    ];
    protected $casts = [
        'fecha' => 'date',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
    ];

    // Relación con persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }
}
