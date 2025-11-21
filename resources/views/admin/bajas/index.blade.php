@extends('dashboard')

@section('contenido')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-uppercase fw-bold">Listado de Bajas de Personal</h2>
        <a href="{{ route('altasbajas') }}" class="btn btn-success">
            <i class="fas fa-arrow-left me-2"></i>Volver a Altas/Bajas
        </a>
    </div>

    <!-- Filtros Mejorados -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('bajasaltas.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar por nombre</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre, apellidos..."
                        value="{{ request('nombre') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Motivo</label>
                    <select name="motivo" class="form-select">
                        <option value="">Todos los motivos</option>
                        <option value="renuncia" {{ request('motivo') == 'renuncia' ? 'selected' : '' }}>Renuncia</option>
                        <option value="despido" {{ request('motivo') == 'despido' ? 'selected' : '' }}>Despido</option>
                        <option value="jubilacion" {{ request('motivo') == 'jubilacion' ? 'selected' : '' }}>Jubilación</option>
                        <option value="termino_contrato" {{ request('motivo') == 'termino_contrato' ? 'selected' : '' }}>Término de contrato</option>
                        <option value="otros" {{ request('motivo') == 'otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark me-2">
                        <i class="fas fa-filter me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('bajasaltas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas -->
    @if(isset($estadisticas))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $estadisticas['total'] }}</h4>
                            <p class="mb-0">Total Bajas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $estadisticas['este_mes'] }}</h4>
                            <p class="mb-0">Este Mes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $estadisticas['con_pdf'] }}</h4>
                            <p class="mb-0">Con PDF</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-pdf fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $estadisticas['sin_pdf'] }}</h4>
                            <p class="mb-0">Sin PDF</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-excel fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla Mejorada -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-sm" id="tablaBajas">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">N°</th>
                            <th>Nombre Completo</th>
                            <th>CI</th>
                            <th>Fecha Ingreso</th>
                            <th>Fecha Baja</th>
                            <th>Motivo</th>
                            <th>Observación</th>
                            <th>Tiempo en Institución</th>
                            <th>PDF</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bajas as $key => $baja)
                            <tr>
                                <td class="fw-bold">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($baja['foto'] && file_exists(public_path('storage/' . $baja['foto'])))
                                            <img src="{{ asset('storage/' . $baja['foto']) }}"
                                                 class="rounded-circle me-2"
                                                 width="32" height="32"
                                                 alt="{{ $baja['nombre'] }}">
                                        @else
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <span>{{ $baja['nombre'] }}</span>
                                    </div>
                                </td>
                                <td>{{ $baja['ci'] ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($baja['fecha_ingreso'])->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-danger">
                                        {{ \Carbon\Carbon::parse($baja['fecha_baja'])->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $baja['motivo'] }}</span>
                                </td>
                                <td>
                                    @if($baja['observacion'])
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;"
                                              title="{{ $baja['observacion'] }}">
                                            {{ $baja['observacion'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin observación</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $baja['tiempo_en_institucion'] }}</span>
                                </td>
                                <td>
                                    @if($baja['pdfbaja'])
                                        <a href="{{ asset('storage/' . $baja['pdfbaja']) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-danger"
                                           title="Ver PDF">
                                            <i class="fas fa-file-pdf"></i> Ver
                                        </a>
                                    @else
                                        <span class="text-muted small">Sin PDF</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Botón Ver Detalles -->
                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalVer{{ $key }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Botón Editar -->
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditar{{ $key }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Botón Eliminar -->
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEliminar{{ $key }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Ver Detalles -->
                            <div class="modal fade" id="modalVer{{ $key }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">
                                                <i class="fas fa-eye me-2"></i>Detalles de Baja
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Información Personal</h6>
                                                    <p><strong>Nombre:</strong> {{ $baja['nombre'] }}</p>
                                                    <p><strong>CI:</strong> {{ $baja['ci'] ?? 'N/A' }}</p>
                                                    <p><strong>Fecha Nacimiento:</strong>
                                                        {{ $baja['fecha_nacimiento'] ? \Carbon\Carbon::parse($baja['fecha_nacimiento'])->format('d/m/Y') : 'N/A' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Información de Baja</h6>
                                                    <p><strong>Fecha Ingreso:</strong>
                                                        {{ \Carbon\Carbon::parse($baja['fecha_ingreso'])->format('d/m/Y') }}
                                                    </p>
                                                    <p><strong>Fecha Baja:</strong>
                                                        {{ \Carbon\Carbon::parse($baja['fecha_baja'])->format('d/m/Y') }}
                                                    </p>
                                                    <p><strong>Tiempo:</strong> {{ $baja['tiempo_en_institucion'] }}</p>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <p><strong>Motivo:</strong> {{ $baja['motivo'] }}</p>
                                                    <p><strong>Observación:</strong>
                                                        {{ $baja['observacion'] ?: 'Sin observación' }}
                                                    </p>
                                                    @if($baja['pdfbaja'])
                                                        <p><strong>Documento:</strong></p>
                                                        <a href="{{ asset('storage/' . $baja['pdfbaja']) }}"
                                                           target="_blank"
                                                           class="btn btn-danger">
                                                            <i class="fas fa-file-pdf me-1"></i>Ver PDF de Baja
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Editar -->
                            <div class="modal fade" id="modalEditar{{ $key }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('bajasaltas.update', $baja['id']) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">Editar Baja</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Fecha de Baja *</label>
                                                    <input type="date" name="fecha" class="form-control"
                                                           value="{{ \Carbon\Carbon::parse($baja['fecha_baja'])->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Motivo *</label>
                                                    <select name="motivo" class="form-select" required>
                                                        <option value="renuncia" {{ $baja['motivo'] == 'renuncia' ? 'selected' : '' }}>Renuncia</option>
                                                        <option value="despido" {{ $baja['motivo'] == 'despido' ? 'selected' : '' }}>Despido</option>
                                                        <option value="jubilacion" {{ $baja['motivo'] == 'jubilacion' ? 'selected' : '' }}>Jubilación</option>
                                                        <option value="termino_contrato" {{ $baja['motivo'] == 'termino_contrato' ? 'selected' : '' }}>Término de contrato</option>
                                                        <option value="otros" {{ $baja['motivo'] == 'otros' ? 'selected' : '' }}>Otros</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Observaciones</label>
                                                    <textarea name="observacion" class="form-control" rows="3">{{ $baja['observacion'] }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">PDF de Baja (Actualizar)</label>
                                                    <input type="file" name="pdffile" class="form-control" accept=".pdf">
                                                    @if($baja['pdfbaja'])
                                                        <div class="form-text">
                                                            <a href="{{ asset('storage/' . $baja['pdfbaja']) }}" target="_blank">
                                                                Ver PDF actual
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Eliminar -->
                            <div class="modal fade" id="modalEliminar{{ $key }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Estás seguro de que deseas eliminar la baja de <strong>{{ $baja['nombre'] }}</strong>?</p>
                                            <p class="text-muted">Esta acción no se puede deshacer.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('bajasaltas.destroy', $baja['id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay registros de bajas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($bajas instanceof \Illuminate\Pagination\LengthAwarePaginator && $bajas->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $bajas->firstItem() }} - {{ $bajas->lastItem() }} de {{ $bajas->total() }} registros
                    </div>
                    {{ $bajas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Scripts adicionales -->
@section('scripts')
<script>
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Confirmación antes de eliminar
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('form[action*="destroy"]');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection

@endsection
