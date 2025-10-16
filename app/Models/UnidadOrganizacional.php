<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadOrganizacional extends Model
{
    use HasFactory;

    protected $table = 'unidad_organizacionals';

    protected $fillable = [
        'denominacion',
        'codigo',
        'sigla',
        'tipo',
        'idPadre',
        'esActivo',
        'estado'
    ];

    protected $casts = [
        'esActivo' => 'boolean',
        'estado' => 'boolean'
    ];

    public function historial()
    {
        return $this->hasManyThrough(
            Historial::class,
            Puesto::class,
            'idUnidadOrganizacional',
            'puesto_id',
            'id',
            'id'
        );
    }

    // Relación con sí misma (jerarquía)
    public function padre()
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'idPadre');
    }

    public function hijos()
    {
        return $this->hasMany(UnidadOrganizacional::class, 'idPadre')->where('esActivo', true);
    }

    public function todosLosHijos()
    {
        return $this->hasMany(UnidadOrganizacional::class, 'idPadre');
    }

    // Relación con puestos
    public function puestos()
    {
        return $this->hasMany(Puesto::class, 'idUnidadOrganizacional')
                    ->where('esActivo', true);
    }

    public function todosLosPuestos()
    {
        return $this->hasMany(Puesto::class, 'idUnidadOrganizacional');
    }

    // Obtener jefe de la unidad
    public function jefe()
    {
        return $this->hasOne(Puesto::class, 'idUnidadOrganizacional')
                    ->where('esJefatura', true)
                    ->where('esActivo', true);
    }

    // Obtener todos los jefes históricos
    public function jefesHistoricos()
    {
        return $this->hasMany(Puesto::class, 'idUnidadOrganizacional')
                    ->where('esJefatura', true);
    }

    // Scope para unidades activas
    public function scopeActivas($query)
    {
        return $query->where('esActivo', true);
    }

    public function scopeInactivas($query)
    {
        return $query->where('esActivo', false);
    }

    // Scope por tipo
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope para buscar por denominación
    public function scopeBuscar($query, $termino)
    {
        return $query->where('denominacion', 'LIKE', "%{$termino}%")
                     ->orWhere('codigo', 'LIKE', "%{$termino}%")
                     ->orWhere('sigla', 'LIKE', "%{$termino}%");
    }

    // Método para obtener jerarquía completa
    public function obtenerJerarquia()
    {
        $jerarquia = collect([$this]);
        $actual = $this;

        while ($actual->padre) {
            $jerarquia->prepend($actual->padre);
            $actual = $actual->padre;
        }

        return $jerarquia;
    }

    // Método para obtener árbol completo de subunidades
    public function obtenerArbolCompleto()
    {
        $arbol = $this->load(['hijos.jefe', 'jefe']);

        if ($arbol->hijos->isNotEmpty()) {
            foreach ($arbol->hijos as $hijo) {
                $hijo->load(['hijos.jefe', 'jefe']);
            }
        }

        return $arbol;
    }

    // Método para obtener todas las subunidades recursivamente
    public function obtenerTodasLasSubunidades()
    {
        $subunidades = collect();

        $this->cargarSubunidadesRecursivamente($subunidades);

        return $subunidades;
    }

    private function cargarSubunidadesRecursivamente(&$coleccion)
    {
        $coleccion->push($this);

        foreach ($this->hijos as $hijo) {
            $hijo->cargarSubunidadesRecursivamente($coleccion);
        }
    }

// Método para desactivar unidad y sus puestos
public function desactivar()
{
    \DB::transaction(function () {
        $this->update(['esActivo' => false]); // CORREGIDO: agregar =>
        $this->todosLosPuestos()->update(['esActivo' => false]); // CORREGIDO: agregar =>

        // Desactivar también las subunidades
        foreach ($this->todosLosHijos as $hijo) {
            $hijo->desactivar();
        }
    });
}

    // Método para reactivar unidad
// Método para reactivar unidad
public function reactivar()
{
    \DB::transaction(function () {
        $this->update(['esActivo' => true]); // CORREGIDO: agregar =>
        $this->todosLosPuestos()->update(['esActivo' => true]); // CORREGIDO: agregar =>
    });
}
    // Método para contar personal total (incluyendo subunidades)
    public function contarPersonalTotal()
    {
        $subunidades = $this->obtenerTodasLasSubunidades();
        $unidadesIds = $subunidades->pluck('id');

        return Puesto::whereIn('idUnidadOrganizacional', $unidadesIds)
                    ->where('esActivo', true)
                    ->count();
    }

    // Método para obtener presupuesto total
    public function obtenerPresupuestoTotal()
    {
        $subunidades = $this->obtenerTodasLasSubunidades();
        $unidadesIds = $subunidades->pluck('id');

        return Puesto::whereIn('idUnidadOrganizacional', $unidadesIds)
                    ->where('esActivo', true)
                    ->sum('haber');
    }
}
