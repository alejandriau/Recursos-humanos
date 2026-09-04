<?php

namespace App\Console\Commands;

use App\Models\BeneficioPeriodo;
use App\Models\Gestion;
use App\Models\Persona;
use App\Models\TipoSalida;
use Illuminate\Console\Command;

class ReplicarBeneficiosGestion extends Command
{
    protected $signature = 'beneficios:replicar-gestion';
    protected $description = 'Crea la gestión del nuevo año y replica los beneficios asignados';

    public function handle()
    {
        $anioActual = now()->year;

        // --- 1. Crear gestión si no existe ---
        $gestion = Gestion::firstOrCreate(
            ['anio' => $anioActual],
            [
                'fecha'  => now()->toDateString(),
                'estado' => 'habilitado',
            ]
        );

        // Opcional: cerrar la gestión anterior
        Gestion::where('anio', '<', $anioActual)
            ->where('estado', 'habilitado')
            ->update(['estado' => 'cerrado']);

        // --- 2. ¿Ya se replicó este año? (idempotente) ---
        $yaReplicado = BeneficioPeriodo::where('gestion_id', $gestion->id)->exists();
        if ($yaReplicado) {
            $this->info("La gestión {$anioActual} ya fue replicada.");
            return;
        }

        $gestionAnterior = Gestion::where('anio', $anioActual - 1)->first();
        if (!$gestionAnterior) {
            $this->warn('No existe gestión anterior para replicar.');
            return;
        }

        // --- 3. Replicar beneficios (solo mensuales y anuales) ---
        $beneficiosAnteriores = BeneficioPeriodo::where('gestion_id', $gestionAnterior->id)
            ->where(function ($q) {
                $q->whereNull('mes')          // anuales
                  ->orWhere('mes', 1);        // mensuales: solo enero (los otros meses se van creando)
            })
            ->get();

        $creados = 0;

        foreach ($beneficiosAnteriores as $b) {
            $tipo = TipoSalida::find($b->tiposalida_id);

            // Saltar si el tipo fue desactivado o eliminado
            if (!$tipo || !$tipo->activo) continue;

            // EVENTO no se replica: se crea bajo demanda al aprobar la salida
            if ($tipo->periodicidad === 'evento') continue;

            // VACACION tiene su propio tratamiento
            if ($tipo->usa_tabla_antiguedad) continue;

            // El mes depende de la periodicidad, no de lo que tenía antes
            $mes = $tipo->periodicidad === 'mensual' ? 1 : null;

            // Seguridad extra: no duplicar si ya existe
            $existe = BeneficioPeriodo::where('persona_id', $b->persona_id)
                ->where('tiposalida_id', $b->tiposalida_id)
                ->where('gestion_id', $gestion->id)
                ->where('mes', $mes)
                ->exists();

            if ($existe) continue;

            // Arrastre: lo que no usó del período anual anterior pasa al nuevo
            $arrastre = 0;
            if ($tipo->permite_arrastre && $tipo->periodicidad === 'anual') {
                $arrastre = max(0, $b->saldo_disponible);
            }

            $cantidad = $tipo->cantidad_default ?? $b->cantidad_asignada;

            BeneficioPeriodo::create([
                'persona_id'        => $b->persona_id,
                'tiposalida_id'     => $b->tiposalida_id,
                'gestion_id'        => $gestion->id,
                'mes'               => $mes,
                'unidad'            => $tipo->unidad ?? $b->unidad,
                'cantidad_asignada' => $cantidad,
                'cantidad_usada'    => 0,
                'cantidad_vencida'  => 0,
                'arrastre'          => $arrastre,
                'saldo_disponible'  => $cantidad + $arrastre,
                'fecha_habilitacion'=> now(),
                'estado'            => 'activo',
            ]);

            $creados++;
        }

        $this->info("Gestión {$anioActual} creada. {$creados} beneficios replicados.");
    }
}