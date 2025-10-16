<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Puesto extends Model
{
    protected $table = 'puestos';

    protected $fillable = [
        'denominacion',
        'nivelJerarquico',
        'nivel',
        'item',
        'manual',
        'perfil',
        'experencia',
        'haber',
        'tipoContrato',
        'idUnidadOrganizacional',
        'esJefatura',
        'esActivo',
        'estado'
    ];

    protected $casts = [
        'esJefatura' => 'boolean',
        'esActivo' => 'boolean',
        'estado' => 'boolean',
        'haber' => 'decimal:2'
    ];

    // Relación con UnidadOrganizacional
    public function unidadOrganizacional()
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'idUnidadOrganizacional');
    }

    // Relación con Historial
    public function historial()
    {
        return $this->hasMany(Historial::class, 'puesto_id');
    }
        public function historiales()
    {
        return $this->hasMany(Historial::class, 'puesto_id');
    }
        public function scopeJefaturas($query)
    {
        return $query->where('esJefatura', true);
    }
     public function getUbicacionCompletaAttribute()
    {
        $ubicacion = [];
        $actual = $this->unidadOrganizacional;

        while ($actual) {
            $ubicacion[] = $actual->tipo . ' de ' . $actual->denominacion;
            $actual = $actual->padre;
        }

        return array_reverse($ubicacion);
    }
        public function obtenerJefeInmediato()
    {
        // Buscar jefatura en la misma unidad
        $jefeUnidad = $this->unidadOrganizacional->jefe()->first();
        if ($jefeUnidad && $jefeUnidad->id !== $this->id) {
            return $jefeUnidad->historialActual->persona ?? null;
        }

        // Si no hay jefe en la misma unidad, buscar en unidad padre
        $unidadPadre = $this->unidadOrganizacional->padre;
        while ($unidadPadre) {
            $jefePadre = $unidadPadre->jefe()->first();
            if ($jefePadre) {
                return $jefePadre->historialActual->persona ?? null;
            }
            $unidadPadre = $unidadPadre->padre;
        }

        return null;
    }
    // RELACIÓN PERSONA ACTUAL - AGREGAR ESTA RELACIÓN
    public function personaActual(): HasOne
    {
        return $this->hasOne(Historial::class, 'puesto_id')
                    ->where('estado', 'activo')
                    ->whereNull('fecha_fin')
                    ->with('persona');
    }

    // Relación con el historial activo
    public function historialActivo(): HasOne
    {
        return $this->hasOne(Historial::class, 'puesto_id')
                    ->where('estado', 'activo')
                    ->whereNull('fecha_fin');
    }

    // Scope para puestos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Método para verificar si está ocupado
    public function estaOcupado(): bool
    {
        return $this->historial()->where('estado', 'activo')->whereNull('fecha_fin')->exists();
    }

    // Método para obtener la persona actual (como atributo)
    public function getPersonaActualAttribute()
    {
        $historialActivo = $this->historial()->where('estado', 'activo')->whereNull('fecha_fin')->first();
        return $historialActivo ? $historialActivo->persona : null;
    }
}
