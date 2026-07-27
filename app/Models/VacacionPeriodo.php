<?php

// app/Models/VacacionPeriodo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacacionPeriodo extends Model
{
    protected $fillable = [
        'persona_id', 'gestion_id', 'cas_id', 'numero_periodo',
        'fecha_habilitacion', 'anios_antiguedad', 'dias_asignados',
        'dias_usados', 'dias_vencidos', 'saldo_disponible',
        'dias_arrastre', 'periodo_vencido', 'estado', 'observacion'
    ];

    public function personal()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function movimientos()
    {
        return $this->hasMany(VacacionMovimiento::class, 'periodo_id');
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

}
