@extends('dashboard')

@section('title', 'Historial de Validaciones')
@section('header', 'Historial de Validaciones de Perfil')

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Registro de Validaciones Realizadas
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Puesto</label>
                                <select name="idPuesto" class="form-select">
                                    <option value="">Todos los puestos</option>
                                    @foreach($puestos as $p)
                                        <option value="{{ $p->id }}" {{ request('idPuesto') == $p->id ? 'selected' : '' }}>
                                            {{ $p->denominacion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Candidato</label>
                                <select name="idPersona" class="form-select">
                                    <option value="">Todos los candidatos</option>
                                    @foreach($personas as $per)
                                        <option value="{{ $per->id }}" {{ request('idPersona') == $per->id ? 'selected' : '' }}>
                                            {{ $per->nombre_completo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Resultado</label>
                                <select name="resultado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('resultado') === '1' ? 'selected' : '' }}>Cumple</option>
                                    <option value="0" {{ request('resultado') === '0' ? 'selected' : '' }}>No Cumple</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaHistorial">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Puesto</th>
                                    <th>Candidato</th>
                                    <th>Resultado</th>
                                    <th>Validado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($validaciones as $validacion)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($validacion->fechaValidacion)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $validacion->puesto->denominacion ?? 'N/A' }}</td>
                                        <td>{{ $validacion->persona->nombre_completo ?? 'N/A' }}</td>
                                        <td>
                                            @if($validacion->resultado)
                                                <span class="badge-cumple">
                                                    <i class="fas fa-check me-1"></i> CUMPLE
                                                </span>
                                            @else
                                                <span class="badge-no-cumple">
                                                    <i class="fas fa-times me-1"></i> NO CUMPLE
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $validacion->usuario->name ?? 'N/A' }}</td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-info btn-ver-detalle"
                                                    data-detalle='@json($validacion->detalleValidacion)'
                                                    data-puesto="{{ $validacion->puesto->denominacion ?? '' }}"
                                                    data-persona="{{ $validacion->persona->nombre_completo ?? '' }}">
                                                <i class="fas fa-eye"></i> Ver Detalle
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        {{ $validaciones->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Validación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetalleBody">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tablaHistorial').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            pageLength: 15,
            searching: false,
            paging: false,
            info: false
        });
        
        $('.btn-ver-detalle').click(function() {
            var detalle = $(this).data('detalle');
            var puesto = $(this).data('puesto');
            var persona = $(this).data('persona');
            
            var html = '<h6>Puesto: ' + puesto + '</h6>';
            html += '<h6>Candidato: ' + persona + '</h6><hr>';
            
            if (detalle && detalle.formacion) {
                html += '<div class="mb-3">';
                html += '<strong><i class="fas fa-graduation-cap"></i> Formación Académica:</strong><br>';
                html += 'Estado: ' + (detalle.formacion.cumple ? '<span class="badge bg-success">Cumple</span>' : '<span class="badge bg-danger">No cumple</span>') + '<br>';
                if (detalle.formacion.detalle) {
                    html += 'Carrera: ' + (detalle.formacion.detalle.carrera || 'N/A') + '<br>';
                    html += 'Nivel: ' + (detalle.formacion.detalle.nivel_academico || 'N/A') + '<br>';
                    html += 'Área: ' + (detalle.formacion.detalle.area_conocimiento || 'N/A');
                }
                html += '</div>';
            }
            
            if (detalle && detalle.experiencia) {
                html += '<div class="mb-3">';
                html += '<strong><i class="fas fa-clock"></i> Experiencia:</strong><br>';
                html += 'Estado: ' + (detalle.experiencia.cumple ? '<span class="badge bg-success">Cumple</span>' : '<span class="badge bg-danger">No cumple</span>') + '<br>';
                if (detalle.experiencia.detalle && typeof detalle.experiencia.detalle === 'object') {
                    html += 'Fecha Titulación: ' + (detalle.experiencia.detalle.fecha_titulacion || 'N/A') + '<br>';
                    html += 'Años Experiencia: ' + (detalle.experiencia.detalle.anios_experiencia || 0) + '<br>';
                    html += 'Años Requeridos: ' + (detalle.experiencia.detalle.anios_requeridos || 0);
                } else {
                    html += detalle.experiencia.detalle;
                }
                html += '</div>';
            }
            
            if (detalle && detalle.titulo_provision) {
                html += '<div class="mb-3">';
                html += '<strong><i class="fas fa-file-certificate"></i> Título en Provisión Nacional:</strong><br>';
                html += 'Estado: ' + (detalle.titulo_provision.cumple ? '<span class="badge bg-success">Cumple</span>' : '<span class="badge bg-danger">No cumple</span>') + '<br>';
                html += detalle.titulo_provision.detalle;
                html += '</div>';
            }
            
            $('#modalDetalleBody').html(html);
            $('#modalDetalle').modal('show');
        });
    });
</script>
@endpush
@endsection