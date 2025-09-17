<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Afps;
use App\Models\Persona;
use App\Models\Cajacordes;

class ArchivoController extends Controller
{
    public function index($id)
    {
        $persona = Persona::findOrFail($id);
        return view('admin.archivos.afps', compact('persona'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cua' => 'required|string|max:45',
            'observacion' => 'nullable|string|max:500',
            'pdfafps' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|integer|exists:persona,id',
        ]);

        if ($request->hasFile('pdfafps')) {
            $validated['pdfafps'] = $request->file('pdfafps')->store('afps_pdfs', 'public');
        }

        Afps::create($validated);
        return redirect()->route('reportes.index')->with('success', 'Registro creado exitosamente.');
    }

    public function show($id)
    {
        $afp = Afp::with('persona')->findOrFail($id);
        return response()->json($afp);
    }

    public function update(Request $request, $id)
    {
        $afp = Afp::findOrFail($id);

        $validated = $request->validate([
            'cua' => 'required|string|max:45',
            'observacion' => 'nullable|string|max:500',
            'pdfafps' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|integer|exists:personas,id',
        ]);

        if ($request->hasFile('pdfafps')) {
            if ($afp->pdfafps) {
                Storage::disk('public')->delete($afp->pdfafps);
            }
            $validated['pdfafps'] = $request->file('pdfafps')->store('afps_pdfs', 'public');
        }

        $afp->update($validated);
        return redirect()->route('reportes.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $afp = Afp::findOrFail($id);
        $afp->delete();

        return response()->json(['message' => 'Registro eliminado']);
    }
    //Caja cordes
    public function Cajacreate()
    {
        $personas = Persona::where('estado', 1)->get();
        return view('admin.archivos.cajacordes', compact('personas'));
    }
    public function Cajaedit($id)
    {
        $caja = Cajacorde::findOrFail($id);
        $personas = Persona::where('estado', 1)->get();
        return view('admin.archivos.cajacordes', compact('caja', 'personas'));
    }
    public function Cajastore(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'codigo' => 'nullable|string|max:45',
            'otros' => 'nullable|string|max:45',
            'pdfcaja' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfcaja')) {
            $data['pdfcaja'] = $request->file('pdfcaja')->store('pdfcajacordes', 'public');
        }

        Cajacorde::create($data);

        return redirect()->route('cajacordes.index')->with('success', 'Registro creado exitosamente.');
    }
    public function Cajaupdate(Request $request, $id)
    {
        $caja = Cajacorde::findOrFail($id);

        $request->validate([
            'fecha' => 'required|date',
            'codigo' => 'nullable|string|max:45',
            'otros' => 'nullable|string|max:45',
            'pdfcaja' => 'nullable|file|mimes:pdf|max:2048',
            'idPersona' => 'required|exists:personas,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('pdfcaja')) {
            $data['pdfcaja'] = $request->file('pdfcaja')->store('pdfcajacordes', 'public');
        }

        $caja->update($data);

        return redirect()->route('cajacordes.index')->with('success', 'Registro actualizado correctamente.');
    }



}
