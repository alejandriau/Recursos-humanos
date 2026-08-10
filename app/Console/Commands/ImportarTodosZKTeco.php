<?php

namespace App\Console\Commands;

use App\Models\DispositivoBiometrico;
use App\Services\ZKTecoService;
use App\Jobs\GenerarAsistenciaJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportarTodosZKTeco extends Command
{
    protected $signature = 'zk:importar-todos
                            {--sync : Ejecutar asistencia sincrónicamente (para debug)}';

    protected $description = 'Descarga marcaciones de TODOS los biométricos activos y regenera asistencias';

    public function handle(): int
    {
        $dispositivos = DispositivoBiometrico::where('activo', true)->get();

        if ($dispositivos->isEmpty()) {
            $this->error('❌ No hay biométricos activos registrados.');
            return 1;
        }

        $this->newLine();
        $this->info("🔌 Biométricos activos encontrados: {$dispositivos->count()}");
        $this->newLine();

        $totalNuevas = 0;
        $dispositivosOk = 0;
        $dispositivosFallidos = 0;
        $rangoInicioGlobal = null;
        $rangoFinGlobal = null;

        foreach ($dispositivos as $dispositivo) {
            $this->line("──────────────────────────────────────────");
            $this->line("📡 {$dispositivo->nombre} | {$dispositivo->ip}:{$dispositivo->puerto}");

            try {
                $zk = new ZKTecoService(
                    $dispositivo->ip,
                    $dispositivo->puerto ?? 4370,
                    $dispositivo->timeout ?? 60
                );

                // SIN conexión de prueba: importarMarcaciones ya conecta por dentro
                $resultado = $zk->importarMarcaciones(null, null, $dispositivo->id);

                if (isset($resultado['error'])) {
                    $this->error("   ❌ Error: {$resultado['error']}");
                    $dispositivosFallidos++;
                    
                    $dispositivo->update([
                        'ultimo_estado' => 'error_importacion',
                        'ultima_sincronizacion' => now(),
                    ]);
                    continue;
                }

                $this->info("   ✅ OK | Nuevas: {$resultado['nuevas_importadas']} | Dup: {$resultado['duplicadas']}");
                $totalNuevas += $resultado['nuevas_importadas'];
                $dispositivosOk++;

                // Acumular el rango más amplio para regenerar asistencia
                $fi = $resultado['fecha_inicio'] ?? null;
                $ff = $resultado['fecha_fin'] ?? null;

                if ($fi && (!$rangoInicioGlobal || $fi < $rangoInicioGlobal)) {
                    $rangoInicioGlobal = $fi;
                }
                if ($ff && (!$rangoFinGlobal || $ff > $rangoFinGlobal)) {
                    $rangoFinGlobal = $ff;
                }

                $dispositivo->update([
                    'ultimo_estado' => 'ok',
                    'ultima_sincronizacion' => now(),
                ]);

            } catch (\Exception $e) {
                $this->error("   ❌ Excepción: " . $e->getMessage());
                $dispositivosFallidos++;
                Log::error("Error biométrico {$dispositivo->id}: " . $e->getMessage());
                
                $dispositivo->update([
                    'ultimo_estado' => 'error',
                    'ultima_sincronizacion' => now(),
                ]);
            }
        }

        $this->newLine();
        $this->line("══════════════════════════════════════════");
        $this->info("📊 RESUMEN DESCARGA");
        $this->line("   ✅ Conectados: {$dispositivosOk}");
        $this->line("   ❌ Fallidos: {$dispositivosFallidos}");
        $this->line("   📥 Nuevas marcaciones: {$totalNuevas}");

        // ============================================================
        // RE-GENERAR ASISTENCIA DE TODO EL RANGO DESCARGADO
        // ============================================================
        if ($rangoInicioGlobal && $rangoFinGlobal) {
            $this->newLine();
            $this->info("🔄 Re-generando asistencias del {$rangoInicioGlobal} al {$rangoFinGlobal}...");

            if ($this->option('sync')) {
                $job = new GenerarAsistenciaJob($rangoInicioGlobal, $rangoFinGlobal);
                $job->handle(app(\App\Services\GenerarAsistenciaService::class));
                $this->info("✅ Asistencias generadas sincrónicamente.");
            } else {
                GenerarAsistenciaJob::dispatch($rangoInicioGlobal, $rangoFinGlobal);
                $this->info("✅ Job encolado. Se procesará en segundo plano.");
            }
        } else {
            $this->warn("⚠️ No se descargó nada nuevo, no se regenera asistencia.");
        }

        $this->newLine();
        $this->info("🎉 Proceso completado!");

        return 0;
    }
}