<?php

namespace App\Http\Controllers;

use App\Models\CedulaIdentidad;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CedulaIdentidadController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver cedulas')->only(['index', 'show']);
    //    $this->middleware('permission:crear cedulas')->only(['create', 'store']);
    //    $this->middleware('permission:editar cedulas')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar cedulas')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf cedulas')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = CedulaIdentidad::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por CI
        if ($request->has('ci') && $request->ci != '') {
            $query->where('ci', 'like', '%' . $request->ci . '%');
        }

        // Filtro por expedido
        if ($request->has('expedido') && $request->expedido != '') {
            $query->where('expedido', 'like', '%' . $request->expedido . '%');
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Filtro por vencimiento
        if ($request->has('vencimiento') && $request->vencimiento != '') {
            $today = Carbon::now();
            if ($request->vencimiento == 'vencidas') {
                $query->where('fechaVencimiento', '<', $today);
            } elseif ($request->vencimiento == 'vigentes') {
                $query->where('fechaVencimiento', '>=', $today);
            }
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fechaRegistro');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $cedulas = $query->paginate(50);

        return view('admin.cedulas.index', compact('cedulas'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.cedulas.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci' => 'nullable|string|max:45',
            'fechanacimiento' => 'nullable|date',
            'fechaVencimiento' => 'nullable|date|after:fechanacimiento',
            'expedido' => 'nullable|string|max:100',
            'nacido' => 'nullable|string|max:1500',
            'domicilio' => 'nullable|string|max:1500',
            'pdfcedula' => 'nullable|file|mimes:pdf|max:2048',
            'observacion' => 'nullable|string|max:300',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->all();

        // Calcular estado basado en vencimiento si hay fecha de vencimiento
        if ($request->has('fechaVencimiento') && $request->fechaVencimiento) {
            $data['estado'] = Carbon::parse($request->fechaVencimiento)->gte(Carbon::now());
        }

        if ($request->hasFile('pdfcedula')) {
            $file = $request->file('pdfcedula');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cedulas_pdfs', $fileName, 'public');
            $data['pdfcedula'] = $path;
        }

        CedulaIdentidad::create($data);

        return redirect()->route('cedulas.index')
            ->with('success', 'Cédula de Identidad creada exitosamente.');
    }

    public function show(CedulaIdentidad $cedula)
    {
        $cedula->actualizarEstadoPorVencimiento();
        return view('admin.cedulas.show', compact('cedula'));
    }

    public function edit(CedulaIdentidad $cedula)
    {
        $personas = Persona::where('estado', 1)->get();
        $cedula->actualizarEstadoPorVencimiento();
        return view('admin.cedulas.edit', compact('cedula', 'personas'));
    }

    public function update(Request $request, CedulaIdentidad $cedula)
    {
        $request->validate([
            'ci' => 'nullable|string|max:45',
            'fechanacimiento' => 'nullable|date',
            'fechaVencimiento' => 'nullable|date|after:fechanacimiento',
            'expedido' => 'nullable|string|max:100',
            'nacido' => 'nullable|string|max:1500',
            'domicilio' => 'nullable|string|max:1500',
            'pdfcedula' => 'nullable|file|mimes:pdf|max:2048',
            'observacion' => 'nullable|string|max:300',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        // Si cambia la fecha de vencimiento, recalcular estado automáticamente
        if ($request->has('fechaVencimiento') && $request->fechaVencimiento != $cedula->fechaVencimiento?->format('Y-m-d')) {
            $data['estado'] = Carbon::parse($request->fechaVencimiento)->gte(Carbon::now());
        }

        if ($request->hasFile('pdfcedula')) {
            if ($cedula->pdfcedula && Storage::disk('public')->exists($cedula->pdfcedula)) {
                Storage::disk('public')->delete($cedula->pdfcedula);
            }

            $file = $request->file('pdfcedula');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cedulas_pdfs', $fileName, 'public');
            $data['pdfcedula'] = $path;
        }

        $cedula->update($data);

        return redirect()->route('cedulas.index')
            ->with('success', 'Cédula de Identidad actualizada exitosamente.');
    }

    public function destroy(CedulaIdentidad $cedula)
    {
        if ($cedula->pdfcedula && Storage::disk('public')->exists($cedula->pdfcedula)) {
            Storage::disk('public')->delete($cedula->pdfcedula);
        }

        $cedula->delete();

        return redirect()->route('cedulas.index')
            ->with('success', 'Cédula de Identidad eliminada exitosamente.');
    }

    public function downloadPdf(CedulaIdentidad $cedula)
    {
        if (!$cedula->pdfcedula || !Storage::disk('public')->exists($cedula->pdfcedula)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($cedula->pdfcedula);
    }
}
