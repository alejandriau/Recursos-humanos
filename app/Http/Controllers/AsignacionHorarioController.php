<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Horario;
use App\Models\PersonaHorario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class AsignacionHorarioController extends Controller
{
    public function index()
    {
        $personas = Persona::where('estado', 1)
            ->with(['asignaciones' => function ($q) {
                $q->where('activo', true);
            }])
            ->get();

        // Añadir el flag
        $personas->each(function($p) {
            $p->tiene_asignacion_activa = $p->asignaciones->isNotEmpty();
        });

        $horarios = Horario::where('activo', true)->get();
        $asignaciones = PersonaHorario::with(['persona', 'horario'])->latest()->paginate(100);

        return view('admin.dispositivos.asignaciones.index', compact('personas', 'horarios', 'asignaciones'));
    }

    public function create()
    {
        $personas = Persona::orderBy('nombre')->get();
        $horarios = Horario::where('activo', true)->get();
        return view('admin.asignacion.create', compact('personas', 'horarios'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'persona_ids' => 'required|array|min:1',
                'persona_ids.*' => 'exists:persona,id',
                'horario_id' => 'required|exists:horarios,id',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'input' => $request->all(),
            ], 422);
        }

        // Si no hay persona_ids, devolver error
        if (empty($request->persona_ids)) {
            return response()->json([
                'success' => false,
                'errors' => ['persona_ids' => ['Debes seleccionar al menos una persona.']]
            ], 422);
        }

        $horarioId = $request->horario_id;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        foreach ($request->persona_ids as $personaId) {
            // Desactivar asignaciones anteriores activas
            PersonaHorario::where('persona_id', $personaId)
                ->where('activo', true)
                ->update(['activo' => false]);

            PersonaHorario::create([
                'persona_id' => $personaId,
                'horario_id' => $horarioId,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'activo' => true,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Horario asignado a ' . count($request->persona_ids) . ' persona(s).'
            ]);
        }

        return redirect()->route('asignacion.index')
            ->with('success', 'Horario asignado a ' . count($request->persona_ids) . ' persona(s).');
    }

    public function destroy(Request $request, PersonaHorario $asignacion)
    {
        $asignacion->update(['activo' => false]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Asignación finalizada.'
            ]);
        }

        return redirect()->route('asignacion.index')
            ->with('success', 'Asignación finalizada.');
    }


    public function edit(PersonaHorario $asignacion)
    {
        $personas = Persona::orderBy('nombre')->get();
        $horarios = Horario::where('activo', true)->get();
        return view('admin.asignacion.edit', compact('asignacion', 'personas', 'horarios'));
    }

    public function update(Request $request, PersonaHorario $asignacion)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $asignacion->update([
            'horario_id' => $request->horario_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ]);

        return redirect()->route('asignacion.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }


}