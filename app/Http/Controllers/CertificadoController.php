<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificado;
use App\Models\Persona;
use Illuminate\Support\Facades\Storage;

class CertificadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificado::with('persona')->where('estado', 1);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->whereHas('persona', function ($q2) use ($buscar) {
                    $q2->where('nombre', 'like', "%$buscar%")
                    ->orWhere('apellidoPat', 'like', "%$buscar%")
                    ->orWhere('apellidoMat', 'like', "%$buscar%");
                })
                ->orWhere('nombre', 'like', "%$buscar%");
            });
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        $certificados = $query->get();

        return view('admin.certificados.index', compact('certificados'));
    }


public function create(Request $request)
{
    $personas = Persona::all();
    $idPersona = $request->get('idPersona'); // capturar persona previa

    return view('admin.certificados.create', compact('personas', 'idPersona'));
}


public function store(Request $request)
{
$request->validate([
    'nombre' => 'required|string|max:255',
    'tipo' => 'nullable|string|max:100',
    'fecha' => 'nullable|date',
    'instituto' => 'nullable|string|max:255',
    'pdfcerts' => 'nullable|file|mimes:pdf|max:2048',
    'idPersona' => 'required|exists:persona,id',
], [
    'pdfcerts.file' => 'El certificado debe ser un archivo.',
    'pdfcerts.mimes' => 'El certificado debe estar en formato PDF.',
    'pdfcerts.max' => 'El certificado no puede superar los 2 MB.',
]);


    $certificado = new Certificado();
    $certificado->nombre = $request->nombre;
    $certificado->tipo = $request->tipo;
    $certificado->fecha = $request->fecha;
    $certificado->instituto = $request->instituto;

    if ($request->hasFile('pdfcerts')) {
        $archivo = $request->file('pdfcerts')->store('certificados', 'public');
        $certificado->pdfcerts = $archivo;
    }

    $certificado->idPersona = $request->idPersona;
    $certificado->save();

    return redirect()->route('certificados.index')->with('success', 'Certificado guardado.');
}


    public function edit(Certificado $certificado)
    {
        // No necesitas cargar todas las personas, solo el certificado con su persona
        $certificado->load('persona');

        return view('admin.certificados.edit', compact('certificado'));
    }


    public function update(Request $request, Certificado $certificado)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:500',
            'tipo' => 'nullable|string|max:80',
            'fecha' => 'nullable|date',
            'instituto' => 'nullable|string|max:80',
            'pdfcerts' => 'nullable|file|mimes:pdf|max:10240', // archivo PDF
            'idPersona' => 'required|integer|exists:persona,id',
        ]);

        $persona = Persona::findOrFail($data['idPersona']);
        $rutaBase = $persona->archivo ?? 'archivos/' . $persona->id;

        // Si se subió un nuevo archivo PDF
        if ($request->hasFile('pdfcerts')) {
            $fecha = now()->format('Y-m-d_H-i-s');
            $nombreArchivo = "certificado_{$fecha}.pdf";
            $rutaCompleta = "{$rutaBase}/{$nombreArchivo}";

            Storage::disk('local')->putFileAs($rutaBase, $request->file('pdfcerts'), $nombreArchivo);

            // Reemplazamos solo si se sube uno nuevo
            $data['pdfcerts'] = $rutaCompleta;
        } else {
            // Si no hay nuevo archivo, mantener el anterior
            $data['pdfcerts'] = $certificado->pdfcerts;
        }

        $certificado->update($data);

        return redirect()->route('certificados.index')->with('success', 'Certificado actualizado correctamente.');
    }

    public function destroy(Certificado $certificado)
    {
        $certificado->estado = 0;
        $certificado->save();
        return redirect()->route('certificados.index')->with('success', 'Certificado eliminado correctamente.');
    }
}
