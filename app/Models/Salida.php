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
        'codigo',
        'tiposalida_id',
        'periodo_id',
        'periodo_type',
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
        'estado',
        'observacion'
    ];

    protected $casts = [
        'fechasal' => 'date',
        'fecharet' => 'date',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }


    public function tiposalida()
    {
        return $this->belongsTo(Tiposalida::class, 'tiposalida_id');
    }

    public function jefe()
    {
        return $this->belongsTo(Persona::class, 'jefe_id');
    }

    public function rrhh()
    {
        return $this->belongsTo(Persona::class, 'rrhh_id');
    }

    // ============================================
    // RELACIÓN POLIMÓRFICA CON PERIODO
    // ============================================
    public function periodo()
    {
        return $this->morphTo('periodo', 'periodo_type', 'periodo_id');
    }

    // También puedes tener relaciones específicas para cada tipo
    public function periodoVacacion()
    {
        return $this->belongsTo(VacacionPeriodo::class, 'periodo_id')
            ->where('periodo_type', 'App\\Models\\VacacionPeriodo');
    }

    public function periodoBeneficio()
    {
        return $this->belongsTo(BeneficioPeriodo::class, 'periodo_id')
            ->where('periodo_type', 'App\\Models\\BeneficioPeriodo');
    }

    // Helper para obtener el período específico
    public function getPeriodoModelAttribute()
    {
        if (!$this->periodo_type || !$this->periodo_id) {
            return null;
        }

        $class = $this->periodo_type;
        return $class::find($this->periodo_id);
    }
    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }
    public function movimientosVacacion()
    {
        return $this->hasMany(VacacionMovimiento::class, 'salida_id');
    }
        public function salidas()
    {
        return $this->morphMany(Salida::class, 'salida_id');
    }

    public static function generarCodigo()
    {
        $anio = date('Y');
        // Prefijo: año * 100000 (ej. 2026 * 100000 = 202600000)
        $prefijo = $anio * 100000;

        // Bloquear para evitar duplicados en concurrencia (dentro de transacción)
        $ultimo = self::where('codigo', '>=', $prefijo)
                    ->where('codigo', '<', $prefijo + 100000)
                    ->lockForUpdate()
                    ->max('codigo');

        // Si no hay registros en este año, empezar desde prefijo
        return $ultimo ? $ultimo + 1 : $prefijo + 1;
    }

}
