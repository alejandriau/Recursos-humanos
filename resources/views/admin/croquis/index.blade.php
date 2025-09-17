@extends('dashboard')

@section('title', 'Lista de Croquis')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Croquis</h5>
        <div class="btn-group">
            @can('crear croquis')
            <a href="{{ route('croquis.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Croquis
            </a>
            @endcan
            @can('ver mapa croquis')
            <a href="{{ route('croquis.mapa') }}" class="btn btn-success">
                <i class="fas fa-map"></i> Ver Mapa
            </a>
            @endcan
        </div>
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('croquis.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
            </div>

            <div class="col-md-4">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion"
                       value="{{ request('direccion') }}" placeholder="Buscar por dirección...">
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
                    <option value="direccion" {{ request('order_by') == 'direccion' ? 'selected' : '' }}>Dirección</option>
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
                <a href="{{ route('croquis.index') }}" class="btn btn-secondary">
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
                        <th>Dirección</th>
                        <th>Persona</th>
                        <th>Coordenadas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($croquis as $croqui)
                    <tr>
                        <td>{{ $croqui->id }}</td>
                        <td>{{ Str::limit($croqui->direccion, 50) }}</td>
                        <td>{{ $croqui->persona->nombre ?? 'N/A' }}</td>
                        <td>
                            <small>Lat: {{ $croqui->latitud }}</small><br>
                            <small>Lng: {{ $croqui->longitud }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $croqui->estado ? 'success' : 'danger' }}">
                                {{ $croqui->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver croquis')
                                <a href="{{ route('croquis.show', $croqui) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar croquis')
                                <a href="{{ route('croquis.edit', $croqui) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                <a href="{{ $croqui->google_maps_link }}" target="_blank" class="btn btn-sm btn-success">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>

                                @can('eliminar croquis')
                                <form action="{{ route('croquis.destroy', $croqui) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar este croquis?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay croquis registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $croquis->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
