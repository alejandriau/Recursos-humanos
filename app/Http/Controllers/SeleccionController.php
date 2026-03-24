<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasivodos;
use App\Models\Pasivouno;
use App\Models\Persona;
use App\Models\Seleccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SeleccionController extends Controller
{
    // Mostrar tabla de selecciones del usuario autenticado (paginado)
    public function index()
    {
        $selecciones = Seleccion::with('carpeta') // Relación polimórfica
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('seleccions.index', compact('selecciones'));
    }

    // Guardar una nueva selección asociada al usuario
public function store(Request $request)
{
    $request->validate([
        'carpeta_type' => 'required|in:pasivouno,pasivodos,persona',
        'carpeta_id' => 'required|integer',
        'registro' => 'nullable|string|max:250',
        'tipo_seleccion' => 'required|in:temporal,prestamo_directo'
    ]);

    // Verificar que el registro exista en la tabla correspondiente
    $modelo = $this->getModeloPorTipo($request->carpeta_type);
    $existeRegistro = $modelo::where('id', $request->carpeta_id)->exists();

    if (!$existeRegistro) {
        return redirect()->back()->with('error', 'El registro no existe en la base de datos.');
    }

    // Verificar si ya existe esta selección para este usuario
    $existeSeleccion = Seleccion::where('carpeta_type', $request->carpeta_type)
        ->where('carpeta_id', $request->carpeta_id)
        ->where('user_id', Auth::id())
        ->exists();

    if ($existeSeleccion) {
        return redirect()->back()->with('error', 'Ya has seleccionado este registro.');
    }

    // Podrías agregar un límite de selecciones por usuario
    $limiteSelecciones = 50; // Ejemplo
    $totalSelecciones = Seleccion::where('user_id', Auth::id())->count();
    
    if ($totalSelecciones >= $limiteSelecciones) {
        return redirect()->back()->with('error', 'Has alcanzado el límite máximo de selecciones.');
    }

    // Crear la selección con un registro generado automáticamente si quieres
    $seleccion = Seleccion::create([
        'carpeta_type' => $request->carpeta_type,
        'carpeta_id' => $request->carpeta_id,
        'registro' => $request->registro ?? $this->generarRegistro(), // Opcional
        'tipo_seleccion' => $request->tipo_seleccion,
        'user_id' => Auth::id(),
    ]);

    // Cargar la relación para la respuesta (si es AJAX)
    if ($request->ajax()) {
        $seleccion->load('carpeta');
        return response()->json([
            'success' => true,
            'message' => 'Registro guardado correctamente',
            'data' => $seleccion
        ]);
    }

    return redirect()->back()->with('success', 'Registro guardado correctamente.');
}

// Método auxiliar para generar registro automático
private function generarRegistro()
{
    return 'SEL-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Método auxiliar para obtener el modelo por tipo
    private function getModeloPorTipo($tipo)
    {
        return match($tipo) {
            'pasivouno' => \App\Models\Pasivouno::class,
            'pasivodos' => \App\Models\Pasivodos::class,
            'persona' => \App\Models\Persona::class,
            default => throw new \InvalidArgumentException("Tipo de carpeta no válido")
        };
    }

    // Eliminar una selección por ID (solo si pertenece al usuario)
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $registro = Seleccion::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($registro) {
            $registro->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json([
                'success' => false, 
                'message' => 'Registro no encontrado o no autorizado'
            ], 404);
        }
    }

    // Método para traer datos de selección (versátil para múltiples tipos)
    public function traerSeleccion(Request $request)
    {
        $request->validate([
            'carpeta_type' => 'required|in:pasivouno,pasivodos,persona',
            'carpeta_id' => 'required|integer',
            'tipo_seleccion' => 'sometimes|in:temporal,prestamo_directo'
        ]);

        try {
            DB::beginTransaction();

            // Obtener el modelo según el tipo
            $modelo = $this->getModeloPorTipo($request->carpeta_type);
            $registro = $modelo::find($request->carpeta_id);

            if (!$registro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado.'
                ]);
            }

            // Verificar si ya existe esta selección
            $existeSeleccion = Seleccion::where('carpeta_type', $request->carpeta_type)
                ->where('carpeta_id', $request->carpeta_id)
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
                'carpeta_type' => $request->carpeta_type,
                'carpeta_id' => $request->carpeta_id,
                'user_id' => Auth::id(),
                'tipo_seleccion' => $request->tipo_seleccion ?? 'temporal',
                'registro' => $this->generarRegistro($registro) // Método para generar código de registro
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    [
                        'id' => $registro->id,
                        'carpeta_type' => $request->carpeta_type,
                        'codigo' => $this->getCodigoRegistro($registro, $request->carpeta_type),
                        'nombrecompleto' => $this->getNombreCompleto($registro, $request->carpeta_type),
                        'observacion' => $this->getObservacion($registro, $request->carpeta_type),
                        'idSeleccion' => $seleccion->id,
                        'tipo_seleccion' => $seleccion->tipo_seleccion
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
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



    private function getCodigoRegistro($registro, $tipo)
    {
        return match($tipo) {
            'pasivouno', 'pasivodos' => ($registro->letra ?? '') . ' ' . ($registro->codigo ?? ''),
            'persona' => $registro->codigo ?? $registro->id,
            default => $registro->id
        };
    }

    private function getNombreCompleto($registro, $tipo)
    {
        return match($tipo) {
            'pasivouno', 'pasivodos' => $registro->nombrecompleto ?? ($registro->nombre ?? ''),
            'persona' => trim(($registro->nombre ?? '') . ' ' . ($registro->apellido ?? '')),
            default => ''
        };
    }

    private function getObservacion($registro, $tipo)
    {
        return match($tipo) {
            'pasivouno', 'pasivodos' => $registro->observacion ?? '',
            'persona' => $registro->cargo ?? $registro->observacion ?? '',
            default => ''
        };
    }

}