<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\HorarioDia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::with('dias')->latest()->paginate(10);
        return view('admin.dispositivos.horarios.index', compact('horarios'));
    }

    public function getHorario(Horario $horario)
    {
        $horario->load('dias');
        return response()->json($horario);
    }

    public function store(Request $request)
    {
        $validated = $this->validateHorario($request);
        $horario = Horario::create($validated);
        $this->syncDias($horario, $request->input('dias', []));
        return $this->respond($request, 'Horario creado exitosamente.', $horario);
    }

    public function update(Request $request, Horario $horario)
    {
        $validated = $this->validateHorario($request);
        $horario->update($validated);
        $this->syncDias($horario, $request->input('dias', []));
        return $this->respond($request, 'Horario actualizado exitosamente.', $horario);
    }

    public function destroy(Request $request, Horario $horario)
    {
        $horario->delete();
        return $this->respond($request, 'Horario eliminado.', null);
    }

    // ---------- Métodos privados ----------

    private function validateHorario(Request $request)
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tolerancia_entrada_minutos' => 'required|integer|min:0',
            'tolerancia_salida_minutos' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);
    }

    private function syncDias(Horario $horario, array $dias)
    {
        $idsRecibidos = collect($dias)->pluck('id')->filter()->toArray();
        $horario->dias()->whereNotIn('id', $idsRecibidos)->delete();

        foreach ($dias as $data) {
            $dia = $data['dia_semana'] ?? null;
            if ($dia === null) continue;

            $registro = null;

            // 1. Buscar por ID si viene
            if (!empty($data['id'])) {
                $registro = HorarioDia::find($data['id']);
            }

            // 2. Si cambió el día y ya existe OTRO registro con ese día, eliminarlo primero
            //    (para no tener duplicados: un horario no puede tener 2 veces el mismo día)
            if ($registro && $registro->dia_semana != $dia) {
                HorarioDia::where('horario_id', $horario->id)
                        ->where('dia_semana', $dia)
                        ->where('id', '!=', $registro->id)
                        ->delete();
            }

            // 3. Si no encontró por ID, buscar por horario + día
            if (!$registro) {
                $registro = HorarioDia::where('horario_id', $horario->id)
                                    ->where('dia_semana', $dia)
                                    ->first();
            }

            // 4. Actualizar o crear
            if ($registro) {
                $registro->update([
                    'dia_semana'          => $dia,  // ← AQUÍ ESTÁ EL FIX
                    'hora_entrada'        => $data['hora_entrada'] ?? null,
                    'hora_salida'         => $data['hora_salida'] ?? null,
                    'hora_entrada_tarde'  => !empty($data['hora_entrada_tarde']) ? $data['hora_entrada_tarde'] : null,
                    'hora_salida_tarde'   => !empty($data['hora_salida_tarde']) ? $data['hora_salida_tarde'] : null,
                ]);
            } else {
                HorarioDia::create([
                    'horario_id'          => $horario->id,
                    'dia_semana'          => $dia,
                    'hora_entrada'        => $data['hora_entrada'] ?? null,
                    'hora_salida'         => $data['hora_salida'] ?? null,
                    'hora_entrada_tarde'  => !empty($data['hora_entrada_tarde']) ? $data['hora_entrada_tarde'] : null,
                    'hora_salida_tarde'   => !empty($data['hora_salida_tarde']) ? $data['hora_salida_tarde'] : null,
                ]);
            }
        }
    }

    private function respond(Request $request, $message, $data)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ]);
        }
        return redirect()->route('horarios.index')->with('success', $message);
    }
}