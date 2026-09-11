<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use App\Models\ConfigDiasVacacion;
use App\Models\Gestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VacacionCargaInicialService
{
    /**
     * Genera los períodos de las 2 últimas gestiones para todos los empleados activos.
     * Procesa de la gestión más antigua a la más nueva para que el arrastre
     * y los vencimientos por acumulación (>2 períodos) queden bien calculados.
     */
    public function generarHistoricoInicial(): array
    {
        // Tomar las 2 últimas gestiones (mayor año) y ordenarlas asc para procesar
        $ultimasDos = Gestion::orderBy('anio', 'desc')
            ->limit(2)
            ->get()
            ->sortBy('anio')
            ->values();

        if ($ultimasDos->isEmpty()) {
            throw new \Exception('No hay gestiones registradas en el sistema.');
        }

        $generados = 0;
        $vencidos = 0;
        $omitidos = 0;
        $hoy = Carbon::now();

        $personales = Persona::where('estado', true)->get();

        foreach ($ultimasDos as $gestion) {
            foreach ($personales as $personal) {
                $resultado = $this->generarPeriodoParaGestion($personal, $gestion, $hoy);

                if ($resultado === 'generado') {
                    $generados++;
                } elseif ($resultado === 'generado_con_vencimiento') {
                    $generados++;
                    $vencidos++;
                } else {
                    $omitidos++;
                }
            }
        }

        return [
            'generados' => $generados,
            'vencidos' => $vencidos,
            'omitidos' => $omitidos,
        ];
    }

    /**
     * Genera UN período de vacación para un empleado en una gestión específica.
     *
     * @return string 'generado' | 'generado_con_vencimiento' | 'omitido'
     */
    protected function generarPeriodoParaGestion(Persona $personal, Gestion $gestion, Carbon $hoy): string
    {
        $fechaIngreso = Carbon::parse($personal->fechaIngreso);
        $anioIngreso = $fechaIngreso->year;

        // El período N se habilita en el año: ingreso + N años
        // => N = año de la gestión - año de ingreso
        $numeroPeriodo = $gestion->anio - $anioIngreso;

        // Si aún no cumplía 1 año en esa gestión, no le corresponde período
        if ($numeroPeriodo < 1) {
            return 'omitido';
        }

        $fechaHabilitacion = $fechaIngreso->copy()->addYears($numeroPeriodo)->addDay();

        // Si la fecha de habilitación aún no llegó, no generar
        if ($fechaHabilitacion->gt($hoy)) {
            return 'omitido';
        }

        // No duplicar si ya existe
        $existe = VacacionPeriodo::where('persona_id', $personal->id)
            ->where('numero_periodo', $numeroPeriodo)
            ->exists();
        if ($existe) {
            return 'omitido';
        }

        // Vencimientos (máximo 2 períodos con saldo) — misma regla que el servicio normal
        $periodoVencido = $this->procesarVencimientos($personal, $numeroPeriodo);

        // Antigüedad a la fecha de habilitación (CAS o fecha de ingreso)
        $aniosAntiguedad = $this->getAntiguedadTotal($personal, $fechaHabilitacion);
        $diasAsignados = $this->getDiasPorAntiguedad($aniosAntiguedad);

        // Arrastre del período anterior
        $periodoAnterior = VacacionPeriodo::where('persona_id', $personal->id)
            ->where('numero_periodo', $numeroPeriodo - 1)
            ->first();
        $arrastre = ($periodoAnterior && $periodoAnterior->saldo_disponible > 0)
            ? $periodoAnterior->saldo_disponible
            : 0;

        $casId = $personal->ultimoCas?->id ?? null;

        $periodo = VacacionPeriodo::create([
            'persona_id' => $personal->id,
            'gestion_id' => $gestion->id,
            'cas_id' => $casId,
            'numero_periodo' => $numeroPeriodo,
            'fecha_habilitacion' => $fechaHabilitacion,
            'anios_antiguedad' => $aniosAntiguedad,
            'dias_asignados' => $diasAsignados,
            'dias_usados' => 0,
            'dias_vencidos' => 0,
            'saldo_disponible' => $diasAsignados + $arrastre,
            'dias_arrastre' => $arrastre,
            'periodo_vencido' => false,
            'estado' => 'activo',
            'observacion' => 'Carga inicial',
        ]);

        // Movimientos en el kardex
        $this->registrarMovimiento(
            $periodo, 'credito', $diasAsignados, 0, $diasAsignados,
            'Asignación anual por antigüedad (carga inicial)'
        );

        if ($arrastre > 0) {
            $this->registrarMovimiento(
                $periodo, 'arrastre', $arrastre, $diasAsignados, $diasAsignados + $arrastre,
                'Arrastre del período anterior'
            );
        }

        Log::info("Carga inicial: período {$numeroPeriodo} generado para persona_id={$personal->id} (gestión {$gestion->anio})");

        return $periodoVencido ? 'generado_con_vencimiento' : 'generado';
    }

    /**
     * Vence el período más antiguo si hay más de 2 períodos con saldo.
     */
    protected function procesarVencimientos(Persona $personal, int $numeroPeriodoActual): bool
    {
        $periodosActivos = VacacionPeriodo::where('persona_id', $personal->id)
            ->where('estado', 'activo')
            ->where('saldo_disponible', '>', 0)
            ->orderBy('numero_periodo')
            ->get();

        if ($periodosActivos->count() <= 2) {
            return false;
        }

        $periodoAVencer = $periodosActivos->first();

        // No vencer el inmediato anterior (necesario para el arrastre)
        if ($periodoAVencer->numero_periodo == $numeroPeriodoActual - 1) {
            $periodoAVencer = $periodosActivos->skip(1)->first();
        }

        if (!$periodoAVencer || $periodoAVencer->saldo_disponible <= 0) {
            return false;
        }

        $cantidadAVencer = $periodoAVencer->saldo_disponible;
        $saldoAnterior = $periodoAVencer->saldo_disponible;

        $periodoAVencer->dias_vencidos += $cantidadAVencer;
        $periodoAVencer->saldo_disponible = 0;
        $periodoAVencer->periodo_vencido = true;
        $periodoAVencer->estado = 'vencido';
        $periodoAVencer->save();

        $this->registrarMovimiento(
            $periodoAVencer, 'vencimiento', $cantidadAVencer, $saldoAnterior, 0,
            'Vencimiento por acumulación máxima (más de 2 períodos)'
        );

        return true;
    }

    /**
     * Antigüedad total en años (parte entera) usando CAS o fecha de ingreso.
     */
    protected function getAntiguedadTotal(Persona $persona, ?Carbon $fechaReferencia = null): int
    {
        $cas = $persona->ultimoCas;

        if ($cas) {
            $anios = $cas->anios_servicio ?? 0;
            $meses = $cas->meses_servicio ?? 0;
            $dias = $cas->dias_servicio ?? 0;

            $totalAniosDecimal = $anios + ($meses / 12) + ($dias / 365);

            if ($fechaReferencia && $cas->fecha_calculo_antiguedad) {
                $fechaBase = Carbon::parse($cas->fecha_calculo_antiguedad);
                $totalAniosDecimal += (int) floor($fechaBase->diffInYears($fechaReferencia));
            }

            return (int) floor($totalAniosDecimal);
        }

        if (!$fechaReferencia) {
            $fechaReferencia = Carbon::now();
        }

        return (int) floor(Carbon::parse($persona->fechaIngreso)->diffInYears($fechaReferencia));
    }

    /**
     * Días de vacación según años de antigüedad (tabla de configuración).
     */
    protected function getDiasPorAntiguedad(int $anios): int
    {
        $config = ConfigDiasVacacion::where('anios_desde', '<=', $anios)
            ->where(function ($q) use ($anios) {
                $q->where('anios_hasta', '>=', $anios)->orWhereNull('anios_hasta');
            })
            ->where('activo', true)
            ->first();

        return $config ? $config->dias : 15;
    }

    /**
     * Registra un movimiento en el kardex (sin notificaciones, es carga inicial).
     */
    protected function registrarMovimiento(
        VacacionPeriodo $periodo,
        string $tipo,
        float $cantidad,
        float $saldoAnterior,
        float $saldoPosterior,
        ?string $descripcion = null
    ) {
        return VacacionMovimiento::create([
            'periodo_id' => $periodo->id,
            'tipo' => $tipo,
            'fecha' => now(),
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'cantidad' => $cantidad,
            'saldo_anterior' => $saldoAnterior,
            'saldo_posterior' => $saldoPosterior,
            'salida_id' => null,
            'descripcion' => $descripcion,
            'registrado_por' => auth()->id() ?? null,
        ]);
    }
}