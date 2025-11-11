<?php

namespace App\Http\Controllers;

use App\Models\Vacacion;
use App\Models\Empleado;
use App\Models\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class VacacionEmpleadoController extends Controller
{
    //public function __construct()
    //{
    //    $this->middleware('auth');
    //}

    public function index(Request $request)
    {
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return redirect()->back()->with('error', 'No se encontró información del empleado.');
        }

        $buscar = $request->input('buscar');
        $estado = $request->input('estado');

        $vacaciones = Vacacion::where('idPersona', $persona->id)
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('fecha_inicio', 'like', "%$buscar%")
                      ->orWhere('fecha_fin', 'like', "%$buscar%")
                      ->orWhere('dias_tomados', 'like', "%$buscar%");
                });
            })
            ->when($estado, function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->latest()
            ->paginate(10);

        $diasDisponibles = $this->calcularDiasDisponibles($persona->id);

        return view('empleado.vacaciones.index', compact('vacaciones', 'diasDisponibles', 'buscar', 'estado'));
    }

    public function create()
    {
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        if (!$persona) {
            return redirect()->back()->with('error', 'No se encontró información del empleado.');
        }

        $diasDisponibles = $this->calcularDiasDisponibles($persona->id);

        return view('empleado.vacaciones.create', compact('diasDisponibles'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'fecha_inicio' => 'required|date|after:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'motivo' => 'nullable|string|max:500',
        ]);

        $inicio = Carbon::parse($validated['fecha_inicio']);
        $fin = Carbon::parse($validated['fecha_fin']);

        // Validar que no exceda los días disponibles
        $diasSolicitados = $this->calcularDiasHabiles($inicio, $fin);
        $diasDisponibles = $this->calcularDiasDisponibles($persona->id);

        if ($diasSolicitados > $diasDisponibles) {
            return back()->withErrors([
                'fecha_fin' => "Solo tienes {$diasDisponibles} días disponibles. Estás solicitando {$diasSolicitados} días."
            ])->withInput();
        }

        // Validar superposición de fechas
        $vacacionesSuperpuestas = Vacacion::where('idPersona', $persona->id)
            ->where('estado', '!=', 'rechazado')
            ->where(function ($query) use ($inicio, $fin) {
                $query->whereBetween('fecha_inicio', [$inicio, $fin])
                    ->orWhereBetween('fecha_fin', [$inicio, $fin])
                    ->orWhere(function ($q) use ($inicio, $fin) {
                        $q->where('fecha_inicio', '<=', $inicio)
                          ->where('fecha_fin', '>=', $fin);
                    });
            })->exists();

        if ($vacacionesSuperpuestas) {
            return back()->withErrors([
                'fecha_inicio' => 'Ya tienes una solicitud de vacaciones en esas fechas.'
            ])->withInput();
        }

        Vacacion::create([
            'idPersona' => $persona->id,
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'dias_tomados' => $diasSolicitados,
            'motivo' => $validated['motivo'] ?? null
        ]);

        return redirect()->route('empleado.vacaciones.index')->with('success', 'Solicitud de vacaciones enviada correctamente');
    }

    public function show(Vacacion $vacacion)
    {
        // Verificar que el empleado solo pueda ver sus propias vacaciones
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        if ($vacacion->idPersona != $persona->id) {
            abort(403, 'No autorizado');
        }

        return view('empleado.vacaciones.show', compact('vacacion'));
    }

    private function calcularDiasDisponibles($idPersona)
    {
        $diasAcumulados = 15; // Días base por año
        $diasTomados = Vacacion::where('idPersona', $idPersona)
            ->where('estado', 'aprobado')
            ->whereYear('created_at', now()->year)
            ->sum('dias_tomados');

        return max(0, $diasAcumulados - $diasTomados);
    }

    private function calcularDiasHabiles($inicio, $fin)
    {
        $feriados = [
            '2025-01-01', '2025-03-03', '2025-03-04', '2025-04-18',
            '2025-05-01', '2025-06-21', '2025-08-06', '2025-11-02', '2025-12-25',
        ];

        $periodo = CarbonPeriod::create($inicio, $fin);

        return collect($periodo)->filter(function ($date) use ($feriados) {
            return !$date->isWeekend() && !in_array($date->format('Y-m-d'), $feriados);
        })->count();
    }
}
