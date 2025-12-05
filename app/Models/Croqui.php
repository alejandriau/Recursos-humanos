<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Croqui extends Model
{
    use HasFactory;

    protected $table = 'croquis';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'direccion',
        'descripcion',
        'longetud',
        'latitud',
        'estado',
        'idPersona'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime',
        'latitud' => 'float',
        'longetud' => 'float'
    ];

    protected $dates = [
        'fechaRegistro',
        'fechaActualizacion',
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

    // Scope por dirección
    public function scopePorDireccion($query, $direccion)
    {
        return $query->where('direccion', 'like', '%' . $direccion . '%');
    }

    // Método para obtener coordenadas como array
    public function getCoordenadasAttribute()
    {
        return [
            'lat' => (float) $this->latitud,
            'lng' => (float) $this->longetud
        ];
    }

    // Método para obtener enlace de Google Maps
    public function getGoogleMapsLinkAttribute()
    {
        return "https://www.google.com/maps?q={$this->latitud},{$this->longetud}";
    }

    // Método para obtener iframe de Google Maps
    public function getGoogleMapsIframeAttribute()
    {
        return "https://maps.google.com/maps?q={$this->latitud},{$this->longetud}&z=15&output=embed";
    }
}
