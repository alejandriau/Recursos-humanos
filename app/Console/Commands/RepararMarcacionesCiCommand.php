<?php
// app/Console/Commands/RepararMarcacionesCi.php

namespace App\Console\Commands;

use App\Models\MarcacionBiometrica;
use App\Models\Persona;
use App\Services\ZKTecoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepararMarcacionesCi extends Command
{
    protected $signature = 'zkteco:reparar-ci {ip} {port=4370}';
    protected $description = 'Backfill de CI y persona_id en marcaciones_biometricas usando la lista de usuarios del biométrico';

    public function handle()
    {
        $ip = $this->argument('ip');
        $port = (int) $this->argument('port');

        $this->info("Conectando a {$ip}:{$port} para traer la lista de usuarios...");

        $zk = new ZKTecoService($ip, $port, 30);
        $usuarios = $zk->obtenerUsuarios();

        if ($usuarios === null) {
            $this->error('No se pudo obtener la lista de usuarios del dispositivo.');
            return 1;
        }

        // Mapa uid_biometrico (del dispositivo) -> ['ci' => ..., 'nombre' => ...]
        $mapaPorUid = [];
        foreach ($usuarios as $u) {
            $uid = $u['uid'] ?? null;
            if ($uid === null) continue;
            $mapaPorUid[(string)$uid] = [
                'ci' => $u['userid'] ?? null,
                'nombre' => $u['name'] ?? null,
            ];
        }

        $this->info('Usuarios en el dispositivo: ' . count($mapaPorUid));

        // Registros a reparar: ci vacío, null o 'desconocido'
        $malos = MarcacionBiometrica::where(function ($q) {
                $q->whereNull('ci')
                  ->orWhere('ci', '')
                  ->orWhere('ci', 'desconocido');
            })
            ->get(['id', 'uid_biometrico']);

        $this->info('Marcaciones a reparar: ' . $malos->count());

        $reparadas = 0;
        $sinCoincidencia = 0;

        foreach ($malos->chunk(500) as $chunk) {
            DB::transaction(function () use ($chunk, $mapaPorUid, &$reparadas, &$sinCoincidencia) {
                foreach ($chunk as $marcacion) {
                    $uid = (string) $marcacion->uid_biometrico;

                    if (!isset($mapaPorUid[$uid])) {
                        $sinCoincidencia++;
                        continue;
                    }

                    $ci = $mapaPorUid[$uid]['ci'];
                    $nombre = $mapaPorUid[$uid]['nombre'];

                    if (!$ci) {
                        $sinCoincidencia++;
                        continue;
                    }

                    $personaId = Persona::where('ci', $ci)->value('id');

                    $marcacion->ci = $ci;
                    $marcacion->nombre_completo = $marcacion->nombre_completo ?: $nombre;
                    $marcacion->persona_id = $personaId;
                    $marcacion->save();

                    $reparadas++;
                }
            });
        }

        $this->info("Reparadas: {$reparadas}");
        $this->info("Sin coincidencia en el dispositivo (uid no encontrado o sin CI): {$sinCoincidencia}");

        return 0;
    }
}