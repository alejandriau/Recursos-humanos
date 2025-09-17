<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Bachiller extends Model
{
    use HasFactory;

    protected $table = 'bachiller';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'observacion',
        'otros',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime'
    ];

    protected $dates = [
        'fecha',
        'fechaRegistro',
        'fechaActualizacion'
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }

    // Scope para activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Scope por observación
    public function scopePorObservacion($query, $observacion)
    {
        return $query->where('observacion', 'like', '%' . $observacion . '%');
    }

    // Scope por otros
    public function scopePorOtros($query, $otros)
    {
        return $query->where('otros', 'like', '%' . $otros . '%');
    }

    // Scope por fecha
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    // Método para obtener información completa
    public function getInformacionCompletaAttribute()
    {
        $info = [];

        if ($this->fecha) {
            $info[] = "Fecha: {$this->fecha->format('d/m/Y')}";
        }

        if ($this->observacion) {
            $info[] = "Observación: " . Str::limit($this->observacion, 50);
        }

        if ($this->otros) {
            $info[] = "Otros: " . Str::limit($this->otros, 50);
        }

        return implode(' | ', $info);
    }

    // Método para verificar si es reciente (menos de 1 año)
    public function getEsRecienteAttribute()
    {
        if (!$this->fecha) {
            return false;
        }

        return $this->fecha->gt(Carbon::now()->subYear());
    }

    // Método para obtener años desde la fecha
    public function getAniosDesdeFechaAttribute()
    {
        if (!$this->fecha) {
            return null;
        }

        return $this->fecha->diffInYears(Carbon::now());
    }
}
