@extends('dashboard')

@section('title', 'Lista de Licencias de Conducir')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Licencias de Conducir</h5>
        @can('crear licencias conducir')
        <a href="{{ route('licencias-conducir.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Licencia
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('licencias-conducir.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
            </div>

            <div class="col-md-2">
                <label for="categoria" class="form-label">Categoría</label>
                <select class="form-select" id="categoria" name="categoria">
                    <option value="">Todas</option>
                    @foreach($categorias as $key => $value)
                        <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                            {{ $key }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="vencimiento" class="form-label">Vigencia</label>
                <select class="form-select" id="vencimiento" name="vencimiento">
                    <option value="">Todas</option>
                    <option value="vigentes" {{ request('vencimiento') == 'vigentes' ? 'selected' : '' }}>Vigentes</option>
                    <option value="vencidas" {{ request('vencimiento') == 'vencidas' ? 'selected' : '' }}>Vencidas</option>
                </select>
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
                    <option value="fechavencimiento" {{ request('order_by', 'fechavencimiento') == 'fechavencimiento' ? 'selected' : '' }}>Fecha Vencimiento</option>
                    <option value="categoria" {{ request('order_by') == 'categoria' ? 'selected' : '' }}>Categoría</option>
                    <option value="fechaRegistro" {{ request('order_by') == 'fechaRegistro' ? 'selected' : '' }}>Fecha Registro</option>
                    <option value="id" {{ request('order_by') == 'id' ? 'selected' : '' }}>ID</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="order_direction" class="form-label">Dirección</label>
                <select class="form-select" id="order_direction" name="order_direction">
                    <option value="asc" {{ request('order_direction', 'asc') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                    <option value="desc" {{ request('order_direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('licencias-conducir.index') }}" class="btn btn-secondary">
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
                        <th>Categoría</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licencias as $licencia)
                    @php
                        $licencia->actualizarEstadoPorVencimiento();
                    @endphp
                    <tr>
                        <td>{{ $licencia->id }}</td>
                        <td>{{ $licencia->persona->nombre ?? 'N/A' }} {{ $licencia->persona->apellidoPat ?? 'N/A' }} {{ $licencia->persona->apellidoMat ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $licencia->categoria }}</span>
                            <br>
                            <small class="text-muted">{{ $licencia->categoria_completa }}</small>
                        </td>
                        <td>
                            {{ $licencia->fechavencimiento->format('d/m/Y') }}
                            @if($licencia->esta_vencida)
                                <br><span class="badge bg-danger">Vencida</span>
                            @else
                                <br><span class="badge bg-success">{{ $licencia->dias_restantes }} días</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $licencia->estado ? 'success' : 'danger' }}">
                                {{ $licencia->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver licencias conducir')
                                <a href="{{ route('licencias-conducir.show', $licencia) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar licencias conducir')
                                <a href="{{ route('licencias-conducir.edit', $licencia) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @if($licencia->pdflicc && auth()->user()->can('descargar pdf licencias conducir'))
                                <a href="{{ route('licencias-conducir.download', $licencia) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                @can('eliminar licencias conducir')
                                <form action="{{ route('licencias-conducir.destroy', $licencia) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar esta licencia?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay licencias registradas</td>
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
