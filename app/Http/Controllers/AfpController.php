<?php

namespace App\Http\Controllers;

use App\Models\Afp;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AfpController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver afps')->only(['index', 'show']);
    //    $this->middleware('permission:crear afps')->only(['create', 'store']);
    //    $this->middleware('permission:editar afps')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar afps')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf afps')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = Afp::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por CUA
        if ($request->has('cua') && $request->cua != '') {
            $query->where('cua', 'like', '%' . $request->cua . '%');
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fechaRegistro');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $afps = $query->paginate(50);

        return view('admin.afps.index', compact('afps'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.afps.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cua' => 'required|string|max:45',
            'observacion' => 'nullable|string|max:500',
            'pdfafps' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfafps')) {
            $file = $request->file('pdfafps');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('afps_pdfs', $fileName, 'public');
            $data['pdfafps'] = $path;
        }

        Afp::create($data);

        return redirect()->route('afps.index')
            ->with('success', 'AFP creado exitosamente.');
    }

    public function show(Afp $afp)
    {
        return view('admin.afps.show', compact('afp'));
    }

    public function edit(Afp $afp)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.afps.edit', compact('afp', 'personas'));
    }

    public function update(Request $request, Afp $afp)
    {
        $request->validate([
            'cua' => 'required|string|max:45',
            'observacion' => 'nullable|string|max:500',
            'pdfafps' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfafps')) {
            if ($afp->pdfafps && Storage::disk('public')->exists($afp->pdfafps)) {
                Storage::disk('public')->delete($afp->pdfafps);
            }

            $file = $request->file('pdfafps');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('afps_pdfs', $fileName, 'public');
            $data['pdfafps'] = $path;
        }

        $afp->update($data);

        return redirect()->route('afps.index')
            ->with('success', 'AFP actualizado exitosamente.');
    }

    public function destroy(Afp $afp)
    {
        if ($afp->pdfafps && Storage::disk('public')->exists($afp->pdfafps)) {
            Storage::disk('public')->delete($afp->pdfafps);
        }

        $afp->delete();

        return redirect()->route('afps.index')
            ->with('success', 'AFP eliminado exitosamente.');
    }

    public function downloadPdf(Afp $afp)
    {
        if (!$afp->pdfafps || !Storage::disk('public')->exists($afp->pdfafps)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($afp->pdfafps);
    }
}
