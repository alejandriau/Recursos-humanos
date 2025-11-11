<?php

namespace App\Http\Controllers;

use App\Models\Vacacion;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VacacionAdminController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $estado = $request->input('estado');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        $vacaciones = Vacacion::with('persona')
            ->when($buscar, function ($query) use ($buscar) {
                $query->whereHas('persona', function ($q) use ($buscar) {
                    $q->where(function ($subQuery) use ($buscar) {
                        $subQuery->where(DB::raw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat)"), 'LIKE', "%$buscar%")
                                ->orWhere('nombre', 'LIKE', "%$buscar%")
                                ->orWhere('apellidoPat', 'LIKE', "%$buscar%")
                                ->orWhere('apellidoMat', 'LIKE', "%$buscar%");
                    });
                });
            })
            ->when($estado, function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
                $query->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin]);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $estados = Vacacion::ESTADOS;
        $totalPendientes = Vacacion::pendientes()->count();

        return view('admin.vacaciones.index', compact(
            'vacaciones',
            'buscar',
            'estado',
            'fecha_inicio',
            'fecha_fin',
            'estados',
            'totalPendientes'
        ));
    }

    public function show(Vacacion $vacacion)
    {
        $vacacion->load('persona');
        $diasDisponibles = $this->calcularDiasDisponibles($vacacion->idPersona);

        return view('admin.vacaciones.show', compact('vacacion', 'diasDisponibles'));
    }

    public function aprobar(Request $request, Vacacion $vacacion)
    {
        $request->validate([
            'comentario' => 'nullable|string|max:500',
        ]);

        // Validar días disponibles
        $diasDisponibles = $this->calcularDiasDisponibles($vacacion->idPersona);

        if ($vacacion->dias_tomados > $diasDisponibles) {
            return back()->with('error',
                "El empleado no tiene suficientes días disponibles.
                 Días solicitados: {$vacacion->dias_tomados},
                 Días disponibles: {$diasDisponibles}");
        }

        DB::transaction(function () use ($vacacion, $request) {
            $vacacion->update([
                'estado' => Vacacion::ESTADO_APROBADO,
                'motivo_rechazo' => $request->comentario,
                'fecha_aprobacion' => now(),
            ]);

            // Aquí podrías agregar notificación por email
            // Notification::send($vacacion->persona->user, new VacacionAprobada($vacacion));
        });

        return back()->with('success', 'Vacaciones aprobadas correctamente');
    }

    public function rechazar(Request $request, Vacacion $vacacion)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);

        $vacacion->update([
            'estado' => Vacacion::ESTADO_RECHAZADO,
            'motivo_rechazo' => $request->motivo_rechazo,
            'fecha_aprobacion' => now(),
        ]);

        // Aquí podrías agregar notificación por email
        // Notification::send($vacacion->persona->user, new VacacionRechazada($vacacion));

        return back()->with('success', 'Vacaciones rechazadas correctamente');
    }

    public function reporte()
    {
        $totalVacaciones = Vacacion::count();
        $vacacionesAprobadas = Vacacion::aprobados()->count();
        $vacacionesPendientes = Vacacion::pendientes()->count();
        $vacacionesRechazadas = Vacacion::rechazados()->count();

        // Estadísticas por mes del año actual
        $vacacionesPorMes = Vacacion::whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total, estado')
            ->groupBy('mes', 'estado')
            ->orderBy('mes')
            ->get();

        // Empleados con más vacaciones aprobadas
        $empleadosConMasVacaciones = Vacacion::aprobados()
            ->selectRaw('idPersona, SUM(dias_tomados) as total_dias')
            ->with('persona')
            ->groupBy('idPersona')
            ->orderByDesc('total_dias')
            ->limit(10)
            ->get();

        return view('admin.vacaciones.reporte', compact(
            'totalVacaciones',
            'vacacionesAprobadas',
            'vacacionesPendientes',
            'vacacionesRechazadas',
            'vacacionesPorMes',
            'empleadosConMasVacaciones'
        ));
    }

    /**
     * Calcula los días de vacaciones disponibles para una persona
     */
    private function calcularDiasDisponibles($idPersona)
    {
        // Días base por año (podrías hacer esto configurable)
        $diasBasePorAnio = 15;

        // Calcular antigüedad para días adicionales
        $persona = Persona::find($idPersona);
        $diasAdicionales = $this->calcularDiasPorAntiguedad($persona->fechaIngreso);

        $diasAcumulados = $diasBasePorAnio + $diasAdicionales;

        // Días ya tomados este año
        $diasTomados = Vacacion::where('idPersona', $idPersona)
            ->where('estado', Vacacion::ESTADO_APROBADO)
            ->whereYear('fecha_inicio', now()->year)
            ->sum('dias_tomados');

        return max(0, $diasAcumulados - $diasTomados);
    }

    /**
     * Calcula días adicionales basados en la antigüedad
     */
    private function calcularDiasPorAntiguedad($fechaIngreso)
    {
        if (!$fechaIngreso) return 0;

        $aniosAntiguedad = now()->diffInYears($fechaIngreso);

        if ($aniosAntiguedad >= 10) return 5;
        if ($aniosAntiguedad >= 5) return 3;
        if ($aniosAntiguedad >= 3) return 1;

        return 0;
    }

    /**
     * Método adicional para vista rápida de días disponibles
     */
    public function diasDisponibles($idPersona)
    {
        $dias = $this->calcularDiasDisponibles($idPersona);
        return response()->json(['dias_disponibles' => $dias]);
    }
}
