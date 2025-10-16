<?php

namespace App\Http\Controllers;

use App\Models\Djbrenta;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DjbrentaController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver djbrentas')->only(['index', 'show']);
    //    $this->middleware('permission:crear djbrentas')->only(['create', 'store']);
    //    $this->middleware('permission:editar djbrentas')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar djbrentas')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf djbrentas')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = Djbrenta::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por tipo
        if ($request->has('tipo') && $request->tipo != '') {
            $query->where('tipo', 'like', '%' . $request->tipo . '%');
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

        $djbrentas = $query->paginate(50);

        // Obtener tipos únicos para el filtro
        $tipos = Djbrenta::select('tipo')->whereNotNull('tipo')->distinct()->pluck('tipo');

        return view('admin.djbrentas.index', compact('djbrentas', 'tipos'));
    }

public function create()
{
    $personas = Persona::where('estado', 1)->get();

    // Verificar si viene del show de una persona
    $from_show = request()->has('from_dashboard');
    $persona_id = request()->get('persona_id');

    return view('admin.djbrentas.create', compact('personas', 'from_show', 'persona_id'));
}

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'nullable|string|max:600',
            'pdfrenta' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfrenta')) {
            $file = $request->file('pdfrenta');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('djbrenta_pdfs', $fileName, 'public');
            $data['pdfrenta'] = $path;
        }

        Djbrenta::create($data);

        if ($request->has('from_show')) {
            return redirect()->route('personas.show', $request->idPersona)
                ->with('success', 'DJBRenta creado exitosamente.');
        }

        return redirect()->route('djbrentas.index')
            ->with('success', 'DJBRenta creado exitosamente.');

    }

    public function show(Djbrenta $djbrenta)
    {
        return view('admin.djbrentas.show', compact('djbrenta'));
    }

    public function edit(Djbrenta $djbrenta)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.djbrentas.edit', compact('djbrenta', 'personas'));
    }

    public function update(Request $request, Djbrenta $djbrenta)
    {
        $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'nullable|string|max:600',
            'pdfrenta' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfrenta')) {
            if ($djbrenta->pdfrenta && Storage::disk('public')->exists($djbrenta->pdfrenta)) {
                Storage::disk('public')->delete($djbrenta->pdfrenta);
            }

            $file = $request->file('pdfrenta');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('djbrenta_pdfs', $fileName, 'public');
            $data['pdfrenta'] = $path;
        }

        $djbrenta->update($data);

        return redirect()->route('djbrentas.index')
            ->with('success', 'DJBRenta actualizado exitosamente.');
    }

    public function destroy(Djbrenta $djbrenta)
    {
        if ($djbrenta->pdfrenta && Storage::disk('public')->exists($djbrenta->pdfrenta)) {
            Storage::disk('public')->delete($djbrenta->pdfrenta);
        }

        $djbrenta->delete();

        return redirect()->route('djbrentas.index')
            ->with('success', 'DJBRenta eliminado exitosamente.');
    }

    public function downloadPdf(Djbrenta $djbrenta)
    {
        if (!$djbrenta->pdfrenta || !Storage::disk('public')->exists($djbrenta->pdfrenta)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($djbrenta->pdfrenta);
    }
}
