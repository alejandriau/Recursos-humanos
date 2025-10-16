<?php

namespace App\Http\Controllers;

use App\Models\Forconsangui;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ForconsanguiController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver consanguinidades')->only(['index', 'show']);
    //    $this->middleware('permission:crear consanguinidades')->only(['create', 'store']);
    //    $this->middleware('permission:editar consanguinidades')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar consanguinidades')->only(['destroy']);
    //}

    public function index(Request $request)
    {
        $query = Forconsangui::with('persona');

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
        if ($request->has('pdfconsag') && $request->pdfconsag != '') {
            $query->where('pdfconsag', 'like', '%' . $request->pdfconsag . '%');
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

        $consanguinidades = $query->paginate(50);

        return view('admin.consanguinidades.index', compact('consanguinidades'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.consanguinidades.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'observacion' => 'nullable|string|max:500',
            'pdfconsag' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->except('pdfconsag');

        // Manejar la carga del archivo PDF
        if ($request->hasFile('pdfconsag')) {
            $file = $request->file('pdfconsag');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('consanguinidad', $filename, 'public');
            $data['pdfconsag'] = $filename;
        }

        Forconsangui::create($data);

        return redirect()->route('consanguinidades.index')
            ->with('success', 'Declaración de consanguinidad creada exitosamente.');
    }

    public function show(Forconsangui $consanguinidad)
    {
        return view('admin.consanguinidades.show', compact('consanguinidad'));
    }

    public function edit(Forconsangui $consanguinidad)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.consanguinidades.edit', compact('consanguinidad', 'personas'));
    }

    public function update(Request $request, Forconsangui $consanguinidad)
    {
        $request->validate([
            'fecha' => 'nullable|date',
            'observacion' => 'nullable|string|max:500',
            'pdfconsag' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->except('pdfconsag');

        // Manejar la carga del archivo PDF
        if ($request->hasFile('pdfconsag')) {
            // Eliminar el archivo anterior si existe
            if ($consanguinidad->pdfconsag) {
                Storage::disk('public')->delete('consanguinidad/' . $consanguinidad->pdfconsag);
            }

            $file = $request->file('pdfconsag');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('consanguinidad', $filename, 'public');
            $data['pdfconsag'] = $filename;
        }

        $consanguinidad->update($data);

        return redirect()->route('consanguinidades.index')
            ->with('success', 'Declaración de consanguinidad actualizada exitosamente.');
    }

    public function destroy(Forconsangui $consanguinidad)
    {
        // Eliminar el archivo PDF si existe
        if ($consanguinidad->pdfconsag) {
            Storage::disk('public')->delete('consanguinidad/' . $consanguinidad->pdfconsag);
        }

        $consanguinidad->delete();

        return redirect()->route('consanguinidades.index')
            ->with('success', 'Declaración de consanguinidad eliminada exitosamente.');
    }

    public function download(Forconsangui $consanguinidad)
    {
        if (!$consanguinidad->pdfconsag) {
            return redirect()->back()->with('error', 'No hay archivo PDF para descargar.');
        }

        $path = storage_path('app/public/consanguinidad/' . $consanguinidad->pdfconsag);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return response()->download($path, $consanguinidad->pdfconsag);
    }
}
