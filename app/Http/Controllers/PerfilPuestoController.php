<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use App\Models\PerfilPuesto;
use App\Models\AreaConocimiento;
use App\Models\NivelAcademico;
use App\Models\Carrera;
use App\Models\UnidadOrganizacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class PerfilPuestoController extends Controller
{
    /**
     * Listar todos los puestos con sus perfiles
     */
public function index(Request $request)
{
    // =========================
    // Filtros
    // =========================
    $search = $request->get('search');
    $unidadId = $request->get('unidad_id');
    $nivelJerarquico = $request->get('nivel_jerarquico');
    $tienePerfil = $request->get('tiene_perfil');

    // =========================
    // Query base
    // =========================
    $puestos = Puesto::with([
        'unidadOrganizacional',
        'perfilRequisitos'
    ])->where('estado', true);

    // =========================
    // Búsqueda general
    // =========================
    if ($search) {
        $puestos->where(function ($q) use ($search) {
            $q->where('denominacion', 'like', "%{$search}%")
              ->orWhere('item', 'like', "%{$search}%")
              ->orWhere('nivelJerarquico', 'like', "%{$search}%");
        });
    }

    // =========================
    // Filtro por unidad
    // =========================
    if ($unidadId) {
        $puestos->where('idUnidadOrganizacional', $unidadId);
    }

    // =========================
    // Filtro nivel jerárquico
    // =========================
    if ($nivelJerarquico) {
        $puestos->where('nivelJerarquico', $nivelJerarquico);
    }

    // =========================
    // Filtro tiene perfil
    // =========================
    if ($tienePerfil === 'si') {
        $puestos->has('perfilRequisitos');
    } elseif ($tienePerfil === 'no') {
        $puestos->doesntHave('perfilRequisitos');
    }

    // =========================
    // Paginación + orden
    // =========================
    $puestos = $puestos->orderByRaw('CAST(item AS UNSIGNED) ASC')
                   ->paginate(100);

    // =========================
    // Datos para filtros
    // =========================
    $unidades = UnidadOrganizacional::where('estado', true)->get();

    $nivelesJerarquicos = [
        'ESTRATEGICO' => 'Estratégico',
        'TACTICO' => 'Táctico',
        'OPERATIVO' => 'Operativo'
    ];

    // =========================
    // Estadísticas
    // =========================
    $stats = [
        'total' => Puesto::where('estado', true)->count(),
        'con_perfil' => Puesto::where('estado', true)
            ->has('perfilRequisitos')
            ->count(),
        'sin_perfil' => Puesto::where('estado', true)
            ->doesntHave('perfilRequisitos')
            ->count(),
        'por_unidad' => Puesto::where('estado', true)
            ->select('idUnidadOrganizacional', DB::raw('count(*) as total'))
            ->groupBy('idUnidadOrganizacional')
            ->with('unidadOrganizacional')
            ->get()
    ];

    return view('perfiles-puesto.index', compact(
        'puestos',
        'unidades',
        'nivelesJerarquicos',
        'stats',
        'search',
        'unidadId',
        'nivelJerarquico',
        'tienePerfil'
    ));
}

    /**
     * Mostrar el formulario para definir/editar el perfil de un puesto
     */
public function edit($id_puesto)
{
    $puesto = Puesto::with(['perfilRequisitos', 'unidadOrganizacional'])
                    ->findOrFail($id_puesto);
    
    // Datos para los selects del formulario
    $nivelesAcademicos = NivelAcademico::where('estado', true)
        ->where('esTituloUniversitario', true)
        ->orderBy('orden')
        ->get();
    
    $areasConocimiento = AreaConocimiento::where('estado', true)
        ->orderBy('nombre')
        ->get();
    
    $carreras = Carrera::with(['areaConocimiento', 'nivelAcademico'])
        ->where('estado', true)
        ->orderBy('nombre')
        ->get();
    
    // Obtener el perfil actual (primer elemento de la colección)
    $perfilActual = $puesto->perfilRequisitos->first(); // <-- important: first()
    
    $areasSeleccionadas = [];
    $carrerasSeleccionadas = [];
    
    if ($perfilActual) { // now $perfilActual is either a model or null
        // Obtener IDs de áreas seleccionadas (basado en nombres)
        if ($perfilActual->areasConocimientoPermitidas) {
            $areasSeleccionadas = AreaConocimiento::whereIn('nombre', $perfilActual->areasConocimientoPermitidas)
                ->pluck('id')
                ->toArray();
        }
        $carrerasSeleccionadas = $perfilActual->carrerasEspecificas ?? [];
    }
    
    return view('perfiles-puesto.edit', compact(
        'puesto', 
        'nivelesAcademicos', 
        'areasConocimiento', 
        'carreras',
        'perfilActual',
        'areasSeleccionadas',
        'carrerasSeleccionadas'
    ));
}

    /**
     * Guardar o actualizar el perfil del puesto
     */
    public function storeOrUpdate(Request $request, $idPuesto)
    {
        $validator = Validator::make($request->all(), [
            'aniosExperienciaMinimos' => 'nullable|integer|min:0|max:50',
            'nivelAcademicoRequerido' => 'nullable|string|max:100',
            'areasConocimientoPermitidas' => 'nullable|array',
            'areasConocimientoPermitidas.*' => 'exists:areas_conocimiento,id',
            'carrerasEspecificas' => 'nullable|array',
            'carrerasEspecificas.*' => 'exists:carreras,id',
            'requiereTituloEnProvisionNacional' => 'boolean',
            'observacion' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $puesto = Puesto::findOrFail($idPuesto);
            
            // Preparar datos
            $data = [
                'idPuesto' => $puesto->id,
                'aniosExperienciaMinimos' => $request->aniosExperienciaMinimos,
                'nivelAcademicoRequerido' => $request->nivelAcademicoRequerido,
                'requiereTituloEnProvisionNacional' => $request->requiereTituloEnProvisionNacional ?? false,
                'observacion' => $request->observacion
            ];
            
            // Convertir IDs de áreas a nombres para almacenamiento
            if ($request->has('areasConocimientoPermitidas') && is_array($request->areasConocimientoPermitidas)) {
                $areasNombres = AreaConocimiento::whereIn('id', $request->areasConocimientoPermitidas)
                    ->pluck('nombre')
                    ->toArray();
                $data['areasConocimientoPermitidas'] = $areasNombres;
            } else {
                $data['areasConocimientoPermitidas'] = null;
            }
            
            // Guardar IDs de carreras específicas
            if ($request->has('carrerasEspecificas') && is_array($request->carrerasEspecificas)) {
                $data['carrerasEspecificas'] = $request->carrerasEspecificas;
            } else {
                $data['carrerasEspecificas'] = null;
            }
            
            // Actualizar o crear
            $perfil = PerfilPuesto::updateOrCreate(
                ['idPuesto' => $puesto->id],
                $data
            );
            
            DB::commit();
            
            return redirect()->route('perfil-puesto.index')
                ->with('success', 'Perfil del puesto definido correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al guardar el perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar el detalle del perfil del puesto
     */
// En tu controlador (ej. PuestoController.php o PerfilPuestoController.php)

public function show($id_puesto)
{
    $puesto = Puesto::with(['perfilRequisitos', 'unidadOrganizacional'])->findOrFail($id_puesto);
    
    $perfilActual = $puesto->perfilRequisitos->first();
    $tienePerfil = $perfilActual !== null;
    
    // Preparar datos procesados para la vista
    $areasPermitidas = collect();
    $carrerasEspecificas = collect();
    
    if ($tienePerfil) {
        // Obtener áreas de conocimiento permitidas
        if (!empty($perfilActual->areasConocimientoPermitidas) && is_array($perfilActual->areasConocimientoPermitidas)) {
            $areasPermitidas = AreaConocimiento::whereIn('nombre', $perfilActual->areasConocimientoPermitidas)->get();
        }
        
        // Obtener carreras específicas
        if (!empty($perfilActual->carrerasEspecificas) && is_array($perfilActual->carrerasEspecificas)) {
            $carrerasEspecificas = Carrera::with(['areaConocimiento', 'nivelAcademico'])
                ->whereIn('id', $perfilActual->carrerasEspecificas)
                ->get();
        }
    }
    
    return view('perfiles-puesto.show', compact(
        'puesto', 
        'perfilActual', 
        'tienePerfil', 
        'areasPermitidas', 
        'carrerasEspecificas'
    ));
}
    /**
     * Eliminar el perfil del puesto
     */
    public function destroy($idPuesto)
    {
        try {
            $puesto = Puesto::findOrFail($idPuesto);
            
            if ($puesto->perfilRequisitos) {
                $puesto->perfilRequisitos->delete();
                return redirect()->route('perfil-puesto.index')
                    ->with('success', 'Perfil del puesto eliminado correctamente');
            }
            
            return redirect()->route('perfil-puesto.index')
                ->with('info', 'El puesto no tiene un perfil definido');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el perfil: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar el detalle del perfil del puesto
     */


    
}