<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\SituacionesEspeciales;
use App\Models\InmovilidadesLaborales;
use App\Models\Discapacidades;
use App\Models\DependientesDiscapacitados;
use App\Models\PeriodosTemporales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InmovilidadController extends Controller
{
    /**
     * Lista de inmovilidades activas
     */
    public function index()
    {
        $inmovilidades = InmovilidadesLaborales::with(['situacion.persona', 'aprobadoPor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('inmovilidades.index', compact('inmovilidades'));
    }

    /**
     * Formulario para crear nueva inmovilidad
     */
    public function create()
    {
        $tiposInmovilidad = InmovilidadesLaborales::$tiposInmovilidad;
        $personas = Persona::where('estado', '1')
            ->orderBy('apellidoPat')
            ->get();

        return view('inmovilidades.create', compact('tiposInmovilidad', 'personas'));
    }

    /**
     * Guardar nueva inmovilidad
     */
    public function store(Request $request)
    {
        $request->validate([
            'persona_id' => 'required|exists:persona,id',
            'tipo_inmovilidad' => 'required|in:' . implode(',', InmovilidadesLaborales::$tiposInmovilidad),
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'numero_resolucion_rrhh' => 'required|string|max:100',
            'fecha_resolucion_rrhh' => 'required|date',
            'norma_legal' => 'required|string|max:200',
            'articulo' => 'required|string|max:50',
            'resolucion_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'renovable' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear la situación especial
            $situacion = SituacionesEspeciales::create([
                'persona_id' => $request->persona_id,
                'tipo_situacion' => $this->mapTipoInmovilidadToSituacion($request->tipo_inmovilidad),
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'vigente' => true,
                'tiene_inmovilidad' => true,
                'numero_resolucion' => $request->numero_resolucion_rrhh,
                'fecha_resolucion' => $request->fecha_resolucion_rrhh,
                'observaciones_rrhh' => $request->observaciones,
            ]);

            // 2. Guardar documento de resolución
            $resolucionPath = $request->file('resolucion_path')->store('inmovilidades/resoluciones', 'public');

            // 3. Guardar solicitud si existe
            $solicitudPath = null;
            if ($request->hasFile('solicitud_path')) {
                $solicitudPath = $request->file('solicitud_path')->store('inmovilidades/solicitudes', 'public');
            }

            // 4. Crear la inmovilidad laboral
            $inmovilidad = InmovilidadesLaborales::create([
                'situacion_id' => $situacion->id,
                'tipo_inmovilidad' => $request->tipo_inmovilidad,
                'fecha_inicio_inmovilidad' => $request->fecha_inicio,
                'fecha_fin_inmovilidad' => $request->fecha_fin,
                'renovable' => $request->renovable ?? false,
                'norma_legal' => $request->norma_legal,
                'articulo' => $request->articulo,
                'numero_resolucion_rrhh' => $request->numero_resolucion_rrhh,
                'fecha_resolucion_rrhh' => $request->fecha_resolucion_rrhh,
                'resolucion_path' => $resolucionPath,
                'solicitud_path' => $solicitudPath,
                'estado' => InmovilidadesLaborales::ESTADO_PENDIENTE,
            ]);

            // 5. Procesar datos específicos según el tipo
            $this->procesarDatosEspecificos($request, $situacion);

            DB::commit();

            return redirect()->route('rrhh.inmovilidades.show', $inmovilidad)
                ->with('success', 'Inmovilidad registrada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles de inmovilidad
     */
    public function show(InmovilidadesLaborales $inmovilidad)
    {
        $inmovilidad->load(['situacion.persona', 'situacion.discapacidad',
                           'situacion.dependienteDiscapacitado', 'situacion.periodoTemporal',
                           'aprobadoPor']);

        return view('inmovilidades.show', compact('inmovilidad'));
    }

    /**
     * Aprobar inmovilidad
     */



    /**
     * Buscar personas para asignar inmovilidad
     */
    public function buscarPersona(Request $request)
    {
        $request->validate([
            'termino' => 'required|string|min:2',
        ]);

        $personas = Persona::where(function($query) use ($request) {
                $query->where('nombre', 'LIKE', "%{$request->termino}%")
                    ->orWhere('apellidoPat', 'LIKE', "%{$request->termino}%")
                    ->orWhere('ci', 'LIKE', "%{$request->termino}%");
            })
            ->where('estado', '1')
            ->limit(10)
            ->get(['id', 'nombre', 'apellidoPat', 'ci']);

        return response()->json($personas);
    }

    /**
     * Obtener detalles de persona para inmovilidad
     */
    public function obtenerDetallesPersona(Persona $persona)
    {
        $situacionesActivas = $persona->situacionesEspeciales()
            ->where('vigente', true)
            ->get();

        return response()->json([
            'persona' => $persona,
            'situaciones_activas' => $situacionesActivas,
        ]);
    }

    /**
     * Mapear tipo de inmovilidad a tipo de situación
     */
    private function mapTipoInmovilidadToSituacion($tipoInmovilidad)
    {
        $map = [
            InmovilidadesLaborales::TIPO_POR_DISCAPACIDAD => SituacionesEspeciales::TIPO_DISCAPACIDAD,
            InmovilidadesLaborales::TIPO_POR_TUTOR_DISCAPACITADO => SituacionesEspeciales::TIPO_TUTOR_DISCAPACITADO,
            InmovilidadesLaborales::TIPO_POR_DEPENDIENTE_DISCAPACITADO => SituacionesEspeciales::TIPO_DEPENDIENTE_DISCAPACITADO,
            InmovilidadesLaborales::TIPO_POR_EMBARAZO => SituacionesEspeciales::TIPO_EMBARAZO,
            InmovilidadesLaborales::TIPO_POR_LACTANCIA => SituacionesEspeciales::TIPO_LACTANCIA,
            InmovilidadesLaborales::TIPO_POR_PATERNIDAD => SituacionesEspeciales::TIPO_PATERNIDAD,
        ];

        return $map[$tipoInmovilidad] ?? SituacionesEspeciales::TIPO_DISCAPACIDAD;
    }

    /**
     * Procesar datos específicos según el tipo de inmovilidad
     */
    private function procesarDatosEspecificos(Request $request, SituacionesEspeciales $situacion)
    {
        switch ($request->tipo_inmovilidad) {
            case InmovilidadesLaborales::TIPO_POR_DISCAPACIDAD:
                $this->guardarDiscapacidad($request, $situacion);
                break;

            case InmovilidadesLaborales::TIPO_POR_TUTOR_DISCAPACITADO:
                $this->guardarDiscapacidadTutor($request, $situacion);
                break;

            case InmovilidadesLaborales::TIPO_POR_DEPENDIENTE_DISCAPACITADO:
                $this->guardarDependienteDiscapacitado($request, $situacion);
                break;

            case InmovilidadesLaborales::TIPO_POR_EMBARAZO:
            case InmovilidadesLaborales::TIPO_POR_LACTANCIA:
            case InmovilidadesLaborales::TIPO_POR_PATERNIDAD:
                $this->guardarPeriodoTemporal($request, $situacion);
                break;
        }
    }

    private function guardarDiscapacidad(Request $request, SituacionesEspeciales $situacion)
    {
        $request->validate([
            'tipo_discapacidad' => 'required|string',
            'grado_discapacidad' => 'required|in:leve,moderado,severa',
            'porcentaje_discapacidad' => 'required|numeric|min:0|max:100',
            'entidad_certificadora' => 'required|string',
            'fecha_certificacion' => 'required|date',
            'certificado_medico' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $certificadoPath = $request->file('certificado_medico')
            ->store('discapacidades/certificados', 'public');

        Discapacidades::create([
            'situacion_id' => $situacion->id,
            'tipo' => $request->tipo_discapacidad,
            'grado' => $request->grado_discapacidad,
            'porcentaje' => $request->porcentaje_discapacidad,
            'entidad_certificadora' => $request->entidad_certificadora,
            'fecha_certificacion' => $request->fecha_certificacion,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'codigo_certificado' => $request->codigo_certificado,
            'certificado_medico_path' => $certificadoPath,
        ]);
    }

    private function guardarDiscapacidadTutor(Request $request, SituacionesEspeciales $situacion)
    {
        $request->validate([
            'tutor_id' => 'required|exists:persona,id',
            'parentesco_tutor' => 'required|string',
            'tipo_discapacidad_tutor' => 'required|string',
            'grado_discapacidad_tutor' => 'required|in:leve,moderado,severa',
            'porcentaje_discapacidad_tutor' => 'required|numeric|min:0|max:100',
            'certificado_medico_tutor' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $certificadoPath = $request->file('certificado_medico_tutor')
            ->store('discapacidades/certificados_tutores', 'public');

        Discapacidades::create([
            'situacion_id' => $situacion->id,
            'tutor_id' => $request->tutor_id,
            'parentesco_tutor' => $request->parentesco_tutor,
            'tipo' => $request->tipo_discapacidad_tutor,
            'grado' => $request->grado_discapacidad_tutor,
            'porcentaje' => $request->porcentaje_discapacidad_tutor,
            'entidad_certificadora' => $request->entidad_certificadora_tutor,
            'fecha_certificacion' => $request->fecha_certificacion_tutor,
            'fecha_vencimiento' => $request->fecha_vencimiento_tutor,
            'certificado_medico_path' => $certificadoPath,
        ]);
    }

    private function guardarDependienteDiscapacitado(Request $request, SituacionesEspeciales $situacion)
    {
        $request->validate([
            'nombre_dependiente' => 'required|string',
            'parentesco_dependiente' => 'required|string',
            'fecha_nacimiento_dependiente' => 'required|date',
            'tipo_discapacidad_dependiente' => 'required|string',
            'porcentaje_discapacidad_dependiente' => 'required|numeric|min:0|max:100',
            'grado_dependencia' => 'required|in:total,parcial',
            'certificado_discapacidad_dependiente' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $certificadoPath = $request->file('certificado_discapacidad_dependiente')
            ->store('dependientes/certificados', 'public');

        DependientesDiscapacitados::create([
            'situacion_id' => $situacion->id,
            'nombre_dependiente' => $request->nombre_dependiente,
            'parentesco' => $request->parentesco_dependiente,
            'fecha_nacimiento' => $request->fecha_nacimiento_dependiente,
            'tipo_discapacidad' => $request->tipo_discapacidad_dependiente,
            'porcentaje_discapacidad' => $request->porcentaje_discapacidad_dependiente,
            'grado_dependencia' => $request->grado_dependencia,
            'certificado_discapacidad_path' => $certificadoPath,
            'necesidades_especiales' => $request->necesidades_especiales,
        ]);
    }

    private function guardarPeriodoTemporal(Request $request, SituacionesEspeciales $situacion)
    {
        $validacion = [
            'dias_prenatales' => 'nullable|integer|min:0',
            'dias_postnatales' => 'nullable|integer|min:0',
            'dias_lactancia' => 'nullable|integer|min:0',
            'dias_paternidad' => 'nullable|integer|min:0',
        ];

        if ($request->tipo_inmovilidad == InmovilidadesLaborales::TIPO_POR_EMBARAZO) {
            $validacion['fecha_probable_parto'] = 'required|date|after:today';
            $validacion['certificado_embarazo'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        if ($request->tipo_inmovilidad == InmovilidadesLaborales::TIPO_POR_LACTANCIA) {
            $validacion['fecha_inicio_lactancia'] = 'required|date';
            $validacion['fecha_fin_lactancia'] = 'required|date|after:fecha_inicio_lactancia';
        }

        if ($request->tipo_inmovilidad == InmovilidadesLaborales::TIPO_POR_PATERNIDAD) {
            $validacion['fecha_nacimiento'] = 'required|date';
            $validacion['certificado_nacimiento'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        $request->validate($validacion);

        $data = [
            'situacion_id' => $situacion->id,
            'dias_prenatales' => $request->dias_prenatales ?? 0,
            'dias_postnatales' => $request->dias_postnatales ?? 0,
            'dias_lactancia' => $request->dias_lactancia ?? 0,
            'dias_paternidad' => $request->dias_paternidad ?? 0,
            'dias_usados' => 0,
        ];

        if ($request->tipo_inmovilidad == InmovilidadesLaborales::TIPO_POR_EMBARAZO) {
            $data['fecha_probable_parto'] = $request->fecha_probable_parto;
            $certificadoPath = $request->file('certificado_embarazo')
                ->store('periodos/embarazos', 'public');
            $data['certificado_embarazo_path'] = $certificadoPath;
        }

        if ($request->tipo_inmovilidad == InmovilidadesLaborales::TIPO_POR_LACTANCIA) {
            $data['fecha_inicio_lactancia'] = $request->fecha_inicio_lactancia;
            $data['fecha_fin_lactancia'] = $request->fecha_fin_lactancia;
        }

        if ($request->tipo_inmovilidad == InmovilidadesLaborales::TIPO_POR_PATERNIDAD) {
            $data['fecha_nacimiento'] = $request->fecha_nacimiento;
            $certificadoPath = $request->file('certificado_nacimiento')
                ->store('periodos/nacimientos', 'public');
            $data['certificado_nacimiento_path'] = $certificadoPath;
        }

        $data['dias_restantes'] = $data['dias_prenatales'] + $data['dias_postnatales'] +
                                   $data['dias_lactancia'] + $data['dias_paternidad'];

        PeriodosTemporales::create($data);
    }
    /**
     * Obtener campos específicos según el tipo de inmovilidad
     */
    public function getCamposEspecificos($tipo, Request $request)
    {
        $personaId = $request->get('persona_id');

        return view('inmovilidades.partials.campos_especificos', compact('tipo', 'personaId'));
    }

    /**
     * Aprobar inmovilidad (respuesta JSON)
     */
    public function aprobar(InmovilidadesLaborales $inmovilidad, Request $request)
    {
        try {
            DB::beginTransaction();

            $inmovilidad->update([
                'estado' => InmovilidadesLaborales::ESTADO_APROBADO,
                'aprobado_por' => auth()->id(),
                'fecha_aprobacion' => now(),
            ]);

            $inmovilidad->situacion->update([
                'vigente' => true,
                'observaciones_rrhh' => $request->observaciones,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inmovilidad aprobada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar inmovilidad (respuesta JSON)
     */
    public function rechazar(InmovilidadesLaborales $inmovilidad, Request $request)
    {
        try {
            DB::beginTransaction();

            $inmovilidad->update([
                'estado' => InmovilidadesLaborales::ESTADO_RECHAZADO,
                'aprobado_por' => auth()->id(),
                'fecha_aprobacion' => now(),
            ]);

            $inmovilidad->situacion->update([
                'vigente' => false,
                'observaciones_rrhh' => $request->motivo_rechazo,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inmovilidad rechazada'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalizar inmovilidad (respuesta JSON)
     */
    public function finalizar(InmovilidadesLaborales $inmovilidad)
    {
        try {
            DB::beginTransaction();

            $inmovilidad->update([
                'estado' => InmovilidadesLaborales::ESTADO_FINALIZADO,
            ]);

            $inmovilidad->situacion->update([
                'vigente' => false,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inmovilidad finalizada'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar: ' . $e->getMessage()
            ], 500);
        }
    }
}
