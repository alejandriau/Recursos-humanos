<?php

namespace App\Http\Controllers;

use App\Models\UnidadOrganizacional;
use App\Models\Puesto;
use Illuminate\Http\Request;

class UnidadOrganizacionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = UnidadOrganizacional::with(['padre', 'jefe', 'hijos.jefe']);

            // Filtro de estado
            if ($request->has('activo') && $request->activo !== '') {
                $query->where('esActivo', $request->boolean('activo'));
            }

            // Filtro de tipo - con validación
            if ($request->filled('tipo')) {
                $tiposValidos = ['SECRETARIA', 'SERVICIO', 'DIRECCION', 'UNIDAD', 'AREA', 'PROGRAMA', 'PROYECTO'];
                if (in_array($request->tipo, $tiposValidos)) {
                    $query->where('tipo', $request->tipo);
                }
            }

            // Filtro de búsqueda
            if ($request->filled('buscar')) {
                $buscar = trim($request->buscar);
                $query->where(function($q) use ($buscar) {
                    $q->where('denominacion', 'LIKE', "%{$buscar}%")
                    ->orWhere('codigo', 'LIKE', "%{$buscar}%")
                    ->orWhere('sigla', 'LIKE', "%{$buscar}%");
                });
            }

            // Debug (opcional - quitar en producción)
            // \Log::debug('SQL: ' . $query->toSql());
            // \Log::debug('Bindings: ' . json_encode($query->getBindings()));

            // Ordenamiento
            $orden = $request->get('orden', 'denominacion');
            $direccion = $request->get('direccion', 'asc');

            $ordenesPermitidos = ['denominacion', 'codigo', 'sigla', 'tipo', 'created_at'];
            if (in_array($orden, $ordenesPermitidos)) {
                $query->orderBy($orden, $direccion);
            } else {
                $query->orderBy('denominacion', 'asc');
            }

            $unidades = $query->paginate($request->get('por_pagina', 15));

            // Estadísticas para la vista
            $estadisticas = [
                'activas' => UnidadOrganizacional::where('esActivo', true)->count(),
                'inactivas' => UnidadOrganizacional::where('esActivo', false)->count(),
                'con_jefatura' => UnidadOrganizacional::whereHas('jefe')->count(),
            ];

            return view('admin.unidades.index', compact('unidades', 'estadisticas'));

        } catch (\Exception $e) {
            \Log::error('Error en UnidadOrganizacionalController@index: ' . $e->getMessage());
            return redirect()->route('unidades.index')
                            ->with('error', 'Error al obtener unidades organizacionales: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidadesPadre = UnidadOrganizacional::where('esActivo', true)
                        ->get();

        return view('admin.unidades.create', compact('unidadesPadre'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'denominacion' => 'required|string|max:800',
                'codigo' => 'nullable|string|max:45|unique:unidad_organizacionals,codigo',
                'sigla' => 'nullable|string|max:20',
                'tipo' => 'required|in:SECRETARIA,SERVICIO,DIRECCION,UNIDAD,AREA,PROGRAMA,PROYECTO',
                'idPadre' => 'nullable|exists:unidad_organizacionals,id',
                'esActivo' => 'boolean'
            ]);

            $unidad = UnidadOrganizacional::create($validated);

            return redirect()->route('unidades.index')
                             ->with('success', 'Unidad organizacional creada correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al crear unidad organizacional: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $unidad = UnidadOrganizacional::with([
                'padre',
                'jefe',
                'hijos.jefe',
                'puestos' => function($query) {
                    $query->with('personaActual');
                }
            ])->findOrFail($id);

            return view('admin.unidades.show', compact('unidad'));

        } catch (\Exception $e) {
            return redirect()->route('admin.unidades.index')
                             ->with('error', 'Unidad organizacional no encontrada');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $unidad = UnidadOrganizacional::findOrFail($id);
            $unidadesPadre = UnidadOrganizacional::where('esActivo', true)
                            ->where('id', '!=', $unidad->id)
                            ->whereNull('idPadre')
                            ->get();

            return view('admin.unidades.edit', compact('unidad', 'unidadesPadre'));

        } catch (\Exception $e) {
            return redirect()->route('admin.unidades.index')
                             ->with('error', 'Unidad organizacional no encontrada');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $unidad = UnidadOrganizacional::findOrFail($id);

            $validated = $request->validate([
                'denominacion' => 'required|string|max:800',
                'codigo' => 'nullable|string|max:45|unique:unidad_organizacionals,codigo,' . $id,
                'sigla' => 'nullable|string|max:20',
                'tipo' => 'required|in:SECRETARIA,SERVICIO,DIRECCION,UNIDAD,AREA,PROGRAMA,PROYECTO',
                'idPadre' => 'nullable|exists:unidad_organizacionals,id',
                'esActivo' => 'boolean'
            ]);

            $unidad->update($validated);

            return redirect()->route('admin.unidades.show', $unidad)
                             ->with('success', 'Unidad organizacional actualizada correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al actualizar unidad organizacional: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $unidad = UnidadOrganizacional::findOrFail($id);

            // Verificar si tiene puestos activos
            if ($unidad->puestos()->exists()) {
                return redirect()->back()
                                 ->with('error', 'No se puede eliminar la unidad porque tiene puestos asignados');
            }

            // Verificar si tiene subunidades
            if ($unidad->hijos()->exists()) {
                return redirect()->back()
                                 ->with('error', 'No se puede eliminar la unidad porque tiene subunidades');
            }

            $unidad->delete();

            return redirect()->route('admin.unidades.index')
                             ->with('success', 'Unidad organizacional eliminada correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al eliminar unidad organizacional: ' . $e->getMessage());
        }
    }

    /**
     * Obtener árbol organizacional completo
     */
    public function arbolOrganizacional(Request $request)
    {
        try {
            $unidadSeleccionada = null;
            $raices = collect();
            $filtroUnidad = $request->get('unidad_id');

            // Obtener todas las unidades para el dropdown
            $todasUnidades = UnidadOrganizacional::where('esActivo', true)
                ->orderBy('denominacion')
                ->get();

            if ($filtroUnidad) {
                // Buscar la unidad específica seleccionada
                $unidadSeleccionada = UnidadOrganizacional::with([
                    'hijos.jefe.personaActual',
                    'hijos.hijos.jefe.personaActual',
                    'hijos.hijos.hijos.jefe.personaActual',
                    'jefe.personaActual'
                ])->find($filtroUnidad);

                if ($unidadSeleccionada) {
                    // Si la unidad tiene hijos, mostrarlos como raíces
                    if ($unidadSeleccionada->hijos->count() > 0) {
                        $raices = $unidadSeleccionada->hijos;
                    } else {
                        // Si no tiene hijos, mostrar solo la unidad seleccionada
                        $raices = collect([$unidadSeleccionada]);
                    }
                }
            } else {
                // Mostrar todas las unidades raíz
                $raices = UnidadOrganizacional::with([
                    'hijos.jefe.personaActual',
                    'hijos.hijos.jefe.personaActual',
                    'hijos.hijos.hijos.jefe.personaActual',
                    'jefe.personaActual'
                ])->whereNull('idPadre')
                  ->where('esActivo', true)
                  ->orderBy('denominacion')
                  ->get();
            }

            // Preparar datos para D3.js
            $treeData = $this->buildTreeData($raices, $filtroUnidad, $unidadSeleccionada);

            return view('admin.unidades.arbol', compact(
                'raices',
                'todasUnidades',
                'filtroUnidad',
                'unidadSeleccionada',
                'treeData'
            ));

        } catch (\Exception $e) {
            return redirect()->route('unidades.index')
                             ->with('error', 'Error al obtener árbol organizacional: ' . $e->getMessage());
        }
    }

    private function buildTreeData($raices, $filtroUnidad, $unidadSeleccionada)
    {
        // Si hay filtro y la unidad seleccionada no tiene hijos, mostrarla como raíz
        if ($filtroUnidad && $unidadSeleccionada && $unidadSeleccionada->hijos->count() === 0) {
            $rootNode = $this->formatNode($unidadSeleccionada);
            $rootNode['children'] = [];
            return $rootNode;
        }

        // Para múltiples raíces, crear un nodo raíz virtual
        if ($raices->count() > 1 || ($filtroUnidad && $unidadSeleccionada)) {
            $rootNode = [
                'name' => $filtroUnidad && $unidadSeleccionada ? $unidadSeleccionada->denominacion : 'Organigrama General',
                'title' => 'RAIZ',
                'type' => 'ROOT',
                'children' => []
            ];

            foreach ($raices as $raiz) {
                $rootNode['children'][] = $this->buildNodeRecursively($raiz);
            }

            return $rootNode;
        }

        // Para una sola raíz
        if ($raices->count() === 1) {
            return $this->buildNodeRecursively($raices->first());
        }

        return ['name' => 'No hay datos', 'children' => []];
    }

    private function buildNodeRecursively($unidad)
    {
        $node = $this->formatNode($unidad);

        if ($unidad->hijos->count() > 0) {
            $node['children'] = [];
            foreach ($unidad->hijos as $hijo) {
                $node['children'][] = $this->buildNodeRecursively($hijo);
            }
        }

        return $node;
    }

    private function formatNode($unidad)
    {
        $jefeNombre = 'Sin jefe asignado';
        $jefePuesto = '';

        if ($unidad->jefe && $unidad->jefe->personaActual) {
            $jefeNombre = $unidad->jefe->personaActual->nombre . ' ' .
                         $unidad->jefe->personaActual->apellidoPat . ' ' .
                         ($unidad->jefe->personaActual->apellidoMat ?? '');
            $jefePuesto = $unidad->jefe->denominacion;
        }

        return [
            'id' => $unidad->id,
            'name' => $unidad->denominacion,
            'title' => $unidad->tipo,
            'type' => $unidad->tipo,
            'codigo' => $unidad->codigo,
            'jefe' => $jefeNombre,
            'puestoJefe' => $jefePuesto,
            'subunidades' => $unidad->hijos->count(),
            'totalPuestos' => $unidad->puestos->count(),
            'esActivo' => $unidad->esActivo,
            'color' => $this->getColorByType($unidad->tipo)
        ];
    }

    private function getColorByType($tipo)
    {
        $colores = [
            'DIRECCION' => '#2563eb',
            'SECRETARIA' => '#7c3aed',
            'SERVICIO' => '#4f46e5',
            'GERENCIA' => '#059669',
            'UNIDAD' => '#10b981',
            'AREA' => '#d97706',
            'DEPARTAMENTO' => '#dc2626',
            'COORDINACION' => '#db2777',
            'default' => '#6b7280'
        ];

        return $colores[$tipo] ?? $colores['default'];
    }
    /**
     * Obtener estructura completa de una unidad con todas sus subunidades
     */
    public function estructuraCompleta(string $id)
    {
        try {
            $unidad = UnidadOrganizacional::findOrFail($id);
            $estructura = $unidad->obtenerArbolCompleto();

            return view('admin.unidades.estructura', compact('unidad', 'estructura'));

        } catch (\Exception $e) {
            return redirect()->route('admin.unidades.index')
                             ->with('error', 'Error al obtener estructura completa: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de la unidad
     */
    public function estadisticas(string $id)
    {
        try {
            $unidad = UnidadOrganizacional::findOrFail($id);

            $estadisticas = [
                'total_puestos' => $unidad->contarPersonalTotal(),
                'presupuesto_total' => $unidad->obtenerPresupuestoTotal(),
                'puestos_vacantes' => $unidad->puestos()->vacantes()->count(),
                'puestos_ocupados' => $unidad->puestos()->ocupados()->count(),
                'total_subunidades' => $unidad->obtenerTodasLasSubunidades()->count() - 1, // Excluye la unidad principal
                'jefaturas' => $unidad->puestos()->jefaturas()->count()
            ];

            return view('admin.unidades.estadisticas', compact('unidad', 'estadisticas'));

        } catch (\Exception $e) {
            return redirect()->route('admin.unidades.show', $id)
                             ->with('error', 'Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Desactivar unidad
     */
    public function desactivar(string $id)
    {
        try {
            $unidad = UnidadOrganizacional::findOrFail($id);
            $unidad->desactivar();

            return redirect()->back()
                             ->with('success', 'Unidad desactivada correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al desactivar unidad: ' . $e->getMessage());
        }
    }

    /**
     * Reactivar unidad
     */
    public function reactivar(string $id)
    {
        try {
            $unidad = UnidadOrganizacional::findOrFail($id);
            $unidad->reactivar();

            return redirect()->back()
                             ->with('success', 'Unidad reactivada correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al reactivar unidad: ' . $e->getMessage());
        }
    }
}
