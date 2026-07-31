<?php

namespace App\Http\Controllers;

use App\Services\GenerarAsistenciaService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaGeneracionController extends Controller
{
    public function index()
    {
        return view('admin.dispositivos.asistencias.generar');
    }

    public function generar(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $service = new GenerarAsistenciaService();
        $resultados = $service->generarParaRango($request->fecha_inicio, $request->fecha_fin);

        // Contar totales
        $totalProcesadas = collect($resultados)->sum('procesadas');
        $totalErrores = collect($resultados)->sum('errores');

        return redirect()->route('asistencia.generar.index')
            ->with('success', "Asistencia generada del {$request->fecha_inicio} al {$request->fecha_fin}. Procesadas: {$totalProcesadas}, Errores: {$totalErrores}.");
    }
}