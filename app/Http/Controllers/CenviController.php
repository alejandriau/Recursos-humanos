<?php

namespace App\Http\Controllers;

use App\Models\Cenvi;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CenviController extends Controller
{
    public function index(Request $request)
    {
        $query = Cenvi::with('persona');

        // Buscar por persona
        if ($request->filled('nombre')) {
            $query->whereHas('persona', function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->nombre}%")
                  ->orWhere('apellidoPat', 'like', "%{$request->nombre}%")
                  ->orWhere('apellidoMat', 'like', "%{$request->nombre}%");
            });
        }

        // Filtros por fecha
        $query->when($request->fecha_desde, fn($q) =>
            $q->whereDate('fecha', '>=', $request->fecha_desde)
        );

        $query->when($request->fecha_hasta, fn($q) =>
            $q->whereDate('fecha', '<=', $request->fecha_hasta)
        );

        // Filtro por vigencia
        match ($request->vigencia) {
            'vigentes'   => $query->vigentes(),
            'vencidos'   => $query->vencidos(),
            'por_vencer' => $query->porVencer(),
            default      => null,
        };

        // Orden
        $query->orderBy(
            $request->get('order_by', 'fecha'),
            $request->get('order_direction', 'desc')
        );

        $cenvis = $query->paginate(50)->withQueryString();
        $personas = Persona::where('estado', 1)->get();

        $estadisticas = [
            'total'      => Cenvi::count(),
            'vigentes'   => Cenvi::vigentes()->count(),
            'vencidos'   => Cenvi::vencidos()->count(),
            'por_vencer' => Cenvi::porVencer()->count(),
        ];

        return view('admin.cenvis.index', compact(
            'cenvis',
            'personas',
            'estadisticas'
        ));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.cenvis.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha'        => 'required|date',
            'observacion'  => 'nullable|string|max:100',
            'pdf_cenvi'    => 'nullable|file|mimes:pdf|max:2048',
            'persona_id'   => 'required|exists:personas,id',
        ]);

        if ($request->hasFile('pdf_cenvi')) {
            $data['pdf_cenvi'] = $this->guardarArchivo($request->file('pdf_cenvi'));
        }

        $cenvi = Cenvi::create($data);
        $cenvi->actualizarEstadoPorVigencia();

        return redirect()->route('cenvis.index')
            ->with('success', 'CENVI creado correctamente.');
    }

    public function show(Cenvi $cenvi)
    {
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
        $data = $request->validate([
            'fecha'        => 'required|date',
            'observacion'  => 'nullable|string|max:100',
            'pdf_cenvi'    => 'nullable|file|mimes:pdf|max:2048',
            'persona_id'   => 'required|exists:personas,id',
        ]);

        if ($request->hasFile('pdf_cenvi')) {
            if ($cenvi->pdf_cenvi) {
                $this->eliminarArchivo($cenvi->pdf_cenvi);
            }
            $data['pdf_cenvi'] = $this->guardarArchivo($request->file('pdf_cenvi'));
        }

        $cenvi->update($data);
        $cenvi->actualizarEstadoPorVigencia();

        return redirect()->route('cenvis.index')
            ->with('success', 'CENVI actualizado correctamente.');
    }

    public function destroy(Cenvi $cenvi)
    {
        if ($cenvi->pdf_cenvi) {
            $this->eliminarArchivo($cenvi->pdf_cenvi);
        }

        $cenvi->delete();

        return redirect()->route('cenvis.index')
            ->with('success', 'CENVI eliminado correctamente.');
    }

    public function downloadPdf(Cenvi $cenvi)
    {
        if (!$cenvi->pdf_cenvi || !Storage::disk('public')->exists($cenvi->pdf_cenvi)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($cenvi->pdf_cenvi);
    }

    /* =======================
     |  ARCHIVOS
     ======================= */

    private function guardarArchivo($file): string
    {
        return $file->store('cenvi_pdfs', 'public');
    }

    private function eliminarArchivo(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
