<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\VacacionPeriodo;
use App\Models\VacacionMovimiento;
use App\Models\ConfigDiasVacacion;
use App\Models\Salida;
use App\Models\Gestion;
use App\Notifications\VacacionAsignadaNotification;
use App\Notifications\VacacionVencidaNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VacacionService
{
    /**
     * Genera los períodos pendientes para todos los empleados activos.
     */
    public function generarPeriodosPendientes()
    {
        $generados = 0;
        $vencidos = 0;

        $personales = Persona::where('estado', true)->get();

        foreach ($personales as $personal) {
            $gestion = $this->getGestionActual();
            if (!$gestion) continue;

            $fechaIngreso = Carbon::parse($personal->fechaIngreso);
            $hoy = Carbon::now();

            // Años cumplidos desde la fecha de ingreso
            $aniosCumplidos = (int) floor($fechaIngreso->diffInYears($hoy));
            if ($aniosCumplidos < 1) continue;

            $numeroPeriodo = $aniosCumplidos;
            $fechaHabilitacion = $fechaIngreso->copy()->addYears($numeroPeriodo)->addDay();

            if ($fechaHabilitacion->gt($hoy)) continue;

            // Verificar si ya existe período para este número
            $existe = VacacionPeriodo::where('persona_id', $personal->id)
                ->where('numero_periodo', $numeroPeriodo)
                ->exists();
            if ($existe) continue;

            // Procesar vencimientos (devuelve el período que se venció, o null si no hubo)
            $periodoVencido = $this->procesarVencimientos($personal, $numeroPeriodo);
            if ($periodoVencido) {
                $vencidos++;
            }

            // Calcular antigüedad total a la fecha de habilitación (usando CAS o fecha de ingreso)
            $aniosAntiguedad = $this->getAntiguedadTotal($personal, $fechaHabilitacion);
            $diasAsignados = $this->getDiasPorAntiguedad($aniosAntiguedad);

            // Arrastre del período anterior
            $periodoAnterior = VacacionPeriodo::where('persona_id', $personal->id)
                ->where('numero_periodo', $numeroPeriodo - 1)
                ->first();
            $arrastre = 0;
            if ($periodoAnterior && $periodoAnterior->saldo_disponible > 0) {
                $arrastre = $periodoAnterior->saldo_disponible;
            }

            $casId = $personal->ultimoCas?->id ?? null;

            // Crear el período
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
                'observacion' => null,
            ]);

            // Registrar movimientos (tipo credito = asignación; el debito solo se usa al consumir, ver descontarSaldo)
            $this->registrarMovimiento(
                $periodo,
                'credito',
                $diasAsignados,
                0,
                $diasAsignados,
                null,
                'Asignación anual por antigüedad'
            );

            if ($arrastre > 0) {
                $this->registrarMovimiento(
                    $periodo,
                    'arrastre',
                    $arrastre,
                    $diasAsignados,
                    $diasAsignados + $arrastre,
                    null,
                    'Arrastre del período anterior'
                );
            }

            $generados++;

            // Notificar al empleado: asignación, vencimiento, o ambas combinadas si pasaron en el mismo ciclo
            $this->notificarEmpleado($personal, $periodo, $periodoVencido);
        }

        return ['generados' => $generados, 'vencidos' => $vencidos];
    }

    /**
     * Procesa vencimientos: si hay más de 2 períodos con saldo, vence el más antiguo.
     *
     * @return VacacionPeriodo|null El período que se venció, o null si no hubo vencimiento.
     */
    protected function procesarVencimientos(Persona $personal, int $numeroPeriodoActual): ?VacacionPeriodo
    {
        $periodosActivos = VacacionPeriodo::where('persona_id', $personal->id)
            ->where('estado', 'activo')
            ->where('saldo_disponible', '>', 0)
            ->orderBy('numero_periodo')
            ->get();

        if ($periodosActivos->count() <= 2) {
            return null;
        }

        $periodoAVencer = $periodosActivos->first();

        // Evitar vencer el período inmediato anterior (para arrastre)
        if ($periodoAVencer->numero_periodo == $numeroPeriodoActual - 1) {
            $periodoAVencer = $periodosActivos->skip(1)->first();
        }

        if (!$periodoAVencer || $periodoAVencer->saldo_disponible <= 0) {
            return null;
        }

        $cantidadAVencer = $periodoAVencer->saldo_disponible;
        $saldoAnterior = $periodoAVencer->saldo_disponible;

        $periodoAVencer->dias_vencidos += $cantidadAVencer;
        $periodoAVencer->saldo_disponible = 0;
        $periodoAVencer->periodo_vencido = true;
        $periodoAVencer->estado = 'vencido';
        $periodoAVencer->save();

        $this->registrarMovimiento(
            $periodoAVencer,
            'vencimiento',
            $cantidadAVencer,
            $saldoAnterior,
            0,
            null,
            'Vencimiento por acumulación máxima (más de 2 períodos)'
        );

        return $periodoAVencer;
    }

    /**
     * Envía la notificación correspondiente al empleado:
     * - Si venció un período Y se asignó uno nuevo en el mismo ciclo -> notificación combinada.
     * - Si solo se asignó uno nuevo -> notificación de asignación.
     * (Vencimiento sin asignación nueva no ocurre en este flujo, pero se deja previsto.)
     *
     * OJO: asumo que Persona tiene una relación `user` (belongsTo/hasOne) hacia el modelo
     * Notifiable (users). Si tu Persona no tiene esa relación, o el modelo Notifiable es
     * otro, ajusta la línea `$personal->user` por la que corresponda en tu sistema.
     */
    protected function notificarEmpleado(Persona $personal, VacacionPeriodo $periodoNuevo, ?VacacionPeriodo $periodoVencido): void
    {
        $notifiable = $personal->user ?? null;

        if (!$notifiable) {
            Log::warning("No se pudo notificar a persona_id={$personal->id}: no tiene usuario asociado.");
            return;
        }

        if ($periodoVencido) {
            $notifiable->notify(new VacacionVencidaNotification($periodoVencido, $periodoNuevo));
        } else {
            $notifiable->notify(new VacacionAsignadaNotification($periodoNuevo));
        }
    }

    /**
     * Descuenta saldo de una solicitud de vacación aprobada (FIFO).
     */
    public function descontarSaldo(Salida $salida)
    {
        $tipo = $salida->tipoSalida;
        if (!$tipo->usa_antiguedad) {
            return;
        }

        $personal = $salida->persona;
        $cantidadSolicitada = $salida->cantidad;

        if ($cantidadSolicitada <= 0) {
            throw new \Exception('La cantidad debe ser mayor a 0');
        }

        $periodos = VacacionPeriodo::where('persona_id', $personal->id)
            ->where('estado', 'activo')
            ->where('saldo_disponible', '>', 0)
            ->orderBy('numero_periodo')
            ->get();

        if ($periodos->isEmpty()) {
            throw new \Exception('El empleado no tiene saldo disponible');
        }

        $totalDisponible = $periodos->sum('saldo_disponible');
        if ($totalDisponible < $cantidadSolicitada) {
            throw new \Exception("Saldo insuficiente. Disponible: {$totalDisponible}, Solicitado: {$cantidadSolicitada}");
        }

        $restante = $cantidadSolicitada;

        DB::transaction(function () use ($periodos, $restante, $salida) {
            $pendiente = $restante;
            foreach ($periodos as $periodo) {
                if ($pendiente <= 0) break;

                $disponible = $periodo->saldo_disponible;
                $aDescontar = min($pendiente, $disponible);
                $saldoAnterior = $periodo->saldo_disponible;
                $nuevoSaldo = $saldoAnterior - $aDescontar;

                $periodo->saldo_disponible = $nuevoSaldo;
                $periodo->dias_usados += $aDescontar;
                if ($nuevoSaldo == 0 && $periodo->dias_asignados == $periodo->dias_usados) {
                    $periodo->estado = 'agotado';
                }
                $periodo->save();

                $this->registrarMovimiento(
                    $periodo,
                    'debito',
                    $aDescontar,
                    $saldoAnterior,
                    $nuevoSaldo,
                    $salida->id,
                    "Descuento por solicitud #{$salida->id}"
                );

                $pendiente -= $aDescontar;
            }

            if ($pendiente > 0) {
                throw new \Exception("No se pudo descontar completamente la solicitud. Restante: {$pendiente}");
            }
        });
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
        ?int $salidaId = null,
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
            'salida_id' => $salidaId,
            'descripcion' => $descripcion,
            'registrado_por' => auth()->id() ?? null,
        ]);
    }

    /**
     * Calcula la antigüedad total en años (parte entera) para un empleado
     * a partir de los campos del CAS o de la fecha de ingreso.
     */
    protected function getAntiguedadTotal(Persona $persona, ?Carbon $fechaReferencia = null): int
    {
        $cas = $persona->ultimoCas;

        if ($cas) {
            // Usar los campos del CAS
            $anios = $cas->anios_servicio ?? 0;
            $meses = $cas->meses_servicio ?? 0;
            $dias = $cas->dias_servicio ?? 0;

            // Convertir todo a años (decimal)
            $totalAniosDecimal = $anios + ($meses / 12) + ($dias / 365);

            // Si se proporciona una fecha de referencia (fecha de habilitación),
            // sumar los años completos transcurridos desde la fecha de cálculo del CAS
            if ($fechaReferencia && $cas->fecha_calculo_antiguedad) {
                $fechaBase = Carbon::parse($cas->fecha_calculo_antiguedad);
                $aniosAdicionales = (int) floor($fechaBase->diffInYears($fechaReferencia));
                $totalAniosDecimal += $aniosAdicionales;
            }

            return (int) floor($totalAniosDecimal);
        }

        // Sin CAS: usar fecha de ingreso
        if (!$fechaReferencia) {
            $fechaReferencia = Carbon::now();
        }
        $fechaIngreso = Carbon::parse($persona->fechaIngreso);
        return (int) floor($fechaIngreso->diffInYears($fechaReferencia));
    }

    /**
     * Obtiene los días de vacación según años de antigüedad.
     */
    protected function getDiasPorAntiguedad(int $anios): int
    {
        $config = ConfigDiasVacacion::where('anios_desde', '<=', $anios)
            ->where(function ($q) use ($anios) {
                $q->where('anios_hasta', '>=', $anios)
                    ->orWhereNull('anios_hasta');
            })
            ->where('activo', true)
            ->first();

        return $config ? $config->dias : 15;
    }

    /**
     * Obtiene la gestión actual (activa o del año en curso).
     */
    protected function getGestionActual()
    {
        // Primero intentar por estado 'habilitado'
        $gestion = Gestion::where('estado', 'habilitado')->first();
        if ($gestion) {
            return $gestion;
        }

        // Si no, buscar por año actual
        $anioActual = now()->year;
        return Gestion::where('anio', $anioActual)->first();
    }

    /**
     * Verifica si un empleado tiene saldo suficiente.
     */
    public function tieneSaldoDisponible(Persona $personal, float $cantidad): bool
    {
        $total = VacacionPeriodo::where('persona_id', $personal->id)
            ->where('estado', 'activo')
            ->sum('saldo_disponible');

        return $total >= $cantidad;
    }

    /**
     * Obtiene el saldo total disponible.
     */
    public function getSaldoTotal(Persona $personal): float
    {
        return VacacionPeriodo::where('persona_id', $personal->id)
            ->where('estado', 'activo')
            ->sum('saldo_disponible');
    }

    /**
     * Obtiene el detalle de períodos con saldo.
     */
    public function getPeriodosConSaldo(Persona $personal)
    {
        return VacacionPeriodo::where('persona_id', $personal->id)
            ->where('estado', 'activo')
            ->where('saldo_disponible', '>', 0)
            ->orderBy('numero_periodo')
            ->get(['id', 'numero_periodo', 'fecha_habilitacion', 'dias_asignados', 'dias_usados', 'saldo_disponible']);
    }
}