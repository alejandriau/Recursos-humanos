@extends('layouts.baseadm')

@section('title', 'Detalles de Inmovilidad')

@section('contenido')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Detalles de Inmovilidad #{{ $inmovilidad->id }}
                </h3>
                <div>
                    @if($inmovilidad->estado == 'pendiente')
                        <button class="btn btn-success" onclick="aprobarInmovilidad()">
                            <i class="fas fa-check"></i> Aprobar
                        </button>
                        <button class="btn btn-danger" onclick="rechazarInmovilidad()">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    @endif
                    @if($inmovilidad->estado == 'aprobado' && $inmovilidad->fecha_fin_inmovilidad < now())
                        <button class="btn btn-secondary" onclick="finalizarInmovilidad()">
                            <i class="fas fa-flag-checkered"></i> Finalizar
                        </button>
                    @endif
                    <a href="{{ route('rrhh.inmovilidades.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Información de la Persona</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Nombres:</th>
                                    <td>{{ $inmovilidad->situacion->persona->nombres ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Apellidos:</th>
                                    <td>{{ $inmovilidad->situacion->persona->apellidos ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Documento:</th>
                                    <td>{{ $inmovilidad->situacion->persona->documento_identidad ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Nacimiento:</th>
                                    <td>{{ $inmovilidad->situacion->persona->fecha_nacimiento ? \Carbon\Carbon::parse($inmovilidad->situacion->persona->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Información de la Inmovilidad</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Tipo:</th>
                                    <td>
                                        <span class="badge badge-info">{{ $inmovilidad->tipo_inmovilidad_label }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @switch($inmovilidad->estado)
                                            @case('pendiente')
                                                <span class="badge badge-warning">Pendiente de Aprobación</span>
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
                                </tr>
                                <tr>
                                    <th>Fecha Inicio:</th>
                                    <td>{{ \Carbon\Carbon::parse($inmovilidad->fecha_inicio_inmovilidad)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Fin:</th>
                                    <td>{{ \Carbon\Carbon::parse($inmovilidad->fecha_fin_inmovilidad)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Días Restantes:</th>
                                    <td>
                                        @php $dias = $inmovilidad->getDiasRestantes(); @endphp
                                        <span class="font-weight-bold {{ $dias <= 15 ? 'text-danger' : ($dias <= 30 ? 'text-warning' : 'text-success') }}">
                                            {{ $dias }} días
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Renovable:</th>
                                    <td>{{ $inmovilidad->renovable ? 'Sí' : 'No' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Base Legal</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">Norma Legal:</th>
                                    <td>{{ $inmovilidad->norma_legal }}</td>
                                </tr>
                                <tr>
                                    <th>Artículo:</th>
                                    <td>{{ $inmovilidad->articulo }}</td>
                                </tr>
                                <tr>
                                    <th>N° Resolución RRHH:</th>
                                    <td>{{ $inmovilidad->numero_resolucion_rrhh }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Resolución:</th>
                                    <td>{{ \Carbon\Carbon::parse($inmovilidad->fecha_resolucion_rrhh)->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentos -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Documentos</h5>
                        </div>
                        <div class="card-body">
                            @if($inmovilidad->resolucion_path)
                                <div class="mb-2">
                                    <i class="fas fa-file-pdf"></i>
                                    <a href="{{ Storage::url($inmovilidad->resolucion_path) }}" target="_blank">
                                        Ver Resolución
                                    </a>
                                </div>
                            @endif
                            @if($inmovilidad->solicitud_path)
                                <div class="mb-2">
                                    <i class="fas fa-file-alt"></i>
                                    <a href="{{ Storage::url($inmovilidad->solicitud_path) }}" target="_blank">
                                        Ver Solicitud
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">Aprobación</h5>
                        </div>
                        <div class="card-body">
                            @if($inmovilidad->aprobado_por)
                                <table class="table table-sm">
                                    <tr>
                                        <th>Aprobado por:</th>
                                        <td>{{ $inmovilidad->aprobadoPor->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha Aprobación:</th>
                                        <td>{{ $inmovilidad->fecha_aprobacion ? \Carbon\Carbon::parse($inmovilidad->fecha_aprobacion)->format('d/m/Y H:i') : 'N/A' }}</td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted">Pendiente de aprobación</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos específicos según tipo -->
            @if($inmovilidad->situacion->discapacidad->count() > 0)
                @include('rrhh.inmovilidades.partials.discapacidad', ['discapacidad' => $inmovilidad->situacion->discapacidad->first()])
            @endif

            @if($inmovilidad->situacion->dependienteDiscapacitado->count() > 0)
                @include('rrhh.inmovilidades.partials.dependiente', ['dependiente' => $inmovilidad->situacion->dependienteDiscapacitado->first()])
            @endif

            @if($inmovilidad->situacion->periodoTemporal->count() > 0)
                @include('rrhh.inmovilidades.partials.periodo', ['periodo' => $inmovilidad->situacion->periodoTemporal->first()])
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function aprobarInmovilidad() {
    Swal.fire({
        title: '¿Aprobar inmovilidad?',
        text: '¿Está seguro de aprobar esta solicitud?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar',
        input: 'textarea',
        inputPlaceholder: 'Observaciones (opcional)'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('observaciones', result.value || '');

            fetch('{{ route("rrhh.inmovilidades.aprobar", $inmovilidad) }}', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire('Aprobado', data.message, 'success').then(() => {
                          location.reload();
                      });
                  } else {
                      Swal.fire('Error', data.message, 'error');
                  }
              });
        }
    });
}

function rechazarInmovilidad() {
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

            fetch('{{ route("rrhh.inmovilidades.rechazar", $inmovilidad) }}', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire('Rechazado', data.message, 'success').then(() => {
                          location.reload();
                      });
                  } else {
                      Swal.fire('Error', data.message, 'error');
                  }
              });
        }
    });
}

function finalizarInmovilidad() {
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

            fetch('{{ route("rrhh.inmovilidades.finalizar", $inmovilidad) }}', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire('Finalizado', data.message, 'success').then(() => {
                          location.reload();
                      });
                  } else {
                      Swal.fire('Error', data.message, 'error');
                  }
              });
        }
    });
}
</script>
@endpush
@endsection
