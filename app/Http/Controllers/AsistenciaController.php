<?php
// app/Http/Controllers/AsistenciaController.php

namespace App\Http\Controllers;

use App\Models\AsistenciaDiaria;
use App\Models\Persona;
use App\Services\GenerarAsistenciaService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Vista principal del reporte (filtros + tabla)
     */
    public function index(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? Carbon::now()->startOfMonth()->toDateString();
        $fechaFin = $request->fecha_fin ?? Carbon::now()->toDateString();

        return view('admin.dispositivos.asistencias.index', [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ]);
    }

    /**
     * Listado filtrado en JSON, para la tabla AJAX
     */
    public function listar(Request $request)
    {
        $query = AsistenciaDiaria::with(['persona:id,ci,nombre,apellidoPat,apellidoMat']);

        $fechaInicio = $request->fecha_inicio ?? Carbon::now()->startOfMonth()->toDateString();
        $fechaFin = $request->fecha_fin ?? Carbon::now()->toDateString();

        $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($request->filled('persona_id')) {
            $query->where('persona_id', $request->persona_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('ci')) {
            $query->whereHas('persona', function ($q) use ($request) {
                $q->where('ci', 'like', '%' . $request->ci . '%');
            });
        }

        // Por defecto no mostramos los días 'no_laborable' para no inflar la tabla
        if (!$request->filled('estado')) {
            $query->where('estado', '!=', 'no_laborable');
        }

        $query->orderBy('fecha', 'desc')->orderBy('persona_id');

        $asistencias = $query->paginate($request->per_page ?? 50);

        return response()->json([
            'success' => true,
            'data' => $asistencias,
        ]);
    }

    /**
     * Detalle de un día (los checkpoints: entrada/salida mañana/tarde)
     */
    public function detalle($id)
    {
        $asistencia = AsistenciaDiaria::with([
            'persona:id,ci,nombre,apellidoPat,apellidoMat',
            'marcas.salida.tipoSalida',
        ])->find($id);

        if (!$asistencia) {
            return response()->json(['success' => false, 'mensaje' => 'No encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $asistencia,
        ]);
    }

    /**
     * Resumen agregado por persona en un rango (para tarjetas/totales)
     */
    public function resumenPorPersona(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? Carbon::now()->startOfMonth()->toDateString();
        $fechaFin = $request->fecha_fin ?? Carbon::now()->toDateString();

        $resumen = AsistenciaDiaria::with('persona:id,ci,nombre,apellidoPat,apellidoMat')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->selectRaw('persona_id,
                SUM(CASE WHEN estado = "falta_injustificada" THEN 1 ELSE 0 END) as faltas_injustificadas,
                SUM(CASE WHEN estado = "falta_justificada" THEN 1 ELSE 0 END) as faltas_justificadas,
                SUM(CASE WHEN estado = "tardanza" THEN 1 ELSE 0 END) as tardanzas,
                SUM(minutos_tardanza) as total_minutos_tardanza,
                COUNT(*) as dias_evaluados')
            ->groupBy('persona_id')
            ->having('faltas_injustificadas', '>', 0)
            ->orHaving('tardanzas', '>', 0)
            ->orderByDesc('faltas_injustificadas')
            ->get();

        return response()->json(['success' => true, 'data' => $resumen]);
    }

    /**
     * Botón "Regenerar" manual desde la vista, por si se aprobó una
     * salida tarde o se importaron marcaciones nuevas de un rango pasado.
     */
    public function regenerar(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $service = new GenerarAsistenciaService();
        $resultado = $service->generarParaRango($request->fecha_inicio, $request->fecha_fin);

        return response()->json(['success' => true, 'data' => $resultado]);
    }

    /**
     * Vista de reporte por empleado (uso de RRHH, elige a cualquier persona)
     */
    public function verEmpleado()
    {
        return view('empleado.asistencias.index');
    }

    /**
     * Vista de "mi asistencia" (el propio empleado ve la suya)
     */
    public function miAsistenciaVista()
    {
        return view('asistencia.mi-asistencia');
    }

    /**
     * Datos del reporte de un empleado específico (uso de RRHH)
     */
    public function reportePorEmpleado(Request $request, $personaId)
    {
        $persona = Persona::find($personaId);
        if (!$persona) {
            return response()->json(['success' => false, 'mensaje' => 'Persona no encontrada'], 404);
        }

        [$fechaInicio, $fechaFin] = $this->resolverRango($request);

        return response()->json([
            'success' => true,
            'data' => $this->construirReporte($persona, $fechaInicio, $fechaFin),
        ]);
    }


    /**
     * Datos del reporte del empleado autenticado (uso de "mi asistencia")
     */
    public function miAsistencia(Request $request)
    {
        $persona = Persona::where('user_id', auth()->id())->first();

        if (!$persona) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Tu usuario no tiene un registro de personal asociado.'
            ], 404);
        }

        [$fechaInicio, $fechaFin] = $this->resolverRango($request);

        return response()->json([
            'success' => true,
            'data' => $this->construirReporte($persona, $fechaInicio, $fechaFin),
        ]);
    }

    /**
     * Búsqueda simple de personas para el selector de RRHH (por CI o nombre)
     */
    public function buscarPersonas(Request $request)
    {
        $q = $request->q;

        $personas = Persona::where('estado', true)
            ->when($q, function ($query) use ($q) {
                $query->where('ci', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%")
                    ->orWhere('apellidoPat', 'like', "%{$q}%")
                    ->orWhere('apellidoMat', 'like', "%{$q}%");
            })
            ->orderBy('nombre')
            ->limit(30)
            ->get(['id', 'ci', 'nombre', 'apellidoPat', 'apellidoMat']);

        return response()->json(['success' => true, 'data' => $personas]);
    }

    /**
     * Resuelve el rango de fechas
     */
    protected function resolverRango(Request $request): array
    {
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            return [$request->fecha_inicio, $request->fecha_fin];
        }

        $hoy = Carbon::now();
        $inicio = $hoy->day >= 21
            ? $hoy->copy()->startOfDay()->day(21)
            : $hoy->copy()->startOfDay()->subMonthNoOverflow()->day(21);

        return [$inicio->toDateString(), $hoy->toDateString()];
    }

    /**
     * Helper para convertir minutos a HH:MM (sin límite de 24h)
     */
    protected function minutosToString($minutos)
    {
        $h = floor($minutos / 60);
        $m = $minutos % 60;
        return sprintf("%02d:%02d", $h, $m);
    }

    /**
     * Arma el reporte completo
     */
    /**
     * Arma el reporte completo
     */
    protected function construirReporte(Persona $persona, string $fechaInicio, string $fechaFin): array
    {
        $dias = AsistenciaDiaria::where('persona_id', $persona->id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['marcas.salida.tipoSalida', 'marcas.marcacion'])
            ->orderBy('fecha')
            ->get();

        $datosFilas = [];
        $totalesMinutos = [
            'atraso' => 0, 'sal_ant' => 0, 'ext' => 0, 'jor' => 0, 'ausen' => 0
        ];

        foreach ($dias as $dia) {
            // CORRECCIÓN CRÍTICA: Usar str_starts_with para encontrar las marcas de entrada y salida
            $marcasEntrada = $dia->marcas->filter(fn($m) => str_starts_with($m->tipo_marca, 'entrada'));
            $marcasSalida = $dia->marcas->filter(fn($m) => str_starts_with($m->tipo_marca, 'salida'));

            // Obtenemos la primera entrada y la última salida del día
            $entrada = $marcasEntrada->sortBy('hora_real')->first();
            $salida = $marcasSalida->sortByDesc('hora_real')->first();

            // 1. Fecha formateada en español + indicadores
            $fechaCarbon = Carbon::parse($dia->fecha);
            $fechaFormato = $fechaCarbon->translatedFormat('l, j \\d\\e F \\d\\e Y');

            // 2. Turno esperado
            $entradaEsperadaStr = $entrada && $entrada->hora_esperada 
                ? ($entrada->hora_esperada instanceof Carbon ? $entrada->hora_esperada->format('H:i') : $entrada->hora_esperada) 
                : '--';
            $salidaEsperadaStr = $salida && $salida->hora_esperada 
                ? ($salida->hora_esperada instanceof Carbon ? $salida->hora_esperada->format('H:i') : $salida->hora_esperada) 
                : '--';
            $turnoEsperado = $entradaEsperadaStr . '-' . $salidaEsperadaStr;

            // 3. Entrada y Salida real
            $horaRealEntrada = $entrada?->hora_real?->format('H:i') ?? null;
            $horaRealSalida  = $salida?->hora_real?->format('H:i') ?? null;

            // 4. Atraso y Sal Ant
            $atrasoMinutos = 0;
            $salAntMinutos = 0;
            if ($entrada && $entrada->diferencia_minutos !== null && $entrada->diferencia_minutos > 0) {
                $atrasoMinutos = $entrada->diferencia_minutos;
            }
            if ($salida && $salida->diferencia_minutos !== null && $salida->diferencia_minutos < 0) {
                $salAntMinutos = abs($salida->diferencia_minutos);
            }

            // 5. Horas Extra y Jornada
            $horasExtMinutos = 0;
            $jorMinutos = 0;
            if ($entrada && $salida && $entrada->hora_real && $salida->hora_real) {
                $jorMinutos = $entrada->hora_real->diffInMinutes($salida->hora_real);
                if ($salida->diferencia_minutos !== null && $salida->diferencia_minutos > 0) {
                    $horasExtMinutos = $salida->diferencia_minutos;
                }
            }

            // 6. Ausencias y Justificaciones
            $omisiones = $dia->marcas->filter(fn($m) => str_starts_with($m->estado, 'faltante'))->count();
            $justificacionTexto = '';
            $marcaJustificada = $dia->marcas->firstWhere('estado', 'faltante_justificada');
            if ($marcaJustificada && $marcaJustificada->salida && $marcaJustificada->salida->tipoSalida) {
                $justificacionTexto = $marcaJustificada->salida->tipoSalida->nombre;
            }

            // Acumulamos totales
            $totalesMinutos['atraso'] += $atrasoMinutos;
            $totalesMinutos['sal_ant'] += $salAntMinutos;
            $totalesMinutos['ext'] += $horasExtMinutos;
            $totalesMinutos['jor'] += $jorMinutos;
            $totalesMinutos['ausen'] += $omisiones;

            // ============================================================
            // NUEVO: Enviar es_hoy y estado para el frontend
            // ============================================================
            $datosFilas[] = [
                'fecha' => $fechaFormato,
                'es_hoy' => $fechaCarbon->isToday(),      // ← NUEVO
                'estado' => $dia->estado,                  // ← NUEVO
                'turno' => $turnoEsperado,
                'entrada' => $horaRealEntrada,
                'salida' => $horaRealSalida,
                'atraso_min' => $atrasoMinutos,
                'sal_ant_min' => $salAntMinutos,
                'ausen' => $omisiones,
                'justificacion' => $justificacionTexto,
                'ext_min' => $horasExtMinutos,
                'jor_min' => $jorMinutos,
            ];
        }

        // Convertir totales de minutos a formato HH:MM
        $totalesFormato = [
            'atraso' => $this->minutosToString($totalesMinutos['atraso']),
            'sal_ant' => $this->minutosToString($totalesMinutos['sal_ant']),
            'ext' => $this->minutosToString($totalesMinutos['ext']),
            'jor' => $this->minutosToString($totalesMinutos['jor']),
            'ausen' => $totalesMinutos['ausen']
        ];

        return [
            'persona' => $persona,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'dias' => $datosFilas,
            'totales' => $totalesFormato,
        ];
    }
}