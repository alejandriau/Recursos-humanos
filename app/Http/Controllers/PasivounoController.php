<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasivouno;
use App\Models\Seleccion;
use App\Exports\PasivoUnoExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use PDF;

class PasivounoController extends Controller
{
    public function index()
    {
        // Obtener selecciones del usuario autenticado usando la relación polimórfica
        $selecciones = Seleccion::with('carpeta')
            ->where('user_id', Auth::id())
            ->where('carpeta_type', 'pasivouno')
            ->get();

        $letter = "A";
        $resultados = Pasivouno::where('letra', $letter)
            ->orderBy('codigo', 'ASC')
            ->paginate(100);

        return view('admin.pasivos.pasivosuno.index', compact('resultados', 'selecciones'));
    }



    public function show($id)
    {
        return Pasivouno::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'nullable|integer',
            'nombrecompleto' => 'nullable|string|max:800',
            'letra' => 'nullable|string|max:2',
            'observacion' => 'nullable|string|max:800',
        ]);

        $registro = Pasivouno::create($data);

        return redirect()->back()->with('success', 'Registro guardado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $registro = Pasivouno::findOrFail($id);

        $data = $request->validate([
            'nombrecompleto' => 'nullable|string|max:800',
            'observacion' => 'nullable|string|max:800',
        ]);
        $registro->update($data);

        return redirect()->back()->with('mensaje', 'Actualizado correctamente');
    }

    public function destroy($id)
    {
        $registro = Pasivouno::findOrFail($id);
        $registro->delete();

        return redirect()->back()->with('success', 'Registro eliminado con exito.');
    }
    /*public function letra(Request $request)
    {

        $letter = $request->input('letter');

        $resultados = Pasivouno::where('letra', $letter)
        ->orderBy('codigo', 'ASC')
        ->get();
        return response()->view('admin.pasivos.pasivosuno.partes.letras', compact('resultados'));
    }*/

    /*public function letras(Request $request)
    {

        $letter = $request->input('letter');
        return view('admin.pasivos.pasivosdos.provar', compact('letter'));
    }*/
    public function letra(Request $request)
    {
        $letter = $request->input('letra');

        // Solo selecciones del usuario autenticado usando relación polimórfica
        $selecciones = Seleccion::with('carpeta')
            ->where('user_id', Auth::id())
            ->where('carpeta_type', 'pasivouno')
            ->get();

        $resultados = Pasivouno::where('letra', $letter)
            ->orderBy('codigo', 'ASC')
            ->paginate(100);

        return view('admin.pasivos.pasivosuno.index', compact('resultados','selecciones', 'letter'));
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        $search = $request->input('query');
        $letter = $request->get('letra', ''); // Mantener la letra si existe

        // Solo selecciones del usuario autenticado usando relación polimórfica
        $selecciones = Seleccion::with('carpeta')
            ->where('user_id', Auth::id())
            ->where('carpeta_type', 'pasivouno')
            ->get();

        // Si hay búsqueda, buscar por nombre; si no, usar la letra
        if ($search) {
            $resultados = Pasivouno::where('nombrecompleto', 'like', '%' . $search . '%')
                ->paginate(100);
        } else {
            $resultados = Pasivouno::where('letra', $letter)
                ->orderBy('codigo', 'ASC')
                ->paginate(100);
        }

        return view('admin.pasivos.pasivosuno.index', compact('resultados','selecciones', 'letter', 'search'));
    }

    public function traer(Request $request)
    {
        $id = $request->input('idselecc');

        // Validar primero el ID
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'ID de persona inválido.'
            ]);
        }

        // Validar existencia del pasivo
        $pasivo = Pasivouno::find($id);
        if (!$pasivo) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron resultados.'
            ]);
        }

        // Verificar si el usuario ya tiene este pasivo seleccionado (con la nueva estructura)
        $seleccionExistente = Seleccion::where('carpeta_type', 'pasivouno')
            ->where('carpeta_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($seleccionExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has seleccionado este registro anteriormente.'
            ]);
        }

        // Crear la nueva selección con la estructura polimórfica
        $seleccion = Seleccion::create([
            'carpeta_type' => 'pasivouno', // Nuevo campo
            'carpeta_id' => $id, // Nuevo campo (antes era idPasivouno)
            'tipo_seleccion' => $request->tipo_seleccion ?? 'temporal', // Nuevo campo con valor por defecto
            'registro' => $request->registro,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'data' => [[
                'id' => $pasivo->id,
                'codigo' => e($pasivo->letra . ' ' . $pasivo->codigo),
                'nombrecompleto' => e($pasivo->nombrecompleto),
                'observacion' => e($pasivo->observacion),
                'idSeleccion' => $seleccion->id,
                'carpeta_type' => 'pasivosdos' // Agregar para identificar el tipo
            ]]
        ]);
    }

public function reportepasivos(Request $request)
{
    $ids = $request->input('idreporte');

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['error' => 'No se enviaron IDs válidos.'], 400);
    }

    // Obtener carpetas seleccionadas por el usuario
    $idsPermitidos = Seleccion::where('user_id', Auth::id())
        ->where('carpeta_type', 'pasivouno')
        ->whereIn('carpeta_id', $ids)
        ->pluck('carpeta_id')
        ->toArray();

    if (empty($idsPermitidos)) {
        return response()->json(['error' => 'No tienes permisos para generar este reporte.'], 403);
    }

    $datos = Pasivouno::whereIn('id', $idsPermitidos)->get();

    $letra = $datos->first()?->letra ?? 'N/A';

    $pdf = new \FPDF('P', 'mm', 'Letter');
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode("Reporte de Datos - Letra {$letra}"), 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(20, 10, 'Codigo', 1, 0, 'C');
    $pdf->Cell(150, 10, 'Nombre', 1, 0, 'C');
    $pdf->Cell(20, 10, 'Letra', 1, 1, 'C');

    $pdf->SetFont('Arial', 'B', 18);

    foreach ($datos as $row) {
        $pdf->Cell(20, 10, $row->codigo, 1, 0, 'C');
        $pdf->Cell(150, 10, utf8_decode($row->nombrecompleto), 1, 0, 'L');
        $pdf->Cell(20, 10, $row->letra, 1, 1, 'C');
    }

    return response($pdf->Output('S'), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="reporte_pasivos.pdf"');
}


    //exportables
    public function exportPdf($letra = null)
    {
        if ($letra) {
            // Exportar PDF para una letra específica
            return $this->exportPdfPorLetra($letra);
        }

        // Mostrar vista de selección de letras
        $letras = PasivoUno::where('estado', 1)
            ->whereRaw('LENGTH(TRIM(nombrecompleto)) > 0')
            ->get()
            ->groupBy(function($item) {
                return strtoupper(substr(trim($item->nombrecompleto), 0, 1));
            })
            ->keys()
            ->sort()
            ->toArray();

        return view('reportes.seleccion-letra', [
            'letras' => $letras,
            'titulo' => 'PASIVO UNO - EX CORDECO',
            'rutaBase' => 'reportes.pasivouno.pdf.letra'
        ]);
    }

public function exportPdfPorLetra($letra)
{
    $letraMayus = strtoupper($letra);
    
    // Obtener todos los datos
    $datos = PasivoUno::where('estado', 1)
        ->whereRaw('UPPER(SUBSTRING(TRIM(nombrecompleto), 1, 1)) = ?', [$letraMayus])
        ->orderBy('nombrecompleto')
        ->get();
    
    $totalRegistros = $datos->count();
    $porPagina = 25;
    $totalPaginas = ceil($totalRegistros / $porPagina);
    
    // Generar el PDF
    $pdf = PDF::loadView('reportes.exports.pasivouno-pdf-letra', [
        'datos' => $datos, // Pasamos todos los datos pero la vista manejará la paginación
        'letra' => $letraMayus,
        'titulo' => 'PASIVO UNO - EX CORDECO - LETRA ' . $letraMayus,
        'totalPaginas' => $totalPaginas,
        'totalRegistros' => $totalRegistros,
        'porPagina' => $porPagina
    ]);
    
    // Configurar para que DOMPDF maneje la paginación automáticamente
    $pdf->setPaper('letter', 'portrait');
    $pdf->setOptions([
        'defaultFont' => 'sans-serif',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'isPhpEnabled' => true, // Habilitar PHP para contadores de página
        'margin_top' => 15,
        'margin_bottom' => 20,
        'margin_left' => 15,
        'margin_right' => 15
    ]);
    
    return $pdf->download("pasivo_uno_letra_{$letra}_".date('Y-m-d').'.pdf');
}

// Versión con streaming (para ver en navegador)
public function verPdfPorLetra($letra)
{
    $letraMayus = strtoupper($letra);
    
    $datos = PasivoUno::where('estado', 1)
        ->whereRaw('UPPER(SUBSTRING(TRIM(nombrecompleto), 1, 1)) = ?', [$letraMayus])
        ->orderBy('nombrecompleto')
        ->get();
    
    $totalRegistros = $datos->count();
    $porPagina = 25;
    $totalPaginas = ceil($totalRegistros / $porPagina);
    
    $pdf = PDF::loadView('reportes.exports.pasivouno-pdf-letra', [
        'datos' => $datos,
        'letra' => $letraMayus,
        'titulo' => 'PASIVO UNO - EX CORDECO - LETRA ' . $letraMayus,
        'totalPaginas' => $totalPaginas,
        'totalRegistros' => $totalRegistros,
        'porPagina' => $porPagina
    ]);
    
    $pdf->setPaper('letter', 'portrait');
    $pdf->setOptions([
        'defaultFont' => 'sans-serif',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'isPhpEnabled' => true,
        'margin_top' => 15,
        'margin_bottom' => 20,
        'margin_left' => 15,
        'margin_right' => 15
    ]);
    
    // Mostrar en el navegador en lugar de descargar
    return $pdf->stream("pasivo_uno_letra_{$letra}.pdf");
}

    public function exportExcel()
    {
        return Excel::download(new PasivoUnoExport, 'reporte_pasivo_uno_'.date('Y-m-d').'.xlsx');
    }
}
