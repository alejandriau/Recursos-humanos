{{-- resources/views/personas/documentos-vencidos.blade.php --}}
@extends('dashboard')

@section('title', 'Documentos Vencidos')
@section('header-title', 'Documentos Vencidos')

@section('contenido')
<div class="space-y-6">
    <!-- Encabezado con Búsqueda -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Gestión de Documentos Vencidos y Faltantes
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Verifique documentos y envíe notificaciones por WhatsApp
            </p>
        </div>

        <!-- Barra de Búsqueda -->
        <div class="border-t border-gray-200 px-4 py-4 bg-gray-50">
            <form method="GET" action="{{ route('alertas.index') }}" class="flex items-center space-x-4">
                <div class="flex-grow">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text"
                               name="buscar"
                               value="{{ $busqueda ?? '' }}"
                               placeholder="Buscar por nombre o apellido..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Buscar
                </button>
                @if($busqueda ?? false)
                <a href="{{ route('alertas.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Limpiar
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabla de Personas -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Persona
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Teléfono / WhatsApp
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                            CENVI
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                            Cert. Quechua
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                            Cédula
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($personas as $persona)
                    @php
                        // Calcular estado de cada documento en la vista
                        $cenviEstado = 'ok';
                        $cenviMensaje = '';

                        if ($persona->cenvis->isEmpty()) {
                            $cenviEstado = 'falta';
                            $cenviMensaje = 'FALTA CENVI';
                        } else {
                            $cenvi = $persona->cenvis->first();
                            if ($cenvi->fecha) {
                                $vencimientoCenvi = \Carbon\Carbon::parse($cenvi->fecha)->addYear();
                                if (\Carbon\Carbon::now()->greaterThan($vencimientoCenvi)) {
                                    $cenviEstado = 'vencido';
                                    $cenviMensaje = 'CENVI VENCIDO desde ' . $vencimientoCenvi->format('d/m/Y');
                                }
                            }
                        }

                        // Certificado Quechua
                        $quechuaEstado = 'ok';
                        $quechuaMensaje = '';
                        $certificadoQuechua = $persona->certificados->first(function($cert) {
                            return stripos($cert->nombre, 'quechua') !== false ||
                                   (!empty($cert->categoria) && stripos($cert->categoria, 'quechua') !== false);
                        });

                        if (!$certificadoQuechua) {
                            $quechuaEstado = 'falta';
                            $quechuaMensaje = 'FALTA Certificado Quechua';
                        } elseif ($certificadoQuechua->fecha) {
                            $vencimientoQuechua = $certificadoQuechua->fecha_vencimiento ?
                                \Carbon\Carbon::parse($certificadoQuechua->fecha_vencimiento) :
                                \Carbon\Carbon::parse($certificadoQuechua->fecha)->addYears(3);

                            if (\Carbon\Carbon::now()->greaterThan($vencimientoQuechua)) {
                                $quechuaEstado = 'vencido';
                                $quechuaMensaje = 'Certificado Quechua VENCIDO desde ' . $vencimientoQuechua->format('d/m/Y');
                            }
                        }

                        // Cédula
                        $cedulaEstado = 'ok';
                        $cedulaMensaje = '';

                        if (!$persona->cedula) {
                            $cedulaEstado = 'falta';
                            $cedulaMensaje = 'FALTA Cédula Identidad';
                        } elseif ($persona->cedula->fechaVencimiento) {
                            if (\Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($persona->cedula->fechaVencimiento))) {
                                $cedulaEstado = 'vencido';
                                $cedulaMensaje = 'Cédula Identidad VENCIDA desde ' . \Carbon\Carbon::parse($persona->cedula->fechaVencimiento)->format('d/m/Y');
                            }
                        }

                        // Determinar color de fila
                        $filaColor = '';
                        if ($cenviEstado == 'vencido' || $quechuaEstado == 'vencido' || $cedulaEstado == 'vencido') {
                            $filaColor = 'bg-red-50';
                        } elseif ($cenviEstado == 'falta' || $quechuaEstado == 'falta' || $cedulaEstado == 'falta') {
                            $filaColor = 'bg-orange-50';
                        }

                        // Generar enlace de WhatsApp con mensaje detallado
                        $whatsappLink = '';
                        if ($persona->telefono) {
                            $telefonoLimpio = preg_replace('/\D/', '', $persona->telefono);
                            $numeroWhatsapp = strlen($telefonoLimpio) == 8 ? '591' . $telefonoLimpio : $telefonoLimpio;

                            $hora = now()->hour;
                            if ($hora >= 5 && $hora < 12) {
                                $saludo = 'Buen día';
                            } elseif ($hora >= 12 && $hora < 18) {
                                $saludo = 'Buenas tardes';
                            } else {
                                $saludo = 'Buenas noches';
                            }

                            // Construir mensaje personalizado
                            $problemas = [];
                            $documentosFaltantes = [];
                            $documentosVencidos = [];
                            
                            if ($cenviEstado == 'vencido') {
                                $problemas[] = $cenviMensaje;
                                $documentosVencidos[] = 'Certificado de No Violencia (CENVI)';
                            }
                            if ($cenviEstado == 'falta') {
                                $problemas[] = $cenviMensaje;
                                $documentosFaltantes[] = 'Certificado de No Violencia (CENVI)';
                            }
                            if ($quechuaEstado == 'vencido') {
                                $problemas[] = $quechuaMensaje;
                                $documentosVencidos[] = 'Certificado de Idioma Quechua';
                            }
                            if ($quechuaEstado == 'falta') {
                                $problemas[] = $quechuaMensaje;
                                $documentosFaltantes[] = 'Certificado de Idioma Quechua';
                            }
                            if ($cedulaEstado == 'vencido') {
                                $problemas[] = $cedulaMensaje;
                                $documentosVencidos[] = 'Cédula de Identidad';
                            }
                            if ($cedulaEstado == 'falta') {
                                $problemas[] = $cedulaMensaje;
                                $documentosFaltantes[] = 'Cédula de Identidad';
                            }

                            $mensaje = "{$saludo} {$persona->nombre} {$persona->apellidoPat} {$persona->apellidoMat}:\n\n";
                            $mensaje .= "Le escribe la *Unidad de Gestión de Recursos Humanos (UGRH)* del ";
                            $mensaje .= "*Gobierno Autónomo Departamental de Cochabamba (GADC)*.\n\n";
                            
                            $mensaje .= "*ALERTA DE DOCUMENTACIÓN PERSONAL*\n\n";
                            
                            if (!empty($problemas)) {
                                foreach ($problemas as $problema) {
                                    $mensaje .= "🔴 {$problema}\n";
                                }
                                $mensaje .= "\n";
                            }

                            $mensaje .= "📋 *INFORMACIÓN DETALLADA:*\n\n";
                            
                            if (!empty($documentosVencidos)) {
                                $mensaje .= "⚠️ *DOCUMENTOS POR RENOVAR:*\n";
                                foreach ($documentosVencidos as $doc) {
                                    $mensaje .= "• {$doc}\n";
                                }
                                $mensaje .= "\n";
                            }
                            
                            if (!empty($documentosFaltantes)) {
                                $mensaje .= "📄 *DOCUMENTOS REQUERIDOS:*\n";
                                foreach ($documentosFaltantes as $doc) {
                                    $mensaje .= "• {$doc}\n";
                                }
                                $mensaje .= "\n";
                            }

                            $mensaje .= "📅 *PLAZO DE ENTREGA:*\n";
                            $mensaje .= "La documentación solicitada debe ser presentada *hasta el Martes 20 de enero de 2026.*\n\n";

                            $mensaje .= "📌 *PROCEDIMIENTO:*\n";
                            $mensaje .= "1. Renovar/obtener los documentos indicados\n";
                            $mensaje .= "2. Presentar copias físicas en la Unidad de Gestión de Recursos Humanos\n";
                            $mensaje .= "3. La documentación será registrada y archivada en su file personal\n\n";

                            $mensaje .= "La documentación es obligatoria para mantener su file personal actualizado y regular, conforme a los procedimientos administrativos internos.\n\n";
                            
                            $mensaje .= "Agradecemos su pronta atención a esta solicitud y quedamos a su disposición para cualquier consulta o coordinación relacionada.\n\n";
                            
                            $mensaje .= "Saludos cordiales,\n";
                            $mensaje .= "Unidad de Gestión de Recursos Humanos\n";
                            $mensaje .= "Gobierno Autónomo Departamental de Cochabamba (GADC)";

                            $whatsappLink = "https://wa.me/{$numeroWhatsapp}?text=" . urlencode($mensaje);
                        }
                    @endphp

                    <tr class="hover:bg-gray-50 {{ $filaColor }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $persona->nombre }} {{ $persona->apellidoPat }}
                                        @if($persona->apellidoMat)
                                        {{ $persona->apellidoMat }}
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        CI: {{ $persona->ci }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- TELÉFONO Y WHATSAPP -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($persona->telefono)
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-900">{{ $persona->telefono }}</span>
                                @if($whatsappLink)
                                <a href="{{ $whatsappLink }}"
                                   target="_blank"
                                   class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                   title="Enviar WhatsApp">
                                    <i class="fab fa-whatsapp mr-1"></i> Enviar
                                </a>
                                @endif
                            </div>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Sin teléfono
                            </span>
                            @endif
                        </td>

                        <!-- CELDA CENVI -->
                        <td class="px-6 py-4 text-center">
                            @if($cenviEstado == 'falta')
                                <div class="bg-red-100 text-red-800 rounded-md p-2">
                                    <div class="text-xs font-medium">
                                        FALTA
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                </div>
                            @elseif($cenviEstado == 'vencido')
                                <div class="bg-red-100 text-red-800 rounded-md p-2">
                                    <div class="font-medium">
                                        {{ $persona->cenvis->first()->fecha->format('d/m/Y') ?? '' }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-exclamation-triangle"></i> Vencido
                                    </div>
                                </div>
                            @elseif($persona->cenvis->isNotEmpty())
                                <div class="bg-green-100 text-green-800 rounded-md p-2">
                                    <div class="font-medium">
                                        {{ $persona->cenvis->first()->fecha->format('d/m/Y') ?? '' }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-check"></i> OK
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">N/A</span>
                            @endif
                        </td>

                        <!-- CELDA CERTIFICADO QUECHUA -->
                        <td class="px-6 py-4 text-center">
                            @if($quechuaEstado == 'falta')
                                <div class="bg-red-100 text-red-800 rounded-md p-2">
                                    <div class="text-xs font-medium">
                                        FALTA
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                </div>
                            @elseif($quechuaEstado == 'vencido')
                                <div class="bg-red-100 text-red-800 rounded-md p-2">
                                    <div class="font-medium">
                                        {{ $certificadoQuechua->fecha->format('d/m/Y') ?? '' }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-exclamation-triangle"></i> Vencido
                                    </div>
                                </div>
                            @elseif($certificadoQuechua)
                                <div class="bg-green-100 text-green-800 rounded-md p-2">
                                    <div class="font-medium">
                                        {{ $certificadoQuechua->fecha->format('d/m/Y') ?? 'Registrado' }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-check"></i> OK
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">N/A</span>
                            @endif
                        </td>

                        <!-- CELDA CÉDULA -->
                        <td class="px-6 py-4 text-center">
                            @if($cedulaEstado == 'falta')
                                <div class="bg-red-100 text-red-800 rounded-md p-2">
                                    <div class="text-xs font-medium">
                                        FALTA
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                </div>
                            @elseif($cedulaEstado == 'vencido')
                                <div class="bg-red-100 text-red-800 rounded-md p-2">
                                    <div class="font-medium">
                                        {{ \Carbon\Carbon::parse($persona->cedula->fechaVencimiento)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-exclamation-triangle"></i> Vencida
                                    </div>
                                </div>
                            @elseif($persona->cedula)
                                <div class="bg-green-100 text-green-800 rounded-md p-2">
                                    <div class="font-medium">
                                        {{ $persona->cedula->fechaVencimiento ? \Carbon\Carbon::parse($persona->cedula->fechaVencimiento)->format('d/m/Y') : 'Registrada' }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas fa-check"></i> OK
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">N/A</span>
                            @endif
                        </td>

                        <!-- ESTADO GENERAL -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cenviEstado == 'vencido' || $quechuaEstado == 'vencido' || $cedulaEstado == 'vencido')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Vencidos
                                </span>
                            @elseif($cenviEstado == 'falta' || $quechuaEstado == 'falta' || $cedulaEstado == 'falta')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Faltan
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Al Día
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            @if($busqueda ?? false)
                                No se encontraron personas para "{{ $busqueda }}"
                            @else
                                No hay personas registradas
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Botón para Generar WhatsApps para Todos -->
    @if($personas->count() > 0)
    <div class="bg-white shadow rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Acción Masiva</h4>
        <button onclick="abrirWhatsAppsTodos()"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <i class="fab fa-whatsapp mr-2"></i>Abrir WhatsApp para Todos
        </button>
        <p class="text-xs text-gray-500 mt-2">
            Se abrirán pestañas separadas para cada persona con teléfono registrado
        </p>
    </div>
    @endif
</div>

<!-- Script para abrir todos los WhatsApps -->
<script>
    function abrirWhatsAppsTodos() {
        if (!confirm('¿Abrir WhatsApp para todas las personas con teléfono registrado?')) {
            return;
        }

        // Obtener todos los enlaces de WhatsApp de la tabla
        const whatsappLinks = document.querySelectorAll('a[href*="wa.me"]');

        if (whatsappLinks.length === 0) {
            alert('No hay personas con teléfono registrado');
            return;
        }

        // Abrir cada enlace en una nueva pestaña
        whatsappLinks.forEach(link => {
            window.open(link.href, '_blank');
        });

        alert(`Se abrieron ${whatsappLinks.length} ventanas de WhatsApp`);
    }

    // También puedes agregar un botón para copiar todos los números
    function copiarNumerosTodos() {
        const telefonos = [];
        document.querySelectorAll('td:nth-child(3) span.text-sm').forEach(span => {
            if (!span.textContent.includes('Sin teléfono')) {
                telefonos.push(span.textContent.trim());
            }
        });

        if (telefonos.length === 0) {
            alert('No hay teléfonos para copiar');
            return;
        }

        const numerosTexto = telefonos.join('\n');
        navigator.clipboard.writeText(numerosTexto)
            .then(() => alert(`${telefonos.length} números copiados al portapapeles`))
            .catch(err => alert('Error al copiar: ' + err));
    }
</script>
@endsection