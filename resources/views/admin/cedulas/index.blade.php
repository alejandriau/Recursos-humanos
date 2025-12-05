@extends('dashboard')

@section('title', 'Lista de Cédulas de Identidad')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Cédulas de Identidad</h5>
        @can('crear cedulas')
        <a href="{{ route('cedulas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Cédula
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom">
        <form action="{{ route('cedulas.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="nombre" class="form-label">Nombre de Persona</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                       value="{{ request('nombre') }}" placeholder="Buscar por nombre...">
            </div>

            <div class="col-md-2">
                <label for="ci" class="form-label">C.I.</label>
                <input type="text" class="form-control" id="ci" name="ci"
                       value="{{ request('ci') }}" placeholder="Buscar por CI...">
            </div>

            <div class="col-md-2">
                <label for="expedido" class="form-label">Expedido</label>
                <input type="text" class="form-control" id="expedido" name="expedido"
                       value="{{ request('expedido') }}" placeholder="Lugar expedición...">
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
                <label for="vencimiento" class="form-label">Vigencia</label>
                <select class="form-select" id="vencimiento" name="vencimiento">
                    <option value="">Todos</option>
                    <option value="vigentes" {{ request('vencimiento') == 'vigentes' ? 'selected' : '' }}>Vigentes</option>
                    <option value="vencidas" {{ request('vencimiento') == 'vencidas' ? 'selected' : '' }}>Vencidas</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="order_by" class="form-label">Ordenar por</label>
                <select class="form-select" id="order_by" name="order_by">
                    <option value="fechaRegistro" {{ request('order_by', 'fechaRegistro') == 'fechaRegistro' ? 'selected' : '' }}>Fecha Registro</option>
                    <option value="ci" {{ request('order_by') == 'ci' ? 'selected' : '' }}>C.I.</option>
                    <option value="fechaVencimiento" {{ request('order_by') == 'fechaVencimiento' ? 'selected' : '' }}>Vencimiento</option>
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
                <a href="{{ route('cedulas.index') }}" class="btn btn-secondary">
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
                        <th>C.I.</th>
                        <th>Persona</th>
                        <th>Expedido</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cedulas as $cedula)
                    @php
                        $cedula->actualizarEstadoPorVencimiento();
                    @endphp
                    <tr>
                        <td>{{ $cedula->id }}</td>
                        <td>{{ $cedula->ci ?? 'N/A' }}</td>
                        <td>{{ $cedula->persona->nombre ?? 'N/A' }} {{ $cedula->persona->apellidoPat ?? 'N/A' }} {{ $cedula->persona->apellidoMat ?? 'N/A' }}</td>
                        <td>{{ $cedula->expedido ?? 'N/A' }}</td>
                        <td>
                            @if($cedula->fechaVencimiento)
                                {{ $cedula->fechaVencimiento->format('d/m/Y') }}
                                @if($cedula->esta_vencida)
                                    <br><span class="badge bg-danger">Vencida</span>
                                @else
                                    <br><span class="badge bg-success">{{ $cedula->dias_restantes }} días</span>
                                @endif
                            @else
                                <span class="text-muted">Sin vencimiento</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $cedula->estado ? 'success' : 'danger' }}">
                                {{ $cedula->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('ver cedulas')
                                <a href="{{ route('cedulas.show', $cedula) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar cedulas')
                                <a href="{{ route('cedulas.edit', $cedula) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @if($cedula->pdfcedula && auth()->user()->can('descargar pdf cedulas'))
                                <a href="{{ route('cedulas.download', $cedula) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                @can('eliminar cedulas')
                                <form action="{{ route('cedulas.destroy', $cedula) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Estás seguro de eliminar esta cédula?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay cédulas registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $cedulas->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
