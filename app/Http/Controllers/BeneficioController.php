<?php

namespace App\Http\Controllers;

use App\Models\BeneficioPeriodo;
use App\Models\Gestion;
use App\Models\Persona;
use App\Models\TipoSalida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use App\Models\Salida;
use App\Models\BeneficioMovimiento;
use Illuminate\Support\Facades\Auth;

class BeneficioController extends Controller
{
    public function asignarVista()
    {
        // Obtener tipos de salida activos que son asignables
        $tiposalidas = Tiposalida::where('activo', true)
            ->whereDoesntHave('hijos')
            ->orderBy('descripcion')
            ->get();

        $gestiones = Gestion::where('estado', 'habilitado')->get();

        return view('admin.beneficio.asigBeneficio', compact('tiposalidas', 'gestiones'));
    }

    public function buscarPersonal(Request $request)
    {
        $q = $request->get('q');

        return Persona::where('nombre', 'like', "%$q%")
            ->orWhere('apellidoPat', 'like', "%$q%")
            ->orWhere('apellidoMat', 'like', "%$q%")
            ->orWhere('ci', 'like', "%$q%")
            ->select('id', 'nombre', 'apellidoPat', 'apellidoMat', 'ci')
            ->limit(10)
            ->get();
    }

    /**
     * Guardar beneficios asignados
     */
    public function guardarBeneficios(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'persona_id' => 'required|exists:persona,id',
            'gestion_id' => 'required|exists:gestions,id',
            'beneficios' => 'required|array|min:1',
            'beneficios.*.tiposalida_id' => 'required|exists:tiposalidas,id',
            'beneficios.*.cantidad' => 'required|numeric|min:0.1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $beneficiosCreados = [];

            foreach ($request->beneficios as $b) {
                $tipo = TipoSalida::find($b['tiposalida_id']);

                // Verificar que el tipo sea asignable
                if ($tipo->hijos()->exists()) {
                    throw new \Exception("El tipo {$tipo->descripcion} no es asignable directamente.");
                }

                // Verificar si ya existe el beneficio para esta persona, gestión y tipo
                $existente = BeneficioPeriodo::where('persona_id', $request->persona_id)
                    ->where('tiposalida_id', $tipo->id)
                    ->where('gestion_id', $request->gestion_id)
                    ->first();

                if ($existente) {
                    // Si existe, actualizar la cantidad
                    $existente->cantidad_asignada = $b['cantidad'];
                    $existente->saldo_disponible = $b['cantidad'] - $existente->cantidad_usada;
                    $existente->fecha_habilitacion = now();
                    $existente->estado = 'activo';
                    $existente->save();

                    $beneficiosCreados[] = $existente->fresh(['tiposalida', 'gestion', 'persona']);
                } else {
                    // Crear nuevo beneficio
                    $beneficio = BeneficioPeriodo::create([
                        'persona_id' => $request->persona_id,
                        'tiposalida_id' => $tipo->id,
                        'gestion_id' => $request->gestion_id,
                        'mes' => $tipo->periodicidad === 'mensual' ? date('n') : null,
                        'unidad' => $tipo->unidad ?? 'dias',
                        'cantidad_asignada' => $b['cantidad'],
                        'cantidad_usada' => 0,
                        'cantidad_vencida' => 0,
                        'arrastre' => 0,
                        'saldo_disponible' => $b['cantidad'],
                        'fecha_habilitacion' => now(),
                        'estado' => 'activo',
                    ]);

                    $beneficiosCreados[] = $beneficio->fresh(['tiposalida', 'gestion', 'persona']);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Beneficios asignados correctamente.',
                'data' => $beneficiosCreados
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al asignar beneficios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener beneficios de una persona
     */
    public function obtenerBeneficios(Request $request)
    {
        $personaId = $request->get('persona_id');
        $gestionId = $request->get('gestion_id');

        if (!$personaId || !$gestionId) {
            return response()->json([]);
        }

        $beneficios = BeneficioPeriodo::with(['tiposalida', 'gestion', 'persona'])
            ->where('persona_id', $personaId)
            ->where('gestion_id', $gestionId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($beneficios);
    }

    /**
 * Obtener vacaciones de una persona
 */
    public function obtenerVacaciones(Request $request)
    {
        $personaId = $request->get('persona_id');
        $gestionId = $request->get('gestion_id');

        if (!$personaId) {
            return response()->json([]);
        }

        $vacaciones = VacacionPeriodo::with(['movimientos'])
            ->where('persona_id', $personaId)
            ->when($gestionId, function($q) use ($gestionId) {
                return $q->where('gestion_id', $gestionId);
            })
            ->orderBy('numero_periodo', 'desc')
            ->get();

        return response()->json($vacaciones);
    }
    /**
     * Eliminar beneficio
     */
    public function eliminarBeneficio($id)
    {
        $beneficio = BeneficioPeriodo::findOrFail($id);

        // Verificar que no tenga usos registrados
        if ($beneficio->cantidad_usada > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el beneficio porque ya tiene uso registrado.'
            ], 422);
        }

        $beneficio->delete();

        return response()->json([
            'message' => 'Beneficio eliminado correctamente.'
        ]);
    }

    /**
     * Editar cantidad de beneficio
     */
    public function editarBeneficio(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|numeric|min:0.1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $beneficio = BeneficioPeriodo::findOrFail($id);

        // Verificar que la nueva cantidad no sea menor a lo ya usado
        if ($request->cantidad < $beneficio->cantidad_usada) {
            return response()->json([
                'message' => 'La nueva cantidad no puede ser menor a lo ya usado (' . $beneficio->cantidad_usada . ').'
            ], 422);
        }

        $beneficio->cantidad_asignada = $request->cantidad;
        $beneficio->saldo_disponible = $request->cantidad - $beneficio->cantidad_usada;
        $beneficio->save();

        return response()->json([
            'message' => 'Cantidad actualizada correctamente.',
            'data' => $beneficio
        ]);
    }

    /**
     * Asignar vacaciones automáticas
     */
    public function asignarVacaciones(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'persona_id' => 'required|exists:persona,id',
            'gestion_id' => 'required|exists:gestions,id',
            'dias' => 'required|numeric|min:1',
            'anios_antiguedad' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $persona = Persona::find($request->persona_id);
            $gestion = Gestion::find($request->gestion_id);

            // Calcular número de período (año de habilitación)
            $numeroPeriodo = date('Y');

            // Crear período de vacaciones
            $periodo = VacacionPeriodo::create([
                'persona_id' => $request->persona_id,
                'gestion_id' => $request->gestion_id,
                'numero_periodo' => $numeroPeriodo,
                'fecha_habilitacion' => now(),
                'anios_antiguedad' => $request->anios_antiguedad,
                'dias_asignados' => $request->dias,
                'dias_usados' => 0,
                'dias_vencidos' => 0,
                'saldo_disponible' => $request->dias,
                'dias_arrastre' => 0,
                'periodo_vencido' => false,
                'estado' => 'activo',
                'observacion' => 'Asignación automática por administrador'
            ]);

            // Crear movimiento de crédito inicial
            VacacionMovimiento::create([
                'periodo_id' => $periodo->id,
                'tipo' => 'credito',
                'fecha' => now(),
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'cantidad' => $request->dias,
                'saldo_anterior' => 0,
                'saldo_posterior' => $request->dias,
                'salida_id' => null,
                'descripcion' => 'Asignación inicial de vacaciones',
                'registrado_por' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Vacaciones asignadas correctamente.',
                'data' => $periodo->load('movimientos')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al asignar vacaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen de beneficios de un empleado
     */
    public function resumenEmpleado(Request $request)
    {
        $personaId = $request->get('persona_id');
        $gestionId = $request->get('gestion_id');

        if (!$personaId) {
            return response()->json(['error' => 'Persona no especificada'], 400);
        }

        // Beneficios generales
        $beneficios = BeneficioPeriodo::with(['tiposalida'])
            ->where('persona_id', $personaId)
            ->when($gestionId, function($q) use ($gestionId) {
                return $q->where('gestion_id', $gestionId);
            })
            ->get();

        // Vacaciones
        $vacaciones = VacacionPeriodo::with(['movimientos'])
            ->where('persona_id', $personaId)
            ->when($gestionId, function($q) use ($gestionId) {
                return $q->where('gestion_id', $gestionId);
            })
            ->get();

        return response()->json([
            'beneficios' => $beneficios,
            'vacaciones' => $vacaciones,
            'totales' => [
                'beneficios_asignados' => $beneficios->sum('cantidad_asignada'),
                'beneficios_disponibles' => $beneficios->sum('saldo_disponible'),
                'vacaciones_asignadas' => $vacaciones->sum('dias_asignados'),
                'vacaciones_disponibles' => $vacaciones->sum('saldo_disponible'),
            ]
        ]);
    }


    // Elimina un beneficio despues de asignar el beneficio al personal
    public function eliminar($id)
    {
        $beneficio = BeneficioPeriodo::findOrFail($id);
        $beneficio->delete();

        return response()->json(['mensaje' => 'Beneficio eliminado correctamente']);
    }
    // Modificar el campo cantidad del beneficio
    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'nullable|numeric|min:0'
        ]);

        $beneficio = BeneficioPeriodo::findOrFail($id);
        $beneficio->cantidad = $request->cantidad;
        $beneficio->save();

        return response()->json(['mensaje' => 'Cantidad actualizada']);
    }
    // funcion para listar beneficios del persona
    public function beneficiosView()
    {
        $gestiones = \App\Models\Gestion::orderBy('anio', 'desc')->get();
        return view('usuario.reportesUsr.listarbeneficio', compact('gestiones'));
    }

    public function data(Request $request)
    {
        $idserv = $request->idserv;

        // Buscar el personal por idservidor
        $personal = \App\Models\Persona::where('idservidor', $idserv)->first();

        if (!$personal) {
            return response()->json(['data' => []]);
        }

        $query = \App\Models\BeneficioPeriodo::with(['gestion', 'tiposalida'])
            ->where('personal_id', $personal->id);

        // Filtro por gestión
        if ($request->filled('gestion')) {
            $query->whereHas('gestion', function ($q) use ($request) {
                $q->where('anio', $request->gestion);
            });
        }

        $beneficios = $query->orderByDesc('gestion_id')->get();

        // Mapear resultados
        $result = $beneficios->map(function ($b) {
            return [
                'gestion'        => $b->gestion->anio ?? '',
                'tipo'           => $b->tiposalida->descripcion ?? '',
                'dias_otorgados' => $b->cantidad,
                'expresado_en'   => $b->tiposalida->expresa ?? '',
            ];
        });

        return response()->json(['data' => $result]);
    }


    //nuevo integracion con el modelo Salida para obtener los beneficios asociados a una salida
    /**
     * Dashboard principal de beneficios del empleado
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return redirect('/')->withErrors('No se encontró información del usuario.');
        }

        // Obtener gestión actual
        $gestionActual = Gestion::where('estado', 'Habilitado')->first();
        $gestionId = $gestionActual?->id ?? null;

        // 1. OBTENER PERÍODOS DE VACACIONES (con sus movimientos)
        $periodosVacacion = VacacionPeriodo::where('persona_id', $persona->id)
            ->with(['movimientos' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->orderBy('numero_periodo', 'desc')
            ->get();

        // 2. OBTENER PERÍODOS DE BENEFICIOS (sin movimientos propios)
        $periodosBeneficio = BeneficioPeriodo::where('persona_id', $persona->id)
            ->with(['tipoSalida'])
            ->whereHas('tipoSalida', function($q) {
                $q->where('activo', true)
                  ->where('descripcion', '!=', 'VACACION'); // Excluir vacaciones que ya están separadas
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. OBTENER SALIDAS (movimientos tanto de vacaciones como de otros beneficios)
        $salidas = Salida::where('persona_id', $persona->id)
            ->with(['tipoSalida', 'periodo'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        // 4. AGRUPAR BENEFICIOS
        $beneficiosAgrupados = $this->agruparBeneficios($periodosVacacion, $periodosBeneficio, $salidas);

        // 5. RESUMEN GENERAL
        $resumenGeneral = $this->calcularResumenGeneral($periodosVacacion, $periodosBeneficio);

        // 6. OBTENER SOLICITUDES RECIENTES (de la tabla salidas)
        $solicitudesRecientes = Salida::where('persona_id', $persona->id)
            ->with(['tipoSalida'])
            ->whereIn('estado', ['pendiente_jefe', 'pendiente_rrhh', 'aprobado'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 7. PRÓXIMOS VENCIMIENTOS
        $proximosVencimientos = $this->obtenerProximosVencimientos($periodosVacacion, $periodosBeneficio);

        // 8. ESTADÍSTICAS POR TIPO
        $estadisticasPorTipo = $this->calcularEstadisticasPorTipo($beneficiosAgrupados);

        return view('empleado.beneficios.index', compact(
            'persona',
            'beneficiosAgrupados',
            'resumenGeneral',
            'solicitudesRecientes',
            'proximosVencimientos',
            'estadisticasPorTipo',
            'gestionActual',
            'salidas'
        ));
    }

    /**
     * Agrupa beneficios por tipo de salida
     */
    private function agruparBeneficios($periodosVacacion, $periodosBeneficio, $salidas)
    {
        $agrupados = [];

        // --- PROCESAR VACACIONES ---
        foreach ($periodosVacacion as $periodo) {
            $tipo = 'VACACION';
            if (!isset($agrupados[$tipo])) {
                $agrupados[$tipo] = [
                    'tipo_salida' => (object) [
                        'id' => null,
                        'descripcion' => 'VACACION',
                        'unidad' => 'dias',
                        'usa_tabla_antiguedad' => true,
                        'periodicidad' => 'anual',
                    ],
                    'periodos' => [],
                    'totales' => [
                        'asignado' => 0,
                        'usado' => 0,
                        'vencido' => 0,
                        'arrastre' => 0,
                        'disponible' => 0,
                        'solicitudes' => 0,
                    ],
                    'movimientos_recientes' => [],
                ];
            }

            // Obtener salidas asociadas a este período
            $salidasPeriodo = $salidas->filter(function($s) use ($periodo) {
                return $s->periodo_id == $periodo->id && $s->periodo_type == 'App\\Models\\VacacionPeriodo';
            });

            $agrupados[$tipo]['periodos'][] = [
                'tipo' => 'vacacion',
                'periodo' => $periodo,
                'numero' => $periodo->numero_periodo,
                'fecha_habilitacion' => $periodo->fecha_habilitacion,
                'asignado' => $periodo->dias_asignados,
                'usado' => $periodo->dias_usados,
                'vencido' => $periodo->dias_vencidos,
                'arrastre' => $periodo->dias_arrastre,
                'disponible' => $periodo->saldo_disponible,
                'estado' => $periodo->estado,
                'movimientos' => $periodo->movimientos ?? collect(),
                'salidas' => $salidasPeriodo,
            ];

            $agrupados[$tipo]['totales']['asignado'] += $periodo->dias_asignados;
            $agrupados[$tipo]['totales']['usado'] += $periodo->dias_usados;
            $agrupados[$tipo]['totales']['vencido'] += $periodo->dias_vencidos;
            $agrupados[$tipo]['totales']['arrastre'] += $periodo->dias_arrastre;
            $agrupados[$tipo]['totales']['disponible'] += $periodo->saldo_disponible;
            $agrupados[$tipo]['totales']['solicitudes'] += $salidasPeriodo->count();
        }

        // --- PROCESAR OTROS BENEFICIOS ---
        foreach ($periodosBeneficio as $periodo) {
            $tipoDescripcion = $periodo->tipoSalida->descripcion ?? 'OTRO';

            if (!isset($agrupados[$tipoDescripcion])) {
                $agrupados[$tipoDescripcion] = [
                    'tipo_salida' => $periodo->tipoSalida,
                    'periodos' => [],
                    'totales' => [
                        'asignado' => 0,
                        'usado' => 0,
                        'vencido' => 0,
                        'arrastre' => 0,
                        'disponible' => 0,
                        'solicitudes' => 0,
                    ],
                    'movimientos_recientes' => [],
                ];
            }

            // Obtener salidas asociadas a este período
            $salidasPeriodo = $salidas->filter(function($s) use ($periodo) {
                return $s->periodo_id == $periodo->id && $s->periodo_type == 'App\\Models\\BeneficioPeriodo';
            });

            $periodoData = [
                'tipo' => 'beneficio',
                'periodo' => $periodo,
                'numero' => $periodo->mes ? "Mes {$periodo->mes}" : 'Anual',
                'fecha_habilitacion' => $periodo->fecha_habilitacion,
                'asignado' => $periodo->cantidad_asignada,
                'usado' => $periodo->cantidad_usada,
                'vencido' => $periodo->cantidad_vencida,
                'arrastre' => $periodo->arrastre,
                'disponible' => $periodo->saldo_disponible,
                'estado' => $periodo->estado,
                'movimientos' => collect(), // No tiene movimientos propios
                'salidas' => $salidasPeriodo,
            ];

            $agrupados[$tipoDescripcion]['periodos'][] = $periodoData;
            $agrupados[$tipoDescripcion]['totales']['asignado'] += $periodo->cantidad_asignada;
            $agrupados[$tipoDescripcion]['totales']['usado'] += $periodo->cantidad_usada;
            $agrupados[$tipoDescripcion]['totales']['vencido'] += $periodo->cantidad_vencida;
            $agrupados[$tipoDescripcion]['totales']['arrastre'] += $periodo->arrastre;
            $agrupados[$tipoDescripcion]['totales']['disponible'] += $periodo->saldo_disponible;
            $agrupados[$tipoDescripcion]['totales']['solicitudes'] += $salidasPeriodo->count();
        }

        return $agrupados;
    }

    /**
     * Calcula el resumen general de todos los beneficios
     */
    private function calcularResumenGeneral($periodosVacacion, $periodosBeneficio)
    {
        $resumen = [
            'total_asignado' => 0,
            'total_usado' => 0,
            'total_vencido' => 0,
            'total_arrastre' => 0,
            'total_disponible' => 0,
            'tipos_activos' => 0,
            'total_periodos' => 0,
            'solicitudes_pendientes' => 0,
        ];

        // Sumar vacaciones
        foreach ($periodosVacacion as $p) {
            $resumen['total_asignado'] += $p->dias_asignados;
            $resumen['total_usado'] += $p->dias_usados;
            $resumen['total_vencido'] += $p->dias_vencidos;
            $resumen['total_arrastre'] += $p->dias_arrastre;
            $resumen['total_disponible'] += $p->saldo_disponible;
            $resumen['total_periodos']++;

            if ($p->estado === 'activo') {
                $resumen['tipos_activos']++;
            }
        }

        // Sumar beneficios
        foreach ($periodosBeneficio as $p) {
            $resumen['total_asignado'] += $p->cantidad_asignada;
            $resumen['total_usado'] += $p->cantidad_usada;
            $resumen['total_vencido'] += $p->cantidad_vencida;
            $resumen['total_arrastre'] += $p->arrastre;
            $resumen['total_disponible'] += $p->saldo_disponible;
            $resumen['total_periodos']++;

            if ($p->estado === 'activo') {
                $resumen['tipos_activos']++;
            }
        }

        return $resumen;
    }

    /**
     * Obtiene los próximos vencimientos para alertas
     */
    private function obtenerProximosVencimientos($periodosVacacion, $periodosBeneficio)
    {
        $vencimientos = [];

        $hoy = now();
        $limite = $hoy->copy()->addDays(30);

        // Vacaciones vencidas o por vencer
        foreach ($periodosVacacion as $p) {
            if ($p->estado === 'activo' && $p->saldo_disponible > 0) {
                // Verificar si tiene días vencidos
                if ($p->dias_vencidos > 0) {
                    $vencimientos[] = [
                        'tipo' => 'VACACION',
                        'periodo' => "Año {$p->numero_periodo}",
                        'dias_afectados' => $p->dias_vencidos,
                        'estado' => 'vencido',
                        'fecha' => $p->fecha_habilitacion->copy()->addYear(),
                        'mensaje' => "Tienes {$p->dias_vencidos} días vencidos del período {$p->numero_periodo}",
                        'prioridad' => 'alta',
                    ];
                }
            }
        }

        // Beneficios por vencer
        foreach ($periodosBeneficio as $p) {
            if ($p->estado === 'activo' && $p->saldo_disponible > 0) {
                $fechaVencimiento = $this->calcularFechaVencimiento($p);
                if ($fechaVencimiento && $fechaVencimiento <= $limite) {
                    $vencimientos[] = [
                        'tipo' => $p->tipoSalida->descripcion ?? 'Beneficio',
                        'periodo' => $p->mes ? "Mes {$p->mes}" : 'Anual',
                        'dias_afectados' => $p->saldo_disponible,
                        'estado' => 'por_vencer',
                        'fecha' => $fechaVencimiento,
                        'mensaje' => "{$p->saldo_disponible} {$p->unidad} de {$p->tipoSalida->descripcion} vencen el " . $fechaVencimiento->format('d/m/Y'),
                        'prioridad' => 'media',
                    ];
                }
            }
        }

        // Ordenar por fecha
        usort($vencimientos, function($a, $b) {
            return $a['fecha']->timestamp - $b['fecha']->timestamp;
        });

        return $vencimientos;
    }

    /**
     * Calcula la fecha de vencimiento de un beneficio
     */
    private function calcularFechaVencimiento($periodo)
    {
        if (!$periodo->fecha_habilitacion) {
            return null;
        }

        $fecha = $periodo->fecha_habilitacion->copy();
        $tipoSalida = $periodo->tipoSalida;

        switch ($tipoSalida->periodicidad ?? 'ninguna') {
            case 'mensual':
                return $fecha->endOfMonth();
            case 'anual':
                return $fecha->addYear();
            case 'evento':
                return $fecha->addYear();
            default:
                return null;
        }
    }

    /**
     * Calcula estadísticas por tipo de beneficio
     */
    private function calcularEstadisticasPorTipo($beneficiosAgrupados)
    {
        $estadisticas = [];

        foreach ($beneficiosAgrupados as $tipo => $data) {
            $disponible = $data['totales']['disponible'];
            $asignado = $data['totales']['asignado'];

            $estadisticas[$tipo] = [
                'nombre' => $tipo,
                'disponible' => $disponible,
                'asignado' => $asignado,
                'usado' => $data['totales']['usado'],
                'porcentaje_uso' => $asignado > 0 ? round((($asignado - $disponible) / $asignado) * 100, 1) : 0,
                'periodos_count' => count($data['periodos']),
                'solicitudes_count' => $data['totales']['solicitudes'] ?? 0,
                'unidad' => $data['tipo_salida']->unidad ?? 'dias',
            ];
        }

        return $estadisticas;
    }

    /**
     * Muestra el detalle de un beneficio específico
     */
    public function detalle(Request $request, $tipo)
    {
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return redirect('/')->withErrors('No se encontró información del usuario.');
        }

        $data = [];
        $tipoDecodificado = urldecode($tipo);

        // Si es VACACION
        if (strtoupper($tipoDecodificado) === 'VACACION') {
            $periodos = VacacionPeriodo::where('persona_id', $persona->id)
                ->with(['movimientos' => function($q) {
                    $q->orderBy('created_at', 'desc');
                }])
                ->orderBy('numero_periodo', 'desc')
                ->get();

            // Obtener salidas asociadas
            $salidas = Salida::where('persona_id', $persona->id)
                ->where('periodo_type', 'App\\Models\\VacacionPeriodo')
                ->with(['tipoSalida'])
                ->orderBy('created_at', 'desc')
                ->get();

            $data = [
                'tipo' => 'VACACION',
                'tipo_salida' => null,
                'periodos' => $periodos,
                'totales' => [
                    'asignado' => $periodos->sum('dias_asignados'),
                    'usado' => $periodos->sum('dias_usados'),
                    'vencido' => $periodos->sum('dias_vencidos'),
                    'arrastre' => $periodos->sum('dias_arrastre'),
                    'disponible' => $periodos->sum('saldo_disponible'),
                ],
                'movimientos' => VacacionMovimiento::whereHas('periodo', function($q) use ($persona) {
                    $q->where('persona_id', $persona->id);
                })->orderBy('created_at', 'desc')->limit(50)->get(),
                'salidas' => $salidas,
            ];
        }
        // Si es otro beneficio
        else {
            $tipoSalida = Tiposalida::where('descripcion', $tipoDecodificado)->first();
            if (!$tipoSalida) {
                return redirect()->route('beneficios.index')->withErrors('Tipo de beneficio no encontrado');
            }

            $periodos = BeneficioPeriodo::where('persona_id', $persona->id)
                ->where('tiposalida_id', $tipoSalida->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Obtener salidas asociadas a estos períodos
            $periodosIds = $periodos->pluck('id')->toArray();
            $salidas = Salida::where('persona_id', $persona->id)
                ->where('periodo_type', 'App\\Models\\BeneficioPeriodo')
                ->whereIn('periodo_id', $periodosIds)
                ->with(['tipoSalida'])
                ->orderBy('created_at', 'desc')
                ->get();

            $data = [
                'tipo' => $tipoDecodificado,
                'tipo_salida' => $tipoSalida,
                'periodos' => $periodos,
                'totales' => [
                    'asignado' => $periodos->sum('cantidad_asignada'),
                    'usado' => $periodos->sum('cantidad_usada'),
                    'vencido' => $periodos->sum('cantidad_vencida'),
                    'arrastre' => $periodos->sum('arrastre'),
                    'disponible' => $periodos->sum('saldo_disponible'),
                ],
                'movimientos' => collect(), // No tiene movimientos propios
                'salidas' => $salidas,
            ];
        }

        return view('empleado.beneficios.detalle', compact('data', 'tipoDecodificado'));
    }

    /**
     * API para obtener datos en tiempo real (para gráficos)
     */
    public function apiDatos(Request $request)
    {
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Obtener vacaciones
        $vacaciones = VacacionPeriodo::where('persona_id', $persona->id)->get();

        // Obtener beneficios
        $beneficios = BeneficioPeriodo::where('persona_id', $persona->id)
            ->with('tipoSalida')
            ->get();

        // Preparar datos para gráficos
        $datosGraficos = [
            'labels' => [],
            'disponible' => [],
            'asignado' => [],
            'usado' => [],
        ];

        // Vacaciones
        if ($vacaciones->count() > 0) {
            $datosGraficos['labels'][] = 'VACACION';
            $datosGraficos['disponible'][] = $vacaciones->sum('saldo_disponible');
            $datosGraficos['asignado'][] = $vacaciones->sum('dias_asignados');
            $datosGraficos['usado'][] = $vacaciones->sum('dias_usados');
        }

        // Otros beneficios
        foreach ($beneficios->groupBy('tiposalida_id') as $tipoId => $items) {
            $tipo = $items->first()->tipoSalida;
            if ($tipo && $tipo->descripcion !== 'VACACION') {
                $datosGraficos['labels'][] = $tipo->descripcion;
                $datosGraficos['disponible'][] = $items->sum('saldo_disponible');
                $datosGraficos['asignado'][] = $items->sum('cantidad_asignada');
                $datosGraficos['usado'][] = $items->sum('cantidad_usada');
            }
        }

        return response()->json($datosGraficos);
    }

    /**
     * Obtiene el historial completo de movimientos (vacaciones + salidas)
     */
    public function historialCompleto(Request $request)
    {
        $user = auth()->user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return redirect('/')->withErrors('No se encontró información del usuario.');
        }

        // Movimientos de vacaciones
        $movimientosVacacion = VacacionMovimiento::whereHas('periodo', function($q) use ($persona) {
            $q->where('persona_id', $persona->id);
        })
        ->with(['periodo'])
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();

        // Salidas (movimientos de beneficios)
        $salidas = Salida::where('persona_id', $persona->id)
            ->with(['tipoSalida', 'periodo'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('empleado.beneficios.historial', compact(
            'movimientosVacacion',
            'salidas',
            'persona'
        ));
    }
}
