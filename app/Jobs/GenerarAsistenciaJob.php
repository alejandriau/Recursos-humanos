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

        $personas = $this->personaId
            ? Persona::where('id', $this->personaId)->get()
            : Persona::where('estado', true)->get();

        $totalProcesadas = 0;
        $totalErrores = 0;

        while ($inicio->lte($fin)) {
            foreach ($personas as $persona) {
                try {
                    $service->procesarPersonaFecha($persona, $inicio->copy());
                    $totalProcesadas++;
                } catch (\Exception $e) {
                    $totalErrores++;
                    Log::error("GenerarAsistenciaJob: error persona {$persona->id} fecha {$inicio->toDateString()}: " . $e->getMessage());
                }
            }
            $inicio->addDay();
        }

        Log::info("GenerarAsistenciaJob completado: {$totalProcesadas} procesadas, {$totalErrores} errores. Rango: {$this->fechaInicio} a {$this->fechaFin}" . ($this->personaId ? " (persona {$this->personaId})" : ''));
    }
}