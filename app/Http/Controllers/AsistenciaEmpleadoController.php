<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AsistenciaEmpleadoController extends Controller
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

        $fecha = $request->input('fecha', date('Y-m-d'));
        $mes = $request->input('mes', date('m'));
        $ano = $request->input('ano', date('Y'));

        // Asistencia del día actual
        $asistenciaHoy = Asistencia::where('idPersona', $persona->id)
            ->whereDate('fecha', $fecha)
            ->first();

        // Historial del mes
        $asistencias = Asistencia::where('idPersona', $persona->id)
            ->delMes($mes, $ano)
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        // Estadísticas del mes
        $estadisticasMes = $this->calcularEstadisticasMes($persona->id, $mes, $ano);

        return view('empleado.asistencias.index', compact(
            'asistenciaHoy', 'asistencias', 'fecha', 'mes', 'ano', 'estadisticasMes'
        ));
    }

    public function marcarEntrada(Request $request)
    {
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $fechaHoy = now()->toDateString();
        $horaActual = now()->format('H:i:s');

        // Verificar si ya marcó entrada hoy
        $asistenciaExistente = Asistencia::where('idPersona', $persona->id)
            ->whereDate('fecha', $fechaHoy)
            ->first();

        if ($asistenciaExistente) {
            return back()->with('error', 'Ya has marcado tu entrada hoy.');
        }

        // Calcular retraso
        $horaEntrada = Carbon::parse($horaActual);
        $horaEsperada = Carbon::parse('08:00:00');
        $minutosRetraso = $horaEntrada > $horaEsperada ?
            $horaEntrada->diffInMinutes($horaEsperada) : 0;

        $estado = $minutosRetraso > 0 ? 'tardanza' : 'presente';

        Asistencia::create([
            'idPersona' => $persona->id,
            'fecha' => $fechaHoy,
            'hora_entrada' => $horaActual,
            'minutos_retraso' => $minutosRetraso,
            'estado' => $estado,
            'tipo_registro' => 'web',
            'observaciones' => $validated['observaciones'] ?? null,
            'latitud' => $validated['latitud'] ?? null,
            'longitud' => $validated['longitud'] ?? null
        ]);

        return back()->with('success', "Entrada marcada a las {$horaActual}. ¡Buen día!");
    }

    public function marcarSalida(Request $request)
    {
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $fechaHoy = now()->toDateString();
        $horaActual = now()->format('H:i:s');

        // Buscar registro de entrada de hoy
        $asistencia = Asistencia::where('idPersona', $persona->id)
            ->whereDate('fecha', $fechaHoy)
            ->first();

        if (!$asistencia) {
            return back()->with('error', 'No has marcado entrada hoy.');
        }

        if ($asistencia->hora_salida) {
            return back()->with('error', 'Ya has marcado salida hoy.');
        }

        // Calcular horas extras
        $horaSalida = Carbon::parse($horaActual);
        $horaFinJornada = Carbon::parse('18:00:00');
        $horasExtras = $horaSalida > $horaFinJornada ?
            $horaSalida->diffInHours($horaFinJornada) : 0;

        $asistencia->update([
            'hora_salida' => $horaActual,
            'horas_extras' => $horasExtras,
            'observaciones' => $asistencia->observaciones . ' ' . ($validated['observaciones'] ?? ''),
            'latitud_salida' => $validated['latitud'] ?? null,
            'longitud_salida' => $validated['longitud'] ?? null
        ]);

        return back()->with('success', "Salida marcada a las {$horaActual}. ¡Hasta mañana!");
    }

    public function justificarAusencia(Request $request)
    {
        $user = Auth::user();
        $persona = Persona::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'fecha' => 'required|date|before_or_equal:today',
            'tipo' => 'required|in:permiso,vacaciones',
            'observaciones' => 'required|string|max:500',
            'archivo' => 'nullable|file|max:2048|mimes:pdf,jpg,png'
        ]);

        // Verificar si ya existe registro para esa fecha
        $existente = Asistencia::where('idPersona', $persona->id)
            ->whereDate('fecha', $validated['fecha'])
            ->first();

        if ($existente) {
            return back()->with('error', 'Ya existe un registro para esta fecha.');
        }

        $datosJustificacion = [
            'idPersona' => $persona->id,
            'fecha' => $validated['fecha'],
            'estado' => $validated['tipo'],
            'observaciones' => $validated['observaciones'],
            'tipo_registro' => 'web'
        ];

        // Guardar archivo si se subió
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('justificaciones', 'public');
            $datosJustificacion['archivo_justificacion'] = $archivoPath;
        }

        Asistencia::create($datosJustificacion);

        return back()->with('success', 'Ausencia justificada correctamente. Espera la aprobación.');
    }

    private function calcularEstadisticasMes($personaId, $mes, $ano)
    {
        $asistenciasMes = Asistencia::where('idPersona', $personaId)
            ->delMes($mes, $ano)
            ->get();

        return [
            'dias_presente' => $asistenciasMes->where('estado', 'presente')->count(),
            'dias_tardanza' => $asistenciasMes->where('estado', 'tardanza')->count(),
            'dias_ausente' => $asistenciasMes->where('estado', 'ausente')->count(),
            'dias_permiso' => $asistenciasMes->where('estado', 'permiso')->count(),
            'total_retraso' => $asistenciasMes->sum('minutos_retraso'),
            'total_horas_extras' => $asistenciasMes->sum('horas_extras'),
            'dias_trabajados' => $asistenciasMes->whereIn('estado', ['presente', 'tardanza'])->count()
        ];
    }
}
