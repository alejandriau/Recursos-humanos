<?php

// app/Http/Controllers/VacacionController.php
namespace App\Http\Controllers;

use App\Models\Vacacion;
use App\Models\Empleado;
use App\Models\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class VacacionController extends Controller
{
public function index(Request $request)
{
    $buscar = $request->input('buscar');

    $vacaciones = Vacacion::with('persona')
        ->when($buscar, function ($query) use ($buscar) {
            $query->whereHas('persona', function ($q) use ($buscar) {
                $q->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$buscar%"])
                ->orWhere('nombre', 'like', "%$buscar%")
                ->orWhere('apellidoPat', 'like', "%$buscar%")
                ->orWhere('apellidoMat', 'like', "%$buscar%");
            });
        })
        ->latest()
        ->paginate(10);

    return view('admin.vacaciones.index', compact('vacaciones', 'buscar'));

}


    public function create()
    {
        $empleados = Persona::where('estado', 1)->get();
        return view('admin.vacaciones.create', compact('empleados'));
    }


public function store(Request $request)
{
    $validated = $request->validate([
        'idPersona' => 'required|exists:persona,id',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after:fecha_inicio',
    ]);

    $inicio = Carbon::parse($validated['fecha_inicio']);
    $fin = Carbon::parse($validated['fecha_fin']);

    // Lista de feriados (puedes mantener esto en base de datos o archivo si crece)
    $feriados = [
        '2025-01-01', // Año Nuevo
        '2025-03-03', // Carnaval Lunes
        '2025-03-04', // Carnaval Martes
        '2025-04-18', // Viernes Santo
        '2025-05-01', // Día del Trabajador
        '2025-06-21', // Año Nuevo Andino
        '2025-08-06', // Independencia Bolivia
        '2025-11-02', // Todos Santos
        '2025-12-25', // Navidad
        // Agrega otros feriados nacionales o regionales
    ];

    $periodo = CarbonPeriod::create($inicio, $fin);

    $diasHabiles = collect($periodo)->filter(function ($date) use ($feriados) {
        return !$date->isWeekend() && !in_array($date->format('Y-m-d'), $feriados);
    })->count();

    Vacacion::create([
        'idPersona' => $validated['idPersona'],
        'fecha_inicio' => $validated['fecha_inicio'],
        'fecha_fin' => $validated['fecha_fin'],
        'dias_tomados' => $diasHabiles
    ]);

    return redirect()->route('vacaciones.index')->with('success', 'Solicitud de vacaciones creada correctamente');

}


    public function aprobar(Vacacion $vacacion)
    {
        $vacacion->update(['estado' => 'aprobado']);
        return back()->with('success', 'Vacaciones aprobadas');
    }

    public function rechazar(Vacacion $vacacion)
    {
        $vacacion->update(['estado' => 'rechazado']);
        return back()->with('success', 'Vacaciones rechazadas');
    }
}
