<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VacacionService;

class GenerarPeriodosVacacion extends Command
{
    protected $signature = 'vacaciones:generar-periodos';
    protected $description = 'Genera los períodos de vacaciones pendientes y procesa vencimientos';

    public function handle(VacacionService $service)
    {
        $this->info('Iniciando generación de períodos de vacaciones...');

        $resultado = $service->generarPeriodosPendientes();

        $this->info("Períodos generados: {$resultado['generados']}");
        $this->info("Vencimientos procesados: {$resultado['vencidos']}");
        $this->info('Proceso finalizado.');
    }
}