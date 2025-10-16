@extends('dashboards')

@section('title', 'Puestos Vacantes')
@section('header-title', 'Puestos Vacantes')

@section('contenido')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Puestos Vacantes</h1>
            <p class="text-gray-600">Puestos disponibles para asignación</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('puestos.index') }}"
               class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-list mr-2"></i>Ver Todos
            </a>
        </div>
    </div>

    <!-- Tarjeta de Estadísticas -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $puestos->total() }} Puestos Vacantes</h3>
                <p class="text-gray-600">Disponibles para asignación inmediata</p>
            </div>
            <div class="text-3xl text-yellow-500">
                <i class="fas fa-briefcase"></i>
            </div>
        </div>
    </div>

    <!-- Lista de Puestos Vacantes -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puesto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contrato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Haber</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($puestos as $puesto)
                    <tr class="hover:bg-gray-50">
                        <!-- Denominación -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-tie text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('puestos.show', $puesto) }}" class="hover:text-blue-600">
                                            {{ $puesto->denominacion }}
                                        </a>
                                    </div>
                                    @if($puesto->esJefatura)
                                    <div class="text-xs text-purple-600">
                                        <i class="fas fa-crown mr-1"></i>Jefatura Vacante
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Unidad -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <a href="{{ route('unidades.show', $puesto->unidadOrganizacional) }}" class="hover:text-blue-600">
                                    {{ $puesto->unidadOrganizacional->denominacion }}
                                </a>
                            </div>
                            <div class="text-xs text-gray-500">{{ $puesto->unidadOrganizacional->tipo }}</div>
                        </td>

                        <!-- Nivel Jerárquico -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $puesto->nivelJerarquico }}
                        </td>

                        <!-- Item -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $puesto->item ?? 'N/A' }}
                        </td>

                        <!-- Tipo de Contrato -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $puesto->tipoContrato == 'PERMANENTE' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $puesto->tipoContrato }}
                            </span>
                        </td>

                        <!-- Haber -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($puesto->haber)
                                Bs. {{ number_format($puesto->haber, 2) }}
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('puestos.show', $puesto) }}"
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('puestos.edit', $puesto) }}"
                                   class="text-green-600 hover:text-green-900" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$puesto->esJefatura)
                                <form action="{{ route('puestos.asignar-jefatura', $puesto) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                            onclick="return confirm('¿Está seguro de asignar jefatura a este puesto?')"
                                            class="text-purple-600 hover:text-purple-900" title="Asignar jefatura">
                                        <i class="fas fa-crown"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
                            <p>¡Excelente! No hay puestos vacantes en este momento.</p>
                            <p class="text-xs text-gray-400 mt-1">Todos los puestos están asignados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($puestos->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $puestos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
