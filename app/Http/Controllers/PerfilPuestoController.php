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
            'perfilRequisitos.nivelAcademico',
            'perfilRequisitos.areaConocimiento',
            'perfilRequisitos.carrera'
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
        
        // Selects
        $nivelesAcademicos = NivelAcademico::where('estado', true)
            ->orderBy('orden')
            ->get();
        
        $areasConocimiento = AreaConocimiento::where('estado', true)
            ->orderBy('nombre')
            ->get();
        
        $carreras = Carrera::with(['areaConocimiento', 'nivelAcademico'])
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();
        
        // 🔥 PERFIL (UNO SOLO)
        $perfilActual = $puesto->perfilRequisitos;

        return view('perfiles-puesto.edit', compact(
            'puesto', 
            'nivelesAcademicos', 
            'areasConocimiento', 
            'carreras',
            'perfilActual'
        ));
    }

    /**
     * Guardar o actualizar el perfil del puesto
     */
    public function storeOrUpdate(Request $request, $idPuesto)
    {
        $data = $request->validate([
            'idNivelAcademico' => 'required|exists:niveles_academicos,id',
            'idAreaConocimiento' => 'nullable|exists:areas_conocimiento,id',
            'idCarrera' => 'nullable|exists:carreras,id',
            'aniosExperienciaMinimos' => 'nullable|integer|min:0|max:50',
            'requiereTituloEnProvisionNacional' => 'nullable|boolean',
            'conocimientoTexto' => 'nullable|string',
            'objetivo' => 'nullable|string',
            'observacion' => 'nullable|string'
        ]);

        // 🔥 CORRECCIÓN CLAVE
        $data['requiereTituloEnProvisionNacional'] = $request->has('requiereTituloEnProvisionNacional');

        $data['id_puesto'] = $idPuesto;

        PerfilPuesto::updateOrCreate(
            ['id_puesto' => $idPuesto],
            $data
        );

        return redirect()
            ->route('perfil-puesto.show', $idPuesto)
            ->with('success', 'Perfil guardado correctamente');
    }

    /**
     * Mostrar el detalle del perfil del puesto
     */


    
    public function show($id_puesto)
{
    $puesto = Puesto::with([
        'unidad.padre',
        'perfilRequisitos.nivelAcademico',
        'perfilRequisitos.areaConocimiento',
        'perfilRequisitos.carrera'
    ])->findOrFail($id_puesto);

    $perfilActual = $puesto->perfilRequisitos;

    // Si existe el perfil, obtén las áreas permitidas


    return view('perfiles-puesto.show', compact('puesto', 'perfilActual'));
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