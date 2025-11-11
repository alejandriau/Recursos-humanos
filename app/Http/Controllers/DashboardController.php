<?php

namespace App\Http\Controllers;

use App\Models\UnidadOrganizacional;
use App\Models\Puesto;
use App\Models\Historial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Vacacion;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        // Verificar si el usuario tiene el rol de empleado
        if ($user->hasRole('empleado') || $user->role === 'empleado') {
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

        try {
            $estadisticas = [
                'total_unidades' => UnidadOrganizacional::where('esActivo', true)->count(),
                'total_puestos' => Puesto::where('esActivo', true)->count(),
                'puestos_vacantes' => Puesto::where('esActivo', true)->doesntHave('historialActivo')->count(),
                'puestos_ocupados' => Puesto::where('esActivo', true)->has('historialActivo')->count(),
                'jefaturas' => Puesto::where('esActivo', true)->where('esJefatura', true)->count(),
                'jefaturas_vacantes' => Puesto::where('esActivo', true)
                                            ->where('esJefatura', true)
                                            ->doesntHave('historialActivo')
                                            ->count(),
                'unidades_por_tipo' => UnidadOrganizacional::where('esActivo', true)
                                    ->selectRaw('tipo, COUNT(*) as total')
                                    ->groupBy('tipo')
                                    ->get(),
                'puestos_por_contrato' => Puesto::where('esActivo', true)
                                    ->selectRaw('tipoContrato, COUNT(*) as total')
                                    ->groupBy('tipoContrato')
                                    ->get(),
                'movimientos_recientes' => Historial::with(['persona', 'puesto'])
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get()
            ];

            $ultimasUnidades = UnidadOrganizacional::with('padre')
                                ->where('esActivo', true)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

            $ultimosPuestos = Puesto::with(['unidadOrganizacional', 'historialActivo.persona'])
                                ->where('esActivo', true)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

            return view('admin.dashboards.index', compact('estadisticas', 'ultimasUnidades', 'ultimosPuestos'));

        } catch (\Exception $e) {
            // En caso de error, mostrar estadísticas básicas
            $estadisticas = [
                'total_unidades' => UnidadOrganizacional::where('esActivo', true)->count(),
                'total_puestos' => Puesto::where('esActivo', true)->count(),
                'puestos_vacantes' => Puesto::where('esActivo', true)->count(),
                'puestos_ocupados' => 0,
                'jefaturas' => Puesto::where('esActivo', true)->where('esJefatura', true)->count(),
                'jefaturas_vacantes' => Puesto::where('esActivo', true)->where('esJefatura', true)->count(),
                'unidades_por_tipo' => collect(),
                'puestos_por_contrato' => collect(),
                'movimientos_recientes' => collect()
            ];

            $ultimasUnidades = collect();
            $ultimosPuestos = collect();

            return view('admin.dashboards.index', compact('estadisticas', 'ultimasUnidades', 'ultimosPuestos'))
                   ->with('warning', 'Algunos datos no pudieron ser cargados correctamente.');
        }
    }



}
