<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Afp extends Model
{
    use HasFactory;

    protected $table = 'afps';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cua',
        'observacion',
        'pdfafps',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'FechaActualizacion' => 'datetime'
    ];

    protected $dates = [
        'fechaRegistro',
        'FechaActualizacion'
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

    // Scope por CUA
    public function scopePorCua($query, $cua)
    {
        return $query->where('cua', 'like', '%' . $cua . '%');
    }
}
