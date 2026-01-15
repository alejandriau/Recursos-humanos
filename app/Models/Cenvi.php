<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cenvi extends Model
{
    use HasFactory;

    protected $table = 'cenvi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'fecha',
        'observacion',
        'pdfcenvi',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'FechaActualizacion' => 'datetime'
    ];

    /**
     * Relación con Persona
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }

    /**
     * Verificar si el certificado está vigente
     * (tiene menos de 1 año desde la fecha de emisión)
     */
    public function getEstaVigenteAttribute()
    {
        $fechaVencimiento = $this->fecha->copy()->addYear();
        return Carbon::now()->lt($fechaVencimiento); // lt = less than (menor que)
    }

    /**
     * Calcular días restantes de vigencia
     */
    public function getDiasRestantesAttribute()
    {
        if (!$this->esta_vigente) {
            return 0;
        }

        $fechaVencimiento = $this->fecha->copy()->addYear();
        return Carbon::now()->diffInDays($fechaVencimiento, false); // false = incluye signo negativo
    }

    /**
     * Actualizar estado automáticamente según vigencia
     */
    public function actualizarEstadoPorVigencia()
    {
        $nuevoEstado = $this->esta_vigente ? 1 : 0;

        if ($this->estado != $nuevoEstado) {
            $this->update(['estado' => $nuevoEstado]);
        }

        return $nuevoEstado;
    }

    /**
     * Scope para certificados vigentes
     */
    public function scopeVigentes($query)
    {
        return $query->where('fecha', '>=', Carbon::now()->subYear());
    }

    /**
     * Scope para certificados vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('fecha', '<', Carbon::now()->subYear());
    }

    /**
     * Obtener la fecha de vencimiento
     */
    public function getFechaVencimientoAttribute()
    {
        return $this->fecha->copy()->addYear();
    }

    /**
     * Verificar si está por vencer (menos de 30 días)
     */
    public function getPorVencerAttribute()
    {
        return $this->esta_vigente && $this->dias_restantes <= 30;
    }

    /**
     * Scope para certificados por vencer
     */
    public function scopePorVencer($query)
    {
        $fechaLimite = Carbon::now()->addDays(30);
        $fechaMinima = Carbon::now()->subYear();

        return $query->where('fecha', '>=', $fechaMinima)
                     ->where('fecha', '<=', $fechaLimite->subYear());
    }
        // Verificar si el CENVI está vencido (1 año de validez)
    public function isVencido()
    {
        if (!$this->fecha) return false;

        $fechaVencimiento = Carbon::parse($this->fecha)->addYear();
        return Carbon::now()->greaterThan($fechaVencimiento);
    }

    public function diasParaVencer()
    {
        if (!$this->fecha) return null;

        $fechaVencimiento = Carbon::parse($this->fecha)->addYear();
        return Carbon::now()->diffInDays($fechaVencimiento, false);
    }
}
