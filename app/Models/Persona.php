<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        'fechaRegistro' => 'date',
        'fechaActualizacion' => 'date',
        'fechaIngreso' => 'date',
    ];

    public function profesiones()
    {
        return $this->hasMany(Profesion::class, 'idPersona');
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
    public function cenvis()
    {
        return $this->hasMany(Cenvi::class, 'idPersona');
    }


    // Acceder al puesto actual
    public function puestoActual()
    {
        return $this->hasOne(Historial::class, 'persona_id')->whereNull('fecha_fin')->with('puesto');
    }
    public function bajasaltas()
    {
        return $this->hasMany(Bajasaltas::class, 'idPersona');
    }

    public function djbRenta()
    {
        return $this->hasMany(DjbRenta::class, 'idPersona');
    }
    public function bachilleres()
    {
        return $this->hasMany(Bachiller::class, 'idPersona');
    }
    public function formularios1()
    {
        return $this->hasMany(Formulario1::class, 'idPersona');
    }
    public function formularios2()
    {
        return $this->hasMany(Formulario2::class, 'idPersona');
    }
    public function consanguinidades()
    {
        return $this->hasMany(forconsangui::class, 'idPersona');
    }
    public function croquis()
    {
        return $this->hasMany(Croqui::class, 'idPersona');
    }
    public function cedulasIdentidad()
    {
        return $this->hasMany(CedulaIdentidad::class, 'idPersona');
    }
    public function certificadosNacimiento()
    {
        return $this->hasMany(CertNacimiento::class, 'idPersona');
    }
    public function licenciasConducir()
    {
        return $this->hasMany(LicenciaConducir::class, 'idPersona');
    }
    public function licenciasMilitar()
    {
        return $this->hasMany(LicenciaMilitar::class, 'idPersona');
    }
    public function curriculums()
    {
        return $this->hasMany(Curriculum::class, 'idPersona');
    }
    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'idPersona');
    }

    public function historialPuestos()
    {
        return $this->hasMany(Historial::class, 'persona_id');
    }

    /**
     * Relación con el puesto actual (historial activo más reciente)
     */


    /**
     * Relación con todos los puestos activos
     */
    public function puestosActivos()
    {
        return $this->hasMany(Historial::class, 'persona_id')
            ->where('estado', 'activo');
    }

    /**
     * Obtener el historial ordenado por fecha más reciente
     */
    public function historialReciente()
    {
        return $this->hasMany(Historial::class, 'persona_id')
            ->orderBy('fecha_inicio', 'desc')
            ->orderBy('created_at', 'desc');
    }

}
