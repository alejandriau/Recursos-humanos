<?php

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
                Log::info("ZKTeco: Conectado a {$this->ip}:{$this->port}");
                return true;
            }
            Log::error("ZKTeco: No se pudo conectar a {$this->ip}:{$this->port}");
            return false;
        } catch (\Exception $e) {
            Log::error("ZKTeco Error: " . $e->getMessage());
            return false;
        }
    }

    public function desconectar()
    {
        try {
            $this->zk->disconnect();
            Log::info("ZKTeco: Desconectado de {$this->ip}:{$this->port}");
            return true;
        } catch (\Exception $e) {
            Log::error("ZKTeco Error al desconectar: " . $e->getMessage());
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

        $usuarios = null;
        try {
            $this->zk->disableDevice();
            $usuarios = $this->zk->getUser();
            Log::info("ZKTeco: Obtenidos " . count($usuarios) . " usuarios");
        } catch (\Exception $e) {
            Log::error("ZKTeco Error obteniendo usuarios: " . $e->getMessage());
            $usuarios = null;
        } finally {
            try {
                $this->zk->enableDevice();
            } catch (\Exception $e) {
                Log::error("ZKTeco Error al habilitar dispositivo (usuarios): " . $e->getMessage());
            }
            $this->desconectar();
        }

        return $usuarios;
    }

    /**
     * Obtener marcaciones del biométrico.
     * 
     * IMPORTANTE: Siempre habilita el dispositivo al final, aunque falle.
     * NUNCA borra marcaciones del dispositivo.
     */
    public function obtenerMarcaciones($fechaInicio = null, $fechaFin = null)
    {
        if (!$this->conectar()) {
            return null;
        }

        $marcaciones = null;
        try {
            $this->zk->disableDevice();

            $inicioDescarga = microtime(true);
            $marcaciones = $this->zk->getAttendance();
            $duracion = round(microtime(true) - $inicioDescarga, 2);

            Log::info("ZKTeco: Descarga completa: " . count($marcaciones) . " registros en {$duracion}s");

            // Filtrar en PHP por el rango solicitado
            if ($fechaInicio && $fechaFin) {
                $inicio = ($fechaInicio instanceof Carbon) 
                    ? $fechaInicio->copy() 
                    : Carbon::parse($fechaInicio)->startOfDay();
                
                $fin = ($fechaFin instanceof Carbon) 
                    ? $fechaFin->copy() 
                    : Carbon::parse($fechaFin)->endOfDay();

                $marcaciones = array_values(array_filter($marcaciones, function ($m) use ($inicio, $fin) {
                    if (empty($m['timestamp'])) return false;
                    $fecha = Carbon::parse($m['timestamp']);
                    return $fecha->betweenIncluded($inicio, $fin);
                }));

                Log::info("ZKTeco: " . count($marcaciones) . " marcaciones dentro del rango");
            }

        } catch (\Exception $e) {
            Log::error("ZKTeco Error obteniendo marcaciones: " . $e->getMessage());
            $marcaciones = null;
        } finally {
            // SIEMPRE habilitar y desconectar, pase lo que pase
            try {
                $this->zk->enableDevice();
            } catch (\Exception $e) {
                Log::error("ZKTeco Error al habilitar dispositivo (marcaciones): " . $e->getMessage());
            }
            $this->desconectar();
        }

        return $marcaciones;
    }

    /**
     * Importar marcaciones.
     * 
     * Modo automático (sin fechas): descarga desde la última marcación 
     * importada de ESTE dispositivo menos 30 min, hasta ahora + 5 min.
     * NUNCA borra las marcaciones del dispositivo.
     */
    public function importarMarcaciones($fechaInicio = null, $fechaFin = null, $dispositivoId = null)
    {
        $modo = 'manual';
        $rangoInicio = null;
        $rangoFin = null;

        // ============================================================
        // MODO AUTOMÁTICO: calcular desde la última descarga exitosa
        // ============================================================
        if (!$fechaInicio || !$fechaFin) {
            $ultimaFecha = MarcacionBiometrica::where('dispositivo_id', $dispositivoId)->max('fecha_hora');
            
            if ($ultimaFecha) {
                $rangoInicio = Carbon::parse($ultimaFecha)->subMinutes(30);
            } else {
                $rangoInicio = Carbon::today();
            }
            
            $rangoFin = Carbon::now()->addMinutes(5);
            $modo = 'automatico';
            
            Log::info("ZKTeco: Modo automático. Desde {$rangoInicio->toDateTimeString()} hasta {$rangoFin->toDateTimeString()}");
        } else {
            $rangoInicio = $fechaInicio;
            $rangoFin = $fechaFin;
        }

        $log = SincronizacionLog::create([
            'dispositivo_id' => $dispositivoId,
            'ip_biometrico' => $this->ip,
            'puerto' => $this->port,
            'estado' => 'iniciado',
            'fecha_inicio' => now(),
            'detalles' => [
                'fecha_inicio' => $rangoInicio instanceof Carbon ? $rangoInicio->toDateTimeString() : $rangoInicio,
                'fecha_fin' => $rangoFin instanceof Carbon ? $rangoFin->toDateTimeString() : $rangoFin,
                'modo' => $modo,
            ]
        ]);

        try {
            $marcaciones = $this->obtenerMarcaciones($rangoInicio, $rangoFin);

            if ($marcaciones === null) {
                $log->update([
                    'estado' => 'error',
                    'mensaje' => 'No se pudieron obtener las marcaciones del biométrico',
                    'fecha_fin' => now(),
                ]);
                return [
                    'error' => 'No se pudieron obtener las marcaciones',
                    'fecha_inicio' => $rangoInicio instanceof Carbon ? $rangoInicio->toDateString() : $rangoInicio,
                    'fecha_fin' => $rangoFin instanceof Carbon ? $rangoFin->toDateString() : $rangoFin,
                ];
            }

            $totalObtenidas = count($marcaciones);

            if ($totalObtenidas === 0) {
                $log->update([
                    'estado' => 'exito',
                    'mensaje' => 'Sin marcaciones nuevas en el rango',
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
                    'fecha_inicio' => $rangoInicio instanceof Carbon ? $rangoInicio->toDateString() : $rangoInicio,
                    'fecha_fin' => $rangoFin instanceof Carbon ? $rangoFin->toDateString() : $rangoFin,
                ];
            }

            // 1) Calcular hash
            foreach ($marcaciones as &$m) {
                $hashBase = MarcacionBiometrica::generarHash($m);
                $m['_hash'] = md5($hashBase . '|dispositivo:' . ($dispositivoId ?? '0'));
            }
            unset($m);

            // Normalizar CI
            foreach ($marcaciones as &$m) {
                $m['userid'] = $m['id'] ?? $m['userid'] ?? null;
            }
            unset($m);

            // 2) Hashes existentes
            $todosLosHashes = array_column($marcaciones, '_hash');
            $hashesExistentes = [];
            foreach (array_chunk($todosLosHashes, 1000) as $chunkHashes) {
                $existentes = MarcacionBiometrica::whereIn('hash_unique', $chunkHashes)
                    ->pluck('hash_unique')
                    ->toArray();
                $hashesExistentes = array_merge($hashesExistentes, $existentes);
            }
            $hashesExistentes = array_flip($hashesExistentes);

            // 3) Mapear CI -> persona_id
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
                        'dispositivo_id' => $dispositivoId,
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

                    $hashesExistentes[$hash] = true;
                    $nuevasImportadas++;

                } catch (\Exception $e) {
                    $conError++;
                    Log::error("Error preparando marcación: " . $e->getMessage(), ['marcacion' => $marcacion]);
                }
            }

            // 4) Insertar en bloques
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
                'fecha_inicio' => $rangoInicio instanceof Carbon ? $rangoInicio->toDateString() : $rangoInicio,
                'fecha_fin' => $rangoFin instanceof Carbon ? $rangoFin->toDateString() : $rangoFin,
            ];

        } catch (\Exception $e) {
            $log->update([
                'estado' => 'error',
                'mensaje' => mb_substr($e->getMessage(), 0, 250),
                'fecha_fin' => now(),
            ]);
            return [
                'error' => $e->getMessage(),
                'fecha_inicio' => $rangoInicio instanceof Carbon ? $rangoInicio->toDateString() : $rangoInicio,
                'fecha_fin' => $rangoFin instanceof Carbon ? $rangoFin->toDateString() : $rangoFin,
            ];
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
            '0' => 'No identificado', '1' => 'Huella Dactilar', '2' => 'Contraseña',
            '3' => 'Tarjeta RFID', '4' => 'Contraseña', '5' => 'Huella + Contraseña',
            '6' => 'Tarjeta + Contraseña', '7' => 'Huella + Tarjeta',
            '8' => 'Huella + Tarjeta + Contraseña', '9' => 'Face',
            '10' => 'Face + Huella', '11' => 'Face + Contraseña',
            '12' => 'Face + Tarjeta', '13' => 'Face + Huella + Contraseña',
            '14' => 'Face + Huella + Tarjeta', '15' => 'Face',
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