<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadAsistencia;
use App\Models\Persona;
use App\Services\QrParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActividadAsistenciaController extends Controller
{
    protected QrParserService $parser;

    public function __construct(QrParserService $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Muestra la vista del escáner para una actividad.
     */
    public function escaner(Actividad $actividad)
    {
        if (!$actividad->estaActiva()) {
            return redirect()
                ->route('actividades.show', $actividad)
                ->with('error', 'La actividad está cerrada.');
        }

        $totalAsistencias = $actividad->asistencias()->count();

        return view('admin.actividades.escaner', compact('actividad', 'totalAsistencias'));
    }

    /**
     * Endpoint AJAX: recibe el texto del QR y registra la asistencia.
     */
    public function registrar(Request $request, Actividad $actividad)
    {
        $request->validate([
            'qr_texto' => 'required|string',
        ]);

        // 1) ¿Está activa la actividad?
        if (!$actividad->estaActiva()) {
            return response()->json([
                'success' => false,
                'tipo'    => 'evento_no_activo',
                'message' => 'La actividad está cerrada.',
            ], 409);
        }

        $qrRaw = $request->input('qr_texto');

        // 2) Resolver persona desde el QR (soporta ambos formatos)
        $resultado = $this->parser->resolverPersona($qrRaw);

        if (!$resultado['persona']) {
            return response()->json([
                'success' => false,
                'tipo'    => 'no_encontrado',
                'message' => $resultado['error'] ?? 'QR no reconocido.',
                'qr_raw'  => $qrRaw,
            ], 404);
        }

        /** @var Persona $persona */
        $persona = $resultado['persona'];

        // 3) ¿Ya registró asistencia en esta actividad?
        $existente = ActividadAsistencia::where('actividad_id', $actividad->id)
            ->where('persona_id', $persona->id)
            ->first();

        if ($existente) {
            return response()->json([
                'success' => false,
                'tipo'    => 'duplicado',
                'message' => "{$persona->nombre_completo} ya registró asistencia a las "
                             . $existente->hora_registro->format('H:i'),
                'persona' => [
                    'nombre' => $persona->nombre_completo,
                    'ci'     => $persona->ci,
                    'foto'   => $persona->foto,
                ],
            ], 409);
        }

        // 4) Registrar la asistencia
        $horaRegistro = now();
        $estado = $actividad->calcularEstadoPorHora($horaRegistro);

        $asistencia = ActividadAsistencia::create([
            'actividad_id'    => $actividad->id,
            'persona_id'      => $persona->id,
            'hora_registro'   => $horaRegistro,
            'estado'          => $estado,
            'metodo_registro' => ActividadAsistencia::METODO_OPERADOR,
            'qr_raw'          => $qrRaw,
            'registrado_por'  => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'tipo'    => $estado === ActividadAsistencia::ESTADO_TARDANZA ? 'tardanza' : 'ok',
            'message' => $estado === ActividadAsistencia::ESTADO_TARDANZA
                            ? "⚠️ {$persona->nombre_completo} registrado con TARDANZA"
                            : "✅ {$persona->nombre_completo} registrado",
            'persona' => [
                'nombre' => $persona->nombre_completo,
                'ci'     => $persona->ci,
                'foto'   => $persona->foto,
            ],
            'hora'    => $horaRegistro->format('H:i:s'),
            'total'   => $actividad->asistencias()->count(),
        ]);
    }

    /**
     * Registro manual: cuando el operador marca a alguien sin escanear QR.
     */
    public function registrarManual(Request $request, Actividad $actividad)
    {
        if (!$actividad->estaActiva()) {
            return back()->with('error', 'La actividad está cerrada.');
        }

        if (!$actividad->permite_manual) {
            return back()->with('error', 'Esta actividad no permite registro manual.');
        }

        $data = $request->validate([
            'persona_id'    => 'required|exists:persona,id',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $existente = ActividadAsistencia::where('actividad_id', $actividad->id)
            ->where('persona_id', $data['persona_id'])
            ->first();

        if ($existente) {
            return back()->with('error', 'Esta persona ya tiene asistencia registrada.');
        }

        $horaRegistro = now();
        $estado = $actividad->calcularEstadoPorHora($horaRegistro);

        ActividadAsistencia::create([
            'actividad_id'    => $actividad->id,
            'persona_id'      => $data['persona_id'],
            'hora_registro'   => $horaRegistro,
            'estado'          => $estado,
            'metodo_registro' => ActividadAsistencia::METODO_MANUAL,
            'registrado_por'  => Auth::id(),
            'observaciones'   => $data['observaciones'] ?? null,
        ]);

        return back()->with('success', 'Asistencia manual registrada.');
    }

    /**
     * Anula una asistencia mal registrada (estado = 0).
     */
    public function anular(ActividadAsistencia $asistencia)
    {
        $asistencia->update([
            'estado' => ActividadAsistencia::ESTADO_ANULADO,
        ]);

        return back()->with('success', 'Asistencia anulada.');
    }

    /**
     * Reporte simple de asistencias de una actividad.
     */
    public function reporte(Actividad $actividad)
    {
        $asistencias = $actividad->asistencias()
            ->with(['persona', 'registrador'])
            ->orderBy('hora_registro')
            ->get();

        $resumen = [
            'total'       => $asistencias->where('estado', '!=', 0)->count(),
            'presentes'   => $asistencias->where('estado', 1)->count(),
            'tardanzas'   => $asistencias->where('estado', 2)->count(),
            'justificados'=> $asistencias->where('estado', 3)->count(),
            'anulados'    => $asistencias->where('estado', 0)->count(),
        ];

        return view('actividades.reporte', compact('actividad', 'asistencias', 'resumen'));
    }
}
