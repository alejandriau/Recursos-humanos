<?php

namespace App\Http\Controllers;

use App\Models\CasHistorialBonos;
use App\Models\Cas;
use App\Models\Persona;
use App\Models\ConfiguracionSalarioMinimo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CasHistorialBonosController extends Controller
{
    /**
     * Mostrar historial de cambios de bono de un CAS específico
     */
    public function index(Request $request)
    {
        $request->validate([
            'id_cas' => 'required|exists:cas,id',
            'tipo_cambio' => 'nullable|string|in:inicial,antiguedad,salario,ambos,ajuste',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date'
        ]);

        $cas = Cas::with(['persona', 'escalaBono', 'salarioMinimo'])->find($request->id_cas);

        $filtros = [
            'tipo_cambio' => $request->tipo_cambio,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin
        ];

        $historial = CasHistorialBonos::obtenerHistorialPorCas(
            $request->id_cas,
            array_filter($filtros)
        );

        return view('historial-bonos.index', [
            'cas' => $cas,
            'historial' => $historial,
            'filtros' => $filtros,
            'tiposCambio' => [
                'inicial' => 'Registro Inicial',
                'antiguedad' => 'Por Antigüedad',
                'salario' => 'Por Salario Mínimo',
                'ambos' => 'Por Ambos',
                'ajuste' => 'Ajuste Manual'
            ]
        ]);
    }

    /**
     * Mostrar historial de cambios de bono por persona
     */
    public function porPersona($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        // Obtener todos los CAS de la persona
        $casPersona = Cas::where('id_persona', $idPersona)
            ->with(['escalaBono', 'salarioMinimo'])
            ->orderBy('fecha_calculo_antiguedad', 'desc')
            ->get();

        // Obtener historial de bonos para cada CAS
        $historialCompleto = [];
        foreach ($casPersona as $cas) {
            $historialCas = CasHistorialBonos::obtenerHistorialPorCas($cas->id);

            $historialCompleto[] = [
                'cas' => $cas,
                'historial_bonos' => $historialCas,
                'total_cambios' => $historialCas->count()
            ];
        }

        $resumen = $this->generarResumenPersona($historialCompleto);

        return view('historial-bonos.por-persona', [
            'persona' => $persona,
            'historialCompleto' => $historialCompleto,
            'resumen' => $resumen
        ]);
    }

    /**
     * Mostrar estadísticas de cambios de bono
     */
    public function estadisticas(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'id_persona' => 'nullable|exists:persona,id'
        ]);

        $filtros = array_filter([
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'id_persona' => $request->id_persona
        ]);

        $cambios = CasHistorialBonos::generarReporteCambios($filtros);

        $estadisticas = [
            'total_cambios' => $cambios->count(),
            'por_tipo' => $cambios->groupBy('tipo_cambio')->map->count(),
            'cambios_por_mes' => $this->agruparCambiosPorMes($cambios),
            'personas_afectadas' => $cambios->pluck('cas.id_persona')->unique()->count(),
            'impacto_economico' => [
                'total_aumentos' => $cambios->sum('monto_bono_nuevo') - $cambios->sum('monto_bono_anterior'),
                'promedio_aumento' => $cambios->avg(function($item) {
                    return ($item->monto_bono_nuevo ?? 0) - ($item->monto_bono_anterior ?? 0);
                })
            ]
        ];

        return view('historial-bonos.estadisticas', [
            'estadisticas' => $estadisticas,
            'filtros' => $filtros,
            'personas' => Persona::all() // Para el select de filtro
        ]);
    }

    /**
     * Mostrar reporte completo de cambios
     */
    public function reporte(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'tipo_cambio' => 'nullable|string|in:inicial,antiguedad,salario,ambos,ajuste'
        ]);

        $filtros = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'tipo_cambio' => $request->tipo_cambio
        ];

        $reporte = CasHistorialBonos::generarReporteCambios($filtros);

        return view('historial-bonos.reporte', [
            'reporte' => $reporte,
            'filtros' => $filtros,
            'resumen' => [
                'total_registros' => $reporte->count(),
                'periodo' => "{$request->fecha_inicio} al {$request->fecha_fin}",
                'tipos_cambio' => $reporte->groupBy('tipo_cambio')->map->count()
            ],
            'tiposCambio' => [
                'inicial' => 'Registro Inicial',
                'antiguedad' => 'Por Antigüedad',
                'salario' => 'Por Salario Mínimo',
                'ambos' => 'Por Ambos',
                'ajuste' => 'Ajuste Manual'
            ]
        ]);
    }

    /**
     * Mostrar detalle de un cambio específico
     */
    public function show($id)
    {
        $cambio = CasHistorialBonos::with([
            'cas.persona',
            'salarioMinimoAnterior',
            'salarioMinimoNuevo',
            'usuario'
        ])->findOrFail($id);

        return view('historial-bonos.show', [
            'cambio' => $cambio,
            'analisis' => [
                'hubo_cambio_porcentaje' => $cambio->huboCambioPorcentaje(),
                'hubo_cambio_monto' => $cambio->huboCambioMonto(),
                'hubo_cambio_salario' => $cambio->huboCambioSalario(),
                'diferencia_monto' => $cambio->diferencia_monto,
                'diferencia_porcentaje' => $cambio->diferencia_porcentaje,
                'descripcion_cambio' => $cambio->descripcion_cambio
            ]
        ]);
    }

    /**
     * Mostrar formulario para cambio manual
     */
    public function create()
    {
        $casList = Cas::where('estado_cas', 'vigente')
            ->with('persona')
            ->get();

        $salariosMinimos = ConfiguracionSalarioMinimo::where('vigente', true)->get();

        return view('historial-bonos.create', [
            'casList' => $casList,
            'salariosMinimos' => $salariosMinimos
        ]);
    }

    /**
     * Procesar cambio manual de bono
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_cas' => 'required|exists:cas,id',
            'porcentaje_bono_nuevo' => 'required|numeric|min:0|max:100',
            'observacion' => 'required|string|max:500',
            'id_salario_minimo_nuevo' => 'nullable|exists:configuracion_salario_minimo,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $cas = Cas::findOrFail($request->id_cas);

                // Guardar valores anteriores
                $valoresAnteriores = [
                    'porcentaje_bono' => $cas->porcentaje_bono,
                    'monto_bono' => $cas->monto_bono,
                    'id_salario_minimo' => $cas->id_salario_minimo,
                    'anios_servicio' => $cas->anios_servicio,
                    'meses_servicio' => $cas->meses_servicio,
                    'dias_servicio' => $cas->dias_servicio
                ];

                // Calcular nuevo monto
                $salarioMinimo = $request->id_salario_minimo_nuevo
                    ? ConfiguracionSalarioMinimo::find($request->id_salario_minimo_nuevo)
                    : $cas->salarioMinimo;

                $montoBonoNuevo = $salarioMinimo->monto_salario_minimo * ($request->porcentaje_bono_nuevo / 100);

                // Actualizar CAS
                $cas->update([
                    'porcentaje_bono' => $request->porcentaje_bono_nuevo,
                    'monto_bono' => $montoBonoNuevo,
                    'id_salario_minimo' => $salarioMinimo->id,
                    'id_escala_bono' => null // Ya que es manual
                ]);

                // Registrar en historial
                CasHistorialBonos::registrarCambio([
                    'id_cas' => $cas->id,
                    'porcentaje_anterior' => $valoresAnteriores['porcentaje_bono'],
                    'porcentaje_nuevo' => $request->porcentaje_bono_nuevo,
                    'monto_anterior' => $valoresAnteriores['monto_bono'],
                    'monto_nuevo' => $montoBonoNuevo,
                    'id_salario_minimo_anterior' => $valoresAnteriores['id_salario_minimo'],
                    'id_salario_minimo_nuevo' => $salarioMinimo->id,
                    'anios_servicio_anterior' => $valoresAnteriores['anios_servicio'],
                    'anios_servicio_nuevo' => $cas->anios_servicio,
                    'meses_servicio_anterior' => $valoresAnteriores['meses_servicio'],
                    'meses_servicio_nuevo' => $cas->meses_servicio,
                    'dias_servicio_anterior' => $valoresAnteriores['dias_servicio'],
                    'dias_servicio_nuevo' => $cas->dias_servicio,
                    'tipo_cambio' => 'ajuste',
                    'observacion' => $request->observacion
                ]);
            });

            return redirect()->route('historial-bonos.index', ['id_cas' => $request->id_cas])
                ->with('success', 'Bono actualizado y registrado en historial correctamente');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar cambio manual: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar formulario para forzar recálculo
     */
    public function showForzarRecalculo()
    {
        $salariosMinimos = ConfiguracionSalarioMinimo::all();
        $totalCasVigentes = Cas::where('estado_cas', 'vigente')->count();

        return view('historial-bonos.forzar-recalculo', [
            'salariosMinimos' => $salariosMinimos,
            'totalCasVigentes' => $totalCasVigentes
        ]);
    }

    /**
     * Procesar recálculo forzado
     */
    public function forzarRecalculo(Request $request)
    {
        $request->validate([
            'id_salario_minimo' => 'nullable|exists:configuracion_salario_minimo,id',
            'observacion' => 'required|string|max:500'
        ]);

        try {
            $casVigentes = Cas::where('estado_cas', 'vigente')->get();
            $procesados = 0;
            $cambiosRegistrados = 0;

            foreach ($casVigentes as $cas) {
                $procesados++;

                // Recalcular bono (aquí asumimos que calcularBonoAntiguedad retorna true si hubo cambios)
                $huboCambios = $cas->calcularBonoAntiguedad(
                    $request->id_salario_minimo,
                    true // Registrar en historial
                );

                if ($huboCambios) {
                    $cambiosRegistrados++;
                }
            }

            return redirect()->route('historial-bonos.estadisticas')
                ->with('success', "Recálculo completado: {$procesados} CAS procesados, {$cambiosRegistrados} cambios registrados");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error en el recálculo: ' . $e->getMessage())
                ->withInput();
        }
    }

    // MÉTODOS PRIVADOS DE AYUDA

    private function generarResumenPersona(array $historialCompleto): array
    {
        $totalCambios = 0;
        $tiposCambio = [];
        $ultimoCambio = null;

        foreach ($historialCompleto as $casHistorial) {
            $totalCambios += $casHistorial['total_cambios'];

            foreach ($casHistorial['historial_bonos'] as $cambio) {
                $tiposCambio[$cambio->tipo_cambio] = ($tiposCambio[$cambio->tipo_cambio] ?? 0) + 1;

                if (!$ultimoCambio || $cambio->fecha_cambio > $ultimoCambio->fecha_cambio) {
                    $ultimoCambio = $cambio;
                }
            }
        }

        return [
            'total_cambios' => $totalCambios,
            'distribucion_tipos' => $tiposCambio,
            'ultimo_cambio' => $ultimoCambio
        ];
    }

    private function agruparCambiosPorMes($cambios): array
    {
        return $cambios->groupBy(function($item) {
            return $item->fecha_cambio->format('Y-m');
        })->map->count();
    }
}
