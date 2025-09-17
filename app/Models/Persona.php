<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\profesion;
use App\Models\memopuesto;
use App\Models\Historial;
use App\Models\Afps;
use App\Models\Cajacordes;

class Persona extends Model
{
    protected $table = 'persona';
    protected $primaryKey = 'id';
    public $timestamps = false; // porque no usas los campos created_at y updated_at típicos

    protected $fillable = [
        'ci',
        'nombre',
        'apellidoPat',
        'apellidoMat',
        'fechaIngreso',
        'fechaNacimiento',
        'sexo',
        'telefono',
        'observaciones',
        'estado',
        'foto',
        'tipo',
        'archivo',
        'fechaRegistro',
        'fechaActualizacion'
    ];



    public function profesiones()
    {
        return $this->hasOne(Profesion::class, 'idPersona');
    }
    public function profesion()
    {
        return $this->hasOne(Profesion::class, 'idPersona');
    }

    public function historial()
    {
        return $this->hasMany(Historial::class, 'persona_id'); // 👈 Usa el nombre real de la columna
    }

    public function afps()
    {
        return $this->hasMany(Afps::class, 'idPersona');
    }
    public function cajacordes()
    {
        return $this->hasMany(Cajacordes::class, 'idPersona');
    }


    // Acceder al puesto actual
    public function puestoActual()
    {
        return $this->hasOne(Historial::class, 'persona_id')->whereNull('fecha_fin')->with('puesto');
    }
    //casst
    // En el modelo Persona
    protected $casts = [
        'fechaNacimiento' => 'date',
        'fechaIngreso' => 'date',
    ];
    //bajas altas
    public function bajasaltas()
    {
        return $this->hasMany(Bajasaltas::class, 'idPersona');
    }


}
