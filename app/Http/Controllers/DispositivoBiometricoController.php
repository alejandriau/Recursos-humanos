<?php
// app/Http/Controllers/DispositivoBiometricoController.php

namespace App\Http\Controllers;

use App\Models\DispositivoBiometrico;
use App\Services\ZKTecoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DispositivoBiometricoController extends Controller
{
    /**
     * Vista principal (tabla + modal de creación/edición)
     */
    public function index()
    {
        $dispositivos = DispositivoBiometrico::orderBy('nombre')->get();
        return view('dispositivos.index', compact('dispositivos'));
    }

    /**
     * Listado en JSON (para refrescar la tabla vía AJAX sin recargar la página)
     */
    public function listar()
    {
        return response()->json([
            'success' => true,
            'data' => DispositivoBiometrico::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'ip' => 'required|ip',
            'puerto' => 'nullable|integer|min:1|max:65535',
            'ubicacion' => 'nullable|string|max:255',
            'timeout' => 'nullable|integer|min:5|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $existe = DispositivoBiometrico::where('ip', $request->ip)
            ->where('puerto', $request->puerto ?? 4370)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Ya existe un dispositivo registrado con esa IP y puerto.'
            ], 422);
        }

        $dispositivo = DispositivoBiometrico::create([
            'nombre' => $request->nombre,
            'ip' => $request->ip,
            'puerto' => $request->puerto ?? 4370,
            'ubicacion' => $request->ubicacion,
            'timeout' => $request->timeout ?? 60,
            'activo' => true,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Dispositivo registrado correctamente.',
            'data' => $dispositivo,
        ]);
    }

    public function update(Request $request, $id)
    {
        $dispositivo = DispositivoBiometrico::find($id);
        if (!$dispositivo) {
            return response()->json(['success' => false, 'mensaje' => 'Dispositivo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'ip' => 'required|ip',
            'puerto' => 'nullable|integer|min:1|max:65535',
            'ubicacion' => 'nullable|string|max:255',
            'timeout' => 'nullable|integer|min:5|max:300',
            'activo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $dispositivo->update([
            'nombre' => $request->nombre,
            'ip' => $request->ip,
            'puerto' => $request->puerto ?? 4370,
            'ubicacion' => $request->ubicacion,
            'timeout' => $request->timeout ?? 60,
            'activo' => $request->boolean('activo', $dispositivo->activo),
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Dispositivo actualizado correctamente.',
            'data' => $dispositivo,
        ]);
    }

    public function destroy($id)
    {
        $dispositivo = DispositivoBiometrico::find($id);
        if (!$dispositivo) {
            return response()->json(['success' => false, 'mensaje' => 'Dispositivo no encontrado'], 404);
        }

        $dispositivo->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Dispositivo eliminado correctamente.',
        ]);
    }

    /**
     * Activar/desactivar rápido desde la tabla (switch)
     */
    public function toggleActivo($id)
    {
        $dispositivo = DispositivoBiometrico::find($id);
        if (!$dispositivo) {
            return response()->json(['success' => false, 'mensaje' => 'Dispositivo no encontrado'], 404);
        }

        $dispositivo->update(['activo' => !$dispositivo->activo]);

        return response()->json([
            'success' => true,
            'activo' => $dispositivo->activo,
        ]);
    }

    /**
     * Probar conexión a un dispositivo ya guardado (botón "Probar" en la tabla)
     */
    public function probarConexion($id)
    {
        $dispositivo = DispositivoBiometrico::find($id);
        if (!$dispositivo) {
            return response()->json(['success' => false, 'mensaje' => 'Dispositivo no encontrado'], 404);
        }

        $zk = new ZKTecoService($dispositivo->ip, $dispositivo->puerto, $dispositivo->timeout);

        if ($zk->conectar()) {
            $info = $zk->obtenerInfoDispositivo(true);
            $zk->desconectar();

            return response()->json([
                'success' => true,
                'mensaje' => '✅ Conexión exitosa',
                'informacion' => $info,
            ]);
        }

        return response()->json([
            'success' => false,
            'mensaje' => '❌ No se pudo conectar a ' . $dispositivo->ip . ':' . $dispositivo->puerto,
        ], 500);
    }
}
