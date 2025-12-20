<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Puesto;
use App\Models\UnidadOrganizacional;
use App\Exports\ReportePersonalizadoExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportePersonasController extends Controller
{
    /**
     * Vista principal para generar reportes
     */
    public function index()
    {
        $unidades = UnidadOrganizacional::where('estado', true)->get();
        $tiposContrato = ['PERMANENTE', 'EVENTUAL'];

        // Columnas disponibles organizadas por categorías
        $columnas = [
            'Datos Personales' => [
                'ci' => 'CI / Documento',
                'nombre_completo' => 'Nombre Completo',
                'fecha_nacimiento' => 'Fecha Nacimiento',
                'edad' => 'Edad',
                'sexo' => 'Sexo',
                'telefono' => 'Teléfono',
            ],
            'Datos Laborales' => [
                'puesto' => 'Puesto Actual',
                'unidad' => 'Unidad Organizacional',
                'nivel_jerarquico' => 'Nivel Jerárquico',
                'salario' => 'Salario',
                'tipo_contrato' => 'Tipo de Contrato',
                'fecha_ingreso' => 'Fecha de Ingreso',
                'estado_laboral' => 'Estado Laboral',
                'es_jefatura' => 'Es Jefatura',
            ],
            'Antigüedad y CAS' => [
                'antiguedad_anios' => 'Años de Servicio',
                'antiguedad_meses' => 'Meses de Servicio',
                'bono_antiguedad' => 'Bono Antigüedad',
                'estado_cas' => 'Estado CAS',
                'fecha_emision_cas' => 'Fecha Emisión CAS',
            ],
            'Formación' => [
                'profesiones' => 'Profesiones',
                'universidades' => 'Universidades',
                'registros_profesionales' => 'Registros Profesionales',
                'total_certificados' => 'Total Certificados',
                'licencia_militar' => 'Licencia Militar',
            ],
        ];

        return view('reportes.personas', compact('unidades', 'tiposContrato', 'columnas'));
    }

    /**
     * Vista previa del reporte
     */
    public function vistaPrevia(Request $request)
    {
        $request->validate([
            'columnas' => 'required|array|min:1',
        ]);

        $personas = $this->obtenerPersonasFiltradas($request);
        $columnasSeleccionadas = $request->columnas;
        $filtrosAplicados = $this->obtenerFiltrosAplicados($request);

        // Obtener nombres de columnas para mostrar
        $nombresColumnas = $this->getNombresColumnas();
        $columnasMostrar = [];
        foreach ($columnasSeleccionadas as $columna) {
            $columnasMostrar[$columna] = $nombresColumnas[$columna] ?? $columna;
        }

        return view('reportes.vista-previa', compact('personas', 'columnasMostrar', 'filtrosAplicados'));
    }

    /**
     * Exportar a Excel
     */
    public function exportarExcel(Request $request)
    {
        $request->validate([
            'columnas' => 'required|array|min:1',
        ]);

        $columnas = $request->columnas;
        $filtros = $request->except(['_token', 'columnas']);

        $nombreArchivo = 'reporte_personal_' . date('Y-m-d_H-i') . '.xlsx';

        return Excel::download(
            new ReportePersonalizadoExport($columnas, $filtros),
            $nombreArchivo
        );
    }

    /**
     * Exportar a PDF
     */
    public function exportarPDF(Request $request)
    {
        $request->validate([
            'columnas' => 'required|array|min:1',
        ]);

        $personas = $this->obtenerPersonasFiltradas($request);
        $columnasSeleccionadas = $request->columnas;
        $filtrosAplicados = $this->obtenerFiltrosAplicados($request);

        $nombresColumnas = $this->getNombresColumnas();
        $columnasMostrar = [];
        foreach ($columnasSeleccionadas as $columna) {
            $columnasMostrar[$columna] = $nombresColumnas[$columna] ?? $columna;
        }

        $pdf = Pdf::loadView('reportes.pdf.personas', [
            'personas' => $personas,
            'columnas' => $columnasMostrar,
            'filtros' => $filtrosAplicados,
            'total' => $personas->count(),
            'fechaReporte' => now()->format('d/m/Y H:i'),
        ]);

        $nombreArchivo = 'reporte_personal_' . date('Y-m-d_H-i') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar a CSV
     */
    public function exportarCSV(Request $request)
    {
        $request->validate([
            'columnas' => 'required|array|min:1',
        ]);

        $columnas = $request->columnas;
        $filtros = $request->except(['_token', 'columnas']);

        $nombreArchivo = 'reporte_personal_' . date('Y-m-d_H-i') . '.csv';

        return Excel::download(
            new ReportePersonalizadoExport($columnas, $filtros),
            $nombreArchivo,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Obtener datos filtrados de personas (CORREGIDO)
     */
    private function obtenerPersonasFiltradas(Request $request)
    {
        $query = Persona::where('persona.estado', 1)
            ->select('persona.*')
            ->with([
                'historialActivo.puesto.unidadOrganizacional',
                'casActual',
                'profesiones' => function($q) {
                    $q->where('estado', 1);
                },
                'certificados' => function($q) {
                    $q->where('estado', 1);
                },
                'licenciaMilitar' => function($q) {
                    $q->where('estado', 1);
                }
            ]);

        // Aplicar filtros
        if ($request->filled('unidad_id')) {
            $query->whereHas('historialActivo.puesto.unidadOrganizacional', function($q) use ($request) {
                $q->where('id', $request->unidad_id);
            });
        }

        if ($request->filled('tipo_contrato')) {
            $query->whereHas('historialActivo.puesto', function($q) use ($request) {
                $q->where('tipoContrato', $request->tipo_contrato);
            });
        }

        if ($request->filled('nivel_jerarquico')) {
            $query->whereHas('historialActivo.puesto', function($q) use ($request) {
                $q->where('nivelJerarquico', $request->nivel_jerarquico);
            });
        }

        if ($request->filled('es_jefatura')) {
            $query->whereHas('historialActivo.puesto', function($q) use ($request) {
                $q->where('esJefatura', $request->es_jefatura == 'si');
            });
        }

        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        if ($request->filled('estado_cas')) {
            $query->whereHas('casActual', function($q) use ($request) {
                $q->where('estado_cas', $request->estado_cas);
            });
        }

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('ci', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellidoPat', 'LIKE', "%{$busqueda}%");
            });
        }

        // Ordenar
        if ($request->filled('ordenar_por')) {
            $orden = $request->ordenar_direccion ?? 'asc';
            $query->orderBy($request->ordenar_por, $orden);
        } else {
            $query->orderBy('apellidoPat')->orderBy('nombre');
        }

        // Límite de registros
        if ($request->filled('limite') && $request->limite > 0) {
            $query->limit($request->limite);
        }

        return $query->get()->map(function($persona) {
            return $this->formatearDatosPersona($persona);
        });
    }

    /**
     * Formatear datos de persona para reporte (CORREGIDO - manejo de null)
     */
    private function formatearDatosPersona($persona)
    {
        // Obtener datos con verificaciones de null
        $historial = $persona->historialActivo ?? null;
        $puesto = $historial->puesto ?? null;
        $unidad = $puesto->unidadOrganizacional ?? null;
        $cas = $persona->casActual ?? null;

        // Manejar fechas con null safety
        $fechaNacimiento = $persona->fechaNacimiento ?? null;
        $fechaIngreso = $historial->fecha_inicio ?? null;
        $fechaEmisionCas = $cas->fecha_emision_cas ?? null;

        return [
            // Datos básicos (siempre disponibles)
            'id' => $persona->id,
            'ci' => $persona->ci ?? '',
            'nombre_completo' => ($persona->nombre ?? '') . ' ' .
                               ($persona->apellidoPat ?? '') . ' ' .
                               ($persona->apellidoMat ?? ''),
            'nombre' => $persona->nombre ?? '',
            'apellido_paterno' => $persona->apellidoPat ?? '',
            'apellido_materno' => $persona->apellidoMat ?? '',
            'fecha_nacimiento' => $fechaNacimiento ? $fechaNacimiento->format('d/m/Y') : '',
            'edad' => $fechaNacimiento ? now()->diffInYears($fechaNacimiento) : '',
            'sexo' => $persona->sexo ?? '',
            'telefono' => $persona->telefono ?? '',

            // Datos laborales (pueden ser null)
            'puesto' => $puesto->denominacion ?? 'Sin asignar',
            'unidad' => $unidad->nombre ?? 'Sin unidad',
            'nivel_jerarquico' => $puesto->nivelJerarquico ?? '',
            'salario' => $puesto ? number_format($puesto->haber, 2) : '0.00',
            'tipo_contrato' => $puesto->tipoContrato ?? '',
            'fecha_ingreso' => $fechaIngreso ? $fechaIngreso->format('d/m/Y') : '',
            'estado_laboral' => $historial ? ($historial->estado ?? '') : 'Sin historial',
            'es_jefatura' => $puesto ? ($puesto->esJefatura ? 'Sí' : 'No') : 'No',

            // CAS (puede ser null)
            'antiguedad_anios' => $cas->anios_servicio ?? '',
            'antiguedad_meses' => $cas->meses_servicio ?? '',
            'bono_antiguedad' => $cas ? number_format($cas->monto_bono, 2) : '0.00',
            'estado_cas' => $cas->estado_cas ?? '',
            'fecha_emision_cas' => $fechaEmisionCas ? $fechaEmisionCas->format('d/m/Y') : '',

            // Formación
            'profesiones' => $persona->profesiones->pluck('universidad')->implode(', '),
            'universidades' => $persona->profesiones->pluck('universidad')->unique()->implode(', '),
            'registros_profesionales' => $persona->profesiones->pluck('registro')->filter()->implode(', '),
            'total_certificados' => $persona->certificados->count(),
            'licencia_militar' => $persona->licenciaMilitar->codigo ?? '',
        ];
    }

    /**
     * Obtener nombres amigables de columnas
     */
    private function getNombresColumnas()
    {
        return [
            'ci' => 'CI / Documento',
            'nombre_completo' => 'Nombre Completo',
            'nombre' => 'Nombre',
            'apellido_paterno' => 'Apellido Paterno',
            'apellido_materno' => 'Apellido Materno',
            'fecha_nacimiento' => 'Fecha Nacimiento',
            'edad' => 'Edad',
            'sexo' => 'Sexo',
            'telefono' => 'Teléfono',
            'puesto' => 'Puesto',
            'unidad' => 'Unidad Organizacional',
            'nivel_jerarquico' => 'Nivel Jerárquico',
            'salario' => 'Salario (Bs.)',
            'tipo_contrato' => 'Tipo de Contrato',
            'fecha_ingreso' => 'Fecha de Ingreso',
            'estado_laboral' => 'Estado Laboral',
            'es_jefatura' => 'Es Jefatura',
            'antiguedad_anios' => 'Años de Servicio',
            'antiguedad_meses' => 'Meses de Servicio',
            'bono_antiguedad' => 'Bono Antigüedad (Bs.)',
            'estado_cas' => 'Estado CAS',
            'fecha_emision_cas' => 'Fecha Emisión CAS',
            'profesiones' => 'Profesiones',
            'universidades' => 'Universidades',
            'registros_profesionales' => 'Registros Profesionales',
            'total_certificados' => 'Total Certificados',
            'licencia_militar' => 'Licencia Militar',
        ];
    }

    /**
     * Obtener texto de filtros aplicados
     */
    private function obtenerFiltrosAplicados(Request $request)
    {
        $filtros = [];

        if ($request->filled('unidad_id')) {
            $unidad = UnidadOrganizacional::find($request->unidad_id);
            $filtros[] = 'Unidad: ' . ($unidad->nombre ?? 'Desconocida');
        }

        if ($request->filled('tipo_contrato')) {
            $filtros[] = 'Tipo Contrato: ' . $request->tipo_contrato;
        }

        if ($request->filled('sexo')) {
            $filtros[] = 'Sexo: ' . $request->sexo;
        }

        if ($request->filled('es_jefatura')) {
            $filtros[] = 'Jefatura: ' . ($request->es_jefatura == 'si' ? 'Sí' : 'No');
        }

        if ($request->filled('nivel_jerarquico')) {
            $filtros[] = 'Nivel: ' . $request->nivel_jerarquico;
        }

        if ($request->filled('busqueda')) {
            $filtros[] = 'Búsqueda: "' . $request->busqueda . '"';
        }

        return $filtros;
    }
}
