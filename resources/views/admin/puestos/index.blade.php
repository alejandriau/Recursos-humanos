@extends('dashboard')

@section('title', 'Puestos de Trabajo')
@section('header-title', 'Puestos de Trabajo')

@section('contenido')
<div x-data="{ showFilters: false }">
    <!-- Header con Botones -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Puestos de Trabajo</h1>
            <p class="text-gray-600">Gestiona todos los puestos de la organización</p>
        </div>
        <div class="flex space-x-3">
            <button @click="showFilters = !showFilters"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-filter mr-2"></i>Filtros
            </button>
            <a href="{{ route('puestos.create') }}"
               class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>Nuevo Puesto
            </a>
        </div>
    </div>

    <!-- Filtros -->
<div x-show="showFilters" x-transition class="bg-white p-4 rounded-lg shadow mb-6">
    <form method="GET" action="{{ route('puestos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Búsqueda -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Nombre, item, nivel...">
        </div>

        <!-- Nivel Jerárquico -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Nivel Jerárquico</label>
            <select name="nivel_jerarquico" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos</option>
                @foreach($nivelesJerarquicos as $nivel)
                    <option value="{{ $nivel }}" {{ request('nivel_jerarquico') == $nivel ? 'selected' : '' }}>
                        {{ $nivel }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tipo de Contrato -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo Contrato</label>
            <select name="tipo_contrato" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos</option>
                <option value="PERMANENTE" {{ request('tipo_contrato') == 'PERMANENTE' ? 'selected' : '' }}>Permanente</option>
                <option value="EVENTUAL" {{ request('tipo_contrato') == 'EVENTUAL' ? 'selected' : '' }}>Eventual</option>
            </select>
        </div>

        <!-- Botones -->
        <div class="flex items-end space-x-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search mr-2"></i>Filtrar
            </button>
            <a href="{{ route('puestos.index') }}"
               class="px-4 py-2 bg-gray-200 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                <i class="fas fa-redo mr-2"></i>Limpiar
            </a>
        </div>
    </form>
</div>

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Puestos</p>
                    <p class="text-lg font-semibold">{{ $puestos->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Activos</p>
                    <p class="text-lg font-semibold">{{ $estadisticas['activos'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Vacantes</p>
                    <p class="text-lg font-semibold">{{ $estadisticas['vacantes'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Jefaturas</p>
                    <p class="text-lg font-semibold">{{ $estadisticas['jefaturas'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Puestos -->
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($puestos as $puesto)
                    <tr class="hover:bg-gray-50">
                        <!-- Denominación -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-tie text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('puestos.show', $puesto) }}" class="hover:text-blue-600">
                                            {{ $puesto->denominacion }}
                                        </a>
                                    </div>
                                    @if($puesto->esJefatura)
                                    <div class="text-xs text-purple-600">
                                        <i class="fas fa-crown mr-1"></i>Jefatura
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
                                <a href="{{ route('puestos.show', $puesto) }}"
                                   class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('puestos.edit', $puesto) }}"
                                   class="text-green-600 hover:text-green-900" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($puesto->esActivo)
                                <form action="{{ route('puestos.desactivar', $puesto) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                            onclick="return confirm('¿Está seguro de desactivar este puesto?')"
                                            class="text-yellow-600 hover:text-yellow-900" title="Desactivar">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('puestos.reactivar', $puesto) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                            onclick="return confirm('¿Está seguro de reactivar este puesto?')"
                                            class="text-green-600 hover:text-green-900" title="Reactivar">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </form>
                                @endif
                                @if($puesto->esJefatura)
                                <form action="{{ route('puestos.quitar-jefatura', $puesto) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                            onclick="return confirm('¿Está seguro de quitar la jefatura de este puesto?')"
                                            class="text-purple-600 hover:text-purple-900" title="Quitar jefatura">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                                @else
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
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <p>No se encontraron puestos de trabajo</p>
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
