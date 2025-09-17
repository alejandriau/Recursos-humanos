<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Persona;
use App\Models\Profesion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Memopuesto;

class ProfesionController extends Controller
{
    public function index(Request $request)
    {
        $query = Profesion::with(['persona' => function($query) {
            $query->where('estado', 1); // Solo personas activas
        }]);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {
                $q->where('provisionN', 'like', "%$buscar%")
                ->orWhereHas('persona', function ($q2) use ($buscar) {
                    $q2->where('nombre', 'like', "%$buscar%")
                        ->orWhere('apellidoPat', 'like', "%$buscar%")
                        ->orWhere('apellidoMat', 'like', "%$buscar%")
                        ->orWhereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$buscar%"]);
                });
            });
        }

        $profesiones = $query->get();

        return view('admin.profesion.index', compact('profesiones'));
    }


    public function create(Persona $persona)
    {
        return view('.admin.profesion.create', compact('persona'));
    }

    public function store(Request $request, Persona $persona)
    {
        $data = $request->validate([
            'diploma' => 'nullable|string|max:200',
            'fechaDiploma' => 'nullable|date',
            'provisionN' => 'nullable|string|max:800',
            'fechaProvision' => 'nullable|date',
            'universidad' => 'nullable|string|max:150',
            'registro' => 'nullable|string|max:45',
            'observacion' => 'nullable|string|max:500',

            // Validaciones para los archivos PDF
            'pdfDiploma' => 'nullable|file|mimes:pdf|max:5120',       // 5MB
            'pdfProvision' => 'nullable|file|mimes:pdf|max:5120',
            'pdfcedulap' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data['idPersona'] = $persona->id;

        // Ruta base donde guardaremos los archivos (privado)
        $rutaBase = $persona->archivo ?? 'archivos/' . $persona->id;

        // Subir y guardar el archivo Diploma
        if ($request->hasFile('pdfDiploma')) {
            $fecha = now()->format('Y-m-d');
            $nombre = "diploma_{$fecha}.pdf";
            $ruta = "{$rutaBase}/{$nombre}";

            Storage::disk('local')->putFileAs($rutaBase, $request->file('pdfDiploma'), $nombre);
            $data['pdfDiploma'] = $ruta;
        }

        // Igual para los otros archivos:
        if ($request->hasFile('pdfProvision')) {
            $fecha = now()->format('Y-m-d');
            $nombre = "provision_{$fecha}.pdf";
            $ruta = "{$rutaBase}/{$nombre}";

            Storage::disk('local')->putFileAs($rutaBase, $request->file('pdfProvision'), $nombre);
            $data['pdfProvision'] = $ruta;
        }

        if ($request->hasFile('pdfcedulap')) {
            $fecha = now()->format('Y-m-d');
            $nombre = "cedula_profesion_{$fecha}.pdf";
            $ruta = "{$rutaBase}/{$nombre}";

            Storage::disk('local')->putFileAs($rutaBase, $request->file('pdfcedulap'), $nombre);
            $data['pdfcedulap'] = $ruta;
        }


        // Crear el registro de profesión
        Profesion::create($data);

        return redirect()->route('profesion.index')->with('success', 'Profesión registrada.');
    }

    public function edit(Persona $persona, Profesion $profesion)
    {
        return view('admin.profesion.edit', compact('persona', 'profesion'));
    }

    public function update(Request $request, Persona $persona, Profesion $profesion)
    {
        $data = $request->validate([
            'diploma' => 'nullable|string|max:200',
            'fechaDiploma' => 'nullable|date',
            'provisionN' => 'nullable|string|max:800',
            'fechaProvision' => 'nullable|date',
            'universidad' => 'nullable|string|max:150',
            'registro' => 'nullable|string|max:45',
            'observacion' => 'nullable|string|max:500',
        ]);

        $profesion->update($data);

        return redirect()->route('profesion.index', $persona)->with('success', 'Profesión actualizada.');
    }
}
