<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Pasivodos;
use App\Models\Seleccion;
use Illuminate\Support\Facades\Auth;



use Barryvdh\DomPDF\Facade\Pdf;


class PasivodosController extends Controller
{
    public function index()
    {
        // Solo selecciones del usuario autenticado
        $selecciones = Seleccion::with('pasivodos')
            ->where('user_id', Auth::id())
            ->get();

        $letter = "A";
        $resultados =  Pasivodos::where('letra', $letter)
            ->orderBy('codigo', 'ASC')
            ->paginate(100);

        return view('admin.pasivos.pasivosdos.index', compact('resultados','selecciones'));
    }



    public function show($id)
    {
        return Pasivodos::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'nullable|integer',
            'nombrecompleto' => 'nullable|string|max:800',
            'letra' => 'nullable|string|max:2',
            'observacion' => 'nullable|string|max:800',
        ]);

        $registro = Pasivodos::create($data);

        return redirect()->back()->with('success', 'Registro guardado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $registro = Pasivodos::findOrFail($id);

        $data = $request->validate([
            'nombrecompleto' => 'nullable|string|max:800',
            'observacion' => 'nullable|string|max:800',
        ]);
        $registro->update($data);

        return redirect()->back()->with('mensaje', 'Actualizado correctamente');
    }

    public function destroy($id)
    {
        $registro = Pasivodos::findOrFail($id);
        $registro->delete();

        return redirect()->back()->with('success', 'Registro eliminado con exito.');
    }
    /*public function letra(Request $request)
    {

        $letter = $request->input('letter');

        $resultados = Pasivodos::where('letra', $letter)
        ->orderBy('codigo', 'ASC')
        ->get();
        return response()->view('admin.pasivos.pasivosdos.partes.letras', compact('resultados'));
    }*/

    /*public function letras(Request $request)
    {

        $letter = $request->input('letter');
        return view('admin.pasivos.pasivosdos.provar', compact('letter'));
    }*/
    public function letra(Request $request)
    {
        $letter = $request->input('letra');

        // Solo selecciones del usuario autenticado
        $selecciones = Seleccion::with('pasivodos')
            ->where('user_id', Auth::id())
            ->get();

        $resultados = Pasivodos::where('letra', $letter)
            ->orderBy('codigo', 'ASC')
            ->paginate(100);

        return view('admin.pasivos.pasivosdos.index', compact('resultados','selecciones', 'letter'));
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        $search = $request->input('query');
        $letter = $request->get('letra', ''); // Mantener la letra si existe

        // Solo selecciones del usuario autenticado
        $selecciones = Seleccion::with('pasivodos')
            ->where('user_id', Auth::id())
            ->get();

        // Si hay búsqueda, buscar por nombre; si no, usar la letra
        if ($search) {
            $resultados = Pasivodos::where('nombrecompleto', 'like', '%' . $search . '%')
                ->paginate(100);
        } else {
            $resultados = Pasivodos::where('letra', $letter)
                ->orderBy('codigo', 'ASC')
                ->paginate(100);
        }

        return view('admin.pasivos.pasivosdos.index', compact('resultados','selecciones', 'letter', 'search'));
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
    $pasivo = Pasivodos::find($id);
    if (!$pasivo) {
        return response()->json([
            'success' => false,
            'message' => 'No se encontraron resultados.'
        ]);
    }

    // Verificar si el usuario ya tiene este pasivo seleccionado
    $seleccionExistente = Seleccion::where('idPasivodos', $id)
        ->where('user_id', Auth::id())
        ->first();

    if ($seleccionExistente) {
        return response()->json([
            'success' => false,
            'message' => 'Ya has seleccionado este registro anteriormente.'
        ]);
    }

    // Validar y guardar en Seleccion
    $request->validate([
        'registro' => 'nullable|string|max:45',
    ]);

    // Crear la nueva selección asociada al usuario
    $seleccion = Seleccion::create([
        'idPasivodos' => $id,
        'registro' => $request->registro,
        'user_id' => Auth::id() // Asociar al usuario autenticado
    ]);

    return response()->json([
        'success' => true,
        'data' => [[
            'id' => $pasivo->id,
            'codigo' => e($pasivo->letra . ' ' . $pasivo->codigo), // Formato corregido
            'nombrecompleto' => e($pasivo->nombrecompleto),
            'observacion' => e($pasivo->observacion),
            'idSeleccion' => $seleccion->id
        ]]
    ]);
}

public function reportepasivos(Request $request)
    {
        $ids = $request->input('idreporte');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'No se enviaron IDs válidos.'], 400);
        }

        // Opcional: Verificar que los IDs pertenecen al usuario actual
        $idsPermitidos = Seleccion::where('user_id', Auth::id())
            ->whereIn('idPasivodos', $ids)
            ->pluck('idPasivodos')
            ->toArray();

        if (empty($idsPermitidos)) {
            return response()->json(['error' => 'No tienes permisos para generar este reporte.'], 403);
        }

        $datos = Pasivodos::whereIn('id', $idsPermitidos)->get();

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
            $pdf->Cell(20, 10, $row->codigo, 1, 0, 'R');
            $pdf->Cell(150, 10, utf8_decode($row->nombrecompleto), 1, 0, 'L');
            $pdf->Cell(20, 10, $row->letra, 1, 1, 'C');
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="reporte_pasivos.pdf"');
    }
    //ultimo registro o modificado
    public function ultimo(Request $request)
    {
        $query = Pasivodos::query();

        // Filtro por rango de fecha de registro
        if ($request->filled(['fecha_inicio', 'fecha_fin'])) {
            $query->whereBetween('fechaRegistro', [
                $request->fecha_inicio . ' 00:00:00',
                $request->fecha_fin . ' 23:59:59'
            ]);
        }

        // Filtro por rango de fecha de actualización
        if ($request->filled(['fecha_actualizacion_inicio', 'fecha_actualizacion_fin'])) {
            $query->whereBetween('fechaActualizacion', [
                $request->fecha_actualizacion_inicio . ' 00:00:00',
                $request->fecha_actualizacion_fin . ' 23:59:59'
            ]);
        }

        $pasivos = $query->orderBy('fechaRegistro', 'desc')->get();

        return view('admin.pasivos.pasivosdos.ultimo', compact('pasivos'));
    }

    //exportables
    public function exportPdf($letra = null)
    {
        if ($letra) {
            // Exportar PDF para una letra específica
            return $this->exportPdfPorLetra($letra);
        }

        // Mostrar vista de selección de letras
        $letras = PasivoDos::where('estado', 1)
            ->whereNotNull('letra')
            ->where('letra', '!=', '')
            ->distinct()
            ->orderBy('letra')
            ->pluck('letra')
            ->toArray();

        return view('reportes.seleccion-letra', [
            'letras' => $letras,
            'titulo' => 'PASIVO DOS - GADC',
            'rutaBase' => 'reportes.pasivodos.pdf.letra'
        ]);
    }

    public function exportPdfPorLetra($letra)
    {
        $datos = PasivoDos::where('estado', 1)
            ->where('letra', strtoupper($letra))
            ->orderBy('codigo')
            ->get();

        $pdf = PDF::loadView('reportes.exports.pasivodos-pdf-letra', [
            'datos' => $datos,
            'letra' => strtoupper($letra),
            'titulo' => 'PASIVO DOS - GADC - LETRA ' . strtoupper($letra)
        ]);

        return $pdf->download("pasivo_dos_letra_{$letra}_".date('Y-m-d').'.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new PasivoDosExport, 'reporte_pasivo_dos_'.date('Y-m-d').'.xlsx');
    }
}
