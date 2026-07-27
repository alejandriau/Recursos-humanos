<?php
// app/Services/ZKTecoService.php

namespace App\Services;

use Jmrashed\Zkteco\Lib\ZKTeco;
use App\Models\MarcacionBiometrica;
use App\Models\SincronizacionLog;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ZKTecoService
{
    protected $zk;
    protected $ip;
    protected $port;
    protected $timeout;

    public function __construct($ip, $port = 4370, $timeout = 60)
    {
        // OJO: subimos el timeout por defecto a 60s. getAttendance() de esta
        // librería siempre trae TODO el log del dispositivo (no filtra por
        // fecha en el equipo), así que la descarga puede tardar según el
        // volumen de marcaciones almacenadas.
        $this->ip = $ip;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->zk = new ZKTeco($ip, $port, $timeout);
    }

    public function conectar()
    {
        try {
            $conectado = $this->zk->connect();
            if ($conectado) {
                Log::info("ZKTeco UFace802 Plus: Conectado a {$this->ip}:{$this->port}");
                return true;
            }
            Log::error("ZKTeco UFace802 Plus: No se pudo conectar a {$this->ip}:{$this->port}");
            return false;
        } catch (\Exception $e) {
            Log::error("ZKTeco UFace802 Plus Error: " . $e->getMessage());
            return false;
        }
    }

    public function desconectar()
    {
        try {
            $this->zk->disconnect();
            Log::info("ZKTeco UFace802 Plus: Desconectado de {$this->ip}:{$this->port}");
            return true;
        } catch (\Exception $e) {
            Log::error("ZKTeco UFace802 Plus Error al desconectar: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerInfoDispositivo($yaConectado = false)
    {
        if (!$yaConectado && !$this->conectar()) {
            return null;
        }

        try {
            $info = [
                'serial' => $this->zk->serialNumber(),
                'firmware' => $this->zk->version(),
                'nombre' => $this->zk->deviceName(),
                'plataforma' => $this->zk->platform(),
                'os' => $this->zk->osVersion(),
                'tipo' => 'UFace802 Plus'
            ];

            if (!$yaConectado) {
                $this->desconectar();
            }
            return $info;
        } catch (\Exception $e) {
            Log::error("ZKTeco Error obteniendo info: " . $e->getMessage());
            if (!$yaConectado) {
                $this->desconectar();
            }
            return null;
        }
    }

    public function obtenerUsuarios()
    {
        if (!$this->conectar()) {
            return null;
        }

        try {
            $this->zk->disableDevice();
            $usuarios = $this->zk->getUser();
            $this->zk->enableDevice();
            $this->desconectar();

            Log::info("ZKTeco UFace802 Plus: Obtenidos " . count($usuarios) . " usuarios");
            return $usuarios;
        } catch (\Exception $e) {
            Log::error("ZKTeco Error obteniendo usuarios: " . $e->getMessage());
            $this->desconectar();
            return null;
        }
    }

    /**
     * Obtener marcaciones del biométrico.
     *
     * IMPORTANTE: la librería jmrashed/zkteco NO soporta filtrar por rango
     * de fechas en el propio dispositivo. getAttendance() siempre descarga
     * el log completo. El filtrado por $fechaInicio/$fechaFin se hace acá
     * en PHP, después de la descarga.
     */
    public function obtenerMarcaciones($fechaInicio = null, $fechaFin = null)
    {
        if (!$this->conectar()) {
            return null;
        }

        try {
            $this->zk->disableDevice();

            $inicioDescarga = microtime(true);
            $marcaciones = $this->zk->getAttendance();
            $duracion = round(microtime(true) - $inicioDescarga, 2);

            $this->zk->enableDevice();
            $this->desconectar();

            Log::info("ZKTeco: Descarga completa del dispositivo: " . count($marcaciones) . " registros en {$duracion}s");

            // Filtrar en PHP por el rango solicitado
            if ($fechaInicio && $fechaFin) {
                $inicio = Carbon::parse($fechaInicio)->startOfDay();
                $fin = Carbon::parse($fechaFin)->endOfDay();

                $marcaciones = array_values(array_filter($marcaciones, function ($m) use ($inicio, $fin) {
                    if (empty($m['timestamp'])) return false;
                    $fecha = Carbon::parse($m['timestamp']);
                    return $fecha->betweenIncluded($inicio, $fin);
                }));

                Log::info("ZKTeco: " . count($marcaciones) . " marcaciones dentro del rango {$fechaInicio} - {$fechaFin}");
            }

            return $marcaciones;

        } catch (\Exception $e) {
            Log::error("ZKTeco Error obteniendo marcaciones: " . $e->getMessage());
            $this->desconectar();
            return null;
        }
    }

    /**
     * Importar marcaciones con inserción masiva (bulk insert) y verificación
     * de duplicados/personas en lote, en vez de una query por registro.
     */
    public function importarMarcaciones($fechaInicio = null, $fechaFin = null)
    {
        if (!$fechaInicio || !$fechaFin) {
            $fechaInicio = Carbon::now()->toDateString();
            $fechaFin = Carbon::now()->toDateString();
            Log::info("ZKTeco: No se especificaron fechas, usando hoy: {$fechaInicio}");
        }

        $log = SincronizacionLog::create([
            'ip_biometrico' => $this->ip,
            'puerto' => $this->port,
            'estado' => 'iniciado',
            'fecha_inicio' => now(),
            'detalles' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ]
        ]);

        try {
            $marcaciones = $this->obtenerMarcaciones($fechaInicio, $fechaFin);

            if ($marcaciones === null) {
                $log->update([
                    'estado' => 'error',
                    'mensaje' => 'No se pudieron obtener las marcaciones del biométrico',
                    'fecha_fin' => now(),
                ]);
                return ['error' => 'No se pudieron obtener las marcaciones'];
            }

            $totalObtenidas = count($marcaciones);

            if ($totalObtenidas === 0) {
                $log->update([
                    'estado' => 'exito',
                    'mensaje' => 'Sin marcaciones nuevas en el rango solicitado',
                    'total_obtenidas' => 0,
                    'nuevas_importadas' => 0,
                    'duplicadas' => 0,
                    'con_error' => 0,
                    'fecha_fin' => now(),
                ]);
                return [
                    'mensaje' => 'No hay marcaciones en ese rango',
                    'total_obtenidas' => 0,
                    'nuevas_importadas' => 0,
                    'duplicadas' => 0,
                    'con_error' => 0,
                    'log_id' => $log->id,
                ];
            }

            // 1) Calcular hash de cada marcación de una sola pasada
            foreach ($marcaciones as &$m) {
                $m['_hash'] = MarcacionBiometrica::generarHash($m);
            }
            unset($m);

            // Normalizar el campo de CI: getAttendance() de jmrashed/zkteco
            // devuelve la clave 'id' (no 'userid') para el identificador del
            // empleado. Se deja 'userid' como fallback por si cambia la lib.
            foreach ($marcaciones as &$m) {
                $m['userid'] = $m['id'] ?? $m['userid'] ?? null;
            }
            unset($m);

            // 2) UNA sola query para saber cuáles hashes ya existen
            $todosLosHashes = array_column($marcaciones, '_hash');
            $hashesExistentes = [];
            foreach (array_chunk($todosLosHashes, 1000) as $chunkHashes) {
                $existentes = MarcacionBiometrica::whereIn('hash_unique', $chunkHashes)
                    ->pluck('hash_unique')
                    ->toArray();
                $hashesExistentes = array_merge($hashesExistentes, $existentes);
            }
            $hashesExistentes = array_flip($hashesExistentes);

            // 3) UNA sola query para mapear CI -> persona_id (en vez de N queries)
            $cisEnLote = array_unique(array_filter(array_column($marcaciones, 'userid')));
            $mapaPersonas = Persona::whereIn('ci', $cisEnLote)
                ->pluck('id', 'ci')
                ->toArray();

            $nuevasImportadas = 0;
            $duplicadas = 0;
            $conError = 0;
            $filasParaInsertar = [];
            $ahora = now();

            foreach ($marcaciones as $marcacion) {
                try {
                    $hash = $marcacion['_hash'];

                    if (isset($hashesExistentes[$hash])) {
                        $duplicadas++;
                        continue;
                    }

                    $ci = $marcacion['userid'] ?? null;
                    $personaId = $ci && isset($mapaPersonas[$ci]) ? $mapaPersonas[$ci] : null;

                    $filasParaInsertar[] = [
                        'ci' => $ci ?? 'desconocido',
                        'nombre_completo' => $marcacion['name'] ?? null,
                        'uid_biometrico' => $marcacion['uid'],
                        'fecha_hora' => $marcacion['timestamp'],
                        'tipo' => $this->mapearTipoMarcacion($marcacion['type'] ?? null),
                        'estado_verificacion' => $marcacion['state'] ?? null,
                        'sn' => $marcacion['sn'] ?? null,
                        'importada' => true,
                        'fecha_importacion' => $ahora,
                        'hash_unique' => $hash,
                        'persona_id' => $personaId,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ];

                    // Evitar marcar como "no encontrado" dos veces el mismo hash
                    $hashesExistentes[$hash] = true;
                    $nuevasImportadas++;

                } catch (\Exception $e) {
                    $conError++;
                    Log::error("Error preparando marcación: " . $e->getMessage(), ['marcacion' => $marcacion]);
                }
            }

            // 4) Insertar en bloques de 500 (bulk insert real, no create() por fila)
            foreach (array_chunk($filasParaInsertar, 500) as $chunkFilas) {
                DB::table('marcaciones_biometricas')->insert($chunkFilas);
            }

            $log->update([
                'estado' => $conError > 0 ? 'parcial' : 'exito',
                'mensaje' => 'Importación completada',
                'total_obtenidas' => $totalObtenidas,
                'nuevas_importadas' => $nuevasImportadas,
                'duplicadas' => $duplicadas,
                'con_error' => $conError,
                'fecha_fin' => now(),
            ]);

            return [
                'mensaje' => 'Importación completada exitosamente',
                'total_obtenidas' => $totalObtenidas,
                'nuevas_importadas' => $nuevasImportadas,
                'duplicadas' => $duplicadas,
                'con_error' => $conError,
                'log_id' => $log->id,
            ];

        } catch (\Exception $e) {
            $log->update([
                'estado' => 'error',
                'mensaje' => mb_substr($e->getMessage(), 0, 250),
                'fecha_fin' => now(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    protected function mapearTipoMarcacion($tipo)
    {
        $mapa = [
            '0' => 'entrada',
            '1' => 'salida',
            '2' => 'entrada2',
            '3' => 'salida2',
            '4' => 'entrada3',
            '5' => 'salida3',
            '255' => 'otro'
        ];

        return $mapa[$tipo] ?? 'otro';
    }

    public function getEstadisticasLocales()
    {
        return [
            'total_marcaciones' => MarcacionBiometrica::count(),
            'por_tipo' => MarcacionBiometrica::select('tipo', DB::raw('count(*) as total'))
                ->groupBy('tipo')
                ->get()
                ->pluck('total', 'tipo')
                ->toArray(),
            'ultimas_10' => MarcacionBiometrica::latest('fecha_hora')
                ->take(10)
                ->get(),
            'fecha_max' => MarcacionBiometrica::max('fecha_hora'),
            'fecha_min' => MarcacionBiometrica::min('fecha_hora'),
            'total_personas' => MarcacionBiometrica::distinct('ci')->count('ci'),
        ];
    }

    public function getResumenPorPersona($ci, $fechaInicio = null, $fechaFin = null)
    {
        $query = MarcacionBiometrica::where('ci', $ci);

        if ($fechaInicio) {
            $query->where('fecha_hora', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha_hora', '<=', $fechaFin);
        }

        $marcaciones = $query->orderBy('fecha_hora')->get();

        return [
            'ci' => $ci,
            'total' => $marcaciones->count(),
            'entradas' => $marcaciones->where('tipo', 'entrada')->count(),
            'salidas' => $marcaciones->where('tipo', 'salida')->count(),
            'primera_marcacion' => $marcaciones->first()?->fecha_hora,
            'ultima_marcacion' => $marcaciones->last()?->fecha_hora,
            'marcaciones' => $marcaciones->map(function ($m) {
                return [
                    'fecha_hora' => $m->fecha_hora->format('Y-m-d H:i:s'),
                    'tipo' => $m->tipo,
                    'verificacion' => $this->getNombreVerificacion($m->estado_verificacion),
                ];
            })
        ];
    }

    protected function getNombreVerificacion($codigo)
    {
        $mapa = [
            '0' => 'No identificado',
            '1' => 'Huella Dactilar',
            '2' => 'Contraseña',
            '3' => 'Tarjeta RFID',
            '4' => 'Contraseña',
            '5' => 'Huella + Contraseña',
            '6' => 'Tarjeta + Contraseña',
            '7' => 'Huella + Tarjeta',
            '8' => 'Huella + Tarjeta + Contraseña',
            '9' => 'Face',
            '10' => 'Face + Huella',
            '11' => 'Face + Contraseña',
            '12' => 'Face + Tarjeta',
            '13' => 'Face + Huella + Contraseña',
            '14' => 'Face + Huella + Tarjeta',
            '15' => 'Face',
        ];

        return $mapa[(string)$codigo] ?? 'Método desconocido';
    }

    public function getLogs($limit = 50)
    {
        return SincronizacionLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}