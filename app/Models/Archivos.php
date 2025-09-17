<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivos extends Model
{
    protected $table = 'archivos';
    protected $primaryKey = 'id';
    public $timestamps = false; // Desactiva si usas campos personalizados como fechaRegistro

    protected $fillable = [
        'idPersona',
        'tipoDocumento',
        'rutaArchivo',
        'nombreOriginal',
        'observaciones',
        'estado',
        'fechaRegistro',
        'FechaActualizacion',
    ];

    /**
     * Relación: Archivo pertenece a una persona.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'idPersona', 'id');
    }
}
