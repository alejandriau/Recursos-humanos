<?php

namespace App\Http\Controllers;

use App\Models\CajaCorde;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CajaCordeController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver cajacordes')->only(['index', 'show']);
    //    $this->middleware('permission:crear cajacordes')->only(['create', 'store']);
    //    $this->middleware('permission:editar cajacordes')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar cajacordes')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf cajacordes')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = Cajacorde::with('persona');

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

        // Filtro por otros
        if ($request->has('otros') && $request->otros != '') {
            $query->where('otros', 'like', '%' . $request->otros . '%');
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

        $cajacordes = $query->paginate(50);

        return view('admin.cajacordes.index', compact('cajacordes'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.cajacordes.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'codigo' => 'nullable|string|max:45',
            'otros' => 'nullable|string|max:45',
            'pdfcaja' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfcaja')) {
            $file = $request->file('pdfcaja');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cajacordes_pdfs', $fileName, 'public');
            $data['pdfcaja'] = $path;
        }

        Cajacorde::create($data);

        return redirect()->route('cajacordes.index')
            ->with('success', 'Caja de Cordes creada exitosamente.');
    }

    public function show(Cajacorde $cajacorde)
    {
        return view('admin.cajacordes.show', compact('cajacorde'));
    }

    public function edit(Cajacorde $cajacorde)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.cajacordes.edit', compact('cajacorde', 'personas'));
    }

    public function update(Request $request, Cajacorde $cajacorde)
    {
        $request->validate([
            'fecha' => 'required|date',
            'codigo' => 'nullable|string|max:45',
            'otros' => 'nullable|string|max:45',
            'pdfcaja' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfcaja')) {
            if ($cajacorde->pdfcaja && Storage::disk('public')->exists($cajacorde->pdfcaja)) {
                Storage::disk('public')->delete($cajacorde->pdfcaja);
            }

            $file = $request->file('pdfcaja');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('cajacordes_pdfs', $fileName, 'public');
            $data['pdfcaja'] = $path;
        }

        $cajacorde->update($data);

        return redirect()->route('cajacordes.index')
            ->with('success', 'Caja de Cordes actualizada exitosamente.');
    }

    public function destroy(Cajacorde $cajacorde)
    {
        if ($cajacorde->pdfcaja && Storage::disk('public')->exists($cajacorde->pdfcaja)) {
            Storage::disk('public')->delete($cajacorde->pdfcaja);
        }

        $cajacorde->delete();

        return redirect()->route('cajacordes.index')
            ->with('success', 'Caja de Cordes eliminada exitosamente.');
    }

    public function downloadPdf(Cajacorde $cajacorde)
    {
        if (!$cajacorde->pdfcaja || !Storage::disk('public')->exists($cajacorde->pdfcaja)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($cajacorde->pdfcaja);
    }
}
