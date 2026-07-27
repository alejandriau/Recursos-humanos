<?php
// app/Http/Controllers/ZKTecoController.php

namespace App\Http\Controllers;

use App\Services\ZKTecoService;
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
            // Obtener información SIN reconectar
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

        // Filtros
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

        // Ordenamiento
        $orderBy = $request->order_by ?? 'fecha_hora';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        // Paginación
        $perPage = $request->per_page ?? 50;
        $marcaciones = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $marcaciones
        ]);
    }

    /**
     * Obtener una marcación específica
     */
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

    /**
     * Obtener resumen de marcaciones por persona
     */
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

    /**
     * Obtener estadísticas generales
     */
    public function estadisticas()
    {
        $zk = new ZKTecoService('0.0.0.0');
        $estadisticas = $zk->getEstadisticasLocales();

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Obtener logs de sincronización
     */
    public function logs(Request $request)
    {
        $query = SincronizacionLog::query();

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
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

    /**
     * Eliminar marcaciones duplicadas (limpieza)
     */
    public function limpiarDuplicados()
    {
        // Encontrar duplicados por hash_unique
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

            // Mantener el primero, eliminar los demás
            $registros->shift(); // Quitar el primero
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

    /**
     * Sincronizar UIDs de biométrico con tabla persona
     */
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
            // Buscar persona por CI
            $persona = \App\Models\Persona::where('ci', $usuario['userid'])->first();

            if ($persona) {
                // Actualizar UID biométrico
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
 * IMPORTAR MARCACIONES DE UN DÍA ESPECÍFICO (PRUEBA)
 */
public function importarMarcaciones(Request $request)
{
    set_time_limit(300); // 5 minutos

    $validator = Validator::make($request->all(), [
        //'ip' => 'required|ip',
        'port' => 'nullable|integer',
        'fecha' => 'nullable|date', // ¡Ahora es un solo parámetro!
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    // Si no se envía fecha, usar hoy
    $fecha = $request->fecha ?? now()->toDateString();

    // Usar fecha_inicio/fecha_fin si se envían, sino usar la fecha única
    $fechaInicio = $request->fecha_inicio ?? $fecha;
    $fechaFin = $request->fecha_fin ?? $fecha;

    $zk = new ZKTecoService('172.16.34.6', $request->port ?? 4370, 30);
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
}
