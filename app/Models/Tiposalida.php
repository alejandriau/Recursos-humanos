<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoSalida extends Model
{
    protected $table = 'tiposalidas';

    protected $fillable = [
        'descripcion',
        'sustLegal',
        'expresa',
        'id_padre',
        'unidad',
        'tiene_cupo',
        'periodicidad',
        'cantidad_default',
        'permite_arrastre',
        'max_veces_periodo',
        'usa_tabla_antiguedad',
        'requiere_aprobacion_jefe',
        'requiere_aprobacion_rrhh',
        'activo',
    ];

    protected $casts = [
        'tiene_cupo'                => 'boolean',
        'permite_arrastre'          => 'boolean',
        'usa_tabla_antiguedad'      => 'boolean',
        'requiere_aprobacion_jefe'  => 'boolean',
        'requiere_aprobacion_rrhh'  => 'boolean',
        'activo'                    => 'boolean',
        'cantidad_default'          => 'decimal:2',
    ];

    // Etiquetas legibles para las vistas
    public static array $unidades = [
        'dias'   => 'Días',
        'horas'  => 'Horas',
        'mixto'  => 'Días y Horas',
    ];

    public static array $periodicidades = [
        'ninguna'  => 'Sin límite (libre)',
        'mensual'  => 'Mensual',
        'anual'    => 'Anual',
        'evento'   => 'Por evento',
    ];

    // Accessor: label de unidad para la vista
    public function getUnidadLabelAttribute(): string
    {
        return self::$unidades[$this->unidad] ?? $this->unidad;
    }

    // Accessor: label de periodicidad para la vista
    public function getPeriodicidadLabelAttribute(): string
    {
        return self::$periodicidades[$this->periodicidad] ?? $this->periodicidad;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConCupo($query)
    {
        return $query->where('tiene_cupo', true);
    }

    // Relaciones (se usarán en fases siguientes)
    public function beneficiosPeriodo(): HasMany
    {
        return $this->hasMany(BeneficioPeriodo::class, 'tiposalida_id');
    }

    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class, 'tiposalida_id');
    }
    public function hijos()
    {
        return $this->hasMany(TipoSalida::class, 'id_padre');
    }

}
