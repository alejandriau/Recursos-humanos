@extends('dashboard')

@section('title', 'Estructura de ' . $unidad->denominacion)
@section('header-title', 'Estructura Completa: ' . $unidad->denominacion)

@section('contenido')
<div class="space-y-6">
    <!-- Información de la Unidad Principal -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Unidad Principal
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Información de la unidad base de la estructura
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.unidades.show', $unidad) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-eye mr-2"></i>Ver Detalles
                </a>
                <a href="{{ route('admin.unidades.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Unidades
                </a>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Denominación</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $unidad->denominacion }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Tipo</label>
                    <p class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @switch($unidad->tipo)
                                @case('SECRETARIA') bg-purple-100 text-purple-800 @break
                                @case('SERVICIO') bg-indigo-100 text-indigo-800 @break
                                @case('DIRECCION') bg-blue-100 text-blue-800 @break
                                @case('UNIDAD') bg-green-100 text-green-800 @break
                                @case('AREA') bg-yellow-100 text-yellow-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ $unidad->tipo }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Código</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $unidad->codigo ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de la Estructura -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-sitemap text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Subunidades</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $estructura->obtenerTodasLasSubunidades()->count() - 1 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-tie text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Puestos</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $estructura->contarPersonalTotal() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Puestos Vacantes</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $estructura->puestos()->vacantes()->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-crown text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Jefaturas</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $estructura->puestos()->jefaturas()->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estructura Jerárquica -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Estructura Jerárquica Completa
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Todas las subunidades organizadas jerárquicamente
            </p>
        </div>
        <div class="p-6">
            <div class="estructura-container overflow-x-auto">
                @include('admin.unidades.partials.nodo-estructura', ['unidad' => $estructura, 'nivel' => 0])
            </div>
        </div>
    </div>

    <!-- Lista Detallada de Subunidades -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Lista de Todas las Subunidades
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Vista tabular de todas las unidades en la estructura
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jefe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puestos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($estructura->obtenerTodasLasSubunidades() as $subunidad)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 text-xs">
                                {{ $subunidad->nivelJerarquico ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="{{ route('admin.unidades.show', $subunidad) }}" class="hover:text-blue-600">
                                    {{ $subunidad->denominacion }}
                                </a>
                            </div>
                            @if($subunidad->sigla)
                            <div class="text-sm text-gray-500">{{ $subunidad->sigla }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @switch($subunidad->tipo)
                                    @case('SECRETARIA') bg-purple-100 text-purple-800 @break
                                    @case('SERVICIO') bg-indigo-100 text-indigo-800 @break
                                    @case('DIRECCION') bg-blue-100 text-blue-800 @break
                                    @case('UNIDAD') bg-green-100 text-green-800 @break
                                    @case('AREA') bg-yellow-100 text-yellow-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch">
                                {{ $subunidad->tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $subunidad->codigo ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($subunidad->jefe)
                                <div class="text-sm text-gray-900">{{ $subunidad->jefe->denominacion }}</div>
                                <div class="text-xs text-gray-500">{{ $subunidad->jefe->nivelJerarquico }}</div>
                            @else
                                <span class="text-xs text-gray-400">Sin jefe</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $subunidad->puestos->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($subunidad->esActivo)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.unidades.show', $subunidad) }}"
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.unidades.edit', $subunidad) }}"
                                   class="text-green-600 hover:text-green-900" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.estructura-container {
    min-width: 800px;
}

.nivel-0 { margin-left: 0; }
.nivel-1 { margin-left: 2rem; }
.nivel-2 { margin-left: 4rem; }
.nivel-3 { margin-left: 6rem; }
.nivel-4 { margin-left: 8rem; }
.nivel-5 { margin-left: 10rem; }

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

.nodo-estructura {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.nodo-estructura:hover {
    border-left-color: #3b82f6;
    background-color: #f8fafc;
}
</style>
@endsection
