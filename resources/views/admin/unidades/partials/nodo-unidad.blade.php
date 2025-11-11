@php
    // Determinar colores según el tipo de unidad o nivel
    $coloresTipo = [
        'DIRECCION' => 'bg-blue-600 border-blue-700',
        'GERENCIA' => 'bg-green-500 border-green-600',
        'SUBDIRECCION' => 'bg-yellow-500 border-yellow-600',
        'COORDINACION' => 'bg-purple-500 border-purple-600',
        'DEPARTAMENTO' => 'bg-indigo-500 border-indigo-600',
        'AREA' => 'bg-pink-500 border-pink-600',
        'SECRETARIA' => 'bg-teal-500 border-teal-600',
        'SERVICIO' => 'bg-orange-500 border-orange-600',
        'UNIDAD' => 'bg-red-500 border-red-600',
        'default' => 'bg-gray-500 border-gray-600'
    ];

    $tipoUnidad = $unidad->tipo ?? 'default';
    $colorClase = $coloresTipo[$tipoUnidad] ?? $coloresTipo['default'];

    // Tamaño según nivel de profundidad
    $tamanos = [
        0 => 'max-w-lg',
        1 => 'max-w-md',
        2 => 'max-w-sm',
        3 => 'max-w-xs',
        4 => 'max-w-xs text-sm'
    ];
    $tamano = $tamanos[min($nivel, 4)] ?? 'max-w-xs text-sm';

    // Verificar si esta unidad es la seleccionada (cuando se muestra sola sin hijos)
    $esUnidadSeleccionada = isset($unidadSeleccionada) &&
                           $unidadSeleccionada &&
                           $unidadSeleccionada->id === $unidad->id &&
                           $unidadSeleccionada->hijos->count() === 0;
    $claseDestacada = $esUnidadSeleccionada ? 'unidad-seleccionada' : '';
@endphp

<div class="conexion mb-4">
    <div class="nodo-unidad {{ $tamano }} {{ $claseDestacada }} bg-white border border-gray-200 rounded-lg shadow-sm p-3 mx-auto">
        <!-- Header de la Unidad -->
        <div class="text-center mb-3">
            @if($esUnidadSeleccionada)
            <div class="mb-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-star mr-1"></i>Unidad Seleccionada
                </span>
            </div>
            @endif

            <h4 class="text-sm font-semibold text-gray-900 mb-1 leading-tight">
                <a href="{{ route('unidades.show', $unidad) }}" class="hover:text-blue-600 inline-block">
                    {{ $unidad->denominacion }}
                </a>
            </h4>

            <div class="flex flex-wrap justify-center gap-1 mb-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                    @switch($unidad->tipo)
                        @case('SECRETARIA') bg-purple-100 text-purple-800 @break
                        @case('SERVICIO') bg-indigo-100 text-indigo-800 @break
                        @case('DIRECCION') bg-blue-100 text-blue-800 @break
                        @case('GERENCIA') bg-green-100 text-green-800 @break
                        @case('UNIDAD') bg-green-100 text-green-800 @break
                        @case('AREA') bg-yellow-100 text-yellow-800 @break
                        @case('DEPARTAMENTO') bg-red-100 text-red-800 @break
                        @case('COORDINACION') bg-pink-100 text-pink-800 @break
                        @default bg-gray-100 text-gray-800
                    @endswitch">
                    {{ $unidad->tipo }}
                </span>
                @if($unidad->codigo)
                <span class="text-xs text-gray-500 bg-gray-100 px-1 rounded">{{ $unidad->codigo }}</span>
                @endif
            </div>
        </div>

        <!-- Jefe de la Unidad -->
        @if($unidad->jefe && $unidad->jefe->personaActual)
        <div class="bg-gradient-to-r {{ $colorClase }} rounded p-2 mb-2 text-white">
            <div class="flex items-center justify-center">
                <div class="flex-shrink-0 h-5 w-5 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-2">
                    <i class="fas fa-user-tie text-white text-xs"></i>
                </div>
                <div class="text-center">
                    <p class="text-xs font-bold truncate">
                        {{ $unidad->jefe->personaActual->nombre }}
                        {{ $unidad->jefe->personaActual->paterno }}
                        {{ $unidad->jefe->personaActual->materno ?? '' }}
                    </p>
                    <p class="text-xs opacity-90">{{ $unidad->jefe->denominacion }}</p>
                    <p class="text-xs opacity-75">Jefe de {{ $unidad->tipo }}</p>
                </div>
            </div>
        </div>
        @elseif($unidad->jefe)
        <div class="bg-gray-100 rounded p-2 mb-2">
            <div class="flex items-center justify-center">
                <div class="flex-shrink-0 h-5 w-5 bg-gray-400 rounded-full flex items-center justify-center mr-2">
                    <i class="fas fa-user-tie text-white text-xs"></i>
                </div>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-700">{{ $unidad->jefe->denominacion }}</p>
                    <p class="text-xs text-gray-500">Jefe de {{ $unidad->tipo }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-gray-100 rounded p-2 mb-2 text-center">
            <p class="text-xs text-gray-500 italic">Sin jefe asignado</p>
        </div>
        @endif

        <!-- Información adicional -->
        <div class="flex justify-between items-center text-xs text-gray-500 mt-2">
            <span class="flex items-center" title="Subunidades">
                <i class="fas fa-sitemap mr-1"></i>
                {{ $unidad->hijos->count() }}
            </span>
            <span class="flex items-center" title="Total de puestos">
                <i class="fas fa-users mr-1"></i>
                {{ $unidad->puestos->count() }}
            </span>
            @if($unidad->esActivo)
            <span class="flex items-center text-green-600" title="Activo">
                <i class="fas fa-circle mr-1 text-xs"></i>
                Activo
            </span>
            @else
            <span class="flex items-center text-red-600" title="Inactivo">
                <i class="fas fa-circle mr-1 text-xs"></i>
                Inactivo
            </span>
            @endif
        </div>
    </div>

    <!-- Subunidades -->
    @if($unidad->hijos->count() > 0)
    <div class="mt-4 relative">
        <div class="conexion-hijos absolute left-1/2 transform -translate-x-1/2 w-1 bg-gray-300" style="top: -15px; height: 15px;"></div>
        <div class="grid grid-cols-1 md:grid-cols-{{ min($unidad->hijos->count(), 4) }} gap-4 justify-center mt-2">
            @foreach($unidad->hijos as $hijo)
                <div class="flex justify-center">
                    @include('admin.unidades.partials.nodo-unidad', [
                        'unidad' => $hijo,
                        'nivel' => $nivel + 1,
                        'unidadSeleccionada' => $unidadSeleccionada
                    ])
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
