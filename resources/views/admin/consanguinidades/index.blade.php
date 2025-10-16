@extends('dashboard')

@section('title', 'Lista de Declaraciones de Consanguinidad')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Declaraciones de Consanguinidad</h5>
        @can('crear consanguinidades')
        <a href="{{ route('consanguinidades.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Declaración
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('consanguinidades.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
            </div>

            <div class="col-md-3">
                <label for="observacion" class="form-label">Observación</label>
                <input type="text" class="form-control" id="observacion" name="observacion"
                       value="{{ request('observacion') }}" placeholder="Buscar por observación...">
            </div>

            <div class="col-md-2">
                <label for="pdfconsag" class="form-label">PDF</label>
                <input type="text" class="form-control" id="pdfconsag" name="pdfconsag"
                       value="{{ request('pdfconsag') }}" placeholder="Buscar PDF...">
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
                    <option value="fechaRegistro" {{ request('order_by') == 'fechaRegistro' ? 'selected' : '' }}>Fecha Registro</option>
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
                <a href="{{ route('consanguinidades.index') }}" class="btn btn-secondary">
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
                        <th>Fecha</th>
                        <th>Observación</th>
                        <th>PDF</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consanguinidades as $consanguinidad)
                    <tr>
                        <td>{{ $consanguinidad->id }}</td>
                        <td>{{ $consanguinidad->persona->nombre ?? 'N/A' }}</td>
                        <td>
                            @if($consanguinidad->fecha)
                                {{ $consanguinidad->fecha->format('d/m/Y') }}
                                @if($consanguinidad->es_reciente)
                                    <br><span class="badge bg-info">Reciente</span>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ Str::limit($consanguinidad->observacion, 50) }}</td>
                        <td>
                            @if($consanguinidad->pdfconsag)
                                <a href="{{ route('consanguinidades.download', $consanguinidad) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> PDF
                                </a>
                            @else
                                <span class="text-muted">Sin PDF</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $consanguinidad->estado ? 'success' : 'danger' }}">
                                {{ $consanguinidad->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver consanguinidades')
                                <a href="{{ route('consanguinidades.show', $consanguinidad) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar consanguinidades')
                                <a href="{{ route('consanguinidades.edit', $consanguinidad) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('eliminar consanguinidades')
                                <form action="{{ route('consanguinidades.destroy', $consanguinidad) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta declaración?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay declaraciones de consanguinidad registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $consanguinidades->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
