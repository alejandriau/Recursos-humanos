<?php

namespace App\Http\Controllers;

use App\Models\Formulario1;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Formulario1Controller extends Controller
{
   // public function __construct()
   // {
   //
   //     $this->middleware('permission:ver formularios1')->only(['index', 'show']);
   //     $this->middleware('permission:crear formularios1')->only(['create', 'store']);
   //     $this->middleware('permission:editar formularios1')->only(['edit', 'update']);
   //     $this->middleware('permission:eliminar formularios1')->only(['destroy']);
   // }

    public function index(Request $request)
    {
        $query = Formulario1::with('persona');

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
        if ($request->has('pdfform1') && $request->pdfform1 != '') {
            $query->where('pdfform1', 'like', '%' . $request->pdfform1 . '%');
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

        return view('admin.formularios1.index', compact('formularios'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.formularios1.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'observacion' => 'nullable|string|max:45',
            'pdfform1' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id'
        ]);

        $data = $request->except('pdfform1');

        // Manejar la carga del archivo PDF
        if ($request->hasFile('pdfform1')) {
            $file = $request->file('pdfform1');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('formularios', $filename, 'public');
            $data['pdfform1'] = $filename;
        }

        Formulario1::create($data);

        return redirect()->route('formularios1.index')
            ->with('success', 'Formulario 1 creado exitosamente.');
    }

    public function show(Formulario1 $formulario1)
    {
        return view('admin.formularios1.show', compact('formulario1'));
    }

    public function edit(Formulario1 $formulario1)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.formularios1.edit', compact('formulario1', 'personas'));
    }

    public function update(Request $request, Formulario1 $formulario1)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'observacion' => 'nullable|string|max:45',
            'pdfform1' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->except('pdfform1');

        // Manejar la carga del archivo PDF
        if ($request->hasFile('pdfform1')) {
            // Eliminar el archivo anterior si existe
            if ($formulario1->pdfform1) {
                Storage::disk('public')->delete('formularios/' . $formulario1->pdfform1);
            }

            $file = $request->file('pdfform1');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('formularios', $filename, 'public');
            $data['pdfform1'] = $filename;
        }

        $formulario1->update($data);

        return redirect()->route('formularios1.index')
            ->with('success', 'Formulario 1 actualizado exitosamente.');
    }

    public function destroy(Formulario1 $formulario1)
    {
        // Eliminar el archivo PDF si existe
        if ($formulario1->pdfform1) {
            Storage::disk('public')->delete('formularios/' . $formulario1->pdfform1);
        }

        $formulario1->delete();

        return redirect()->route('formularios1.index')
            ->with('success', 'Formulario 1 eliminado exitosamente.');
    }

    public function download(Formulario1 $formulario1)
    {
        if (!$formulario1->pdfform1) {
            return redirect()->back()->with('error', 'No hay archivo PDF para descargar.');
        }

        $path = storage_path('app/public/formularios/' . $formulario1->pdfform1);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return response()->download($path, $formulario1->pdfform1);
    }
}
