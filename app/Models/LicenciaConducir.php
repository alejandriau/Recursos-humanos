<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LicenciaConducir extends Model
{
    use HasFactory;

    protected $table = 'licenciaconducir';
    protected $primaryKey = 'id';

    protected $fillable = [
        'fechavencimiento',
        'categoria',
        'descripcion',
        'pdflicc',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'fechavencimiento' => 'date',
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime'
    ];

    protected $dates = [
        'fechavencimiento',
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

    // Scope por categoría
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Scope por vencimiento
    public function scopePorVencimiento($query, $tipo)
    {
        $today = Carbon::now();
        if ($tipo === 'vencidas') {
            return $query->where('fechavencimiento', '<', $today);
        } elseif ($tipo === 'vigentes') {
            return $query->where('fechavencimiento', '>=', $today);
        }
        return $query;
    }

    // Método para verificar si está vencida
    public function getEstaVencidaAttribute()
    {
        return Carbon::now()->gt($this->fechavencimiento);
    }

    // Método para días restantes de vigencia
    public function getDiasRestantesAttribute()
    {
        return Carbon::now()->diffInDays($this->fechavencimiento, false);
    }

    // Método para actualizar estado según vencimiento
    public function actualizarEstadoPorVencimiento()
    {
        $nuevoEstado = !$this->esta_vencida;

        if ($this->estado != $nuevoEstado) {
            $this->estado = $nuevoEstado;
            $this->save();
        }

        return $this;
    }

    // Método para obtener el nombre completo de la categoría
    public function getCategoriaCompletaAttribute()
    {
        $categorias = [
            'A' => 'A - Motocicletas',
            'B' => 'B - Vehículos particulares',
            'C' => 'C - Vehículos de carga',
            'D' => 'D - Transporte público',
            'E' => 'E - Maquinaria pesada'
        ];

        return $categorias[$this->categoria] ?? $this->categoria;
    }
}
