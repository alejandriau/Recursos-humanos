<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaConocimiento extends Model
{
    protected $table = 'areas_conocimiento';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    /**
     * Relación con las carreras de esta área
     */
    public function carreras(): HasMany
    {
        return $this->hasMany(Carrera::class, 'idAreaConocimiento');
    }

    /**
     * Scope para áreas activas
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}