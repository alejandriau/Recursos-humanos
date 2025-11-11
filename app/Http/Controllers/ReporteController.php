<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Profesion;
use App\Models\Memopuesto;
use App\Models\Puesto;
use App\Models\Historial;
use App\Models\UnidadOrganizacional;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Exports\ReportePersonalExport;
use App\Exports\AnalyticsPersonalExport;
use Illuminate\Support\Facades\DB;
use App\Exports\PersonalExport;

class ReporteController extends Controller
{
    public function inicio()
    {
        return view('admin.inicio.index');
    }

    public function index()
    {
        $unidades = UnidadOrganizacional::where('estado', 1)->get();

        $personas = Persona::with([
                'puestoActual.puesto.unidadOrganizacional',
                'profesion'
            ])
            ->where('estado', 1)
            ->paginate(100); // Agregar paginación aquí también

        return view('reportes.index', compact('personas', 'unidades'));
    }

    public function buscar(Request $request)
    {
        $search = $request->input('search');
        $tipo = $request->input('tipo');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $unidad_id = $request->input('unidad_id');
        $estado = $request->input('estado', 1);

        $query = Persona::with(['puestoActual.puesto.unidadOrganizacional', 'profesion'])
            ->where('estado', $estado);

        // Filtro por tipo
        if ($tipo && $tipo != 'TODOS') {
            $query->where('tipo', strtolower($tipo));
        }

        // Filtro por búsqueda general
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$search%"])
                  ->orWhere('apellidoPat', 'LIKE', "%$search%")
                  ->orWhere('apellidoMat', 'LIKE', "%$search%")
                  ->orWhere('ci', 'LIKE', "%$search%")
                  ->orWhereHas('profesion', function ($q2) use ($search) {
                      $q2->where('provisionN', 'LIKE', "%$search%")
                         ->orWhere('diploma', 'LIKE', "%$search%");
                  })
                  ->orWhereHas('puestoActual.puesto', function ($q3) use ($search) {
                      $q3->where('nombre', 'LIKE', "%$search%")
                         ->orWhere('item', 'LIKE', "%$search%");
                  });
            });
        }

        // Filtro por fechas de ingreso
        if ($fecha_inicio) {
            $query->whereDate('fechaIngreso', '>=', Carbon::parse($fecha_inicio)->format('Y-m-d'));
        }
        if ($fecha_fin) {
            $query->whereDate('fechaIngreso', '<=', Carbon::parse($fecha_fin)->format('Y-m-d'));
        }

        // Filtro por unidad organizacional
        if ($unidad_id) {
            $query->whereHas('puestoActual.puesto.unidadOrganizacional', function ($q) use ($unidad_id) {
                $q->where('id', $unidad_id);
            });
        }

        $personas = $query->get();

        $vista = $tipo == 'CONTRATO' ? 'reportes.partes.buscarcontrato' : 'reportes.partes.buscar';

        return response()->view($vista, compact('personas'));
    }

    public function tipo(Request $request)
    {
        $tipo = $request->input('tipo');
        $unidad_id = $request->input('unidad_id');

        $query = Persona::with(['puestoActual.puesto.unidadOrganizacional', 'profesion'])
            ->where('estado', 1);

        if ($tipo && $tipo != 'TODOS') {
            $query->where('tipo', strtolower($tipo));
        }

        if ($unidad_id) {
            $query->whereHas('puestoActual.puesto.unidadOrganizacional', function ($q) use ($unidad_id) {
                $q->where('id', $unidad_id);
            });
        }

        $personas = $query->get();

        $vista = $tipo == 'CONTRATO' ? 'reportes.partes.pers' : 'reportes.partes.tipo';

        return response()->view($vista, compact('personas'));
    }

public function filtrosAvanzados(Request $request)
{
    $filtros = $request->only([
        'search', 'tipo', 'fecha_inicio', 'fecha_fin',
        'unidad_id', 'nivel_jerarquico', 'estado'
    ]);

    $query = Persona::with([
        'puestoActual.puesto.unidadOrganizacional',
        'profesion',
        'historialPuestos.puesto.unidadOrganizacional'
    ]);

    // Aplicar filtros
    if (!empty($filtros['search'])) {
        $query->where(function ($q) use ($filtros) {
            $q->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%{$filtros['search']}%"])
              ->orWhere('ci', 'LIKE', "%{$filtros['search']}%");
        });
    }

    if (!empty($filtros['tipo']) && $filtros['tipo'] != 'TODOS') {
        $query->where('tipo', strtolower($filtros['tipo']));
    }

    if (!empty($filtros['fecha_inicio'])) {
        $query->whereDate('fechaIngreso', '>=', Carbon::parse($filtros['fecha_inicio']));
    }

    if (!empty($filtros['fecha_fin'])) {
        $query->whereDate('fechaIngreso', '<=', Carbon::parse($filtros['fecha_fin']));
    }

    if (!empty($filtros['unidad_id'])) {
        $query->whereHas('puestoActual.puesto.unidadOrganizacional', function ($q) use ($filtros) {
            $q->where('id', $filtros['unidad_id']);
        });
    }

    if (!empty($filtros['nivel_jerarquico'])) {
        $query->whereHas('puestoActual.puesto', function ($q) use ($filtros) {
            $q->where('nivelJerarquico', 'LIKE', "%{$filtros['nivel_jerarquico']}%");
        });
    }

    if (isset($filtros['estado'])) {
        $query->where('estado', $filtros['estado']);
    }

    // Cambiar get() por paginate() y manejar el parámetro de página
    $personas = $query->paginate(100)->appends($filtros);

    return response()->json([
        'success' => true,
        'html' => view('reportes.partes.tabla-personas', compact('personas'))->render()
    ]);
}

    // Resto de métodos existentes (exportarpdf, personalPDF, personalXLS)...

public function personalPDF(Request $request)
{
    $filtros = $request->only([
        'search', 'tipo', 'fecha_inicio', 'fecha_fin',
        'unidad_id', 'nivel_jerarquico', 'estado'
    ]);

    $query = Persona::with([
        'puestoActual.puesto.unidadOrganizacional',
        'profesion',
        'historials.puesto'
    ]);

    // Aplicar los mismos filtros que en el index
    if (!empty($filtros['search'])) {
        $query->where(function ($q) use ($filtros) {
            $q->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%{$filtros['search']}%"])
              ->orWhere('ci', 'LIKE', "%{$filtros['search']}%")
              ->orWhere('apellidoPat', 'LIKE', "%{$filtros['search']}%")
              ->orWhere('apellidoMat', 'LIKE', "%{$filtros['search']}%")
              ->orWhereHas('profesion', function ($q2) use ($filtros) {
                  $q2->where('provisionN', 'LIKE', "%{$filtros['search']}%")
                     ->orWhere('diploma', 'LIKE', "%{$filtros['search']}%");
              })
              ->orWhereHas('puestoActual.puesto', function ($q3) use ($filtros) {
                  $q3->where('nombre', 'LIKE', "%{$filtros['search']}%")
                     ->orWhere('item', 'LIKE', "%{$filtros['search']}%");
              });
        });
    }

    if (!empty($filtros['tipo']) && $filtros['tipo'] != 'TODOS') {
        $query->where('tipo', strtolower($filtros['tipo']));
    }

    if (!empty($filtros['fecha_inicio'])) {
        $query->whereDate('fechaIngreso', '>=', Carbon::parse($filtros['fecha_inicio']));
    }

    if (!empty($filtros['fecha_fin'])) {
        $query->whereDate('fechaIngreso', '<=', Carbon::parse($filtros['fecha_fin']));
    }

    if (!empty($filtros['unidad_id'])) {
        $query->whereHas('puestoActual.puesto.unidadOrganizacional', function ($q) use ($filtros) {
            $q->where('id', $filtros['unidad_id']);
        });
    }

    if (!empty($filtros['nivel_jerarquico'])) {
        $query->whereHas('puestoActual.puesto', function ($q) use ($filtros) {
            $q->where('nivelJerarquico', 'LIKE', "%{$filtros['nivel_jerarquico']}%");
        });
    }

    if (isset($filtros['estado'])) {
        $query->where('estado', $filtros['estado']);
    }

    $personas = $query->get();

    $pdf = new \FPDF('L', 'mm', [216, 356]);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Título
    $pdf->Cell(0, 10, 'REPORTE DE PERSONAL', 0, 1, 'C');
    $pdf->Ln(5);

    // Fecha de generación
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, 'Generado: ' . now()->format('d/m/Y H:i:s'), 0, 1, 'R');
    $pdf->Ln(5);

    // Tabla
    $pdf->SetFont('Arial', 'B', 9);

    // Encabezados de la tabla (igual que tu vista)
    $headers = ['N°', 'APELLIDO 1', 'APELLIDO 2', 'NOMBRE', 'CI', 'HABER',
                'FECHA INGRESO', 'FECHA NACIMIENTO', 'TITULO PROVISION NACIONAL',
                'FECHA TITULO', 'TELEFONO', 'ESTADO ACTUAL'];

    $widths = [8, 20, 20, 30, 15, 18, 30, 35, 80, 18, 20, 25];

    // Dibujar encabezados
    foreach ($headers as $i => $header) {
        $pdf->Cell($widths[$i], 8, utf8_decode($header), 1, 0, 'C');
    }
    $pdf->Ln();

    // Contenido
    $pdf->SetFont('Arial', '', 8);
    $contador = 1;

    foreach ($personas as $persona) {
        $pdf->Cell($widths[0], 8, $contador++, 1, 0, 'C');
        $pdf->Cell($widths[1], 8, utf8_decode($persona->apellidoPat), 1, 0, 'L');
        $pdf->Cell($widths[2], 8, utf8_decode($persona->apellidoMat), 1, 0, 'L');
        $pdf->Cell($widths[3], 8, utf8_decode($persona->nombre), 1, 0, 'L');
        $pdf->Cell($widths[4], 8, utf8_decode($persona->ci), 1, 0, 'C');

        // Haber
        $haber = $persona->puestoActual && $persona->puestoActual->puesto
            ? number_format($persona->puestoActual->puesto->haber ?? 0, 2, ',', '.')
            : '0,00';
        $pdf->Cell($widths[5], 8, $haber, 1, 0, 'R');

        // Fechas
        $fechaIngreso = !empty($persona->fechaIngreso)
            ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y')
            : '';
        $pdf->Cell($widths[6], 8, $fechaIngreso, 1, 0, 'C');

        $fechaNacimiento = !empty($persona->fechaNacimiento)
            ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y')
            : '';
        $pdf->Cell($widths[7], 8, $fechaNacimiento, 1, 0, 'C');

        // Información profesional
        $pdf->Cell($widths[8], 8, utf8_decode($persona->profesion->provisionN ?? ''), 1, 0, 'L');

        $fechaTitulo = !empty($persona->profesion->fechaProvision)
            ? \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y')
            : '';
        $pdf->Cell($widths[9], 8, $fechaTitulo, 1, 0, 'C');

        $pdf->Cell($widths[10], 8, utf8_decode($persona->telefono), 1, 0, 'C');

        // Estado
        $estado = 'Sin puesto';
        if ($persona->puestoActual) {
            $estado = $persona->puestoActual->estado == 'activo' ? 'Activo' :
                     ($persona->puestoActual->estado == 'concluido' ? 'Concluido' :
                     ucfirst($persona->puestoActual->estado));
        }
        $pdf->Cell($widths[11], 8, utf8_decode($estado), 1, 0, 'C');

        $pdf->Ln();

        // Verificar si necesita nueva página
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
            // Redibujar encabezados
            $pdf->SetFont('Arial', 'B', 9);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 8, utf8_decode($header), 1, 0, 'C');
            }
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
        }
    }

    // Estadísticas al final
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 8, utf8_decode("Total de registros: " . $personas->count()), 0, 1);

    $conPuesto = $personas->where('puestoActual')->count();
    $sinPuesto = $personas->count() - $conPuesto;
    $pdf->Cell(0, 8, utf8_decode("Con puesto: $conPuesto • Sin puesto: $sinPuesto"), 0, 1);

    return response($pdf->Output('S'), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="reporte_personal_' . now()->format('Y_m_d_H_i') . '.pdf"');
}

public function personalXLS(Request $request)
{
    $filtros = $request->only([
        'search', 'tipo', 'fecha_inicio', 'fecha_fin',
        'unidad_id', 'nivel_jerarquico', 'estado'
    ]);

    return Excel::download(new PersonalExport($filtros),
        'reporte_personal_' . now()->format('Y_m_d_H_i') . '.xlsx');
}



    ///===========Reportes finales ==============================================////
    // Dashboard Principal
    public function dashboard()
    {
        $estadisticas = $this->obtenerEstadisticasDashboard();
        $distribucionUnidades = $this->distribucionPorUnidad();
        $evolucionPersonal = $this->evolucionPersonalUltimosMeses();

        return view('reportes.dashboard', compact(
            'estadisticas',
            'distribucionUnidades',
            'evolucionPersonal'
        ));
    }

    // Reporte de Censo Laboral
public function censoLaboral(Request $request)
{
    $filtros = $request->only(['unidad', 'estado', 'genero']);

    $query = Persona::with(['historialActivo.puesto.unidadOrganizacional'])
        ->when($request->unidad, function($query) use ($request) {
            $query->whereHas('historialActivo.puesto.unidadOrganizacional', function($q) use ($request) {
                $q->where('id', $request->unidad);
            });
        })
        ->when($request->estado, function($query) use ($request) {
            $query->where('estado', $request->estado);
        })
        ->when($request->genero, function($query) use ($request) {
            $query->where('sexo', $request->genero);
        })
        ->orderBy('nombre');

    // Usar paginación en lugar de get()
    $personas = $query->paginate(25); // 25 registros por página

    $unidades = UnidadOrganizacional::activos()->get();

    if ($request->has('exportar')) {
        // Para exportar, obtener todos los registros sin paginación
        $personasExport = $query->get();
        return Excel::download(new ReportePersonalExport($personasExport, 'censo'), 'censo_laboral.xlsx');
    }

    return view('reportes.censo-laboral', compact('personas', 'unidades', 'filtros'));
}

    // Reporte de Distribución por Unidad
    public function distribucionUnidades()
    {
        $distribucion = UnidadOrganizacional::withCount([
            'puestosActivos as total_puestos',
            'puestosActivos as puestos_ocupados' => function($query) {
                $query->whereHas('historiales', function($q) {
                    $q->where('estado', 'activo');
                });
            }
        ])
        ->activos()
        ->get()
        ->map(function($unidad) {
            $unidad->puestos_vacantes = $unidad->total_puestos - $unidad->puestos_ocupados;
            $unidad->porcentaje_ocupacion = $unidad->total_puestos > 0
                ? round(($unidad->puestos_ocupados / $unidad->total_puestos) * 100, 2)
                : 0;
            return $unidad;
        });

        return view('reportes.distribucion-unidades', compact('distribucion'));
    }

    // Reporte de Rotación de Personal
    public function rotacionPersonal(Request $request)
    {
        $mesSeleccionado = $request->get('mes', now()->month);
        $anioSeleccionado = $request->get('anio', now()->year);

        $altas = Historial::whereYear('fecha_inicio', $anioSeleccionado)
            ->whereMonth('fecha_inicio', $mesSeleccionado)
            ->where('tipo_movimiento', 'designacion_inicial')
            ->count();

        $bajas = Historial::whereYear('fecha_fin', $anioSeleccionado)
            ->whereMonth('fecha_fin', $mesSeleccionado)
            ->where('estado', 'concluido')
            ->count();

        $totalPersonal = Persona::activos()->count();
        $tasaRotacion = $totalPersonal > 0 ? round(($bajas / $totalPersonal) * 100, 2) : 0;

        $rotacionMensual = $this->obtenerRotacionMensual($anioSeleccionado);

        return view('reportes.rotacion-personal', compact(
            'altas', 'bajas', 'tasaRotacion', 'rotacionMensual',
            'mesSeleccionado', 'anioSeleccionado'
        ));
    }

    // Reporte de Documentación
    public function estadoDocumentacion()
    {
        $personas = Persona::with(['profesiones', 'historialActivo.puesto.unidadOrganizacional'])
            ->activos()
            ->get()
            ->map(function($persona) {
                $persona->documentacion_completa = $this->verificarDocumentacionCompleta($persona);
                $persona->documentos_faltantes = $this->obtenerDocumentosFaltantes($persona);
                return $persona;
            });

        $estadisticasDocumentos = [
            'completos' => $personas->where('documentacion_completa', true)->count(),
            'incompletos' => $personas->where('documentacion_completa', false)->count(),
            'porcentaje_completos' => $personas->count() > 0
                ? round(($personas->where('documentacion_completa', true)->count() / $personas->count()) * 100, 2)
                : 0
        ];

        return view('reportes.estado-documentacion', compact('personas', 'estadisticasDocumentos'));
    }

    // Métodos auxiliares privados
private function obtenerEstadisticasDashboard()
{
    $totalPersonal = Persona::activos()->count();
    $totalPuestos = Puesto::activos()->count();
    $puestosOcupados = Historial::where('estado', 'activo')->distinct('puesto_id')->count('puesto_id');
    $puestosVacantes = $totalPuestos - $puestosOcupados;

    $distribucionGenero = Persona::activos()
        ->selectRaw('sexo, COUNT(*) as total')
        ->groupBy('sexo')
        ->pluck('total', 'sexo')
        ->toArray();

    // Convertir 'f' y 'm' a nombres completos para mejor visualización
    $generosFormateados = [];
    foreach ($distribucionGenero as $sexo => $total) {
        $label = match($sexo) {
            'F' => 'Femenino',
            'M' => 'Masculino',
            default => ucfirst($sexo)
        };
        $generosFormateados[$label] = $total;
    }

    return [
        'total_personal' => $totalPersonal,
        'total_puestos' => $totalPuestos,
        'puestos_ocupados' => $puestosOcupados,
        'puestos_vacantes' => $puestosVacantes,
        'distribucion_sexo' => $generosFormateados, // Usar los formateados
        'distribucion_sexo_raw' => $distribucionGenero, // Mantener original para debug
        'promedio_edad' => Persona::activos()->whereNotNull('fechaNacimiento')
            ->get()
            ->avg('edad'),
        'antiguedad_promedio' => Persona::activos()->whereNotNull('fechaIngreso')
            ->get()
            ->avg('antiguedad'),
    ];
}

    private function distribucionPorUnidad()
    {
        return UnidadOrganizacional::withCount(['puestosActivos as total_puestos'])
            ->withCount(['puestosActivos as puestos_ocupados' => function($query) {
                $query->whereHas('historiales', function($q) {
                    $q->where('estado', 'activo');
                });
            }])
            ->activos()
            ->get()
            ->map(function($unidad) {
                return [
                    'unidad' => $unidad->denominacion,
                    'total_puestos' => $unidad->total_puestos,
                    'puestos_ocupados' => $unidad->puestos_ocupados,
                    'vacantes' => $unidad->total_puestos - $unidad->puestos_ocupados,
                    'porcentaje_ocupacion' => $unidad->total_puestos > 0
                        ? round(($unidad->puestos_ocupados / $unidad->total_puestos) * 100, 2)
                        : 0
                ];
            });
    }

    private function evolucionPersonalUltimosMeses($meses = 12)
    {
        $evolucion = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $total = Persona::where('estado', 1)
                ->where('fechaIngreso', '<=', $fecha->endOfMonth())
                ->count();

            $evolucion[] = [
                'mes' => $fecha->format('M Y'),
                'total' => $total
            ];
        }

        return $evolucion;
    }

    private function obtenerRotacionMensual($anio)
    {
        $rotacion = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $altas = Historial::whereYear('fecha_inicio', $anio)
                ->whereMonth('fecha_inicio', $mes)
                ->where('tipo_movimiento', 'designacion_inicial')
                ->count();

            $bajas = Historial::whereYear('fecha_fin', $anio)
                ->whereMonth('fecha_fin', $mes)
                ->where('estado', 'concluido')
                ->count();

            $rotacion[] = [
                'mes' => Carbon::create()->month($mes)->format('M'),
                'altas' => $altas,
                'bajas' => $bajas
            ];
        }

        return $rotacion;
    }

    private function verificarDocumentacionCompleta($persona)
    {
        $documentosObligatorios = ['diploma', 'provison', 'cedula_profesional'];
        $documentosCompletos = 0;

        foreach ($documentosObligatorios as $doc) {
            if ($this->tieneDocumento($persona, $doc)) {
                $documentosCompletos++;
            }
        }

        return $documentosCompletos === count($documentosObligatorios);
    }

    private function tieneDocumento($persona, $tipoDocumento)
    {
        switch ($tipoDocumento) {
            case 'diploma':
                return $persona->profesiones->whereNotNull('pdfDiploma')->isNotEmpty();
            case 'provison':
                return $persona->profesiones->whereNotNull('pdfProvision')->isNotEmpty();
            case 'cedula_profesional':
                return $persona->profesiones->whereNotNull('pdfcedulap')->isNotEmpty();
            default:
                return false;
        }
    }

    private function obtenerDocumentosFaltantes($persona)
    {
        $documentosObligatorios = [
            'diploma' => 'Diploma Académico',
            'provison' => 'Provisión Nacional',
            'cedula_profesional' => 'Cédula Profesional'
        ];

        $faltantes = [];
        foreach ($documentosObligatorios as $key => $nombre) {
            if (!$this->tieneDocumento($persona, $key)) {
                $faltantes[] = $nombre;
            }
        }

        return $faltantes;
    }
    // Método para PDF del dashboard
public function exportarDashboardPDF()
{
    $estadisticas = $this->obtenerEstadisticasDashboard();
    $distribucionUnidades = $this->distribucionPorUnidad();
    $evolucionPersonal = $this->evolucionPersonalUltimosMeses();

    $pdf = PDF::loadView('reportes.exports.dashboard-pdf', compact(
        'estadisticas', 'distribucionUnidades', 'evolucionPersonal'
    ));

        $pdf->setOption('print-media-type', true);

    return $pdf->download('dashboard_estadisticas.pdf');
}
}
