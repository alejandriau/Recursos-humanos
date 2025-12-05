<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Area;
use App\Models\Direccion;
use App\Models\Secretaria;
use App\Models\Unidad;
use App\Models\Historial;

class Puestoss extends Model
{
    protected $table = 'puesto';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'denominacion',
        'nivelgerarquico',
        'item',
        'maual',
        'perfil',
        'haber',
        'nivel',
        'puestocol',
        'tipo',
        'idArea',
        'idUnidad',
        'idDireccion',
        'idSecretaria',
        'idContrato',
        'estado',
        'fechaRegistro',
        'fechaActualizacion',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'idArea');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'idUnidad');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'idDireccion');
    }

    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class, 'idSecretaria');
    }

    public function historial()
    {
        return $this->hasMany(Historial::class, 'puesto_id');
    }
    public function unidadOrganizacional()
    {
        return $this->belongsTo(UnidadOrganizacional::class);
    }
    // En app/Models/Puesto.php
public function obtenerJefeInmediato()
{
    // Verificar si existe la relación con la unidad organizacional
    if (!$this->unidadOrganizacional) {
        return null;
    }

    // Buscar el jefe en la unidad organizacional actual
    $jefe = $this->unidadOrganizacional->obtenerJefe();

    // Si no hay jefe en esta unidad, buscar en la unidad padre
    if (!$jefe && $this->unidadOrganizacional->padre) {
        $jefe = $this->unidadOrganizacional->padre->obtenerJefe();
    }

    return $jefe;
}



}
