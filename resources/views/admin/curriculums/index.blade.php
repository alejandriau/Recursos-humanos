@extends('dashboard')

@section('title', 'Lista de Curriculums')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Curriculums</h5>
        @can('crear curriculums')
        <a href="{{ route('curriculums.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Curriculum
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('curriculums.index') }}" method="GET" class="row g-3">
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
                <label for="mas" class="form-label">Campo "Mas"</label>
                <input type="text" class="form-control" id="mas" name="mas"
                       value="{{ request('mas') }}" placeholder="Buscar en mas...">
            </div>

            <div class="col-md-2">
                <label for="otros" class="form-label">Campo "Otros"</label>
                <input type="text" class="form-control" id="otros" name="otros"
                       value="{{ request('otros') }}" placeholder="Buscar en otros...">
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
                    <option value="descripcion" {{ request('order_by') == 'descripcion' ? 'selected' : '' }}>Descripción</option>
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
                <a href="{{ route('curriculums.index') }}" class="btn btn-secondary">
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
                        <th>Descripción</th>
                        <th>Información</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($curriculums as $curriculum)
                    <tr>
                        <td>{{ $curriculum->id }}</td>
                        <td>{{ $curriculum->persona->nombre ?? 'N/A' }}</td>
                        <td>{{ Str::limit($curriculum->descripcion, 50) }}</td>
                        <td>
                            <small class="text-muted">{{ $curriculum->informacion_resumida }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $curriculum->estado ? 'success' : 'danger' }}">
                                {{ $curriculum->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver curriculums')
                                <a href="{{ route('curriculums.show', $curriculum) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar curriculums')
                                <a href="{{ route('curriculums.edit', $curriculum) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @if($curriculum->tiene_archivo && auth()->user()->can('descargar pdf curriculums'))
                                <a href="{{ route('curriculums.download', $curriculum) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                @can('eliminar curriculums')
                                <form action="{{ route('curriculums.destroy', $curriculum) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar este curriculum?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay curriculums registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $curriculums->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
