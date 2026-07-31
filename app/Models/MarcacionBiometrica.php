<?php
// app/Models/MarcacionBiometrica.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarcacionBiometrica extends Model
{
    use HasFactory;
    protected $table = 'marcaciones_biometricas';

    protected $fillable = [
        'ci',
        'nombre_completo',
        'uid_biometrico',
        'fecha_hora',
        'tipo',
        'estado_verificacion',
        'sn',
        'importada',
        'fecha_importacion',
        'hash_unique',
        'persona_id'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'fecha_importacion' => 'datetime',
        'importada' => 'boolean',
    ];



    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(DispositivoBiometrico::class, 'dispositivo_id');
    }
    // Relación con persona (cuando la tengas)


    // Scopes útiles
    public function scopeDeCi($query, $ci)
    {
        return $query->where('ci', $ci);
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_hora', [$inicio, $fin]);
    }

    public function scopeNoImportadas($query)
    {
        return $query->where('importada', false);
    }

    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now()->toDateString();
        return $query->whereDate('fecha_hora', $fecha);
    }

    // Método para generar hash único
    public static function generarHash($marcacion)
    {
        return md5(
            $marcacion['uid'] .
            $marcacion['timestamp'] .
            ($marcacion['sn'] ?? '') .
            ($marcacion['userid'] ?? '')
        );
    }
}
