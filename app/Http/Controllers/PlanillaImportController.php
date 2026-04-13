<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PlanillaImport;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use App\Models\Persona;
use App\Models\Planilla;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\TablePosition;
use PhpOffice\PhpWord\Style\Cell;
use Carbon\Carbon;
class PlanillaImportController extends Controller
{
    public function showForm()
    {
        return view('planillas.importar-planilla'); // ajusta la ruta de tu vista
    }
    
    public function import(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB
            'mes'     => 'required|integer|min:1|max:12',
            'anio'    => 'required|integer|min:1990|max:' . date('Y'),
            'tipo'    => 'required|in:planta,eventual',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $mes  = $request->input('mes');
        $anio = $request->input('anio');
        $archivo = $request->file('archivo');
        $tipo = $request->input('tipo');
        
        try {
            // Importar usando la clase que ya creamos (le pasamos mes y año)
            Excel::import(new PlanillaImport($anio, $mes, $tipo), $archivo);
            
            return redirect()->route('planillas.import.form')
                ->with('success', "Planilla de $mes/$anio importada correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    //parte de exportar 

public function buscar(Request $request)
{
    // 1. Búsqueda de personas (sin cambios)
    $personas = collect();
    if ($request->filled('q')) {
        $q = $request->q;
        $personas = Persona::where('ci', 'LIKE', "%$q%")
            ->orWhere('nombre', 'LIKE', "%$q%")
            ->orWhere('apellidoPat', 'LIKE', "%$q%")
            ->orWhere('apellidoMat', 'LIKE', "%$q%")
            ->orWhereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$q%"])
            ->orWhereRaw("CONCAT(apellidoPat, ' ', apellidoMat, ' ', nombre) LIKE ?", ["%$q%"])
            ->limit(50)
            ->get();
    }

    // 2. Filtro de planillas (ahora con condiciones opcionales)
    $planillas = collect();
    if ($request->filled('mes') || $request->filled('anio') || $request->filled('tipo')) {
        $query = Planilla::with('persona');
        
        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }
        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        $planillas = $query->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
    }

    return view('planillas.buscar', compact('personas', 'planillas'))->withInput($request->all());
}

    public function mostrarPlanillas($id)
    {
        $persona = Persona::with(['planillas' => function($q) {
            $q->orderBy('anio', 'desc')->orderBy('mes', 'asc');
        }])->findOrFail($id);
        $planillasPorAnio = $persona->planillas->groupBy('anio');
        return view('planillas.detalle', compact('persona', 'planillasPorAnio'));
    }
    public function exportarPDF($id)
{
    $persona = Persona::with('planillas')->findOrFail($id);
    
    // Ordenar planillas
    $planillas = $persona->planillas->sortBy(function ($p) {
        return $p->anio * 100 + $p->mes;
    })->values();
    
    // Detectar períodos (misma lógica que en Word)
    $periodos = [];
    $periodoActual = null;
    $ultimoCargo = null;
    
    foreach ($planillas as $p) {
        $cargo = $p->cargo ?? 'SIN CARGO';
        if ($cargo !== $ultimoCargo) {
            if ($periodoActual) {
                $periodoActual['fin'] = $periodoActual['planillas']->last()->fecha_vencimiento;
                $periodos[] = $periodoActual;
            }
            $periodoActual = [
                'cargo' => $cargo,
                'planillas' => collect([$p]),
                'inicio' => $p->fecha_ingreso,
                'fin' => null,
            ];
            $ultimoCargo = $cargo;
        } else {
            $periodoActual['planillas']->push($p);
        }
    }
    if ($periodoActual) {
        $periodoActual['fin'] = $periodoActual['planillas']->last()->fecha_vencimiento;
        $periodos[] = $periodoActual;
    }
    
    // Agrupar por año para las tablas
    $planillasPorAnio = $planillas->groupBy('anio');
    
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('planillas.pdf', compact('persona', 'periodos', 'planillasPorAnio'));
    return $pdf->download("certificado_{$persona->ci}.pdf");
}

public function exportarWord($id)
{
    $persona = Persona::with('planillas')->findOrFail($id);
    
    // Ordenar planillas cronológicamente
    $planillas = $persona->planillas->sortBy(function ($p) {
        return $p->anio * 100 + $p->mes;
    })->values();

    // 1. Detectar períodos de cargo consecutivos usando fechas reales
    $periodos = [];
    $periodoActual = null;
    $ultimoCargo = null;

    foreach ($planillas as $p) {
        $cargo = $p->cargo ?? 'SIN CARGO';
        if ($cargo !== $ultimoCargo) {
            // Cerrar período anterior si existe
            if ($periodoActual) {
                $periodoActual['fin'] = $periodoActual['planillas']->last()->fecha_vencimiento;
                $periodos[] = $periodoActual;
            }
            // Iniciar nuevo período
            $periodoActual = [
                'cargo' => $cargo,
                'planillas' => collect([$p]),
                'inicio' => $p->fecha_ingreso,
                'fin' => null,
            ];
            $ultimoCargo = $cargo;
        } else {
            $periodoActual['planillas']->push($p);
        }
    }
    // Cerrar el último período
    if ($periodoActual) {
        $periodoActual['fin'] = $periodoActual['planillas']->last()->fecha_vencimiento;
        $periodos[] = $periodoActual;
    }

    // 2. Configurar PHPWord
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $phpWord->setDefaultFontName('Arial');
    $phpWord->setDefaultFontSize(10);

    // Estilo de tabla
    $tableStyle = [
        'borderSize' => 1,
        'borderColor' => '000000',
        'cellMargin' => 0,
    ];
    $phpWord->addTableStyle('miTabla', $tableStyle);

    // Estilo de párrafo compacto
    $paragraphStyle = [
        'spaceBefore' => 0,
        'spaceAfter' => 0,
        'lineHeight' => 1,
    ];

    $section = $phpWord->addSection();
        $section->addText(
        'CERTIFICACION DE APORTES',
        ['bold' => true, 'size' => 18],
        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
    );

    // Número de GD (autogenerado)
    $numeroGD = 'GD-UGRH/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '/' . date('Y');
    $section->addText($numeroGD, ['bold' => true, 'size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
    $section->addTextBreak(1);

    // Encabezado institucional
    $section->addText(
        'LA UNIDAD DE GESTIÓN DE RECURSOS HUMANOS DEL GOBIERNO AUTONOMO DEPARTAMENTAL DE COCHABAMBA.',
        ['bold' => true, 'size' => 11]
    );

    $section->addText(
        'CERTIFICA:',
        ['bold' => true, 'size' => 11]
    );

    // Datos de la persona
    // Datos de la persona (con negrita en nombre y CI)
    $nombreCompleto = trim("{$persona->apellidoPat} {$persona->apellidoMat} {$persona->nombre}");
    $textRun = $section->addTextRun();
    $textRun->addText("Que, la Sra. ");
    $textRun->addText(strtoupper($nombreCompleto), ['bold' => true]);
    $textRun->addText(" con C.I. ");
    $textRun->addText($persona->ci, ['bold' => true]);
    $textRun->addText(", presta servicios en el Gobierno Autónomo Departamental de Cochabamba bajo el Régimen de la Ley Nº 2027 Estatuto del funcionario Público, como personal de contrato y planta, de acuerdo al siguiente detalle:");

    // Listado de períodos
    foreach ($periodos as $indice => $periodo) {
        $cargo = $periodo['cargo'];
        $fechaInicio = \Carbon\Carbon::parse($periodo['inicio'])->locale('es');
        $esUltimo = ($indice === count($periodos) - 1);
        
        $inicioStr = $fechaInicio->isoFormat('D [de] MMMM [de] YYYY');
        
        if ($esUltimo) {
            $texto = "Del {$inicioStr} a la fecha viene desempeñando funciones como {$cargo}";
        } else {
            $fechaFin = \Carbon\Carbon::parse($periodo['fin'])->locale('es');
            $finStr = $fechaFin->isoFormat('D [de] MMMM [de] YYYY');
            $texto = "Del {$inicioStr} al {$finStr} desempeño funciones como {$cargo}";
        }
        
        $section->addText($texto);
    }

    $section->addText("siendo sus haberes y aportes los siguientes:", ['italic' => true]);
    $section->addTextBreak(1);

    // Tablas agrupadas por año
    $planillasPorAnio = $planillas->groupBy('anio');
    
    foreach ($planillasPorAnio as $anio => $planillasAnio) {
        $section->addText("GESTION $anio", ['bold' => true, 'size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        
        $table = $section->addTable('miTabla');
        
        // Encabezados de la tabla
        $table->addRow(null, ['tblHeader' => true]);
        $table->addCell(1000)->addText('MES', ['bold' => true], $paragraphStyle);
        $table->addCell(800)->addText('DÍAS TRAB.', ['bold' => true], $paragraphStyle);
        $table->addCell(1500)->addText('HABER BASICO', ['bold' => true], $paragraphStyle);
        $table->addCell(1500)->addText('TOTAL GANADO', ['bold' => true], $paragraphStyle);
        $table->addCell(2000)->addText('APORTE A LA SEGURIDAD SOCIAL DE LARGO PLAZO', ['bold' => true,'size' => 7], $paragraphStyle);
        
        // Filas de datos
        foreach ($planillasAnio as $p) {
            $mesNombre = strtoupper(\Carbon\Carbon::create()->month($p->mes)->locale('es')->monthName);
            $table->addRow();
            $table->addCell()->addText($mesNombre, ['bold' => true], $paragraphStyle);
            $table->addCell()->addText($p->dia_trab ?? '-', [], $paragraphStyle);
            $table->addCell()->addText(number_format($p->h_basico ?? 0, 2, ',', '.'), [], $paragraphStyle);
            $table->addCell()->addText(number_format($p->neto ?? 0, 2, ',', '.'), [], $paragraphStyle);
            $table->addCell()->addText(number_format($p->t_afp ?? 0, 2, ',', '.'), [], $paragraphStyle);
        }
        
        $section->addTextBreak(1);
    }

    // Pie de certificación
    $section->addTextBreak(1);
    $section->addText(
        "Es cuanto certifico, para fines que convenga a la interesada.",
        ['italic' => true],
        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]
    );
    $section->addTextBreak(2);
    $section->addText(
        "Cochabamba, " . now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY'),
        [],
        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]
    );
    $section->addTextBreak(3);
    $section->addText("___________________________", [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
    $section->addText("Unidad de Gestión de Recursos Humanos", ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

    // Guardar y descargar
    $fileName = "certificado_aportes_{$persona->ci}.docx";
    $tempFile = tempnam(sys_get_temp_dir(), $fileName);
    $phpWord->save($tempFile, 'Word2007');
    
    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}
}