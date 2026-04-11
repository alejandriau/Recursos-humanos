<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PlanillaImport;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use App\Models\Persona;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\TablePosition;
use PhpOffice\PhpWord\Style\Cell;

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
        return view('planillas.buscar', compact('personas'));
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

    $planillasPorAnio = $persona->planillas->groupBy('anio');

    return \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'planillas.pdf',
        compact('persona', 'planillasPorAnio')
    )->download("certificado_{$persona->ci}.pdf");
}
public function exportarWord($id)
{
    $persona = Persona::with('planillas')->findOrFail($id);
    $planillasPorAnio = $persona->planillas->groupBy('anio');

    $phpWord = new PhpWord();
    $phpWord->setDefaultFontName('Arial');
    $phpWord->setDefaultFontSize(10);

    // Estilo de tabla SIN márgenes internos (cellMargin = 0)
    $tableStyle = [
        'borderSize' => 1,
        'borderColor' => '000000',
        'cellMargin' => 0,          // ← Elimina el espacio interior de las celdas
    ];
    $phpWord->addTableStyle('miTabla', $tableStyle);

    // Estilo de párrafo compacto (sin espaciado)
    $paragraphStyle = [
        'spaceBefore' => 0,
        'spaceAfter'  => 0,
        'lineHeight'  => 1,         // Altura de línea mínima
    ];

    $section = $phpWord->addSection();

    // Encabezado institucional (sin cambios)
    $section->addText(
        'GOBIERNO AUTÓNOMO DEPARTAMENTAL DE COCHABAMBA',
        ['bold' => true, 'size' => 14],
        ['alignment' => Jc::CENTER]
    );
    $section->addText(
        'UNIDAD DE GESTIÓN DE RECURSOS HUMANOS',
        ['bold' => true, 'size' => 12],
        ['alignment' => Jc::CENTER]
    );
    $section->addTextBreak(1);

    $section->addText(
        'CERTIFICADO DE APORTES',
        ['bold' => true, 'size' => 16],
        ['alignment' => Jc::CENTER]
    );
    $section->addTextBreak(1);

    // Datos de la persona
    $section->addText("Certifica que:");
    $section->addText("CI: {$persona->ci}");
    $section->addText("Nombre: {$persona->nombre} {$persona->apellidoPat} {$persona->apellidoMat}");
    $section->addText("Fecha de nacimiento: " . optional($persona->fechaNacimiento)->format('d/m/Y'));
    $section->addTextBreak(1);

    foreach ($planillasPorAnio as $anio => $planillas) {
        $section->addText("GESTIÓN $anio", ['bold' => true, 'size' => 12]);

        $table = $section->addTable('miTabla');

        // FILA DE ENCABEZADO (sin altura fija)
        $table->addRow(null, ['tblHeader' => true]);

        // Cada celda usa addText con el estilo de párrafo compacto
        $headerCell = $table->addCell(1000);
        $headerCell->addText('MES', ['bold' => true], $paragraphStyle);

        $headerCell = $table->addCell(800);
        $headerCell->addText('DÍAS TRAB.', ['bold' => true], $paragraphStyle);

        $headerCell = $table->addCell(1500);
        $headerCell->addText('HABER BÁSICO', ['bold' => true], $paragraphStyle);

        $headerCell = $table->addCell(1500);
        $headerCell->addText('TOTAL GANADO', ['bold' => true], $paragraphStyle);

        $headerCell = $table->addCell(1500);
        $headerCell->addText('APORTE SEGURO LARGO PLAZO', ['bold' => true], $paragraphStyle);

        $headerCell = $table->addCell(1500);
        $headerCell->addText('OTROS DESC.', ['bold' => true], $paragraphStyle);

        // FILAS DE DATOS
        foreach ($planillas as $p) {
            $mesNombre = strtoupper(
                \Carbon\Carbon::create()->month($p->mes)->locale('es')->monthName
            );

            $table->addRow(); // ← Sin argumentos, altura automática

            $cell = $table->addCell();
            $cell->addText($mesNombre, ['bold' => true], $paragraphStyle);

            $cell = $table->addCell();
            $cell->addText($p->dia_trab ?? '-', [], $paragraphStyle);

            $cell = $table->addCell();
            $cell->addText(number_format($p->h_basico ?? 0, 2), [], $paragraphStyle);

            $cell = $table->addCell();
            $cell->addText(number_format($p->neto ?? 0, 2), [], $paragraphStyle);

            $cell = $table->addCell();
            $cell->addText(number_format($p->t_afp ?? 0, 2), [], $paragraphStyle);

            $cell = $table->addCell();
            $cell->addText(number_format($p->tot_des ?? 0, 2), [], $paragraphStyle);
        }

        $section->addTextBreak(1);
    }

    // Pie de certificación
    $section->addTextBreak(1);
    $section->addText(
        "Es cuanto certifico, para fines que convenga al interesado.",
        ['italic' => true],
        ['alignment' => Jc::BOTH]
    );
    $section->addTextBreak(2);
    $section->addText(
        "Cochabamba, " . now()->format('d \d\e F \d\e Y'),
        [],
        ['alignment' => Jc::RIGHT]
    );
    $section->addTextBreak(3);
    $section->addText("___________________________", [], ['alignment' => Jc::CENTER]);
    $section->addText("Unidad de Gestión de Recursos Humanos", ['bold' => true], ['alignment' => Jc::CENTER]);

    // Guardar y descargar
    $fileName = "certificado_aportes_{$persona->ci}.docx";
    $tempFile = tempnam(sys_get_temp_dir(), $fileName);
    $phpWord->save($tempFile, 'Word2007');

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}
}