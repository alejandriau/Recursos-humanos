<?php

namespace App\Http\Controllers;

use App\Models\LicenciaMilitar;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LicenciaMilitarController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver licencias militares')->only(['index', 'show']);
    //    $this->middleware('permission:crear licencias militares')->only(['create', 'store']);
    //    $this->middleware('permission:editar licencias militares')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar licencias militares')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf licencias militares')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = LicenciaMilitar::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por código
        if ($request->has('codigo') && $request->codigo != '') {
            $query->where('codigo', 'like', '%' . $request->codigo . '%');
        }

        // Filtro por serie
        if ($request->has('serie') && $request->serie != '') {
            $query->where('serie', 'like', '%' . $request->serie . '%');
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
        $orderBy = $request->get('order_by', 'fechaRegistro');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $licencias = $query->paginate(50);

        return view('admin.licencias-militares.index', compact('licencias'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.licencias-militares.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'nullable|string|max:45',
            'fecha' => 'nullable|date',
            'serie' => 'nullable|string|max:45',
            'descripcion' => 'nullable|string|max:500',
            'pdflic' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdflic')) {
            $file = $request->file('pdflic');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('licencias_militares_pdfs', $fileName, 'public');
            $data['pdflic'] = $path;
        }

        LicenciaMilitar::create($data);

        return redirect()->route('licencias-militares.index')
            ->with('success', 'Licencia Militar creada exitosamente.');
    }

    public function show(LicenciaMilitar $licencia)
    {
        return view('admin.licencias-militares.show', compact('licencia'));
    }

    public function edit(LicenciaMilitar $licencia)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.licencias-militares.edit', compact('licencia', 'personas'));
    }

    public function update(Request $request, LicenciaMilitar $licencia)
    {
        $request->validate([
            'codigo' => 'nullable|string|max:45',
            'fecha' => 'nullable|date',
            'serie' => 'nullable|string|max:45',
            'descripcion' => 'nullable|string|max:500',
            'pdflic' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdflic')) {
            if ($licencia->pdflic && Storage::disk('public')->exists($licencia->pdflic)) {
                Storage::disk('public')->delete($licencia->pdflic);
            }

            $file = $request->file('pdflic');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('licencias_militares_pdfs', $fileName, 'public');
            $data['pdflic'] = $path;
        }

        $licencia->update($data);

        return redirect()->route('licencias-militares.index')
            ->with('success', 'Licencia Militar actualizada exitosamente.');
    }

    public function destroy(LicenciaMilitar $licencia)
    {
        if ($licencia->pdflic && Storage::disk('public')->exists($licencia->pdflic)) {
            Storage::disk('public')->delete($licencia->pdflic);
        }

        $licencia->delete();

        return redirect()->route('licencias-militares.index')
            ->with('success', 'Licencia Militar eliminada exitosamente.');
    }

    public function downloadPdf(LicenciaMilitar $licencia)
    {
        if (!$licencia->pdflic || !Storage::disk('public')->exists($licencia->pdflic)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($licencia->pdflic);
    }
}
