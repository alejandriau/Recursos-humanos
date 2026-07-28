<?php
// app/Console/Commands/GenerarAsistencia.php

namespace App\Console\Commands;

use App\Services\GenerarAsistenciaService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerarAsistencia extends Command
{
    protected $signature = 'asistencia:generar {fecha_inicio?} {fecha_fin?}';
    protected $description = 'Genera/regenera la asistencia diaria cruzando marcaciones, horarios y salidas aprobadas';

    public function handle()
    {
        $inicio = $this->argument('fecha_inicio') ?? Carbon::yesterday()->toDateString();
        $fin = $this->argument('fecha_fin') ?? $inicio;

        $this->info("Generando asistencia de {$inicio} a {$fin}...");

        $service = new GenerarAsistenciaService();
        $resultado = $service->generarParaRango($inicio, $fin);

        foreach ($resultado as $dia) {
            $this->line("  {$dia['fecha']}: {$dia['procesadas']} procesadas, {$dia['errores']} errores");
        }

        $this->info('Listo.');
        return 0;
    }
}
