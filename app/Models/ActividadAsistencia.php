<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadAsistencia extends Model
{
    protected $table = 'actividad_asistencia';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'actividad_id', 'persona_id', 'hora_registro',
        'estado', 'metodo_registro', 'qr_raw',
        'registrado_por', 'observaciones',
    ];

    protected $casts = [
        'hora_registro' => 'datetime',
        'estado' => 'integer',
        'metodo_registro' => 'integer',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
    ];

    /* ---------- Constantes de estado ---------- */
    const ESTADO_ANULADO     = 0;
    const ESTADO_PRESENTE    = 1;
    const ESTADO_TARDANZA    = 2;
    const ESTADO_JUSTIFICADO = 3;
    const ESTADO_AUSENTE     = 4;

    /* ---------- Constantes de método ---------- */
    const METODO_OPERADOR   = 1;
    const METODO_AUTOSERVICIO = 2;
    const METODO_MANUAL     = 3;

    /* ---------- Relaciones ---------- */

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /* ---------- Helpers de etiquetas ---------- */

    public function getEstadoTextoAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_PRESENTE    => 'Presente',
            self::ESTADO_TARDANZA    => 'Tardanza',
            self::ESTADO_JUSTIFICADO => 'Justificado',
            self::ESTADO_AUSENTE     => 'Ausente',
            self::ESTADO_ANULADO     => 'Anulado',
            default                  => 'Desconocido',
        };
    }

    public function getMetodoTextoAttribute(): string
    {
        return match ($this->metodo_registro) {
            self::METODO_OPERADOR     => 'Operador',
            self::METODO_AUTOSERVICIO => 'Autoservicio',
            self::METODO_MANUAL       => 'Manual',
            default                   => 'Desconocido',
        };
    }
}
