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

            // Filtros
            if ($request->has('activo')) {
                $query->where('esActivo', $request->boolean('activo'));
            }

            if ($request->has('jefatura')) {
                $query->where('esJefatura', $request->boolean('jefatura'));
            }

            if ($request->has('tipo_contrato')) {
                $query->where('tipoContrato', $request->tipo_contrato);
            }

            if ($request->has('nivel_jerarquico')) {
                $query->where('nivelJerarquico', $request->nivel_jerarquico);
            }

            if ($request->has('nivel')) {
                $query->where('nivel', $request->nivel);
            }

            if ($request->has('id_unidad')) {
                $query->where('idUnidadOrganizacional', $request->id_unidad);
            }

            if ($request->has('buscar')) {
                $query->where('denominacion', 'LIKE', "%{$request->buscar}%")
                      ->orWhere('item', 'LIKE', "%{$request->buscar}%");
            }

            // Ordenamiento
            $orden = $request->get('orden', 'denominacion');
            $direccion = $request->get('direccion', 'asc');
            $query->orderBy($orden, $direccion);

            $puestos = $query->paginate($request->get('por_pagina', 15));

            // Estadísticas para la vista
            $estadisticas = [
                'activos' => Puesto::where('esActivo', true)->count(),
                'inactivos' => Puesto::where('esActivo', false)->count(),
                'vacantes' => Puesto::where('esActivo', true)->count(), // Por ahora, todos se consideran vacantes
                'jefaturas' => Puesto::where('esActivo', true)->where('esJefatura', true)->count(),
            ];

            return view('admin.puestos.index', compact('puestos', 'estadisticas'));

        } catch (\Exception $e) {
            return redirect()->route('admin.puestos.index')
                             ->with('error', 'Error al obtener puestos: ' . $e->getMessage());
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
                'nivelJerarquico' => 'required|in:GOBERNADOR (A),SECRETARIA (O) DEPARTAMENTAL,ASESORA (OR) / DIRECTORA (OR) / DIR. SERV. DPTAL.,JEFA (E) DE UNIDAD,PROFESIONAL I,PROFESIONAL II,ADMINISTRATIVO I,ADMINISTRATIVO II,APOYO ADMINISTRATIVO I,APOYO ADMINISTRATIVO II,ASISTENTE',
                'item' => 'nullable|string|max:45|unique:puestos,item',
                'manual' => 'nullable|string|max:500',
                'perfil' => 'nullable|string',
                'experencia' => 'nullable|string',
                'nivel' => 'nullable|integer',
                'haber' => 'nullable|numeric|min:0',
                'tipoContrato' => 'required|in:PERMANENTE,EVENTUAL',
                'idUnidadOrganizacional' => 'required|exists:unidad_organizacionals,id',
                'esJefatura' => 'boolean'
            ]);

            $puesto = Puesto::create($validated);

            // Si es jefatura, asignar automáticamente
            if ($puesto->esJefatura) {
                $this->asignarJefatura($puesto->id);
            }

            return redirect()->route('admin.puestos.show', $puesto)
                             ->with('success', 'Puesto creado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al crear puesto: ' . $e->getMessage())
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
            $unidades = UnidadOrganizacional::where('esActivo', true)->get();

            return view('admin.puestos.edit', compact('puesto', 'unidades'));

        } catch (\Exception $e) {
            return redirect()->route('admin.puestos.index')
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
                'nivelJerarquico' => 'required|in:GOBERNADOR (A),SECRETARIA (O) DEPARTAMENTAL,ASESORA (OR) / DIRECTORA (OR) / DIR. SERV. DPTAL.,JEFA (E) DE UNIDAD,PROFESIONAL I,PROFESIONAL II,ADMINISTRATIVO I,ADMINISTRATIVO II,APOYO ADMINISTRATIVO I,APOYO ADMINISTRATIVO II,ASISTENTE',
                'item' => 'nullable|string|max:45|unique:puestos,item,' . $id,
                'manual' => 'nullable|string|max:500',
                'perfil' => 'nullable|string',
                'experencia' => 'nullable|string',
                'nivel' => 'nullable|integer',
                'haber' => 'nullable|numeric|min:0',
                'tipoContrato' => 'required|in:PERMANENTE,EVENTUAL',
                'idUnidadOrganizacional' => 'required|exists:unidad_organizacionals,id',
                'esJefatura' => 'boolean'
            ]);

            $puesto->update($validated);

            // Si se marcó como jefatura, asignar automáticamente
            if ($request->has('esJefatura') && $puesto->esJefatura) {
                $this->asignarJefatura($puesto->id);
            }

            return redirect()->route('admin.puestos.show', $puesto)
                             ->with('success', 'Puesto actualizado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al actualizar puesto: ' . $e->getMessage())
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

            $puestos = $query->paginate(15);

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
