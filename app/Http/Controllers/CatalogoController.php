<?php

namespace App\Http\Controllers;

use App\Models\AreaConocimiento;
use App\Models\NivelAcademico;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    // ==================== ÁREAS DE CONOCIMIENTO ====================
    
    public function areasIndex()
    {
        $areas = AreaConocimiento::orderBy('nombre')->paginate(20);
        return view('catalogos.areas.index', compact('areas'));
    }
    
    public function store(Request $request): JsonResponse
    {
        try {
            // Limpiar y sanitizar los datos
            $nombre = strip_tags(trim($request->nombre));
            $descripcion = strip_tags(trim($request->descripcion));
            
            Log::info('Datos recibidos', [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'raw_nombre' => $request->nombre,
                'raw_descripcion' => $request->descripcion
            ]);
            
            // Validación
            $validator = Validator::make(
                ['nombre' => $nombre, 'descripcion' => $descripcion],
                [
                    'nombre' => 'required|string|max:300|unique:areas_conocimiento,nombre',
                    'descripcion' => 'nullable|string|max:1000'
                ]
            );
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Verificar si la tabla existe
            if (!Schema::hasTable('areas_conocimiento')) {
                Log::error('La tabla areas_conocimiento no existe');
                return response()->json([
                    'success' => false,
                    'message' => 'Error de configuración: La tabla areas_conocimiento no existe'
                ], 500);
            }
            
            // Crear el área - CORREGIDO: Usar AreaConocimiento en lugar de Area
            $area = new AreaConocimiento();
            $area->nombre = $nombre;
            $area->descripcion = $descripcion ?: null;
            $area->estado = true; // Si tienes este campo en tu tabla
            
            if ($area->save()) {
                Log::info('Área creada exitosamente', ['id' => $area->id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Área creada exitosamente',
                    'data' => $area
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo guardar el área'
                ], 500);
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error de base de datos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error general: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    public function areasUpdate(Request $request, $id)
    {
        $area = AreaConocimiento::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:150|unique:areas_conocimiento,nombre,' . $id,
            'descripcion' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $area->update($request->all());
        
        return response()->json([
            'success' => true,
            'data' => $area,
            'message' => 'Área actualizada correctamente'
        ]);
    }
    
    public function areasDestroy($id)
    {
        $area = AreaConocimiento::findOrFail($id);
        
        // Verificar si tiene carreras asociadas
        if ($area->carreras()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el área porque tiene carreras asociadas'
            ], 400);
        }
        
        $area->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Área eliminada correctamente'
        ]);
    }
    
    // ==================== NIVELES ACADÉMICOS ====================
    
    public function nivelesIndex()
    {
        $niveles = NivelAcademico::orderBy('orden')->paginate(20);
        return view('catalogos.niveles.index', compact('niveles'));
    }
    
public function nivelesStore(Request $request): JsonResponse
{
    try {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:niveles_academicos,nombre',
            'orden' => 'required|integer|min:0',
            'esTituloUniversitario' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $nivel = NivelAcademico::create([
            'nombre' => $request->nombre,
            'orden' => $request->orden,
            'esTituloUniversitario' => $request->esTituloUniversitario ? true : false,
            'estado' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Nivel académico creado exitosamente',
            'data' => $nivel
        ], 201);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
    
    public function nivelesUpdate(Request $request, $id)
    {
        $nivel = NivelAcademico::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:niveles_academicos,nombre,' . $id,
            'orden' => 'required|integer|min:0',
            'esTituloUniversitario' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $nivel->update($request->all());
        
        return response()->json([
            'success' => true,
            'data' => $nivel,
            'message' => 'Nivel académico actualizado correctamente'
        ]);
    }
    
    public function nivelesDestroy($id)
    {
        $nivel = NivelAcademico::findOrFail($id);
        
        // Verificar si tiene carreras asociadas
        if ($nivel->carreras()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el nivel porque tiene carreras asociadas'
            ], 400);
        }
        
        $nivel->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Nivel académico eliminado correctamente'
        ]);
    }
    
    // ==================== CARRERAS ====================
    
    public function carrerasIndex()
    {
        $carreras = Carrera::with(['areaConocimiento', 'nivelAcademico'])
            ->orderBy('nombre')
            ->paginate(20);
        
        $areas = AreaConocimiento::where('estado', true)->get();
        $niveles = NivelAcademico::where('estado', true)->get();
        
        return view('catalogos.carreras.index', compact('carreras', 'areas', 'niveles'));
    }
    

    
    public function carrerasUpdate(Request $request, $id)
    {
        $carrera = Carrera::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:200|unique:carreras,nombre,' . $id,
            'idAreaConocimiento' => 'required|exists:areas_conocimiento,id',
            'idNivelAcademico' => 'required|exists:niveles_academicos,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $carrera->update($request->all());
        
        return response()->json([
            'success' => true,
            'data' => $carrera->load(['areaConocimiento', 'nivelAcademico']),
            'message' => 'Carrera actualizada correctamente'
        ]);
    }
    
    public function carrerasDestroy($id)
    {
        $carrera = Carrera::findOrFail($id);
        
        // Verificar si tiene profesiones asociadas
        if ($carrera->profesiones()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la carrera porque tiene profesiones asociadas'
            ], 400);
        }
        
        $carrera->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Carrera eliminada correctamente'
        ]);
    }
    
    /**
     * API: Obtener carreras por área de conocimiento
     */
    public function carrerasPorArea($idArea)
    {
        $carreras = Carrera::with(['nivelAcademico'])
            ->where('idAreaConocimiento', $idArea)
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $carreras
        ]);
    }
    
    /**
     * API: Obtener carreras por nivel académico
     */
    public function carrerasPorNivel($idNivel)
    {
        $carreras = Carrera::with(['areaConocimiento'])
            ->where('idNivelAcademico', $idNivel)
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $carreras
        ]);
    }
}