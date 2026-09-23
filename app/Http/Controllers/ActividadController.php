<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    public function index()
    {
        $actividades = Actividad::withCount('asistencias')
            ->orderByDesc('fecha')
            ->paginate(15);

        return view('actividades.index', compact('actividades'));
    }

    public function create()
    {
        return view('actividades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'               => 'required|string|max:150',
            'descripcion'          => 'nullable|string',
            'lugar'                => 'nullable|string|max:200',
            'fecha'                => 'required|date',
            'hora_inicio'          => 'nullable|date_format:H:i',
            'hora_fin'             => 'nullable|date_format:H:i',
            'hora_limite_puntual'  => 'nullable|date_format:H:i',
            'permite_manual'       => 'nullable|boolean',
        ]);

        $data['user_id'] = Auth::id();
        $data['estado']  = 1; // activa por defecto
        $data['permite_manual'] = $request->boolean('permite_manual', true);

        // Token único para el QR del evento (modo autoservicio)
        $data['token_qr'] = Str::random(48);

        $actividad = Actividad::create($data);

        return redirect()
            ->route('actividades.show', $actividad)
            ->with('success', 'Actividad creada correctamente.');
    }

    public function show(Actividad $actividad)
    {
        $actividad->load(['asistencias.persona', 'asistencias.registrador']);

        return view('actividades.show', compact('actividad'));
    }

    public function edit(Actividad $actividad)
    {
        return view('actividades.edit', compact('actividad'));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:150',
            'descripcion'         => 'nullable|string',
            'lugar'               => 'nullable|string|max:200',
            'fecha'               => 'required|date',
            'hora_inicio'         => 'nullable|date_format:H:i',
            'hora_fin'            => 'nullable|date_format:H:i',
            'hora_limite_puntual' => 'nullable|date_format:H:i',
            'estado'              => 'required|integer|in:0,1',
            'permite_manual'      => 'nullable|boolean',
        ]);

        $data['permite_manual'] = $request->boolean('permite_manual', true);

        $actividad->update($data);

        return redirect()
            ->route('actividades.show', $actividad)
            ->with('success', 'Actividad actualizada.');
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return redirect()
            ->route('actividades.index')
            ->with('success', 'Actividad eliminada.');
    }

    /**
     * Cierra la actividad para que no se sigan marcando asistencias.
     */
    public function cerrar(Actividad $actividad)
    {
        $actividad->update(['estado' => 0]);

        return back()->with('success', 'Actividad cerrada.');
    }

    /**
     * Reabre la actividad.
     */
    public function abrir(Actividad $actividad)
    {
        $actividad->update(['estado' => 1]);

        return back()->with('success', 'Actividad reabierta.');
    }
}
