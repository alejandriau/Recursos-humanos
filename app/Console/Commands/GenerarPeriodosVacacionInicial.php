<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VacacionPeriodo;
use App\Services\VacacionCargaInicialService;

class GenerarPeriodosVacacionInicial extends Command
{
    protected $signature = 'vacaciones:generar-inicial {--force : Ejecutar aunque ya existan períodos}';

    protected $description = 'Carga inicial: genera los períodos de vacaciones de las 2 últimas gestiones';

    public function handle(VacacionCargaInicialService $service)
    {
        // Protección: solo corre cuando la tabla está vacía (salvo --force)
        if (VacacionPeriodo::exists() && !$this->option('force')) {
            $this->error('¡Atención! Ya existen períodos de vacación registrados.');
            $this->warn('Este comando es SOLO para carga inicial. El flujo normal es:');
            $this->line('  php artisan vacaciones:generar-periodos');
            $this->warn('Si estás seguro de regenerar todo, limpia primero:');
            $this->line('  VacacionMovimiento::truncate(); VacacionPeriodo::truncate();');
            $this->line('  y luego: php artisan vacaciones:generar-inicial --force');
            return Command::FAILURE;
        }

        if (!$this->confirm('¿Generar períodos de las 2 últimas gestiones para todos los empleados activos?')) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }

        $this->info('Iniciando carga inicial de períodos de vacaciones...');

        try {
            $resultado = $service->generarHistoricoInicial();
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("✔ Períodos generados: {$resultado['generados']}");
        $this->info("✔ Vencimientos procesados: {$resultado['vencidos']}");
        $this->warn("○ Omitidos (no aplica o ya existía): {$resultado['omitidos']}");
        $this->info('Carga inicial finalizada. Ahora usa vacaciones:generar-periodos para el flujo normal.');

        return Command::SUCCESS;
    }
}