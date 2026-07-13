<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salida extends Model
{
    use HasFactory;

    protected $fillable = [
        'persona_id',
        'tiposalida_id',
        'beneficio_periodo_id',
        'fechasal',
        'horasal',
        'fecharet',
        'horaret',
        'cantidad',
        'motivo',
        'fechasol',
        'img',
        'estado_jefe',
        'jefe_id',
        'fecha_aprobacion_jefe',
        'observacion_jefe',
        'estado_rrhh',
        'rrhh_id',
        'fecha_aprobacion_rrhh',
        'observacion_rrhh',
        'estado'
    ];

    // Relaciones
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function tipoSalida(): BelongsTo
    {
        return $this->belongsTo(Tiposalida::class, 'tiposalida_id');
    }

    public function beneficioPeriodo(): BelongsTo
    {
        return $this->belongsTo(BeneficioPeriodo::class, 'beneficio_periodo_id');
    }

    public function jefe(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'jefe_id');
    }

    public function rrhh(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'rrhh_id');
    }
    
}