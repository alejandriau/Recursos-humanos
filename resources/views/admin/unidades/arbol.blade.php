@extends('dashboard')

@section('title', 'Organigrama')
@section('header-title', 'Organigrama de la Empresa')

@section('contenido')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Estructura Organizacional
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Vista completa del organigrama de la empresa
                </p>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()"
                        class="no-print inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
                <a href="{{ route('unidades.index') }}"
                   class="no-print inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-list mr-2"></i>Ver Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="no-print px-4 py-4 bg-gray-50 border-b border-gray-200">
        <form method="GET" action="{{ route('unidades.arbol') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label for="unidad_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Buscar Unidad:
                </label>
                <div class="flex gap-2">
                    <select name="unidad_id" id="unidad_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Todas las unidades (vista completa)</option>
                        @foreach($todasUnidades as $unidad)
                            <option value="{{ $unidad->id }}"
                                    {{ $filtroUnidad == $unidad->id ? 'selected' : '' }}>
                                {{ $unidad->denominacion }}
                                @if($unidad->codigo)
                                    ({{ $unidad->codigo }})
                                @endif
                                - {{ $unidad->tipo }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                </div>
            </div>

            @if($filtroUnidad && $unidadSeleccionada)
            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-filter mr-1"></i>
                    Vista de: {{ $unidadSeleccionada->denominacion }}
                    <a href="{{ route('unidades.arbol') }}" class="ml-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            </div>
            @endif
        </form>

        @if($filtroUnidad && $unidadSeleccionada)
        <div class="mt-3 p-3 bg-white border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Unidad seleccionada:</h4>
                    <p class="text-sm text-gray-600">
                        <strong>{{ $unidadSeleccionada->denominacion }}</strong>
                        <span class="text-xs text-gray-500 ml-2">{{ $unidadSeleccionada->tipo }}</span>
                        @if($unidadSeleccionada->codigo)
                        <span class="text-xs text-gray-500 ml-2">({{ $unidadSeleccionada->codigo }})</span>
                        @endif
                    </p>
                    @if($unidadSeleccionada->jefe && $unidadSeleccionada->jefe->personaActual)
                    <p class="text-xs text-gray-500 mt-1">
                        Jefe: {{ $unidadSeleccionada->jefe->personaActual->nombre }}
                        {{ $unidadSeleccionada->jefe->personaActual->paterno }}
                        {{ $unidadSeleccionada->jefe->personaActual->materno ?? '' }}
                    </p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">
                        <span class="inline-flex items-center">
                            <i class="fas fa-sitemap mr-1"></i>
                            {{ $unidadSeleccionada->hijos->count() }} subunidades
                        </span>
                    </p>
                    <p class="text-xs text-gray-500">
                        <span class="inline-flex items-center">
                            <i class="fas fa-users mr-1"></i>
                            {{ $unidadSeleccionada->puestos->count() }} puestos
                        </span>
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="px-4 py-5 sm:p-6">
        <!-- Leyenda -->
        <div class="no-print mb-6 bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-center mb-2">
                <h4 class="text-sm font-medium text-gray-900">Leyenda de Tipos:</h4>
                @if($filtroUnidad && $unidadSeleccionada)
                <span class="text-xs text-gray-500">
                    Mostrando estructura de: <strong>{{ $unidadSeleccionada->denominacion }}</strong>
                </span>
                @else
                <span class="text-xs text-gray-500">
                    Vista completa del organigrama
                </span>
                @endif
            </div>
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-600 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Dirección</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Gerencia</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Subdirección</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Coordinación</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Departamento</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-pink-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Área</span>
                </div>
            </div>
        </div>

        <div class="organigrama-container overflow-x-auto">
            @if($raices->count() > 0)
                <!-- Mostrar breadcrumb si estamos en una vista filtrada -->
                @if($filtroUnidad && $unidadSeleccionada && $unidadSeleccionada->hijos->count() > 0)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li>
                                <a href="{{ route('unidades.arbol') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-home"></i>
                                </a>
                            </li>
                            <li class="flex items-center">
                                <span class="text-gray-400 mx-2">/</span>
                                <span class="text-gray-600 text-sm">Subunidades de:</span>
                            </li>
                            <li>
                                <span class="text-gray-900 font-medium text-sm">{{ $unidadSeleccionada->denominacion }}</span>
                            </li>
                        </ol>
                    </nav>
                </div>
                @endif

                @foreach($raices as $unidad)
                    @include('admin.unidades.partials.nodo-unidad', [
                        'unidad' => $unidad,
                        'nivel' => 0,
                        'unidadSeleccionada' => $unidadSeleccionada
                    ])
                @endforeach
            @else
                <div class="text-center py-12">
                    <i class="fas fa-sitemap text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">No se encontraron unidades</p>
                    @if($filtroUnidad)
                        <p class="text-gray-400 text-sm mb-4">
                            La unidad seleccionada no tiene subunidades.
                        </p>
                        <a href="{{ route('unidades.arbol') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al organigrama completo
                        </a>
                    @else
                        <p class="text-gray-400 text-sm">
                            No hay unidades organizacionales activas en el sistema.
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.organigrama-container {
    min-width: 800px;
}

.nodo-unidad {
    transition: all 0.3s ease;
    position: relative;
}

.nodo-unidad:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.conexion {
    position: relative;
}

.conexion::before {
    content: '';
    position: absolute;
    left: -1rem;
    top: 50%;
    width: 1rem;
    height: 2px;
    background-color: #d1d5db;
}

.conexion-hijos::before {
    content: '';
    position: absolute;
    left: 50%;
    top: -1rem;
    width: 2px;
    height: 1rem;
    background-color: #d1d5db;
}

/* Destacar la unidad seleccionada */
.nodo-unidad.unidad-seleccionada {
    border: 2px solid #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

@media print {
    .no-print {
        display: none;
    }

    .organigrama-container {
        overflow: visible;
        min-width: auto;
    }

    .nodo-unidad {
        break-inside: avoid;
    }
}
</style>

<script>
// Mejorar la experiencia del dropdown de búsqueda
document.addEventListener('DOMContentLoaded', function() {
    const selectUnidad = document.getElementById('unidad_id');
    if (selectUnidad) {
        // Agregar búsqueda en tiempo real
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Buscar unidad...';
        searchInput.className = 'w-full px-3 py-2 border border-gray-300 rounded-md mb-2';
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const options = selectUnidad.options;

            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const text = option.text.toLowerCase();
                option.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });

        // Insertar el campo de búsqueda antes del select
        selectUnidad.parentNode.insertBefore(searchInput, selectUnidad);
    }
});
</script>
@endsection
