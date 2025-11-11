<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalaBonoAntiguedad extends Model
{
    use HasFactory;

    protected $table = 'escala_bono_antiguedad';

    protected $fillable = [
        'anio_inicio',
        'anio_fin',
        'porcentaje_bono',
        'rango_texto',
        'base_legal',
        'estado'
    ];

    protected $casts = [
        'porcentaje_bono' => 'decimal:2',
        'estado' => 'boolean'
    ];

    // Relación con CAS
    public function cas()
    {
        return $this->hasMany(Cas::class, 'id_escala_bono');
    }

    // Scope para escalas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    // Método para encontrar escala por años de servicio
    public static function encontrarPorAniosServicio($aniosServicio)
    {
        return static::activas()
            ->where('anio_inicio', '<=', $aniosServicio)
            ->where(function($query) use ($aniosServicio) {
                $query->where('anio_fin', '>=', $aniosServicio)
                      ->orWhereNull('anio_fin');
            })
            ->first();
    }
}
