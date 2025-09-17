<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LicenciaMilitar extends Model
{
    use HasFactory;

    protected $table = 'licenciamilitar';
    protected $primaryKey = 'id';

    protected $fillable = [
        'codigo',
        'fecha',
        'serie',
        'descripcion',
        'pdflic',
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

    // Scope por código
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', 'like', '%' . $codigo . '%');
    }

    // Scope por serie
    public function scopePorSerie($query, $serie)
    {
        return $query->where('serie', 'like', '%' . $serie . '%');
    }

    // Scope por fecha
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    // Método para verificar si es reciente (menos de 1 año)
    public function getEsRecienteAttribute()
    {
        if (!$this->fecha) {
            return false;
        }

        return $this->fecha->gt(Carbon::now()->subYear());
    }

    // Método para obtener información completa
    public function getInformacionCompletaAttribute()
    {
        $info = [];

        if ($this->codigo) {
            $info[] = "Código: {$this->codigo}";
        }

        if ($this->serie) {
            $info[] = "Serie: {$this->serie}";
        }

        if ($this->fecha) {
            $info[] = "Fecha: {$this->fecha->format('d/m/Y')}";
        }

        return implode(' | ', $info);
    }
}
