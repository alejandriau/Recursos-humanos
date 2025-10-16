@extends('dashboard')

@section('title', 'Estadísticas de Puestos')
@section('header-title', 'Estadísticas de Puestos')

@section('contenido')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Estadísticas de Puestos</h1>
            <p class="text-gray-600">Métricas y análisis de los puestos de trabajo</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.puestos.index') }}"
               class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-2"></i>Volver a Puestos
            </a>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Puestos -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Puestos</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['total_puestos'] }}</p>
                </div>
            </div>
        </div>

        <!-- Puestos Ocupados -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Puestos Ocupados</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['puestos_ocupados'] }}</p>
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

        <!-- Jefaturas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Jefaturas</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['jefaturas'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda Fila de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Jefaturas Vacantes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Jefaturas Vacantes</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['jefaturas_vacantes'] }}</p>
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

    <!-- Distribuciones -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribución por Tipo de Contrato -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribución por Tipo de Contrato</h3>
            <div class="space-y-4">
                @foreach($estadisticas['por_tipo_contrato'] as $contrato)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium
                            {{ $contrato->tipoContrato == 'PERMANENTE' ? 'text-green-700' : 'text-yellow-700' }}">
                            {{ $contrato->tipoContrato }}
                        </span>
                        <span class="text-sm font-medium">
                            {{ $contrato->total }} ({{ number_format(($contrato->total / $estadisticas['total_puestos']) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full
                            {{ $contrato->tipoContrato == 'PERMANENTE' ? 'bg-green-600' : 'bg-yellow-500' }}"
                             style="width: {{ ($contrato->total / $estadisticas['total_puestos']) * 100 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Distribución por Nivel Jerárquico -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribución por Nivel Jerárquico</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($estadisticas['por_nivel_jerarquico'] as $nivel)
                <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                    <span class="text-sm text-gray-700 flex-1">{{ $nivel->nivelJerarquico }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $nivel->total }}</span>
                    <div class="w-20 bg-gray-200 rounded-full h-2 ml-2">
                        <div class="bg-blue-600 h-2 rounded-full"
                             style="width: {{ ($nivel->total / $estadisticas['total_puestos']) * 100 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Resumen Ejecutivo -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Resumen Ejecutivo</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">Indicadores Positivos</h4>
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
                        {{ $estadisticas['jefaturas'] }} estructuras de jefatura establecidas
                    </li>
                    @endif
                    @if($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos'] > 0.8)
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Alta tasa de ocupación ({{ number_format(($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos']) * 100, 1) }}%)
                    </li>
                    @endif
                </ul>
            </div>
            <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">Áreas de Atención</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    @if($estadisticas['puestos_vacantes'] > 0)
                    <li class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        {{ $estadisticas['puestos_vacantes'] }} puestos requieren asignación urgente
                    </li>
                    @endif
                    @if($estadisticas['jefaturas_vacantes'] > 0)
                    <li class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        {{ $estadisticas['jefaturas_vacantes'] }} jefaturas se encuentran vacantes
                    </li>
                    @endif
                    @if($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos'] < 0.5)
                    <li class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        Baja tasa de ocupación ({{ number_format(($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos']) * 100, 1) }}%)
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
