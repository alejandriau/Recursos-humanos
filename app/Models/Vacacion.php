<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{


    // Definir constantes para los estados
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_RECHAZADO = 'rechazado';

    protected $table = 'vacaciones';
    protected $fillable = [
        'idPersona', 'fecha_inicio', 'fecha_fin',
        'dias_tomados', 'estado', 'motivo_rechazo'
    ];

    protected $attributes = [
        'estado' => 'pendiente'
    ];

    const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function getDiasDisponiblesAttribute()
    {
        $diasAcumulados = 15; // Días base por año
        $diasTomados = self::where('idPersona', $this->idPersona)
            ->where('estado', 'aprobado')
            ->whereYear('created_at', now()->year)
            ->sum('dias_tomados');

        return max(0, $diasAcumulados - $diasTomados);
    }
    public static function getEstados()
    {
        return [
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_APROBADO => 'Aprobado',
            self::ESTADO_RECHAZADO => 'Rechazado'
        ];
    }
}
