<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historial extends Model
{
    use SoftDeletes;

    protected $table = 'historials';

    protected $fillable = [
        'persona_id',
        'puesto_id',
        'fecha_inicio',
        'fecha_fin',
        'tipo_movimiento',
        'tipo_contrato',
        'estado',
        'numero_memo',
        'fecha_memo',
        'archivo_memo',
        'historial_anterior_id',
        'puesto_anterior_id',
        'conserva_puesto_original',
        'puesto_original_id',
        'motivo',
        'observaciones',
        'salario',
        'jornada_laboral',
        'fecha_vencimiento',
        'renovacion_automatica',
        'porcentaje_dedicacion'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_memo' => 'date',
        'fecha_vencimiento' => 'date',
        'conserva_puesto_original' => 'boolean',
        'renovacion_automatica' => 'boolean',
        'salario' => 'decimal:2'
    ];

    // Relaciones
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_id');
    }

    public function puestoAnterior(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_anterior_id');
    }

    public function puestoOriginal(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_original_id');
    }

    public function historialAnterior(): BelongsTo
    {
        return $this->belongsTo(Historial::class, 'historial_anterior_id');
    }

    // Scopes para consultas comunes
    public function scopeDesignacionesIniciales($query)
    {
        return $query->where('tipo_movimiento', 'designacion_inicial');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorPersona($query, $personaId)
    {
        return $query->where('persona_id', $personaId);
    }

    // Métodos para gestionar movimientos
    public function marcarComoConcluido()
    {
        $this->update([
            'fecha_fin' => now(),
            'estado' => 'concluido'
        ]);
    }

    public function esDesignacionInicial(): bool
    {
        return $this->tipo_movimiento === 'designacion_inicial';
    }

    public function esActivo(): bool
    {
        return $this->estado === 'activo';
    }
}
