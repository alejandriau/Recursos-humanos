<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Historial;
use App\Models\Puesto; // FALTABA ESTA IMPORTACIÓN
use App\Models\UnidadOrganizacional;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HistorialController extends Controller
{
    // Mostrar formulario de creación

    public function create(Request $request, $id)
{
    $niveles = $request->input('niveles');
    $personas = Persona::where('estado', 1)->get();

    // Verificar que el puesto existe y está activo
    $puesto = Puesto::where('id', $id)
                    ->where('estado', 1)
                    ->with('unidadOrganizacional.padre.padre.padre')
                    ->firstOrFail();

    return view('admin.historial.create', compact('personas', 'puesto', 'niveles'));
}

    // Búsqueda de personas para select2
    public function buscarPersonas(Request $request)
    {
        $term = $request->get('q');

        $personas = Persona::where('estado', 1)
            ->where(function($query) use ($term) {
                $query->where('nombre', 'LIKE', "%$term%")
                    ->orWhere('apellidoPat', 'LIKE', "%$term%")
                    ->orWhere('apellidoMat', 'LIKE', "%$term%")
                    ->orWhere('ci', 'LIKE', "%$term%")
                    ->orWhere(DB::raw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat)"), 'LIKE', "%$term%");
            })
            ->get();

        return response()->json($personas);
    }

    // Guardar nuevo historial
public function store(Request $request)
{
    $request->validate([
        'persona_id' => 'required|exists:persona,id', // Cambiado de 'persona' a 'personas'
        'puesto_id' => 'required|exists:puestos,id',   // Cambiado de 'puesto' a 'puestos'
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'tipo_movimiento' => 'required|in:designacion_inicial,movilidad,reasignacion,ascenso,comision,interinato,encargo_funciones,recontratacion',
        'tipo_contrato' => 'required|in:permanente,contrato_administrativo,contrato_plazo_fijo,contrato_obra,honorarios',
        'numero_memo' => 'nullable|string|max:100',
        'fecha_memo' => 'nullable|date',
        'archivo_memo' => 'nullable|file|mimes:pdf|max:2048',
        'salario' => 'nullable|numeric|min:0',
        'porcentaje_dedicacion' => 'nullable|integer|min:1|max:100',
        'fecha_vencimiento' => 'nullable|date|after:fecha_inicio',
        'motivo' => 'nullable|string|max:500',
        'observaciones' => 'nullable|string|max:1000'
    ]);

    DB::beginTransaction();

    try {
        $data = $request->except('archivo_memo');

        // Validación adicional para asegurar que el puesto y persona existen y están activos
        $puesto = Puesto::where('id', $request->puesto_id)
                       ->where('estado', 1)
                       ->first();

        if (!$puesto) {
            throw new \Exception('El puesto seleccionado no existe o no está activo.');
        }

        $persona = Persona::where('id', $request->persona_id)
                         ->where('estado', 1)
                         ->first();

        if (!$persona) {
            throw new \Exception('La persona seleccionada no existe o no está activa.');
        }

        // Manejar archivo PDF
        if ($request->hasFile('archivo_memo')) {
            $archivo = $request->file('archivo_memo');
            $nombreArchivo = 'memo_' . time() . '_' . $request->persona_id . '.' . $archivo->getClientOriginalExtension();
            $ruta = $archivo->storeAs('memos', $nombreArchivo, 'public');
            $data['archivo_memo'] = $ruta;
        }

        // Si es movilidad o ascenso, concluir el registro anterior
        if (in_array($request->tipo_movimiento, ['movilidad', 'ascenso', 'reasignacion'])) {
            $historialAnterior = Historial::where('persona_id', $request->persona_id)
                ->where('estado', 'activo')
                ->first();

            if ($historialAnterior) {
                $historialAnterior->marcarComoConcluido();
                $data['historial_anterior_id'] = $historialAnterior->id;
                $data['puesto_anterior_id'] = $historialAnterior->puesto_id;
            }
        }

        // Si es comisión o interinato, verificar puesto original
        if (in_array($request->tipo_movimiento, ['comision', 'interinato', 'encargo_funciones'])) {
            $puestoPrincipal = Historial::where('persona_id', $request->persona_id)
                ->where('estado', 'activo')
                ->where('conserva_puesto_original', false)
                ->first();

            if ($puestoPrincipal) {
                $data['conserva_puesto_original'] = true;
                $data['puesto_original_id'] = $puestoPrincipal->puesto_id;
            }
        }

        Historial::create($data);

        DB::commit();

        return redirect()->route('puesto')
            ->with('success', 'Designación registrada correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Error al registrar la designación: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show($id)
    {
        $historial = Historial::with(['puesto', 'persona', 'puestoOriginal', 'historialAnterior'])
            ->findOrFail($id);

        return view('admin.historial.show', compact('historial'));
    }

    public function edit($id)
    {
        $historial = Historial::with(['puesto', 'persona'])->find($id);

        if (!$historial) {
            return redirect()->route('historial')
                ->with('error', 'Registro no encontrado');
        }

        // Verificar que las relaciones existan
        if (!$historial->puesto) {
            return redirect()->route('historial')
                ->with('error', 'El puesto asociado no existe');
        }

        if (!$historial->persona) {
            return redirect()->route('historial')
                ->with('error', 'La persona asociada no existe');
        }

        return view('admin.historial.edit', compact('historial'));
    }

    // Desactivar puesto
    public function desactivar($id)
    {
        $puesto = Puesto::findOrFail($id);
        $puesto->estado = 0;
        $puesto->save();

        return redirect()->back()->with('success', 'Puesto desactivado correctamente.');
    }

    // Lista principal con filtros
public function index(Request $request)
{
    $search = $request->input('search');
    $tipoMovimiento = $request->input('tipo_movimiento');
    $estado = $request->input('estado');
    $tipoContrato = $request->input('tipo_contrato');

    $puestos = Puesto::where('estado', 1)
        ->where(function ($query) use ($search, $tipoMovimiento, $estado, $tipoContrato) {
            if ($search) {
                $query->where('item', 'like', "%$search%")
                      ->orWhere('nivelJerarquico', 'like', "%$search%")
                      ->orWhere('denominacion', 'like', "%$search%")
                      ->orWhereHas('historial', function ($historialQuery) use ($search, $tipoMovimiento, $estado, $tipoContrato) {
                          $historialQuery->whereNull('fecha_fin')
                              ->when($tipoMovimiento, function ($q) use ($tipoMovimiento) {
                                  $q->where('tipo_movimiento', $tipoMovimiento);
                              })
                              ->when($estado, function ($q) use ($estado) {
                                  $q->where('estado', $estado);
                              })
                              ->when($tipoContrato, function ($q) use ($tipoContrato) {
                                  $q->where('tipo_contrato', $tipoContrato);
                              })
                              ->whereHas('persona', function ($personaQuery) use ($search) {
                                  $personaQuery->where('estado', 1)
                                      ->where(function ($subquery) use ($search) {
                                          $subquery->where('nombre', 'like', '%' . $search . '%')
                                              ->orWhere('apellidoPat', 'like', '%' . $search . '%')
                                              ->orWhere('apellidoMat', 'like', '%' . $search . '%')
                                              ->orWhere(DB::raw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat)"), 'like', '%' . $search . '%');
                                      });
                              });
                      });
            }
        })
        ->with([
            'unidadOrganizacional.padre.padre.padre',
            'historial' => function ($query) use ($tipoMovimiento, $estado, $tipoContrato) {
                $query->whereNull('fecha_fin')
                      ->when($tipoMovimiento, function ($q) use ($tipoMovimiento) {
                          $q->where('tipo_movimiento', $tipoMovimiento);
                      })
                      ->when($estado, function ($q) use ($estado) {
                          $q->where('estado', $estado);
                      })
                      ->when($tipoContrato, function ($q) use ($tipoContrato) {
                          $q->where('tipo_contrato', $tipoContrato);
                      })
                      ->with('persona')
                      ->orderBy('id', 'desc');
            }
        ])
        ->paginate(100); // Primero paginar

    // Luego mapear los resultados
    $puestos->getCollection()->transform(function ($puesto) {
        $historial = $puesto->historial->first();
        $puesto->persona = $historial?->persona;
        $puesto->historial_actual = $historial;
        return $puesto;
    });

    return view('admin.pasivos.bajas', compact('puestos', 'search', 'tipoMovimiento', 'estado', 'tipoContrato'));
}

    // Puestos vacíos
// Puestos vacíos
public function vacios(Request $request)
{
    $search = $request->input('search');

    $puestos = Puesto::where('estado', 1)
        ->whereDoesntHave('historial', function ($query) {
            $query->whereNull('fecha_fin')
                ->where('estado', 'activo')
                ->whereHas('persona', function ($q) {
                    $q->where('estado', 1);
                });
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item', 'like', "%$search%")
                  ->orWhere('nivelJerarquico', 'like', "%$search%")
                  ->orWhere('denominacion', 'like', "%$search%");
            });
        })
        ->with(['unidadOrganizacional.padre.padre.padre'])
        ->paginate(100); // Cambiado de get() a paginate(100)

    return view('admin.pasivos.bajas', compact('puestos', 'search'));
}

    // Concluir designación actual
    public function concluir($id)
    {
        DB::beginTransaction();

        try {
            $historial = Historial::findOrFail($id);

            if ($historial->estado !== 'activo') {
                throw new \Exception('Solo se pueden concluir designaciones activas');
            }

            $historial->marcarComoConcluido();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Designación concluida correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al concluir la designación: ' . $e->getMessage());
        }
    }

    // Historial completo de una persona
public function historial($id)
{
    $persona = Persona::with([
        'historial' => function($query) {
            $query->with([
                'puesto.unidadOrganizacional',
                'persona'
            ])->orderBy('fecha_inicio', 'desc');
        },
        'puestoActual.puesto.unidadOrganizacional'
    ])->findOrFail($id);

    $puestos = Puesto::where('estado', 1)->get();
    $unidades = UnidadOrganizacional::where('estado', 1)->get();

    return view('admin.personas.historial', compact('persona', 'puestos', 'unidades'));
}

    // Descargar memo PDF
    public function descargarMemo($id)
    {
        $historial = Historial::findOrFail($id);

        if (!$historial->archivo_memo || !Storage::disk('public')->exists($historial->archivo_memo)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($historial->archivo_memo);
    }

    // Ver designaciones activas de una persona
    public function designacionesActivas($personaId)
    {
        $designaciones = Historial::where('persona_id', $personaId)
            ->where('estado', 'activo')
            ->with(['puesto', 'puestoOriginal'])
            ->get();

        return response()->json($designaciones);
    }

    // Estadísticas y reportes
    public function estadisticas()
    {
        $stats = [
            'total_designaciones' => Historial::count(),
            'activos' => Historial::where('estado', 'activo')->count(),
            'por_tipo_movimiento' => Historial::select('tipo_movimiento', DB::raw('count(*) as total'))
                ->groupBy('tipo_movimiento')
                ->get(),
            'por_tipo_contrato' => Historial::select('tipo_contrato', DB::raw('count(*) as total'))
                ->groupBy('tipo_contrato')
                ->get(),
            'puestos_vacios' => Puesto::where('estado', 1)
                ->whereDoesntHave('historial', function ($query) {
                    $query->whereNull('fecha_fin')
                        ->where('estado', 'activo');
                })->count()
        ];

        return view('admin.historial.estadisticas', compact('stats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $historial = Historial::with(['puesto', 'persona'])->findOrFail($id);

            $request->validate([
                'tipo_movimiento' => 'required|in:designacion_inicial,movilidad,reasignacion,ascenso,comision,interinato,encargo_funciones,recontratacion',
                'tipo_contrato' => 'required|in:permanente,contrato_administrativo,contrato_plazo_fijo,contrato_obra,honorarios',
                'estado' => 'required|in:activo,concluido,suspendido',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'fecha_vencimiento' => 'nullable|date|after:fecha_inicio',
                'numero_memo' => 'nullable|string|max:100',
                'fecha_memo' => 'nullable|date',
                'archivo_memo' => 'nullable|file|mimes:pdf|max:2048',
                'salario' => 'nullable|numeric|min:0',
                'porcentaje_dedicacion' => 'nullable|integer|min:1|max:100',
                'jornada_laboral' => 'nullable|in:completa,media_jornada,parcial',
                'motivo' => 'nullable|string|max:500',
                'observaciones' => 'nullable|string|max:1000',
                'renovacion_automatica' => 'nullable|boolean',
            ]);

            $data = $request->except('archivo_memo', '_token', '_method');

            // Manejar archivo PDF si se subió uno nuevo
            if ($request->hasFile('archivo_memo')) {
                // Eliminar archivo anterior si existe
                if ($historial->archivo_memo && Storage::disk('public')->exists($historial->archivo_memo)) {
                    Storage::disk('public')->delete($historial->archivo_memo);
                }

                $archivo = $request->file('archivo_memo');
                $nombreArchivo = 'memo_' . time() . '_' . $historial->persona_id . '.' . $archivo->getClientOriginalExtension();
                $ruta = $archivo->storeAs('memos', $nombreArchivo, 'public');
                $data['archivo_memo'] = $ruta;
            }

            // Si se cambia el estado a concluido, establecer fecha_fin si no existe
            if ($request->estado == 'concluido' && empty($request->fecha_fin)) {
                $data['fecha_fin'] = now()->format('Y-m-d');
            }

            // Si se reactiva una designación concluida, quitar fecha_fin
            if ($request->estado == 'activo' && $historial->estado == 'concluido') {
                $data['fecha_fin'] = null;
            }

            // Validar lógica de negocio para comisiones e interinatos
            if (in_array($request->tipo_movimiento, ['comision', 'interinato', 'encargo_funciones'])) {
                // Verificar que no se quite la bandera de conserva_puesto_original si es necesario
                if ($historial->conserva_puesto_original && !$request->conserva_puesto_original) {
                    throw new \Exception('No puede quitar la bandera de conserva puesto original para este tipo de movimiento');
                }
            }

            // Si es movilidad o ascenso, verificar que no tenga designaciones activas conflictivas
            if (in_array($request->tipo_movimiento, ['movilidad', 'ascenso', 'designacion_inicial'])) {
                $designacionActiva = Historial::where('persona_id', $historial->persona_id)
                    ->where('id', '!=', $historial->id)
                    ->where('estado', 'activo')
                    ->where('conserva_puesto_original', false)
                    ->first();

                if ($designacionActiva) {
                    throw new \Exception('La persona ya tiene una designación principal activa. Debe concluirla primero.');
                }
            }

            // Actualizar el historial
            $historial->update($data);

            // Si se concluye una designación, verificar si hay comisiones asociadas
            if ($request->estado == 'concluido' && $historial->conserva_puesto_original == false) {
                // Concluir también las comisiones e interinatos asociados
                Historial::where('persona_id', $historial->persona_id)
                    ->where('puesto_original_id', $historial->puesto_id)
                    ->where('estado', 'activo')
                    ->update(['estado' => 'concluido', 'fecha_fin' => now()]);
            }

            DB::commit();

            return redirect()->route('puesto')
                ->with('success', 'Designación actualizada correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la designación: ' . $e->getMessage())
                ->withInput();
        }
    }
}
