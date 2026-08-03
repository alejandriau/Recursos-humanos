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
use App\Notifications\GenericNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role; // si usas spatie

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


    // ============================================================
    // APROBAR INDIVIDUAL (RRHH) — AQUÍ SÍ SE DESCUENTAN DÍAS DE VACACIONES
    // ============================================================
    public function aprobarIndividual(Request $request, $id)
    {
        $solicitud = Salida::with(['persona', 'tiposalida', 'periodo'])->findOrFail($id);

        if ($solicitud->estado_rrhh != 'pendiente') {
            return response()->json(['error' => 'Esta solicitud ya fue procesada'], 400);
        }

        $request->validate(['observacion' => 'nullable|string|max:500']);

        DB::beginTransaction();
        try {
            // Solo VACACIONES descuentan días al aprobar RRHH
            if ($this->esVacacion($solicitud) && $solicitud->periodo_id) {
                $periodo = VacacionPeriodo::find($solicitud->periodo_id);

                if (!$periodo) {
                    return response()->json(['error' => 'Período de vacación no encontrado'], 404);
                }

                if ($periodo->saldo_disponible < $solicitud->cantidad) {
                    return response()->json([
                        'error' => "Saldo insuficiente. Disponible: {$periodo->saldo_disponible} días"
                    ], 403);
                }

                $saldo_anterior = $periodo->saldo_disponible;
                $nuevo_saldo = $saldo_anterior - $solicitud->cantidad;

                $periodo->dias_usados += $solicitud->cantidad;
                $periodo->saldo_disponible = $nuevo_saldo;
                if ($nuevo_saldo <= 0) $periodo->estado = 'agotado';
                $periodo->save();

                // Actualizar o crear movimiento
                $mov = VacacionMovimiento::where('salida_id', $solicitud->id)->first();
                if ($mov) {
                    $mov->saldo_posterior = $nuevo_saldo;
                    $mov->descripcion = 'Vacación aprobada por RRHH - Días descontados';
                    $mov->registrado_por = Auth::id();
                    $mov->save();
                } else {
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
                }
            }

            // Actualizar solicitud
            $solicitud->estado_rrhh = 'aprobado';
            $solicitud->estado = 'aprobado';
            $solicitud->fecha_aprobacion_rrhh = now();

            $rrhh = Persona::where('user_id', Auth::id())->first();
            if ($rrhh) $solicitud->rrhh_id = $rrhh->id;

            $solicitud->observacion_rrhh = $request->observacion;
            $solicitud->save();

            // NOTIFICAR AL EMPLEADO
            $this->notificarEmpleado($solicitud, 'aprobada_rrhh');

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => 'Solicitud aprobada por RRHH',
                'saldo_disponible' => $periodo->saldo_disponible ?? null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al aprobar: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================================
    // RECHAZAR INDIVIDUAL (RRHH)
    // ============================================================
    public function rechazarIndividual(Request $request, $id)
    {
        try {
            $solicitud = Salida::with(['persona', 'tiposalida', 'periodo'])->findOrFail($id);

            if ($solicitud->estado_rrhh != 'pendiente') {
                return response()->json(['error' => 'Esta solicitud ya fue procesada'], 400);
            }

            $request->validate(['observacion' => 'required|string|max:500']);

            DB::beginTransaction();

            $solicitud->estado_rrhh = 'rechazado';
            $solicitud->estado = 'rechazado';
            $solicitud->fecha_aprobacion_rrhh = now();

            $rrhh = Persona::where('user_id', Auth::id())->first();
            if ($rrhh) $solicitud->rrhh_id = $rrhh->id;

            $solicitud->observacion_rrhh = $request->observacion;
            $solicitud->save();

            // Solo vacaciones actualizan movimiento
            if ($this->esVacacion($solicitud) && $solicitud->periodo_id) {
                $mov = VacacionMovimiento::where('salida_id', $solicitud->id)->first();
                if ($mov) {
                    $mov->descripcion = 'Rechazado por RRHH: ' . $request->observacion;
                    $mov->registrado_por = Auth::id();
                    $mov->save();
                } else {
                    $periodo = $solicitud->periodo;
                    if ($periodo) {
                        VacacionMovimiento::create([
                            'periodo_id' => $periodo->id,
                            'tipo' => 'debito',
                            'fecha' => now(),
                            'fecha_inicio' => $solicitud->fechasal,
                            'fecha_fin' => $solicitud->fecharet,
                            'cantidad' => $solicitud->cantidad,
                            'saldo_anterior' => $periodo->saldo_disponible,
                            'saldo_posterior' => $periodo->saldo_disponible,
                            'salida_id' => $solicitud->id,
                            'descripcion' => 'Solicitud rechazada por RRHH: ' . $request->observacion,
                            'registrado_por' => Auth::id(),
                        ]);
                    }
                }
            }

            // NOTIFICAR AL EMPLEADO
            $this->notificarEmpleado($solicitud, 'rechazada_rrhh', $request->observacion);

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => 'Solicitud rechazada por RRHH'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    // APROBAR MASIVO (RRHH)
    // ============================================================
    public function aprobarMasivo(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:salidas,id',
            'observacion' => 'nullable|string|max:500',
        ]);

        $solicitudes = Salida::whereIn('id', $request->ids)
            ->where('estado_rrhh', 'pendiente')
            ->with(['tiposalida', 'periodo', 'persona'])
            ->get();

        if ($solicitudes->count() == 0) {
            return response()->json(['error' => 'No hay solicitudes válidas'], 400);
        }

        $aprobadas = 0;
        $errores = [];

        DB::beginTransaction();
        try {
            foreach ($solicitudes as $solicitud) {
                try {
                    // Solo vacaciones descuentan días
                    if ($this->esVacacion($solicitud) && $solicitud->periodo_id) {
                        $periodo = VacacionPeriodo::find($solicitud->periodo_id);

                        if (!$periodo || $periodo->saldo_disponible < $solicitud->cantidad) {
                            $errores[] = "ID {$solicitud->id}: Saldo insuficiente";
                            continue;
                        }

                        $saldo_anterior = $periodo->saldo_disponible;
                        $nuevo_saldo = $saldo_anterior - $solicitud->cantidad;

                        $periodo->dias_usados += $solicitud->cantidad;
                        $periodo->saldo_disponible = $nuevo_saldo;
                        if ($nuevo_saldo <= 0) $periodo->estado = 'agotado';
                        $periodo->save();

                        VacacionMovimiento::where('salida_id', $solicitud->id)
                            ->update([
                                'saldo_posterior' => $nuevo_saldo,
                                'descripcion' => 'Vacación aprobada por RRHH (masivo)',
                                'registrado_por' => Auth::id(),
                            ]);
                    }

                    $solicitud->estado_rrhh = 'aprobado';
                    $solicitud->estado = 'aprobado';
                    $solicitud->fecha_aprobacion_rrhh = now();

                    $rrhh = Persona::where('user_id', Auth::id())->first();
                    if ($rrhh) $solicitud->rrhh_id = $rrhh->id;

                    $solicitud->observacion_rrhh = $request->observacion;
                    $solicitud->save();

                    $this->notificarEmpleado($solicitud, 'aprobada_rrhh');
                    $aprobadas++;

                } catch (\Exception $e) {
                    $errores[] = "ID {$solicitud->id}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => "Se aprobaron {$aprobadas} solicitudes",
                'aprobadas' => $aprobadas,
                'errores' => $errores
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error masivo: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    // HELPERS PRIVADOS (RRHH) — mismos nombres, mismos resultados
    // ============================================================

    private function esVacacion($solicitud): bool
    {
        return strtoupper($solicitud->tiposalida->descripcion ?? '') === 'VACACION';
    }

    private function nombreTipo($solicitud): string
    {
        return $solicitud->tiposalida->descripcion ?? 'Solicitud';
    }

    private function notificarEmpleado($solicitud, string $accion, string $motivo = null): void
    {
        $user = $solicitud->persona->user ?? null;
        if (!$user) return;

        $tipo = $this->nombreTipo($solicitud);
        $fechas = "{$solicitud->fechasal->format('d/m/Y')} al {$solicitud->fecharet->format('d/m/Y')}";

        $data = match($accion) {
            'aprobada_jefe' => [
                'titulo' => "{$tipo} aprobada por tu jefe",
                'mensaje' => "Tu {$tipo} ({$fechas}) fue aprobada por tu jefe. Ahora está pendiente de RRHH.",
                'tipo' => 'solicitud_aprobada',
                'url' => '/empleado/vacacion/mi-historial',
            ],
            'rechazada_jefe' => [
                'titulo' => "{$tipo} rechazada por tu jefe",
                'mensaje' => "Tu {$tipo} fue rechazada. Motivo: " . ($motivo ?: 'Sin observación'),
                'tipo' => 'solicitud_rechazada',
                'url' => '/empleado/vacacion/mi-historial',
            ],
            'aprobada_rrhh' => [
                'titulo' => "¡{$tipo} aprobada definitivamente!",
                'mensaje' => "Tu {$tipo} ({$fechas}) fue aprobada por Recursos Humanos.",
                'tipo' => 'solicitud_aprobada',
                'url' => '/empleado/vacacion/mi-historial',
            ],
            'rechazada_rrhh' => [
                'titulo' => "{$tipo} rechazada por RRHH",
                'mensaje' => "Tu {$tipo} fue rechazada por RRHH. Motivo: " . ($motivo ?: 'Sin observación'),
                'tipo' => 'solicitud_rechazada',
                'url' => '/empleado/vacacion/mi-historial',
            ],
            default => [
                'titulo' => 'Actualización de solicitud',
                'mensaje' => 'Hay una actualización en tu solicitud.',
                'tipo' => 'info',
                'url' => '#',
            ]
        };

        $user->notify(new GenericNotification($data));
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
