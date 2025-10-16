<?php

namespace App\Http\Controllers;

use App\Models\Formulario2;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Formulario2Controller extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver formularios2')->only(['index', 'show']);
    //    $this->middleware('permission:crear formularios2')->only(['create', 'store']);
    //    $this->middleware('permission:editar formularios2')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar formularios2')->only(['destroy']);
    //}

    public function index(Request $request)
    {
        $query = Formulario2::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por observación
        if ($request->has('observacion') && $request->observacion != '') {
            $query->where('observacion', 'like', '%' . $request->observacion . '%');
        }

        // Filtro por PDF
        if ($request->has('pdfform2') && $request->pdfform2 != '') {
            $query->where('pdfform2', 'like', '%' . $request->pdfform2 . '%');
        }

        // Filtro por fecha desde
        if ($request->has('fecha_desde') && $request->fecha_desde != '') {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->has('fecha_hasta') && $request->fecha_hasta != '') {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fecha');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $formularios = $query->paginate(50);

        return view('admin.formularios2.index', compact('formularios'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.formularios2.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'observacion' => 'nullable|string|max:300',
            'pdfform2' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id'
        ]);

        $data = $request->except('pdfform2');

        // Manejar la carga del archivo PDF
        if ($request->hasFile('pdfform2')) {
            $file = $request->file('pdfform2');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('formularios2', $filename, 'public');
            $data['pdfform2'] = $filename;
        }

        Formulario2::create($data);

        return redirect()->route('formularios2.index')
            ->with('success', 'Formulario 2 creado exitosamente.');
    }

    public function show(Formulario2 $formulario2)
    {
        return view('admin.formularios2.show', compact('formulario2'));
    }

    public function edit(Formulario2 $formulario2)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.formularios2.edit', compact('formulario2', 'personas'));
    }

    public function update(Request $request, Formulario2 $formulario2)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'observacion' => 'nullable|string|max:300',
            'pdfform2' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->except('pdfform2');

        // Manejar la carga del archivo PDF
        if ($request->hasFile('pdfform2')) {
            // Eliminar el archivo anterior si existe
            if ($formulario2->pdfform2) {
                Storage::disk('public')->delete('formularios2/' . $formulario2->pdfform2);
            }

            $file = $request->file('pdfform2');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('formularios2', $filename, 'public');
            $data['pdfform2'] = $filename;
        }

        $formulario2->update($data);

        return redirect()->route('formularios2.index')
            ->with('success', 'Formulario 2 actualizado exitosamente.');
    }

    public function destroy(Formulario2 $formulario2)
    {
        // Eliminar el archivo PDF si existe
        if ($formulario2->pdfform2) {
            Storage::disk('public')->delete('formularios2/' . $formulario2->pdfform2);
        }

        $formulario2->delete();

        return redirect()->route('formularios2.index')
            ->with('success', 'Formulario 2 eliminado exitosamente.');
    }

    public function download(Formulario2 $formulario2)
    {
        if (!$formulario2->pdfform2) {
            return redirect()->back()->with('error', 'No hay archivo PDF para descargar.');
        }

        $path = storage_path('app/public/formularios2/' . $formulario2->pdfform2);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return response()->download($path, $formulario2->pdfform2);
    }
}
