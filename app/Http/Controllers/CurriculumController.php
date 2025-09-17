<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class CurriculumController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver curriculums')->only(['index', 'show']);
    //    $this->middleware('permission:crear curriculums')->only(['create', 'store']);
    //    $this->middleware('permission:editar curriculums')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar curriculums')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf curriculums')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = Curriculum::with('persona');

        // Filtro por nombre de persona
        if ($request->has('nombre') && $request->nombre != '') {
            $query->whereHas('persona', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%');
            });
        }

        // Filtro por descripción
        if ($request->has('descripcion') && $request->descripcion != '') {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }

        // Filtro por campo "mas"
        if ($request->has('mas') && $request->mas != '') {
            $query->where('mas', 'like', '%' . $request->mas . '%');
        }

        // Filtro por campo "otros"
        if ($request->has('otros') && $request->otros != '') {
            $query->where('otros', 'like', '%' . $request->otros . '%');
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fechaRegistro');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $curriculums = $query->paginate(50);

        return view('admin.curriculums.index', compact('curriculums'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.curriculums.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'nullable|string|max:200',
            'mas' => 'nullable|string|max:200',
            'otros' => 'nullable|string|max:200',
            'pdfcorri' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfcorri')) {
            $file = $request->file('pdfcorri');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('curriculums_pdfs', $fileName, 'public');
            $data['pdfcorri'] = $path;
        }

        Curriculum::create($data);

        return redirect()->route('curriculums.index')
            ->with('success', 'Curriculum creado exitosamente.');
    }

    public function show(Curriculum $curriculum)
    {
        return view('admin.curriculums.show', compact('curriculum'));
    }

    public function edit(Curriculum $curriculum)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.curriculums.edit', compact('curriculum', 'personas'));
    }

    public function update(Request $request, Curriculum $curriculum)
    {
        $request->validate([
            'descripcion' => 'nullable|string|max:200',
            'mas' => 'nullable|string|max:200',
            'otros' => 'nullable|string|max:200',
            'pdfcorri' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfcorri')) {
            if ($curriculum->pdfcorri && Storage::disk('public')->exists($curriculum->pdfcorri)) {
                Storage::disk('public')->delete($curriculum->pdfcorri);
            }

            $file = $request->file('pdfcorri');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('curriculums_pdfs', $fileName, 'public');
            $data['pdfcorri'] = $path;
        }

        $curriculum->update($data);

        return redirect()->route('curriculums.index')
            ->with('success', 'Curriculum actualizado exitosamente.');
    }

    public function destroy(Curriculum $curriculum)
    {
        if ($curriculum->pdfcorri && Storage::disk('public')->exists($curriculum->pdfcorri)) {
            Storage::disk('public')->delete($curriculum->pdfcorri);
        }

        $curriculum->delete();

        return redirect()->route('curriculums.index')
            ->with('success', 'Curriculum eliminado exitosamente.');
    }

    public function downloadPdf(Curriculum $curriculum)
    {
        if (!$curriculum->pdfcorri || !Storage::disk('public')->exists($curriculum->pdfcorri)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($curriculum->pdfcorri);
    }
}
