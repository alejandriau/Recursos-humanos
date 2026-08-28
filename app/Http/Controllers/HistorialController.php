<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Historial;
use App\Models\Puesto; // FALTABA ESTA IMPORTACIÓN
use App\Models\UnidadOrganizacional;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
    \Log::info('Iniciando registro de designación', $request->all());

    $request->validate([
        'persona_id' => 'required|exists:persona,id',
        'puesto_id' => 'required|exists:puestos,id',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'tipo_movimiento' => 'required|in:designacion_inicial,movilidad,ascenso,reasignacion,comision,interinato,encargo_funciones,recontratacion',
        'tipo_contrato' => 'required|in:permanente,contrato_administrativo,contrato_plazo_fijo,contrato_obra,honorarios',
        'numero_memo' => 'nullable|string|max:100',
        'fecha_memo' => 'nullable|date',
        'archivo_memo' => 'nullable|file|mimes:pdf|max:2048',
        'salario' => 'nullable|numeric|min:0',
        'porcentaje_dedicacion' => 'nullable|integer|min:1|max:100',
        'fecha_vencimiento' => 'nullable|date|after:fecha_inicio',
        'motivo' => 'nullable|string|max:500',
        'observaciones' => 'nullable|string|max:1000',
        'jornada_laboral' => 'nullable|in:completa,media_jornada,parcial',
        'renovacion_automatica' => 'nullable|boolean'
    ]);

    DB::beginTransaction();

    try {
        $data = $request->except('archivo_memo');

        // Convertir checkbox booleano
        $data['renovacion_automatica'] = $request->has('renovacion_automatica') ? true : false;

        \Log::info('Datos preparados:', $data);

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

        \Log::info('Puesto y persona validados:', [
            'puesto' => $puesto->id,
            'persona' => $persona->id
        ]);

        // Manejar archivo PDF
        // Verificar/crear estructura de carpetas de la persona
        if (!$persona->archivo) {
            $nombre = Str::slug($persona->nombre);
            $apellidoMat = Str::slug($persona->apellidoMat ?? 'SinApellido');
            $fecha = now()->format('Y-m-d');
            $nombreCarpeta = "{$persona->id}_{$nombre}_{$apellidoMat}_{$fecha}";
            $rutaBase = "archivos/{$nombreCarpeta}";

            // Crear carpeta principal y subcarpetas
            Storage::disk('local')->makeDirectory($rutaBase);

            // Actualizar persona con la ruta
            $persona->archivo = $rutaBase;
            $persona->save();
            \Log::info('Carpeta creada para persona:', ['ruta' => $rutaBase]);
        }

        // Manejar archivo PDF del memo
        if ($request->hasFile('archivo_memo')) {
            // Crear subcarpeta para designaciones si no existe
            $rutaDesignaciones = $persona->archivo . '/designaciones';
            if (!Storage::disk('local')->exists($rutaDesignaciones)) {
                Storage::disk('local')->makeDirectory($rutaDesignaciones);
            }

            // Generar nombre descriptivo para el archivo
            $tipoMovimientoSlug = Str::slug($request->tipo_movimiento, '_');
            $fechaInicio = date('Ymd', strtotime($request->fecha_inicio));
            $nombreArchivo = "MEMO_" . $persona->ci . "_" .
                           $tipoMovimientoSlug . "_" .
                           $fechaInicio . "_" .
                           now()->format('His') . ".pdf";

            // Guardar archivo
            $archivo = $request->file('archivo_memo');
            $path = $archivo->storeAs($rutaDesignaciones, $nombreArchivo, 'local');
            $data['archivo_memo'] = $path;

            \Log::info('Archivo memo guardado:', [
                'ruta' => $path,
                'tipo_movimiento' => $request->tipo_movimiento
            ]);
        }

        // Si es movilidad, ascenso o reasignación, concluir el registro anterior
        if (in_array($request->tipo_movimiento, ['movilidad', 'ascenso', 'reasignacion'])) {
            \Log::info('Procesando movimiento tipo: ' . $request->tipo_movimiento);

            $historialAnterior = Historial::where('persona_id', $request->persona_id)
                ->where('estado', 'activo')
                ->first();

            \Log::info('Historial anterior encontrado:', [
                'existe' => !is_null($historialAnterior),
                'id' => $historialAnterior ? $historialAnterior->id : null
            ]);

            if ($historialAnterior) {
                // Calcular fecha fin para el historial anterior (un día antes del nuevo inicio)
                $nuevaFechaInicio = new \DateTime($request->fecha_inicio);
                $nuevaFechaInicio->modify('-1 day');
                $fechaFinAnterior = $nuevaFechaInicio->format('Y-m-d');

                \Log::info('Actualizando historial anterior:', [
                    'id' => $historialAnterior->id,
                    'fecha_fin_anterior' => $fechaFinAnterior,
                    'nueva_fecha_inicio' => $request->fecha_inicio
                ]);

                // Actualizar el historial anterior - USAR UPDATE directamente
                $actualizado = Historial::where('id', $historialAnterior->id)
                    ->update([
                        'fecha_fin' => $fechaFinAnterior,
                        'estado' => 'concluido',
                        // Si no existe la columna, usamos 'motivo' temporalmente
                        'motivo' => $historialAnterior->motivo . ' | Concluido por: ' . $request->tipo_movimiento
                    ]);

                if (!$actualizado) {
                    throw new \Exception('Error al actualizar el historial anterior');
                }

                \Log::info('Historial anterior actualizado exitosamente');

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

        // Establecer estado por defecto
        $data['estado'] = 'activo';

        \Log::info('Creando nuevo historial con datos:', $data);

        // Crear el nuevo registro de historial
        $nuevoHistorial = Historial::create($data);

        \Log::info('Nuevo historial creado:', [
            'id' => $nuevoHistorial->id,
            'persona_id' => $nuevoHistorial->persona_id,
            'puesto_id' => $nuevoHistorial->puesto_id
        ]);

        DB::commit();

        \Log::info('Designación registrada exitosamente');

        return redirect()->route('historial')
            ->with('success', 'Designación registrada correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('Error al registrar designación:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

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

        $baseQuery = Puesto::where('estado', 1)
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
            });

        // --- Totales reales (sin paginación) ---
        // Clonamos el query antes de aplicar with()/paginate() para no arrastrar eager loads innecesarios
        $totalDesignacionesActivas = (clone $baseQuery)
            ->whereHas('historial', function ($q) use ($tipoMovimiento, $estado, $tipoContrato) {
                $q->whereNull('fecha_fin')
                ->where('estado', 'activo')
                ->when($tipoMovimiento, fn($q2) => $q2->where('tipo_movimiento', $tipoMovimiento))
                ->when($tipoContrato, fn($q2) => $q2->where('tipo_contrato', $tipoContrato));
            })->count();

        $totalPuestosVacios = (clone $baseQuery)
            ->whereDoesntHave('historial', function ($q) {
                $q->whereNull('fecha_fin');
            })->count();

        $totalComisiones = (clone $baseQuery)
            ->whereHas('historial', function ($q) use ($estado, $tipoContrato) {
                $q->whereNull('fecha_fin')
                ->where('tipo_movimiento', 'comision')
                ->when($estado, fn($q2) => $q2->where('estado', $estado))
                ->when($tipoContrato, fn($q2) => $q2->where('tipo_contrato', $tipoContrato));
            })->count();

        $totalInterinatos = (clone $baseQuery)
            ->whereHas('historial', function ($q) use ($estado, $tipoContrato) {
                $q->whereNull('fecha_fin')
                ->where('tipo_movimiento', 'interinato')
                ->when($estado, fn($q2) => $q2->where('estado', $estado))
                ->when($tipoContrato, fn($q2) => $q2->where('tipo_contrato', $tipoContrato));
            })->count();

        // --- Listado paginado ---
        $puestos = (clone $baseQuery)
            ->with([
                'unidadOrganizacional.padre.padre.padre',
                'historial' => function ($query) use ($tipoMovimiento, $estado, $tipoContrato) {
                    $query->whereNull('fecha_fin')
                        ->when($tipoMovimiento, fn($q) => $q->where('tipo_movimiento', $tipoMovimiento))
                        ->when($estado, fn($q) => $q->where('estado', $estado))
                        ->when($tipoContrato, fn($q) => $q->where('tipo_contrato', $tipoContrato))
                        ->with('persona')
                        ->orderBy('id', 'desc');
                }
            ])
            ->paginate(100);

        $puestos->getCollection()->transform(function ($puesto) {
            $historial = $puesto->historial->first();
            $puesto->persona = $historial?->persona;
            $puesto->historial_actual = $historial;
            return $puesto;
        });

        return view('admin.pasivos.bajas', compact(
            'puestos', 'search', 'tipoMovimiento', 'estado', 'tipoContrato',
            'totalDesignacionesActivas', 'totalPuestosVacios', 'totalComisiones', 'totalInterinatos'
        ));
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
            $data['renovacion_automatica'] = $request->has('renovacion_automatica') ? true : false;

            // Obtener la persona relacionada
            $persona = $historial->persona;

            // Verificar/crear estructura de carpetas de la persona (igual que en store)
            if (!$persona->archivo) {
                $nombre = Str::slug($persona->nombre);
                $apellidoMat = Str::slug($persona->apellidoMat ?? 'SinApellido');
                $fecha = now()->format('Y-m-d');
                $nombreCarpeta = "{$persona->id}_{$nombre}_{$apellidoMat}_{$fecha}";
                $rutaBase = "archivos/{$nombreCarpeta}";

                // Crear carpeta principal y subcarpetas
                Storage::disk('local')->makeDirectory($rutaBase);

                // Actualizar persona con la ruta
                $persona->archivo = $rutaBase;
                $persona->save();
            }

            // Manejar archivo PDF si se subió uno nuevo (igual que en store)
            if ($request->hasFile('archivo_memo')) {
                // Crear subcarpeta para designaciones si no existe
                $rutaDesignaciones = $persona->archivo . '/designaciones';
                if (!Storage::disk('local')->exists($rutaDesignaciones)) {
                    Storage::disk('local')->makeDirectory($rutaDesignaciones);
                }

                // Generar nombre descriptivo para el archivo
                $tipoMovimientoSlug = Str::slug($request->tipo_movimiento, '_');
                $fechaInicio = date('Ymd', strtotime($request->fecha_inicio));
                $nombreArchivo = "MEMO_" . $persona->ci . "_" .
                            $tipoMovimientoSlug . "_" .
                            $fechaInicio . "_" .
                            now()->format('His') . ".pdf";

                // Guardar archivo
                $archivo = $request->file('archivo_memo');
                $path = $archivo->storeAs($rutaDesignaciones, $nombreArchivo, 'local');
                $data['archivo_memo'] = $path;

                // Eliminar archivo anterior si existe (usando el mismo disk 'local')
                if ($historial->archivo_memo && Storage::disk('local')->exists($historial->archivo_memo)) {
                    Storage::disk('local')->delete($historial->archivo_memo);
                }
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
            if (in_array($request->estado, ['activo', 'suspended']) &&
                in_array($request->tipo_movimiento, ['movilidad', 'ascenso', 'designacion_inicial'])) {

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

            return redirect()->route('historial')
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

    // En el modelo Historial
public function marcarComoConcluido($fechaFin = null, $motivo = 'Movimiento a nuevo puesto')
{
    $this->update([
        'fecha_fin' => $fechaFin,
        'estado' => 'concluido',
        'motivo_conclusion' => $motivo
    ]);
}

    public function destroy($id)
    {
        // Buscar el registro
        $historial = Historial::findOrFail($id);

        // Eliminarlo
        $historial->delete();

        // Redirigir o devolver respuesta
        return redirect()->back()->with('success', 'Registro eliminado correctamente');
    }

        //ver pdf
    public function verPdf($id)
    {
        $historial = Historial::find($id);

        if (!$historial) {
            abort(404, 'Designación no encontrada.');
        }

        if (empty($historial->archivo_memo)) {
            abort(404, 'Esta designación no tiene un archivo PDF asociado.');
        }

        if (!Storage::disk('local')->exists($historial->archivo_memo)) {
            abort(404, 'El archivo PDF no se encuentra en el servidor.');
        }

        // Opción más limpia con Storage::response()
        return Storage::disk('local')->response(
            $historial->archivo_memo,
            basename($historial->archivo_memo),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($historial->archivo_memo) . '"'
            ]
        );
    }
    public function pdfPlanilla(Request $request)
    {
        $puestos = $this->obtenerDatosPlanilla($request);

        $pdf = Pdf::loadView('admin.historial.pdf.planilla', compact('puestos'))
            ->setPaper('legal', 'landscape')
            ->setOption('isPhpEnabled', true)
            ->setOption('defaultFont', 'Arial');

        return $pdf->download('Planilla_Presupuestaria_'.now()->format('Ymd').'.pdf');
    }

    public function excelPlanilla(Request $request)
    {
        $puestos = $this->obtenerDatosPlanilla($request);
        $spreadsheet = $this->construirExcelPlanilla($puestos);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Planilla_Presupuestaria_'.now()->format('Ymd').'.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Trae TODOS los puestos activos, ordenados por item,
     * agrupados por su unidad raíz.
     */
private function obtenerDatosPlanilla(Request $request)
{
    $search = $request->input('search');
    $tipoMovimiento = $request->input('tipo_movimiento');
    $estado = $request->input('estado');
    $tipoContrato = $request->input('tipo_contrato');

    // Query base: todos los activos, ordenados por item ASC
    $query = Puesto::where('estado', 1)
        ->orderBy('item', 'asc');

    // Filtros de búsqueda (solo si hay término)
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('item', 'like', "%{$search}%")
              ->orWhere('denominacion', 'like', "%{$search}%")
              ->orWhere('nivelJerarquico', 'like', "%{$search}%")
              ->orWhereHas('historial', function ($hq) use ($search) {
                  $hq->whereNull('fecha_fin')
                     ->whereHas('persona', function ($pq) use ($search) {
                         $pq->where('estado', 1)
                            ->where(function ($sq) use ($search) {
                                $sq->where('nombre', 'like', "%{$search}%")
                                   ->orWhere('apellidoPat', 'like', "%{$search}%")
                                   ->orWhere('apellidoMat', 'like', "%{$search}%")
                                   ->orWhereRaw("CONCAT(nombre,' ',apellidoPat,' ',apellidoMat) LIKE ?", ["%{$search}%"]);
                            });
                     });
              });
        });
    }

    // Filtros de historial (movimiento, estado, contrato)
    if ($tipoMovimiento || $estado || $tipoContrato) {
        $query->whereHas('historial', function ($hq) use ($tipoMovimiento, $estado, $tipoContrato) {
            $hq->whereNull('fecha_fin');
            if ($tipoMovimiento) $hq->where('tipo_movimiento', $tipoMovimiento);
            if ($estado)         $hq->where('estado', $estado);
            if ($tipoContrato)   $hq->where('tipo_contrato', $tipoContrato);
        });
    }

    // Cargar relaciones (ajusta la cadena de .padre si tu organigrama tiene más niveles)
    $puestos = $query->with([
        'unidadOrganizacional.padre.padre.padre.padre.padre',
        'historial' => function ($q) use ($tipoMovimiento, $estado, $tipoContrato) {
            $q->whereNull('fecha_fin')
              ->when($tipoMovimiento, fn($sq) => $sq->where('tipo_movimiento', $tipoMovimiento))
              ->when($estado,         fn($sq) => $sq->where('estado', $estado))
              ->when($tipoContrato,   fn($sq) => $sq->where('tipo_contrato', $tipoContrato))
              ->with('persona')
              ->orderBy('id', 'desc');
        }
    ])->get();

    // 1) Puestos agrupados por el id de su unidad DIRECTA
    $puestosPorUnidad = $puestos->groupBy(fn($p) => $p->unidadOrganizacional?->id ?? 0);

    // 2) Mapa de todas las unidades involucradas (la directa + todos sus ancestros)
    $unidadesMap = [];
    foreach ($puestos as $puesto) {
        $unidad = $puesto->unidadOrganizacional;
        while ($unidad) {
            $unidadesMap[$unidad->id] = $unidad;
            $unidad = $unidad->padre;
        }
    }

    // 3) Árbol: hijos por padre, y raíces (unidades sin padre dentro del mapa)
    $hijosPorPadre = [];
    $raices = [];
    foreach ($unidadesMap as $unidad) {
        $padreId = $unidad->padre?->id;
        if ($padreId && isset($unidadesMap[$padreId])) {
            $hijosPorPadre[$padreId][] = $unidad;
        } else {
            $raices[] = $unidad;
        }
    }

    // 4) Ítem mínimo de cada unidad (propio + toda su descendencia), para ordenar
    $itemMinimoCache = [];
    $calcularItemMinimo = function ($unidadId) use (&$calcularItemMinimo, &$itemMinimoCache, $puestosPorUnidad, $hijosPorPadre) {
        if (isset($itemMinimoCache[$unidadId])) return $itemMinimoCache[$unidadId];
        $min = PHP_INT_MAX;
        foreach (($puestosPorUnidad[$unidadId] ?? []) as $p) {
            $min = min($min, (int) $p->item);
        }
        foreach (($hijosPorPadre[$unidadId] ?? []) as $hijo) {
            $min = min($min, $calcularItemMinimo($hijo->id));
        }
        return $itemMinimoCache[$unidadId] = $min;
    };

    // 5) Recorrido en profundidad: cabecera de unidad -> sus ítems -> sus hijos (recursivo)
    $resultado = [];
    $recorrer = function ($unidad) use (&$recorrer, &$resultado, $puestosPorUnidad, $hijosPorPadre, $calcularItemMinimo) {
        $itemsPropios = ($puestosPorUnidad[$unidad->id] ?? collect())->isNotEmpty();
        $hijos = $hijosPorPadre[$unidad->id] ?? [];

        if (!$itemsPropios && empty($hijos)) return; // unidad sin nada que mostrar

        $resultado[] = [
            'tipo'   => 'dependencia',
            'nombre' => strtoupper($unidad->denominacion),
        ];

        $items = ($puestosPorUnidad[$unidad->id] ?? collect())->sortBy(fn($p) => (int) $p->item);
        foreach ($items as $puesto) {
            $historial = $puesto->historial->first();
            $persona   = $historial?->persona;

            $resultado[] = [
                'tipo'                   => 'puesto',
                'item'                   => $puesto->item,
                'dependencia_jerarquica' => $unidad->denominacion,
                'nombre_cargo'           => $puesto->denominacion,
                'categoria'              => $this->inferirCategoria($puesto->nivelJerarquico),
                'nivel_clase'            => $puesto->nivelJerarquico,
                'nivel_salarial'         => $puesto->nivel_salarial ?? '',
                'clasificacion'          => $puesto->clasificacion ?? 'SUSTANTIVO',
                'haber'                  => $puesto->haber,
                'nombre_completo'        => $persona
                    ? trim("{$persona->apellidoPat} {$persona->apellidoMat} {$persona->nombre}")
                    : 'ACEFALIA',
                'fecha_nacimiento'       => $persona?->fecha_nacimiento
                    ? \Carbon\Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y')
                    : '',
                'ci'                     => $persona?->ci ?? '',
                'fecha_ingreso'          => $historial?->fecha_inicio
                    ? \Carbon\Carbon::parse($historial->fecha_inicio)->format('d/m/Y')
                    : '',
                'observaciones'          => $historial?->observaciones ?? '',
                'formacion'              => $persona?->formacion ?? '',
            ];
        }

        $hijosOrdenados = collect($hijos)->sortBy(fn($h) => $calcularItemMinimo($h->id));
        foreach ($hijosOrdenados as $hijo) {
            $recorrer($hijo);
        }
    };

    $raicesOrdenadas = collect($raices)->sortBy(fn($u) => $calcularItemMinimo($u->id));
    foreach ($raicesOrdenadas as $raiz) {
        $recorrer($raiz);
    }

    return $resultado;
}

    /**
     * Sube hasta la unidad de nivel 0 (raíz) para agrupar la planilla
     */
    private function obtenerUnidadRaiz($unidad): string
    {
        if (!$unidad) return 'SIN DEPENDENCIA';
        while ($unidad->padre) {
            $unidad = $unidad->padre;
        }
        return $unidad->denominacion ?? 'SIN DEPENDENCIA';
    }

    /**
     * Categoría según nivel (ajusta a tu escala real)
     */
    private function inferirCategoria($nivel): string
    {
        return match(true) {
            $nivel <= 2 => 'SUPERIOR',
            $nivel <= 4 => 'EJECUTIVO',
            default     => 'OPERATIVO',
        };
    }

    /**
     * ============================================================
     * EXCEL
     * ============================================================
     */
    private function construirExcelPlanilla(array $filas): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Planilla 2026');

        // Anchos de columna
        $anchos = [
            'A'=>6, 'B'=>38, 'C'=>55, 'D'=>14, 'E'=>12, 'F'=>14,
            'G'=>18, 'H'=>14, 'I'=>35, 'J'=>16, 'K'=>14, 'L'=>16, 'M'=>45, 'N'=>30
        ];
        foreach ($anchos as $col => $ancho) {
            $sheet->getColumnDimension($col)->setWidth($ancho);
        }

        // --- FILA 1: TÍTULO ---
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1',
            'PLANILLA PRESUPUESTARIA DE PERSONAL DE PLANTA DEL ÓRGANO EJECUTIVO '.
            'DEL GOBIERNO AUTÓNOMO DEPARTAMENTAL DE COCHABAMBA - 2026');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // --- FILA 2: ENCABEZADOS ---
        $headers = [
            'N°', 'DEPENDENCIA/DENOMINACIÓN JERÁRQUICA', 'NOMBRE DE CARGO', 'CATEGORÍA',
            'NIVEL (CLASE)', 'NIVEL SALARIAL', 'CLASIFICACIÓN DEL PUESTO', 'SUELDO O HABER MENSUAL',
            'NOMBRE COMPLETO', 'FECHA DE NACIMIENTO', 'Nº CARNET', 'FECHA DE INGRESO',
            'OBSERVACIONES', 'MDC 2026'
        ];
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValueByColumnAndRow($col, 2, $h);
            $col++;
        }

        $sheet->getStyle('A2:N2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '4F81BD']]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(35);

        // --- DATOS ---
        $row = 3;
        foreach ($filas as $fila) {
            if ($fila['tipo'] === 'dependencia') {
                $sheet->mergeCells("A{$row}:N{$row}");
                $sheet->setCellValue("A{$row}", $fila['nombre']);
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B4C7DC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '4F81BD']]],
                ]);
                $row++;
                continue;
            }

            $sheet->setCellValue("A{$row}", $fila['item']);
            $sheet->setCellValue("B{$row}", $fila['dependencia_jerarquica']);
            $sheet->setCellValue("C{$row}", $fila['nombre_cargo']);
            $sheet->setCellValue("D{$row}", $fila['categoria']);
            $sheet->setCellValue("E{$row}", $fila['nivel_clase']);
            $sheet->setCellValue("F{$row}", $fila['nivel_salarial']);
            $sheet->setCellValue("G{$row}", $fila['clasificacion']);
            $sheet->setCellValue("H{$row}", $fila['haber']);
            $sheet->setCellValue("I{$row}", $fila['nombre_completo']);
            $sheet->setCellValue("J{$row}", $fila['fecha_nacimiento']);
            $sheet->setCellValue("K{$row}", $fila['ci']);
            $sheet->setCellValue("L{$row}", $fila['fecha_ingreso']);
            $sheet->setCellValue("M{$row}", $fila['observaciones']);
            $sheet->setCellValue("N{$row}", $fila['formacion']);

            // Formato moneda
            $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0');

            // Bordes y alineación
            $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '4F81BD']]],
            ]);
            $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("J{$row}:L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $sheet->freezePane('A3');
        return $spreadsheet;
    }

}
