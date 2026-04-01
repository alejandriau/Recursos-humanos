<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrera extends Model
{
    protected $table = 'carreras';

    protected $fillable = [
        'nombre',
        'idAreaConocimiento',
        'idNivelAcademico',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    /**
     * Relación con el área de conocimiento
     */
    public function areaConocimiento(): BelongsTo
    {
        return $this->belongsTo(AreaConocimiento::class, 'idAreaConocimiento');
    }

    /**
     * Relación con el nivel académico
     */
    public function nivelAcademico(): BelongsTo
    {
        return $this->belongsTo(NivelAcademico::class, 'idNivelAcademico');
    }

    /**
     * Relación con las profesiones que tienen esta carrera
     */
    public function profesiones(): HasMany
    {
        return $this->hasMany(Profesion::class, 'idCarrera');
    }

    /**
     * Scope para carreras activas
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Verificar si la carrera es afín a un área específica
     */
    public function esAfínA(string $areaNombre): bool
    {
        return $this->areaConocimiento && 
               stripos($this->areaConocimiento->nombre, $areaNombre) !== false;
    }

    /**
     * Obtener el nivel jerárquico de la carrera
     */
    public function getNivelJerarquicoAttribute(): string
    {
        return $this->nivelAcademico ? $this->nivelAcademico->nombre : 'No definido';
    }
}