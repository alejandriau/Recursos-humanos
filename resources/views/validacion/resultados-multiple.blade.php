@extends('dashboard')

@section('title', 'Resultados de Validación Múltiple')
@section('header', 'Resultados de Validación Múltiple')

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2 text-primary"></i>
                        Puesto: {{ $puesto->denominacion }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Se evaluaron {{ count($resultados) }} candidatos para este puesto.
                        <strong>{{ collect($resultados)->where('resultado.cumple', true)->count() }}</strong> cumplen con el perfil.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaResultados">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Candidato</th>
                                    <th>Profesión</th>
                                    <th>Experiencia</th>
                                    <th>Formación</th>
                                    <th>Provisión</th>
                                    <th>Resultado Global</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultados as $index => $item)
                                    @php
                                        $resultado = $item['resultado'];
                                        $persona = $item['persona'];
                                        $detalle = $resultado['detalle'] ?? [];
                                    @endphp
                                    <tr class="{{ $resultado['cumple'] ? 'table-success' : 'table-danger' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $persona->nombre_completo }}</strong>
                                        </td>
                                        <td>
                                            @if($persona->profesionPrincipal)
                                                {{ $persona->profesionPrincipal->carrera->nombre ?? 'N/A' }}
                                                <br><small class="text-muted">{{ $persona->profesionPrincipal->universidad ?? '' }}</small>
                                            @else
                                                <span class="text-muted">No registrada</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($detalle['experiencia']['detalle']))
                                                @if(is_array($detalle['experiencia']['detalle']))
                                                    <span class="badge {{ ($detalle['experiencia']['detalle']['anios_experiencia'] ?? 0) >= ($puesto->perfilRequisitos->aniosExperienciaMinimos ?? 0) ? 'bg-success' : 'bg-warning' }}">
                                                        {{ number_format($detalle['experiencia']['detalle']['anios_experiencia'] ?? 0, 1) }} años
                                                    </span>
                                                @else
                                                    {{ $detalle['experiencia']['detalle'] }}
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($detalle['formacion']['cumple']))
                                                @if($detalle['formacion']['cumple'])
                                                    <span class="badge bg-success">✓ Cumple</span>
                                                @else
                                                    <span class="badge bg-danger">✗ No cumple</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">
                                                    {{ $detalle['formacion']['detalle']['carrera'] ?? 'N/A' }}
                                                </small>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($detalle['titulo_provision']['cumple']))
                                                @if($detalle['titulo_provision']['cumple'])
                                                    <span class="badge bg-success">✓ Tiene</span>
                                                @else
                                                    <span class="badge bg-danger">✗ No tiene</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($resultado['cumple'])
                                                <span class="badge-cumple">
                                                    <i class="fas fa-check-circle me-1"></i> CUMPLE
                                                </span>
                                            @else
                                                <span class="badge-no-cumple">
                                                    <i class="fas fa-times-circle me-1"></i> NO CUMPLE
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-info btn-ver-detalle" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalDetalle"
                                                    data-persona="{{ $persona->nombre_completo }}"
                                                    data-detalle='@json($detalle)'>
                                                <i class="fas fa-eye"></i> Ver Detalle
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('validacion.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Nueva Validación
                    </a>
                    <button onclick="window.print()" class="btn btn-info ms-2">
                        <i class="fas fa-print me-1"></i> Imprimir
                    </button>
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
        $('#tablaResultados').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[6, 'desc']],
            pageLength: 10
        });
        
        $('.btn-ver-detalle').click(function() {
            var persona = $(this).data('persona');
            var detalle = $(this).data('detalle');
            
            var html = '<h6>Candidato: ' + persona + '</h6><hr>';
            
            if (detalle.formacion) {
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
            
            if (detalle.experiencia) {
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
            
            if (detalle.titulo_provision) {
                html += '<div class="mb-3">';
                html += '<strong><i class="fas fa-file-certificate"></i> Título en Provisión Nacional:</strong><br>';
                html += 'Estado: ' + (detalle.titulo_provision.cumple ? '<span class="badge bg-success">Cumple</span>' : '<span class="badge bg-danger">No cumple</span>') + '<br>';
                html += detalle.titulo_provision.detalle;
                html += '</div>';
            }
            
            $('#modalDetalleBody').html(html);
        });
    });
</script>
@endpush
@endsection