<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Djbrenta extends Model
{
    use HasFactory;

    protected $table = 'djbrenta';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'pdfrenta',
        'tipo',
        'estado',
        'idPersona'
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

    // Scope para documentos vigentes (si aplica)
    public function scopeVigentes($query)
    {
        return $query->where('fecha', '>=', Carbon::now()->subYear());
    }

    // Scope para documentos vencidos (si aplica)
    public function scopeVencidos($query)
    {
        return $query->where('fecha', '<', Carbon::now()->subYear());
    }

    // Método para verificar si está vigente (si aplica)
    public function getEstaVigenteAttribute()
    {
        return $this->fecha->gte(Carbon::now()->subYear());
    }

    // Método para días restantes de vigencia (si aplica)
    public function getDiasRestantesAttribute()
    {
        $fechaVencimiento = $this->fecha->addYear();
        return Carbon::now()->diffInDays($fechaVencimiento, false);
    }
}
