<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficioPeriodo extends Model
{
    use HasFactory;

    protected $table = 'beneficio_periodo';

    protected $fillable = [
        'persona_id',
        'tiposalida_id',
        'gestion_id',
        'mes',
        'unidad',
        'cantidad_asignada',
        'cantidad_usada',
        'cantidad_vencida',
        'arrastre',
        'saldo_disponible',
        'fecha_habilitacion',
        'estado'
    ];

    protected $casts = [
        'cantidad_asignada' => 'decimal:2',
        'cantidad_usada' => 'decimal:2',
        'cantidad_vencida' => 'decimal:2',
        'arrastre' => 'decimal:2',
        'saldo_disponible' => 'decimal:2',
        'fecha_habilitacion' => 'date',
        'mes' => 'integer',
    ];

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function tiposalida()
    {
        return $this->belongsTo(TipoSalida::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    // Scope para filtrar por persona y gestión
    public function scopeDePersonaGestion($query, $personaId, $gestionId)
    {
        return $query->where('persona_id', $personaId)
                     ->where('gestion_id', $gestionId);
    }

    // Scope para beneficios activos (con saldo disponible)
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo')
                     ->where('saldo_disponible', '>', 0);
    }

    // Método para calcular saldo disponible (actualiza el campo)
    public function recalcularSaldo()
    {
        $this->saldo_disponible = $this->cantidad_asignada
                                   - $this->cantidad_usada
                                   - $this->cantidad_vencida
                                   + $this->arrastre;

        // Actualizar estado según saldo
        if ($this->saldo_disponible <= 0) {
            $this->estado = 'agotado';
        } elseif ($this->fecha_habilitacion && now()->gt($this->fecha_habilitacion->addDays(30))) {
            // Ejemplo: si pasan 30 días desde habilitación y no se usó, se vence
            // Ajusta según tu lógica de negocio
            $this->estado = 'vencido';
        } else {
            $this->estado = 'activo';
        }

        $this->save();
        return $this->saldo_disponible;
    }

    // Método para usar una cantidad del beneficio (restar saldo)
    public function usarCantidad($cantidad)
    {
        if ($cantidad <= 0) {
            throw new \Exception('La cantidad a usar debe ser mayor a cero.');
        }

        if ($cantidad > $this->saldo_disponible) {
            throw new \Exception('Saldo insuficiente. Disponible: ' . $this->saldo_disponible);
        }

        $this->cantidad_usada += $cantidad;
        $this->saldo_disponible -= $cantidad;
        $this->save();

        return $this;
    }

    // Método para renovar/recargar el beneficio (ej: nuevo período)
    public function renovar($nuevaCantidad, $nuevaFecha = null)
    {
        // Si el tipo permite arrastre, se acumula
        if ($this->tiposalida->permite_arrastre) {
            $this->arrastre += $this->saldo_disponible;
        } else {
            // Si no permite arrastre, se pierde el saldo no usado
            $this->cantidad_vencida += $this->saldo_disponible;
        }

        // Asignar nueva cantidad
        $this->cantidad_asignada = $nuevaCantidad;
        $this->cantidad_usada = 0;
        $this->saldo_disponible = $nuevaCantidad + $this->arrastre;
        $this->fecha_habilitacion = $nuevaFecha ?? now();
        $this->estado = 'activo';
        $this->save();

        return $this;
    }

    // Método para obtener el tipo de unidad en formato legible
    public function getUnidadLabelAttribute()
    {
        return ucfirst($this->unidad);
    }

    // Método para obtener el período en texto (mes/año)
    public function getPeriodoLabelAttribute()
    {
        if ($this->mes) {
            return \Carbon\Carbon::create()->month($this->mes)->monthName . ' ' . $this->gestion->anio;
        }
        return 'Anual ' . $this->gestion->anio;
    }

    // Boot: al crear, establecer saldo disponible igual a cantidad asignada
    protected static function booted()
    {
        static::creating(function ($beneficio) {
            if (!isset($beneficio->saldo_disponible)) {
                $beneficio->saldo_disponible = $beneficio->cantidad_asignada ?? 0;
            }
            if (!isset($beneficio->estado) && $beneficio->saldo_disponible > 0) {
                $beneficio->estado = 'activo';
            }
        });
    }

}
