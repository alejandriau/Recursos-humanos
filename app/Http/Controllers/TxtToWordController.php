<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TxtToWordController extends Controller
{
    public function showForm()
    {
        return view('convert.form');
    }

    public function convertTxtToWord(Request $request)
    {
        try {
            $request->validate([
                'txt_file' => 'required|file|max:10240'
            ]);

            $txtFile = $request->file('txt_file');
            $content = file_get_contents($txtFile->getRealPath());
            $content = $this->convertEncoding($content);

            if (empty($content)) {
                return back()->with('error', 'El archivo TXT está vacío.');
            }

            $wordContent = $this->generateHorizontalWord($content, 8);

            $fileName = pathinfo($txtFile->getClientOriginalName(), PATHINFO_FILENAME) . '_HORIZONTAL.doc';

            return response($wordContent)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            Log::error('Error converting TXT to Word: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Genera Word en HORIZONTAL OBLIGATORIO
     */
    private function generateHorizontalWord($content, $fontSize)
    {
        $fontFamily = "'Courier New', Courier, monospace";

        $lines = explode("\n", $content);
        $formattedContent = '';

        foreach ($lines as $line) {
            if (trim($line) === '') {
                $formattedContent .= '<div style="height: 12pt;"></div>';
            } else {
                $formattedLine = $this->preserveSpecialCharacters($line);
                $formattedLine = str_replace(' ', '&nbsp;', $formattedLine);
                $formattedLine = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $formattedLine);

                $formattedContent .= '<div style="white-space: nowrap; margin: 0; padding: 0; line-height: 1.0;">' . $formattedLine . '</div>';
            }
        }

        $html = <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PLANILLA HORIZONTAL</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
            <w:ValidateAgainstSchemas/>
            <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
            <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
            <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
            <w:Compatibility>
                <w:BreakWrappedTables/>
                <w:SnapToGridInCell/>
                <w:WrapTextWithPunct/>
                <w:UseAsianBreakRules/>
                <w:UseWord2010TableStyleRules/>
            </w:Compatibility>
        </w:WordDocument>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
            <w:Compatibility>
                <w:BreakWrappedTables/>
                <w:SnapToGridInCell/>
                <w:WrapTextWithPunct/>
                <w:UseAsianBreakRules/>
                <w:UseWord2010TableStyleRules/>
            </w:Compatibility>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        /* FORZAR HORIZONTAL - CONFIGURACIÓN CORREGIDA */
        @page WordSection1 {
            size: 11in 8.5in; /* ALTO x ANCHO - Esto es horizontal */
            mso-page-orientation: landscape;
            margin: 0.2in 0.2in 0.2in 0.2in;
            mso-header-margin: 0.2in;
            mso-footer-margin: 0.2in;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: $fontFamily;
            font-size: {$fontSize}pt;
            line-height: 1.0;
            width: 10.6in; /* Ancho máximo en horizontal */
        }

        div.WordSection1 {
            page: WordSection1;
        }

        /* Estilo para contenido ancho */
        .wide-content {
            width: 10.6in;
            min-width: 10.6in;
            max-width: 10.6in;
        }
    </style>
</head>
<body>
    <div class="WordSection1">
        <div class="wide-content">
            $formattedContent
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Versión EXTRA HORIZONTAL con configuración más agresiva
     */
    public function convertTxtToWordExtraHorizontal(Request $request)
    {
        try {
            $request->validate([
                'txt_file' => 'required|file|max:10240'
            ]);

            $txtFile = $request->file('txt_file');
            $content = file_get_contents($txtFile->getRealPath());
            $content = $this->convertEncoding($content);

            $wordContent = $this->generateExtraHorizontalWord($content, 8);

            $fileName = pathinfo($txtFile->getClientOriginalName(), PATHINFO_FILENAME) . '_EXTRA_HORIZONTAL.doc';

            return response($wordContent)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function generateExtraHorizontalWord($content, $fontSize)
    {
        $fontFamily = "'Courier New', Courier, monospace";

        $lines = explode("\n", $content);
        $formattedContent = '';

        foreach ($lines as $line) {
            if (trim($line) === '') {
                $formattedContent .= '<br style="mso-data-placement:same-cell;">';
            } else {
                $formattedLine = $this->preserveSpecialCharacters($line);
                $formattedLine = str_replace(' ', '&nbsp;', $formattedLine);
                $formattedLine = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $formattedLine);

                $formattedContent .= '<div style="white-space: nowrap; margin: 0; padding: 0; line-height: 1.0; mso-line-height-rule: exactly;">' . $formattedLine . '</div>';
            }
        }

        $html = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>PLANILLA EXTRA HORIZONTAL</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: $fontFamily;
            font-size: {$fontSize}pt;
            line-height: 1.0;
        }
    </style>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
            <w:Compatibility>
                <w:BreakWrappedTables/>
                <w:SnapToGridInCell/>
                <w:WrapTextWithPunct/>
                <w:UseAsianBreakRules/>
            </w:Compatibility>
        </w:WordDocument>
    </xml>
    <![endif]-->
</head>
<body lang="ES" style="tab-interval: 0.5in;">
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
            <w:Compatibility>
                <w:BreakWrappedTables/>
                <w:SnapToGridInCell/>
                <w:WrapTextWithPunct/>
                <w:UseAsianBreakRules/>
            </w:Compatibility>
        </w:WordDocument>
        <w:LatentStyles DefLockedState="false" LatentStyleCount="156"></w:LatentStyles>
    </xml>
    <style>
        /* Page Definitions */
        @page Section1 {
            size: 11in 8.5in;
            mso-page-orientation: landscape;
            margin: 0.2in 0.2in 0.2in 0.2in;
            mso-header-margin: 0.2in;
            mso-footer-margin: 0.2in;
            mso-paper-source: 0;
        }
        div.Section1 {
            page: Section1;
            mso-page-orientation: landscape;
        }
    </style>
    <![endif]-->
    <div class="Section1">
        $formattedContent
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Método con tamaño de fuente más pequeño para contenido MUY ancho
     */
    public function convertTxtToWordWide(Request $request)
    {
        try {
            $request->validate([
                'txt_file' => 'required|file|max:10240'
            ]);

            $txtFile = $request->file('txt_file');
            $content = file_get_contents($txtFile->getRealPath());
            $content = $this->convertEncoding($content);

            // Calcular tamaño óptimo para contenido ancho
            $optimalSize = $this->calculateOptimalFontSize($content);
            $wordContent = $this->generateHorizontalWord($content, $optimalSize);

            $fileName = pathinfo($txtFile->getClientOriginalName(), PATHINFO_FILENAME) . '_ANCHO.doc';

            return response($wordContent)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Métodos auxiliares
    private function convertEncoding($content)
    {
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        return $content;
    }

    private function preserveSpecialCharacters($text)
    {
        $text = str_replace('&', '&amp;', $text);
        $text = str_replace('<', '&lt;', $text);
        $text = str_replace('>', '&gt;', $text);
        return $text;
    }

    private function calculateOptimalFontSize($content)
    {
        $lines = explode("\n", $content);
        $maxLineLength = 0;

        foreach ($lines as $line) {
            $lineLength = mb_strlen($line);
            if ($lineLength > $maxLineLength) {
                $maxLineLength = $lineLength;
            }
        }

        // Ajustes más agresivos para contenido ancho
        if ($maxLineLength > 250) return 5;
        if ($maxLineLength > 200) return 6;
        if ($maxLineLength > 150) return 7;
        return 8;
    }

    // Métodos para tamaños específicos
    public function convertTxtToWordSize7(Request $request)
    {
        return $this->convertWithFixedSize($request, 7, 'size7');
    }

    public function convertTxtToWordSize8(Request $request)
    {
        return $this->convertWithFixedSize($request, 8, 'size8');
    }

    public function convertTxtToWordSize6(Request $request)
    {
        return $this->convertWithFixedSize($request, 6, 'size6');
    }

    private function convertWithFixedSize($request, $fontSize, $suffix)
    {
        try {
            $request->validate(['txt_file' => 'required|file|max:10240']);

            $txtFile = $request->file('txt_file');
            $content = file_get_contents($txtFile->getRealPath());
            $content = $this->convertEncoding($content);

            $wordContent = $this->generateHorizontalWord($content, $fontSize);

            $fileName = pathinfo($txtFile->getClientOriginalName(), PATHINFO_FILENAME) . "_{$suffix}.doc";

            return response($wordContent)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
