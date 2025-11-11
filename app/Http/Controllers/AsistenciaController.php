<?php

// app/Http/Controllers/AsistenciaController.php
namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Empleado;
use App\Models\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->input('fecha');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $personaId = $request->input('persona_id');

        $query = Asistencia::with('persona');

        // Filtrar por persona si se seleccionó
        if ($personaId) {
            $query->where('idPersona', $personaId);
        }

        // Filtrar por fechas
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        } elseif ($fecha) {
            $query->whereDate('fecha', $fecha);
        } else {
            $query->whereDate('fecha', now());
        }

        $asistencias = $query->get();

        // Obtener todas las personas para el selector
        $personas = Persona::orderBy('apellidoPat')->get();

        return view('admin.asistencias.index', compact('asistencias', 'fecha', 'fechaInicio', 'fechaFin', 'personaId', 'personas'));
    }



    public function create()
    {
        $empleados = Persona::where('estado', 1)->get();
        return view('admin.asistencias.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idPersona' => 'required|exists:persona,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'required',
            'hora_salida' => 'required'
        ]);

        // Calcular retraso automáticamente
        $horaEntrada = Carbon::parse($validated['hora_entrada']);
        $horaEsperada = Carbon::parse('08:00:00');
        $minutosRetraso = $horaEntrada > $horaEsperada ? $horaEntrada->diffInMinutes($horaEsperada) : 0;

        // Calcular horas extras
        $horaSalida = Carbon::parse($validated['hora_salida']);
        $horaFinJornada = Carbon::parse('18:00:00');
        $horasExtras = $horaSalida > $horaFinJornada ? $horaSalida->diffInHours($horaFinJornada) : 0;

        Asistencia::create([
            'idPersona' => $validated['idPersona'],
            'fecha' => $validated['fecha'],
            'hora_entrada' => $validated['hora_entrada'],
            'hora_salida' => $validated['hora_salida'],
            'minutos_retraso' => $minutosRetraso,
            'horas_extras' => $horasExtras
        ]);

        return redirect()->route('asistencias.index')->with('success', 'Asistencia registrada');
    }

    public function reporteMensual(Request $request)
    {
        $mes = $request->input('mes', date('m'));
        $ano = $request->input('ano', date('Y'));

        $asistencias = Asistencia::with('persona')
            ->whereYear('fecha', $ano)
            ->whereMonth('fecha', $mes)
            ->get()
            ->groupBy('empleado_id');

        return view('asistencias.reporte-mensual', compact('asistencias', 'mes', 'ano'));
    }
}
