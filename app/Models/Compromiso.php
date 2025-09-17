<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Compromiso extends Model
{
    use HasFactory;

    protected $table = 'compromiso';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'compromiso1',
        'pdfcomp1',
        'compromiso2',
        'pdfcomp2',
        'compromiso3',
        'pdfcomp3',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime'
    ];

    protected $dates = [
        'fechaRegistro',
        'fechaActualizacion'
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }

    // Scope para activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Método para obtener compromisos no vacíos
    public function getCompromisosAttribute()
    {
        $compromisos = [];

        if (!empty($this->compromiso1)) {
            $compromisos[] = [
                'numero' => 1,
                'descripcion' => $this->compromiso1,
                'archivo' => $this->pdfcomp1
            ];
        }

        if (!empty($this->compromiso2)) {
            $compromisos[] = [
                'numero' => 2,
                'descripcion' => $this->compromiso2,
                'archivo' => $this->pdfcomp2
            ];
        }

        if (!empty($this->compromiso3)) {
            $compromisos[] = [
                'numero' => 3,
                'descripcion' => $this->compromiso3,
                'archivo' => $this->pdfcomp3
            ];
        }

        return $compromisos;
    }

    // Método para contar compromisos activos
    public function getTotalCompromisosAttribute()
    {
        $count = 0;
        if (!empty($this->compromiso1)) $count++;
        if (!empty($this->compromiso2)) $count++;
        if (!empty($this->compromiso3)) $count++;
        return $count;
    }
}
