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
                            $cedulaMensaje = 'FALTA Cédula';
                        } elseif ($persona->cedula->fechaVencimiento) {
                            if (\Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($persona->cedula->fechaVencimiento))) {
                                $cedulaEstado = 'vencido';
                                $cedulaMensaje = 'Cédula VENCIDA desde ' . \Carbon\Carbon::parse($persona->cedula->fechaVencimiento)->format('d/m/Y');
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
                            if ($cenviEstado == 'vencido') $problemas[] = $cenviMensaje;
                            if ($cenviEstado == 'falta') $problemas[] = $cenviMensaje;
                            if ($quechuaEstado == 'vencido') $problemas[] = $quechuaMensaje;
                            if ($quechuaEstado == 'falta') $problemas[] = $quechuaMensaje;
                            if ($cedulaEstado == 'vencido') $problemas[] = $cedulaMensaje;
                            if ($cedulaEstado == 'falta') $problemas[] = $cedulaMensaje;

                            $mensaje = "{$saludo} {$persona->nombre} {$persona->apellidoPat}:\n\n";
                            $mensaje .= "Le escribe la *Unidad de Gestión de Recursos Humanos (UGRH)* del ";
                            $mensaje .= "*Gobierno Autónomo Departamental de Cochabamba (GADC)*.\n\n";

                            if (!empty($problemas)) {
                                $mensaje .= "*ALERTA DE DOCUMENTOS:*\n";
                                foreach ($problemas as $problema) {
                                    $mensaje .= "❌ {$problema}\n";
                                }
                                $mensaje .= "\n";
                            }

                            $mensaje .= "Por favor, regularice su documentación personal lo antes posible.\n\n";
                            $mensaje .= "Documentación requerida:\n";
                            if ($cenviEstado == 'falta') $mensaje .= "• Certificado de Años de Servicio (CENVI)\n";
                            if ($quechuaEstado == 'falta') $mensaje .= "• Certificado de Idioma Quechua\n";
                            if ($cedulaEstado == 'falta') $mensaje .= "• Cédula de Identidad\n";

                            if ($cenviEstado == 'vencido' || $quechuaEstado == 'vencido' || $cedulaEstado == 'vencido') {
                                $mensaje .= "\n*Los documentos vencidos deben ser renovados inmediatamente.*\n";
                            }

                            $mensaje .= "\nAgradecemos su colaboración y quedamos atentos para coordinar.\n\n";
                            $mensaje .= "Saludos cordiales,\n";
                            $mensaje .= "Unidad de Gestión de Recursos Humanos\n";
                            $mensaje .= "GADC";

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

{{-- resources/views/personas/documentos-vencidos.blade.php --}}
@extends('dashboard')

@section('title', 'Documentos Vencidos')
@section('header-title', 'Documentos Vencidos')

@section('contenido')
<div class="space-y-6">
    <!-- Encabezado con Acciones y Búsqueda -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Gestión de Documentos Vencidos
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Personas con documentos vencidos o faltantes.
                </p>
            </div>
            <div class="flex space-x-3">
                <form action="{{ route('alertas.enviar-todos') }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('¿Enviar WhatsApp a TODAS las personas con documentos vencidos o faltantes?')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fab fa-whatsapp mr-2"></i>Enviar WhatsApp a Todos
                    </button>
                </form>
                <a href="{{ route('personas.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
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
                               value="{{ $busqueda }}"
                               placeholder="Buscar por nombre o apellido..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Buscar
                </button>
                @if($busqueda)
                <a href="{{ route('alertas.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Limpiar
                </a>
                @endif
            </form>

            <!-- Filtro rápido -->
            @if($busqueda)
            <div class="mt-2 text-sm text-gray-600">
                Resultados para: "<span class="font-semibold">{{ $busqueda }}</span>"
                <span class="ml-2">({{ $personas->count() }} personas)</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Resumen Mejorado -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Vencidos</div>
                    <div class="text-2xl font-bold text-red-600">{{ $totalVencidos }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-orange-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Faltan</div>
                    <div class="text-2xl font-bold text-orange-600">{{ $totalFaltan }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Por Vencer</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $totalPorVencer }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Al Día</div>
                    <div class="text-2xl font-bold text-green-600">{{ $totalAlDia }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-paper-plane text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">A Notificar</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $totalNotificar }}</div>
                </div>
            </div>
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
                            Teléfono
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
                            Estado General
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($personas as $persona)
                    <tr class="hover:bg-gray-50
                        {{ $persona->tieneVencidos ? 'bg-red-50' : '' }}
                        {{ $persona->faltanRegistros && !$persona->tieneVencidos ? 'bg-orange-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($persona->foto)
                                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $persona->foto) }}" alt="">
                                    @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    @endif
                                </div>
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($persona->telefono)
                            <div class="text-sm text-gray-900">{{ $persona->telefono }}</div>
                            <div class="text-xs text-gray-500">
                                <i class="fab fa-whatsapp text-green-500"></i> Disponible
                            </div>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Sin teléfono
                            </span>
                            @endif
                        </td>

                        <!-- CELDA CENVI -->
                        <td class="px-6 py-4 text-center">
                            @if(!$persona->infoCenvi['tiene'])
                                <div class="{{ $persona->infoCenvi['claseColor'] }} rounded-md p-2">
                                    <div class="text-xs font-medium">
                                        {{ $persona->infoCenvi['texto'] }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas {{ $persona->infoCenvi['icono'] }}"></i>
                                    </div>
                                </div>
                            @else
                                <div class="{{ $persona->infoCenvi['claseColor'] }} rounded-md p-2">
                                    <div class="font-medium">
                                        @if(isset($persona->infoCenvi['datos']))
                                            {{ $persona->infoCenvi['datos'][0]['fecha'] ?? 'N/A' }}
                                        @endif
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas {{ $persona->infoCenvi['icono'] }}"></i>
                                        {{ $persona->infoCenvi['textoEstado'] }}
                                    </div>
                                </div>
                            @endif
                        </td>

                        <!-- CELDA CERTIFICADO QUECHUA -->
                        <td class="px-6 py-4 text-center">
                            @if(!$persona->infoQuechua['tiene'])
                                <div class="{{ $persona->infoQuechua['claseColor'] }} rounded-md p-2">
                                    <div class="text-xs font-medium">
                                        {{ $persona->infoQuechua['texto'] }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas {{ $persona->infoQuechua['icono'] }}"></i>
                                    </div>
                                </div>
                            @else
                                <div class="{{ $persona->infoQuechua['claseColor'] }} rounded-md p-2">
                                    <div class="font-medium">
                                        {{ $persona->infoQuechua['fecha'] }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas {{ $persona->infoQuechua['icono'] }}"></i>
                                        {{ $persona->infoQuechua['textoEstado'] }}
                                    </div>
                                </div>
                            @endif
                        </td>

                        <!-- CELDA CÉDULA -->
                        <td class="px-6 py-4 text-center">
                            @if(!$persona->infoCedula['tiene'])
                                <div class="{{ $persona->infoCedula['claseColor'] }} rounded-md p-2">
                                    <div class="text-xs font-medium">
                                        {{ $persona->infoCedula['texto'] }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas {{ $persona->infoCedula['icono'] }}"></i>
                                    </div>
                                </div>
                            @else
                                <div class="{{ $persona->infoCedula['claseColor'] }} rounded-md p-2">
                                    <div class="font-medium">
                                        {{ $persona->infoCedula['fecha'] }}
                                    </div>
                                    <div class="text-xs">
                                        <i class="fas {{ $persona->infoCedula['icono'] }}"></i>
                                        {{ $persona->infoCedula['textoEstado'] }}
                                    </div>
                                </div>
                            @endif
                        </td>

                        <!-- ESTADO GENERAL -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($persona->tieneVencidos)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Vencidos
                                </span>
                                <div class="text-xs text-red-600 mt-1">
                                    {{ count($persona->documentosVencidos) }} documento(s)
                                </div>
                            @elseif($persona->faltanRegistros)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Faltan
                                </span>
                                <div class="text-xs text-orange-600 mt-1">
                                    Registros incompletos
                                </div>
                            @elseif($persona->algunoPorVencer)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Por Vencer
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Al Día
                                </span>
                            @endif
                        </td>

                        <!-- ACCIONES -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @if($persona->necesitaNotificacion && $persona->telefono)
                                    <form action="{{ route('alertas.enviar-individual', $persona->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('¿Enviar WhatsApp a {{ $persona->nombre }}?')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('personas.show', $persona->id) }}"
                                   class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-eye mr-1"></i> Ver
                                </a>

                                <a href="{{ route('personas.edit', $persona->id) }}"
                                   class="inline-flex items-center px-3 py-1 border border-blue-300 text-xs font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </a>
                            </div>

                            @if($persona->necesitaNotificacion && !$persona->telefono)
                                <div class="mt-2">
                                    <span class="text-xs text-yellow-600">
                                        <i class="fas fa-exclamation-circle"></i> Sin teléfono
                                    </span>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            @if($busqueda)
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

        <!-- Mostrar mensaje si no hay resultados -->
        @if($personas->isEmpty() && !$busqueda)
        <div class="text-center py-8">
            <i class="fas fa-folder-open text-gray-400 text-4xl mb-3"></i>
            <p class="text-gray-500">No hay personas con documentos para mostrar</p>
        </div>
        @endif
    </div>
</div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif
@endsection
