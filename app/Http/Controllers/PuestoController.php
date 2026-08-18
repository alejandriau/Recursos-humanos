<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use App\Models\UnidadOrganizacional;
use Illuminate\Http\Request;

class PuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        try {
            $query = Puesto::with(['unidadOrganizacional.padre']);

            // Por defecto, mostrar solo activos a menos que se especifique lo contrario
            if ($request->has('estado') && $request->estado !== '') {
                // Si el usuario selecciona un estado, aplicar ese filtro
                if ($request->estado == 'activo') {
                    $query->where('esActivo', true);
                } elseif ($request->estado == 'inactivo') {
                    $query->where('esActivo', false);
                }
                // si es 'todos', no aplicar filtro de estado
            } else {
                // Por defecto: solo activos
                $query->where('esActivo', true);
            }

            // Filtros básicos (buscar)
            if ($request->filled('buscar')) {
                $search = $request->buscar;
                $query->where(function($q) use ($search) {
                    $q->where('denominacion', 'LIKE', "%{$search}%")
                    ->orWhere('item', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('nivel_jerarquico')) {
                $query->where('nivelJerarquico', $request->nivel_jerarquico);
            }

            if ($request->filled('tipo_contrato')) {
                $query->where('tipoContrato', $request->tipo_contrato);
            }

            // Filtro de categoría (nuevo)
            if ($request->filled('categoria')) {
                $query->where('categoria', $request->categoria);
            }

            // Filtros de nivel salarial (desde-hasta)
            if ($request->filled('nivel_salarial_desde')) {
                $query->where('nivel_salarial', '>=', $request->nivel_salarial_desde);
            }
            if ($request->filled('nivel_salarial_hasta')) {
                $query->where('nivel_salarial', '<=', $request->nivel_salarial_hasta);
            }

            // Eliminamos filtros de nivel_clase (desde/hasta) - ya no se aplican

            $query->orderByRaw('CAST(item AS UNSIGNED) ASC');

            $puestos = $query->paginate(100)->appends($request->all());

            $nivelesJerarquicos = [
                'GOBERNADOR (A)',
                'SECRETARIA (O) DEPARTAMENTAL',
                'ASESORA (OR) / DIRECTORA (OR) / DIR. SERV. DPTAL.',
                'JEFA (E) DE UNIDAD',
                'PROFESIONAL I',
                'PROFESIONAL II',
                'ADMINISTRATIVO I',
                'ADMINISTRATIVO II',
                'APOYO ADMINISTRATIVO I',
                'APOYO ADMINISTRATIVO II',
                'APOYO ADMINISTRATIVO',
                'ASISTENTE'
            ];

            $estadisticas = [
                'total' => Puesto::count(),
                'activos' => Puesto::where('esActivo', true)->count(),
                'inactivos' => Puesto::where('esActivo', false)->count(),
                'jefaturas' => Puesto::where('esActivo', true)->where('esJefatura', true)->count(),
                'vacantes' => Puesto::where('esActivo', true)->count(), // o la lógica que tengas
            ];

            return view('admin.puestos.index', compact('puestos', 'estadisticas', 'nivelesJerarquicos'));

        } catch (\Exception $e) {
            return redirect()->route('puestos.index')
                            ->with('error', 'Error al cargar los puestos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidades = UnidadOrganizacional::where('esActivo', true)->get();

        return view('admin.puestos.create', compact('unidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'denominacion' => 'required|string|max:800',
                'descripcion_puesto' => 'nullable|string',
                'nivelJerarquico' => 'required|string|max:255',
                'categoria' => 'nullable|in:SUPERIOR,EJECUTIVO,OPERATIVO',
                'nivel_clase' => 'nullable|integer|min:1',
                'nivel_salarial' => 'nullable|integer|min:1',
                'item' => 'nullable|string|max:45',
                'idUnidadOrganizacional' => 'required|exists:unidad_organizacionals,id',
                'tipoContrato' => 'required|in:PERMANENTE,EVENTUAL',
                'haber' => 'nullable|numeric|min:0',
                'manual' => 'nullable|string|max:500',
                'esJefatura' => 'sometimes|boolean',
                'perfil' => 'nullable|string',
                'experencia' => 'nullable|string',
            ]);

            // Asegurar esJefatura
            $validated['esJefatura'] = $request->has('esJefatura');

            // 🔍 Depuración: ver los datos que se van a guardar
            // dd($validated); // Descomenta para probar

            $puesto = Puesto::create($validated);

            // Si es jefatura, asignar automáticamente
            if ($puesto->esJefatura) {
                $this->asignarJefatura($puesto->id);
            }

            // Cambia a una ruta que sepas que existe para probar
            return redirect()->route('puestos.index')
                            ->with('success', 'Puesto creado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->errors())
                            ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura específica de errores de BD
            return redirect()->back()
                            ->with('error', 'Error de base de datos: ' . $e->getMessage())
                            ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Error general: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $puesto = Puesto::with([
                'unidadOrganizacional.padre'
            ])->findOrFail($id);

            return view('admin.puestos.show', compact('puesto'));

        } catch (\Exception $e) {
            return redirect()->route('admin.puestos.index')
                             ->with('error', 'Puesto no encontrado');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit(string $id)
{
    try {
        $puesto = Puesto::findOrFail($id);

        // Obtener unidades activas
        $unidades = UnidadOrganizacional::where('esActivo', true)->get();

        // Si la unidad del puesto no está en la lista (porque está inactiva), la agregamos
        if ($puesto->idUnidadOrganizacional) {
            $unidadPuesto = UnidadOrganizacional::find($puesto->idUnidadOrganizacional);
            if ($unidadPuesto && !$unidades->contains('id', $puesto->idUnidadOrganizacional)) {
                $unidades->push($unidadPuesto);
            }
        }

        // Obtener niveles jerárquicos únicos
        $nivelesJerarquicos = Puesto::select('nivelJerarquico')
            ->distinct()
            ->whereNotNull('nivelJerarquico')
            ->pluck('nivelJerarquico')
            ->toArray();
        sort($nivelesJerarquicos);

        return view('admin.puestos.edit', compact('puesto', 'unidades', 'nivelesJerarquicos'));

    } catch (\Exception $e) {
        return redirect()->route('puestos.index')
                         ->with('error', 'Puesto no encontrado');
    }
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $puesto = Puesto::findOrFail($id);

            $validated = $request->validate([
                'denominacion' => 'required|string|max:800',
                'descripcion_puesto' => 'nullable|string',
                'nivelJerarquico' => 'required|string|max:255',
                'categoria' => 'nullable|in:SUPERIOR,EJECUTIVO,OPERATIVO',
                'nivel_clase' => 'nullable|integer|min:1',
                'nivel_salarial' => 'nullable|integer|min:1',
                'item' => 'nullable|string|max:45',
                'idUnidadOrganizacional' => 'required|exists:unidad_organizacionals,id',
                'tipoContrato' => 'required|in:PERMANENTE,EVENTUAL',
                'haber' => 'nullable|numeric|min:0',
                'manual' => 'nullable|string|max:500',
                'esJefatura' => 'sometimes|boolean',
                'perfil' => 'nullable|string',
                'experencia' => 'nullable|string',
            ]);

            // Asegurar el valor booleano de esJefatura
            $validated['esJefatura'] = $request->has('esJefatura');

            // Actualizar
            $puesto->update($validated);

            // Si es jefatura, asignar
            if ($puesto->esJefatura) {
                $this->asignarJefatura($puesto->id);
            }

            // Redirigir a una ruta que exista (cambiar si es necesario)
            return redirect()->route('puestos.index', $puesto)
                            ->with('success', 'Puesto actualizado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->errors())
                            ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                            ->with('error', 'Error de base de datos: ' . $e->getMessage())
                            ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Error general: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $puesto = Puesto::findOrFail($id);

            $puesto->delete();

            return redirect()->route('admin.puestos.index')
                             ->with('success', 'Puesto eliminado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al eliminar puesto: ' . $e->getMessage());
        }
    }

    /**
     * Asignar jefatura a un puesto
     */
    public function asignarJefatura(string $id)
    {
        try {
            $puesto = Puesto::findOrFail($id);

            // Quitar jefatura anterior de la unidad
            Puesto::where('idUnidadOrganizacional', $puesto->idUnidadOrganizacional)
                  ->where('esJefatura', true)
                  ->where('id', '!=', $puesto->id)
                  ->update(['esJefatura' => false]);

            // Asignar nueva jefatura
            $puesto->update(['esJefatura' => true]);

            return redirect()->back()
                             ->with('success', 'Jefatura asignada correctamente al puesto');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al asignar jefatura: ' . $e->getMessage());
        }
    }

    /**
     * Quitar jefatura de un puesto
     */
    public function quitarJefatura(string $id)
    {
        try {
            $puesto = Puesto::findOrFail($id);
            $puesto->update(['esJefatura' => false]);

            return redirect()->back()
                             ->with('success', 'Jefatura quitada correctamente del puesto');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al quitar jefatura: ' . $e->getMessage());
        }
    }

    /**
     * Obtener puestos vacantes
     */
    public function vacantes(Request $request)
    {
        try {
            $query = Puesto::with(['unidadOrganizacional.padre'])
                        ->where('esActivo', true);

            if ($request->has('id_unidad')) {
                $query->where('idUnidadOrganizacional', $request->id_unidad);
            }

            if ($request->has('buscar')) {
                $query->where('denominacion', 'LIKE', "%{$request->buscar}%");
            }

            $puestos = $query->paginate(100); // Ya tienes la paginación aquí

            return view('admin.puestos.vacantes', compact('puestos'));

        } catch (\Exception $e) {
            return redirect()->route('admin.puestos.index')
                            ->with('error', 'Error al obtener puestos vacantes: ' . $e->getMessage());
        }
    }

    /**
     * Obtener jefaturas
     */
    public function jefaturas(Request $request)
    {
        try {
            $query = Puesto::with(['unidadOrganizacional.padre'])
                          ->where('esActivo', true)
                          ->where('esJefatura', true);

            if ($request->has('id_unidad')) {
                $query->where('idUnidadOrganizacional', $request->id_unidad);
            }

            $jefaturas = $query->get();

            return view('admin.puestos.jefaturas', compact('jefaturas'));

        } catch (\Exception $e) {
            return redirect()->route('admin.puestos.index')
                             ->with('error', 'Error al obtener jefaturas: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de puestos
     */
    public function estadisticas()
    {
        try {
            $estadisticas = [
                'total_puestos' => Puesto::where('esActivo', true)->count(),
                'puestos_vacantes' => Puesto::where('esActivo', true)->count(), // Por ahora todos son vacantes
                'puestos_ocupados' => 0, // Por ahora no hay ocupados
                'jefaturas' => Puesto::where('esActivo', true)->where('esJefatura', true)->count(),
                'jefaturas_vacantes' => Puesto::where('esActivo', true)
                                            ->where('esJefatura', true)
                                            ->count(),
                'por_tipo_contrato' => Puesto::where('esActivo', true)
                    ->selectRaw('tipoContrato, COUNT(*) as total')
                    ->groupBy('tipoContrato')
                    ->get(),
                'por_nivel_jerarquico' => Puesto::where('esActivo', true)
                    ->selectRaw('nivelJerarquico, COUNT(*) as total')
                    ->groupBy('nivelJerarquico')
                    ->get()
            ];

            return view('admin.puestos.estadisticas', compact('estadisticas'));

        } catch (\Exception $e) {
            return redirect()->route('admin.puestos.index')
                             ->with('error', 'Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Desactivar puesto
     */
    public function desactivar(string $id)
    {
        try {
            $puesto = Puesto::findOrFail($id);
            $puesto->update(['esActivo' => false]);

            return redirect()->back()
                             ->with('success', 'Puesto desactivado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al desactivar puesto: ' . $e->getMessage());
        }
    }

    /**
     * Reactivar puesto
     */
    public function reactivar(string $id)
    {
        try {
            $puesto = Puesto::findOrFail($id);
            $puesto->update(['esActivo' => true]);

            return redirect()->back()
                             ->with('success', 'Puesto reactivado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al reactivar puesto: ' . $e->getMessage());
        }
    }
}
