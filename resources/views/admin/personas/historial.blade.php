@extends('dashboard')

@section('contenidouno')
    <meta content="Historial de puestos" name="description">
    <title>Historial de Puestos - {{ $persona->nombre }} {{ $persona->apellidoPat }}</title>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">Historial de Puestos</h1>
                        <h2 class="h5 text-muted">{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h2>
                        <p class="mb-0">CI: {{ $persona->ci }} | Código: {{ $persona->codigo }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('personas.show', $persona->id) }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarHistorial">
                            <i class="fa fa-plus"></i> Agregar Puesto
                        </button>
                    </div>
                </div>

                <!-- Puesto Actual -->
                @if($persona->puestoActual)
                <div class="card border-success mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fa fa-briefcase"></i> Puesto Actual</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Item:</strong> {{ $persona->puestoActual->puesto->item ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Puesto:</strong> {{ $persona->puestoActual->puesto->nombre ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Unidad:</strong> {{ $persona->puestoActual->puesto->unidadOrganizacional->nombre ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Nivel:</strong> {{ $persona->puestoActual->puesto->nivelJerarquico ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Historial de Puestos -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-history"></i> Historial de Puestos Anteriores</h5>
                    </div>
                    <div class="card-body">
                        @if($persona->historialPuestos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Puesto</th>
                                        <th>Unidad Organizacional</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Duración</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($persona->historialPuestos->sortByDesc('fecha_inicio') as $index => $historial)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $historial->puesto->item ?? 'N/A' }}</td>
                                        <td>{{ $historial->puesto->nombre ?? 'N/A' }}</td>
                                        <td>{{ $historial->puesto->unidadOrganizacional->nombre ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($historial->fecha_inicio)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($historial->fecha_fin)
                                                {{ \Carbon\Carbon::parse($historial->fecha_fin)->format('d/m/Y') }}
                                            @else
                                                <span class="badge bg-warning">Actual</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $fin = $historial->fecha_fin ? \Carbon\Carbon::parse($historial->fecha_fin) : \Carbon\Carbon::now();
                                                $duracion = $fin->diff(\Carbon\Carbon::parse($historial->fecha_inicio));
                                            @endphp
                                            {{ $duracion->y > 0 ? $duracion->y . ' años ' : '' }}
                                            {{ $duracion->m > 0 ? $duracion->m . ' meses ' : '' }}
                                            {{ $duracion->y == 0 && $duracion->m == 0 ? $duracion->d . ' días' : '' }}
                                        </td>
                                        <td>{{ $historial->observaciones ?? 'Sin observaciones' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarHistorial"
                                                    data-historial-id="{{ $historial->id }}"
                                                    data-puesto-id="{{ $historial->puesto_id }}"
                                                    data-fecha-inicio="{{ $historial->fecha_inicio }}"
                                                    data-fecha-fin="{{ $historial->fecha_fin }}"
                                                    data-observaciones="{{ $historial->observaciones }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <form action="{{ route('personas.historial.destroy', [$persona->id, $historial->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('¿Está seguro de eliminar este registro del historial?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay registros en el historial</h5>
                            <p class="text-muted">Agregue el primer puesto al historial usando el botón "Agregar Puesto"</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Historial -->
<div class="modal fade" id="modalAgregarHistorial" tabindex="-1" aria-labelledby="modalAgregarHistorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarHistorialLabel">Agregar al Historial de Puestos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="puesto_id" class="form-label">Puesto *</label>
                            <select class="form-select" id="puesto_id" name="puesto_id" required>
                                <option value="">Seleccionar puesto</option>
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}">
                                        {{ $puesto->item }} - {{ $puesto->nombre }} ({{ $puesto->unidadOrganizacional->nombre }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="unidad_organizacional" class="form-label">Unidad Organizacional</label>
                            <input type="text" class="form-control" id="unidad_organizacional" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio *</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                            <div class="form-text">Dejar vacío si es el puesto actual</div>
                        </div>
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                      placeholder="Observaciones adicionales..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Historial -->
<div class="modal fade" id="modalEditarHistorial" tabindex="-1" aria-labelledby="modalEditarHistorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditarHistorial" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarHistorialLabel">Editar Registro del Historial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_puesto_id" class="form-label">Puesto *</label>
                            <select class="form-select" id="edit_puesto_id" name="puesto_id" required>
                                <option value="">Seleccionar puesto</option>
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}">
                                        {{ $puesto->item }} - {{ $puesto->nombre }} ({{ $puesto->unidadOrganizacional->nombre }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_unidad_organizacional" class="form-label">Unidad Organizacional</label>
                            <input type="text" class="form-control" id="edit_unidad_organizacional" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_fecha_inicio" class="form-label">Fecha Inicio *</label>
                            <input type="date" class="form-control" id="edit_fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="edit_fecha_fin" name="fecha_fin">
                            <div class="form-text">Dejar vacío si es el puesto actual</div>
                        </div>
                        <div class="col-12">
                            <label for="edit_observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="edit_observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card-header {
        font-weight: 600;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .badge {
        font-size: 0.75em;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Actualizar unidad organizacional cuando se selecciona un puesto
    $('#puesto_id, #edit_puesto_id').on('change', function() {
        const puestoId = $(this).val();
        const isEdit = $(this).attr('id') === 'edit_puesto_id';
        const prefix = isEdit ? 'edit_' : '';

        if (puestoId) {
            // En una implementación real, aquí harías una petición AJAX para obtener los datos del puesto
            // Por ahora, asumimos que los datos están en el option seleccionado
            const selectedOption = $(this).find('option:selected');
            const texto = selectedOption.text();
            // Extraer la unidad del texto (asumiendo el formato: "item - nombre (unidad)")
            const match = texto.match(/\((.*?)\)/);
            if (match) {
                $('#' + prefix + 'unidad_organizacional').val(match[1]);
            }
        } else {
            $('#' + prefix + 'unidad_organizacional').val('');
        }
    });

    // Configurar modal de edición
    $('#modalEditarHistorial').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const historialId = button.data('historial-id');
        const puestoId = button.data('puesto-id');
        const fechaInicio = button.data('fecha-inicio');
        const fechaFin = button.data('fecha-fin');
        const observaciones = button.data('observaciones');

        const modal = $(this);
        modal.find('#formEditarHistorial').attr('action', '{{ url("personas") }}/{{ $persona->id }}/historial/' + historialId);

        modal.find('#edit_puesto_id').val(puestoId);
        modal.find('#edit_fecha_inicio').val(fechaInicio);
        modal.find('#edit_fecha_fin').val(fechaFin);
        modal.find('#edit_observaciones').val(observaciones);

        // Disparar el cambio para actualizar la unidad organizacional
        modal.find('#edit_puesto_id').trigger('change');
    });

    // Validación de fechas
    $('#fecha_fin, #edit_fecha_fin').on('change', function() {
        const fechaInicio = $(this).closest('.modal').find('input[name="fecha_inicio"]').val();
        const fechaFin = $(this).val();

        if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
            alert('La fecha fin no puede ser anterior a la fecha inicio');
            $(this).val('');
        }
    });
});
</script>

@if (session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#28a745',
        color: '#fff',
    });
</script>
@endif
@endsection
