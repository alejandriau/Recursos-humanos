<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NivelAcademico extends Model
{
    protected $table = 'niveles_academicos';

    protected $fillable = [
        'nombre',
        'orden',
        'esTituloUniversitario',
        'estado'
    ];

    protected $casts = [
        'orden' => 'integer',
        'esTituloUniversitario' => 'boolean',
        'estado' => 'boolean'
    ];

    /**
     * Relación con las carreras que pertenecen a este nivel académico
     */
    public function carreras(): HasMany
    {
        return $this->hasMany(Carrera::class, 'idNivelAcademico');
    }

    /**
     * Scope para obtener solo títulos universitarios
     */
    public function scopeTitulosUniversitarios($query)
    {
        return $query->where('esTituloUniversitario', true);
    }

    /**
     * Scope para niveles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}