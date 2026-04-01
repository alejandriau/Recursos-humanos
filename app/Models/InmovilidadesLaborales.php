<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InmovilidadesLaborales extends Model
{
    protected $table = 'inmovilidades_laborales';

    protected $fillable = [
        'situacion_id',
        'tipo_inmovilidad',
        'fecha_inicio_inmovilidad',
        'fecha_fin_inmovilidad',
        'renovable',
        'norma_legal',
        'articulo',
        'numero_resolucion_rrhh',
        'fecha_resolucion_rrhh',
        'resolucion_path',
        'solicitud_path',
        'aprobado_por',
        'fecha_aprobacion',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio_inmovilidad' => 'date',
        'fecha_fin_inmovilidad' => 'date',
        'fecha_resolucion_rrhh' => 'date',
        'fecha_aprobacion' => 'datetime',
        'renovable' => 'boolean',
    ];

    // Tipos de inmovilidad
    const TIPO_POR_DISCAPACIDAD = 'por_discapacidad';
    const TIPO_POR_TUTOR_DISCAPACITADO = 'por_tutor_discapacitado';
    const TIPO_POR_DEPENDIENTE_DISCAPACITADO = 'por_dependiente_discapacitado';
    const TIPO_POR_EMBARAZO = 'por_embarazo';
    const TIPO_POR_LACTANCIA = 'por_lactancia';
    const TIPO_POR_PATERNIDAD = 'por_paternidad';

    public static $tiposInmovilidad = [
        self::TIPO_POR_DISCAPACIDAD,
        self::TIPO_POR_TUTOR_DISCAPACITADO,
        self::TIPO_POR_DEPENDIENTE_DISCAPACITADO,
        self::TIPO_POR_EMBARAZO,
        self::TIPO_POR_LACTANCIA,
        self::TIPO_POR_PATERNIDAD,
    ];

    // Estados
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_RECHAZADO = 'rechazado';
    const ESTADO_FINALIZADO = 'finalizado';

    public static $estados = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_APROBADO,
        self::ESTADO_RECHAZADO,
        self::ESTADO_FINALIZADO,
    ];

    // Relaciones
    public function situacion(): BelongsTo
    {
        return $this->belongsTo(SituacionesEspeciales::class, 'situacion_id');
    }

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_APROBADO)
                     ->where('fecha_fin_inmovilidad', '>=', now());
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorVencer($query, $dias = 30)
    {
        return $query->where('estado', self::ESTADO_APROBADO)
                     ->where('fecha_fin_inmovilidad', '<=', now()->addDays($dias))
                     ->where('fecha_fin_inmovilidad', '>=', now());
    }

    // Métodos útiles
    public function estaActiva(): bool
    {
        return $this->estado === self::ESTADO_APROBADO &&
               $this->fecha_inicio_inmovilidad <= now() &&
               $this->fecha_fin_inmovilidad >= now();
    }

    public function getDiasRestantes(): int
    {
        if (!$this->estaActiva()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->fecha_fin_inmovilidad, false));
    }

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_APROBADO => 'Aprobado',
            self::ESTADO_RECHAZADO => 'Rechazado',
            self::ESTADO_FINALIZADO => 'Finalizado',
        ];

        return $labels[$this->estado] ?? $this->estado;
    }

    public function getTipoInmovilidadLabelAttribute(): string
    {
        $labels = [
            self::TIPO_POR_DISCAPACIDAD => 'Por Discapacidad',
            self::TIPO_POR_TUTOR_DISCAPACITADO => 'Por Tutor de Discapacitado',
            self::TIPO_POR_DEPENDIENTE_DISCAPACITADO => 'Por Dependiente Discapacitado',
            self::TIPO_POR_EMBARAZO => 'Por Embarazo',
            self::TIPO_POR_LACTANCIA => 'Por Lactancia',
            self::TIPO_POR_PATERNIDAD => 'Por Paternidad',
        ];

        return $labels[$this->tipo_inmovilidad] ?? $this->tipo_inmovilidad;
    }
}
