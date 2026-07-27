<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\VacacionPeriodo;
use App\Models\Salida;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VacacionReporteService
{
    /**
     * Obtener resumen de vacaciones por área
     */
    public function getResumenPorArea(): Collection
    {
        return Persona::where('estado', true)
            ->with(['area', 'vacacionPeriodos' => function($q) {
                $q->where('estado', 'activo');
            }])
            ->get()
            ->groupBy('area_id')
            ->map(function($personas) {
                $area = $personas->first()->area;
                return [
                    'area' => $area ? $area->nombre : 'Sin área',
                    'total_empleados' => $personas->count(),
                    'total_saldo' => $personas->sum(function($p) {
                        return $p->vacacionPeriodos->sum('saldo_disponible');
                    }),
                    'empleados_sin_saldo' => $personas->filter(function($p) {
                        return $p->vacacionPeriodos->sum('saldo_disponible') == 0;
                    })->count(),
                ];
            });
    }

    /**
     * Obtener calendario de vacaciones por mes
     */
    public function getCalendarioMensual(int $year, int $month): Collection
    {
        $inicio = Carbon::create($year, $month, 1);
        $fin = $inicio->copy()->endOfMonth();

        return Salida::where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->where(function($q) use ($inicio, $fin) {
                $q->whereBetween('fechasal', [$inicio, $fin])
                  ->orWhereBetween('fecharet', [$inicio, $fin]);
            })
            ->with(['persona', 'tipoSalida'])
            ->orderBy('fechasal')
            ->get();
    }

    /**
     * Detalle de vencimientos por período
     */
    public function getVencimientosPendientes(): Collection
    {
        return VacacionPeriodo::where('periodo_vencido', true)
            ->where('dias_vencidos', '>', 0)
            ->where('estado', 'vencido')
            ->with('persona')
            ->orderBy('numero_periodo')
            ->get()
            ->map(function($periodo) {
                return [
                    'persona' => $periodo->persona->full_name,
                    'periodo' => $periodo->numero_periodo,
                    'dias_vencidos' => $periodo->dias_vencidos,
                    'fecha_vencimiento' => $periodo->updated_at,
                ];
            });
    }

    /**
     * Verificar si un empleado está de vacaciones en un rango de fechas
     */
    public function estaEnVacaciones(int $personaId, string $fechaInicio, string $fechaFin): bool
    {
        return Salida::where('persona_id', $personaId)
            ->where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->where(function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fechasal', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecharet', [$fechaInicio, $fechaFin])
                  ->orWhere(function($sub) use ($fechaInicio, $fechaFin) {
                      $sub->where('fechasal', '<=', $fechaInicio)
                          ->where('fecharet', '>=', $fechaFin);
                  });
            })
            ->exists();
    }

    /**
     * Proyección de vacaciones para el próximo mes
     */
    public function getProyeccionProximoMes(): Collection
    {
        $proximoMes = now()->addMonth();
        $inicio = $proximoMes->copy()->startOfMonth();
        $fin = $proximoMes->copy()->endOfMonth();

        return Salida::where('tiposalida_id', function($q) {
                $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true);
            })
            ->where('estado', 'aprobado')
            ->where('fechasal', '>=', $inicio)
            ->where('fechasal', '<=', $fin)
            ->with('persona')
            ->orderBy('fechasal')
            ->get()
            ->groupBy('persona_id')
            ->map(function($vacaciones) {
                $persona = $vacaciones->first()->persona;
                return [
                    'persona' => $persona->full_name,
                    'total_dias' => $vacaciones->sum('cantidad'),
                    'fechas' => $vacaciones->map(function($v) {
                        return [
                            'inicio' => $v->fechasal,
                            'fin' => $v->fecharet,
                        ];
                    }),
                ];
            });
    }
}
