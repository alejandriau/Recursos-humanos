<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Pasivodos;
use App\Models\Seleccion;



use Barryvdh\DomPDF\Facade\Pdf;


class PasivodosController extends Controller
{
    public function index()
    {
        $selecciones = Seleccion::with('pasivodos')->get();
        $letter = "A";
        $resultados =  Pasivodos::where('letra', $letter)
        ->orderBy('codigo', 'ASC')
        ->get();
        
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
        $selecciones = Seleccion::with('pasivodos')->get();

        $resultados =  Pasivodos::where('letra', $letter)
        ->orderBy('codigo', 'ASC')
        ->get();

        return view('admin.pasivos.pasivosdos.index', compact('resultados','selecciones'));
    }
    public function buscar(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);
        $search = $request->input('query');
        $selecciones = Seleccion::with('pasivodos')->get();

        $resultados = Pasivodos::where('nombrecompleto', 'like', '%' . $search . '%')->get();

        return view('admin.pasivos.pasivosdos.index', compact('resultados','selecciones'));
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

        // Validar y guardar en Seleccion
        $request->validate([
            'registro' => 'nullable|string|max:45',
        ]);

        $seleccion = Seleccion::create([
            'idPasivodos' => $id,
            'registro' => $request->registro
        ]);

        return response()->json([
            'success' => true,
            'data' => [[
                'id' => $pasivo->id,
                'codigo' => e($pasivo->codigo . $pasivo->letra),
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

        $datos = Pasivodos::whereIn('id', $ids)->get();

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
}
