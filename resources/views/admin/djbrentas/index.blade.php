@extends('dashboard')

@section('title', 'Lista de DJBRenta')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de DJBRenta</h5>
        @can('crear djbrentas')
        <a href="{{ route('djbrentas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo DJBRenta
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('djbrentas.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
            </div>

            <div class="col-md-3">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" class="form-control" id="tipo" name="tipo"
                       value="{{ request('tipo') }}" placeholder="Buscar por tipo...">
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
                    <option value="fecha" {{ request('order_by', 'fecha') == 'fecha' ? 'selected' : '' }}>Fecha</option>
                    <option value="id" {{ request('order_by') == 'id' ? 'selected' : '' }}>ID</option>
                    <option value="tipo" {{ request('order_by') == 'tipo' ? 'selected' : '' }}>Tipo</option>
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
                <a href="{{ route('djbrentas.index') }}" class="btn btn-secondary">
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
                        <th>Fecha</th>
                        <th>Persona</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($djbrentas as $djbrenta)
                    <tr>
                        <td>{{ $djbrenta->id }}</td>
                        <td>{{ $djbrenta->fecha->format('d/m/Y') }}</td>
                        <td>{{ $djbrenta->persona->nombre ?? 'N/A' }} {{ $djbrenta->persona->apellidoPat ?? 'N/A' }} {{ $djbrenta->persona->apellidoMat ?? 'N/A' }}</td>
                        <td>{{ $djbrenta->tipo ?? 'Sin tipo' }}</td>
                        <td>
                            <span class="badge bg-{{ $djbrenta->estado ? 'success' : 'danger' }}">
                                {{ $djbrenta->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver djbrentas')
                                <a href="{{ route('djbrentas.show', $djbrenta) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar djbrentas')
                                <a href="{{ route('djbrentas.edit', $djbrenta) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @if($djbrenta->pdfrenta && auth()->user()->can('descargar pdf djbrentas'))
                                <a href="{{ route('djbrentas.download', $djbrenta) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                @can('eliminar djbrentas')
                                <form action="{{ route('djbrentas.destroy', $djbrenta) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar este DJBRenta?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay DJBRenta registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $djbrentas->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
