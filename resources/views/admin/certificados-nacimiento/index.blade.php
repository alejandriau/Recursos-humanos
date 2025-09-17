@extends('dashboard')

@section('title', 'Lista de Certificados de Nacimiento')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Certificados de Nacimiento</h5>
        @can('crear certificados nacimiento')
        <a href="{{ route('certificados-nacimiento.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Certificado
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('certificados-nacimiento.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
            </div>

            <div class="col-md-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion"
                       value="{{ request('descripcion') }}" placeholder="Buscar por descripción...">
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
                    <option value="fecha" {{ request('order_by') == 'fecha' ? 'selected' : '' }}>Fecha Certificado</option>
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
                <a href="{{ route('certificados-nacimiento.index') }}" class="btn btn-secondary">
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
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificados as $certificado)
                    <tr>
                        <td>{{ $certificado->id }}</td>
                        <td>{{ $certificado->fecha ? $certificado->fecha->format('d/m/Y') : 'Sin fecha' }}</td>

                        <td>{{ $certificado->persona->nombre ?? 'N/A' }}</td>
                        <td>{{ Str::limit($certificado->descripcion, 50) }}</td>
                        <td>
                            <span class="badge bg-{{ $certificado->estado ? 'success' : 'danger' }}">
                                {{ $certificado->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                            @if($certificado->es_reciente)
                                <br><span class="badge bg-info mt-1">Reciente</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver certificados nacimiento')
                                <a href="{{ route('certificados-nacimiento.show', $certificado) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar certificados nacimiento')
                                <a href="{{ route('certificados-nacimiento.edit', $certificado) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @if($certificado->pdfcern && auth()->user()->can('descargar pdf certificados nacimiento'))
                                <a href="{{ route('certificados-nacimiento.download', $certificado) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                @can('eliminar certificados nacimiento')
                                <form action="{{ route('certificados-nacimiento.destroy', $certificado) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar este certificado?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay certificados registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $certificados->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
