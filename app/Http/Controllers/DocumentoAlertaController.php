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
        // MODIFICACIÓN: Cargar solo cédulas con estado = 1 también
        $query = Persona::with([
            'cenvis' => function($query) {
                $query->where('estado', 1);
            },
            'certificados' => function($query) {
                $query->where('estado', 1);
            },
            'cedula' => function($query) {
                $query->where('estado', 1); // SOLO cédulas activas
            }
        ])->where('estado', 1);

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
                // Ya solo tenemos documentos activos
                $persona->ultimoCenvi = $this->getUltimoCenvi($persona);
                $persona->ultimoQuechua = $this->getUltimoQuechua($persona);
                $persona->ultimoCedula = $this->getUltimaCedulaActiva($persona); // Nueva función

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

    // Obtener el último CENVI (ya vienen filtrados con estado = 1)
    private function getUltimoCenvi($persona)
    {
        if ($persona->cenvis->isEmpty()) {
            return null;
        }

        // Ordenar por fecha descendente y tomar el primero (más reciente)
        return $persona->cenvis->sortByDesc('fecha')->first();
    }

    // Obtener el último certificado de Quechua (ya vienen filtrados con estado = 1)
    private function getUltimoQuechua($persona)
    {
        // Filtrar certificados Quechua de los ya activos
        $certificadosQuechua = $persona->certificados->filter(function($cert) {
            return $this->esQuechua($cert);
        });

        if ($certificadosQuechua->isEmpty()) {
            return null;
        }

        // Ordenar por fecha descendente y tomar el primero (más reciente)
        return $certificadosQuechua->sortByDesc('fecha')->first();
    }

    // NUEVO: Obtener la cédula activa (estado = 1)
    private function getUltimaCedulaActiva($persona)
    {
        // Como ya cargamos solo cédulas con estado = 1, podemos devolverla directamente
        // Pero primero verificamos que exista
        if (!$persona->cedula) {
            return null;
        }

        // Verificar que realmente tenga estado = 1 (por si acaso)
        if (isset($persona->cedula->estado) && $persona->cedula->estado != 1) {
            return null;
        }

        return $persona->cedula;
    }

    // NUEVO: Verificar si faltan registros ACTIVOS
    private function faltanRegistros($persona)
    {
        // Si no tiene ningún CENVI ACTIVO registrado
        $sinCenviActivo = $persona->cenvis->isEmpty();

        // Si no tiene ningún certificado de quechua ACTIVO registrado
        $sinQuechuaActivo = $persona->certificados->filter(function($cert) {
            return $this->esQuechua($cert);
        })->isEmpty();

        // Si no tiene cédula ACTIVA registrada
        $sinCedulaActiva = !$this->getUltimaCedulaActiva($persona);

        // Considerar que falta si no tiene al menos uno de estos registros ACTIVOS
        return $sinCenviActivo || $sinQuechuaActivo || $sinCedulaActiva;
    }

    private function tieneDocumentosVencidos($persona)
    {
        // Si falta registro ACTIVO, considerar como "pendiente" pero no vencido
        if ($this->faltanRegistros($persona)) {
            return false; // Los que faltan van en categoría aparte
        }

        // Verificar último CENVI ACTIVO vencido
        $ultimoCenvi = $this->getUltimoCenvi($persona);
        if ($ultimoCenvi && $ultimoCenvi->fecha &&
            Carbon::now()->greaterThan(Carbon::parse($ultimoCenvi->fecha)->addYear())) {
            return true;
        }

        // Verificar último certificado quechua ACTIVO vencido
        $ultimoQuechua = $this->getUltimoQuechua($persona);
        if ($ultimoQuechua && $ultimoQuechua->fecha &&
            Carbon::now()->greaterThan($this->getFechaVencimientoCertificado($ultimoQuechua))) {
            return true;
        }

        // Verificar cédula ACTIVA vencida
        $ultimaCedula = $this->getUltimaCedulaActiva($persona);
        if ($ultimaCedula &&
            $ultimaCedula->fechaVencimiento &&
            Carbon::now()->greaterThan(Carbon::parse($ultimaCedula->fechaVencimiento))) {
            return true;
        }

        return false;
    }

    private function obtenerDocumentosVencidos($persona)
    {
        $documentos = [];

        // Verificar último CENVI ACTIVO
        $ultimoCenvi = $this->getUltimoCenvi($persona);
        if ($ultimoCenvi && $ultimoCenvi->fecha &&
            Carbon::now()->greaterThan(Carbon::parse($ultimoCenvi->fecha)->addYear())) {
            $documentos[] = [
                'tipo' => 'CENVI',
                'fecha_vencimiento' => Carbon::parse($ultimoCenvi->fecha)->addYear()->format('d/m/Y'),
                'fecha_registro' => $ultimoCenvi->fecha->format('d/m/Y'),
                'estado_registro' => $ultimoCenvi->estado
            ];
        }

        // Verificar último certificado quechua ACTIVO
        $ultimoQuechua = $this->getUltimoQuechua($persona);
        if ($ultimoQuechua && $ultimoQuechua->fecha &&
            Carbon::now()->greaterThan($this->getFechaVencimientoCertificado($ultimoQuechua))) {
            $documentos[] = [
                'tipo' => 'Certificado Quechua',
                'fecha_vencimiento' => $this->getFechaVencimientoCertificado($ultimoQuechua)->format('d/m/Y'),
                'fecha_registro' => $ultimoQuechua->fecha->format('d/m/Y'),
                'estado_registro' => $ultimoQuechua->estado,
                'nombre' => $ultimoQuechua->nombre
            ];
        }

        // Verificar cédula ACTIVA
        $ultimaCedula = $this->getUltimaCedulaActiva($persona);
        if ($ultimaCedula &&
            $ultimaCedula->fechaVencimiento &&
            Carbon::now()->greaterThan(Carbon::parse($ultimaCedula->fechaVencimiento))) {
            $documentos[] = [
                'tipo' => 'Cédula de Identidad',
                'fecha_vencimiento' => Carbon::parse($ultimaCedula->fechaVencimiento)->format('d/m/Y'),
                'fecha_emision' => $ultimaCedula->fechaEmision ?
                    Carbon::parse($ultimaCedula->fechaEmision)->format('d/m/Y') : 'No registrada',
                'estado_registro' => $ultimaCedula->estado
            ];
        }

        return $documentos;
    }

    private function getInfoCenvi($persona)
    {
        $ultimoCenvi = $this->getUltimoCenvi($persona);

        if (!$ultimoCenvi) {
            return [
                'tiene' => false,
                'estado' => 'falta',
                'texto' => 'Falta',
                'claseColor' => 'bg-red-100 text-red-800',
                'icono' => 'fa-exclamation-circle',
                'total_registros' => $persona->cenvis->count()
            ];
        }

        if (!$ultimoCenvi->fecha) {
            return [
                'tiene' => true,
                'estado' => 'sin-fecha',
                'texto' => 'Sin fecha',
                'claseColor' => 'bg-gray-100 text-gray-800',
                'icono' => 'fa-question',
                'total_registros' => $persona->cenvis->count()
            ];
        }

        $vencimiento = Carbon::parse($ultimoCenvi->fecha)->addYear();
        $dias = Carbon::now()->diffInDays($vencimiento, false);
        $estaVencido = $dias < 0;
        $porVencer = $dias >= 0 && $dias <= 30;

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
            'ultimo_registro' => [
                'fecha' => $ultimoCenvi->fecha->format('d/m/Y'),
                'vencimiento' => $vencimiento->format('d/m/Y'),
                'dias' => $dias,
                'estaVencido' => $estaVencido,
                'porVencer' => $porVencer,
                'estado' => $ultimoCenvi->estado
            ],
            'claseColor' => $claseColor,
            'icono' => $icono,
            'textoEstado' => $textoEstado,
            'total_registros' => $persona->cenvis->count(),
            'es_ultimo' => true,
            'es_activo' => true
        ];
    }

    private function getInfoQuechua($persona)
    {
        $ultimoQuechua = $this->getUltimoQuechua($persona);

        if (!$ultimoQuechua) {
            return [
                'tiene' => false,
                'estado' => 'falta',
                'texto' => 'Falta',
                'claseColor' => 'bg-red-100 text-red-800',
                'icono' => 'fa-exclamation-circle',
                'total_registros' => $persona->certificados->filter(function($cert) {
                    return $this->esQuechua($cert);
                })->count()
            ];
        }

        $fechaVencimiento = $this->getFechaVencimientoCertificado($ultimoQuechua);

        if (!$fechaVencimiento) {
            return [
                'tiene' => true,
                'estado' => 'sin-fecha',
                'texto' => 'Sin fecha',
                'claseColor' => 'bg-gray-100 text-gray-800',
                'icono' => 'fa-question',
                'total_registros' => $persona->certificados->filter(function($cert) {
                    return $this->esQuechua($cert);
                })->count()
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
            'ultimo_registro' => [
                'fecha' => $ultimoQuechua->fecha ?
                    $ultimoQuechua->fecha->format('d/m/Y') : 'Sin fecha',
                'vencimiento' => $fechaVencimiento->format('d/m/Y'),
                'dias' => $dias,
                'estaVencido' => $estaVencido,
                'porVencer' => $porVencer,
                'nombre' => $ultimoQuechua->nombre,
                'categoria' => $ultimoQuechua->categoria,
                'estado' => $ultimoQuechua->estado
            ],
            'claseColor' => $claseColor,
            'icono' => $icono,
            'textoEstado' => $textoEstado,
            'total_registros' => $persona->certificados->filter(function($cert) {
                return $this->esQuechua($cert);
            })->count(),
            'es_ultimo' => true,
            'es_activo' => true
        ];
    }

    private function getInfoCedula($persona)
    {
        $ultimaCedula = $this->getUltimaCedulaActiva($persona);

        if (!$ultimaCedula) {
            // Podrías cargar todas las cédulas para mostrar estadísticas
            // $todasCedulas = $persona->cedulas()->get(); // Si tienes relación cedulas (plural)
            // Pero asumiendo que solo hay una relación one-to-one

            return [
                'tiene' => false,
                'estado' => 'falta',
                'texto' => 'Falta',
                'claseColor' => 'bg-red-100 text-red-800',
                'icono' => 'fa-exclamation-circle',
                'mensaje' => 'No hay cédula activa (estado = 1)'
            ];
        }

        if (!$ultimaCedula->fechaVencimiento) {
            return [
                'tiene' => true,
                'estado' => 'sin-fecha',
                'texto' => 'Sin fecha',
                'claseColor' => 'bg-gray-100 text-gray-800',
                'icono' => 'fa-question',
                'fecha_registro' => $ultimaCedula->fechaRegistro ?
                    $ultimaCedula->fechaRegistro->format('d/m/Y') : 'No registrada'
            ];
        }

        $dias = Carbon::now()->diffInDays(Carbon::parse($ultimaCedula->fechaVencimiento), false);
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
            'ultimo_registro' => [
                'fecha' => Carbon::parse($ultimaCedula->fechaVencimiento)->format('d/m/Y'),
                'dias' => $dias,
                'estaVencido' => $estaVencido,
                'porVencer' => $porVencer,
                'estado' => $ultimaCedula->estado,
                'ci' => $ultimaCedula->ci,
                'expedido' => $ultimaCedula->expedido
            ],
            'claseColor' => $claseColor,
            'icono' => $icono,
            'textoEstado' => $textoEstado,
            'es_ultimo' => true,
            'es_activo' => true
        ];
    }

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
        // Verificar si algún documento está por vencer basado en el último registro ACTIVO
        if ($persona->infoCenvi['tiene'] && isset($persona->infoCenvi['ultimo_registro'])) {
            if ($persona->infoCenvi['ultimo_registro']['porVencer']) return true;
        }

        if ($persona->infoQuechua['tiene'] && isset($persona->infoQuechua['ultimo_registro']) &&
            $persona->infoQuechua['ultimo_registro']['porVencer']) {
            return true;
        }

        if ($persona->infoCedula['tiene'] && isset($persona->infoCedula['ultimo_registro']) &&
            $persona->infoCedula['ultimo_registro']['porVencer']) {
            return true;
        }

        return false;
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

    // Los métodos de envío de mensajes también necesitan actualizarse
    public function enviarATodos()
    {
        // Cargar solo documentos activos aquí también
        $personas = Persona::with([
            'cenvis' => function($query) {
                $query->where('estado', 1);
            },
            'certificados' => function($query) {
                $query->where('estado', 1);
            },
            'cedula' => function($query) {
                $query->where('estado', 1); // SOLO cédulas activas
            }
        ])
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
        $persona = Persona::with([
            'cenvis' => function($query) {
                $query->where('estado', 1);
            },
            'certificados' => function($query) {
                $query->where('estado', 1);
            },
            'cedula' => function($query) {
                $query->where('estado', 1); // SOLO cédulas activas
            }
        ])
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

        // Verificar último CENVI ACTIVO
        $ultimoCenvi = $this->getUltimoCenvi($persona);
        if ($ultimoCenvi && $ultimoCenvi->fecha) {
            $vencimiento = Carbon::parse($ultimoCenvi->fecha)->addYear();
            if (Carbon::now()->greaterThan($vencimiento)) {
                $vencidos[] = "CENVI - Vencido el " . $vencimiento->format('d/m/Y');
            }
        }

        // Verificar último certificado de quechua ACTIVO
        $ultimoQuechua = $this->getUltimoQuechua($persona);
        if ($ultimoQuechua && $ultimoQuechua->fecha) {
            $vencimiento = $this->getFechaVencimientoCertificado($ultimoQuechua);
            if ($vencimiento && Carbon::now()->greaterThan($vencimiento)) {
                $vencidos[] = "Certificado Quechua - Vencido el " . $vencimiento->format('d/m/Y');
            }
        }

        // Verificar cédula ACTIVA
        $ultimaCedula = $this->getUltimaCedulaActiva($persona);
        if ($ultimaCedula && $ultimaCedula->fechaVencimiento) {
            $vencimiento = Carbon::parse($ultimaCedula->fechaVencimiento);
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
}
