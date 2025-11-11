<?php

namespace App\Http\Controllers;

use App\Models\Cas;
use App\Models\Persona;
use App\Models\EscalaBonoAntiguedad;
use App\Models\ConfiguracionSalarioMinimo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\CasExport;
use Maatwebsite\Excel\Facades\Excel;

class CasController extends Controller
{
public function index(Request $request)
{
    // Obtener todas las personas activas (estado = 1)
    $query = Persona::activas()->with(['ultimoCas', 'cas']);

    // Filtro para personas con o sin registros en la tabla CAS
    if ($request->has('tiene_cas')) {
        $tieneCas = $request->tiene_cas;
        if ($tieneCas === 'con_cas') {
            $query->whereHas('cas');
        } elseif ($tieneCas === 'sin_cas') {
            $query->whereDoesntHave('cas');
        } elseif ($tieneCas === 'necesita_cas') {
            // Personas que necesitan CAS urgente (sin CAS)
            $query->whereDoesntHave('cas');
        }
    }

    // Filtro de búsqueda
    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function($q) use ($search) {
            $q->where('nombre', 'LIKE', "%{$search}%")
            ->orWhere('apellidoPat', 'LIKE', "%{$search}%")
            ->orWhere('apellidoMat', 'LIKE', "%{$search}%")
            ->orWhere('ci', 'LIKE', "%{$search}%")
            // 🔹 Buscar por nombre completo en distintos órdenes
            ->orWhere(DB::raw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat)"), 'LIKE', "%{$search}%")
            ->orWhere(DB::raw("CONCAT(apellidoPat, ' ', apellidoMat, ' ', nombre)"), 'LIKE', "%{$search}%")
            // 🔹 Buscar solo por ambos apellidos juntos
            ->orWhere(DB::raw("CONCAT(apellidoPat, ' ', apellidoMat)"), 'LIKE', "%{$search}%");
        });
    }

    // Ordenar
    $query->orderBy('apellidoPat')->orderBy('apellidoMat')->orderBy('nombre');

    // Si es exportación a Excel
    if ($request->has('export') && $request->export == 'excel') {
        $personas = $query->get();

        // Calcular datos para cada persona
        foreach ($personas as $persona) {
            $persona->calculo_antiguedad = $this->calcularAntiguedadPersona($persona);
            $persona->calculo_bono = $this->calcularBonoPersona($persona, $persona->calculo_antiguedad);
            $persona->nivel_alerta_persona = $this->calcularAlertaPersona($persona, $persona->calculo_antiguedad, $persona->calculo_bono);
        }

        // Filtrar solo los que necesitan CAS urgente si es el caso
        if ($request->has('tiene_cas') && $request->tiene_cas === 'necesita_cas') {
            $personas = $personas->filter(function($persona) {
                return $persona->nivel_alerta_persona === 'urgente' && !$persona->calculo_antiguedad['tiene_cas'];
            });
        }

        $fileName = 'control_cas_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new CasExport($personas), $fileName);
    }

    // Para la vista normal (paginación)
    $personas = $query->paginate(20);

    // Calcular antigüedad, bono y alertas para cada persona
    foreach ($personas as $persona) {
        $persona->calculo_antiguedad = $this->calcularAntiguedadPersona($persona);
        $persona->calculo_bono = $this->calcularBonoPersona($persona, $persona->calculo_antiguedad);
        $persona->nivel_alerta_persona = $this->calcularAlertaPersona($persona, $persona->calculo_antiguedad, $persona->calculo_bono);
    }

    // Si el filtro es "necesita_cas", filtrar solo los que tienen alerta urgente
    if ($request->has('tiene_cas') && $request->tiene_cas === 'necesita_cas') {
        $personas = $personas->filter(function($persona) {
            return $persona->nivel_alerta_persona === 'urgente' && !$persona->calculo_antiguedad['tiene_cas'];
        });

        // Reconstruir la paginación manualmente
        $perPage = 20;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $personas->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $personas = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $personas->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }

    return view('admin.cas.index', compact('personas'));
}

    /**
     * Calcular alerta para una persona
     */
    private function calcularAlertaPersona($persona, $antiguedad, $calculoBono)
    {
        $aniosServicio = $antiguedad['anios'];

        // Si no aplica bono (menos de 2 años) - VERDE
        if (!$calculoBono['aplica_bono']) {
            return 'normal';
        }

        // Si NO tiene CAS registrado y ya aplica bono - ROJO (urgente)
        if (!$antiguedad['tiene_cas']) {
            return 'urgente';
        }

        // Si TIENE CAS pero ya pasó al siguiente rango - ROJO (urgente)
        if ($antiguedad['tiene_cas'] && $this->haPasadoSiguienteRango($persona, $antiguedad)) {
            return 'advertencia';
        }

        // Si tiene CAS vigente y está en el rango correcto - VERDE
        if ($antiguedad['tiene_cas'] && $antiguedad['cas_vigente'] && !$this->haPasadoSiguienteRango($persona, $antiguedad)) {
            return 'normal';
        }

        // Si tiene CAS pero no está vigente - AMARILLO
        if ($antiguedad['tiene_cas'] && !$antiguedad['cas_vigente']) {
            return 'advertencia';
        }

        return 'normal';
    }

    /**
     * Verificar si ha pasado al siguiente rango de bono
     */
    private function haPasadoSiguienteRango($persona, $antiguedad)
    {
        if (!$persona->ultimoCas) {
            return false;
        }

        $aniosActuales = $antiguedad['anios'];
        $aniosCAS = $persona->ultimoCas->anios_servicio;

        // Obtener el rango actual del CAS
        $escalaActual = EscalaBonoAntiguedad::encontrarPorAniosServicio($aniosCAS);
        $escalaActualAnioFin = $escalaActual ? $escalaActual->anio_fin : null;

        // Obtener el rango actual basado en antigüedad real
        $escalaReal = EscalaBonoAntiguedad::encontrarPorAniosServicio($aniosActuales);
        $escalaRealAnioFin = $escalaReal ? $escalaReal->anio_fin : null;

        // Si los años actuales son mayores que el fin del rango del CAS, necesita actualizar
        if ($escalaActualAnioFin && $aniosActuales > $escalaActualAnioFin) {
            return true;
        }

        // Si cambió de escala (porcentaje diferente)
        if ($escalaActual && $escalaReal && $escalaActual->id != $escalaReal->id) {
            return true;
        }

        return false;
    }

    /**
     * Calcular antigüedad para una persona
     */
private function calcularAntiguedadPersona($persona)
{
    $hoy = Carbon::now();

    // Si tiene CAS vigente, calcular considerando posibles brechas temporales
    if ($persona->ultimoCas && $persona->ultimoCas->estado_cas == 'vigente') {
        $cas = $persona->ultimoCas;

        // Antigüedad base del CAS
        $aniosBase = $cas->anios_servicio;
        $mesesBase = $cas->meses_servicio;
        $diasBase = $cas->dias_servicio;

        // Fecha de cálculo del CAS y fecha de ingreso de la persona
        $fechaCalculoCas = Carbon::parse($cas->fecha_calculo_antiguedad);
        $fechaIngreso = Carbon::parse($persona->fechaIngreso);

        // Determinar desde cuándo calcular el tiempo adicional
        // Usar la fecha más reciente entre fecha cálculo CAS y fecha ingreso
        if ($fechaIngreso->greaterThan($fechaCalculoCas)) {
            // Si ingresó después del cálculo del CAS, calcular desde la fecha de ingreso
            $fechaInicioCalculo = $fechaIngreso;
            $tieneBrecha = true;
        } else {
            // Si el cálculo del CAS es más reciente o igual, calcular desde ahí
            $fechaInicioCalculo = $fechaCalculoCas;
            $tieneBrecha = false;
        }

        // Calcular tiempo transcurrido desde la fecha de inicio adecuada
        $diferencia = $hoy->diff($fechaInicioCalculo);

        // Sumar tiempo transcurrido al tiempo base del CAS
        $aniosTotal = $aniosBase + $diferencia->y;
        $mesesTotal = $mesesBase + $diferencia->m;
        $diasTotal = $diasBase + $diferencia->d;

        // Ajustar meses y días si exceden
        if ($diasTotal >= 30) {
            $mesesExtra = floor($diasTotal / 30);
            $mesesTotal += $mesesExtra;
            $diasTotal = $diasTotal % 30;
        }

        if ($mesesTotal >= 12) {
            $aniosExtra = floor($mesesTotal / 12);
            $aniosTotal += $aniosExtra;
            $mesesTotal = $mesesTotal % 12;
        }

        return [
            'anios' => $aniosTotal,
            'meses' => $mesesTotal,
            'dias' => $diasTotal,
            'tiene_cas' => true,
            'cas_vigente' => true,
            'fecha_base' => $cas->fecha_calculo_antiguedad,
            'fecha_ingreso' => $persona->fechaIngreso,
            'fecha_inicio_calculo' => $fechaInicioCalculo->toDateString(),
            'antiguedad_base' => "{$aniosBase}a {$mesesBase}m {$diasBase}d",
            'tiempo_adicional' => "{$diferencia->y}a {$diferencia->m}m {$diferencia->d}d",
            'tiene_brecha_temporal' => $tieneBrecha,
            'cas_anios' => $aniosBase,
            'cas_id' => $cas->id
        ];
    }

    // Si no tiene CAS, calcular desde fecha de ingreso
    $fechaIngreso = Carbon::parse($persona->fechaIngreso);
    $diferencia = $hoy->diff($fechaIngreso);

    return [
        'anios' => $diferencia->y,
        'meses' => $diferencia->m,
        'dias' => $diferencia->d,
        'tiene_cas' => false,
        'cas_vigente' => false,
        'fecha_base' => $persona->fechaIngreso,
        'antiguedad_base' => null,
        'cas_anios' => 0,
        'cas_id' => null
    ];
}

    /**
     * Calcular bono para una persona basado en su antigüedad
     */
    private function calcularBonoPersona($persona, $antiguedad)
    {
        $aniosServicio = $antiguedad['anios'];

        // Verificar si aplica bono (mínimo 2 años)
        $aplicaBono = $aniosServicio >= 2;

        if (!$aplicaBono) {
            return [
                'aplica_bono' => false,
                'porcentaje' => 0,
                'monto' => 0,
                'rango' => 'No aplica',
                'color' => 'secondary',
                'escala_actual' => null,
                'escala_cas' => null
            ];
        }

        // Buscar escala correspondiente a la antigüedad actual
        $escalaActual = EscalaBonoAntiguedad::encontrarPorAniosServicio($aniosServicio);

        // Buscar escala del CAS (si tiene)
        $escalaCAS = null;
        if ($antiguedad['tiene_cas'] && $persona->ultimoCas) {
            $escalaCAS = EscalaBonoAntiguedad::encontrarPorAniosServicio($antiguedad['cas_anios']);
        }

        $salarioVigente = ConfiguracionSalarioMinimo::obtenerSalarioVigente();

        if (!$escalaActual || !$salarioVigente) {
            return [
                'aplica_bono' => true,
                'porcentaje' => 0,
                'monto' => 0,
                'rango' => 'Error en cálculo',
                'color' => 'danger',
                'escala_actual' => $escalaActual,
                'escala_cas' => $escalaCAS
            ];
        }

        $montoBono = $salarioVigente->monto_salario_minimo * ($escalaActual->porcentaje_bono / 100);

        return [
            'aplica_bono' => true,
            'porcentaje' => $escalaActual->porcentaje_bono,
            'monto' => $montoBono,
            'rango' => $escalaActual->rango_texto,
            'escala_actual' => $escalaActual,
            'escala_cas' => $escalaCAS,
            'color' => 'success'
        ];
    }


public function create($idPersona = null)
{
    $personas = Persona::where('estado', 1)->get();

    $personaSeleccionada = null;

    if ($idPersona) {
        $personaSeleccionada = Persona::find($idPersona);
        if (!$personaSeleccionada) {
            return redirect()->route('cas.index')->with('error', 'Persona no encontrada');
        }
    } else {
        // Si no hay ID pero solo hay una persona disponible, seleccionarla automáticamente
        if ($personas->count() === 1) {
            $personaSeleccionada = $personas->first();
        }
    }

    $escalas = EscalaBonoAntiguedad::activas()->get();
    $salarioVigente = ConfiguracionSalarioMinimo::obtenerSalarioVigente();

    return view('admin.cas.create', compact('personas', 'personaSeleccionada', 'escalas', 'salarioVigente'));
}

    public function edit($id)
    {
        $cas = Cas::with(['persona', 'escalaBono', 'salarioMinimo'])->find($id);

        if (!$cas) {
            return redirect()->route('cas.index')->with('error', 'CAS no encontrado');
        }

        $personas = Persona::where('estado', 1)->get();
        $escalas = EscalaBonoAntiguedad::activas()->get();

        return view('admin.cas.edit', compact('cas', 'personas', 'escalas'));
    }

    public function show($id)
    {
        $cas = Cas::with([
            'persona',
            'escalaBono',
            'salarioMinimo',
            'historial.usuario'
        ])->find($id);

        if (!$cas) {
            return redirect()->route('cas.index')->with('error', 'CAS no encontrado');
        }

        // Cargar historial de bonos por separado (más eficiente)
        $historialBonos = $cas->historialBonos()
            ->with(['usuario', 'salarioMinimoAnterior', 'salarioMinimoNuevo'])
            ->orderBy('fecha_cambio', 'desc')
            ->take(10) // Solo últimos 10 para el resumen
            ->get();

        return view('admin.cas.show', compact('cas', 'historialBonos'));
    }

    /**
     * Calcular antigüedad para una persona
     */


    /**
     * Calcular bono para una persona basado en su antigüedad
     */

public function store(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'id_persona' => 'required|exists:persona,id',
            'fecha_ingreso_institucion' => 'required|date', // AÑADIDO
            'fecha_emision_cas' => 'required|date',
            'fecha_presentacion_rrhh' => 'required|date',
            'fecha_calculo_antiguedad' => 'required|date',
            'anios_servicio' => 'required|integer|min:0',
            'meses_servicio' => 'required|integer|min:0|max:11',
            'dias_servicio' => 'required|integer|min:0|max:30',
            'archivo_cas' => 'nullable|string|max:250',
            'periodo_calificacion' => 'nullable|string|max:100',
            'meses_calificacion' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string'
        ]);
        // 1. VERIFICAR Y CERRAR CAS ANTERIOR SI EXISTE
        $casAnterior = Cas::where('id_persona', $request->id_persona)
            ->where('estado_cas', 'vigente')
            ->first();

        if ($casAnterior) {
            // Cerrar CAS anterior
            $casAnterior->update(['estado_cas' => 'vencido']);

            // Registrar en historial de estados
            \App\Models\CasHistorial::create([
                'id_cas' => $casAnterior->id,
                'id_usuario' => auth()->id(),
                'estado_anterior' => 'vigente',
                'estado_nuevo' => 'vencido',
                'observacion' => 'Reemplazado por nuevo CAS ' . ($request->periodo_calificacion ?? 'sin período')
            ]);
        }

        $cas = new Cas();

        // Asignar campos básicos
        $cas->id_persona = $request->id_persona;
        $cas->fecha_ingreso_institucion = $request->fecha_ingreso_institucion; // IMPORTANTE
        $cas->fecha_emision_cas = $request->fecha_emision_cas;
        $cas->fecha_presentacion_rrhh = $request->fecha_presentacion_rrhh;
        $cas->fecha_calculo_antiguedad = $request->fecha_calculo_antiguedad;
        $cas->anios_servicio = $request->anios_servicio;
        $cas->meses_servicio = $request->meses_servicio;
        $cas->dias_servicio = $request->dias_servicio;
        $cas->archivo_cas = $request->archivo_cas;
        $cas->periodo_calificacion = $request->periodo_calificacion;
        $cas->meses_calificacion = $request->meses_calificacion;
        $cas->observaciones = $request->observaciones;

        // Calcular bono con los datos ingresados
        $calculoBono = $this->calcularBonoConAntiguedad(
            $request->anios_servicio,
            $request->meses_servicio,
            $request->dias_servicio
        );

        $cas->aplica_bono_antiguedad = $calculoBono['aplica_bono'];
        $cas->porcentaje_bono = $calculoBono['porcentaje'];
        $cas->monto_bono = $calculoBono['monto'];
        $cas->rango_antiguedad = $calculoBono['rango'];

        if (isset($calculoBono['escala'])) {
            $cas->id_escala_bono = $calculoBono['escala']->id;
        }

        // Asignar salario mínimo vigente
        $salarioVigente = ConfiguracionSalarioMinimo::where('vigente', true)->first();
        if ($salarioVigente) {
            $cas->id_salario_minimo = $salarioVigente->id;
        }

        $cas->id_usuario_registro = auth()->id();
        $cas->estado_cas = 'vigente';
        $cas->nivel_alerta = 'normal'; // Se actualizará con el método

        // Actualizar alerta basada en fechas
        $cas->actualizarAlerta();

        $cas->save();

        // 3. ✅ REGISTRAR EN HISTORIAL DE BONOS COMO 'inicial' (SIEMPRE para nuevo CAS)
        if ($cas->aplica_bono_antiguedad) {
            $observacion = $casAnterior
                ? "Nuevo CAS - Reemplaza CAS anterior #{$casAnterior->id}"
                : "Primer CAS registrado para la persona";

            \App\Models\CasHistorialBonos::registrarCambio([
                'id_cas' => $cas->id,
                'id_usuario' => auth()->id(),
                'porcentaje_anterior' => null,
                'porcentaje_nuevo' => $cas->porcentaje_bono,
                'monto_anterior' => null,
                'monto_nuevo' => $cas->monto_bono,
                'id_salario_minimo_anterior' => null,
                'id_salario_minimo_nuevo' => $cas->id_salario_minimo,
                'anios_servicio_anterior' => null,
                'anios_servicio_nuevo' => $cas->anios_servicio,
                'meses_servicio_anterior' => null,
                'meses_servicio_nuevo' => $cas->meses_servicio,
                'dias_servicio_anterior' => null,
                'dias_servicio_nuevo' => $cas->dias_servicio,
                'tipo_cambio' => 'inicial', // ← SIEMPRE 'inicial' para NUEVO CAS
                'observacion' => $observacion
            ]);
        }

        // 4. Registrar en historial de estados (nuevo CAS)
        \App\Models\CasHistorial::create([
            'id_cas' => $cas->id,
            'id_usuario' => auth()->id(),
            'estado_anterior' => null,
            'estado_nuevo' => 'vigente',
            'observacion' => 'Registro inicial del CAS'
        ]);

        DB::commit();

        $mensaje = $casAnterior
            ? 'CAS registrado exitosamente (reemplazó CAS anterior)'
            : 'CAS registrado exitosamente';

        return redirect()->route('cas.index')->with('success', $mensaje);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Error al registrar CAS: ' . $e->getMessage())->withInput();
    }
}

// Método para calcular bono (debe estar en el controlador)
private function calcularBonoConAntiguedad($anios, $meses, $dias)
{
    $totalMeses = ($anios * 12) + $meses + ($dias / 30);
    $totalAnios = $totalMeses / 12;

    // Buscar en la escala de bonos
    $escala = EscalaBonoAntiguedad::where('estado', true)
        ->where(function($query) use ($totalAnios) {
            $query->where('anio_inicio', '<=', $totalAnios)
                  ->where(function($q) use ($totalAnios) {
                      $q->where('anio_fin', '>=', $totalAnios)
                        ->orWhereNull('anio_fin');
                  });
        })
        ->first();

    $salarioMinimo = ConfiguracionSalarioMinimo::where('vigente', true)->first();
    $montoSalario = $salarioMinimo ? $salarioMinimo->monto_salario_minimo : 0;

    if ($escala && $totalAnios >= 2) { // Mínimo 2 años para bono
        $montoBono = ($montoSalario * $escala->porcentaje_bono) / 100;

        return [
            'aplica_bono' => true,
            'porcentaje' => $escala->porcentaje_bono,
            'monto' => $montoBono,
            'rango' => $escala->rango_texto,
            'escala' => $escala
        ];
    }

    return [
        'aplica_bono' => false,
        'porcentaje' => 0,
        'monto' => 0,
        'rango' => 'No aplica',
        'escala' => null
    ];
}
    /**
     * Calcular bono con antigüedad específica
     */


    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $cas = Cas::find($id);

            if (!$cas) {
                return redirect()->route('cas.index')->with('error', 'CAS no encontrado');
            }

            $request->validate([
                'fecha_emision_cas' => 'sometimes|date',
                'fecha_presentacion_rrhh' => 'sometimes|date',
                'fecha_calculo_antiguedad' => 'sometimes|date',
                'anios_servicio' => 'sometimes|integer|min:0',
                'meses_servicio' => 'sometimes|integer|min:0|max:11',
                'dias_servicio' => 'sometimes|integer|min:0|max:30',
                'archivo_cas' => 'nullable|string|max:250',
                'periodo_calificacion' => 'nullable|string|max:100',
                'meses_calificacion' => 'nullable|string|max:100',
                'observaciones' => 'nullable|string',
                'estado_cas' => 'sometimes|in:vigente,vencido,procesado'
            ]);

            $estadoAnterior = $cas->estado_cas;
            $alertaAnterior = $cas->nivel_alerta;

            $cas->fill($request->all());

            // Recalcular bono si cambia la antigüedad
            if ($request->has('anios_servicio') || $request->has('meses_servicio') || $request->has('dias_servicio')) {
                $calculoBono = $this->calcularBonoConAntiguedad(
                    $request->anios_servicio,
                    $request->meses_servicio,
                    $request->dias_servicio
                );

                $cas->aplica_bono_antiguedad = $calculoBono['aplica_bono'];
                $cas->porcentaje_bono = $calculoBono['porcentaje'];
                $cas->monto_bono = $calculoBono['monto'];
                $cas->rango_antiguedad = $calculoBono['rango'];

                if (isset($calculoBono['escala'])) {
                    $cas->id_escala_bono = $calculoBono['escala']->id;
                }
            }

            $cas->actualizarAlerta();
            $cas->save();

            // Registrar en historial si cambió el estado o alerta
            if ($estadoAnterior != $cas->estado_cas || $alertaAnterior != $cas->nivel_alerta) {
                \App\Models\CasHistorial::create([
                    'id_cas' => $cas->id,
                    'id_usuario' => auth()->id(),
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $cas->estado_cas,
                    'alerta_anterior' => $alertaAnterior,
                    'alerta_nuevo' => $cas->nivel_alerta,
                    'observacion' => 'Actualización de estado/alertas'
                ]);
            }

            DB::commit();

            return redirect()->route('cas.show', $cas->id)->with('success', 'CAS actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar CAS: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $cas = Cas::find($id);

            if (!$cas) {
                return redirect()->route('cas.index')->with('error', 'CAS no encontrado');
            }

            $cas->historial()->delete();
            $cas->delete();

            DB::commit();

            return redirect()->route('cas.index')->with('success', 'CAS eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cas.index')->with('error', 'Error al eliminar CAS: ' . $e->getMessage());
        }
    }

    public function calcularBonoPersonaIndividual($idPersona)
    {
        try {
            $persona = Persona::with(['ultimoCas'])->find($idPersona);

            if (!$persona) {
                return redirect()->back()->with('error', 'Persona no encontrada');
            }

            $antiguedad = $this->calcularAntiguedadPersona($persona);
            $calculoBono = $this->calcularBonoPersona($persona, $antiguedad);

            return view('admin.cas.calculo-bono', compact('persona', 'antiguedad', 'calculoBono'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al calcular bono: ' . $e->getMessage());
        }
    }

    public function actualizarAlertas(Request $request)
    {
        try {
            $casVigentes = Cas::vigentes()->get();
            $actualizados = 0;

            foreach ($casVigentes as $cas) {
                $alertaAnterior = $cas->nivel_alerta;
                $cas->actualizarAlerta();

                if ($alertaAnterior != $cas->nivel_alerta) {
                    \App\Models\CasHistorial::create([
                        'id_cas' => $cas->id,
                        'id_usuario' => auth()->id(),
                        'alerta_anterior' => $alertaAnterior,
                        'alerta_nuevo' => $cas->nivel_alerta,
                        'observacion' => 'Actualización automática de alerta'
                    ]);
                    $actualizados++;
                }
            }

            return redirect()->route('cas.index')->with('success', "Alertas actualizadas: {$actualizados} registros modificados");

        } catch (\Exception $e) {
            return redirect()->route('cas.index')->with('error', 'Error al actualizar alertas: ' . $e->getMessage());
        }
    }


    public function calcularBono($anios_servicio)
    {
        // 1️⃣ Obtener la escala de bono según años de servicio
        $escala = EscalaBonoAntiguedad::where('anio_inicio', '<=', $anios_servicio)
            ->where(function ($query) use ($anios_servicio) {
                $query->where('anio_fin', '>=', $anios_servicio)
                    ->orWhereNull('anio_fin');
            })
            ->where('estado', true)
            ->first();

        if (!$escala) {
            return [
                'id_escala_bono' => null,
                'porcentaje_bono' => 0,
                'monto_bono' => 0,
                'rango_antiguedad' => 'Sin rango',
            ];
        }

        // 2️⃣ Obtener el salario mínimo vigente
        $salarioMinimo = ConfiguracionSalarioMinimo::where('vigente', true)->first();

        if (!$salarioMinimo) {
            return [
                'id_escala_bono' => $escala->id,
                'porcentaje_bono' => $escala->porcentaje_bono,
                'monto_bono' => 0,
                'rango_antiguedad' => $escala->rango_texto,
            ];
        }

        // 3️⃣ Calcular monto del bono
        $montoBono = ($salarioMinimo->monto_salario_minimo * $escala->porcentaje_bono) / 100;

        // 4️⃣ Retornar resultados
        return [
            'id_escala_bono' => $escala->id,
            'porcentaje_bono' => $escala->porcentaje_bono,
            'monto_bono' => $montoBono,
            'rango_antiguedad' => $escala->rango_texto,
            'id_salario_minimo' => $salarioMinimo->id,
        ];
    }
}
