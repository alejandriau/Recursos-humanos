@extends('dashboard')

@section('title', 'Lista de Licencias Militares')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Licencias Militares</h5>
        @can('crear licencias militares')
        <a href="{{ route('licencias-militares.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Licencia Militar
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('licencias-militares.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
            </div>

            <div class="col-md-2">
                <label for="codigo" class="form-label">Código</label>
                <input type="text" class="form-control" id="codigo" name="codigo"
                       value="{{ request('codigo') }}" placeholder="Buscar código...">
            </div>

            <div class="col-md-2">
                <label for="serie" class="form-label">Serie</label>
                <input type="text" class="form-control" id="serie" name="serie"
                       value="{{ request('serie') }}" placeholder="Buscar serie...">
            </div>

            <div class="col-md-2">
                <label for="fecha_desde" class="form-label">Fecha Desde</label>
                <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                       value="{{ request('fecha_desde') }}">
            </div>

            <div class="col-md-2">
                <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                       value="{{ request('fecha_hasta') }}">
            </div>

            <div class="col-md-2">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="order_by" class="form-label">Ordenar por</label>
                <select class="form-select" id="order_by" name="order_by">
                    <option value="fechaRegistro" {{ request('order_by', 'fechaRegistro') == 'fechaRegistro' ? 'selected' : '' }}>Fecha Registro</option>
                    <option value="fecha" {{ request('order_by') == 'fecha' ? 'selected' : '' }}>Fecha Licencia</option>
                    <option value="codigo" {{ request('order_by') == 'codigo' ? 'selected' : '' }}>Código</option>
                    <option value="id" {{ request('order_by') == 'id' ? 'selected' : '' }}>ID</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="order_direction" class="form-label">Dirección</label>
                <select class="form-select" id="order_direction" name="order_direction">
                    <option value="desc" {{ request('order_direction', 'desc') == 'desc' ? 'selected' : '' }}>Descendente</option>
                    <option value="asc" {{ request('order_direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('licencias-militares.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Persona</th>
                        <th>Código</th>
                        <th>Serie</th>
                        <th>Fecha</th>
                        <th>Descripcion</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licencias as $licencia)
                    <tr>
                        <td>{{ $licencia->id }}</td>
                        <td>{{ $licencia->persona->nombre ?? 'N/A' }} {{ $licencia->persona->apellidoPat ?? 'N/A' }} {{ $licencia->persona->apellidoMat ?? 'N/A' }}</td>
                        <td>{{ $licencia->codigo ?? 'N/A' }}</td>
                        <td>{{ $licencia->serie ?? 'N/A' }}</td>
                        <td>
                            @if($licencia->fecha)
                            {{ $licencia->fecha->format('d/m/Y') }}
                            @if($licencia->es_reciente)
                            <br><span class="badge bg-info">Reciente</span>
                            @endif
                            @else
                            N/A
                            @endif
                        </td>
                        <td>{{ $licencia->descripcion ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $licencia->estado ? 'success' : 'danger' }}">
                                {{ $licencia->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver licencias militares')
                                <a href="{{ route('licencias-militares.show', $licencia) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar licencias militares')
                                <a href="{{ route('licencias-militares.edit', $licencia) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @if($licencia->pdflic && auth()->user()->can('descargar pdf licencias militares'))
                                <a href="{{ route('licencias-militares.download', $licencia) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                @can('eliminar licencias militares')
                                <form action="{{ route('licencias-militares.destroy', $licencia) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar esta licencia militar?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay licencias militares registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $licencias->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
