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
        /* Estilos para prevenir parpadeo del modal */
        .modal {
            z-index: 1060 !important;
        }
        .dropdown-menu {
            z-index: 1000 !important;
        }
        .pagination-wrapper {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
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
                <!--<a href="{{ route('historial.estadisticas') }}" class="btn btn-outline-info">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas
                </a>-->
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
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                                        <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start"
                                                                onclick="return confirm('¿Está seguro de concluir esta designación?')">
                                                            <i class="fas fa-times-circle me-2"></i>Concluir Designación
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" type="button"
                                                            onclick="abrirModalBaja({{ $puesto->historial_actual->id }}, '{{ $puesto->persona->id }}', '{{ $puesto->persona->apellidoPat }}', '{{ $puesto->persona->apellidoMat }}', '{{ $puesto->persona->nombre }}')">
                                                        <i class="fas fa-user-times me-2"></i>Dar de Baja
                                                    </button>
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

            <!-- PAGINACIÓN -->
            <div class="pagination-wrapper">
                {{ $puestos->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL GLOBAL FUERA DEL BUCLE Y FUERA DE LA TABLA -->
<div class="modal fade" id="bajaModalGlobal" tabindex="-1" aria-labelledby="bajaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('altasbajas.store') }}" enctype="multipart/form-data" id="bajaFormGlobal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bajaModalLabel">Dar de baja a: <span id="nombrePersona"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idPersona" id="modalIdPersona">
                    <input type="hidden" name="apellidopaterno" id="modalApellidoPaterno">
                    <input type="hidden" name="apellidomaterno" id="modalApellidoMaterno">
                    <input type="hidden" name="nombre" id="modalNombre">
                    <input type="hidden" name="idHistorial" id="modalHistorial">

                    <div class="mb-3">
                        <label class="form-label">Fecha de retiro *</label>
                        <input type="date" name="fechafin" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo *</label>
                        <input type="text" name="motivo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="obser" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PDF (Renuncia)</label>
                        <input type="file" name="pdffile" class="form-control" accept=".pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Guardar Baja</button>
                </div>
            </form>
        </div>
    </div>
</div>
@if (session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true,
        background: '#007BFF', // azul
        color: '#fff', // texto blanco
        customClass: {
            popup: 'custom-toast'
        },
    });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: '{{ session('error') }}',
        showConfirmButton: false,
        timer: 3000, // Más tiempo para errores
        timerProgressBar: true,
        background: '#dc3545', // rojo
        color: '#fff',
        customClass: {
            popup: 'custom-toast'
        },
    });
</script>
@endif

@if ($errors->any())
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Errores en el formulario',
        html: `@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
        showConfirmButton: false,
        timer: 5000, // Aún más tiempo para múltiples errores
        timerProgressBar: true,
        background: '#dc3545',
        color: '#fff',
        customClass: {
            popup: 'custom-toast custom-toast-large'
        },
    });
</script>
@endif

@if (session('warning'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'warning',
        title: '{{ session('warning') }}',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        background: '#ffc107', // amarillo
        color: '#000', // texto negro para mejor contraste
        customClass: {
            popup: 'custom-toast'
        },
    });
</script>
@endif

@if (session('info'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'info',
        title: '{{ session('info') }}',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        background: '#17a2b8', // azul info
        color: '#fff',
        customClass: {
            popup: 'custom-toast'
        },
    });
</script>
@endif

<!-- Estilos personalizados -->
<style>
    .swal2-popup.custom-toast {
        width: 300px !important;
        height: 80px !important;
        border-radius: 12px;
        font-size: 16px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }

    .swal2-popup.custom-toast-large {
        width: 400px !important;
        height: auto !important;
        min-height: 100px;
    }

    .swal2-popup.custom-toast-large p {
        margin: 5px 0;
        font-size: 14px;
    }
</style>

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

function abrirModalBaja(historialId, personaId, apellidoPat, apellidoMat, nombre) {
    // Cerrar dropdown primero
    var dropdown = document.querySelector('.dropdown-menu.show');
    if (dropdown) {
        var bsDropdown = bootstrap.Dropdown.getInstance(dropdown.closest('.dropdown').querySelector('.dropdown-toggle'));
        if (bsDropdown) {
            bsDropdown.hide();
        }
    }

    // Configurar los datos en el modal
    document.getElementById('modalIdPersona').value = personaId;
    document.getElementById('modalApellidoPaterno').value = apellidoPat;
    document.getElementById('modalApellidoMaterno').value = apellidoMat;
    document.getElementById('modalNombre').value = nombre;
    document.getElementById('modalHistorial').value = historialId;
    document.getElementById('nombrePersona').textContent = apellidoPat + ' ' + apellidoMat + ' ' + nombre;

    // Mostrar el modal
    var modal = new bootstrap.Modal(document.getElementById('bajaModalGlobal'));
    modal.show();
}

// Limpiar el modal cuando se cierre
document.getElementById('bajaModalGlobal').addEventListener('hidden.bs.modal', function () {
    // Resetear el formulario
    document.getElementById('bajaFormGlobal').reset();
    document.getElementById('modalIdPersona').value = '';
    document.getElementById('modalApellidoPaterno').value = '';
    document.getElementById('modalApellidoMaterno').value = '';
    document.getElementById('modalNombre').value = '';
    document.getElementById('nombrePersona').textContent = '';
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('Vista de designaciones cargada correctamente');
});
</script>
@endsection
