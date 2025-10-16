<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Formulario1 extends Model
{
    use HasFactory;

    protected $table = 'formulario1';
    protected $primaryKey = 'id';

    protected $fillable = [
        'fecha',
        'observacion',
        'pdfform1',
        'estado',
        'idPersona'
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

    // Scope por PDF
    public function scopePorPdf($query, $pdf)
    {
        return $query->where('pdfform1', 'like', '%' . $pdf . '%');
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
            $info[] = "Observación: " . Str::limit($this->observacion, 30);
        }

        if ($this->pdfform1) {
            $info[] = "PDF: " . Str::limit($this->pdfform1, 30);
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

    // Método para obtener la ruta del PDF
    public function getRutaPdfAttribute()
    {
        if (!$this->pdfform1) {
            return null;
        }

        return asset('storage/formularios/' . $this->pdfform1);
    }
}
