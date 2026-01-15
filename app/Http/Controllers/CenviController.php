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
        if ($request->filled('nombre')) {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%')
                  ->orWhere('apellidoPat', 'like', '%' . $request->nombre . '%')
                  ->orWhere('apellidoMat', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtros de fecha
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por vigencia
        if ($request->filled('vigencia')) {
            if ($request->vigencia == 'vigentes') {
                $query->vigentes();
            } elseif ($request->vigencia == 'vencidos') {
                $query->vencidos();
            } elseif ($request->vigencia == 'por_vencer') {
                $query->porVencer();
            }
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fecha');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $cenvis = $query->paginate(50);
        $personas = Persona::where('estado', 1)->get();

        // PASAR ESTADÍSTICAS DESDE EL CONTROLLER
        $estadisticas = [
            'total' => Cenvi::count(),
            'vigentes' => Cenvi::where('estado', 1)->count(),
            'inactivos' => Cenvi::where('estado', 0)->count(),
        ];

        return view('admin.cenvis.index', compact('cenvis', 'personas', 'estadisticas'));
    }

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

        // Calcular estado automáticamente basado en vigencia
        $fechaEmision = Carbon::parse($data['fecha']);
        $fechaVencimiento = $fechaEmision->copy()->addYear();
        $data['estado'] = Carbon::now()->lt($fechaVencimiento) ? 1 : 0;

        if ($request->hasFile('pdfcenvi')) {
            $data['pdfcenvi'] = $this->guardarArchivo($request->file('pdfcenvi'));
        }

        Cenvi::create($data);

        return redirect()->route('cenvis.index')
            ->with('success', 'CENVI creado exitosamente.');
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
        $request->validate([
            'fecha' => 'required|date',
            'observacion' => 'nullable|string|max:100',
            'pdfcenvi' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        // Si cambia la fecha, recalcular estado automáticamente
        if ($request->fecha != $cenvi->fecha->format('Y-m-d')) {
            $fechaEmision = Carbon::parse($data['fecha']);
            $fechaVencimiento = $fechaEmision->copy()->addYear();
            $data['estado'] = Carbon::now()->lt($fechaVencimiento) ? 1 : 0;
        }

        if ($request->hasFile('pdfcenvi')) {
            // Eliminar archivo anterior si existe
            if ($cenvi->pdfcenvi) {
                $this->eliminarArchivo($cenvi->pdfcenvi);
            }
            $data['pdfcenvi'] = $this->guardarArchivo($request->file('pdfcenvi'));
        }

        $cenvi->update($data);

        return redirect()->route('cenvis.index')
            ->with('success', 'CENVI actualizado exitosamente.');
    }

    public function destroy(Cenvi $cenvi)
    {
        if ($cenvi->pdfcenvi) {
            $this->eliminarArchivo($cenvi->pdfcenvi);
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

    /**
     * Métodos auxiliares para manejo de archivos
     */
    private function guardarArchivo($file)
    {
        $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        return $file->storeAs('cenvi_pdfs', $fileName, 'public');
    }

    private function eliminarArchivo($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
