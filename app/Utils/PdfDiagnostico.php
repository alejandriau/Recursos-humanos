<?php

namespace App\Utils;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PdfDiagnostico
{
    public static function analizarPdf($rutaArchivo)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($rutaArchivo);
            $texto = $pdf->getText();

            $resultado = [
                'exito' => true,
                'nombre_archivo' => basename($rutaArchivo),
                'tamano_bytes' => filesize($rutaArchivo),
                'longitud_texto' => strlen($texto),
                'lineas_totales' => count(explode("\n", $texto)),
                'es_posible_ocr' => strlen(trim($texto)) < 100,
                'patrones_encontrados' => [],
                'primeras_lineas' => [],
                'texto_completo' => $texto
            ];

            // Analizar patrones
            $resultado['patrones_encontrados'] = self::buscarPatrones($texto);

            // Obtener primeras líneas
            $lineas = explode("\n", $texto);
            $resultado['primeras_lineas'] = array_slice($lineas, 0, 30);

            // Guardar diagnóstico
            self::guardarDiagnostico($resultado, $rutaArchivo);

            return $resultado;

        } catch (\Exception $e) {
            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'nombre_archivo' => basename($rutaArchivo)
            ];
        }
    }

    private static function buscarPatrones($texto)
    {
        $patrones = [
            'cedulas' => preg_match_all('/\d{7,8}/', $texto, $matches) ? $matches[0] : [],
            'fechas' => preg_match_all('/\d{2}\/\d{2}\/\d{4}/', $texto, $matches) ? $matches[0] : [],
            'montos' => preg_match_all('/[\d,]+\.\d{2}/', $texto, $matches) ? $matches[0] : [],
            'lineas_empleados' => preg_match_all('/\d{7,8}\s+[A-ZÁÉÍÓÚÑ\s]+\s+\d{2}\/\d{2}\/\d{4}/', $texto, $matches) ? $matches[0] : [],
            'seccion_empleados' => strpos($texto, 'Doc. de Identificación') !== false ||
                                    strpos($texto, 'Identificación') !== false,
            'totales' => strpos($texto, 'Totales por Estructura Programática') !== false ||
                        strpos($texto, 'TOTAL GENERAL') !== false
        ];

        return $patrones;
    }

    private static function guardarDiagnostico($resultado, $rutaArchivo)
    {
        $nombreBase = basename($rutaArchivo, '.pdf');
        $contenido = "=== DIAGNÓSTICO PDF ===\n";
        $contenido .= "Archivo: " . $resultado['nombre_archivo'] . "\n";
        $contenido .= "Tamaño: " . $resultado['tamano_bytes'] . " bytes\n";
        $contenido .= "Texto extraído: " . $resultado['longitud_texto'] . " caracteres\n";
        $contenido .= "Líneas: " . $resultado['lineas_totales'] . "\n";
        $contenido .= "¿Necesita OCR?: " . ($resultado['es_posible_ocr'] ? 'SÍ' : 'NO') . "\n\n";

        $contenido .= "=== PATRONES ENCONTRADOS ===\n";
        foreach ($resultado['patrones_encontrados'] as $patron => $valor) {
            if (is_array($valor)) {
                $contenido .= $patron . ": " . count($valor) . " encontrados\n";
            } else {
                $contenido .= $patron . ": " . ($valor ? 'SÍ' : 'NO') . "\n";
            }
        }

        $contenido .= "\n=== PRIMERAS 30 LÍNEAS ===\n";
        foreach ($resultado['primeras_lineas'] as $i => $linea) {
            $contenido .= sprintf("%3d: %s\n", $i + 1, $linea);
        }

        $contenido .= "\n=== TEXTO COMPLETO ( primeros 2000 chars ) ===\n";
        $contenido .= substr($resultado['texto_completo'], 0, 2000);

        Storage::put("diagnosticos/{$nombreBase}_diagnostico.txt", $contenido);
    }
}
