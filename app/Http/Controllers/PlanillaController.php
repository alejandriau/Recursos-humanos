<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use App\Models\PlanillaEmpleado;
use App\Models\PlanillaRegistro;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Utils\PdfDiagnostico;

class PlanillaController extends Controller
{
    public function index()
    {
        $totalEmpleados = PlanillaEmpleado::count();
        $totalRegistros = PlanillaRegistro::count();
        $ultimoProcesamiento = PlanillaRegistro::latest()->first();

        // Obtener archivos PDF existentes
        $archivosExistentes = [];
        for ($year = 2010; $year <= date('Y'); $year++) {
            $rutaYear = storage_path("app/planillas/{$year}");
            if (file_exists($rutaYear)) {
                $archivos = glob($rutaYear . '/*.pdf');
                foreach ($archivos as $archivo) {
                    $archivosExistentes[] = [
                        'nombre' => basename($archivo),
                        'ruta' => $archivo,
                        'year' => $year,
                        'tamano' => filesize($archivo)
                    ];
                }
            }
        }

        return view('planillas.index', compact('totalEmpleados', 'totalRegistros', 'ultimoProcesamiento', 'archivosExistentes'));
    }

    public function subirPlanillas(Request $request)
    {
        $request->validate([
            'planillas' => 'required|array',
            'planillas.*' => 'file|mimes:pdf|max:10240',
            'year' => 'required|integer|min:2010|max:' . date('Y')
        ]);

        $year = $request->year;
        $resultados = [];

        foreach ($request->file('planillas') as $planilla) {
            $filename = $planilla->getClientOriginalName();
            $path = $planilla->storeAs("planillas/{$year}", $filename);

            // Primero diagnosticar
            $diagnostico = PdfDiagnostico::analizarPdf(storage_path('app/' . $path));

            if (!$diagnostico['exito']) {
                $resultados[] = [
                    'archivo' => $filename,
                    'estado' => 'error',
                    'error' => $diagnostico['error']
                ];
                continue;
            }

            // Procesar si es posible
            if ($diagnostico['es_posible_ocr']) {
                $resultados[] = [
                    'archivo' => $filename,
                    'estado' => 'advertencia',
                    'mensaje' => 'PDF parece ser escaneado. Se necesita OCR.',
                    'diagnostico' => $diagnostico
                ];
            } else {
                $procesado = $this->procesarPlanillaConDiagnostico(storage_path('app/' . $path), $year, $diagnostico);
                $resultados[] = $procesado;
            }
        }

        return redirect()->back()
            ->with('resultados', $resultados)
            ->with('diagnosticos', true);
    }

    private function procesarPlanillaConDiagnostico($rutaArchivo, $year, $diagnostico)
    {
        try {
            $texto = $diagnostico['texto_completo'];
            $lineas = explode("\n", $texto);
            $empleadosProcesados = 0;
            $nombreArchivo = basename($rutaArchivo);

            // Estrategia de procesamiento basada en el diagnóstico
            if ($diagnostico['patrones_encontrados']['seccion_empleados']) {
                // Procesamiento estructurado
                $empleadosProcesados = $this->procesarEstructurado($lineas, $year, $nombreArchivo);
            } else {
                // Procesamiento por patrones
                $empleadosProcesados = $this->procesarPorPatrones($texto, $year, $nombreArchivo);
            }

            return [
                'archivo' => $nombreArchivo,
                'estado' => 'éxito',
                'empleados_procesados' => $empleadosProcesados,
                'diagnostico' => $diagnostico
            ];

        } catch (\Exception $e) {
            Log::error("Error procesando planilla: " . $e->getMessage());
            return [
                'archivo' => basename($rutaArchivo),
                'estado' => 'error',
                'error' => $e->getMessage(),
                'diagnostico' => $diagnostico
            ];
        }
    }

    private function procesarEstructurado($lineas, $year, $nombreArchivo)
    {
        $enSeccionEmpleados = false;
        $empleadoActual = [];
        $empleadosProcesados = 0;

        foreach ($lineas as $linea) {
            $lineaLimpia = trim($linea);

            // Buscar inicio de sección
            if (str_contains($lineaLimpia, 'Doc. de Identificación') ||
                str_contains($lineaLimpia, 'Identificación')) {
                $enSeccionEmpleados = true;
                continue;
            }

            // Buscar fin de sección
            if (str_contains($lineaLimpia, 'Totales por') ||
                str_contains($lineaLimpia, 'TOTAL')) {
                $enSeccionEmpleados = false;
                if (!empty($empleadoActual)) {
                    $this->guardarEmpleado($empleadoActual, $year, $nombreArchivo);
                    $empleadosProcesados++;
                    $empleadoActual = [];
                }
                continue;
            }

            if ($enSeccionEmpleados && !empty($lineaLimpia)) {
                if ($this->esLineaDeEmpleado($lineaLimpia)) {
                    if (!empty($empleadoActual)) {
                        $this->guardarEmpleado($empleadoActual, $year, $nombreArchivo);
                        $empleadosProcesados++;
                    }
                    $empleadoActual = $this->extraerDatosLinea($lineaLimpia);
                } elseif (!empty($empleadoActual)) {
                    $this->completarDatosEmpleado($empleadoActual, $lineaLimpia);
                }
            }
        }

        return $empleadosProcesados;
    }

    private function procesarPorPatrones($texto, $year, $nombreArchivo)
    {
        $empleadosProcesados = 0;

        // Buscar todas las líneas que parecen ser de empleados
        preg_match_all('/\d{7,8}\s+[^\n]+\s+\d{2}\/\d{2}\/\d{4}[^\n]+[\d,]+\.\d{2}[\s\d,\.]+/', $texto, $matches);

        foreach ($matches[0] as $lineaEmpleado) {
            $datosEmpleado = $this->extraerDatosLinea($lineaEmpleado);
            if (!empty($datosEmpleado['cedula'])) {
                $this->guardarEmpleado($datosEmpleado, $year, $nombreArchivo);
                $empleadosProcesados++;
            }
        }

        return $empleadosProcesados;
    }

    private function esLineaDeEmpleado($linea)
    {
        return preg_match('/^\d{7,8}\s+/', $linea) ||
               preg_match('/\d{2}\/\d{2}\/\d{4}\s+[A-Z]{2}\s+\d+/', $linea) ||
               preg_match('/[\d,]+\.\d{2}\s+[\d,]+\.\d{2}\s+[\d,]+\.\d{2}/', $linea);
    }

    private function extraerDatosLinea($linea)
    {
        // Múltiples estrategias de extracción
        $patrones = [
            // Intento 1: Patrón completo
            '/^(?<cedula>\d{7,8})\s+(?<nombre>[A-ZÁÉÍÓÚÑ\s]+)\s+(?<fecha_nac>\d{2}\/\d{2}\/\d{4})\s+(?<nacionalidad>[A-Z]{2})\s+(?<dias_trab>\d+)\s+(?<puesto>[^\d]+)\s+(?<item>\d+)\s+(?<haber_basico>[\d,]+\.\d{2})\s+(?<bono_antiguedad>[\d,]+\.\d{2})\s+(?<otros_ingresos>[\d,]+\.\d{2})\s+(?<total_ingresos>[\d,]+\.\d{2})\s+(?<rc_iva>[\d,]+\.\d{2})\s+(?<afp>[\d,]+\.\d{2})\s+(?<otros_descuentos>[\d,]+\.\d{2})\s+(?<total_descuentos>[\d,]+\.\d{2})\s+(?<liquido_pagable>[\d,]+\.\d{2})/',

            // Intento 2: Más flexible
            '/(?<cedula>\d{7,8})\s+(?<nombre>[A-ZÁÉÍÓÚÑ\s]+)\s+(?<fecha_nac>\d{2}\/\d{2}\/\d{4})\s+[A-Z]{2}\s+\d+\s+[^\d]+\s+\d+\s+(?<haber_basico>[\d,]+\.\d{2})\s+([\d,]+\.\d{2}\s+){8}(?<liquido_pagable>[\d,]+\.\d{2})/',

            // Intento 3: Solo datos esenciales
            '/(?<cedula>\d{7,8})\s+(?<nombre>[A-ZÁÉÍÓÚÑ\s]+)\s+\d{2}\/\d{2}\/\d{4}\s+[A-Z]{2}\s+\d+\s+[^\d]+\s+\d+\s+(?<haber_basico>[\d,]+\.\d{2})\s+(?<bono_antiguedad>[\d,]+\.\d{2})\s+(?<otros_ingresos>[\d,]+\.\d{2})\s+(?<total_ingresos>[\d,]+\.\d{2})\s+(?<rc_iva>[\d,]+\.\d{2})\s+(?<afp>[\d,]+\.\d{2})\s+(?<otros_descuentos>[\d,]+\.\d{2})\s+(?<total_descuentos>[\d,]+\.\d{2})\s+(?<liquido_pagable>[\d,]+\.\d{2})/'
        ];

        foreach ($patrones as $patron) {
            if (preg_match($patron, $linea, $coincidencias)) {
                return [
                    'cedula' => trim($coincidencias['cedula']),
                    'nombre_completo' => trim($coincidencias['nombre']),
                    'fecha_nacimiento' => $coincidencias['fecha_nac'] ?? '',
                    'nacionalidad' => $coincidencias['nacionalidad'] ?? 'BO',
                    'dias_trabajados' => (int) ($coincidencias['dias_trab'] ?? 30),
                    'puesto' => trim($coincidencias['puesto'] ?? 'Empleado'),
                    'item' => $coincidencias['item'] ?? '',
                    'haber_basico' => (float) str_replace(',', '', $coincidencias['haber_basico']),
                    'bono_antiguedad' => (float) str_replace(',', '', $coincidencias['bono_antiguedad'] ?? 0),
                    'otros_ingresos' => (float) str_replace(',', '', $coincidencias['otros_ingresos'] ?? 0),
                    'total_ingresos' => (float) str_replace(',', '', $coincidencias['total_ingresos'] ?? 0),
                    'rc_iva' => (float) str_replace(',', '', $coincidencias['rc_iva'] ?? 0),
                    'afp' => (float) str_replace(',', '', $coincidencias['afp'] ?? 0),
                    'otros_descuentos' => (float) str_replace(',', '', $coincidencias['otros_descuentos'] ?? 0),
                    'total_descuentos' => (float) str_replace(',', '', $coincidencias['total_descuentos'] ?? 0),
                    'liquido_pagable' => (float) str_replace(',', '', $coincidencias['liquido_pagable'])
                ];
            }
        }

        return [];
    }

    // ... (resto de métodos保持不变)
}
