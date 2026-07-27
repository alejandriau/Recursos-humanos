<?php
// app/Services/ZKTecoService.php

namespace App\Services;

use Jmrashed\Zkteco\Lib\ZKTeco;
use App\Models\MarcacionBiometrica;
use App\Models\SincronizacionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ZKTecoService
{
    protected $zk;
    protected $ip;
    protected $port;

    public function __construct($ip, $port = 4370)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->zk = new ZKTeco($ip, $port);
    }

    /**
     * Conectar al dispositivo UFace802 Plus
     */
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

    /**
     * Desconectar del dispositivo
     */
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

    /**
     * Obtener información del dispositivo UFace802 Plus
     */
    public function obtenerInfoDispositivo()
    {
        if (!$this->conectar()) {
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

            $this->desconectar();
            return $info;
        } catch (\Exception $e) {
            Log::error("ZKTeco Error obteniendo info: " . $e->getMessage());
            $this->desconectar();
            return null;
        }
    }

    /**
     * Obtener TODOS los usuarios del biométrico
     * Devuelve: uid, userid (CI), name, badgenumber, etc.
     */
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
     * Obtener TODAS las marcaciones del biométrico UFace802 Plus
     * Estructura esperada de cada marcación:
     * [
     *   'uid' => '1',           // ID único en el dispositivo
     *   'userid' => '1234567',  // CI del empleado (¡este es el importante!)
     *   'name' => 'Juan Perez', // Nombre
     *   'timestamp' => '2026-07-27 08:00:00',
     *   'type' => '0',          // 0=Entrada, 1=Salida, 255=otros
     *   'state' => 15,          // 1=Huella, 15=Face, 4=Contraseña, etc.
     *   'machine_id' => '1',    // ID de la máquina
     *   'sn' => 12345,          // Número de serie del registro
     *   'badgenumber' => '1001' // Número de tarjeta
     * ]
     */
    public function obtenerMarcaciones($fechaInicio = null, $fechaFin = null)
    {
        if (!$this->conectar()) {
            return null;
        }

        try {
            $this->zk->disableDevice();

            // Si se especifican fechas, convertirlas a timestamp para ZKTeco
            if ($fechaInicio && $fechaFin) {
                $inicio = Carbon::parse($fechaInicio)->timestamp;
                $fin = Carbon::parse($fechaFin)->timestamp;
                $marcaciones = $this->zk->getAttendance($inicio, $fin);
            } else {
                $marcaciones = $this->zk->getAttendance();
            }

            $this->zk->enableDevice();
            $this->desconectar();

            Log::info("ZKTeco UFace802 Plus: Obtenidas " . count($marcaciones) . " marcaciones");
            return $marcaciones;

        } catch (\Exception $e) {
            Log::error("ZKTeco Error obteniendo marcaciones: " . $e->getMessage());
            $this->desconectar();
            return null;
        }
    }

    /**
     * Importar marcaciones a la base de datos local
     * Solo importa las que no existen aún
     */
    public function importarMarcaciones($fechaInicio = null, $fechaFin = null)
    {
        // Crear log de sincronización
        $log = SincronizacionLog::create([
            'ip_biometrico' => $this->ip,
            'puerto' => $this->port,
            'estado' => 'iniciado',
            'fecha_inicio' => now(),
        ]);

        try {
            // Obtener marcaciones del biométrico
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
            $nuevasImportadas = 0;
            $duplicadas = 0;
            $conError = 0;
            $detalles = [];

            // Procesar cada marcación
            foreach ($marcaciones as $marcacion) {
                try {
                    // Generar hash único para evitar duplicados
                    $hash = MarcacionBiometrica::generarHash($marcacion);

                    // Verificar si ya existe
                    $existente = MarcacionBiometrica::where('hash_unique', $hash)->first();

                    if ($existente) {
                        $duplicadas++;
                        continue;
                    }

                    // Crear la marcación en la base de datos
                    MarcacionBiometrica::create([
                        'ci' => $marcacion['userid'] ?? 'desconocido',
                        'nombre_completo' => $marcacion['name'] ?? null,
                        'uid_biometrico' => $marcacion['uid'],
                        'fecha_hora' => $marcacion['timestamp'],
                        'tipo' => $this->mapearTipoMarcacion($marcacion['type'] ?? null),
                        'estado_verificacion' => $marcacion['state'] ?? null,
                        'sn' => $marcacion['sn'] ?? null,
                        'importada' => true,
                        'fecha_importacion' => now(),
                        'hash_unique' => $hash,
                        'persona_id' => $this->buscarPersonaPorCI($marcacion['userid'] ?? null),
                    ]);

                    $nuevasImportadas++;

                    $detalles[] = [
                        'ci' => $marcacion['userid'] ?? 'desconocido',
                        'nombre' => $marcacion['name'] ?? 'Sin nombre',
                        'fecha_hora' => $marcacion['timestamp'],
                        'tipo' => $this->mapearTipoMarcacion($marcacion['type'] ?? null),
                        'estado' => 'importada'
                    ];

                } catch (\Exception $e) {
                    $conError++;
                    Log::error("Error importando marcación: " . $e->getMessage(), [
                        'marcacion' => $marcacion
                    ]);

                    $detalles[] = [
                        'error' => $e->getMessage(),
                        'datos' => $marcacion
                    ];
                }
            }

            // Actualizar el log
            $log->update([
                'estado' => $conError > 0 ? 'parcial' : 'exito',
                'mensaje' => 'Importación completada',
                'total_obtenidas' => $totalObtenidas,
                'nuevas_importadas' => $nuevasImportadas,
                'duplicadas' => $duplicadas,
                'con_error' => $conError,
                'detalles' => $detalles,
                'fecha_fin' => now(),
            ]);

            return [
                'mensaje' => 'Importación completada exitosamente',
                'total_obtenidas' => $totalObtenidas,
                'nuevas_importadas' => $nuevasImportadas,
                'duplicadas' => $duplicadas,
                'con_error' => $conError,
                'log_id' => $log->id,
                'detalles' => $detalles
            ];

        } catch (\Exception $e) {
            $log->update([
                'estado' => 'error',
                'mensaje' => $e->getMessage(),
                'fecha_fin' => now(),
            ]);

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Mapear el tipo de marcación de ZKTeco a formato legible
     */
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

    /**
     * Buscar persona por CI en la base de datos local
     */
    protected function buscarPersonaPorCI($ci)
    {
        if (!$ci) return null;

        // Buscar en tu tabla persona
        $persona = \App\Models\Persona::where('ci', $ci)->first();

        if ($persona) {
            // Opcional: Actualizar UID biométrico
            $persona->uid_biometrico = $persona->uid_biometrico ?? $ci;
            $persona->save();
            return $persona->id;
        }

        return null;
    }

    /**
     * Obtener estadísticas de marcaciones locales
     */
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

    /**
     * Obtener resumen de marcaciones por persona
     */
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
            'marcaciones' => $marcaciones->map(function($m) {
                return [
                    'fecha_hora' => $m->fecha_hora->format('Y-m-d H:i:s'),
                    'tipo' => $m->tipo,
                    'verificacion' => $this->getNombreVerificacion($m->estado_verificacion),
                ];
            })
        ];
    }

    /**
     * Obtener nombre del método de verificación
     */
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

    /**
     * Obtener logs de sincronización
     */
    public function getLogs($limit = 50)
    {
        return SincronizacionLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
