<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jmrashed\Zkteco\Lib\ZKTeco;

class DiagnosticarBiometrico extends Command
{
    protected $signature = 'biometrico:diagnosticar {ip} {--port=4370}';
    protected $description = 'Diagnostica conexión y datos de un biométrico ZKTeco';

    public function handle()
    {
        $ip = $this->argument('ip');
        $port = $this->option('port');

        $zk = new ZKTeco($ip, $port, 60);

        $this->info("Conectando a {$ip}:{$port}...");
        if (!$zk->connect()) {
            $this->error('No se pudo conectar');
            return 1;
        }
        $this->info('✔ Conectado');

        // Info del dispositivo (verifica que responde de verdad)
        $this->line('Serial: ' . var_export($zk->serialNumber(), true));
        $this->line('DeviceName: ' . var_export($zk->deviceName(), true));
        $this->line('Platform: ' . var_export($zk->platform(), true));
        $this->line('Version: ' . var_export($zk->version(), true));

        // Intentar despertarlo (bug del sleep mode del K40)
        $this->info('Enviando resume()...');
        $this->line('Resume: ' . var_export($zk->resume(), true));

        $zk->disableDevice();

        $usuarios = $zk->getUser();
        $this->line('Usuarios en equipo: ' . (is_array($usuarios) ? count($usuarios) : var_export($usuarios, true)));

        $marcaciones = $zk->getAttendance();
        $this->line('getAttendance(): ' . gettype($marcaciones));
        $this->line('Total registros: ' . (is_array($marcaciones) ? count($marcaciones) : var_export($marcaciones, true)));

        if (is_array($marcaciones) && count($marcaciones) > 0) {
            $this->info('Primer registro:');
            dump(reset($marcaciones));
            $this->info('Último registro:');
            dump(end($marcaciones));
        } else {
            // Último intento: versión saneada por si el parsing falla
            $limpias = $zk->getSanitizedAttendance();
            $this->line('getSanitizedAttendance(): ' . (is_array($limpias) ? count($limpias) : var_export($limpias, true)));
        }

        $zk->enableDevice();
        $zk->disconnect();
        $this->info('Listo.');

        return 0;
    }
}