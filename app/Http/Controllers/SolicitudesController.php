<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Persona;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SolicitudesController extends Controller
{
    /**
     * Dashboard de RRHH
     */
    public function dashboard()
    {
        $rrhh = Persona::where('user_id', Auth::id())->first();

        // Solicitudes pendientes de RRHH
        $solicitudesPendientes = Salida::where('estado', 'pendiente_rrhh')
            ->where('estado_rrhh', 'pendiente')
            ->with(['persona', 'tiposalida', 'jefe'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Solicitudes aprobadas por RRHH (últimas 50)
        $solicitudesAprobadas = Salida::where('estado_rrhh', 'aprobado')
            ->with(['persona', 'tiposalida'])
            ->orderBy('fecha_aprobacion_rrhh', 'desc')
            ->limit(50)
            ->get();

        // Solicitudes rechazadas por RRHH (últimas 50)
        $solicitudesRechazadas = Salida::where('estado_rrhh', 'rechazado')
            ->with(['persona', 'tiposalida'])
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        // Estadísticas
        $estadisticas = [
            'total_pendientes' => $solicitudesPendientes->count(),
            'aprobadas_mes' => Salida::where('estado_rrhh', 'aprobado')
                ->whereMonth('fecha_aprobacion_rrhh', now()->month)
                ->count(),
            'rechazadas_mes' => Salida::where('estado_rrhh', 'rechazado')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'vacaciones_pendientes' => $solicitudesPendientes->where('tiposalida.descripcion', 'Vacación')->count(),
            'total_aprobadas' => Salida::where('estado_rrhh', 'aprobado')->count(),
            'total_rechazadas' => Salida::where('estado_rrhh', 'rechazado')->count(),
        ];

        // Resumen de períodos de vacación activos
        $periodosActivos = VacacionPeriodo::where('estado', 'activo')
            ->with('persona')
            ->orderBy('saldo_disponible', 'desc')
            ->limit(10)
            ->get();

        return view('admin.solicitudes.dashboard', compact(
            'solicitudesPendientes',
            'solicitudesAprobadas',
            'solicitudesRechazadas',
            'estadisticas',
            'periodosActivos',
            'rrhh'
        ));
    }

    /**
     * Ver detalle de solicitud
     */
    public function verSolicitud($id)
    {
        $solicitud = Salida::with(['persona', 'tiposalida', 'jefe', 'rrhh'])
            ->findOrFail($id);

        // Obtener período de vacación si aplica
        $periodo = null;
        $movimientos = null;

        if ($solicitud->tiposalida->descripcion == 'Vacación' && $solicitud->periodo_id) {
            $periodo = VacacionPeriodo::with('persona')
                ->find($solicitud->periodo_id);

            $movimientos = VacacionMovimiento::where('periodo_id', $solicitud->periodo_id)
                ->where('salida_id', $solicitud->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.solicitudes.ver-solicitud', compact('solicitud', 'periodo', 'movimientos'));
    }

    /**
     * Aprobar solicitud individual (RRHH) - ES AQUÍ DONDE SE DESCUENTAN LOS DÍAS
     */
public function aprobarIndividual(Request $request, $id)
{
    $solicitud = Salida::with(['persona', 'tiposalida', 'periodo'])
        ->findOrFail($id);

    // Verificar que esté pendiente
    if ($solicitud->estado_rrhh != 'pendiente') {
        return response()->json(['error' => 'Esta solicitud ya fue procesada'], 400);
    }

    $request->validate([
        'observacion' => 'nullable|string|max:500',
    ]);

    DB::beginTransaction();
    try {
        // ============================================
        // DEBUG: VERIFICAR DATOS ANTES DE PROCESAR
        // ============================================
        \Log::info('=== APROBANDO SOLICITUD ===');
        \Log::info('ID Solicitud: ' . $solicitud->id);
        \Log::info('Descripción Tipo: ' . $solicitud->tiposalida->descripcion);
        \Log::info('Periodo ID: ' . $solicitud->periodo_id);
        \Log::info('Cantidad: ' . $solicitud->cantidad);

        // Si es vacación, descontar días (USANDO strtoupper para comparar)
        if (strtoupper($solicitud->tiposalida->descripcion) == 'VACACION' && $solicitud->periodo_id) {
            \Log::info('=== PROCESANDO VACACIÓN ===');

            $periodo = VacacionPeriodo::find($solicitud->periodo_id);

            if (!$periodo) {
                \Log::error('Período no encontrado: ' . $solicitud->periodo_id);
                return response()->json(['error' => 'Período de vacación no encontrado'], 404);
            }

            \Log::info('Saldo disponible: ' . $periodo->saldo_disponible);
            \Log::info('Días a descontar: ' . $solicitud->cantidad);

            // Verificar saldo disponible nuevamente
            if ($periodo->saldo_disponible < $solicitud->cantidad) {
                return response()->json([
                    'error' => "Saldo insuficiente. Disponible: {$periodo->saldo_disponible} días"
                ], 403);
            }

            // Descontar días
            $saldo_anterior = $periodo->saldo_disponible;
            $nuevo_saldo = $saldo_anterior - $solicitud->cantidad;

            \Log::info('Nuevo saldo: ' . $nuevo_saldo);

            $periodo->dias_usados += $solicitud->cantidad;
            $periodo->saldo_disponible = $nuevo_saldo;

            if ($nuevo_saldo <= 0) {
                $periodo->estado = 'agotado';
            }
            $periodo->save();

            // Actualizar movimiento (VERIFICAR QUE EXISTA)
            $movimiento = VacacionMovimiento::where('salida_id', $solicitud->id)->first();
            if ($movimiento) {
                $movimiento->saldo_posterior = $nuevo_saldo;
                $movimiento->descripcion = 'Vacación aprobada por RRHH - Días descontados';
                $movimiento->registrado_por = Auth::id();
                $movimiento->save();
                \Log::info('Movimiento actualizado: ' . $movimiento->id);
            } else {
                \Log::warning('No se encontró movimiento para salida_id: ' . $solicitud->id);
                // CREAR MOVIMIENTO SI NO EXISTE
                VacacionMovimiento::create([
                    'periodo_id' => $periodo->id,
                    'tipo' => 'debito',
                    'fecha' => now(),
                    'fecha_inicio' => $solicitud->fechasal,
                    'fecha_fin' => $solicitud->fecharet,
                    'cantidad' => $solicitud->cantidad,
                    'saldo_anterior' => $saldo_anterior,
                    'saldo_posterior' => $nuevo_saldo,
                    'salida_id' => $solicitud->id,
                    'descripcion' => 'Vacación aprobada por RRHH - Días descontados',
                    'registrado_por' => Auth::id(),
                ]);
                \Log::info('Movimiento creado');
            }
        } else {
            \Log::info('No es vacación o no tiene período');
        }

        // Actualizar solicitud
        $solicitud->estado_rrhh = 'aprobado';
        $solicitud->estado = 'aprobado';
        $solicitud->fecha_aprobacion_rrhh = now();

        $rrhh = Persona::where('user_id', Auth::id())->first();
        if ($rrhh) {
            $solicitud->rrhh_id = $rrhh->id;
        } else {
            \Log::warning('No se encontró persona para user_id: ' . Auth::id());
        }

        $solicitud->observacion_rrhh = $request->observacion;
        $solicitud->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'mensaje' => 'Solicitud aprobada por RRHH',
            'saldo_disponible' => $periodo->saldo_disponible ?? null
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error en aprobarIndividual: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        return response()->json([
            'error' => 'Error al aprobar: ' . $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ], 500);
    }
}

    /**
     * Rechazar solicitud individual (RRHH)
     */
public function rechazarIndividual(Request $request, $id)
{
    try {
        // Cargar la solicitud con relaciones necesarias
        $solicitud = Salida::with(['persona', 'tiposalida', 'periodo'])
            ->findOrFail($id);

        if ($solicitud->estado_rrhh != 'pendiente') {
            return response()->json(['error' => 'Esta solicitud ya fue procesada'], 400);
        }

        $request->validate([
            'observacion' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        // Actualizar la solicitud
        $solicitud->estado_rrhh = 'rechazado';
        $solicitud->estado = 'rechazado';
        $solicitud->fecha_aprobacion_rrhh = now();

        $rrhh = Persona::where('user_id', Auth::id())->first();
        if ($rrhh) {
            $solicitud->rrhh_id = $rrhh->id;
        }

        $solicitud->observacion_rrhh = $request->observacion;
        $solicitud->save();

        // Actualizar movimiento si es vacación y existe período
        $esVacacion = strtoupper($solicitud->tiposalida->descripcion) == 'VACACION' ||
                      strtolower($solicitud->tiposalida->descripcion) == 'vacación';

        if ($esVacacion && $solicitud->periodo_id) {
            // Buscar el movimiento
            $movimiento = VacacionMovimiento::where('salida_id', $solicitud->id)->first();

            if ($movimiento) {
                $movimiento->descripcion = 'Rechazado por RRHH: ' . $request->observacion;
                $movimiento->registrado_por = Auth::id();
                $movimiento->save();

                \Log::info('Movimiento actualizado para rechazo: ' . $movimiento->id);
            } else {
                // Si no existe movimiento, lo creamos para registro
                $periodo = $solicitud->periodo; // Usar la relación polimórfica

                if ($periodo) {
                    VacacionMovimiento::create([
                        'periodo_id' => $periodo->id,
                        'tipo' => 'debito',
                        'fecha' => now(),
                        'fecha_inicio' => $solicitud->fechasal,
                        'fecha_fin' => $solicitud->fecharet,
                        'cantidad' => $solicitud->cantidad,
                        'saldo_anterior' => $periodo->saldo_disponible,
                        'saldo_posterior' => $periodo->saldo_disponible, // No se descuenta
                        'salida_id' => $solicitud->id,
                        'descripcion' => 'Solicitud rechazada por RRHH: ' . $request->observacion,
                        'registrado_por' => Auth::id(),
                    ]);

                    \Log::info('Movimiento creado para rechazo');
                }
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'mensaje' => 'Solicitud rechazada por RRHH'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error en rechazarIndividual: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return response()->json([
            'error' => 'Error interno del servidor',
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ], 500);
    }
}

    /**
     * Aprobación masiva de RRHH
     */
    public function aprobarMasivo(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:salidas,id',
            'observacion' => 'nullable|string|max:500',
        ]);

        $ids = $request->ids;

        $solicitudes = Salida::whereIn('id', $ids)
            ->where('estado_rrhh', 'pendiente')
            ->with(['tiposalida', 'periodo'])
            ->get();

        if ($solicitudes->count() == 0) {
            return response()->json(['error' => 'No hay solicitudes válidas para aprobar'], 400);
        }

        $aprobadas = 0;
        $errores = [];

        DB::beginTransaction();
        try {
            foreach ($solicitudes as $solicitud) {
                try {
                    // Si es vacación, descontar días
                    if ($solicitud->tiposalida->descripcion == 'Vacación' && $solicitud->periodo_id) {
                        $periodo = VacacionPeriodo::find($solicitud->periodo_id);

                        if ($periodo && $periodo->saldo_disponible >= $solicitud->cantidad) {
                            $saldo_anterior = $periodo->saldo_disponible;
                            $nuevo_saldo = $saldo_anterior - $solicitud->cantidad;

                            $periodo->dias_usados += $solicitud->cantidad;
                            $periodo->saldo_disponible = $nuevo_saldo;

                            if ($nuevo_saldo <= 0) {
                                $periodo->estado = 'agotado';
                            }
                            $periodo->save();

                            VacacionMovimiento::where('salida_id', $solicitud->id)
                                ->update([
                                    'saldo_posterior' => $nuevo_saldo,
                                    'descripcion' => 'Vacación aprobada por RRHH (masivo)',
                                    'registrado_por' => Auth::id(),
                                ]);
                        } else {
                            $errores[] = "Solicitud ID {$solicitud->id}: Saldo insuficiente";
                            continue;
                        }
                    }

                    $solicitud->estado_rrhh = 'aprobado';
                    $solicitud->estado = 'aprobado';
                    $solicitud->fecha_aprobacion_rrhh = now();
                    $solicitud->rrhh_id = Persona::where('user_id', Auth::id())->first()->id;
                    $solicitud->observacion_rrhh = $request->observacion;
                    $solicitud->save();

                    $aprobadas++;
                } catch (\Exception $e) {
                    $errores[] = "Error en solicitud ID {$solicitud->id}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => "Se aprobaron {$aprobadas} solicitudes correctamente",
                'aprobadas' => $aprobadas,
                'errores' => $errores
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error en aprobación masiva: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Filtrar solicitudes por estado o tipo
     */
    public function filtrar(Request $request)
    {
        $filtro = $request->get('filtro', 'pendientes');

        $query = Salida::with(['persona', 'tiposalida', 'jefe']);

        if ($filtro == 'pendientes') {
            $query->where('estado', 'pendiente_rrhh')
                  ->where('estado_rrhh', 'pendiente');
        } elseif ($filtro == 'aprobados') {
            $query->where('estado_rrhh', 'aprobado');
        } elseif ($filtro == 'rechazados') {
            $query->where('estado_rrhh', 'rechazado');
        } elseif ($filtro == 'vacaciones') {
            $query->whereHas('tiposalida', function($q) {
                $q->where('descripcion', 'Vacación');
            })->where('estado_rrhh', 'pendiente');
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'solicitudes' => $solicitudes,
            'total' => $solicitudes->count()
        ]);
    }

    /**
     * Buscar solicitudes en RRHH
     */
    public function buscar(Request $request)
    {
        $busqueda = $request->get('q');

        $solicitudes = Salida::where('estado_rrhh', 'pendiente')
            ->whereHas('persona', function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
                  ->orWhere('ci', 'LIKE', "%{$busqueda}%");
            })
            ->with(['persona', 'tiposalida'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'solicitudes' => $solicitudes,
            'total' => $solicitudes->count()
        ]);
    }

    /**
     * Obtener estadísticas para dashboard
     */
    public function estadisticas()
    {
        $stats = [
            'pendientes' => Salida::where('estado_rrhh', 'pendiente')->count(),
            'aprobados_hoy' => Salida::where('estado_rrhh', 'aprobado')
                ->whereDate('fecha_aprobacion_rrhh', today())
                ->count(),
            'rechazados_hoy' => Salida::where('estado_rrhh', 'rechazado')
                ->whereDate('updated_at', today())
                ->count(),
            'dias_vacacion_aprobados' => Salida::where('estado_rrhh', 'aprobado')
                ->whereHas('tiposalida', function($q) {
                    $q->where('descripcion', 'Vacación');
                })
                ->sum('cantidad'),
        ];

        return response()->json($stats);
    }

    /**
     * Reporte de períodos de vacación
     */
    public function reportePeriodos()
    {
        $periodos = VacacionPeriodo::with(['persona', 'gestion'])
            ->where('estado', 'activo')
            ->orWhere('estado', 'agotado')
            ->orderBy('saldo_disponible', 'desc')
            ->get();

        return view('rrhh.reporte-periodos', compact('periodos'));
    }
}
