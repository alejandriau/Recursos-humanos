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

        <!-- Filtros mejorados -->
        <div class="card-body border-bottom">
            <form action="{{ route('cenvis.index') }}" method="GET" class="row g-3">
                <!-- Campos existentes... -->

                <div class="col-md-2">
                    <label for="vigencia" class="form-label">Vigencia</label>
                    <select class="form-select" id="vigencia" name="vigencia">
                        <option value="">Todos</option>
                        <option value="vigentes" {{ request('vigencia') == 'vigentes' ? 'selected' : '' }}>Vigentes</option>
                        <option value="por_vencer" {{ request('vigencia') == 'por_vencer' ? 'selected' : '' }}>Por Vencer
                        </option>
                        <option value="vencidos" {{ request('vigencia') == 'vencidos' ? 'selected' : '' }}>Vencidos</option>
                    </select>
                </div>
                <!-- Filtros -->
                <div class="col-md-3">
                    <label for="nombre" class="form-label">Nombre de Persona</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ request('nombre') }}"
                        placeholder="Buscar por nombre...">
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
                        <option value="vigentes" {{ request('vigencia') == 'vigentes' ? 'selected' : '' }}>Vigentes
                        </option>
                        <option value="vencidos" {{ request('vigencia') == 'vencidos' ? 'selected' : '' }}>Vencidos
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="order_by" class="form-label">Ordenar por</label>
                    <select class="form-select" id="order_by" name="order_by">
                        <option value="fecha" {{ request('order_by', 'fecha') == 'fecha' ? 'selected' : '' }}>Fecha
                        </option>
                        <option value="id" {{ request('order_by') == 'id' ? 'selected' : '' }}>ID</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="order_direction" class="form-label">Dirección</label>
                    <select class="form-select" id="order_direction" name="order_direction">
                        <option value="desc" {{ request('order_direction', 'desc') == 'desc' ? 'selected' : '' }}>
                            Descendente</option>
                        <option value="asc" {{ request('order_direction') == 'asc' ? 'selected' : '' }}>Ascendente
                        </option>
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


                <!-- Resto de campos... -->
            </form>
        </div>

        <div class="card-body">
            <!-- Estadísticas rápidas -->
            <!-- Estadísticas usando datos del controller -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body p-3">
                            <h6 class="card-title">Total</h6>
                            <h4 class="mb-0">{{ $estadisticas['total'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body p-3">
                            <h6 class="card-title">Vigentes</h6>
                            <h4 class="mb-0">{{ $estadisticas['vigentes'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body p-3">
                            <h6 class="card-title">Vencidos</h6>
                            <h4 class="mb-0">{{ $estadisticas['inactivos'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha Emisión</th>
                            <th>Vencimiento</th>
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
                            <tr class="{{ $cenvi->por_vencer ? 'table-warning' : '' }}">
                                <td>{{ $cenvi->id }}</td>
                                <td>{{ $cenvi->fecha->format('d/m/Y') }}</td>
                                <td>{{ $cenvi->fecha_vencimiento->format('d/m/Y') }}</td>
                                <td>{{ $cenvi->persona->nombreCompleto ?? 'N/A' }}</td>
                                <td>{{ Str::limit($cenvi->observacion, 30) }}</td>
                                <td>
                                    <span class="badge bg-{{ $cenvi->estado ? 'success' : 'danger' }}">
                                        {{ $cenvi->estado ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($cenvi->esta_vigente)
                                        @if ($cenvi->por_vencer)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Por vencer ({{ round($cenvi->dias_restantes) }} días)
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i>
                                                Vigente ({{ round($cenvi->dias_restantes) }} días)
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle"></i>
                                            Vencido
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Acciones existentes... -->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay CENVI registrados</td>
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

    <!-- Script para actualizar automáticamente -->
    @push('scripts')
        <script>
            // Actualizar la página cada 5 minutos para refrescar estados
            setInterval(function() {
                location.reload();
            }, 300000); // 5 minutos
        </script>
    @endpush
@endsection
