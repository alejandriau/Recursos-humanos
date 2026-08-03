<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Persona;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\GenericNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role; // si usas spatie


class JefeInmediatoController extends Controller
{
    /**
     * Dashboard del Jefe Inmediato - Lista todas las solicitudes pendientes
     */
    public function dashboard()
    {
        $jefe = Persona::where('user_id', Auth::id())->first();

        if (!$jefe) {
            return redirect()->back()->with('error', 'No tiene perfil de jefe asignado');
        }

        // Obtener todos los subordinados del jefe
        //$subordinados = Persona::where('jefe_id', $jefe->id)->pluck('id');

        // Solicitudes pendientes de aprobación del jefe
        $solicitudesPendientes = Salida::whereIn('jefe_id', [$jefe->id])
            ->where('estado', 'pendiente_jefe')
            ->where('estado_jefe', 'pendiente')
            ->with(['persona', 'tiposalida', 'jefe'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Solicitudes ya procesadas (historial)
        $solicitudesProcesadas = Salida::whereIn('jefe_id', [$jefe->id])
            ->where('estado_jefe', '!=', 'pendiente')
            ->with(['persona', 'tiposalida', 'jefe'])
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        // Estadísticas
        $estadisticas = [
            'total_pendientes' => $solicitudesPendientes->count(),
            'aprobadas' => Salida::whereIn('jefe_id', [$jefe->id])
                ->where('estado_jefe', 'aprobado')
                ->count(),
            'rechazadas' => Salida::whereIn('jefe_id', [$jefe->id])
                ->where('estado_jefe', 'rechazado')
                ->count(),
            'vacaciones_pendientes' => $solicitudesPendientes->where('tiposalida.descripcion', 'Vacación')->count(),
            'otras_salidas_pendientes' => $solicitudesPendientes->where('tiposalida.descripcion', '!=', 'Vacación')->count(),
        ];

        return view('empleado.jefe.dashboard', compact(
            'solicitudesPendientes',
            'solicitudesProcesadas',
            'estadisticas',
            'jefe'
        ));
    }

    /**
     * Ver detalle de una solicitud específica
     */
    public function verSolicitud($id)
    {
        $jefe = Persona::where('user_id', Auth::id())->first();

        $solicitud = Salida::with(['persona', 'tiposalida', 'jefe', 'rrhh'])
            ->findOrFail($id);

        // Verificar que el jefe tenga autoridad sobre esta solicitud
        if ($solicitud->jefe_id != $jefe->id) {
            abort(403, 'No autorizado para ver esta solicitud');
        }

        // Obtener historial de movimientos si es vacación
        $movimientos = null;
        if ($solicitud->tiposalida->descripcion == 'Vacación' && $solicitud->periodo_id) {
            $movimientos = VacacionMovimiento::where('periodo_id', $solicitud->periodo_id)
                ->where('salida_id', $solicitud->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('jefe.ver-solicitud', compact('solicitud', 'movimientos'));
    }

// ============================================================
// APROBAR INDIVIDUAL (JEFE)
// ============================================================
public function aprobarIndividual(Request $request, $id)
{
    $jefe = Persona::where('user_id', Auth::id())->first();
    $solicitud = Salida::with(['persona', 'tiposalida'])->findOrFail($id);

    if ($solicitud->jefe_id != $jefe->id) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    if ($solicitud->estado_jefe != 'pendiente') {
        return response()->json(['error' => 'Esta solicitud ya fue procesada'], 400);
    }

    $request->validate(['observacion' => 'nullable|string|max:500']);

    DB::beginTransaction();
    try {
        $solicitud->estado_jefe = 'aprobado';
        $solicitud->fecha_aprobacion_jefe = now();
        $solicitud->observacion_jefe = $request->observacion;
        $solicitud->estado = 'pendiente_rrhh';
        $solicitud->estado_rrhh = 'pendiente';
        $solicitud->save();

        // Solo vacaciones actualizan movimientos (pero aún NO descuentan días)
        if ($this->esVacacion($solicitud) && $solicitud->periodo_id) {
            VacacionMovimiento::where('salida_id', $solicitud->id)
                ->update([
                    'descripcion' => 'Aprobado por jefe - En espera de RRHH',
                    'registrado_por' => Auth::id(),
                ]);
        }

        // NOTIFICAR AL EMPLEADO
        $this->notificarEmpleado($solicitud, 'aprobada_jefe');

        // NOTIFICAR A RRHH
        $this->notificarRRHH($solicitud, 'nueva_pendiente');

        DB::commit();

        return response()->json([
            'success' => true,
            'mensaje' => 'Solicitud aprobada. Pasa a revisión de RRHH.',
            'solicitud' => $solicitud
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al aprobar: ' . $e->getMessage()], 500);
    }
}

// ============================================================
// RECHAZAR INDIVIDUAL (JEFE)
// ============================================================
public function rechazarIndividual(Request $request, $id)
{
    $jefe = Persona::where('user_id', Auth::id())->first();
    $solicitud = Salida::with(['persona', 'tiposalida'])->findOrFail($id);

    if ($solicitud->jefe_id != $jefe->id) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    if ($solicitud->estado_jefe != 'pendiente') {
        return response()->json(['error' => 'Esta solicitud ya fue procesada'], 400);
    }

    $request->validate(['observacion' => 'required|string|max:500']);

    DB::beginTransaction();
    try {
        $solicitud->estado_jefe = 'rechazado';
        $solicitud->fecha_aprobacion_jefe = now();
        $solicitud->observacion_jefe = $request->observacion;
        $solicitud->estado = 'rechazado';
        $solicitud->estado_rrhh = 'rechazado';
        $solicitud->save();

        if ($this->esVacacion($solicitud) && $solicitud->periodo_id) {
            VacacionMovimiento::where('salida_id', $solicitud->id)
                ->update([
                    'descripcion' => 'Rechazado por jefe: ' . $request->observacion,
                    'registrado_por' => Auth::id(),
                ]);
        }

        // NOTIFICAR AL EMPLEADO
        $this->notificarEmpleado($solicitud, 'rechazada_jefe', $request->observacion);

        DB::commit();

        return response()->json([
            'success' => true,
            'mensaje' => 'Solicitud rechazada correctamente',
            'solicitud' => $solicitud
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al rechazar: ' . $e->getMessage()], 500);
    }
}

// ============================================================
// APROBAR MASIVO (JEFE)
// ============================================================
public function aprobarMasivo(Request $request)
{
    $jefe = Persona::where('user_id', Auth::id())->first();

    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:salidas,id',
        'observacion' => 'nullable|string|max:500',
    ]);

    $solicitudes = Salida::whereIn('id', $request->ids)
        ->where('jefe_id', $jefe->id)
        ->where('estado_jefe', 'pendiente')
        ->with(['persona', 'tiposalida'])
        ->get();

    if ($solicitudes->count() == 0) {
        return response()->json(['error' => 'No hay solicitudes válidas'], 400);
    }

    $aprobadas = 0;
    $errores = [];
    $notificarRRHH = false;

    DB::beginTransaction();
    try {
        foreach ($solicitudes as $solicitud) {
            try {
                $solicitud->estado_jefe = 'aprobado';
                $solicitud->fecha_aprobacion_jefe = now();
                $solicitud->observacion_jefe = $request->observacion;
                $solicitud->estado = 'pendiente_rrhh';
                $solicitud->estado_rrhh = 'pendiente';
                $solicitud->save();

                if ($this->esVacacion($solicitud) && $solicitud->periodo_id) {
                    VacacionMovimiento::where('salida_id', $solicitud->id)
                        ->update([
                            'descripcion' => 'Aprobado por jefe (masivo) - En espera de RRHH',
                            'registrado_por' => Auth::id(),
                        ]);
                }

                $this->notificarEmpleado($solicitud, 'aprobada_jefe');
                $notificarRRHH = true;
                $aprobadas++;

            } catch (\Exception $e) {
                $errores[] = "ID {$solicitud->id}: " . $e->getMessage();
            }
        }

        if ($notificarRRHH) {
            $this->notificarRRHHMasivo($aprobadas);
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
// HELPERS PRIVADOS (JEFE)
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

private function notificarRRHH($solicitud, string $tipoNotif): void
{
    $usuariosRRHH = \App\Models\User::role('admin')->get();
    if ($usuariosRRHH->count() === 0) return;

    $nombre = "{$solicitud->persona->nombre} {$solicitud->persona->apellidoPat}";
    $tipo = $this->nombreTipo($solicitud);

    Notification::send($usuariosRRHH, new GenericNotification([
        'titulo' => "{$tipo} por aprobar (RRHH)",
        'mensaje' => "{$nombre} tiene una {$tipo} aprobada por jefe. Revisar para aprobación final.",
        'tipo' => 'solicitud_pendiente',
        'url' => '/solicitudes/dashboard',
    ]));
}

private function notificarRRHHMasivo(int $cantidad): void
{
    $usuariosRRHH = \App\Models\User::role('admin')->get();
    if ($usuariosRRHH->count() === 0) return;

    Notification::send($usuariosRRHH, new GenericNotification([
        'titulo' => 'Nuevas solicitudes por aprobar',
        'mensaje' => "Hay {$cantidad} solicitud(es) aprobadas por jefe esperando revisión de RRHH.",
        'tipo' => 'solicitud_pendiente',
        'url' => '/solicitudes/dashboard',
    ]));
}
    /**
     * Filtrar solicitudes por tipo
     */
    public function filtrarPorTipo($tipo)
    {
        $jefe = Persona::where('user_id', Auth::id())->first();
        $subordinados = Persona::where('jefe_id', $jefe->id)->pluck('id');

        $solicitudes = Salida::whereIn('persona_id', $subordinados)
            ->where('estado_jefe', 'pendiente')
            ->whereHas('tiposalida', function($q) use ($tipo) {
                if ($tipo == 'vacacion') {
                    $q->where('descripcion', 'Vacación');
                } else if ($tipo == 'otros') {
                    $q->where('descripcion', '!=', 'Vacación');
                }
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
     * Buscar solicitudes por nombre o CI
     */
    public function buscar(Request $request)
    {
        $jefe = Persona::where('user_id', Auth::id())->first();
        $subordinados = Persona::where('jefe_id', $jefe->id)->pluck('id');

        $busqueda = $request->get('q');

        $solicitudes = Salida::whereIn('persona_id', $subordinados)
            ->where('estado_jefe', 'pendiente')
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
}
