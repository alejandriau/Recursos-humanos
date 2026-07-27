<?php

namespace App\Http\Controllers;

use App\Models\UnidadOrganizacional;
use App\Models\Puesto;
use App\Models\Historial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\VacacionPeriodo;
use App\Models\Salida;
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
            $user = Auth::user();
            $persona = Persona::where('user_id', $user->id)->first();

            if (!$persona) {
                return redirect('/')->withErrors('No se encontró información del usuario.');
            }

            // Puesto actual
            $puestoActual = Historial::with(['puesto.unidadOrganizacional'])
                ->where('persona_id', $persona->id)
                ->where('estado', 'activo')
                ->first()
                ?->puesto;

            // Estadísticas básicas
            $estadisticas = [
                'asistencias_mes' => Asistencia::where('idPersona', $persona->id)
                    ->whereMonth('fecha', now()->month)
                    ->whereYear('fecha', now()->year)
                    ->where('estado', 'presente')
                    ->count(),

                'dias_vacaciones' => VacacionPeriodo::where('persona_id', $persona->id)
                    ->where('estado', 'activo')
                    ->sum('saldo_disponible'),

                'horas_extras_mes' => Asistencia::where('idPersona', $persona->id)
                    ->whereMonth('fecha', now()->month)
                    ->whereYear('fecha', now()->year)
                    ->sum('horas_extras'),
            ];

            // Vacaciones pendientes
            $vacacionesPendientes = Salida::where('persona_id', $persona->id)
                ->whereHas('tipoSalida', function($q) {
                    $q->where('descripcion', 'LIKE', '%VACACION%');
                })
                ->whereIn('estado', ['pendiente_jefe', 'pendiente_rrhh'])
                ->get();

            // Comisiones pendientes
            $comisionesPendientes = Salida::where('persona_id', $persona->id)
                ->whereHas('tipoSalida', function($q) {
                    $q->where('descripcion', 'LIKE', '%COMISION%');
                })
                ->whereIn('estado', ['pendiente_jefe', 'pendiente_rrhh'])
                ->count();

            // Solicitudes recientes (todas)
            $solicitudesRecientes = Salida::where('persona_id', $persona->id)
                ->with('tipoSalida')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Próximos vencimientos de beneficios (ejemplo)
            $proximosVencimientos = collect(); // Aquí podrías calcular según tu lógica

            return view('empleado.dashboard', compact(
                'puestoActual',
                'estadisticas',
                'vacacionesPendientes',
                'comisionesPendientes',
                'solicitudesRecientes',
                'proximosVencimientos'
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
