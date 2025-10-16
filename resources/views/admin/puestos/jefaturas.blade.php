@extends('dashboard')

@section('title', 'Jefaturas')
@section('header-title', 'Jefaturas de la Organización')

@section('contenido')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Jefaturas</h1>
            <p class="text-gray-600">Puestos de jefatura en la organización</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.puestos.index') }}"
               class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-list mr-2"></i>Ver Todos
            </a>
        </div>
    </div>

    <!-- Estadísticas de Jefaturas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-crown text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Jefaturas</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $jefaturas->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Jefaturas Ocupadas</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $jefaturas->where('personaActual', '!=', null)->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-user-times text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Jefaturas Vacantes</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $jefaturas->where('personaActual', null)->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Jefaturas -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puesto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persona Asignada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jefaturas as $puesto)
                    <tr class="hover:bg-gray-50">
                        <!-- Denominación -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-crown text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('admin.puestos.show', $puesto) }}" class="hover:text-blue-600">
                                            {{ $puesto->denominacion }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $puesto->item }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Unidad -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <a href="{{ route('admin.unidades.show', $puesto->unidadOrganizacional) }}" class="hover:text-blue-600">
                                    {{ $puesto->unidadOrganizacional->denominacion }}
                                </a>
                            </div>
                            <div class="text-xs text-gray-500">{{ $puesto->unidadOrganizacional->tipo }}</div>
                        </td>

                        <!-- Nivel Jerárquico -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $puesto->nivelJerarquico }}
                        </td>

                        <!-- Persona Asignada -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($puesto->personaActual)
                                <div class="text-sm text-gray-900">{{ $puesto->personaActual->nombres }} {{ $puesto->personaActual->apellidos }}</div>
                                <div class="text-xs text-gray-500">{{ $puesto->personaActual->ci }}</div>
                            @else
                                <span class="text-sm text-yellow-600 font-medium">Vacante</span>
                            @endif
                        </td>

                        <!-- Estado -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($puesto->esActivo)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.puestos.show', $puesto) }}"
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$puesto->personaActual)
                                <form action="{{ route('admin.puestos.quitar-jefatura', $puesto) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                            onclick="return confirm('¿Está seguro de quitar la jefatura de este puesto?')"
                                            class="text-red-600 hover:text-red-900" title="Quitar jefatura">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            <i class="fas fa-crown text-3xl text-gray-300 mb-2"></i>
                            <p>No se encontraron jefaturas registradas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
