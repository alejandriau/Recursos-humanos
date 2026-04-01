<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DependientesDiscapacitados extends Model
{
    protected $table = 'dependientes_discapacitados';

    protected $fillable = [
        'situacion_id',
        'nombre_dependiente',
        'parentesco',
        'fecha_nacimiento',
        'tipo_discapacidad',
        'porcentaje_discapacidad',
        'codigo_certificado_dependiente',
        'certificado_discapacidad_path',
        'partida_nacimiento_path',
        'declaracion_jurada_path',
        'grado_dependencia',
        'necesidades_especiales'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'porcentaje_discapacidad' => 'decimal:2',
    ];

    // Grados de dependencia
    const GRADO_TOTAL = 'total';
    const GRADO_PARCIAL = 'parcial';

    public static $gradosDependencia = [
        self::GRADO_TOTAL,
        self::GRADO_PARCIAL,
    ];

    // Relaciones
    public function situacion(): BelongsTo
    {
        return $this->belongsTo(SituacionesEspeciales::class, 'situacion_id');
    }

    // Métodos útiles
    public function getEdadAttribute(): ?int
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }

        return $this->fecha_nacimiento->age;
    }

    public function getGradoDependenciaLabelAttribute(): string
    {
        $labels = [
            self::GRADO_TOTAL => 'Dependencia Total',
            self::GRADO_PARCIAL => 'Dependencia Parcial',
        ];

        return $labels[$this->grado_dependencia] ?? $this->grado_dependencia;
    }

    public function requiereAcompanamiento(): bool
    {
        return $this->grado_dependencia === self::GRADO_TOTAL;
    }
}
