<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use App\Models\PlanillaEmpleado;
use App\Models\PlanillaRegistro;
use Carbon\Carbon;

class PlanillaRegistroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $registros = PlanillaRegistro::with('empleado')
            ->orderBy('ano', 'desc')
            ->orderBy('mes', 'desc')
            ->paginate(20);

        $totalRegistros = PlanillaRegistro::count();
        $ultimoProcesamiento = PlanillaRegistro::latest()->first();

        return view('planillas.registros.index', compact('registros', 'totalRegistros', 'ultimoProcesamiento'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $years = range(2010, date('Y'));
        rsort($years);

        return view('planillas.registros.create', compact('years'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'planillas' => 'required|array',
            'planillas.*' => 'file|mimes:pdf|max:10240',
            'year' => 'required|integer|min:2010|max:' . date('Y')
        ]);

        $year = $request->year;
        $procesados = 0;
        $errores = 0;
        $resultados = [];

        foreach ($request->file('planillas') as $planilla) {
            try {
                $filename = $planilla->getClientOriginalName();
                $path = $planilla->storeAs("planillas/{$year}", $filename);

                $resultado = $this->procesarPlanilla(storage_path('app/' . $path), $year);

                if ($resultado['success']) {
                    $procesados++;
                    $resultados[] = [
                        'archivo' => $filename,
                        'estado' => 'éxito',
                        'empleados' => $resultado['empleados_procesados']
                    ];
                } else {
                    $errores++;
                    $resultados[] = [
                        'archivo' => $filename,
                        'estado' => 'error',
                        'error' => $resultado['error']
                    ];
                }

            } catch (\Exception $e) {
                $errores++;
                $resultados[] = [
                    'archivo' => $planilla->getClientOriginalName(),
                    'estado' => 'error',
                    'error' => $e->getMessage()
                ];
                Log::error("Error procesando planilla: " . $e->getMessage());
            }
        }

        $mensaje = "Procesamiento completado. {$procesados} archivos procesados correctamente.";
        if ($errores > 0) {
            $mensaje .= " {$errores} archivos con errores.";
        }

        return redirect()->route('planillas.registros.index')
            ->with('success', $mensaje)
            ->with('resultados', $resultados);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $registro = PlanillaRegistro::with('empleado')->findOrFail($id);

        return view('planillas.registros.show', compact('registro'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $registro = PlanillaRegistro::findOrFail($id);
        $registro->delete();

        return redirect()->route('planillas.registros.index')
            ->with('success', 'Registro eliminado correctamente.');
    }

    /**
     * Procesar un archivo de planilla PDF
     */
    private function procesarPlanilla($rutaArchivo, $year)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($rutaArchivo);
            $texto = $pdf->getText();

            $lineas = explode("\n", $texto);
            $enSeccionEmpleados = false;
            $empleadoActual = [];
            $empleadosProcesados = 0;
            $nombreArchivo = basename($rutaArchivo);

            foreach ($lineas as $linea) {
                $lineaLimpia = trim($linea);

                // Detectar inicio de sección de empleados
                if (strpos($lineaLimpia, 'Doc. de Identificación') !== false) {
                    $enSeccionEmpleados = true;
                    continue;
                }

                // Detectar fin de sección de empleados
                if (strpos($lineaLimpia, 'Totales por Estructura Programática') !== false) {
                    $enSeccionEmpleados = false;
                    if (!empty($empleadoActual)) {
                        $this->guardarRegistroEmpleado($empleadoActual, $year, $nombreArchivo);
                        $empleadosProcesados++;
                        $empleadoActual = [];
                    }
                    continue;
                }

                if ($enSeccionEmpleados && !empty($lineaLimpia)) {
                    // Detectar línea con datos de empleado
                    if (preg_match('/^\d{7,8}\s+/', $lineaLimpia) ||
                        preg_match('/[A-ZÁÉÍÓÚÑ\s]+\s+\d{2}\/\d{2}\/\d{4}\s+[A-Z]+\s+\d+/', $lineaLimpia)) {

                        if (!empty($empleadoActual)) {
                            $this->guardarRegistroEmpleado($empleadoActual, $year, $nombreArchivo);
                            $empleadosProcesados++;
                        }

                        $empleadoActual = $this->extraerDatosEmpleado($lineaLimpia);
                    }
                    // Si ya estamos procesando un empleado, buscar datos adicionales
                    else if (!empty($empleadoActual)) {
                        $this->completarDatosEmpleado($empleadoActual, $lineaLimpia);
                    }
                }
            }

            // Guardar último empleado
            if (!empty($empleadoActual)) {
                $this->guardarRegistroEmpleado($empleadoActual, $year, $nombreArchivo);
                $empleadosProcesados++;
            }

            return [
                'success' => true,
                'empleados_procesados' => $empleadosProcesados,
                'archivo' => $nombreArchivo
            ];

        } catch (\Exception $e) {
            Log::error("Error procesando planilla {$rutaArchivo}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'archivo' => basename($rutaArchivo)
            ];
        }
    }

    /**
     * Extraer datos del empleado desde una línea de texto
     */
    private function extraerDatosEmpleado($linea)
    {
        // Mejorado para manejar diferentes formatos de planilla
        $patron = '/
            (?<cedula>\d{7,8})                          # Cédula (7-8 dígitos)
            \s+
            (?<nombre>[A-ZÁÉÍÓÚÑ\s]+)                   # Nombre (solo mayúsculas y espacios)
            \s+
            (?<fecha_nac>\d{2}\/\d{2}\/\d{4})           # Fecha de nacimiento (dd/mm/yyyy)
            \s+
            (?<nacionalidad>[A-Z]{2})                   # Nacionalidad (2 letras)
            \s+
            (?<dias_trab>\d+)                           # Días trabajados
            \s+
            (?<puesto>[A-ZÁÉÍÓÚÑ\s\/\(\)]+)             # Puesto (texto con caracteres especiales)
            \s+
            (?<item>\d+)                                # Ítem
            \s+
            (?<haber_basico>[\d,]+\.\d{2})              # Haber básico
            \s+
            (?<bono_antiguedad>[\d,]+\.\d{2})           # Bono antigüedad
            \s+
            (?<otros_ingresos>[\d,]+\.\d{2})            # Otros ingresos
            \s+
            (?<total_ingresos>[\d,]+\.\d{2})            # Total ingresos
            \s+
            (?<rc_iva>[\d,]+\.\d{2})                    # RC-IVA
            \s+
            (?<afp>[\d,]+\.\d{2})                       # AFP
            \s+
            (?<otros_descuentos>[\d,]+\.\d{2})          # Otros descuentos
            \s+
            (?<total_descuentos>[\d,]+\.\d{2})          # Total descuentos
            \s+
            (?<liquido_pagable>[\d,]+\.\d{2})           # Líquido pagable
        /x';

        if (preg_match($patron, $linea, $coincidencias)) {
            return [
                'cedula' => trim($coincidencias['cedula']),
                'nombre_completo' => trim($coincidencias['nombre']),
                'fecha_nacimiento' => $coincidencias['fecha_nac'],
                'nacionalidad' => $coincidencias['nacionalidad'],
                'dias_trabajados' => (int) $coincidencias['dias_trab'],
                'puesto' => trim($coincidencias['puesto']),
                'item' => $coincidencias['item'],
                'haber_basico' => (float) str_replace(',', '', $coincidencias['haber_basico']),
                'bono_antiguedad' => (float) str_replace(',', '', $coincidencias['bono_antiguedad']),
                'otros_ingresos' => (float) str_replace(',', '', $coincidencias['otros_ingresos']),
                'total_ingresos' => (float) str_replace(',', '', $coincidencias['total_ingresos']),
                'rc_iva' => (float) str_replace(',', '', $coincidencias['rc_iva']),
                'afp' => (float) str_replace(',', '', $coincidencias['afp']),
                'otros_descuentos' => (float) str_replace(',', '', $coincidencias['otros_descuentos']),
                'total_descuentos' => (float) str_replace(',', '', $coincidencias['total_descuentos']),
                'liquido_pagable' => (float) str_replace(',', '', $coincidencias['liquido_pagable'])
            ];
        }

        // Patrón alternativo para diferentes formatos
        $patronAlternativo = '/(\d{7,8})\s+([A-Z\s]+)\s+(\d{2}\/\d{2}\/\d{4})\s+([A-Z]{2})\s+(\d+)\s+([\w\s\/\(\)]+)\s+(\d+)\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})/';

        if (preg_match($patronAlternativo, $linea, $coincidencias)) {
            return [
                'cedula' => $coincidencias[1],
                'nombre_completo' => trim($coincidencias[2]),
                'fecha_nacimiento' => $coincidencias[3],
                'nacionalidad' => $coincidencias[4],
                'dias_trabajados' => (int) $coincidencias[5],
                'puesto' => trim($coincidencias[6]),
                'item' => $coincidencias[7],
                'haber_basico' => (float) str_replace(',', '', $coincidencias[8]),
                'bono_antiguedad' => (float) str_replace(',', '', $coincidencias[9]),
                'otros_ingresos' => (float) str_replace(',', '', $coincidencias[10]),
                'total_ingresos' => (float) str_replace(',', '', $coincidencias[11]),
                'rc_iva' => (float) str_replace(',', '', $coincidencias[12]),
                'afp' => (float) str_replace(',', '', $coincidencias[13]),
                'otros_descuentos' => (float) str_replace(',', '', $coincidencias[14]),
                'total_descuentos' => (float) str_replace(',', '', $coincidencias[15]),
                'liquido_pagable' => (float) str_replace(',', '', $coincidencias[16])
            ];
        }

        return [];
    }

    /**
     * Completar datos del empleado con información adicional
     */
    private function completarDatosEmpleado(&$empleado, $linea)
    {
        // Buscar cuenta bancaria
        if (preg_match('/\d{16,20}/', $linea, $coincidencias)) {
            $empleado['cuenta_bancaria'] = $coincidencias[0];
        }

        // Buscar departamento u otra información adicional
        if (preg_match('/DEPARTAMENTO:\s*([A-Z\s]+)/i', $linea, $coincidencias)) {
            $empleado['departamento'] = trim($coincidencias[1]);
        }
    }

    /**
     * Guardar registro del empleado en la base de datos
     */
    private function guardarRegistroEmpleado($datos, $year, $nombreArchivo)
    {
        if (empty($datos['cedula']) || empty($datos['nombre_completo'])) {
            return false;
        }

        // Extraer mes del nombre del archivo
        $mes = $this->extraerMesArchivo($nombreArchivo);

        try {
            // Buscar o crear empleado
            $empleado = PlanillaEmpleado::firstOrCreate(
                ['cedula' => $datos['cedula']],
                [
                    'nombre_completo' => $datos['nombre_completo'],
                    'fecha_nacimiento' => $this->formatearFecha($datos['fecha_nacimiento']),
                    'nacionalidad' => $datos['nacionalidad'],
                    'puesto' => $datos['puesto'],
                    'departamento' => $datos['departamento'] ?? null,
                    'cuenta_bancaria' => $datos['cuenta_bancaria'] ?? null,
                    'fecha_ingreso' => $this->determinarFechaIngreso($datos['cedula'])
                ]
            );

            // Crear registro salarial
            PlanillaRegistro::updateOrCreate(
                [
                    'empleado_id' => $empleado->id,
                    'mes' => $mes,
                    'ano' => $year
                ],
                [
                    'dias_trabajados' => $datos['dias_trabajados'],
                    'haber_basico' => $datos['haber_basico'],
                    'bono_antiguedad' => $datos['bono_antiguedad'],
                    'otros_ingresos' => $datos['otros_ingresos'],
                    'total_ingresos' => $datos['total_ingresos'],
                    'rc_iva' => $datos['rc_iva'],
                    'afp' => $datos['afp'],
                    'otros_descuentos' => $datos['otros_descuentos'],
                    'total_descuentos' => $datos['total_descuentos'],
                    'liquido_pagable' => $datos['liquido_pagable'],
                    'item' => $datos['item'],
                    'cuenta_bancaria' => $datos['cuenta_bancaria'] ?? null,
                    'archivo_origen' => $nombreArchivo
                ]
            );

            return true;

        } catch (\Exception $e) {
            Log::error("Error guardando registro para {$datos['cedula']}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Extraer mes del nombre del archivo
     */
    private function extraerMesArchivo($nombreArchivo)
    {
        $meses = [
            'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04',
            'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08',
            'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12',
            'ene' => '01', 'feb' => '02', 'mar' => '03', 'abr' => '04',
            'may' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
            'sep' => '09', 'oct' => '10', 'nov' => '11', 'dic' => '12'
        ];

        $nombreLower = strtolower($nombreArchivo);

        foreach ($meses as $mesEsp => $mesNum) {
            if (str_contains($nombreLower, $mesEsp)) {
                return $mesNum;
            }
        }

        // Si no se encuentra el mes, intentar extraer del contenido del PDF
        return '01'; // Por defecto, enero
    }

    /**
     * Formatear fecha desde string a objeto Carbon
     */
    private function formatearFecha($fecha)
    {
        try {
            return Carbon::createFromFormat('d/m/Y', $fecha);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Determinar fecha de ingreso (para empleados nuevos)
     */
    private function determinarFechaIngreso($cedula)
    {
        // Si es un empleado nuevo, usar la fecha actual
        // En una implementación real, podrías tener otra forma de determinar esto
        return now();
    }

    /**
     * Procesar planillas existentes en el sistema de archivos
     */
    public function procesarExistentes(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2010|max:' . date('Y')
        ]);

        $year = $request->year;
        $directorio = storage_path("app/planillas/{$year}");

        if (!file_exists($directorio)) {
            return redirect()->back()->with('error', "No existe el directorio para el año {$year}");
        }

        $archivos = glob($directorio . '/*.pdf');
        $procesados = 0;
        $errores = 0;
        $resultados = [];

        foreach ($archivos as $archivo) {
            $resultado = $this->procesarPlanilla($archivo, $year);

            if ($resultado['success']) {
                $procesados++;
                $resultados[] = [
                    'archivo' => basename($archivo),
                    'estado' => 'éxito',
                    'empleados' => $resultado['empleados_procesados']
                ];
            } else {
                $errores++;
                $resultados[] = [
                    'archivo' => basename($archivo),
                    'estado' => 'error',
                    'error' => $resultado['error']
                ];
            }
        }

        $mensaje = "Procesamiento completado. {$procesados} archivos procesados correctamente.";
        if ($errores > 0) {
            $mensaje .= " {$errores} archivos con errores.";
        }

        return redirect()->route('planillas.registros.index')
            ->with('success', $mensaje)
            ->with('resultados', $resultados);
    }

    /**
     * Limpiar registros de un año específico
     */
    public function limpiarRegistros(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2010|max:' . date('Y'),
            'confirmacion' => 'required|accepted'
        ]);

        $year = $request->year;
        $registrosEliminados = PlanillaRegistro::where('ano', $year)->delete();

        return redirect()->route('planillas.registros.index')
            ->with('success', "Se eliminaron {$registrosEliminados} registros del año {$year}.");
    }
}
