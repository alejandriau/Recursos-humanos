<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use App\Models\Persona;
use App\Models\ValidacionPerfil;
use App\Traits\ValidablePerfilTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidacionPerfilController extends Controller
{
    use ValidablePerfilTrait;

    /**
     * Mostrar formulario para seleccionar puesto y persona a validar
     */
    public function index()
    {
        $puestos = Puesto::with('perfilRequisitos')
            ->where('estado', true)
            ->where('esActivo', true)
            ->orderBy('denominacion')
            ->get();
        
            $personas = Persona::orderBy('apellidoPat')
                ->orderBy('apellidoMat')
                ->orderBy('nombre')
                ->get();
        
        return view('validacion.index', compact('puestos', 'personas'));
    }

    /**
     * Realizar validación de perfil
     */
    public function validar(Request $request)
    {
        $request->validate([
            'id_puesto' => 'required|exists:puestos,id',
            'id_persona' => 'required|exists:persona,id'
        ]);

        $puesto = Puesto::with(['perfilRequisitos', 'unidadOrganizacional'])
            ->findOrFail($request->id_puesto);
        
        $persona = Persona::with([
            'profesionPrincipal.carrera.areaConocimiento',
            'profesionPrincipal.carrera.nivelAcademico',
            'profesionesActivas.carrera.areaConocimiento',
            'profesionesActivas.carrera.nivelAcademico'
        ])->findOrFail($request->id_persona);

        // Realizar validación usando el Trait
        $resultado = $this->validarPerfilPuesto($puesto, $persona);
        
        // Guardar historial de validación
        $this->guardarHistorialValidacion($puesto, $persona, $resultado);
        
        return view('validacion.resultado', compact('puesto', 'persona', 'resultado'));
    }

    /**
     * Validar múltiples candidatos para un puesto
     */
    public function validarMultiple(Request $request)
    {
        $request->validate([
            'id_puesto' => 'required|exists:puestos,id',
            'idsPersonas' => 'required|array',
            'idsPersonas.*' => 'exists:personas,id'
        ]);

        $puesto = Puesto::with('perfilRequisitos')->findOrFail($request->id_puesto);
        
        $resultados = [];
        
        foreach ($request->idsPersonas as $id_persona) {
            $persona = Persona::with([
                'profesionPrincipal.carrera.areaConocimiento',
                'profesionPrincipal.carrera.nivelAcademico'
            ])->findOrFail($id_persona);
            
            $resultado = $this->validarPerfilPuesto($puesto, $persona);
            $resultados[] = [
                'persona' => $persona,
                'resultado' => $resultado
            ];
            
            // Guardar historial
            $this->guardarHistorialValidacion($puesto, $persona, $resultado);
        }
        
        // Ordenar: primero los que cumplen
        usort($resultados, function($a, $b) {
            return $b['resultado']['cumple'] <=> $a['resultado']['cumple'];
        });
        
        return view('validacion.resultados-multiple', compact('puesto', 'resultados'));
    }

    /**
     * Mostrar historial de validaciones
     */
    public function historial(Request $request)
    {
        $query = ValidacionPerfil::with(['puesto', 'persona', 'usuario'])
            ->orderBy('fechaValidacion', 'desc');
        
        if ($request->has('id_puesto') && $request->id_puesto) {
            $query->where('id_puesto', $request->id_puesto);
        }
        
        if ($request->has('id_persona') && $request->id_persona) {
            $query->where('id_persona', $request->id_persona);
        }
        
        if ($request->has('resultado') && $request->resultado !== '') {
            $query->where('resultado', $request->resultado);
        }
        
        $validaciones = $query->paginate(20);
        
        $puestos = Puesto::where('estado', true)->orderBy('denominacion')->get();
        $personas = Persona::orderBy('nombre_completo')->get();
        
        return view('validacion.historial', compact('validaciones', 'puestos', 'personas'));
    }

    /**
     * Mostrar reporte de validaciones
     */
    public function reporte(Request $request)
    {
        $request->validate([
            'fechaInicio' => 'nullable|date',
            'fechaFin' => 'nullable|date|after_or_equal:fechaInicio'
        ]);
        
        $query = ValidacionPerfil::with(['puesto', 'persona'])
            ->orderBy('fechaValidacion', 'desc');
        
        if ($request->fechaInicio) {
            $query->whereDate('fechaValidacion', '>=', $request->fechaInicio);
        }
        
        if ($request->fechaFin) {
            $query->whereDate('fechaValidacion', '<=', $request->fechaFin);
        }
        
        $validaciones = $query->get();
        
        // Estadísticas
        $total = $validaciones->count();
        $cumplen = $validaciones->where('resultado', true)->count();
        $noCumplen = $total - $cumplen;
        $porcentajeCumplimiento = $total > 0 ? ($cumplen / $total) * 100 : 0;
        
        // Agrupar por puesto
        $porPuesto = $validaciones->groupBy('id_puesto')->map(function ($group) {
            $total = $group->count();
            $cumplen = $group->where('resultado', true)->count();
            return [
                'puesto' => $group->first()->puesto->denominacion,
                'total' => $total,
                'cumplen' => $cumplen,
                'porcentaje' => $total > 0 ? ($cumplen / $total) * 100 : 0
            ];
        })->sortByDesc('porcentaje');
        
        return view('validacion.reporte', compact(
            'validaciones', 
            'total', 
            'cumplen', 
            'noCumplen', 
            'porcentajeCumplimiento',
            'porPuesto',
            'request'
        ));
    }

    /**
     * API: Validar candidato (para uso en JavaScript)
     */
    public function apiValidar(Request $request)
    {
        $request->validate([
            'id_puesto' => 'required|exists:puestos,id',
            'id_persona' => 'required|exists:personas,id'
        ]);

        $puesto = Puesto::with('perfilRequisitos')->findOrFail($request->id_puesto);
        
        $persona = Persona::with([
            'profesionPrincipal.carrera.areaConocimiento',
            'profesionPrincipal.carrera.nivelAcademico'
        ])->findOrFail($request->id_persona);

        $resultado = $this->validarPerfilPuesto($puesto, $persona);
        
        // Guardar historial
        $this->guardarHistorialValidacion($puesto, $persona, $resultado);
        
        return response()->json([
            'success' => true,
            'data' => $resultado
        ]);
    }

    /**
     * Guardar historial de validación
     */
    private function guardarHistorialValidacion($puesto, $persona, $resultado)
    {
        try {
            ValidacionPerfil::create([
                'id_puesto' => $puesto->id,
                'id_persona' => $persona->id,
                'id_usuario' => auth()->id(),
                'resultado' => $resultado['cumple'],
                'detalleValidacion' => $resultado['detalle'],
                'fechaValidacion' => now()
            ]);
        } catch (\Exception $e) {
            // No interrumpir el flujo si falla el guardado del historial
            \Log::error('Error al guardar historial de validación: ' . $e->getMessage());
        }
    }
}