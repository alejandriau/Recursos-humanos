<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discapacidades extends Model
{
    protected $table = 'discapacidades';

    protected $fillable = [
        'situacion_id',
        'tipo',
        'grado',
        'porcentaje',
        'codigo_certificado',
        'entidad_certificadora',
        'fecha_certificacion',
        'fecha_vencimiento',
        'tutor_id',
        'parentesco_tutor',
        'certificado_medico_path',
        'resolucion_conadis_path'
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'fecha_certificacion' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    // Grados
    const GRADO_LEVE = 'leve';
    const GRADO_MODERADO = 'moderado';
    const GRADO_SEVERA = 'severa';

    public static $grados = [
        self::GRADO_LEVE,
        self::GRADO_MODERADO,
        self::GRADO_SEVERA,
    ];

    // Relaciones
    public function situacion(): BelongsTo
    {
        return $this->belongsTo(SituacionesEspeciales::class, 'situacion_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'tutor_id');
    }

    // Scopes
    public function scopeVigente($query)
    {
        return $query->where(function($q) {
            $q->whereNull('fecha_vencimiento')
              ->orWhere('fecha_vencimiento', '>=', now());
        });
    }

    // Métodos útiles
    public function certificadoVigente(): bool
    {
        if (!$this->fecha_vencimiento) {
            return true;
        }

        return $this->fecha_vencimiento >= now();
    }

    public function getGradoLabelAttribute(): string
    {
        $labels = [
            self::GRADO_LEVE => 'Leve',
            self::GRADO_MODERADO => 'Moderado',
            self::GRADO_SEVERA => 'Severa',
        ];

        return $labels[$this->grado] ?? $this->grado;
    }
}
