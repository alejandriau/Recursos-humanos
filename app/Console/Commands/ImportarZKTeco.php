<?php
// app/Console/Commands/ImportarZKTeco.php

namespace App\Console\Commands;

use App\Services\ZKTecoService;
use Illuminate\Console\Command;

class ImportarZKTeco extends Command
{
    protected $signature = 'zk:importar
                            {ip : IP del biométrico}
                            {--port=4370 : Puerto}
                            {--fecha-inicio= : Fecha inicio (YYYY-MM-DD)}
                            {--fecha-fin= : Fecha fin (YYYY-MM-DD)}
                            {--limite=1000 : Límite de marcaciones a importar}';

    protected $description = 'Importar marcaciones desde ZKTeco UFace802 Plus';

    public function handle()
    {
        $ip = $this->argument('ip');
        $port = $this->option('port');

        $this->newLine();
        $this->line('🔌 === ZKTeco UFace802 Plus - Importación ===');
        $this->line("📡 IP: {$ip}:{$port}");

        $zk = new ZKTecoService($ip, $port);

        // Probar conexión
        $this->info("\n⏳ Conectando al dispositivo...");
        if (!$zk->conectar()) {
            $this->error("❌ No se pudo conectar al biométrico");
            return 1;
        }
        $this->info("✅ Conexión exitosa");
        $zk->desconectar();

        // Obtener información del dispositivo
        $this->info("\n📋 Obteniendo información del dispositivo...");
        $info = $zk->obtenerInfoDispositivo();
        if ($info) {
            $this->line("   📌 Serial: {$info['serial']}");
            $this->line("   📌 Modelo: UFace802 Plus");
            $this->line("   📌 Firmware: {$info['firmware']}");
        }

        // Importar marcaciones
        $fechaInicio = $this->option('fecha-inicio');
        $fechaFin = $this->option('fecha-fin');

        $this->info("\n📥 Importando marcaciones...");
        if ($fechaInicio && $fechaFin) {
            $this->line("   Rango: {$fechaInicio} hasta {$fechaFin}");
        } else {
            $this->line("   Rango: Todas las marcaciones");
        }

        $resultado = $zk->importarMarcaciones($fechaInicio, $fechaFin);

        if (isset($resultado['error'])) {
            $this->error("❌ Error: {$resultado['error']}");
            return 1;
        }

        // Mostrar resultados
        $this->newLine();
        $this->line('📊 === RESULTADOS ===');
        $this->info("✅ {$resultado['mensaje']}");
        $this->line("   📌 Obtenidas: {$resultado['total_obtenidas']}");
        $this->line("   📌 Nuevas: {$resultado['nuevas_importadas']}");
        $this->line("   📌 Duplicadas: {$resultado['duplicadas']}");
        $this->line("   📌 Errores: {$resultado['con_error']}");

        $this->newLine();
        $this->info("🎉 Proceso completado!");

        return 0;
    }
}
