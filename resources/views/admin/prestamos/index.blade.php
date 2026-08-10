@extends('layouts.baseadm')
@section('contenido')

<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Gestión de Préstamos</h2>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-clock fa-3x text-warning"></i>
                <div class="ms-3">
                    <p class="mb-2">Pendientes</p>
                    <h6 class="mb-0">{{ $stats['pendientes'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-check-circle fa-3x text-info"></i>
                <div class="ms-3">
                    <p class="mb-2">Aprobados</p>
                    <h6 class="mb-0">{{ $stats['aprobados'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-hand-holding-heart fa-3x text-success"></i>
                <div class="ms-3">
                    <p class="mb-2">En Préstamo</p>
                    <h6 class="mb-0">{{ $stats['prestados'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-undo-alt fa-3x text-secondary"></i>
                <div class="ms-3">
                    <p class="mb-2">Devueltos</p>
                    <h6 class="mb-0">{{ $stats['devueltos'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                <div class="ms-3">
                    <p class="mb-2">Vencidos</p>
                    <h6 class="mb-0">{{ $stats['vencidos'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-times-circle fa-3x text-dark"></i>
                <div class="ms-3">
                    <p class="mb-2">Rechazados</p>
                    <h6 class="mb-0">{{ $stats['rechazados'] }}</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas -->
    <ul class="nav nav-tabs mb-4" id="prestamoTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab">
                <i class="fas fa-clock me-2"></i>Solicitudes Pendientes
                @if($stats['pendientes'] > 0)
                    <span class="badge bg-warning ms-2">{{ $stats['pendientes'] }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="activos-tab" data-bs-toggle="tab" data-bs-target="#activos" type="button" role="tab">
                <i class="fas fa-hand-holding-heart me-2"></i>Préstamos Activos
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab">
                <i class="fas fa-history me-2"></i>Historial
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="reporte-tab" data-bs-toggle="tab" data-bs-target="#reporte" type="button" role="tab">
                <i class="fas fa-chart-bar me-2"></i>Reportes
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Solicitudes Pendientes -->
        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-warning">
                        <tr>
                            <th>ID</th>
                            <th>Solicitante</th>
                            <th>Carpeta</th>
                            <th>Fecha Solicitud</th>
                            <th>Fecha Estimada</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendientes as $prestamo)
                        <tr>
                            <td>{{ $prestamo->id }}</td>
                            <td>{{ $prestamo->solicitante->name ?? 'N/A' }}</td>
                            <td>
                                @if($prestamo->carpeta)
                                    {{ $prestamo->carpeta->letra ?? '' }} {{ $prestamo->carpeta->codigo ?? '' }}
                                    <small class="d-block text-muted">
                                        {{ $prestamo->carpeta->nombrecompleto ?? '' }}
                                        <br>
                                        <span class="badge bg-{{ $prestamo->carpeta_type == 'pasivouno' ? 'info' : ($prestamo->carpeta_type == 'pasivodos' ? 'success' : 'primary') }}">
                                            <i class="fas fa-folder me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $prestamo->carpeta_type)) }}
                                        </span>
                                    </small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $prestamo->fecha_solicitud }}</td>
                            <td>{{ $prestamo->fecha_devolucion_estimada ?? 'No definida' }}</td>
                            <td>{{ Str::limit($prestamo->motivo_solicitud, 50) }}</td>
                            <td>
                                <button class="btn btn-sm btn-info btn-ver-detalle" data-id="{{ $prestamo->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success btn-aprobar" data-id="{{ $prestamo->id }}">
                                    <i class="fas fa-check"></i> Aprobar
                                </button>
                                <button class="btn btn-sm btn-danger btn-rechazar" data-id="{{ $prestamo->id }}">
                                    <i class="fas fa-times"></i> Rechazar
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay solicitudes pendientes</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Préstamos Activos -->
        <div class="tab-pane fade" id="activos" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Solicitante</th>
                            <th>Carpeta</th>
                            <th>Fecha Préstamo</th>
                            <th>Devolución Estimada</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activos as $prestamo)
                        @php
                            $diasRestantes = $prestamo->fecha_devolucion_estimada ? 
                                \Carbon\Carbon::parse($prestamo->fecha_devolucion_estimada)->diffInDays(now(), false) : null;
                            $claseVencimiento = $diasRestantes < 0 ? 'text-danger' : ($diasRestantes < 3 ? 'text-warning' : '');
                        @endphp
                        <tr>
                            <td>{{ $prestamo->id }}</td>
                            <td>{{ $prestamo->solicitante->name ?? 'N/A' }}</td>
                            <td>
                                @if($prestamo->carpeta)
                                    {{ $prestamo->carpeta->letra ?? '' }} {{ $prestamo->carpeta->codigo ?? '' }}
                                    <small class="d-block text-muted">
                                        {{ $prestamo->carpeta->nombrecompleto ?? '' }}
                                        <br>
                                        <span class="badge bg-{{ $prestamo->carpeta_type == 'pasivouno' ? 'info' : ($prestamo->carpeta_type == 'pasivodos' ? 'success' : 'primary') }}">
                                            <i class="fas fa-folder me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $prestamo->carpeta_type)) }}
                                        </span>
                                    </small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $prestamo->fecha_prestamo ?? 'Pendiente' }}</td>
                            <td class="{{ $claseVencimiento }}">
                                {{ $prestamo->fecha_devolucion_estimada ?? 'No definida' }}
                                @if($diasRestantes !== null)
                                    <small class="d-block">
                                        @if($diasRestantes < 0)
                                            Vencido hace {{ abs($diasRestantes) }} días
                                        @elseif($diasRestantes == 0)
                                            Vence hoy
                                        @else
                                            Vence en {{ $diasRestantes }} días
                                        @endif
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $prestamo->estado == 'aprobado' ? 'info' : 'success' }}">
                                    {{ $prestamo->estado == 'aprobado' ? 'Aprobado' : 'En préstamo' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info btn-ver-detalle" data-id="{{ $prestamo->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($prestamo->estado == 'aprobado')
                                <button class="btn btn-sm btn-success btn-entregar" data-id="{{ $prestamo->id }}">
                                    <i class="fas fa-hand-holding-heart"></i> Entregar
                                </button>
                                @endif
                                @if($prestamo->estado == 'prestado')
                                <button class="btn btn-sm btn-secondary btn-devolver" data-id="{{ $prestamo->id }}">
                                    <i class="fas fa-undo-alt"></i> Devolver
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay préstamos activos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Historial -->
        <div class="tab-pane fade" id="historial" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Solicitante</th>
                            <th>Carpeta</th>
                            <th>Fecha Solicitud</th>
                            <th>Fecha Devolución</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historial as $prestamo)
                        <tr>
                            <td>{{ $prestamo->id }}</td>
                            <td>{{ $prestamo->solicitante->name ?? 'N/A' }}</td>
                            <td>
                                @if($prestamo->carpeta)
                                    {{ $prestamo->carpeta->letra ?? '' }} {{ $prestamo->carpeta->codigo ?? '' }}
                                    <small class="d-block text-muted">
                                        {{ $prestamo->carpeta->nombrecompleto ?? '' }}
                                        <br>
                                        <span class="badge bg-{{ $prestamo->carpeta_type == 'pasivouno' ? 'info' : ($prestamo->carpeta_type == 'pasivodos' ? 'success' : 'primary') }}">
                                            <i class="fas fa-folder me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $prestamo->carpeta_type)) }}
                                        </span>
                                    </small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $prestamo->fecha_solicitud }}</td>
                            <td>{{ $prestamo->fecha_devolucion_real ?? $prestamo->fecha_devolucion_estimada ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $prestamo->estado == 'devuelto' ? 'secondary' : ($prestamo->estado == 'rechazado' ? 'danger' : 'dark') }}">
                                    {{ ucfirst($prestamo->estado) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info btn-ver-detalle" data-id="{{ $prestamo->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay historial</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reportes -->
        <div class="tab-pane fade" id="reporte" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5>Generar Reporte de Préstamos</h5>
                </div>
                <div class="card-body">
                    <form id="formReporte">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>Fecha Desde</label>
                                <input type="date" name="fecha_desde" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Estado</label>
                                <select name="estado" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="prestado">En préstamo</option>
                                    <option value="devuelto">Devuelto</option>
                                    <option value="rechazado">Rechazado</option>
                                    <option value="vencido">Vencido</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Generar
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="resultadoReporte"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle -->
<div class="modal fade" id="modalDetallePrestamo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalle del Préstamo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detallePrestamoBody">
                <!-- Cargado vía AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para aprobar -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Aprobar Solicitud</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAprobar">
                <div class="modal-body">
                    <input type="hidden" id="aprobar_id" name="id">
                    <div class="mb-3">
                        <label>Notas (opcional)</label>
                        <textarea name="notas_archivero" class="form-control" rows="3" placeholder="Agregar notas sobre la aprobación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Aprobación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Rechazar Solicitud</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRechazar">
                <div class="modal-body">
                    <input type="hidden" id="rechazar_id" name="id">
                    <div class="mb-3">
                        <label>Motivo del rechazo *</label>
                        <textarea name="motivo_rechazo" class="form-control" rows="3" required placeholder="Explica por qué se rechaza la solicitud..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Notas adicionales</label>
                        <textarea name="notas_archivero" class="form-control" rows="2" placeholder="Notas internas..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para entregar -->
<div class="modal fade" id="modalEntregar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Marcar como Entregado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEntregar">
                <div class="modal-body">
                    <input type="hidden" id="entregar_id" name="id">
                    <div class="mb-3">
                        <label>Fecha de entrega *</label>
                        <input type="date" name="fecha_prestamo" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Notas</label>
                        <textarea name="notas_archivero" class="form-control" rows="2" placeholder="Notas sobre la entrega..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Entrega</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para devolver -->
<div class="modal fade" id="modalDevolver" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Marcar como Devuelto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDevolver">
                <div class="modal-body">
                    <input type="hidden" id="devolver_id" name="id">
                    <div class="mb-3">
                        <label>Fecha de devolución *</label>
                        <input type="date" name="fecha_devolucion" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Observaciones</label>
                        <textarea name="observaciones_devolucion" class="form-control" rows="2" placeholder="Estado de la carpeta, observaciones..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-secondary">Confirmar Devolución</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== INICIALIZANDO SISTEMA DE PRÉSTAMOS ===');
    console.log('URL actual:', window.location.href);
    
    // Verificar que los botones existen
    console.log('Botones aprobar encontrados:', $('.btn-aprobar').length);
    console.log('Botones rechazar encontrados:', $('.btn-rechazar').length);
    
    // Ver detalle
    $(document).on('click', '.btn-ver-detalle', function() {
        var id = $(this).data('id');
        console.log('Ver detalle ID:', id);
        console.log('URL a llamar:', '/admin/prestamos/' + id);
        
        $.ajax({
            url: '/admin/prestamos/' + id,
            type: 'GET',
            success: function(response) {
                $('#detallePrestamoBody').html(response);
                $('#modalDetallePrestamo').modal('show');
            },
            error: function(xhr) {
                console.error('Error detalle:', xhr);
                Swal.fire('Error', 'No se pudo cargar el detalle', 'error');
            }
        });
    });

    // Aprobar - Prevenir propagación de eventos
    $(document).on('click', '.btn-aprobar', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        console.log('Aprobar modal abierto con ID:', id);
        console.log('URL que se usará:', '/admin/prestamos/' + id + '/aprobar');
        $('#aprobar_id').val(id);
        $('#modalAprobar').modal('show');
    });

    $('#formAprobar').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $('#aprobar_id').val();
        var notas = $('#formAprobar textarea[name="notas_archivero"]').val();
        var data = {
            notas_archivero: notas,
            _token: '{{ csrf_token() }}'
        };
        
        var urlCompleta = '/admin/prestamos/' + id + '/aprobar';
        console.log('=== ENVIANDO APROBACIÓN ===');
        console.log('ID:', id);
        console.log('URL:', urlCompleta);
        console.log('Datos:', data);
        
        // Deshabilitar botón
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
            url: urlCompleta,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Respuesta éxito:', response);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: true
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message || 'Error al aprobar', 'error');
                    submitBtn.prop('disabled', false).html('Confirmar Aprobación');
                }
                $('#modalAprobar').modal('hide');
            },
            error: function(xhr) {
                console.error('=== ERROR EN APROBAR ===');
                console.error('Status:', xhr.status);
                console.error('StatusText:', xhr.statusText);
                console.error('Response:', xhr.responseText);
                
                let msg = 'Error al procesar la solicitud';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    msg = 'Ruta no encontrada. Verifica que la URL sea correcta: ' + urlCompleta;
                } else if (xhr.status === 500) {
                    msg = 'Error interno del servidor. Revisa los logs de Laravel.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error ' + xhr.status,
                    html: msg,
                    confirmButtonText: 'OK'
                });
                submitBtn.prop('disabled', false).html('Confirmar Aprobación');
            }
        });
    });

    // Rechazar
    $(document).on('click', '.btn-rechazar', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        console.log('Rechazar modal abierto con ID:', id);
        $('#rechazar_id').val(id);
        $('#modalRechazar').modal('show');
    });

    $('#formRechazar').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $('#rechazar_id').val();
        var motivo = $('#formRechazar textarea[name="motivo_rechazo"]').val();
        var notas = $('#formRechazar textarea[name="notas_archivero"]').val();
        
        if (!motivo) {
            Swal.fire('Error', 'El motivo del rechazo es obligatorio', 'error');
            return;
        }
        
        var data = {
            motivo_rechazo: motivo,
            notas_archivero: notas,
            _token: '{{ csrf_token() }}'
        };
        
        var urlCompleta = '/admin/prestamos/' + id + '/rechazar';
        console.log('=== ENVIANDO RECHAZO ===');
        console.log('URL:', urlCompleta);
        
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: urlCompleta,
            type: 'POST',
            data: data,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function(response) {
                console.log('Respuesta éxito:', response);
                if (response.success) {
                    Swal.fire('Éxito', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message || 'Error al rechazar', 'error');
                    submitBtn.prop('disabled', false).html('Confirmar Rechazo');
                }
                $('#modalRechazar').modal('hide');
            },
            error: function(xhr) {
                console.error('Error en rechazar:', xhr);
                Swal.fire('Error', 'Error al procesar la solicitud', 'error');
                submitBtn.prop('disabled', false).html('Confirmar Rechazo');
            }
        });
    });

    // Entregar
    $(document).on('click', '.btn-entregar', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        console.log('Entregar modal abierto con ID:', id);
        $('#entregar_id').val(id);
        $('#modalEntregar').modal('show');
    });

    $('#formEntregar').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $('#entregar_id').val();
        var data = $(this).serialize();
        console.log('Enviando entrega para ID:', id, 'datos:', data);
        
        var urlCompleta = '/admin/prestamos/' + id + '/entregar';
        
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: urlCompleta,
            type: 'POST',
            data: data,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function(response) {
                console.log('Respuesta éxito:', response);
                if (response.success) {
                    Swal.fire('Éxito', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message || 'Error al marcar entregado', 'error');
                    submitBtn.prop('disabled', false).html('Confirmar Entrega');
                }
                $('#modalEntregar').modal('hide');
            },
            error: function(xhr) {
                console.error('Error en entregar:', xhr);
                Swal.fire('Error', 'Error al procesar la solicitud', 'error');
                submitBtn.prop('disabled', false).html('Confirmar Entrega');
            }
        });
    });

    // Devolver
    $(document).on('click', '.btn-devolver', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        console.log('Devolver modal abierto con ID:', id);
        $('#devolver_id').val(id);
        $('#modalDevolver').modal('show');
    });

    $('#formDevolver').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $('#devolver_id').val();
        var data = $(this).serialize();
        console.log('Enviando devolución para ID:', id, 'datos:', data);
        
        var urlCompleta = '/admin/prestamos/' + id + '/devolver';
        
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: urlCompleta,
            type: 'POST',
            data: data,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function(response) {
                console.log('Respuesta éxito:', response);
                if (response.success) {
                    Swal.fire('Éxito', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message || 'Error al marcar devuelto', 'error');
                    submitBtn.prop('disabled', false).html('Confirmar Devolución');
                }
                $('#modalDevolver').modal('hide');
            },
            error: function(xhr) {
                console.error('Error en devolver:', xhr);
                Swal.fire('Error', 'Error al procesar la solicitud', 'error');
                submitBtn.prop('disabled', false).html('Confirmar Devolución');
            }
        });
    });

    // Reporte
// Reporte - Mejorado
$('#formReporte').on('submit', function(e) {
    e.preventDefault();
    
    // Mostrar indicador de carga
    $('#resultadoReporte').html('<div class="text-center mt-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Generando reporte...</p></div>');
    
    var data = $(this).serialize();
    console.log('Generando reporte con:', data);
    
    $.ajax({
        url: '/admin/prestamos/reporte/jh',
        type: 'GET',
        data: data,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta reporte:', response);
            if (response.success) {
                $('#resultadoReporte').html(response.html);
                // Scroll hacia el resultado
                $('html, body').animate({
                    scrollTop: $('#resultadoReporte').offset().top - 100
                }, 500);
            } else {
                $('#resultadoReporte').html('<div class="alert alert-danger mt-3">' + (response.message || 'Error al generar el reporte') + '</div>');
            }
        },
        error: function(xhr) {
            console.error('Error reporte:', xhr);
            let errorMsg = 'Error al generar el reporte';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 422) {
                // Errores de validación
                const errors = xhr.responseJSON.errors;
                errorMsg = '<ul>';
                $.each(errors, function(key, value) {
                    errorMsg += '<li>' + value[0] + '</li>';
                });
                errorMsg += '</ul>';
            }
            $('#resultadoReporte').html('<div class="alert alert-danger mt-3">' + errorMsg + '</div>');
        }
    });
});
});

$(document).ready(function() {
    // Al entrar a la página de préstamos, marcar TODAS las notificaciones relacionadas como leídas
    $.ajax({
        url: '{{ route("notificaciones.todas-leidas") }}',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                // Refrescar el contador de la campana
                if (typeof cargarNotificaciones === 'function') {
                    cargarNotificaciones();
                }
                // También puedes actualizar el badge de solicitudes pendientes (si es necesario)
                if (typeof actualizarContadorPendientes === 'function') {
                    actualizarContadorPendientes();
                }
            }
        },
        error: function() {
            console.log('Error al marcar notificaciones como leídas');
        }
    });
});
</script>

@endsection