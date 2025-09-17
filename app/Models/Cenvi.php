<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cenvi extends Model
{
    use HasFactory;

    protected $table = 'cenvi';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'observacion',
        'pdfcenvi',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'FechaActualizacion' => 'datetime'
    ];

    protected $dates = [
        'fecha',
        'fechaRegistro',
        'FechaActualizacion'
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }

    // Scope para documentos vigentes
    public function scopeVigentes($query)
    {
        return $query->where('fecha', '>=', Carbon::now()->subYear());
    }

    // Scope para documentos vencidos
    public function scopeVencidos($query)
    {
        return $query->where('fecha', '<', Carbon::now()->subYear());
    }

    // Método para verificar si está vigente
    public function getEstaVigenteAttribute()
    {
        return $this->fecha->gte(Carbon::now()->subYear());
    }

    // Método para días restantes de vigencia
    public function getDiasRestantesAttribute()
    {
        $fechaVencimiento = $this->fecha->addYear();
        return Carbon::now()->diffInDays($fechaVencimiento, false);
    }

    // Método para actualizar estado según vigencia
    public function actualizarEstadoPorVigencia()
    {
        $nuevoEstado = $this->esta_vigente ? 1 : 0;

        if ($this->estado != $nuevoEstado) {
            $this->estado = $nuevoEstado;
            $this->save();
        }

        return $this;
    }
}
