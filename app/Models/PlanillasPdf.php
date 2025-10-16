<?php
// app/Models/Planilla.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PlanillasPdf extends Model
{
    use HasFactory;
    use Auditable;
    protected $table = 'planillas_pdf';

    protected $fillable = [
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'periodo_pago',
        'fecha_elaboracion',
        'total_empleados',
        'anio'
    ];

    protected $casts = [
        'fecha_elaboracion' => 'date'
    ];
}
