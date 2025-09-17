<?php

namespace App\Http\Controllers;
use App\Models\Puesto;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Direccion;
use App\Models\Secretaria;
use App\Models\Unidad;

class PuestoController extends Controller
{
    /**
     * Mostrar todos los puestos.
     */
    public function index(Request $request)
    {
        $query = Puesto::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('denominacion', 'like', "%$buscar%")
                ->orWhere('nivelgerarquico', 'like', "%$buscar%")
                ->orWhere('item', 'like', "%$buscar%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $puestos = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.puestos.index', compact('puestos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo puesto.
     */
    public function create()
    {
        return view('admin.puestos.create');
    }

    /**
     * Guardar un nuevo puesto en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nivelgerarquico' => 'required|string|max:500',
            'haber' => 'nullable|numeric',
            'nivel' => 'nullable|integer',
            'idArea' => 'nullable|integer',
            'idUnidad' => 'nullable|integer',
            'idDireccion' => 'nullable|integer',
            'idSecretaria' => 'nullable|integer',
            'idContrato' => 'nullable|integer',
            'estado' => 'nullable|integer|in:0,1',
        ]);

        Puesto::create($request->all());

        return redirect()->route('puesto')->with('success', 'Puesto creado exitosamente.');
    }

    /**
     * Mostrar el formulario de edición para un puesto específico.
     */
    public function edit($id)
    {

        $puesto = Puesto::with([
            'area.unidad',
            'area.direccion',
            'area.secretaria',
            'unidad.direccion',
            'unidad.secretaria',
            'direccion.secretaria',
            'secretaria'
        ])->findOrFail($id);


        return view('admin.puestos.edit', compact('puesto'));
    }

    /**
     * Actualizar la información de un puesto.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nivelgerarquico' => 'required|string|max:500',
            'haber' => 'nullable|numeric',
            'nivel' => 'nullable|integer',
            'idArea' => 'nullable|integer',
            'idUnidad' => 'nullable|integer',
            'idDireccion' => 'nullable|integer',
            'idSecretaria' => 'nullable|integer',
            'idContrato' => 'nullable|integer',
            'estado' => 'nullable|integer|in:0,1',
        ]);

        $puesto = Puesto::findOrFail($id);
        $puesto->update($request->all());

        return redirect()->route('puestos')->with('success', 'Puesto actualizado correctamente.');
    }

    /**
     * Eliminar un puesto específico.
     */
    public function destroy($id)
    {
        $puesto = Puesto::findOrFail($id);
        $puesto->delete();

        return redirect()->route('puestos')->with('success', 'Puesto eliminado correctamente.');
    }
}
