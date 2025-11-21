<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasivouno;
use App\Exports\PasivoUnoExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class PasivounoController extends Controller
{
            // Listar todos los registros
            public function index()
            {
                return view('admin.pasivos.pasivosuno.index');
            }

            // buscar
            public function buscar(Request $request)
            {
                $request->validate([
                    'pasivosu' => 'required|string'
                ]);

                $search = $request->input('pasivosu');

                $resultados = Pasivouno::where('nombrecompleto', 'like', '%' . $search . '%')->get();

                return view('admin.pasivos.pasivosuno.index', compact('resultados'));
            }

            // Mostrar un registro específico
            public function show($id)
            {
                $registro = Pasivouno::find($id);
                return $registro ? response()->json($registro) : response()->json(['message' => 'No encontrado'], 404);
            }

            // Crear un nuevo registro
            public function store(Request $request)
            {
                $nuevo = Pasivouno::create($request->all());
                return response()->json($nuevo, 201);
            }

            // Actualizar un registro existente
            public function update(Request $request, $id)
            {
                $registro = Pasivouno::find($id);
                if (!$registro) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }

                $registro->update($request->all());
                return response()->json($registro);
            }

            // Eliminar un registro
            public function destroy($id)
            {
                $registro = Pasivouno::find($id);
                if (!$registro) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }

                $registro->delete();
                return response()->json(['message' => 'Eliminado con éxito']);
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
        $datos = PasivoUno::where('estado', 1)
            ->whereRaw('UPPER(SUBSTRING(TRIM(nombrecompleto), 1, 1)) = ?', [strtoupper($letra)])
            ->orderBy('nombrecompleto')
            ->get();

        $pdf = PDF::loadView('reportes.exports.pasivouno-pdf-letra', [
            'datos' => $datos,
            'letra' => strtoupper($letra),
            'titulo' => 'PASIVO UNO - EX CORDECO - LETRA ' . strtoupper($letra)
        ]);

        //return  view('reportes.exports.pasivouno-pdf-letra', [
        //    'datos' => $datos,
        //    'letra' => strtoupper($letra),
        //    'titulo' => 'PASIVO UNO - EX CORDECO - LETRA ' . strtoupper($letra)
        //]);

        return $pdf->download("pasivo_uno_letra_{$letra}_".date('Y-m-d').'.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new PasivoUnoExport, 'reporte_pasivo_uno_'.date('Y-m-d').'.xlsx');
    }
}
