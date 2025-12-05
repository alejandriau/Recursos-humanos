@extends('dashboard')

@section('title', 'Lista de CENVI')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de CENVI</h5>
        @can('crear cenvis')
        <a href="{{ route('cenvis.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo CENVI
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('cenvis.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
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

            <div class="col-md-2">
                <label for="vigencia" class="form-label">Vigencia</label>
                <select class="form-select" id="vigencia" name="vigencia">
                    <option value="">Todos</option>
                    <option value="vigentes" {{ request('vigencia') == 'vigentes' ? 'selected' : '' }}>Vigentes</option>
                    <option value="vencidos" {{ request('vigencia') == 'vencidos' ? 'selected' : '' }}>Vencidos</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="order_by" class="form-label">Ordenar por</label>
                <select class="form-select" id="order_by" name="order_by">
                    <option value="fecha" {{ request('order_by', 'fecha') == 'fecha' ? 'selected' : '' }}>Fecha</option>
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
                <a href="{{ route('cenvis.index') }}" class="btn btn-secondary">
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
                        <th>Observación</th>
                        <th>Estado</th>
                        <th>Vigencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cenvis as $cenvi)
                    @php
                        $cenvi->actualizarEstadoPorVigencia();
                    @endphp
                    <tr>
                        <td>{{ $cenvi->id }}</td>
                        <td>{{ $cenvi->fecha->format('d/m/Y') }}</td>
                        <td>{{ $cenvi->persona->nombre ?? 'N/A' }} {{ $cenvi->persona->apellidoPat ?? 'N/A' }} {{ $cenvi->persona->apellidoMat ?? 'N/A' }}</td>
                        <td>{{ Str::limit($cenvi->observacion, 30) }}</td>
                        <td>
                            <span class="badge bg-{{ $cenvi->estado ? 'success' : 'danger' }}">
                                {{ $cenvi->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            @if($cenvi->esta_vigente)
                                <span class="badge bg-success">
                                    Vigente ({{ round($cenvi->dias_restantes) }} días)
                                </span>
                            @else
                                <span class="badge bg-danger">Vencido</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver cenvis')
                                <a href="{{ route('cenvis.show', $cenvi) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar cenvis')
                                <a href="{{ route('cenvis.edit', $cenvi) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @if($cenvi->pdfcenvi && auth()->user()->can('descargar pdf cenvis'))
                                <a href="{{ route('cenvis.download', $cenvi) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                @can('eliminar cenvis')
                                <form action="{{ route('cenvis.destroy', $cenvi) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar este CENVI?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay CENVI registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $cenvis->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
