<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionesSituaciones extends Model
{
    protected $table = 'revisiones_situaciones';

    protected $fillable = [
        'situacion_id',
        'revisado_por',
        'observaciones',
        'resultado',
        'proxima_revision'
    ];

    protected $casts = [
        'proxima_revision' => 'date',
    ];

    // Resultados
    const RESULTADO_APROBADO = 'aprobado';
    const RESULTADO_OBSERVADO = 'observado';
    const RESULTADO_RECHAZADO = 'rechazado';

    public static $resultados = [
        self::RESULTADO_APROBADO,
        self::RESULTADO_OBSERVADO,
        self::RESULTADO_RECHAZADO,
    ];

    // Relaciones
    public function situacion(): BelongsTo
    {
        return $this->belongsTo(SituacionesEspeciales::class, 'situacion_id');
    }

    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    // Scopes
    public function scopePendientesRevision($query)
    {
        return $query->where('proxima_revision', '<=', now());
    }

    // Métodos útiles
    public function getResultadoLabelAttribute(): string
    {
        $labels = [
            self::RESULTADO_APROBADO => 'Aprobado',
            self::RESULTADO_OBSERVADO => 'Observado',
            self::RESULTADO_RECHAZADO => 'Rechazado',
        ];

        return $labels[$this->resultado] ?? $this->resultado;
    }

    public function esAprobado(): bool
    {
        return $this->resultado === self::RESULTADO_APROBADO;
    }

    public function requiereNuevaRevision(): bool
    {
        return $this->resultado === self::RESULTADO_OBSERVADO;
    }
}
