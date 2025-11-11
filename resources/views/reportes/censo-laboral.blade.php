@extends('dashboard')

@section('title', 'Censo Laboral')
@section('header-title', 'Censo Laboral')

@section('contenido')
<div class="space-y-6">
    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros de Búsqueda</h3>
        <form method="GET" action="{{ route('reportes.censo-laboral') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Unidad -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unidad Organizacional</label>
                <select name="unidad" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas las unidades</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ request('unidad') == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->denominacion }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <!-- Género -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                <select name="genero" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="M" {{ request('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ request('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                <a href="{{ route('reportes.censo-laboral') }}" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 text-center">
                    <i class="fas fa-redo mr-2"></i>Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Registros</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $personas->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Activos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $personas->where('estado', 1)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Inactivos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $personas->where('estado', 0)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Resultados -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Lista de Personal</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('reportes.dashboard-pdfs') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 inline-flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                    </a>



                    <form method="GET" action="{{ route('reportes.censo-laboral') }}">
                        <input type="hidden" name="exportar" value="excel">
                        <input type="hidden" name="unidad" value="{{ request('unidad') }}">
                        <input type="hidden" name="estado" value="{{ request('estado') }}">
                        <input type="hidden" name="genero" value="{{ request('genero') }}">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sexo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Edad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puesto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($personas as $persona)
                    @php
                        $puestoActual = $persona->historialActivo->puesto ?? null;
                        $unidadActual = $puestoActual->unidadOrganizacional ?? null;
                        $fechaIngresoFormateada = $persona->fechaIngreso ? (\Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y')) : 'N/A';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $persona->ci }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">{{ $persona->nombre_completo }}</div>
                            <div class="text-gray-500 text-xs">Ingreso: {{ $fechaIngresoFormateada }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $persona->sexo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $persona->edad ? $persona->edad . ' años' : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $puestoActual ? $puestoActual->denominacion : 'Sin puesto' }}
                            @if($puestoActual && $puestoActual->nivelJerarquico)
                                <div class="text-xs text-gray-500">{{ $puestoActual->nivelJerarquico }}</div>
                            @endif
                            @if($persona->historialActivo && $persona->historialActivo->tipo_contrato)
                                <div class="text-xs text-blue-500">{{ ucfirst(str_replace('_', ' ', $persona->historialActivo->tipo_contrato)) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $unidadActual ? $unidadActual->denominacion : 'Sin unidad' }}
                            @if($unidadActual && $unidadActual->sigla)
                                <div class="text-xs text-gray-500">{{ $unidadActual->sigla }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($persona->estado)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                                @if($persona->historialActivo)
                                    <div class="text-xs text-green-600 mt-1">
                                        Desde: {{ \Carbon\Carbon::parse($persona->historialActivo->fecha_inicio)->format('d/m/Y') }}
                                    </div>
                                @endif
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-3xl text-gray-300 mb-2"></i>
                            <p>No se encontraron registros</p>
                            @if(request()->anyFilled(['unidad', 'estado', 'genero']))
                                <p class="text-sm mt-2">Intenta ajustar los filtros de búsqueda</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación si es necesaria -->
        @if($personas->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-700">
                    Mostrando {{ $personas->firstItem() }} a {{ $personas->lastItem() }} de {{ $personas->total() }} resultados
                </div>
                <div>
                    {{ $personas->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover\:bg-gray-50:hover {
        transition: background-color 0.2s ease-in-out;
    }
</style>
@endpush
