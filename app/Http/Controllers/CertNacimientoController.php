<?php

namespace App\Http\Controllers;

use App\Models\CertNacimiento;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CertNacimientoController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('permission:ver certificados nacimiento')->only(['index', 'show']);
    //    $this->middleware('permission:crear certificados nacimiento')->only(['create', 'store']);
    //    $this->middleware('permission:editar certificados nacimiento')->only(['edit', 'update']);
    //    $this->middleware('permission:eliminar certificados nacimiento')->only(['destroy']);
    //    $this->middleware('permission:descargar pdf certificados nacimiento')->only(['downloadPdf']);
    //}

    public function index(Request $request)
    {
        $query = CertNacimiento::with('persona');

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
        $orderBy = $request->get('order_by', 'fechaRegistro');
        $orderDirection = $request->get('order_direction', 'desc');

        $query->orderBy($orderBy, $orderDirection);

        $certificados = $query->paginate(50);

        return view('admin.certificados-nacimiento.index', compact('certificados'));
    }

    public function create()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.certificados-nacimiento.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string|max:250',
            'pdfcern' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfcern')) {
            $file = $request->file('pdfcern');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('certificados_nacimiento_pdfs', $fileName, 'public');
            $data['pdfcern'] = $path;
        }

        CertNacimiento::create($data);

        return redirect()->route('certificados-nacimiento.index')
            ->with('success', 'Certificado de Nacimiento creado exitosamente.');
    }

    public function show(CertNacimiento $certificado)
    {
        return view('admin.certificados-nacimiento.show', compact('certificado'));
    }

    public function edit(CertNacimiento $certificado)
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.certificados-nacimiento.edit', compact('certificado', 'personas'));
    }

    public function update(Request $request, CertNacimiento $certificado)
    {
        $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string|max:250',
            'pdfcern' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:persona,id',
            'estado' => 'required|boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfcern')) {
            if ($certificado->pdfcern && Storage::disk('public')->exists($certificado->pdfcern)) {
                Storage::disk('public')->delete($certificado->pdfcern);
            }

            $file = $request->file('pdfcern');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('certificados_nacimiento_pdfs', $fileName, 'public');
            $data['pdfcern'] = $path;
        }

        $certificado->update($data);

        return redirect()->route('certificados-nacimiento.index')
            ->with('success', 'Certificado de Nacimiento actualizado exitosamente.');
    }

    public function destroy(CertNacimiento $certificado)
    {
        if ($certificado->pdfcern && Storage::disk('public')->exists($certificado->pdfcern)) {
            Storage::disk('public')->delete($certificado->pdfcern);
        }

        $certificado->delete();

        return redirect()->route('certificados-nacimiento.index')
            ->with('success', 'Certificado de Nacimiento eliminado exitosamente.');
    }

    public function downloadPdf(CertNacimiento $certificado)
    {
        if (!$certificado->pdfcern || !Storage::disk('public')->exists($certificado->pdfcern)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($certificado->pdfcern);
    }
}
