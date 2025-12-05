@extends('dashboard')

@section('title', 'Lista de Compromisos')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Compromisos</h5>
        @can('crear compromisos')
        <a href="{{ route('compromisos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Compromiso
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('compromisos.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
            </div>

            <div class="col-md-4">
                <label for="compromiso" class="form-label">Compromiso</label>
                <input type="text" class="form-control" id="compromiso" name="compromiso"
                       value="{{ request('compromiso') }}" placeholder="Buscar compromiso...">
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
                <a href="{{ route('compromisos.index') }}" class="btn btn-secondary">
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
                        <th>Compromisos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compromisos as $compromiso)
                    <tr>
                        <td>{{ $compromiso->id }}</td>
                        <td>{{ $compromiso->persona->nombre ?? 'N/A' }} {{ $compromiso->persona->apellidoPat ?? 'N/A' }} {{ $compromiso->persona->apellidoMat ?? 'N/A' }}</td>
                        <td>
                            @foreach($compromiso->compromisos as $comp)
                                <span class="badge bg-info mb-1">Comp. {{ $comp['numero'] }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $compromiso->total_compromisos }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $compromiso->estado ? 'success' : 'danger' }}">
                                {{ $compromiso->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver compromisos')
                                <a href="{{ route('compromisos.show', $compromiso) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar compromisos')
                                <a href="{{ route('compromisos.edit', $compromiso) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('descargar pdf compromisos')
                                @if($compromiso->total_compromisos > 0)
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach($compromiso->compromisos as $comp)
                                            @if($comp['archivo'])
                                            <li>
                                                <a class="dropdown-item" href="{{ route('compromisos.download', ['compromiso' => $compromiso, 'numero' => $comp['numero']]) }}">
                                                    Compromiso {{ $comp['numero'] }}
                                                </a>
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                @endcan

                                @can('eliminar compromisos')
                                <form action="{{ route('compromisos.destroy', $compromiso) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar este compromiso?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay compromisos registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $compromisos->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script para el dropdown de descargas
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl)
        });
    });
</script>
@endpush
