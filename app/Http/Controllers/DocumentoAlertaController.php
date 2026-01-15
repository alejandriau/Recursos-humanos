<?php
// app/Http/Controllers/DocumentoAlertaController.php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DocumentoAlertaController extends Controller
{
    public function index(Request $request)
    {
        $query = Persona::with(['cenvis', 'certificados', 'cedula'])
            ->where('estado', 1);

        // BÚSQUEDA POR NOMBRE O APELLIDO
        if ($request->has('buscar') && !empty($request->buscar)) {
            $searchTerm = $request->buscar;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('apellidoPat', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('apellidoMat', 'LIKE', "%{$searchTerm}%")
                  ->orWhereRaw("CONCAT(nombre, ' ', apellidoPat) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("CONCAT(apellidoPat, ' ', nombre) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        $personas = $query->get()
            ->map(function ($persona) {
                // Calcular toda la información necesaria para cada persona
                $persona->tieneVencidos = $this->tieneDocumentosVencidos($persona);
                $persona->documentosVencidos = $this->obtenerDocumentosVencidos($persona);
                $persona->faltanRegistros = $this->faltanRegistros($persona);

                // Información específica de cada documento
                $persona->infoCenvi = $this->getInfoCenvi($persona);
                $persona->infoQuechua = $this->getInfoQuechua($persona);
                $persona->infoCedula = $this->getInfoCedula($persona);

                // Estado general
                $persona->algunoPorVencer = $this->tienePorVencer($persona);
                $persona->necesitaNotificacion = $persona->tieneVencidos || $persona->faltanRegistros;

                return $persona;
            });

        // Filtrar solo los que necesitan notificación si se solicita
        if ($request->has('solo_notificar')) {
            $personas = $personas->filter(function ($persona) {
                return $persona->necesitaNotificacion;
            });
        }

        // Estadísticas
        $totalVencidos = $personas->filter(function ($persona) {
            return $persona->tieneVencidos;
        })->count();

        $totalFaltan = $personas->filter(function ($persona) {
            return $persona->faltanRegistros;
        })->count();

        $totalPorVencer = $personas->filter(function ($persona) {
            return !$persona->tieneVencidos && $persona->algunoPorVencer;
        })->count();

        $totalAlDia = $personas->filter(function ($persona) {
            return !$persona->tieneVencidos && !$persona->algunoPorVencer && !$persona->faltanRegistros;
        })->count();

        $totalPersonas = $personas->count();
        $totalNotificar = $totalVencidos + $totalFaltan;

        $busqueda = $request->buscar ?? '';

        return view('admin.personas.alertas', compact(
            'personas',
            'totalVencidos',
            'totalPorVencer',
            'totalAlDia',
            'totalPersonas',
            'totalFaltan',
            'totalNotificar',
            'busqueda'
        ));
    }
        // NUEVO: Verificar si faltan registros
    private function faltanRegistros($persona)
    {
        // Si no tiene ningún CENVI registrado
        $sinCenvi = $persona->cenvis->isEmpty();

        // Si no tiene ningún certificado de quechua registrado
        $sinQuechua = $persona->certificados->filter(function($cert) {
            return $this->esQuechua($cert);
        })->isEmpty();

        // Si no tiene cédula registrada
        $sinCedula = !$persona->cedula;

        // Considerar que falta si no tiene al menos uno de estos registros
        return $sinCenvi || $sinQuechua || $sinCedula;
    }
        // Métodos privados para calcular información

    /*private function tieneDocumentosVencidos($persona)
    {
        // CENVI vencido
        foreach ($persona->cenvis as $cenvi) {
            if ($cenvi->fecha && Carbon::now()->greaterThan(Carbon::parse($cenvi->fecha)->addYear())) {
                return true;
            }
        }

        // Certificado quechua vencido
        foreach ($persona->certificados as $certificado) {
            if ($this->esQuechua($certificado) &&
                $certificado->fecha &&
                Carbon::now()->greaterThan(Carbon::parse($certificado->fecha)->addYears(3))) {
                return true;
            }
        }

        // Cédula vencida
        if ($persona->cedula &&
            $persona->cedula->fechaVencimiento &&
            Carbon::now()->greaterThan(Carbon::parse($persona->cedula->fechaVencimiento))) {
            return true;
        }

        return false;
    }*/

    private function obtenerDocumentosVencidos($persona)
    {
        $documentos = [];

        foreach ($persona->cenvis as $cenvi) {
            if ($cenvi->fecha && Carbon::now()->greaterThan(Carbon::parse($cenvi->fecha)->addYear())) {
                $documentos[] = [
                    'tipo' => 'CENVI',
                    'fecha_vencimiento' => Carbon::parse($cenvi->fecha)->addYear()->format('d/m/Y')
                ];
            }
        }

        foreach ($persona->certificados as $certificado) {
            if ($this->esQuechua($certificado) &&
                $certificado->fecha &&
                Carbon::now()->greaterThan(Carbon::parse($certificado->fecha)->addYears(3))) {
                $documentos[] = [
                    'tipo' => 'Certificado Quechua',
                    'fecha_vencimiento' => Carbon::parse($certificado->fecha)->addYears(3)->format('d/m/Y')
                ];
            }
        }

        if ($persona->cedula &&
            $persona->cedula->fechaVencimiento &&
            Carbon::now()->greaterThan(Carbon::parse($persona->cedula->fechaVencimiento))) {
            $documentos[] = [
                'tipo' => 'Cédula de Identidad',
                'fecha_vencimiento' => Carbon::parse($persona->cedula->fechaVencimiento)->format('d/m/Y')
            ];
        }

        return $documentos;
    }

    /*private function getInfoCenvi($persona)
    {
        if ($persona->cenvis->isEmpty()) {
            return [
                'tiene' => false,
                'estado' => 'sin-datos',
                'texto' => 'Sin CENVI'
            ];
        }

        $infoCenvi = [];
        foreach ($persona->cenvis as $cenvi) {
            if ($cenvi->fecha) {
                $vencimiento = Carbon::parse($cenvi->fecha)->addYear();
                $dias = Carbon::now()->diffInDays($vencimiento, false);
                $estaVencido = $dias < 0;
                $porVencer = $dias >= 0 && $dias <= 30;

                $infoCenvi[] = [
                    'fecha' => $cenvi->fecha->format('d/m/Y'),
                    'vencimiento' => $vencimiento->format('d/m/Y'),
                    'dias' => $dias,
                    'estaVencido' => $estaVencido,
                    'porVencer' => $porVencer,
                    'claseColor' => $estaVencido ? 'bg-red-100 text-red-800' :
                                    ($porVencer ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'),
                    'icono' => $estaVencido ? 'fa-exclamation-triangle' :
                               ($porVencer ? 'fa-clock' : 'fa-check'),
                    'textoEstado' => $estaVencido ? 'Vencido' :
                                     ($porVencer ? $dias . ' días' : 'OK')
                ];
            }
        }

        return [
            'tiene' => true,
            'datos' => $infoCenvi
        ];
    }*/

    /*private function getInfoQuechua($persona)
    {
        // Buscar certificados de quechua por nombre o categoría
        $certificadoQuechua = $persona->certificados->first(function($cert) {
            return $this->esQuechua($cert);
        });

        if (!$certificadoQuechua) {
            return [
                'tiene' => false,
                'estado' => 'sin-datos',
                'texto' => 'Sin certificado'
            ];
        }

        // Usar fecha_vencimiento si existe, sino calcular a 3 años
        if ($certificadoQuechua->fecha_vencimiento) {
            $vencimiento = Carbon::parse($certificadoQuechua->fecha_vencimiento);
        } elseif ($certificadoQuechua->fecha) {
            $vencimiento = Carbon::parse($certificadoQuechua->fecha)->addYears(3);
        } else {
            return [
                'tiene' => false,
                'estado' => 'sin-fecha',
                'texto' => 'Sin fecha'
            ];
        }

        $dias = Carbon::now()->diffInDays($vencimiento, false);
        $estaVencido = $dias < 0;
        $porVencer = $dias >= 0 && $dias <= 60;

        return [
            'tiene' => true,
            'fecha' => $certificadoQuechua->fecha ?
                    $certificadoQuechua->fecha->format('d/m/Y') : 'Sin fecha',
            'vencimiento' => $vencimiento->format('d/m/Y'),
            'dias' => $dias,
            'estaVencido' => $estaVencido,
            'porVencer' => $porVencer,
            'claseColor' => $estaVencido ? 'bg-red-100 text-red-800' :
                            ($porVencer ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'),
            'icono' => $estaVencido ? 'fa-exclamation-triangle' :
                    ($porVencer ? 'fa-clock' : 'fa-check'),
            'textoEstado' => $estaVencido ? 'Vencido' :
                            ($porVencer ? $dias . ' días' : 'OK'),
            'nombre' => $certificadoQuechua->nombre,
            'categoria' => $certificadoQuechua->categoria
        ];
    }*/

    /*private function getInfoCedula($persona)
    {
        if (!$persona->cedula || !$persona->cedula->fechaVencimiento) {
            return [
                'tiene' => false,
                'estado' => 'sin-datos',
                'texto' => 'Sin fecha'
            ];
        }

        $dias = Carbon::now()->diffInDays(Carbon::parse($persona->cedula->fechaVencimiento), false);
        $estaVencido = $dias < 0;
        $porVencer = $dias >= 0 && $dias <= 90;

        return [
            'tiene' => true,
            'fecha' => Carbon::parse($persona->cedula->fechaVencimiento)->format('d/m/Y'),
            'dias' => $dias,
            'estaVencido' => $estaVencido,
            'porVencer' => $porVencer,
            'claseColor' => $estaVencido ? 'bg-red-100 text-red-800' :
                            ($porVencer ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'),
            'icono' => $estaVencido ? 'fa-exclamation-triangle' :
                       ($porVencer ? 'fa-clock' : 'fa-check'),
            'textoEstado' => $estaVencido ? 'Vencida' :
                             ($porVencer ? $dias . ' días' : 'OK')
        ];
    }*/

    private function esQuechua($certificado)
    {
        // Verificar en nombre O en categoría
        $nombreEsQuechua = stripos($certificado->nombre, 'quechua') !== false;
        $categoriaEsQuechua = !empty($certificado->categoria) &&
                            stripos($certificado->categoria, 'quechua') !== false;

        return $nombreEsQuechua || $categoriaEsQuechua;
    }

    private function tienePorVencer($persona)
    {
        // Verificar si algún documento está por vencer
        if ($persona->infoCenvi['tiene']) {
            foreach ($persona->infoCenvi['datos'] ?? [] as $cenvi) {
                if ($cenvi['porVencer']) return true;
            }
        }

        if ($persona->infoQuechua['tiene'] && $persona->infoQuechua['porVencer']) {
            return true;
        }

        if ($persona->infoCedula['tiene'] && $persona->infoCedula['porVencer']) {
            return true;
        }

        return false;
    }

    public function enviarATodos()
    {
        $personas = Persona::with(['cenvis', 'certificados', 'cedula'])
            ->where('estado', 1)
            ->whereNotNull('telefono')
            ->get();

        $enviados = 0;
        $errores = [];

        foreach ($personas as $persona) {
            $mensaje = $this->generarMensajeVencidos($persona);

            if ($mensaje && $persona->telefono) {
                $enviado = $this->enviarWhatsAppSimple($persona->telefono, $mensaje);

                if ($enviado) {
                    $enviados++;
                } else {
                    $errores[] = $persona->nombre;
                }
            }
        }

        return back()->with([
            'success' => "Mensajes enviados: {$enviados} personas",
            'errores' => $errores
        ]);
    }

    public function enviarIndividual($personaId)
    {
        $persona = Persona::with(['cenvis', 'certificados', 'cedula'])
            ->where('id', $personaId)
            ->first();

        if (!$persona) {
            return back()->with('error', 'Persona no encontrada');
        }

        if (!$persona->telefono) {
            return back()->with('error', 'No tiene número de teléfono');
        }

        $mensaje = $this->generarMensajeVencidos($persona);

        if (!$mensaje) {
            return back()->with('info', 'No tiene documentos vencidos');
        }

        $enviado = $this->enviarWhatsAppSimple($persona->telefono, $mensaje);

        if ($enviado) {
            return back()->with('success', 'Mensaje enviado a ' . $persona->nombre);
        } else {
            return back()->with('error', 'Error al enviar mensaje');
        }
    }

    private function generarMensajeVencidos($persona)
    {
        $vencidos = [];

        // Verificar CENVI (1 año)
        foreach ($persona->cenvis as $cenvi) {
            if ($cenvi->fecha) {
                $vencimiento = Carbon::parse($cenvi->fecha)->addYear();
                if (Carbon::now()->greaterThan($vencimiento)) {
                    $vencidos[] = "CENVI - Vencido el " . $vencimiento->format('d/m/Y');
                }
            }
        }

        // Verificar certificados de quechua (3 años)
        foreach ($persona->certificados as $certificado) {
            $esQuechua = stripos($certificado->nombre, 'quechua') !== false;

            if ($esQuechua && $certificado->fecha) {
                $vencimiento = Carbon::parse($certificado->fecha)->addYears(3);
                if (Carbon::now()->greaterThan($vencimiento)) {
                    $vencidos[] = "Certificado Quechua - Vencido el " . $vencimiento->format('d/m/Y');
                }
            }
        }

        // Verificar cédula
        if ($persona->cedula && $persona->cedula->fechaVencimiento) {
            $vencimiento = Carbon::parse($persona->cedula->fechaVencimiento);
            if (Carbon::now()->greaterThan($vencimiento)) {
                $vencidos[] = "Cédula de Identidad - Vencida el " . $vencimiento->format('d/m/Y');
            }
        }

        if (empty($vencidos)) {
            return null;
        }

        $mensaje = "Sr(a). {$persona->nombre},\n";
        $mensaje .= "Tiene documentos vencidos:\n\n";

        foreach ($vencidos as $doc) {
            $mensaje .= "• {$doc}\n";
        }

        $mensaje .= "\nPor favor regularice su situación.";

        return $mensaje;
    }

    private function enviarWhatsAppSimple($telefono, $mensaje)
    {
        // MÉTODO 1: API de WhatsApp Web (simple con cURL)
        // Necesitas configurar una instancia de WhatsApp Web

        // MÉTODO 2: Servicio gratuito (CallMeBot)
        $apiKey = 'TU_API_KEY'; // Regístrate en callmebot.com
        $phone = $this->formatearTelefono($telefono);

        $url = "https://api.callmebot.com/whatsapp.php?"
             . "phone={$phone}"
             . "&text=" . urlencode($mensaje)
             . "&apikey={$apiKey}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return strpos($response, 'Message sent') !== false;
    }

    private function formatearTelefono($telefono)
    {
        // Ejemplo: 12345678 -> 59112345678
        $telefono = preg_replace('/[^0-9]/', '', $telefono);

        if (strlen($telefono) == 8) {
            return '591' . $telefono;
        }

        return $telefono;
    }

    // Función helper para verificar si tiene documentos vencidos
    /*public static function tieneDocumentosVencidos($persona)
    {
        $vencidos = false;

        // Verificar CENVI
        foreach ($persona->cenvis as $cenvi) {
            if ($cenvi->fecha) {
                $vencimiento = Carbon::parse($cenvi->fecha)->addYear();
                if (Carbon::now()->greaterThan($vencimiento)) {
                    $vencidos = true;
                    break;
                }
            }
        }

        // Verificar certificados quechua
        foreach ($persona->certificados as $certificado) {
            $esQuechua = stripos($certificado->nombre, 'quechua') !== false;
            if ($esQuechua && $certificado->fecha) {
                $vencimiento = Carbon::parse($certificado->fecha)->addYears(3);
                if (Carbon::now()->greaterThan($vencimiento)) {
                    $vencidos = true;
                    break;
                }
            }
        }

        // Verificar cédula
        if ($persona->cedula && $persona->cedula->fechaVencimiento) {
            if (Carbon::now()->greaterThan(Carbon::parse($persona->cedula->fechaVencimiento))) {
                $vencidos = true;
            }
        }

        return $vencidos;
    }*/

        private function tieneDocumentosVencidos($persona)
    {
        // Si falta registro, considerar como "pendiente" pero no vencido
        if ($this->faltanRegistros($persona)) {
            return false; // Los que faltan van en categoría aparte
        }

        // CENVI vencido
        foreach ($persona->cenvis as $cenvi) {
            if ($cenvi->fecha && Carbon::now()->greaterThan(Carbon::parse($cenvi->fecha)->addYear())) {
                return true;
            }
        }

        // Certificado quechua vencido
        foreach ($persona->certificados as $certificado) {
            if ($this->esQuechua($certificado) &&
                $certificado->fecha &&
                Carbon::now()->greaterThan($this->getFechaVencimientoCertificado($certificado))) {
                return true;
            }
        }

        // Cédula vencida
        if ($persona->cedula &&
            $persona->cedula->fechaVencimiento &&
            Carbon::now()->greaterThan(Carbon::parse($persona->cedula->fechaVencimiento))) {
            return true;
        }

        return false;
    }

    private function getInfoCenvi($persona)
    {
        if ($persona->cenvis->isEmpty()) {
            return [
                'tiene' => false,
                'estado' => 'falta',
                'texto' => 'Falta',
                'claseColor' => 'bg-red-100 text-red-800',
                'icono' => 'fa-exclamation-circle'
            ];
        }

        $infoCenvi = [];
        $hayVencido = false;
        $hayPorVencer = false;

        foreach ($persona->cenvis as $cenvi) {
            if ($cenvi->fecha) {
                $vencimiento = Carbon::parse($cenvi->fecha)->addYear();
                $dias = Carbon::now()->diffInDays($vencimiento, false);
                $estaVencido = $dias < 0;
                $porVencer = $dias >= 0 && $dias <= 30;

                if ($estaVencido) $hayVencido = true;
                if ($porVencer) $hayPorVencer = true;

                $infoCenvi[] = [
                    'fecha' => $cenvi->fecha->format('d/m/Y'),
                    'vencimiento' => $vencimiento->format('d/m/Y'),
                    'dias' => $dias,
                    'estaVencido' => $estaVencido,
                    'porVencer' => $porVencer
                ];
            }
        }

        // Determinar color
        $claseColor = 'bg-green-100 text-green-800';
        $icono = 'fa-check';
        $textoEstado = 'OK';

        if ($hayVencido) {
            $claseColor = 'bg-red-100 text-red-800';
            $icono = 'fa-exclamation-triangle';
            $textoEstado = 'Vencido';
        } elseif ($hayPorVencer) {
            $claseColor = 'bg-yellow-100 text-yellow-800';
            $icono = 'fa-clock';

            // Encontrar el que vence más pronto
            $cenviMasCercano = collect($infoCenvi)
                ->where('dias', '>=', 0)
                ->sortBy('dias')
                ->first();

            $textoEstado = $cenviMasCercano ? $cenviMasCercano['dias'] . ' días' : 'Por Vencer';
        }

        return [
            'tiene' => true,
            'datos' => $infoCenvi,
            'claseColor' => $claseColor,
            'icono' => $icono,
            'textoEstado' => $textoEstado
        ];
    }

    private function getInfoQuechua($persona)
    {
        // Filtrar certificados de quechua
        $certificadosQuechua = $persona->certificados->filter(function($cert) {
            return $this->esQuechua($cert);
        });

        if ($certificadosQuechua->isEmpty()) {
            return [
                'tiene' => false,
                'estado' => 'falta',
                'texto' => 'Falta',
                'claseColor' => 'bg-red-100 text-red-800',
                'icono' => 'fa-exclamation-circle'
            ];
        }

        // Tomar el más reciente para mostrar
        $certificadoMasReciente = $certificadosQuechua->sortByDesc('fecha')->first();

        // Calcular vencimiento
        $fechaVencimiento = $this->getFechaVencimientoCertificado($certificadoMasReciente);

        if (!$fechaVencimiento) {
            return [
                'tiene' => false,
                'estado' => 'sin-fecha',
                'texto' => 'Sin fecha',
                'claseColor' => 'bg-gray-100 text-gray-800',
                'icono' => 'fa-question'
            ];
        }

        $dias = Carbon::now()->diffInDays($fechaVencimiento, false);
        $estaVencido = $dias < 0;
        $porVencer = $dias >= 0 && $dias <= 60;

        $claseColor = 'bg-green-100 text-green-800';
        $icono = 'fa-check';
        $textoEstado = 'OK';

        if ($estaVencido) {
            $claseColor = 'bg-red-100 text-red-800';
            $icono = 'fa-exclamation-triangle';
            $textoEstado = 'Vencido';
        } elseif ($porVencer) {
            $claseColor = 'bg-yellow-100 text-yellow-800';
            $icono = 'fa-clock';
            $textoEstado = $dias . ' días';
        }

        return [
            'tiene' => true,
            'fecha' => $certificadoMasReciente->fecha ?
                       $certificadoMasReciente->fecha->format('d/m/Y') : 'Sin fecha',
            'vencimiento' => $fechaVencimiento->format('d/m/Y'),
            'dias' => $dias,
            'estaVencido' => $estaVencido,
            'porVencer' => $porVencer,
            'claseColor' => $claseColor,
            'icono' => $icono,
            'textoEstado' => $textoEstado
        ];
    }

    private function getInfoCedula($persona)
    {
        if (!$persona->cedula) {
            return [
                'tiene' => false,
                'estado' => 'falta',
                'texto' => 'Falta',
                'claseColor' => 'bg-red-100 text-red-800',
                'icono' => 'fa-exclamation-circle'
            ];
        }

        if (!$persona->cedula->fechaVencimiento) {
            return [
                'tiene' => true,
                'estado' => 'sin-fecha',
                'texto' => 'Sin fecha',
                'claseColor' => 'bg-gray-100 text-gray-800',
                'icono' => 'fa-question'
            ];
        }

        $dias = Carbon::now()->diffInDays(Carbon::parse($persona->cedula->fechaVencimiento), false);
        $estaVencido = $dias < 0;
        $porVencer = $dias >= 0 && $dias <= 90;

        $claseColor = 'bg-green-100 text-green-800';
        $icono = 'fa-check';
        $textoEstado = 'OK';

        if ($estaVencido) {
            $claseColor = 'bg-red-100 text-red-800';
            $icono = 'fa-exclamation-triangle';
            $textoEstado = 'Vencida';
        } elseif ($porVencer) {
            $claseColor = 'bg-yellow-100 text-yellow-800';
            $icono = 'fa-clock';
            $textoEstado = $dias . ' días';
        }

        return [
            'tiene' => true,
            'fecha' => Carbon::parse($persona->cedula->fechaVencimiento)->format('d/m/Y'),
            'dias' => $dias,
            'estaVencido' => $estaVencido,
            'porVencer' => $porVencer,
            'claseColor' => $claseColor,
            'icono' => $icono,
            'textoEstado' => $textoEstado
        ];
    }

    // Método auxiliar para calcular fecha de vencimiento
    private function getFechaVencimientoCertificado($certificado)
    {
        if ($certificado->fecha_vencimiento) {
            return Carbon::parse($certificado->fecha_vencimiento);
        }

        if ($certificado->fecha) {
            return Carbon::parse($certificado->fecha)->addYears(3);
        }

        return null;
    }
}
