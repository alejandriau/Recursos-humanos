<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasivodos;
use App\Models\Seleccion;

class SeleccionController extends Controller
{
        // Mostrar tabla de selecciones con nombre del pasivo (paginado)
    public function index()
    {
        $selecciones = Seleccion::with('pasivodos')->paginate(10); // paginado de 10
       

        return view('seleccions.index', compact('selecciones'));
    }

    // Guardar una nueva selección
    public function store(Request $request)
    {
        $request->validate([
            'idPasivodos' => 'required|exists:pasivodos,id',
            'registro' => 'nullable|string|max:45',
        ]);

        Seleccion::create([
            'idPasivodos' => $request->idPasivodos,
            'registro' => $request->registro,
        ]);
        
        return redirect()->back()->with('success', 'Registro guardado correctamente.');
    }

    // Eliminar una selección por ID
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $registro = Seleccion::find($id);
        if ($registro) {
            $registro->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 404);
        }
    }

    // Eliminar todas las selecciones
    public function destroyAll()
    {
        Seleccion::truncate();

        return redirect()->back()->with('success', 'Todos los registros fueron eliminados.');
    }
}
