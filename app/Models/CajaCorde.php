<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CajaCorde extends Model
{
    use HasFactory;

    protected $table = 'cajacordes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'codigo',
        'otros',
        'pdfcaja',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime'
    ];

    protected $dates = [
        'fecha',
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

    // Scope por código
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', 'like', '%' . $codigo . '%');
    }

    // Scope por otros
    public function scopePorOtros($query, $otros)
    {
        return $query->where('otros', 'like', '%' . $otros . '%');
    }
}
