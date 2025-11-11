<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Vacacion;
use App\Models\Historial;
use App\Models\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmpleadoDashboardController extends Controller
{
    public function index()
    {
        $personaId = Auth::user()->persona->id;

        // Obtener puesto actual
        $puestoActual = Historial::with(['puesto.unidadOrganizacional'])
            ->where('persona_id', $personaId)
            ->where('estado', 'activo')
            ->first()
            ?->puesto;

        // Estadísticas
        $estadisticas = [
            'asistencias_mes' => Asistencia::where('idPersona', $personaId)
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->where('estado', 'presente')
                ->count(),

            'dias_vacaciones' => 30 - Vacacion::where('idPersona', $personaId)
                ->whereYear('fecha_inicio', now()->year)
                ->where('estado', 'aprobado')
                ->sum('dias_tomados'),

            'horas_extras_mes' => Asistencia::where('idPersona', $personaId)
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->sum('horas_extras'),
        ];

        // Asistencias recientes
        $asistenciasRecientes = Asistencia::where('idPersona', $personaId)
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get();

        // Vacaciones recientes
        $vacacionesRecientes = Vacacion::where('idPersona', $personaId)
            ->orderBy('fecha_inicio', 'desc')
            ->limit(3)
            ->get();

        // Vacaciones pendientes
        $vacacionesPendientes = Vacacion::where('idPersona', $personaId)
            ->where('estado', 'pendiente')
            ->get();

        return view('empleado.dashboard', compact(
            'puestoActual',
            'estadisticas',
            'asistenciasRecientes',
            'vacacionesRecientes',
            'vacacionesPendientes'
        ));
    }
}
