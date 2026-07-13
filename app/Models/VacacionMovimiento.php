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
}
