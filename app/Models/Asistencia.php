<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Asistencia extends Model
{
    protected $table = 'asistencias';
    protected $fillable = [
        'idPersona', 'fecha', 'hora_entrada', 'hora_salida',
        'minutos_retraso', 'horas_extras', 'tipo_registro',
        'observaciones', 'estado', 'latitud', 'longitud'
    ];

    protected $attributes = [
        'estado' => 'presente',
        'tipo_registro' => 'manual'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_entrada' => 'datetime:H:i:s',
        'hora_salida' => 'datetime:H:i:s',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    const ESTADOS = [
        'presente' => 'Presente',
        'ausente' => 'Ausente',
        'tardanza' => 'Tardanza',
        'permiso' => 'Permiso',
        'vacaciones' => 'Vacaciones'
    ];

    const TIPOS_REGISTRO = [
        'manual' => 'Manual',
        'biometrico' => 'Biométrico',
        'web' => 'Web/Móvil'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }

    // Scope para filtros
    public function scopeRangoFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha', [$inicio, $fin]);
    }

    public function scopePorPersona($query, $personaId)
    {
        return $query->where('idPersona', $personaId);
    }

    public function scopeDelMes($query, $mes, $ano)
    {
        return $query->whereYear('fecha', $ano)->whereMonth('fecha', $mes);
    }

    // Calcular horas trabajadas
    public function getHorasTrabajadasAttribute()
    {
        if ($this->hora_entrada && $this->hora_salida) {
            $entrada = Carbon::parse($this->hora_entrada);
            $salida = Carbon::parse($this->hora_salida);
            return $entrada->diffInHours($salida);
        }
        return 0;
    }

    // Verificar si es día completo
    public function getDiaCompletoAttribute()
    {
        return $this->horas_trabajadas >= 8;
    }
}
