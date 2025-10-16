@extends('dashboard')

@section('title', 'Estadísticas de ' . $unidad->denominacion)
@section('header-title', 'Estadísticas: ' . $unidad->denominacion)

@section('contenido')
<div class="space-y-6">
    <!-- Información de la Unidad -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Unidad: {{ $unidad->denominacion }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Estadísticas y métricas de la unidad organizacional
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.unidades.show', $unidad) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-eye mr-2"></i>Ver Detalles
                </a>
                <a href="{{ route('admin.unidades.estructura', $unidad) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-sitemap mr-2"></i>Ver Estructura
                </a>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Tipo</label>
                    <p class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $unidad->tipo }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Código</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $unidad->codigo ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Sigla</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $unidad->sigla ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Estado</label>
                    <p class="mt-1">
                        @if($unidad->esActivo)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Activo
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactivo
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Puestos -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total de Puestos</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['total_puestos'] }}</p>
                </div>
            </div>
        </div>

        <!-- Presupuesto Total -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Presupuesto Total</h3>
                    <p class="text-2xl font-semibold text-gray-900">Bs. {{ number_format($estadisticas['presupuesto_total'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Puestos Vacantes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-briefcase text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Puestos Vacantes</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['puestos_vacantes'] }}</p>
                </div>
            </div>
        </div>

        <!-- Puestos Ocupados -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Puestos Ocupados</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['puestos_ocupados'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda Fila de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Subunidades -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <i class="fas fa-sitemap text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Subunidades</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['total_subunidades'] }}</p>
                </div>
            </div>
        </div>

        <!-- Jefaturas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Jefaturas</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['jefaturas'] }}</p>
                </div>
            </div>
        </div>

        <!-- Tasa de Ocupación -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-teal-100 text-teal-600">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Tasa de Ocupación</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        @if($estadisticas['total_puestos'] > 0)
                            {{ number_format(($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Distribuciones -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribución de Puestos por Tipo de Contrato -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribución por Tipo de Contrato</h3>
            <div class="space-y-4">
                @php
                    $puestosPermanentes = $unidad->puestos()->where('tipoContrato', 'PERMANENTE')->count();
                    $puestosEventuales = $unidad->puestos()->where('tipoContrato', 'EVENTUAL')->count();
                    $totalPuestos = $puestosPermanentes + $puestosEventuales;
                @endphp

                @if($totalPuestos > 0)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-green-700">Permanentes</span>
                        <span class="text-sm font-medium">{{ $puestosPermanentes }} ({{ number_format(($puestosPermanentes / $totalPuestos) * 100, 1) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-600 h-3 rounded-full"
                             style="width: {{ ($puestosPermanentes / $totalPuestos) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-yellow-700">Eventuales</span>
                        <span class="text-sm font-medium">{{ $puestosEventuales }} ({{ number_format(($puestosEventuales / $totalPuestos) * 100, 1) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full"
                             style="width: {{ ($puestosEventuales / $totalPuestos) * 100 }}%"></div>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-500 text-center">No hay puestos registrados</p>
                @endif
            </div>
        </div>

        <!-- Distribución de Puestos por Estado -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribución por Estado</h3>
            <div class="space-y-4">
                @if($estadisticas['total_puestos'] > 0)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-blue-700">Ocupados</span>
                        <span class="text-sm font-medium">{{ $estadisticas['puestos_ocupados'] }} ({{ number_format(($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos']) * 100, 1) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full"
                             style="width: {{ ($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos']) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-yellow-700">Vacantes</span>
                        <span class="text-sm font-medium">{{ $estadisticas['puestos_vacantes'] }} ({{ number_format(($estadisticas['puestos_vacantes'] / $estadisticas['total_puestos']) * 100, 1) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full"
                             style="width: {{ ($estadisticas['puestos_vacantes'] / $estadisticas['total_puestos']) * 100 }}%"></div>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-500 text-center">No hay puestos registrados</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Resumen Ejecutivo -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Resumen Ejecutivo</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">Fortalezas</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    @if($estadisticas['puestos_ocupados'] > 0)
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        {{ $estadisticas['puestos_ocupados'] }} puestos correctamente asignados
                    </li>
                    @endif
                    @if($estadisticas['jefaturas'] > 0)
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        {{ $estadisticas['jefaturas'] }} jefaturas establecidas
                    </li>
                    @endif
                    @if($estadisticas['total_subunidades'] > 0)
                    <li class="fas fa-check-circle text-green-500 mr-2"></i>
                        Estructura organizacional bien definida con {{ $estadisticas['total_subunidades'] }} subunidades
                    </li>
                    @endif
                </ul>
            </div>
            <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">Áreas de Oportunidad</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    @if($estadisticas['puestos_vacantes'] > 0)
                    <li class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        {{ $estadisticas['puestos_vacantes'] }} puestos requieren asignación
                    </li>
                    @endif
                    @if($estadisticas['jefaturas'] == 0)
                    <li class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        No se han establecido jefaturas en esta unidad
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Script para gráficos simples -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Puedes agregar aquí gráficos más elaborados con Chart.js si lo deseas
    console.log('Estadísticas cargadas para: {{ $unidad->denominacion }}');
});
</script>
@endsection
