<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasivouno;

class PasivounoController extends Controller
{
            // Listar todos los registros
            public function index()
            {
                return view('admin.pasivos.pasivosuno.index');
            }

            // buscar
            public function buscar(Request $request)
            {
                $request->validate([
                    'pasivosu' => 'required|string'
                ]);

                $search = $request->input('pasivosu');

                $resultados = Pasivouno::where('nombrecompleto', 'like', '%' . $search . '%')->get();

                return view('admin.pasivos.pasivosuno.index', compact('resultados'));
            }

            // Mostrar un registro específico
            public function show($id)
            {
                $registro = Pasivouno::find($id);
                return $registro ? response()->json($registro) : response()->json(['message' => 'No encontrado'], 404);
            }

            // Crear un nuevo registro
            public function store(Request $request)
            {
                $nuevo = Pasivouno::create($request->all());
                return response()->json($nuevo, 201);
            }

            // Actualizar un registro existente
            public function update(Request $request, $id)
            {
                $registro = Pasivouno::find($id);
                if (!$registro) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }

                $registro->update($request->all());
                return response()->json($registro);
            }

            // Eliminar un registro
            public function destroy($id)
            {
                $registro = Pasivouno::find($id);
                if (!$registro) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }

                $registro->delete();
                return response()->json(['message' => 'Eliminado con éxito']);
            }
}
