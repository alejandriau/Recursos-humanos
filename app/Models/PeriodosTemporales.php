<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodosTemporales extends Model
{
    protected $table = 'periodos_temporales';

    protected $fillable = [
        'situacion_id',
        'fecha_probable_parto',
        'fecha_nacimiento',
        'fecha_inicio_lactancia',
        'fecha_fin_lactancia',
        'dias_prenatales',
        'dias_postnatales',
        'dias_lactancia',
        'dias_paternidad',
        'certificado_embarazo_path',
        'certificado_nacimiento_path',
        'certificado_lactancia_path',
        'dias_usados',
        'dias_restantes'
    ];

    protected $casts = [
        'fecha_probable_parto' => 'date',
        'fecha_nacimiento' => 'date',
        'fecha_inicio_lactancia' => 'date',
        'fecha_fin_lactancia' => 'date',
        'dias_prenatales' => 'integer',
        'dias_postnatales' => 'integer',
        'dias_lactancia' => 'integer',
        'dias_paternidad' => 'integer',
        'dias_usados' => 'integer',
        'dias_restantes' => 'integer',
    ];

    // Relaciones
    public function situacion(): BelongsTo
    {
        return $this->belongsTo(SituacionesEspeciales::class, 'situacion_id');
    }

    // Métodos útiles
    public function getTotalDiasPermitidos(): int
    {
        return $this->dias_prenatales + $this->dias_postnatales +
               $this->dias_lactancia + $this->dias_paternidad;
    }

    public function getDiasDisponibles(): int
    {
        return $this->dias_restantes ?? ($this->getTotalDiasPermitidos() - $this->dias_usados);
    }

    public function usarDias(int $dias): bool
    {
        $disponibles = $this->getDiasDisponibles();

        if ($dias <= $disponibles) {
            $this->dias_usados += $dias;
            $this->dias_restantes = $disponibles - $dias;
            $this->save();
            return true;
        }

        return false;
    }

    public function estaEnPeriodoLactancia(): bool
    {
        if (!$this->fecha_inicio_lactancia || !$this->fecha_fin_lactancia) {
            return false;
        }

        return now()->between($this->fecha_inicio_lactancia, $this->fecha_fin_lactancia);
    }
}
