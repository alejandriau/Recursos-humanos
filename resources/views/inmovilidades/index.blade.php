@extends('layouts.baseadm')

@section('title', 'Gestión de Inmovilidades Laborales')

@section('contenido')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-wheelchair"></i> Gestión de Inmovilidades Laborales
                </h3>
                <a href="{{ route('rrhh.inmovilidades.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Inmovilidad
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filtro_estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filtro_tipo" class="form-control">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposInmovilidad ?? [] as $tipo)
                            <option value="{{ $tipo }}">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="buscar_persona" class="form-control" placeholder="Buscar por persona...">
                </div>
                <div class="col-md-3">
                    <button id="btn_filtrar" class="btn btn-info btn-block">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </div>

            <!-- Tabla de inmovilidades -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Persona</th>
                            <th>Documento</th>
                            <th>Tipo de Inmovilidad</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Días Restantes</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inmovilidades as $inmovilidad)
                            <tr>
                                <td>{{ $inmovilidad->id }}</td>
                                <td>
                                    <strong>{{ $inmovilidad->situacion->persona->apellidos ?? '' }},
                                    {{ $inmovilidad->situacion->persona->nombres ?? '' }}</strong>
                                </td>
                                <td>{{ $inmovilidad->situacion->persona->documento_identidad ?? '' }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $inmovilidad->tipo_inmovilidad_label }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($inmovilidad->fecha_inicio_inmovilidad)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($inmovilidad->fecha_fin_inmovilidad)->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $diasRestantes = $inmovilidad->getDiasRestantes();
                                        $clase = $diasRestantes <= 15 ? 'text-danger' : ($diasRestantes <= 30 ? 'text-warning' : 'text-success');
                                    @endphp
                                    <span class="{{ $clase }} font-weight-bold">
                                        {{ $diasRestantes }} días
                                    </span>
                                </td>
                                <td>
                                    @switch($inmovilidad->estado)
                                        @case('pendiente')
                                            <span class="badge badge-warning">Pendiente</span>
                                            @break
                                        @case('aprobado')
                                            <span class="badge badge-success">Aprobado</span>
                                            @break
                                        @case('rechazado')
                                            <span class="badge badge-danger">Rechazado</span>
                                            @break
                                        @case('finalizado')
                                            <span class="badge badge-secondary">Finalizado</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('rrhh.inmovilidades.show', $inmovilidad) }}"
                                           class="btn btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($inmovilidad->estado == 'pendiente')
                                            <button type="button"
                                                    class="btn btn-success"
                                                    onclick="aprobarInmovilidad({{ $inmovilidad->id }})"
                                                    title="Aprobar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-danger"
                                                    onclick="rechazarInmovilidad({{ $inmovilidad->id }})"
                                                    title="Rechazar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        @if($inmovilidad->estado == 'aprobado' && $inmovilidad->fecha_fin_inmovilidad < now())
                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    onclick="finalizarInmovilidad({{ $inmovilidad->id }})"
                                                    title="Finalizar">
                                                <i class="fas fa-flag-checkered"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No hay inmovilidades registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $inmovilidades->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Filtrar
    $('#btn_filtrar').click(function() {
        let estado = $('#filtro_estado').val();
        let tipo = $('#filtro_tipo').val();
        let busqueda = $('#buscar_persona').val();

        window.location.href = '{{ route("rrhh.inmovilidades.index") }}' +
            '?estado=' + estado + '&tipo=' + tipo + '&busqueda=' + busqueda;
    });
});

function aprobarInmovilidad(id) {
    Swal.fire({
        title: '¿Aprobar inmovilidad?',
        text: '¿Está seguro de aprobar esta solicitud de inmovilidad?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar',
        input: 'textarea',
        inputPlaceholder: 'Observaciones (opcional)',
        inputAttributes: {
            'aria-label': 'Observaciones'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('observaciones', result.value || '');

            fetch('{{ url("rrhh/inmovilidades") }}/' + id + '/aprobar', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire('Aprobado', data.message, 'success');
                      location.reload();
                  } else {
                      Swal.fire('Error', data.message, 'error');
                  }
              });
        }
    });
}

function rechazarInmovilidad(id) {
    Swal.fire({
        title: '¿Rechazar inmovilidad?',
        text: 'Por favor, indique el motivo del rechazo:',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar',
        input: 'textarea',
        inputPlaceholder: 'Motivo del rechazo...',
        inputValidator: (value) => {
            if (!value) {
                return 'Debe indicar el motivo del rechazo';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('motivo_rechazo', result.value);

            fetch('{{ url("rrhh/inmovilidades") }}/' + id + '/rechazar', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire('Rechazado', data.message, 'success');
                      location.reload();
                  } else {
                      Swal.fire('Error', data.message, 'error');
                  }
              });
        }
    });
}

function finalizarInmovilidad(id) {
    Swal.fire({
        title: '¿Finalizar inmovilidad?',
        text: '¿Está seguro de finalizar esta inmovilidad?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ url("rrhh/inmovilidades") }}/' + id + '/finalizar', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire('Finalizado', data.message, 'success');
                      location.reload();
                  } else {
                      Swal.fire('Error', data.message, 'error');
                  }
              });
        }
    });
}
</script>
@endpush

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }
</style>
@endpush
@endsection
