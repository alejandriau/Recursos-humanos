<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BiometricoController extends Controller
{
    public function recibirRegistro(Request $request)
    {
        $validated = $request->validate([
            'codigo_empleado' => 'required|string',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i:s',
            'tipo' => 'required|in:entrada,salida',
            'dispositivo_id' => 'required|string'
        ]);

        // Buscar persona por código de empleado
        $persona = Persona::where('codigo_empleado', $validated['codigo_empleado'])
            ->where('estado', 1)
            ->first();

        if (!$persona) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado o inactivo'
            ], 404);
        }

        $fecha = Carbon::parse($validated['fecha'])->toDateString();

        if ($validated['tipo'] === 'entrada') {
            return $this->procesarEntrada($persona, $fecha, $validated['hora'], $validated['dispositivo_id']);
        } else {
            return $this->procesarSalida($persona, $fecha, $validated['hora'], $validated['dispositivo_id']);
        }
    }

    private function procesarEntrada($persona, $fecha, $hora, $dispositivoId)
    {
        // Verificar si ya existe registro de entrada
        $asistenciaExistente = Asistencia::where('idPersona', $persona->id)
            ->whereDate('fecha', $fecha)
            ->first();

        if ($asistenciaExistente && $asistenciaExistente->hora_entrada) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe registro de entrada para hoy'
            ], 400);
        }

        // Calcular retraso
        $horaEntrada = Carbon::parse($hora);
        $horaEsperada = Carbon::parse('08:00:00');
        $minutosRetraso = $horaEntrada > $horaEsperada ?
            $horaEntrada->diffInMinutes($horaEsperada) : 0;

        $estado = $minutosRetraso > 0 ? 'tardanza' : 'presente';

        if ($asistenciaExistente) {
            // Actualizar registro existente
            $asistenciaExistente->update([
                'hora_entrada' => $hora,
                'minutos_retraso' => $minutosRetraso,
                'estado' => $estado,
                'tipo_registro' => 'biometrico'
            ]);
        } else {
            // Crear nuevo registro
            Asistencia::create([
                'idPersona' => $persona->id,
                'fecha' => $fecha,
                'hora_entrada' => $hora,
                'minutos_retraso' => $minutosRetraso,
                'estado' => $estado,
                'tipo_registro' => 'biometrico',
                'observaciones' => "Registro biométrico - Dispositivo: {$dispositivoId}"
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrada registrada correctamente',
            'empleado' => $persona->nombre . ' ' . $persona->apellidoPat,
            'hora' => $hora,
            'estado' => $estado
        ]);
    }

    private function procesarSalida($persona, $fecha, $hora, $dispositivoId)
    {
        // Buscar registro de entrada
        $asistencia = Asistencia::where('idPersona', $persona->id)
            ->whereDate('fecha', $fecha)
            ->first();

        if (!$asistencia) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró registro de entrada para hoy'
            ], 400);
        }

        if ($asistencia->hora_salida) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe registro de salida para hoy'
            ], 400);
        }

        // Calcular horas extras
        $horaSalida = Carbon::parse($hora);
        $horaFinJornada = Carbon::parse('18:00:00');
        $horasExtras = $horaSalida > $horaFinJornada ?
            $horaSalida->diffInHours($horaFinJornada) : 0;

        $asistencia->update([
            'hora_salida' => $hora,
            'horas_extras' => $horasExtras,
            'observaciones' => $asistencia->observaciones . " | Salida biométrica - Dispositivo: {$dispositivoId}"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salida registrada correctamente',
            'empleado' => $persona->nombre . ' ' . $persona->apellidoPat,
            'hora' => $hora,
            'horas_extras' => $horasExtras
        ]);
    }
}
