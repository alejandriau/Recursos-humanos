<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cas;
use App\Models\Persona;
use Illuminate\Support\Facades\Storage;

class CasController extends Controller
{
    public function index()
    {
        $cas = Cas::with('persona')->get();
        return view('admin.cas.index', compact('cas'));
    }

    public function create()
    {
        $personas = Persona::all();
        return view('admin.cas.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'anios' => 'nullable|string|max:45',
            'meses' => 'nullable|string|max:45',
            'dias' => 'nullable|string|max:45',
            'fechaEmision' => 'required|date',
            'fechaTiempo' => 'required|date',
            'pdfcas' => 'nullable|file|mimes:pdf|max:10240',
            'idPersona' => 'required|exists:persona,id',
        ]);

        $persona = Persona::findOrFail($data['idPersona']);
        $rutaBase = $persona->archivo ?? 'archivos/' . $persona->id;

        if ($request->hasFile('pdfcas')) {
            $fecha = now()->format('Y-m-d_H-i-s');
            $nombreArchivo = "cas_{$fecha}.pdf";
            $rutaCompleta = "{$rutaBase}/{$nombreArchivo}";

            Storage::disk('local')->putFileAs($rutaBase, $request->file('pdfcas'), $nombreArchivo);
            $data['pdfcas'] = $rutaCompleta;
        }

        Cas::create($data);

        return redirect()->route('cas.index')->with('success', 'CAS creado correctamente.');
    }

    public function edit(Cas $cas)
    {
        $personas = Persona::all();
        return view('admin.cas.edit', compact('cas', 'personas'));
    }

    public function update(Request $request, Cas $cas)
    {
        $data = $request->validate([
            'anios' => 'nullable|string|max:45',
            'meses' => 'nullable|string|max:45',
            'dias' => 'nullable|string|max:45',
            'fechaEmision' => 'required|date',
            'fechaTiempo' => 'required|date',
            'pdfcas' => 'nullable|file|mimes:pdf|max:10240',
            'idPersona' => 'required|exists:persona,id',
        ]);

        $persona = Persona::findOrFail($data['idPersona']);
        $rutaBase = $persona->archivo ?? 'archivos/' . $persona->id;

        if ($request->hasFile('pdfcas')) {
            $fecha = now()->format('Y-m-d_H-i-s');
            $nombreArchivo = "cas_{$fecha}.pdf";
            $rutaCompleta = "{$rutaBase}/{$nombreArchivo}";
            Storage::disk('local')->putFileAs($rutaBase, $request->file('pdfcas'), $nombreArchivo);
            $data['pdfcas'] = $rutaCompleta;
        } else {
            $data['pdfcas'] = $cas->pdfcas;
        }

        $cas->update($data);

        return redirect()->route('cas.index')->with('success', 'CAS actualizado correctamente.');
    }

    public function destroy(Cas $cas)
    {
        $cas->delete();
        return redirect()->route('cas.index')->with('success', 'CAS eliminado correctamente.');
    }
}
