<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividad';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'descripcion', 'lugar', 'fecha',
        'hora_inicio', 'hora_fin', 'hora_limite_puntual',
        'estado', 'token_qr', 'permite_manual', 'user_id',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'estado'   => 'integer',
        'permite_manual' => 'integer',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
    ];

    /* ---------- Relaciones ---------- */

    public function asistencias()
    {
        return $this->hasMany(ActividadAsistencia::class, 'actividad_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /* ---------- Helpers ---------- */

    public function estaActiva(): bool
    {
        return (int) $this->estado === 1;
    }

    /**
     * Calcula el estado de una asistencia según la hora de registro.
     * Retorna el código numérico: 1=presente, 2=tardanza.
     */
    public function calcularEstadoPorHora($horaRegistro): int
    {
        if (!$this->hora_limite_puntual) {
            return 1; // sin límite definido => presente
        }

        $limite = \Carbon\Carbon::parse($this->fecha->format('Y-m-d') . ' ' . $this->hora_limite_puntual);

        return $horaRegistro->greaterThan($limite) ? 2 : 1;
    }

    /* ---------- Scopes ---------- */

    public function scopeActivas($query)
    {
        return $query->where('estado', 1);
    }
}
