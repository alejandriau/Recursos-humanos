<?php
// app/Models/DispositivoBiometrico.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispositivoBiometrico extends Model
{
    protected $table = 'dispositivos_biometricos';

    protected $fillable = [
        'nombre', 'ip', 'puerto', 'ubicacion', 'timeout',
        'activo', 'ultima_sincronizacion', 'ultimo_estado',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ultima_sincronizacion' => 'datetime',
    ];

    public function marcaciones()
    {
        return $this->hasMany(MarcacionBiometrica::class, 'dispositivo_id');
    }
}
