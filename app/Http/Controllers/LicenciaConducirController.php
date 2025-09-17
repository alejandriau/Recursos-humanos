<?php

namespace App\Http\Controllers;

use App\Models\LicenciaConducir;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LicenciaConducirController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver licencias conducir')->only(['index', 'show']);
    //    $this->middleware('permission:crear licencias conducir')->only(['create', 'store']);
    //    $this->middleware('permission:editar licencias conducir')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar licencias conducir')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf licencias conducir')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = LicenciaConducir::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por categoría
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria', $request->categoria);
        }

        // Filtro por vencimiento
        if ($request->has('vencimiento') && $request->vencimiento != '') {
            $query->porVencimiento($request->vencimiento);
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fechavencimiento');
        $orderDirection = $request->get('order_direction', 'asc');

        $query->orderBy($orderBy, $orderDirection);

        $licencias = $query->paginate(50);

        // Categorías disponibles para el filtro
        $categorias = [
            'A' => 'A - Motocicletas',
            'B' => 'B - Vehículos particulares',
            'C' => 'C - Vehículos de carga',
            'D' => 'D - Transporte público',
            'E' => 'E - Maquinaria pesada'
        ];

        return view('admin.licencias-conducir.index', compact('licencias', 'categorias'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.licencias-conducir.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fechavencimiento' => 'required|date|after:today',
            'categoria' => 'required|in:A,B,C,D,E',
            'descripcion' => 'nullable|string|max:500',
            'pdflicc' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id'
        ]);

        $data = $request->all();

        // Calcular estado basado en vencimiento
        $data['estado'] = Carbon::parse($request->fechavencimiento)->gte(Carbon::now());

        if ($request->hasFile('pdflicc')) {
            $file = $request->file('pdflicc');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('licencias_conducir_pdfs', $fileName, 'public');
            $data['pdflicc'] = $path;
        }

        LicenciaConducir::create($data);

        return redirect()->route('licencias-conducir.index')
            ->with('success', 'Licencia de Conducir creada exitosamente.');
    }

    public function show(LicenciaConducir $licencia)
    {
        $licencia->actualizarEstadoPorVencimiento();
        return view('admin.licencias-conducir.show', compact('licencia'));
    }

    public function edit(LicenciaConducir $licencia)
    {
        $personas = Persona::where('estado', 1)->get();
        $licencia->actualizarEstadoPorVencimiento();
        return view('admin.licencias-conducir.edit', compact('licencia', 'personas'));
    }

    public function update(Request $request, LicenciaConducir $licencia)
    {
        $request->validate([
            'fechavencimiento' => 'required|date',
            'categoria' => 'required|in:A,B,C,D,E',
            'descripcion' => 'nullable|string|max:500',
            'pdflicc' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        // Si cambia la fecha de vencimiento, recalcular estado automáticamente
        if ($request->has('fechavencimiento') && $request->fechavencimiento != $licencia->fechavencimiento->format('Y-m-d')) {
            $data['estado'] = Carbon::parse($request->fechavencimiento)->gte(Carbon::now());
        }

        if ($request->hasFile('pdflicc')) {
            if ($licencia->pdflicc && Storage::disk('public')->exists($licencia->pdflicc)) {
                Storage::disk('public')->delete($licencia->pdflicc);
            }

            $file = $request->file('pdflicc');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('licencias_conducir_pdfs', $fileName, 'public');
            $data['pdflicc'] = $path;
        }

        $licencia->update($data);

        return redirect()->route('licencias-conducir.index')
            ->with('success', 'Licencia de Conducir actualizada exitosamente.');
    }

    public function destroy(LicenciaConducir $licencia)
    {
        if ($licencia->pdflicc && Storage::disk('public')->exists($licencia->pdflicc)) {
            Storage::disk('public')->delete($licencia->pdflicc);
        }

        $licencia->delete();

        return redirect()->route('licencias-conducir.index')
            ->with('success', 'Licencia de Conducir eliminada exitosamente.');
    }

    public function downloadPdf(LicenciaConducir $licencia)
    {
        if (!$licencia->pdflicc || !Storage::disk('public')->exists($licencia->pdflicc)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($licencia->pdflicc);
    }
}
