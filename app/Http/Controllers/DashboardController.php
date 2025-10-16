<?php

namespace App\Http\Controllers;

use App\Models\UnidadOrganizacional;
use App\Models\Puesto;
use App\Models\Historial;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
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

            return view('admin.dashboard', compact('estadisticas', 'ultimasUnidades', 'ultimosPuestos'));

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
