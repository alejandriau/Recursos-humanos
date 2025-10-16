<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanillaEmpleado extends Model
{
    protected $table = 'planillas_empleados';
    protected $fillable = [
        'cedula', 'nombre_completo', 'fecha_nacimiento',
        'nacionalidad', 'puesto', 'departamento',
        'cuenta_bancaria', 'fecha_ingreso'
    ];

    public function registros(): HasMany
    {
        return $this->hasMany(PlanillaRegistro::class, 'empleado_id');
    }

    public function getMesesTrabajadosAttribute()
    {
        return $this->registros()->count();
    }

    public function getTotalGanadoAttribute()
    {
        return $this->registros()->sum('liquido_pagable');
    }

    public function getPromedioMensualAttribute()
    {
        return $this->registros()->avg('liquido_pagable');
    }
}
