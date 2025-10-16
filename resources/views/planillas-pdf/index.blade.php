@extends('dashboard')

@section('contenido')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Planillas de Sueldos</h1>
            <p class="text-muted mb-0">Gestión y administración de planillas PDF</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('convert.form') }}" class="btn btn-outline-secondary">
                <i class="fas fa-file-word me-2"></i>Convertir TXT a Word
            </a>
            @can('crear_planillas')
            <a href="{{ route('planillas-pdf.create') }}" class="btn btn-primary">
                <i class="fas fa-upload me-2"></i>Subir Nueva Planilla
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @can('ver_planillas')
        @if($planillas->count() > 0)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Lista de Planillas
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary">{{ $planillas->total() }} planillas</span>

                        <!-- Selector de ordenamiento -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-sort me-1"></i>
                                Ordenar
                            </button>
                            <ul class="dropdown-menu">
                                <!-- Ordenamiento por Año -->
                                <li>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery([
                                        'orden_campo' => 'anio',
                                        'orden_direccion' => $ordenCampo == 'anio' && $ordenDireccion == 'desc' ? 'asc' : 'desc'
                                    ]) }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Año</span>
                                            @if($ordenCampo == 'anio')
                                                <i class="fas fa-arrow-{{ $ordenDireccion == 'desc' ? 'down' : 'up' }} text-primary"></i>
                                            @endif
                                        </div>
                                    </a>
                                </li>

                                <!-- Ordenamiento por Fecha de Carga -->
                                <li>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery([
                                        'orden_campo' => 'created_at',
                                        'orden_direccion' => $ordenCampo == 'created_at' && $ordenDireccion == 'desc' ? 'asc' : 'desc'
                                    ]) }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Fecha de Carga</span>
                                            @if($ordenCampo == 'created_at')
                                                <i class="fas fa-arrow-{{ $ordenDireccion == 'desc' ? 'down' : 'up' }} text-primary"></i>
                                            @endif
                                        </div>
                                    </a>
                                </li>

                                <!-- Ordenamiento por Período -->
                                <li>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery([
                                        'orden_campo' => 'periodo_pago',
                                        'orden_direccion' => $ordenCampo == 'periodo_pago' && $ordenDireccion == 'desc' ? 'asc' : 'desc'
                                    ]) }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Período</span>
                                            @if($ordenCampo == 'periodo_pago')
                                                <i class="fas fa-arrow-{{ $ordenDireccion == 'desc' ? 'down' : 'up' }} text-primary"></i>
                                            @endif
                                        </div>
                                    </a>
                                </li>

                                <!-- Ordenamiento por Nombre -->
                                <li>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery([
                                        'orden_campo' => 'nombre_original',
                                        'orden_direccion' => $ordenCampo == 'nombre_original' && $ordenDireccion == 'desc' ? 'asc' : 'desc'
                                    ]) }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Nombre</span>
                                            @if($ordenCampo == 'nombre_original')
                                                <i class="fas fa-arrow-{{ $ordenDireccion == 'desc' ? 'down' : 'up' }} text-primary"></i>
                                            @endif
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Indicador de ordenamiento actual -->
                @if($ordenCampo)
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Ordenado por:
                        <strong>
                            @switch($ordenCampo)
                                @case('anio') Año @break
                                @case('created_at') Fecha de Carga @break
                                @case('periodo_pago') Período @break
                                @case('nombre_original') Nombre @break
                            @endswitch
                        </strong>
                        ({{ $ordenDireccion == 'desc' ? 'Descendente' : 'Ascendente' }})
                    </small>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">
                                    <i class="fas fa-file me-1"></i>
                                    Nombre del Archivo
                                </th>
                                <th>
                                    <i class="fas fa-calendar me-1"></i>
                                    Período
                                </th>
                                <th>
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Año
                                </th>
                                <th>
                                    <i class="fas fa-clock me-1"></i>
                                    Fecha de Carga
                                </th>
                                <th>
                                    <i class="fas fa-cog me-1"></i>
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($planillas as $planilla)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-3 fs-5"></i>
                                        <div>
                                            <h6 class="mb-1">{{ $planilla->nombre_original }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-hdd me-1"></i>
                                                {{ number_format(filesize(storage_path('app/public/' . $planilla->ruta)) / 1024, 2) }} KB
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $planilla->periodo_pago }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning bg-opacity-10 text-yellow-800">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $planilla->anio }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $planilla->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $planilla->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Ver PDF -->
                                        @can('descargar_planillas')
                                        <a href="{{ route('planillas-pdf.show', $planilla->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           target="_blank"
                                           data-bs-toggle="tooltip"
                                           title="Ver PDF">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan

                                        <!-- Descargar PDF -->
                                        @can('descargar_planillas')
                                        <a href="{{ route('planillas-pdf.download', $planilla->id) }}"
                                           class="btn btn-sm btn-outline-success"
                                           data-bs-toggle="tooltip"
                                           title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @endcan

                                        <!-- Editar -->
                                        @can('editar_planillas')
                                        <a href="{{ route('planillas-pdf.edit', $planilla->id) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           data-bs-toggle="tooltip"
                                           title="Editar información">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan

                                        <!-- Eliminar -->
                                        @can('eliminar_planillas')
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $planilla->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Eliminar planilla">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>

                                    <!-- Modal de confirmación para eliminar -->
                                    @can('eliminar_planillas')
                                    <div class="modal fade" id="deleteModal{{ $planilla->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $planilla->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $planilla->id }}">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Confirmar Eliminación
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Está seguro de que desea eliminar esta planilla?</p>
                                                    <div class="alert alert-light border">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-file-pdf text-danger me-3"></i>
                                                            <div>
                                                                <strong>{{ $planilla->nombre_original }}</strong><br>
                                                                <small class="text-muted">Período: {{ $planilla->periodo_pago }} | Año: {{ $planilla->anio }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="text-danger mb-0">
                                                        <small>
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Esta acción no se puede deshacer.
                                                        </small>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('planillas-pdf.destroy', $planilla->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash me-2"></i>Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Mostrando {{ $planillas->firstItem() }} - {{ $planillas->lastItem() }} de {{ $planillas->total() }} registros
                    </div>
                    {{ $planillas->links() }}
                </div>
            </div>
        </div>
        @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-file-pdf text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted mb-3">No hay planillas cargadas</h4>
                <p class="text-muted mb-4">Comienza subiendo tu primera planilla PDF al sistema.</p>
                @can('crear_planillas')
                <a href="{{ route('planillas-pdf.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-upload me-2"></i>Subir Primera Planilla
                </a>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-lock me-2"></i>
                    No tienes permisos para subir planillas. Contacta al administrador.
                </div>
                @endcan
            </div>
        </div>
        @endif
    @else
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="fas fa-ban text-danger" style="font-size: 4rem;"></i>
            </div>
            <h4 class="text-danger mb-3">Acceso Denegado</h4>
            <p class="text-muted mb-4">No tienes permisos para ver las planillas.</p>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Contacta al administrador para solicitar acceso.
            </div>
        </div>
    </div>
    @endcan
</div>

<style>
.card {
    border-radius: 12px;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.btn-group .btn {
    border-radius: 6px !important;
    margin: 0 2px;
}

.badge {
    font-size: 0.75rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.04);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.pagination {
    margin-bottom: 0;
}

.dropdown-toggle::after {
    margin-left: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Confirmación adicional para eliminar
    const deleteForms = document.querySelectorAll('form[action*="destroy"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const fileName = this.closest('.modal-content').querySelector('strong').textContent;
            if (!confirm(`¿Está completamente seguro de eliminar "${fileName}" permanentemente?`)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection
