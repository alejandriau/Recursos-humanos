<?php

namespace App\Http\Controllers;

use App\Models\Cenvi;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class CenviController extends Controller
{


    public function index(Request $request)
    {
        $query = Cenvi::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
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

        // Filtro por vigencia
        if ($request->has('vigencia') && $request->vigencia != '') {
            if ($request->vigencia == 'vigentes') {
                $query->vigentes();
            } elseif ($request->vigencia == 'vencidos') {
                $query->vencidos();
            }
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fecha');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $cenvis = $query->paginate(50);

        // Obtener todas las personas para el filtro
        $personas = Persona::where('estado', 1)->get();

        return view('admin.cenvis.index', compact('cenvis', 'personas'));
    }

    // Los demás métodos permanecen igual...
    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.cenvis.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'observacion' => 'nullable|string|max:100',
            'pdfcenvi' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->all();

        // Verificar vigencia al crear
        $fechaEmision = Carbon::parse($data['fecha']);
        $data['estado'] = $fechaEmision->gte(Carbon::now()->subYear()) ? 1 : 0;

        if ($request->hasFile('pdfcenvi')) {
            $file = $request->file('pdfcenvi');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cenvi_pdfs', $fileName, 'public');
            $data['pdfcenvi'] = $path;
        }

        Cenvi::create($data);

        return redirect()->route('cenvis.index')
            ->with('success', 'CENVI creado exitosamente.');
    }

    public function show(Cenvi $cenvi)
    {
        // Actualizar estado por si acaso
        $cenvi->actualizarEstadoPorVigencia();

        return view('admin.cenvis.show', compact('cenvi'));
    }

    public function edit(Cenvi $cenvi)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.cenvis.edit', compact('cenvi', 'personas'));
    }

    public function update(Request $request, Cenvi $cenvi)
    {
        $request->validate([
            'fecha' => 'required|date',
            'observacion' => 'nullable|string|max:100',
            'pdfcenvi' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        // Si cambia la fecha, recalcular estado automáticamente
        if ($request->has('fecha') && $request->fecha != $cenvi->fecha->format('Y-m-d')) {
            $fechaEmision = Carbon::parse($data['fecha']);
            $data['estado'] = $fechaEmision->gte(Carbon::now()->subYear()) ? 1 : 0;
        }

        if ($request->hasFile('pdfcenvi')) {
            if ($cenvi->pdfcenvi && Storage::disk('public')->exists($cenvi->pdfcenvi)) {
                Storage::disk('public')->delete($cenvi->pdfcenvi);
            }

            $file = $request->file('pdfcenvi');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cenvi_pdfs', $fileName, 'public');
            $data['pdfcenvi'] = $path;
        }

        $cenvi->update($data);

        return redirect()->route('cenvis.index')
            ->with('success', 'CENVI actualizado exitosamente.');
    }

    public function destroy(Cenvi $cenvi)
    {
        if ($cenvi->pdfcenvi && Storage::disk('public')->exists($cenvi->pdfcenvi)) {
            Storage::disk('public')->delete($cenvi->pdfcenvi);
        }

        $cenvi->delete();

        return redirect()->route('cenvis.index')
            ->with('success', 'CENVI eliminado exitosamente.');
    }

    public function downloadPdf(Cenvi $cenvi)
    {
        if (!$cenvi->pdfcenvi || !Storage::disk('public')->exists($cenvi->pdfcenvi)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($cenvi->pdfcenvi);
    }
}
