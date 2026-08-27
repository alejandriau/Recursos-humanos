<?php

// app/Models/VacacionMovimiento.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacacionMovimiento extends Model
{
    protected $fillable = [
        'periodo_id', 'tipo', 'fecha', 'fecha_inicio', 'fecha_fin',
        'cantidad', 'saldo_anterior', 'saldo_posterior',
        'salida_id', 'descripcion', 'registrado_por'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'cantidad' => 'decimal:1',
        'saldo_anterior' => 'decimal:1',
        'saldo_posterior' => 'decimal:1',
    ];


    const TIPO_CREDITO = 'credito';
    const TIPO_DEBITO = 'debito';
    const TIPO_VENCIMIENTO = 'vencimiento';
    const TIPO_ARRASTRE = 'arrastre';

    public function periodo()
    {
        return $this->belongsTo(VacacionPeriodo::class, 'periodo_id');
    }

    public function salida()
    {
        return $this->belongsTo(Salida::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
    public function getTipoLabelAttribute(): string
    {
        return [
            self::TIPO_CREDITO => 'Crédito',
            self::TIPO_DEBITO => 'Débito (Uso)',
            self::TIPO_VENCIMIENTO => 'Vencimiento',
            self::TIPO_ARRASTRE => 'Arrastre',
        ][$this->tipo] ?? $this->tipo;
    }

    public function getColorAttribute(): string
    {
        return [
            self::TIPO_CREDITO => 'success',
            self::TIPO_DEBITO => 'danger',
            self::TIPO_VENCIMIENTO => 'warning',
            self::TIPO_ARRASTRE => 'info',
        ][$this->tipo] ?? 'secondary';
    }
    public function movimientosVacacion()
    {
        return $this->hasMany(VacacionMovimiento::class, 'salida_id');
    }
}
