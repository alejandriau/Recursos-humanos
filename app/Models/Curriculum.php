<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curriculum';
    protected $primaryKey = 'id';

    protected $fillable = [
        'descripcion',
        'mas',
        'otros',
        'pdfcorri',
        'idPersona',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fechaRegistro' => 'datetime',
        'fechaActualizacion' => 'datetime'
    ];

    protected $dates = [
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

    // Scope por descripción
    public function scopePorDescripcion($query, $descripcion)
    {
        return $query->where('descripcion', 'like', '%' . $descripcion . '%');
    }

    // Scope por campo "mas"
    public function scopePorMas($query, $mas)
    {
        return $query->where('mas', 'like', '%' . $mas . '%');
    }

    // Scope por campo "otros"
    public function scopePorOtros($query, $otros)
    {
        return $query->where('otros', 'like', '%' . $otros . '%');
    }

    // Método para obtener información resumida
    public function getInformacionResumidaAttribute()
    {
        $info = [];

        if ($this->descripcion) {
            $info[] = Str::limit($this->descripcion, 50);
        }

        if ($this->mas) {
            $info[] = Str::limit($this->mas, 30);
        }

        if ($this->otros) {
            $info[] = Str::limit($this->otros, 30);
        }

        return implode(' | ', $info);
    }

    // Método para verificar si tiene archivo PDF
    public function getTieneArchivoAttribute()
    {
        return !empty($this->pdfcorri);
    }
}
