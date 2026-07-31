<?php

namespace App\Console\Commands;

use App\Jobs\GenerarAsistenciaJob;
use Illuminate\Console\Command;

class GenerarAsistenciasCommand extends Command
{
    protected $signature = 'asistencia:generar
                            {--fecha= : Fecha específica (YYYY-MM-DD)}
                            {--inicio= : Fecha inicio rango}
                            {--fin= : Fecha fin rango}
                            {--persona= : ID de persona específica}
                            {--sync : Ejecutar sincrónicamente sin queue}';

    protected $description = 'Genera/regenera asistencias diarias';

    public function handle(): int
    {
        $fecha = $this->option('fecha');
        $inicio = $this->option('inicio') ?? $fecha;
        $fin = $this->option('fin') ?? $fecha;
        $personaId = $this->option('persona');

        if (!$inicio) {
            // Por defecto: procesar el día anterior (para asegurar que todas las marcaciones llegaron)
            $inicio = now()->subDay()->toDateString();
            $fin = $inicio;
        }

        $this->info("Generando asistencias del {$inicio} al {$fin}...");

        if ($this->option('sync')) {
            $job = new GenerarAsistenciaJob($inicio, $fin, $personaId);
            $job->handle(app(\App\Services\GenerarAsistenciaService::class));
        } else {
            GenerarAsistenciaJob::dispatch($inicio, $fin, $personaId);
            $this->info('Job encolado. Se procesará en segundo plano.');
        }

        $this->info('Listo.');
        return self::SUCCESS;
    }
}