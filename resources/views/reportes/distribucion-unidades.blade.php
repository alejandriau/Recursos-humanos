@extends('dashboard')

@section('title', 'Distribución por Unidades')
@section('header-title', 'Distribución por Unidades Organizacionales')

@section('contenido')
<div class="space-y-6">
    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-sitemap"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Unidades</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $distribucion->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Puestos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $distribucion->sum('total_puestos') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Puestos Ocupados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $distribucion->sum('puestos_ocupados') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-chair"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Puestos Vacantes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $distribucion->sum('puestos_vacantes') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Distribución -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Distribución de Personal por Unidad</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad Organizacional</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Puestos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ocupados</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vacantes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Ocupación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($distribucion as $unidad)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">{{ $unidad->denominacion }}</div>
                            @if($unidad->sigla)
                                <div class="text-xs text-gray-500">{{ $unidad->sigla }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $unidad->total_puestos }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $unidad->puestos_ocupados }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $unidad->puestos_vacantes }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="h-2 rounded-full
                                        @if($unidad->porcentaje_ocupacion >= 80) bg-green-500
                                        @elseif($unidad->porcentaje_ocupacion >= 50) bg-yellow-500
                                        @else bg-red-500 @endif"
                                        style="width: {{ $unidad->porcentaje_ocupacion }}%">
                                    </div>
                                </div>
                                <span class="text-sm font-medium
                                    @if($unidad->porcentaje_ocupacion >= 80) text-green-700
                                    @elseif($unidad->porcentaje_ocupacion >= 50) text-yellow-700
                                    @else text-red-700 @endif">
                                    {{ $unidad->porcentaje_ocupacion }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($unidad->esActivo)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Activa
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    Inactiva
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
