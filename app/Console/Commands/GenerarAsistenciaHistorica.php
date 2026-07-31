<?php
// app/Console/Commands/GenerarAsistenciaHistorica.php

namespace App\Console\Commands;

use App\Jobs\GenerarAsistenciaJob;
use App\Models\MarcacionBiometrica;
use App\Models\PersonaHorario;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerarAsistenciaHistorica extends Command
{
    protected $signature = 'asistencia:generar-historico {--inicio=} {--fin=} {--sync}';
    protected $description = 'Genera la asistencia de TODO el histórico ya importado (primera carga)';

    public function handle()
    {
        // Si no se especifica, calcula el rango automáticamente:
        // desde lo más antiguo entre (primera marcación importada) y
        // (primera asignación de horario), hasta hoy.
        $inicio = $this->option('inicio');
        $fin = $this->option('fin') ?? Carbon::today()->toDateString();

        if (!$inicio) {
            $primeraMarcacion = MarcacionBiometrica::min('fecha_hora');
            $primerHorario = PersonaHorario::min('fecha_inicio');

            $candidatos = array_filter([$primeraMarcacion, $primerHorario]);

            if (empty($candidatos)) {
                $this->error('No hay marcaciones importadas ni horarios asignados todavía. Nada que generar.');
                return 1;
            }

            $inicio = Carbon::parse(min($candidatos))->toDateString();
        }

        $this->info("Generando asistencia histórica desde {$inicio} hasta {$fin}...");
        $this->info('Esto puede tardar varios minutos dependiendo de cuántos empleados y días haya.');

        if ($this->option('sync')) {
            $job = new GenerarAsistenciaJob($inicio, $fin);
            $job->handle(app(\App\Services\GenerarAsistenciaService::class));
            $this->info('Listo (ejecutado de forma síncrona).');
        } else {
            GenerarAsistenciaJob::dispatch($inicio, $fin);
            $this->info('Job encolado. Asegúrate de tener corriendo: php artisan queue:work');
        }

        return 0;
    }
}