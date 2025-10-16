<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Persona;

class Profesion extends Model
{
        // Nombre de la tabla (opcional si sigue la convención)
        protected $table = 'profesion';

        // Clave primaria personalizada
        protected $primaryKey = 'id';

        // No usar timestamps automáticos de Laravel (created_at, updated_at)
        public $timestamps = false;

        // Campos que pueden ser asignados masivamente
        protected $fillable = [
            'diploma',
            'fechaDiploma',
            'provisionN',
            'fechaProvision',
            'universidad',
            'registro',
            'pdfDiploma',
            'pdfProvision',
            'cedulaProfesion',
            'pdfcedulap',
            'observacion',
            'idPersona',
            'estado',
            'fechaRegistro',
            'fechaActualizacion',
        ];
        protected $casts = [
            'fechaDiploma' => 'date',
            'fechaProvision' => 'date',
            'fechaRegistro' => 'datetime',
            'fechaActualizacion' => 'datetime',
        ];

        // Opcional: Si quieres definir la relación con el modelo Persona
        public function persona()
        {
            return $this->belongsTo(Persona::class, 'idPersona');
        }
        // app/Models/Persona.php
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellidoPat . ' ' . $this->apellidoMat;
    }
}
