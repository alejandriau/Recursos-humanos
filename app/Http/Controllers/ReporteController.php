<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Profesion;
use App\Models\Memopuesto;
use App\Models\Puesto;
use App\Models\UnidadOrganizacional;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function inicio()
    {
        return view('admin.inicio.index');
    }

    public function index()
    {
        $unidades = UnidadOrganizacional::where('estado', 1)->get();

        $personas = Persona::with(['puestoActual.puesto.unidadOrganizacional', 'profesion'])
            ->where('estado', 1)
            ->get();

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

        $personas = $query->get();

        return response()->json([
            'success' => true,
            'html' => view('reportes.partes.tabla-personas', compact('personas'))->render()
        ]);
    }

    // Resto de métodos existentes (exportarpdf, personalPDF, personalXLS)...

    public function personalPDF(Request $request)
    {
        $filtros = $request->only(['tipo', 'fecha_inicio', 'fecha_fin', 'unidad_id']);

        $query = Persona::with(['memopuesto', 'profesion', 'puestoActual.puesto.unidadOrganizacional']);

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

        $personas = $query->get();

        $pdf = new \FPDF('L', 'mm', [216, 356]);
        // ... resto del código PDF igual ...

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="personal_filtrado.pdf"');
    }
}
