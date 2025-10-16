@extends('dashboard')

@section('contenidouno')
    <meta content="Gestión de Designaciones" name="description">
    <title>Gestión de Designaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            z-index: 9999 !important;
        }
        .badge-designacion {
            font-size: 0.75rem;
        }
        .table-responsive {
            font-size: 0.875rem;
        }
        .acciones-dropdown {
            min-width: 200px;
        }
        .jerarquia-text {
            font-size: 0.75rem;
            line-height: 1.2;
        }
    </style>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <!-- Alertas -->
    <div class="row g-4">
        <div class="col-sm-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Filtros de Búsqueda</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('historial') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Buscar:</label>
                                <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}"
                                       placeholder="Nombre, apellidos, CI, item...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo Movimiento:</label>
                                <select name="tipo_movimiento" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach(['designacion_inicial','movilidad','ascenso','comision','interinato','reasignacion','encargo_funciones','recontratacion'] as $tipo)
                                        <option value="{{ $tipo }}" {{ ($tipo_movimiento ?? '') == $tipo ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $tipo)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo Contrato:</label>
                                <select name="tipo_contrato" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach(['permanente','contrato_administrativo','contrato_plazo_fijo','contrato_obra','honorarios'] as $contrato)
                                        <option value="{{ $contrato }}" {{ ($tipo_contrato ?? '') == $contrato ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $contrato)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Estado:</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="activo" {{ ($estado ?? '') == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="concluido" {{ ($estado ?? '') == 'concluido' ? 'selected' : '' }}>Concluido</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Buscar</button>
                                <a href="{{ route('historial') }}" class="btn btn-secondary">Limpiar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Designaciones Activas</h6>
                    <h3>{{ $puestos->where('historial_actual.estado', 'activo')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Puestos Vacíos</h6>
                    <h3>{{ $puestos->where('historial_actual', null)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Comisiones</h6>
                    <h3>{{ $puestos->where('historial_actual.tipo_movimiento', 'comision')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Interinatos</h6>
                    <h3>{{ $puestos->where('historial_actual.tipo_movimiento', 'interinato')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex gap-2">
                <a href="{{ route('historial.vacio') }}" class="btn btn-outline-primary">
                    <i class="fas fa-user-plus me-2"></i>Ver Puestos Vacíos
                </a>
                <a href="{{ route('reportes.personal') }}" class="btn btn-outline-danger">
                    <i class="fas fa-file-pdf me-2"></i>Reporte PDF
                </a>
                <a href="{{ route('reportes.excel') }}" class="btn btn-outline-success">
                    <i class="fas fa-file-excel me-2"></i>Descargar Excel
                </a>
                <a href="{{ route('historial.estadisticas') }}" class="btn btn-outline-info">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas
                </a>
            </div>
        </div>
    </div>

    <!-- Tabla Principal -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Lista de Designaciones</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ITEM</th>
                            <th>NIVEL</th>
                            <th>DENOMINACIÓN</th>
                            <th>PERSONAL</th>
                            <th>CI</th>
                            <th>SALARIO</th>
                            <th>FECHA INICIO</th>
                            <th>TIPO</th>
                            <th>ESTADO</th>
                            <th>DEPENDENCIA</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($puestos as $puesto)
                            <tr>
                                <td><strong>{{ $puesto->item ?? 'N/A' }}</strong></td>
                                <td>{{ $puesto->nivelJerarquico ?? 'N/A' }}</td>
                                <td>{{ $puesto->denominacion ?? 'N/A' }}</td>
                                <td>
                                    @if($puesto->persona)
                                        {{ $puesto->persona->apellidoPat }} {{ $puesto->persona->apellidoMat }} {{ $puesto->persona->nombre }}
                                    @else
                                        <span class="text-muted">Vacante</span>
                                    @endif
                                </td>
                                <td>{{ $puesto->persona->ci ?? 'N/A' }}</td>
                                <td>
                                    @if($puesto->historial_actual)
                                        ${{ number_format($puesto->historial_actual->salario ?? 0, 2) }}
                                    @else
                                        ${{ number_format($puesto->haber ?? 0, 2) }}
                                    @endif
                                </td>
                                <td>
                                    {{ $puesto->historial_actual ? $puesto->historial_actual->fecha_inicio->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($puesto->historial_actual)
                                        <span class="badge bg-info badge-designacion">
                                            {{ ucfirst(str_replace('_', ' ', $puesto->historial_actual->tipo_movimiento)) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ ucfirst(str_replace('_', ' ', $puesto->historial_actual->tipo_contrato)) }}
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">Vacante</span>
                                    @endif
                                </td>
                                <td>
                                    @if($puesto->historial_actual)
                                        <span class="badge bg-{{ $puesto->historial_actual->estado == 'activo' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($puesto->historial_actual->estado) }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning">Vacante</span>
                                    @endif
                                </td>
                                <td class="jerarquia-text">
                                    @php
                                        $unidad = $puesto->unidadOrganizacional;
                                        $jerarquia = [];

                                        // Obtener la jerarquía completa
                                        while ($unidad) {
                                            $jerarquia[] = $unidad->denominacion . ' (' . $unidad->tipo . ')';
                                            $unidad = $unidad->padre;
                                        }

                                        // Mostrar en orden inverso (de mayor a menor jerarquía)
                                        echo implode(' → ', array_reverse($jerarquia));
                                    @endphp
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu acciones-dropdown">
                                            @if($puesto->historial_actual && $puesto->persona)
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('historial.persona', $puesto->persona->id) }}">
                                                        <i class="fas fa-history me-2"></i>Ver Historial
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                       onclick="descargarMemo({{ $puesto->historial_actual->id }})">
                                                        <i class="fas fa-download me-2"></i>Descargar Memo
                                                    </a>
                                                </li>
                                                @if($puesto->historial_actual->estado == 'activo')
                                                <li>
                                                    <form action="{{ route('historial.concluir', $puesto->historial_actual->id) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item"
                                                                onclick="return confirm('¿Está seguro de concluir esta designación?')">
                                                            <i class="fas fa-times-circle me-2"></i>Concluir Designación
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bajaModal{{ $puesto->historial_actual->id }}">BAJA</button>
                                                    <div class="modal fade" id="bajaModal{{ $puesto->historial_actual->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content"> <!-- Asegúrate de tener esta clase -->
                                                                <form method="POST" action="{{ route('altasbajas.store') }}" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Dar de baja <p>{{ $puesto->historial_actual->apellidoPat." ". $puesto->historial_actual->apellidoMat ." ". $puesto->historial_actual->nombre }}</p></h5>

                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="idPersona" value="{{ $puesto->historial_actual->id }}">
                                                                        <input type="hidden" name="apellidopaterno" value="{{ $puesto->historial_actual->apellidoPat }}">
                                                                        <input type="hidden" name="apellidomaterno" value="{{ $puesto->historial_actual->apellidoMat }}">
                                                                        <input type="hidden" name="nombre" value="{{ $puesto->historial_actual->nombre }}">

                                                                        <div class="mb-3">
                                                                            <label class="form-label">Fecha de retiro</label>
                                                                            <input type="date" name="fechafin" class="form-control" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Motivo</label>
                                                                            <input type="text" name="motivo" class="form-control" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Observaciones</label>
                                                                            <textarea name="obser" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">PDF (Renuncia)</label>
                                                                            <input type="file" name="pdffile" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-danger">Guardar</button>
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endif
                                            @else
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('historial.create', $puesto->id) }}">
                                                        <i class="fas fa-user-plus me-2"></i>Asignar Personal
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                @if($puesto->historial_actual)
                                                    <a class="dropdown-item" href="{{ route('historial.edit', $puesto->historial_actual->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Editar Designación
                                                    </a>
                                                @else
                                                    <a class="dropdown-item" href="{{ route('puestos.edit', $puesto->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Editar Puesto
                                                    </a>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <p>No se encontraron resultados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function descargarMemo(historialId) {
    window.open(`/historial/${historialId}/descargar-memo`, '_blank');
}

// Inicializar Select2
$(document).ready(function() {
    $('select[name="tipo_movimiento"], select[name="tipo_contrato"], select[name="estado"]').select2({
        placeholder: "Seleccione...",
        allowClear: true
    });
});
</script>

<!-- Script para manejar la jerarquía de unidades -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Puedes agregar aquí funcionalidades adicionales si es necesario
    console.log('Vista de designaciones cargada correctamente');
});
</script>
@endsection
