<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cenvi extends Model
{
    use HasFactory;

    protected $table = 'cenvi';

    protected $fillable = [
        'fecha',
        'observacion',
        'pdf_cenvi',
        'persona_id',
        'estado',
    ];

    protected $casts = [
        'fecha'  => 'date',
        'estado' => 'boolean',
    ];

    /* =======================
     |  RELACIONES
     ======================= */
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    /* =======================
     |  ATRIBUTOS CALCULADOS
     ======================= */

    public function getFechaVencimientoAttribute(): Carbon
    {
        return $this->fecha->copy()->addYear();
    }

    public function getEstaVigenteAttribute(): bool
    {
        return now()->lt($this->fecha_vencimiento);
    }

    public function getDiasRestantesAttribute(): int
    {
        return now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function getPorVencerAttribute(): bool
    {
        return $this->esta_vigente && $this->dias_restantes <= 30;
    }

    /* =======================
     |  SCOPES
     ======================= */

    public function scopeVigentes($query)
    {
        return $query->where('fecha', '>=', now()->subYear());
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha', '<', now()->subYear());
    }

    public function scopePorVencer($query)
    {
        return $query->whereBetween(
            'fecha',
            [now()->subYear(), now()->subYear()->addDays(30)]
        );
    }

    /* =======================
     |  MÉTODOS DE NEGOCIO
     ======================= */

    public function actualizarEstadoPorVigencia(): bool
    {
        $nuevoEstado = $this->esta_vigente;

        if ($this->estado !== $nuevoEstado) {
            $this->update(['estado' => $nuevoEstado]);
        }

        return $nuevoEstado;
    }
}
