<?php
// app/Services/ImportarBiometricosService.php

namespace App\Services;

use App\Models\DispositivoBiometrico;
use Illuminate\Support\Facades\Log;

class ImportarBiometricosService
{
    /**
     * Importa marcaciones de TODOS los dispositivos activos, uno por uno
     * (no en paralelo, para no saturar la red/el equipo de RRHH).
     *
     * Devuelve un resumen por dispositivo para poder mostrar en pantalla
     * cuáles fallaron y cuáles no, sin que un dispositivo caído tumbe
     * la importación de los demás.
     */
    public function importarTodos($fechaInicio = null, $fechaFin = null): array
    {
        $dispositivos = DispositivoBiometrico::where('activo', true)->get();
        $resultados = [];

        foreach ($dispositivos as $dispositivo) {
            $resultados[$dispositivo->id] = $this->importarUno($dispositivo, $fechaInicio, $fechaFin);
        }

        return $resultados;
    }

    public function importarUno(DispositivoBiometrico $dispositivo, $fechaInicio = null, $fechaFin = null): array
    {
        Log::info("Importando desde dispositivo #{$dispositivo->id} ({$dispositivo->nombre}) {$dispositivo->ip}:{$dispositivo->puerto}");

        try {
            $zk = new ZKTecoService($dispositivo->ip, $dispositivo->puerto, $dispositivo->timeout);
            $resultado = $zk->importarMarcaciones($fechaInicio, $fechaFin, $dispositivo->id);

            $dispositivo->update([
                'ultima_sincronizacion' => now(),
                'ultimo_estado' => isset($resultado['error']) ? 'error' : 'exito',
            ]);

            return array_merge($resultado, [
                'dispositivo' => $dispositivo->nombre,
                'ip' => $dispositivo->ip,
            ]);

        } catch (\Exception $e) {
            Log::error("Error importando dispositivo #{$dispositivo->id}: " . $e->getMessage());

            $dispositivo->update([
                'ultima_sincronizacion' => now(),
                'ultimo_estado' => 'error',
            ]);

            return [
                'error' => $e->getMessage(),
                'dispositivo' => $dispositivo->nombre,
                'ip' => $dispositivo->ip,
            ];
        }
    }
}
