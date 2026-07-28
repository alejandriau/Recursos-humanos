<?php
// app/Http/Controllers/ZKTecoController.php

namespace App\Http\Controllers;

use App\Services\ZKTecoService;
use App\Services\ImportarBiometricosService;
use App\Models\MarcacionBiometrica;
use App\Models\SincronizacionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ZKTecoController extends Controller
{
    /**
     * Probar conexión con el biométrico UFace802 Plus
     */
    public function probarConexion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip' => 'required|ip',
            'port' => 'nullable|integer|min:1|max:65535',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $ip = $request->ip;
        $port = $request->port ?? 4370;

        $zk = new ZKTecoService($ip, $port);

        if ($zk->conectar()) {
            $info = $zk->obtenerInfoDispositivo(true);
            $zk->desconectar();

            return response()->json([
                'success' => true,
                'mensaje' => '✅ Conexión exitosa al UFace802 Plus',
                'informacion' => $info
            ]);
        }

        return response()->json([
            'success' => false,
            'mensaje' => '❌ No se pudo conectar.'
        ], 500);
    }

    /**
     * Obtener usuarios del biométrico
     */
    public function obtenerUsuarios(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip' => 'required|ip',
            'port' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $zk = new ZKTecoService($request->ip, $request->port ?? 4370);
        $usuarios = $zk->obtenerUsuarios();

        if ($usuarios === null) {
            return response()->json([
                'success' => false,
                'mensaje' => '❌ Error al obtener usuarios del biométrico'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'total' => count($usuarios),
            'data' => $usuarios
        ]);
    }

    /**
     * Listar marcaciones importadas con filtros
     */
    public function listarMarcaciones(Request $request)
    {
        $query = MarcacionBiometrica::query();

        if ($request->has('ci')) {
            $query->where('ci', $request->ci);
        }
        if ($request->has('fecha_inicio')) {
            $query->where('fecha_hora', '>=', $request->fecha_inicio);
        }
        if ($request->has('fecha_fin')) {
            $query->where('fecha_hora', '<=', $request->fecha_fin);
        }
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->has('persona_id')) {
            $query->where('persona_id', $request->persona_id);
        }
        if ($request->has('dispositivo_id')) {
            $query->where('dispositivo_id', $request->dispositivo_id);
        }

        $orderBy = $request->order_by ?? 'fecha_hora';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->per_page ?? 50;
        $marcaciones = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $marcaciones
        ]);
    }

    public function verMarcacion($id)
    {
        $marcacion = MarcacionBiometrica::find($id);

        if (!$marcacion) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Marcación no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $marcacion
        ]);
    }

    public function resumenPersona(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ci' => 'required|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $zk = new ZKTecoService('0.0.0.0');
        $resumen = $zk->getResumenPorPersona(
            $request->ci,
            $request->fecha_inicio,
            $request->fecha_fin
        );

        return response()->json([
            'success' => true,
            'data' => $resumen
        ]);
    }

    public function estadisticas()
    {
        $zk = new ZKTecoService('0.0.0.0');
        $estadisticas = $zk->getEstadisticasLocales();

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    public function logs(Request $request)
    {
        $query = SincronizacionLog::query();

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->has('dispositivo_id')) {
            $query->where('dispositivo_id', $request->dispositivo_id);
        }
        if ($request->has('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->has('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 50);

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function limpiarDuplicados()
    {
        $duplicados = MarcacionBiometrica::select('hash_unique')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('hash_unique')
            ->having('count', '>', 1)
            ->pluck('hash_unique');

        $eliminados = 0;
        foreach ($duplicados as $hash) {
            $registros = MarcacionBiometrica::where('hash_unique', $hash)
                ->orderBy('id', 'asc')
                ->get();

            $registros->shift();
            foreach ($registros as $registro) {
                $registro->delete();
                $eliminados++;
            }
        }

        return response()->json([
            'success' => true,
            'mensaje' => "Se eliminaron {$eliminados} registros duplicados"
        ]);
    }

    public function sincronizarUIDs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip' => 'required|ip',
            'port' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $zk = new ZKTecoService($request->ip, $request->port ?? 4370);
        $usuarios = $zk->obtenerUsuarios();

        if ($usuarios === null) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al obtener usuarios'
            ], 500);
        }

        $actualizados = 0;
        $noEncontrados = 0;
        $detalles = [];

        foreach ($usuarios as $usuario) {
            $persona = \App\Models\Persona::where('ci', $usuario['userid'])->first();

            if ($persona) {
                $persona->uid_biometrico = $usuario['uid'];
                $persona->badgenumber = $usuario['badgenumber'] ?? null;
                $persona->save();
                $actualizados++;

                $detalles[] = [
                    'ci' => $usuario['userid'],
                    'nombre' => $usuario['name'],
                    'uid' => $usuario['uid'],
                    'estado' => 'actualizado'
                ];
            } else {
                $noEncontrados++;
                $detalles[] = [
                    'ci' => $usuario['userid'],
                    'nombre' => $usuario['name'],
                    'uid' => $usuario['uid'],
                    'estado' => 'no_encontrado'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_usuarios' => count($usuarios),
                'actualizados' => $actualizados,
                'no_encontrados' => $noEncontrados,
                'detalles' => $detalles
            ]
        ]);
    }

    /**
     * IMPORTAR MARCACIONES DE UN SOLO DISPOSITIVO (por IP directa)
     */
    public function importarMarcaciones(Request $request)
    {
        set_time_limit(300);

        $validator = Validator::make($request->all(), [
            'ip' => 'nullable|ip',
            'port' => 'nullable|integer',
            'fecha' => 'nullable|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $fecha = $request->fecha ?? now()->toDateString();
        $fechaInicio = $request->fecha_inicio ?? $fecha;
        $fechaFin = $request->fecha_fin ?? $fecha;
        $ip = $request->ip ?? '172.16.34.6';

        $zk = new ZKTecoService($ip, $request->port ?? 4370, 60);
        $resultado = $zk->importarMarcaciones($fechaInicio, $fechaFin);

        if (isset($resultado['error'])) {
            return response()->json(['success' => false, 'error' => $resultado['error']], 500);
        }

        return response()->json([
            'success' => true,
            'mensaje' => $resultado['mensaje'],
            'data' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'total_obtenidas' => $resultado['total_obtenidas'],
                'nuevas_importadas' => $resultado['nuevas_importadas'],
                'duplicadas' => $resultado['duplicadas'],
                'con_error' => $resultado['con_error'],
                'log_id' => $resultado['log_id'],
            ]
        ]);
    }

    /**
     * IMPORTAR MARCACIONES DE TODOS LOS DISPOSITIVOS ACTIVOS
     * (tabla dispositivos_biometricos)
     */
    public function importarTodosDispositivos(Request $request)
    {
        set_time_limit(0); // puede tardar varios minutos con 10+ equipos

        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $service = new ImportarBiometricosService();
        $resultados = $service->importarTodos($request->fecha_inicio, $request->fecha_fin);

        return response()->json([
            'success' => true,
            'data' => $resultados,
        ]);
    }
}
