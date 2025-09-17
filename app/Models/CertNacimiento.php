<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CertNacimiento extends Model
{
    use HasFactory;

    protected $table = 'certnacimiento';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'descripcion',
        'pdfcern',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualización' => 'datetime'
    ];

    protected $dates = [
        'fecha',
        'fechaRegistro',
        'fechaActualización'
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

    // Scope por descripción
    public function scopePorDescripcion($query, $descripcion)
    {
        return $query->where('descripcion', 'like', '%' . $descripcion . '%');
    }

    // Scope por fecha
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    // Método para obtener la edad en el momento del certificado
    public function getEdadEnCertificadoAttribute()
    {
        if (!$this->fecha || !$this->persona || !$this->persona->fechanacimiento) {
            return null;
        }

        return Carbon::parse($this->persona->fechanacimiento)->diffInYears($this->fecha);
    }

    // Método para verificar si es reciente (menos de 1 año)
    public function getEsRecienteAttribute()
    {
        if (!$this->fecha) {
            return false;
        }

        return $this->fecha->gt(Carbon::now()->subYear());
    }
}
