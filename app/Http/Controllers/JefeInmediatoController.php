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

    /**
     * Aprobar una solicitud individual
     */
    public function aprobarIndividual(Request $request, $id)
    {
        $jefe = Persona::where('user_id', Auth::id())->first();

        $solicitud = Salida::with(['persona', 'tiposalida'])->findOrFail($id);

        // Verificar autorización
        if ($solicitud->jefe_id != $jefe->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if ($solicitud->estado_jefe != 'pendiente') {
            return response()->json(['error' => 'Esta solicitud ya fue procesada'], 400);
        }

        $request->validate([
            'observacion' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $solicitud->estado_jefe = 'aprobado';
            $solicitud->fecha_aprobacion_jefe = now();
            $solicitud->observacion_jefe = $request->observacion;

            // Cambiar a pendiente de RRHH
            $solicitud->estado = 'pendiente_rrhh';
            $solicitud->estado_rrhh = 'pendiente';

            $solicitud->save();

            // Registrar en movimientos si es vacación
            if ($solicitud->tiposalida->descripcion == 'Vacación' && $solicitud->periodo_id) {
                VacacionMovimiento::where('salida_id', $solicitud->id)
                    ->update([
                        'descripcion' => 'Aprobado por jefe - En espera de RRHH',
                        'registrado_por' => Auth::id(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => 'Solicitud aprobada correctamente. Pasa a revisión de RRHH.',
                'solicitud' => $solicitud
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al aprobar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Rechazar una solicitud individual
     */
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

        $request->validate([
            'observacion' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $solicitud->estado_jefe = 'rechazado';
            $solicitud->fecha_aprobacion_jefe = now();
            $solicitud->observacion_jefe = $request->observacion;
            $solicitud->estado = 'rechazado';
            $solicitud->estado_rrhh = 'rechazado';
            $solicitud->save();

            // Actualizar movimiento
            if ($solicitud->tiposalida->descripcion == 'Vacación' && $solicitud->periodo_id) {
                VacacionMovimiento::where('salida_id', $solicitud->id)
                    ->update([
                        'descripcion' => 'Rechazado por jefe: ' . $request->observacion,
                        'registrado_por' => Auth::id(),
                    ]);
            }

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

    /**
     * Aprobación masiva de solicitudes
     */
    public function aprobarMasivo(Request $request)
    {
        $jefe = Persona::where('user_id', Auth::id())->first();

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:salidas,id',
            'observacion' => 'nullable|string|max:500',
        ]);

        $ids = $request->ids;

        // Verificar que todas las solicitudes pertenezcan al jefe
        $solicitudes = Salida::whereIn('id', $ids)
            ->where('jefe_id', $jefe->id)
            ->where('estado_jefe', 'pendiente')
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
                    $solicitud->estado_jefe = 'aprobado';
                    $solicitud->fecha_aprobacion_jefe = now();
                    $solicitud->observacion_jefe = $request->observacion;
                    $solicitud->estado = 'pendiente_rrhh';
                    $solicitud->estado_rrhh = 'pendiente';
                    $solicitud->save();

                    // Actualizar movimiento si es vacación
                    if ($solicitud->tiposalida->descripcion == 'Vacación' && $solicitud->periodo_id) {
                        VacacionMovimiento::where('salida_id', $solicitud->id)
                            ->update([
                                'descripcion' => 'Aprobado por jefe (masivo) - En espera de RRHH',
                                'registrado_por' => Auth::id(),
                            ]);
                    }

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
