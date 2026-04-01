<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InmovilidadesLaborales;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteInmovilidadController extends Controller
{
    /**
     * Reporte de inmovilidades activas
     */
    public function inmovilidadesActivas(Request $request)
    {
        $query = InmovilidadesLaborales::with(['situacion.persona'])
            ->where('estado', InmovilidadesLaborales::ESTADO_APROBADO)
            ->where('fecha_fin_inmovilidad', '>=', now());

        if ($request->tipo_inmovilidad) {
            $query->where('tipo_inmovilidad', $request->tipo_inmovilidad);
        }

        if ($request->fecha_desde) {
            $query->where('fecha_inicio_inmovilidad', '>=', $request->fecha_desde);
        }

        if ($request->fecha_hasta) {
            $query->where('fecha_fin_inmovilidad', '<=', $request->fecha_hasta);
        }

        $inmovilidades = $query->orderBy('fecha_fin_inmovilidad')
            ->paginate(20);

        $tipos = InmovilidadesLaborales::$tiposInmovilidad;

        return view('rrhh.reportes.inmovilidades-activas', compact('inmovilidades', 'tipos'));
    }

    /**
     * Reporte de inmovilidades por vencer
     */
    public function inmovilidadesPorVencer(Request $request)
    {
        $dias = $request->dias ?? 30;

        $inmovilidades = InmovilidadesLaborales::with(['situacion.persona'])
            ->where('estado', InmovilidadesLaborales::ESTADO_APROBADO)
            ->where('fecha_fin_inmovilidad', '<=', now()->addDays($dias))
            ->where('fecha_fin_inmovilidad', '>=', now())
            ->orderBy('fecha_fin_inmovilidad')
            ->get();

        return view('rrhh.reportes.inmovilidades-por-vencer', compact('inmovilidades', 'dias'));
    }

    /**
     * Reporte por tipo de inmovilidad
     */
    public function reportePorTipo(Request $request)
    {
        $estadisticas = InmovilidadesLaborales::select(
                'tipo_inmovilidad',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN estado = "aprobado" THEN 1 ELSE 0 END) as aprobadas'),
                DB::raw('SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as pendientes'),
                DB::raw('SUM(CASE WHEN estado = "rechazado" THEN 1 ELSE 0 END) as rechazadas')
            )
            ->whereYear('created_at', $request->anio ?? date('Y'))
            ->groupBy('tipo_inmovilidad')
            ->get();

        return view('rrhh.reportes.por-tipo', compact('estadisticas'));
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportarExcel(Request $request)
    {
        // Implementar exportación a Excel
        // Puedes usar Laravel Excel o similar
    }
}
