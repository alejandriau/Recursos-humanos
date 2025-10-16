<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanillaRegistro extends Model
{
    protected $table = 'planillas_registros';
    protected $fillable = [
        'empleado_id', 'mes', 'ano', 'dias_trabajados',
        'haber_basico', 'bono_antiguedad', 'otros_ingresos',
        'total_ingresos', 'rc_iva', 'afp', 'otros_descuentos',
        'total_descuentos', 'liquido_pagable', 'item',
        'cuenta_bancaria', 'archivo_origen'
    ];

    protected $casts = [
        'haber_basico' => 'decimal:2',
        'bono_antiguedad' => 'decimal:2',
        'otros_ingresos' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'rc_iva' => 'decimal:2',
        'afp' => 'decimal:2',
        'otros_descuentos' => 'decimal:2',
        'total_descuentos' => 'decimal:2',
        'liquido_pagable' => 'decimal:2'
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(PlanillaEmpleado::class, 'empleado_id');
    }

    public function getPeriodoAttribute()
    {
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        return $meses[$this->mes] . ' ' . $this->ano;
    }
}
