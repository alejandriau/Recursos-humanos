<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AsistenciaAdminController extends Controller
{
    public function index(Request $request)
    {
        // Fecha por defecto (hoy)
        $fecha = $request->input('fecha', date('Y-m-d'));
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $personaId = $request->input('persona_id');
        $estado = $request->input('estado');

        $query = Asistencia::with('persona');

    // Fecha por defecto (hoy)


    // El problema podría estar aquí - verifica el formato de fecha en tu BD
    $query = Asistencia::with('persona');

    // Filtros
    if ($personaId) {
        $query->where('idPersona', $personaId);
    }

    if ($estado) {
        $query->where('estado', $estado);
    }

    // Rango de fechas o fecha específica
    if ($fechaInicio && $fechaFin) {
        $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    } else {
        // POSIBLE PROBLEMA: Si no hay registros para la fecha de hoy
        $query->whereDate('fecha', $fecha);
    }

    // Para debugging, agrega esto temporalmente:
    \Log::info('Consulta SQL:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

    $asistencias = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->paginate(50);

        $personas = Persona::where('estado', 1)->orderBy('apellidoPat')->get();

        // Definir estados si no existen en el modelo
        $estados = [
            'presente' => 'Presente',
            'ausente' => 'Ausente',
            'tardanza' => 'Tardanza',
            'permiso' => 'Permiso',
            'vacaciones' => 'Vacaciones'
        ];

        // Estadísticas
        $totalRegistros = $asistencias->total();
        $presentesHoy = Asistencia::whereDate('fecha', $fecha)
            ->where('estado', 'presente')
            ->count();

        // Pasar todos los parámetros de filtro para mantenerlos en la vista
        $filtros = [
            'fecha' => $fecha,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'persona_id' => $personaId,
            'estado' => $estado
        ];

        return view('admin.asistencias.index', compact(
            'asistencias',
            'personas',
            'estados',
            'totalRegistros',
            'presentesHoy',
            'filtros'
        ));
    }

    public function create()
    {
        $empleados = Persona::where('estado', 1)->get();
        $estados = Asistencia::ESTADOS;
        return view('admin.asistencias.create', compact('empleados', 'estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idPersona' => 'required|exists:persona,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'required|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'estado' => 'required|in:presente,ausente,tardanza,permiso,vacaciones',
            'observaciones' => 'nullable|string|max:500',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric'
        ]);

        // Verificar si ya existe registro para esta persona y fecha
        $existente = Asistencia::where('idPersona', $validated['idPersona'])
            ->whereDate('fecha', $validated['fecha'])
            ->first();

        if ($existente) {
            return back()->withErrors(['fecha' => 'Ya existe un registro de asistencia para esta persona en esta fecha.'])->withInput();
        }

        $datosAsistencia = [
            'idPersona' => $validated['idPersona'],
            'fecha' => $validated['fecha'],
            'estado' => $validated['estado'],
            'observaciones' => $validated['observaciones'] ?? null,
            'tipo_registro' => 'manual'
        ];

        // Solo calcular horas si está presente/tardanza
        if (in_array($validated['estado'], ['presente', 'tardanza']) && $validated['hora_entrada']) {
            $datosAsistencia['hora_entrada'] = $validated['hora_entrada'];
            $datosAsistencia['hora_salida'] = $validated['hora_salida'];

            // Calcular retraso
            $horaEntrada = Carbon::parse($validated['hora_entrada']);
            $horaEsperada = Carbon::parse('08:00:00');
            $datosAsistencia['minutos_retraso'] = $horaEntrada > $horaEsperada ?
                $horaEntrada->diffInMinutes($horaEsperada) : 0;

            // Calcular horas extras si hay hora de salida
            if ($validated['hora_salida']) {
                $horaSalida = Carbon::parse($validated['hora_salida']);
                $horaFinJornada = Carbon::parse('18:00:00');
                $datosAsistencia['horas_extras'] = $horaSalida > $horaFinJornada ?
                    $horaSalida->diffInHours($horaFinJornada) : 0;
            }
        }

        // Geoubicación si se proporciona
        if ($request->filled('latitud') && $request->filled('longitud')) {
            $datosAsistencia['latitud'] = $validated['latitud'];
            $datosAsistencia['longitud'] = $validated['longitud'];
        }

        Asistencia::create($datosAsistencia);

        return redirect()->route('admin.asistencias.index')->with('success', 'Asistencia registrada correctamente.');
    }

    public function edit(Asistencia $asistencia)
    {
        $empleados = Persona::where('estado', 1)->get();
        $estados = Asistencia::ESTADOS;
        return view('admin.asistencias.edit', compact('asistencia', 'empleados', 'estados'));
    }

    public function update(Request $request, Asistencia $asistencia)
    {
        $validated = $request->validate([
            'hora_entrada' => 'required|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'estado' => 'required|in:presente,ausente,tardanza,permiso,vacaciones',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $datosActualizar = [
            'estado' => $validated['estado'],
            'observaciones' => $validated['observaciones'] ?? null
        ];

        if (in_array($validated['estado'], ['presente', 'tardanza'])) {
            $datosActualizar['hora_entrada'] = $validated['hora_entrada'];
            $datosActualizar['hora_salida'] = $validated['hora_salida'];

            // Recalcular retraso y horas extras
            $horaEntrada = Carbon::parse($validated['hora_entrada']);
            $horaEsperada = Carbon::parse('08:00:00');
            $datosActualizar['minutos_retraso'] = $horaEntrada > $horaEsperada ?
                $horaEntrada->diffInMinutes($horaEsperada) : 0;

            if ($validated['hora_salida']) {
                $horaSalida = Carbon::parse($validated['hora_salida']);
                $horaFinJornada = Carbon::parse('18:00:00');
                $datosActualizar['horas_extras'] = $horaSalida > $horaFinJornada ?
                    $horaSalida->diffInHours($horaFinJornada) : 0;
            }
        } else {
            // Limpiar horas si no es presente/tardanza
            $datosActualizar['hora_entrada'] = null;
            $datosActualizar['hora_salida'] = null;
            $datosActualizar['minutos_retraso'] = 0;
            $datosActualizar['horas_extras'] = 0;
        }

        $asistencia->update($datosActualizar);

        return redirect()->route('admin.asistencias.index')->with('success', 'Asistencia actualizada correctamente.');
    }

    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return back()->with('success', 'Registro de asistencia eliminado.');
    }

    public function reporteMensual(Request $request)
    {
        $mes = $request->input('mes', date('m'));
        $ano = $request->input('ano', date('Y'));
        $personaId = $request->input('persona_id');

        $query = Asistencia::with('persona')
            ->delMes($mes, $ano);

        if ($personaId) {
            $query->where('idPersona', $personaId);
        }

        $asistencias = $query->get()->groupBy('idPersona');

        // Estadísticas mensuales
        $totalPersonas = Persona::where('estado', 1)->count();
        $diasDelMes = Carbon::createFromDate($ano, $mes, 1)->daysInMonth;

        $estadisticas = [
            'total_dias_laborales' => $this->calcularDiasLaborales($mes, $ano),
            'total_personas' => $totalPersonas,
            'dias_mes' => $diasDelMes
        ];

        $personas = Persona::where('estado', 1)->orderBy('apellidoPat')->get();

        return view('admin.asistencias.reporte-mensual', compact(
            'asistencias', 'mes', 'ano', 'personaId', 'personas', 'estadisticas'
        ));
    }

    public function marcarAusentes(Request $request)
    {
        $fecha = $request->input('fecha', date('Y-m-d'));

        // Obtener todos los empleados activos
        $empleadosActivos = Persona::where('estado', 1)->pluck('id');

        // Obtener empleados que ya tienen registro
        $empleadosConRegistro = Asistencia::whereDate('fecha', $fecha)
            ->pluck('idPersona');

        // Empleados sin registro
        $empleadosSinRegistro = $empleadosActivos->diff($empleadosConRegistro);

        // Crear registros de ausencia
        foreach ($empleadosSinRegistro as $empleadoId) {
            Asistencia::create([
                'idPersona' => $empleadoId,
                'fecha' => $fecha,
                'estado' => 'ausente',
                'tipo_registro' => 'sistema'
            ]);
        }

        return back()->with('success', "Se marcaron {$empleadosSinRegistro->count()} empleados como ausentes.");
    }

    private function calcularDiasLaborales($mes, $ano)
    {
        $inicio = Carbon::createFromDate($ano, $mes, 1);
        $fin = $inicio->copy()->endOfMonth();

        $periodo = CarbonPeriod::create($inicio, $fin);

        return collect($periodo)->filter(function ($fecha) {
            return !$fecha->isWeekend();
        })->count();
    }
}
