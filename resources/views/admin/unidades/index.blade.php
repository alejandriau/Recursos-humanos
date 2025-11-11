@extends('dashboard')

@section('title', 'Unidades Organizacionales')
@section('header-title', 'Unidades Organizacionales')

@section('contenido')
<div x-data="unidadesManager()">
    <!-- Header con Botones y Estadísticas -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Unidades Organizacionales</h1>
                <p class="text-gray-600">Gestiona la estructura organizacional de la empresa</p>
            </div>
            <div class="flex space-x-3">
                <button @click="toggleFilters()"
                        :class="showFilters ? 'bg-blue-100 text-blue-700' : 'bg-white text-gray-700'"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    <span x-text="showFilters ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
                </button>
                <a href="{{ route('unidades.create') }}"
                   class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nueva Unidad
                </a>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @include('admin.unidades.partials.estadisticas-card', [
                'color' => 'blue',
                'icon' => 'building',
                'title' => 'Total Unidades',
                'value' => $unidades->total(),
                'tooltip' => 'Total de unidades en el sistema'
            ])

            @include('admin.unidades.partials.estadisticas-card', [
                'color' => 'green',
                'icon' => 'check-circle',
                'title' => 'Activas',
                'value' => $estadisticas['activas'],
                'tooltip' => 'Unidades activas'
            ])

            @include('admin.unidades.partials.estadisticas-card', [
                'color' => 'red',
                'icon' => 'times-circle',
                'title' => 'Inactivas',
                'value' => $estadisticas['inactivas'],
                'tooltip' => 'Unidades inactivas'
            ])

            @include('admin.unidades.partials.estadisticas-card', [
                'color' => 'purple',
                'icon' => 'crown',
                'title' => 'Con Jefatura',
                'value' => $estadisticas['con_jefatura'],
                'tooltip' => 'Unidades con jefe asignado'
            ])
        </div>
    </div>

    <!-- Filtros -->
    <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-white p-4 rounded-lg shadow mb-6 border border-gray-200">
        <form method="GET" action="{{ route('unidades.index') }}"
              class="grid grid-cols-1 md:grid-cols-5 gap-4" id="filters-form">
            <!-- Búsqueda -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                       placeholder="Nombre, código, sigla..."
                       x-on:input="debouncedSearch()">
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="tipo"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        x-on:change="submitForm()">
                    <option value="">Todos los tipos</option>
                    @foreach(['SECRETARIA', 'SERVICIO', 'DIRECCION', 'UNIDAD', 'AREA','PROGRAMA','PROYECTO'] as $tipo)
                        <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>
                            {{ ucfirst(strtolower($tipo)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="activo"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        x-on:change="submitForm()">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <!-- Ordenamiento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                <select name="orden"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        x-on:change="submitForm()">
                    <option value="denominacion" {{ request('orden') == 'denominacion' ? 'selected' : '' }}>Denominación</option>
                    <option value="tipo" {{ request('orden') == 'tipo' ? 'selected' : '' }}>Tipo</option>
                    <option value="codigo" {{ request('orden') == 'codigo' ? 'selected' : '' }}>Código</option>
                    <option value="created_at" {{ request('orden') == 'created_at' ? 'selected' : '' }}>Fecha creación</option>
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-end space-x-2">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                <a href="{{ route('unidades.index') }}"
                   class="flex-1 px-4 py-2 bg-gray-200 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors text-center">
                    <i class="fas fa-redo mr-2"></i>Limpiar
                </a>
            </div>
        </form>

        <!-- Filtros activos -->
        @if(request()->anyFilled(['buscar', 'tipo', 'activo']))
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <span>Filtros activos:</span>
                    @if(request('buscar'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                        Búsqueda: "{{ request('buscar') }}"
                        <a href="{{ request()->fullUrlWithQuery(['buscar' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif
                    @if(request('tipo'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                        Tipo: {{ request('tipo') }}
                        <a href="{{ request()->fullUrlWithQuery(['tipo' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif
                    @if(request('activo') !== null)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                        Estado: {{ request('activo') ? 'Activos' : 'Inactivos' }}
                        <a href="{{ request()->fullUrlWithQuery(['activo' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif
                </div>
                <div class="text-sm text-gray-500">
                    {{ $unidades->total() }} resultado(s) encontrado(s)
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Tabla de Unidades -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <!-- Header de la tabla -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Lista de Unidades</h3>
                <div class="text-sm text-gray-500">
                    Mostrando {{ $unidades->firstItem() ?? 0 }}-{{ $unidades->lastItem() ?? 0 }} de {{ $unidades->total() }}
                    <a href="{{ route('unidades.arbol') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    | Organigrama →
                </a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('denominacion')">
                            <div class="flex items-center">
                                <span>Unidad</span>
                                @include('admin.unidades.partials.sort-icon', ['field' => 'denominacion'])
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('tipo')">
                            <div class="flex items-center">
                                <span>Tipo</span>
                                @include('admin.unidades.partials.sort-icon', ['field' => 'tipo'])
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('codigo')">
                            <div class="flex items-center">
                                <span>Código</span>
                                @include('admin.unidades.partials.sort-icon', ['field' => 'codigo'])
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jefe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subunidades
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            onclick="sortTable('esActivo')">
                            <div class="flex items-center">
                                <span>Estado</span>
                                @include('admin.unidades.partials.sort-icon', ['field' => 'esActivo'])
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($unidades as $unidad)
                    <tr class="hover:bg-gray-50 transition-colors"
                        x-data="{ showActions: false }"
                        @mouseenter="showActions = true"
                        @mouseleave="showActions = false">
                        @include('admin.unidades.partials.fila-unidad', ['unidad' => $unidad])
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-lg font-medium text-gray-500 mb-1">No se encontraron unidades</p>
                                <p class="text-sm">
                                    @if(request()->anyFilled(['buscar', 'tipo', 'activo']))
                                        Intenta ajustar los filtros de búsqueda
                                    @else
                                        <a href="{{ route('unidades.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            Crea la primera unidad
                                        </a>
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($unidades->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-700">
                    Mostrando {{ $unidades->firstItem() }} a {{ $unidades->lastItem() }} de {{ $unidades->total() }} resultados
                </div>
                <div>
                    {{ $unidades->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal de confirmación para eliminar -->

</div>
@endsection

@push('scripts')
<script>
function unidadesManager() {
    return {
        showFilters: @json(request()->anyFilled(['buscar', 'tipo', 'activo'])),
        loading: false,

        toggleFilters() {
            this.showFilters = !this.showFilters;
        },

        submitForm() {
            document.getElementById('filters-form').submit();
        },

        debouncedSearch: _.debounce(function() {
            this.submitForm();
        }, 500)
    };
}

function sortTable(field) {
    const url = new URL(window.location.href);
    const currentOrder = url.searchParams.get('orden');
    const currentDirection = url.searchParams.get('direccion');

    let newDirection = 'asc';
    if (currentOrder === field && currentDirection === 'asc') {
        newDirection = 'desc';
    }

    url.searchParams.set('orden', field);
    url.searchParams.set('direccion', newDirection);

    window.location.href = url.toString();
}

// Confirmación para acciones destructivas
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
