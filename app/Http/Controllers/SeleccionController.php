<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasivodos;
use App\Models\Seleccion;
use Illuminate\Support\Facades\Auth;

class SeleccionController extends Controller
{
    // Mostrar tabla de selecciones del usuario autenticado (paginado)
    public function index()
    {
        $selecciones = Seleccion::with('pasivodos')
            ->where('user_id', Auth::id()) // Solo selecciones del usuario logueado
            ->paginate(10);

        return view('seleccions.index', compact('selecciones'));
    }

    // Guardar una nueva selección asociada al usuario
    public function store(Request $request)
    {
        $request->validate([
            'idPasivodos' => 'required|exists:pasivodos,id',
            'registro' => 'nullable|string|max:45',
        ]);

        // Verificar si ya existe esta selección para este usuario
        $existeSeleccion = Seleccion::where('idPasivodos', $request->idPasivodos)
            ->where('user_id', Auth::id())
            ->exists();

        if ($existeSeleccion) {
            return redirect()->back()->with('error', 'Ya has seleccionado este registro.');
        }

        Seleccion::create([
            'idPasivodos' => $request->idPasivodos,
            'registro' => $request->registro,
            'user_id' => Auth::id(), // Asignar el usuario autenticado
        ]);

        return redirect()->back()->with('success', 'Registro guardado correctamente.');
    }

    // Eliminar una selección por ID (solo si pertenece al usuario)
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $registro = Seleccion::where('id', $id)
            ->where('user_id', Auth::id()) // Solo permitir eliminar propias selecciones
            ->first();

        if ($registro) {
            $registro->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Registro no encontrado o no autorizado'], 404);
        }
    }

    // Eliminar todas las selecciones del usuario actual


    // Nuevo método para obtener datos de selección (actualizado)
    public function traerSeleccion(Request $request)
    {
        $id = $request->input('idselecc');
        $pasivo = Pasivodos::find($id);

        if ($pasivo) {
            // Verificar si ya existe esta selección para evitar duplicados
            $existeSeleccion = Seleccion::where('idPasivodos', $id)
                ->where('user_id', Auth::id())
                ->exists();

            if ($existeSeleccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya has seleccionado este registro.'
                ]);
            }

            // Crear la nueva selección
            $seleccion = Seleccion::create([
                'idPasivodos' => $id,
                'user_id' => Auth::id(),
                'registro' => null // o algún valor por defecto
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    [
                        'id' => $pasivo->id,
                        'codigo' => $pasivo->letra . ' ' . $pasivo->codigo,
                        'nombrecompleto' => $pasivo->nombrecompleto,
                        'observacion' => $pasivo->observacion,
                        'idSeleccion' => $seleccion->id
                    ]
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Registro no encontrado.'
        ]);
    }

    // Eliminar todas las selecciones del usuario actual
public function destroyAll()
{
    try {
        $count = Seleccion::where('user_id', Auth::id())->count();

        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes registros para eliminar.'
            ]);
        }

        Seleccion::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todos tus registros fueron eliminados.',
            'count' => $count
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar los registros: ' . $e->getMessage()
        ], 500);
    }
}
}
