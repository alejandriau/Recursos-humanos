<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SituacionesEspeciales extends Model
{
    protected $table = 'situaciones_especiales';

    protected $fillable = [
        'persona_id',
        'tipo_situacion',
        'fecha_inicio',
        'fecha_fin',
        'vigente',
        'documento_soporte_path',
        'numero_resolucion',
        'fecha_resolucion',
        'tiene_inmovilidad',
        'observaciones_rrhh'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_resolucion' => 'date',
        'vigente' => 'boolean',
        'tiene_inmovilidad' => 'boolean',
    ];

    // Tipos de situación
    const TIPO_DISCAPACIDAD = 'discapacidad';
    const TIPO_TUTOR_DISCAPACITADO = 'tutor_discapacitado';
    const TIPO_DEPENDIENTE_DISCAPACITADO = 'dependiente_discapacitado';
    const TIPO_EMBARAZO = 'embarazo';
    const TIPO_LACTANCIA = 'lactancia';
    const TIPO_PATERNIDAD = 'paternidad';

    public static $tipos = [
        self::TIPO_DISCAPACIDAD,
        self::TIPO_TUTOR_DISCAPACITADO,
        self::TIPO_DEPENDIENTE_DISCAPACITADO,
        self::TIPO_EMBARAZO,
        self::TIPO_LACTANCIA,
        self::TIPO_PATERNIDAD,
    ];

    // Relaciones
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function discapacidad(): HasMany
    {
        return $this->hasMany(Discapacidades::class, 'situacion_id');
    }

    public function inmovilidadLaboral(): HasMany
    {
        return $this->hasMany(InmovilidadesLaborales::class, 'situacion_id');
    }

    public function periodoTemporal(): HasMany
    {
        return $this->hasMany(PeriodosTemporales::class, 'situacion_id');
    }

    public function dependienteDiscapacitado(): HasMany
    {
        return $this->hasMany(DependientesDiscapacitados::class, 'situacion_id');
    }

    public function revisiones(): HasMany
    {
        return $this->hasMany(RevisionesSituaciones::class, 'situacion_id');
    }

    // Scopes
    public function scopeVigente($query)
    {
        return $query->where('vigente', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_situacion', $tipo);
    }

    public function scopeConInmovilidad($query)
    {
        return $query->where('tiene_inmovilidad', true);
    }

    // Métodos útiles
    public function estaVigente(): bool
    {
        if (!$this->vigente) {
            return false;
        }

        if ($this->fecha_fin && $this->fecha_fin < now()) {
            return false;
        }

        return true;
    }

    public function getTipoSituacionLabelAttribute(): string
    {
        $labels = [
            self::TIPO_DISCAPACIDAD => 'Discapacidad',
            self::TIPO_TUTOR_DISCAPACITADO => 'Tutor de Discapacitado',
            self::TIPO_DEPENDIENTE_DISCAPACITADO => 'Dependiente Discapacitado',
            self::TIPO_EMBARAZO => 'Embarazo',
            self::TIPO_LACTANCIA => 'Lactancia',
            self::TIPO_PATERNIDAD => 'Paternidad',
        ];

        return $labels[$this->tipo_situacion] ?? $this->tipo_situacion;
    }
}
