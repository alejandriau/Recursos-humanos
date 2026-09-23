<?php
// app/Jobs/GenerarAsistenciaJob.php

namespace App\Jobs;

use App\Models\Persona;
use App\Services\GenerarAsistenciaService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerarAsistenciaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800; // 30 min, por si el rango es grande
    public int $tries = 1;

    public function __construct(
        public string $fechaInicio,
        public string $fechaFin,
        public ?int $personaId = null,
    ) {}

    public function handle(GenerarAsistenciaService $service): void
    {
        $inicio = Carbon::parse($this->fechaInicio)->startOfDay();
        $fin = Carbon::parse($this->fechaFin)->startOfDay();

        $totalProcesadas = 0;
        $totalOmitidas = 0;
        $totalErrores = 0;

        while ($inicio->lte($fin)) {
            if ($this->personaId) {
                // Una sola persona: se procesa directo, sin pasar por
                // generarParaFecha (que trae a todas las personas activas).
                $persona = Persona::find($this->personaId);

                if ($persona) {
                    try {
                        $asistencia = $service->procesarPersonaFecha($persona, $inicio->copy());
                        $asistencia ? $totalProcesadas++ : $totalOmitidas++;
                    } catch (\Exception $e) {
                        $totalErrores++;
                        Log::error("GenerarAsistenciaJob: error persona {$persona->id} fecha {$inicio->toDateString()}: " . $e->getMessage());
                    }
                } else {
                    Log::warning("GenerarAsistenciaJob: persona {$this->personaId} no encontrada, se omite fecha {$inicio->toDateString()}");
                }
            } else {
                // Todas las personas activas: se reutiliza generarParaFecha,
                // que ya busca el feriado UNA vez por fecha (no por persona)
                // y devuelve el resumen correcto (procesadas/omitidas/errores).
                $resumen = $service->generarParaFecha($inicio->toDateString());

                $totalProcesadas += $resumen['procesadas'];
                $totalOmitidas += $resumen['omitidas'];
                $totalErrores += $resumen['errores'];
            }

            $inicio->addDay();
        }

        Log::info(
            "GenerarAsistenciaJob completado: {$totalProcesadas} procesadas, {$totalOmitidas} omitidas, {$totalErrores} errores. Rango: {$this->fechaInicio} a {$this->fechaFin}"
            . ($this->personaId ? " (persona {$this->personaId})" : '')
        );
    }
}