<?php
// app/Console/Commands/AsistenciaCierreDiario.php

namespace App\Console\Commands;

use App\Jobs\GenerarAsistenciaJob;
use App\Models\AsistenciaDiaria;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AsistenciaCierreDiario extends Command
{
    protected $signature = 'asistencia:cierre-diario {--fecha=}';
    protected $description = 'Cierra asistencias del día anterior: pendientes -> faltas definitivas';

    public function handle(): int
    {
        // Por defecto cierra el día de ayer
        $fecha = $this->option('fecha') ?? Carbon::yesterday()->toDateString();

        $this->info("Cerrando asistencias del {$fecha}...");

        // Re-procesar TODAS las personas para esa fecha (ahora es "día pasado", así que
        // el servicio marcará faltas reales en lugar de pendientes)
        GenerarAsistenciaJob::dispatch($fecha, $fecha);

        // Opcional: si querés forzar síncrono para ver errores
        // $job = new GenerarAsistenciaJob($fecha, $fecha);
        // $job->handle(app(\App\Services\GenerarAsistenciaService::class));

        $this->info('Job encolado para cierre definitivo.');

        return self::SUCCESS;
    }
}