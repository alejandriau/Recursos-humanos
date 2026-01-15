<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CedulaIdentidad extends Model
{
    use HasFactory;

    protected $table = 'cedulaidentidad';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ci',
        'fechanacimiento',
        'fechaVencimiento',
        'expedido',
        'nacido',
        'domicilio',
        'estado',
        'pdfcedula',
        'observacion',
        'idPersona'
    ];

    protected $casts = [
        'fechanacimiento' => 'date',
        'fechaVencimiento' => 'date',
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime'
    ];

    protected $dates = [
        'fechanacimiento',
        'fechaVencimiento',
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

    // Scope por CI
    public function scopePorCi($query, $ci)
    {
        return $query->where('ci', 'like', '%' . $ci . '%');
    }

    // Scope por expedido
    public function scopePorExpedido($query, $expedido)
    {
        return $query->where('expedido', 'like', '%' . $expedido . '%');
    }

    // Método para verificar si está vencida
    public function getEstaVencidaAttribute()
    {
        if (!$this->fechaVencimiento) {
            return false;
        }
        return Carbon::now()->gt($this->fechaVencimiento);
    }

    // Método para días restantes de vigencia
    public function getDiasRestantesAttribute()
    {
        if (!$this->fechaVencimiento) {
            return null;
        }
        return Carbon::now()->diffInDays($this->fechaVencimiento, false);
    }

    // Método para edad de la persona
    public function getEdadAttribute()
    {
        if (!$this->fechanacimiento) {
            return null;
        }
        return Carbon::parse($this->fechanacimiento)->age;
    }

    // Método para actualizar estado según vencimiento
    public function actualizarEstadoPorVencimiento()
    {
        if ($this->fechaVencimiento) {
            $nuevoEstado = !$this->esta_vencida;

            if ($this->estado != $nuevoEstado) {
                $this->estado = $nuevoEstado;
                $this->save();
            }
        }

        return $this;
    }


    // Verificar si la cédula está vencida
    public function isVencida()
    {
        if (!$this->fechaVencimiento) return false;

        return Carbon::now()->greaterThan(Carbon::parse($this->fechaVencimiento));
    }
}
