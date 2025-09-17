<?php

namespace App\Http\Controllers;

use App\Models\Compromiso;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompromisoController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver compromisos')->only(['index', 'show']);
    //    $this->middleware('permission:crear compromisos')->only(['create', 'store']);
    //    $this->middleware('permission:editar compromisos')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar compromisos')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf compromisos')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = Compromiso::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Filtro por compromiso
        if ($request->has('compromiso') && $request->compromiso != '') {
            $query->where(function($q) use ($request) {
                $q->where('compromiso1', 'like', '%' . $request->compromiso . '%')
                  ->orWhere('compromiso2', 'like', '%' . $request->compromiso . '%')
                  ->orWhere('compromiso3', 'like', '%' . $request->compromiso . '%');
            });
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fechaRegistro');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $compromisos = $query->paginate(50);

        return view('admin.compromisos.index', compact('compromisos'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.compromisos.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'compromiso1' => 'nullable|string|max:45',
            'pdfcomp1' => 'nullable|file|mimes:pdf|max:2048',
            'compromiso2' => 'nullable|string|max:45',
            'pdfcomp2' => 'nullable|file|mimes:pdf|max:2048',
            'compromiso3' => 'nullable|string|max:45',
            'pdfcomp3' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->all();

        // Procesar archivos PDF
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = "pdfcomp{$i}";
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $fileName = time() . "_comp{$i}_" . $file->getClientOriginalName();
                $path = $file->storeAs('compromisos_pdfs', $fileName, 'public');
                $data[$fieldName] = $path;
            }
        }

        Compromiso::create($data);

        return redirect()->route('compromisos.index')
            ->with('success', 'Compromiso creado exitosamente.');
    }

    public function show(Compromiso $compromiso)
    {
        return view('admin.compromisos.show', compact('compromiso'));
    }

    public function edit(Compromiso $compromiso)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.compromisos.edit', compact('compromiso', 'personas'));
    }

    public function update(Request $request, Compromiso $compromiso)
    {
        $request->validate([
            'compromiso1' => 'nullable|string|max:45',
            'pdfcomp1' => 'nullable|file|mimes:pdf|max:2048',
            'compromiso2' => 'nullable|string|max:45',
            'pdfcomp2' => 'nullable|file|mimes:pdf|max:2048',
            'compromiso3' => 'nullable|string|max:45',
            'pdfcomp3' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        // Procesar archivos PDF
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = "pdfcomp{$i}";
            if ($request->hasFile($fieldName)) {
                // Eliminar archivo anterior si existe
                $oldFile = $compromiso->$fieldName;
                if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }

                $file = $request->file($fieldName);
                $fileName = time() . "_comp{$i}_" . $file->getClientOriginalName();
                $path = $file->storeAs('compromisos_pdfs', $fileName, 'public');
                $data[$fieldName] = $path;
            }
        }

        $compromiso->update($data);

        return redirect()->route('compromisos.index')
            ->with('success', 'Compromiso actualizado exitosamente.');
    }

    public function destroy(Compromiso $compromiso)
    {
        // Eliminar archivos PDF si existen
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = "pdfcomp{$i}";
            $filePath = $compromiso->$fieldName;
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $compromiso->delete();

        return redirect()->route('compromisos.index')
            ->with('success', 'Compromiso eliminado exitosamente.');
    }

    public function downloadPdf(Compromiso $compromiso, $numero)
    {
        $fieldName = "pdfcomp{$numero}";
        $filePath = $compromiso->$fieldName;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($filePath);
    }
}
