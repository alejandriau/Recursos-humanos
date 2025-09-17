<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto;
use App\Models\Area;
use App\Models\Unidad;
use App\Models\Direccion;
use App\Models\Secretaria;


class GerarquiaController extends Controller
{
    public function secretarias()
    {
        $secretarias = Secretaria::all();
        return view('admin.puestos.partes.opcionesse', compact('secretarias'));
    }

    public function direcciones(Request $request)
    {
        $idSecretaria = $request->input('idSecretaria');
        $direcciones = Direccion::where('idSecretaria', $idSecretaria)->get();
        $options = $direcciones;
        return view('admin.puestos.partes.opciones', compact('options'));
    }

    public function unidades(Request $request)
    {
        $idDireccion = $request->input('idDireccion');
        $unidades = Unidad::where('idDireccion', $request->idDireccion)->get();
        $options = $unidades;
        return view('admin.puestos.partes.opciones', compact('options'));
    }

    public function unidadesSecretaria(Request $request)
    {
        $unidades = Unidad::where('idSecretaria', $request->idSecretaria)->get();
        $options = $unidades;
        return view('admin.puestos.partes.opciones', compact('options'));
    }

    public function areas(Request $request)
    {
        $areas = Area::where('idUnidad', $request->idUnidad)->get();
        $options = $areas;
        return view('admin.puestos.partes.opciones', compact('options'));
    }

    public function areasDireccion(Request $request)
    {
        $areas = Area::where('idDireccion', $request->idDireccion)->get();
        $options = $areas;
        return view('admin.puestos.partes.opciones', compact('options'));
    }

    public function areasSecretaria(Request $request)
    {
        $areas = Area::where('idSecretaria', $request->idSecretaria)->get();
        $options = $areas;
        return view('admin.puestos.partes.opciones', compact('options'));
    }

    public function puestos(Request $request)
    {
        $query = Puesto::query();

        if($request->has('idSecretaria')) {
            $query->where('secretaria_id', $request->idSecretaria);
        }
        if($request->has('idDireccion')) {
            $query->where('direccion_id', $request->idDireccion);
        }
        if($request->has('idUnidad')) {
            $query->where('unidad_id', $request->idUnidad);
        }
        if($request->has('idArea')) {
            $query->where('area_id', $request->idArea);
        }

        $puestos = $query->get();
        return view('selects.options', ['options' => $puestos]);
    }
}
