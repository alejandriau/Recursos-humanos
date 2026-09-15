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
     * Carga inicial: genera los últimos 2 períodos de vacación de cada empleado
     * (según sus años cumplidos), sin depender de cuántas gestiones existan en la tabla.
     */
    public function generarHistoricoInicial(): array
    {
        $gestiones = Gestion::orderBy('anio')->get();
        if ($gestiones->isEmpty()) {
            throw new \Exception('No hay gestiones registradas. Registra al menos la gestión actual.');
        }
        $gestionActual = $gestiones->last(); // gestión de mayor año

        $generados = 0;
        $vencidos = 0;
        $omitidos = 0;
        $hoy = Carbon::now();

        $personales = Persona::where('estado', true)->get();

        foreach ($personales as $personal) {
            $fechaIngreso = Carbon::parse($personal->fechaIngreso);
            $aniosCumplidos = (int) floor($fechaIngreso->diffInYears($hoy));

            // Los últimos 2 años cumplidos => 2 períodos
            // Ej: 11 años cumplidos => períodos 10 y 11
            $candidatos = array_unique([
                $aniosCumplidos - 1, // período de la gestión anterior
                $aniosCumplidos,     // período de la gestión actual
            ]);

            foreach ($candidatos as $numeroPeriodo) {
                $resultado = $this->generarPeriodo($personal, $numeroPeriodo, $hoy, $gestiones, $gestionActual);

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
     * Genera UN período para un empleado.
     */
    protected function generarPeriodo(
        Persona $personal,
        int $numeroPeriodo,
        Carbon $hoy,
        $gestiones,
        Gestion $gestionActual
    ): string {
        if ($numeroPeriodo < 1) {
            return 'omitido';
        }

        $fechaIngreso = Carbon::parse($personal->fechaIngreso);
        $fechaHabilitacion = $fechaIngreso->copy()->addYears($numeroPeriodo)->addDay();

        // La fecha de habilitación debe haber llegado
        if ($fechaHabilitacion->gt($hoy)) {
            return 'omitido';
        }

        // No duplicar
        $existe = VacacionPeriodo::where('persona_id', $personal->id)
            ->where('numero_periodo', $numeroPeriodo)
            ->exists();
        if ($existe) {
            return 'omitido';
        }

        // Vencimientos (máximo 2 períodos con saldo)
        $huboVencimiento = $this->procesarVencimientos($personal, $numeroPeriodo);

        // Antigüedad A LA FECHA DE HABILITACIÓN (por eso cada período puede tener días distintos)
        $aniosAntiguedad = $this->getAntiguedadTotal($personal, $fechaHabilitacion);
        $diasAsignados = $this->getDiasPorAntiguedad($aniosAntiguedad);

        // La gestión es la del año en que se habilitó el período
        // (si no existe esa gestión en la tabla, se usa la actual)
        $gestion = $gestiones->firstWhere('anio', $fechaHabilitacion->year) ?? $gestionActual;

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
            'saldo_disponible' => $diasAsignados,
            'dias_arrastre' => 0,
            'periodo_vencido' => false,
            'estado' => 'activo',
            'observacion' => 'Carga inicial',
        ]);

        $this->registrarMovimiento(
            $periodo, 'credito', $diasAsignados, 0, $diasAsignados,
            "Asignación anual por antigüedad ({$aniosAntiguedad} años) - carga inicial"
        );


        Log::info("Carga inicial: período {$numeroPeriodo} ({$diasAsignados} días, hab. {$fechaHabilitacion->toDateString()}) para persona_id={$personal->id}");

        return $huboVencimiento ? 'generado_con_vencimiento' : 'generado';
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
     * Antigüedad total en años a una fecha de referencia.
     * CORREGIDO: si la fecha de referencia es ANTERIOR a la fecha de cálculo del CAS,
     * se RESTAN años (no se suman). Esto es clave para la carga inicial:
     * el CAS se calculó hoy, pero el período antiguo se habilitó antes.
     */
    protected function getAntiguedadTotal(Persona $persona, ?Carbon $fechaReferencia = null): int
    {
        $cas = $persona->ultimoCas;

        if (!$fechaReferencia) {
            $fechaReferencia = Carbon::now();
        }

        if ($cas) {
            $anios = $cas->anios_servicio ?? 0;
            $meses = $cas->meses_servicio ?? 0;
            $dias = $cas->dias_servicio ?? 0;

            $total = $anios + ($meses / 12) + ($dias / 365);

            if ($cas->fecha_calculo_antiguedad) {
                $fechaBase = Carbon::parse($cas->fecha_calculo_antiguedad);

                // diff() respeta el signo vía el flag "invert"
                $intervalo = $fechaBase->diff($fechaReferencia);
                $aniosAdicionales = $intervalo->y * ($intervalo->invert ? -1 : 1);

                $total += $aniosAdicionales;
            }

            // Nunca devolver antigüedad negativa
            return max(0, (int) floor($total));
        }

        // Sin CAS: usar fecha de ingreso
        return (int) floor(Carbon::parse($persona->fechaIngreso)->diffInYears($fechaReferencia));
    }

    /**
     * Días de vacación según años de antigüedad.
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
     * Registra un movimiento en el kardex.
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