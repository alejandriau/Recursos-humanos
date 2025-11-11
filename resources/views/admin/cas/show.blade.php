@extends('dashboard')

@section('title', 'Detalles de CAS - ' . $cas->persona->nombre)

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-contract"></i>
                        Detalles de CAS
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('cas.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('cas.edit', $cas->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información de la Persona -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-user"></i> Información Personal
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Nombre Completo:</strong><br>
                                            {{ $cas->persona->nombre }} {{ $cas->persona->apellidoPat }} {{ $cas->persona->apellidoMat }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Cédula de Identidad:</strong><br>
                                            {{ $cas->persona->ci }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Fecha de Ingreso Institución:</strong><br>
                                            @if($cas->fecha_ingreso_institucion)
                                                {{ \Carbon\Carbon::parse($cas->fecha_ingreso_institucion)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">No registrada</span>
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            <strong>Estado CAS:</strong><br>
                                            @if($cas->estado_cas == "vigente")
                                                <span class="badge bg-success">VIGENTE</span>
                                            @elseif($cas->estado_cas == "vencido")
                                                <span class="badge bg-warning">VENCIDO</span>
                                            @else
                                                <span class="badge bg-secondary">{{ strtoupper($cas->estado_cas) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del CAS -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-info-circle"></i> Información del CAS
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Fecha Emisión CAS:</th>
                                            <td>
                                                @if($cas->fecha_emision_cas)
                                                    {{ \Carbon\Carbon::parse($cas->fecha_emision_cas)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Fecha Presentación RRHH:</th>
                                            <td>
                                                @if($cas->fecha_presentacion_rrhh)
                                                    {{ \Carbon\Carbon::parse($cas->fecha_presentacion_rrhh)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Fecha Cálculo Antigüedad:</th>
                                            <td>
                                                @if($cas->fecha_calculo_antiguedad)
                                                    {{ \Carbon\Carbon::parse($cas->fecha_calculo_antiguedad)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Nivel de Alerta:</th>
                                            <td>
                                                @if($cas->nivel_alerta == 'urgente')
                                                    <span class="badge bg-danger">URGENTE</span>
                                                @elseif($cas->nivel_alerta == 'advertencia')
                                                    <span class="badge bg-warning text-dark">ADVERTENCIA</span>
                                                @else
                                                    <span class="badge bg-success">NORMAL</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Aplica Bono Antigüedad:</th>
                                            <td>
                                                @if($cas->aplica_bono_antiguedad)
                                                    <span class="badge bg-success">SÍ</span>
                                                @else
                                                    <span class="badge bg-secondary">NO</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-money-bill-wave"></i> Información Económica
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        @if($cas->escalaBono)
                                        <tr>
                                            <th width="40%">Escala de Bono:</th>
                                            <td>{{ $cas->escalaBono->rango_texto ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Porcentaje de Bono:</th>
                                            <td>
                                                <span class="badge bg-primary">{{ $cas->porcentaje_bono ?? 0 }}%</span>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Monto de Bono:</th>
                                            <td>
                                                <strong class="text-success">Bs. {{ number_format($cas->monto_bono ?? 0, 2) }}</strong>
                                            </td>
                                        </tr>
                                        @if($cas->salarioMinimo)
                                        <tr>
                                            <th>Salario Mínimo Referencial:</th>
                                            <td>
                                                <strong>Bs. {{ number_format($cas->salarioMinimo->monto_salario_minimo ?? 0, 2) }}</strong>
                                                <small class="text-muted d-block">Gestión: {{ $cas->salarioMinimo->gestion }}</small>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Rango Antigüedad:</th>
                                            <td>{{ $cas->rango_antiguedad ?? 'No especificado' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Antigüedad y Periodo -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt"></i> Antigüedad de Servicio
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Años de Servicio:</th>
                                            <td>{{ $cas->anios_servicio ?? 0 }} años</td>
                                        </tr>
                                        <tr>
                                            <th>Meses de Servicio:</th>
                                            <td>{{ $cas->meses_servicio ?? 0 }} meses</td>
                                        </tr>
                                        <tr>
                                            <th>Días de Servicio:</th>
                                            <td>{{ $cas->dias_servicio ?? 0 }} días</td>
                                        </tr>
                                        <tr>
                                            <th>Antigüedad Total:</th>
                                            <td>
                                                <strong>
                                                    {{ $cas->anios_servicio ?? 0 }}a
                                                    {{ $cas->meses_servicio ?? 0 }}m
                                                    {{ $cas->dias_servicio ?? 0 }}d
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-chart-line"></i> Periodo de Calificación
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Periodo Calificación:</th>
                                            <td>{{ $cas->periodo_calificacion ?? 'No especificado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Meses Calificación:</th>
                                            <td>{{ $cas->meses_calificacion ?? 'No especificado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
                                            <td>
                                                @if($cas->estado_cas == "vigente")
                                                    <span class="badge bg-success">VIGENTE</span>
                                                @elseif($cas->estado_cas == "vencido")
                                                    <span class="badge bg-warning">VENCIDO</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ strtoupper($cas->estado_cas) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HISTORIAL DE BONOS - NUEVA SECCIÓN -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-history"></i> Historial de Cambios de Bono
                                    </h4>
                                    <a href="{{ route('historial-bonos.index', ['id_cas' => $cas->id]) }}"
                                       class="btn btn-sm btn-light">
                                        <i class="fas fa-external-link-alt"></i> Ver Historial Completo
                                    </a>
                                </div>
                                <div class="card-body">
                                    @php
                                        // Obtener últimos 10 cambios del historial de bonos
                                        $historialBonos = $cas->historialBonos->sortByDesc('fecha_cambio')->take(10);
                                    @endphp

                                    @if($historialBonos->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Tipo</th>
                                                        <th>Antigüedad</th>
                                                        <th>Porcentaje</th>
                                                        <th>Monto Bono</th>
                                                        <th>Salario Base</th>
                                                        <th>Usuario</th>
                                                        <th>Observación</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($historialBonos as $cambio)
                                                    <tr>
                                                        <td>{{ $cambio->fecha_cambio->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @php
                                                                $badgeColors = [
                                                                    'inicial' => 'primary',
                                                                    'antiguedad' => 'success',
                                                                    'salario' => 'warning',
                                                                    'ambos' => 'info',
                                                                    'ajuste' => 'secondary'
                                                                ];
                                                                $tipoTextos = [
                                                                    'inicial' => 'Inicial',
                                                                    'antiguedad' => 'Antigüedad',
                                                                    'salario' => 'Salario',
                                                                    'ambos' => 'Ambos',
                                                                    'ajuste' => 'Ajuste'
                                                                ];
                                                            @endphp
                                                            <span class="badge bg-{{ $badgeColors[$cambio->tipo_cambio] ?? 'secondary' }}">
                                                                {{ $tipoTextos[$cambio->tipo_cambio] ?? $cambio->tipo_cambio }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($cambio->anios_servicio_nuevo)
                                                                {{ $cambio->anios_servicio_nuevo }}a
                                                                {{ $cambio->meses_servicio_nuevo }}m
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($cambio->porcentaje_bono_anterior != $cambio->porcentaje_bono_nuevo)
                                                                <span class="text-danger">{{ $cambio->porcentaje_bono_anterior ?? 0 }}%</span>
                                                                <i class="fas fa-arrow-right text-muted mx-1"></i>
                                                            @endif
                                                            <span class="{{ $cambio->porcentaje_bono_anterior != $cambio->porcentaje_bono_nuevo ? 'text-success' : 'text-muted' }}">
                                                                {{ $cambio->porcentaje_bono_nuevo ?? 0 }}%
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($cambio->monto_bono_anterior != $cambio->monto_bono_nuevo)
                                                                <span class="text-danger">Bs. {{ number_format($cambio->monto_bono_anterior ?? 0, 2) }}</span>
                                                                <i class="fas fa-arrow-right text-muted mx-1"></i>
                                                            @endif
                                                            <span class="{{ $cambio->monto_bono_anterior != $cambio->monto_bono_nuevo ? 'text-success' : 'text-muted' }}">
                                                                Bs. {{ number_format($cambio->monto_bono_nuevo ?? 0, 2) }}
                                                            </span>
                                                            @if($cambio->monto_bono_anterior != $cambio->monto_bono_nuevo)
                                                                @php
                                                                    $diferencia = ($cambio->monto_bono_nuevo ?? 0) - ($cambio->monto_bono_anterior ?? 0);
                                                                @endphp
                                                                <br>
                                                                <small class="text-{{ $diferencia >= 0 ? 'success' : 'danger' }}">
                                                                    <i class="fas fa-caret-{{ $diferencia >= 0 ? 'up' : 'down' }}"></i>
                                                                    Bs. {{ number_format($diferencia, 2) }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($cambio->id_salario_minimo_anterior != $cambio->id_salario_minimo_nuevo)
                                                                <span class="text-danger">
                                                                    Bs. {{ number_format($cambio->salarioMinimoAnterior->monto_salario_minimo ?? 0, 2) }}
                                                                </span>
                                                                <i class="fas fa-arrow-right text-muted mx-1"></i>
                                                            @endif
                                                            <span class="{{ $cambio->id_salario_minimo_anterior != $cambio->id_salario_minimo_nuevo ? 'text-success' : 'text-muted' }}">
                                                                Bs. {{ number_format($cambio->salarioMinimoNuevo->monto_salario_minimo ?? 0, 2) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small>{{ $cambio->usuario->name ?? 'Sistema' }}</small>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted" title="{{ $cambio->observacion }}">
                                                                {{ Str::limit($cambio->observacion, 40) }}
                                                            </small>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @if($cas->historialBonos->count() > 10)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('historial-bonos.index', ['id_cas' => $cas->id]) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-history"></i>
                                                    Ver los {{ $cas->historialBonos->count() - 10 }} registros anteriores
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-history fa-2x text-muted mb-3"></i>
                                            <h5>No hay cambios registrados</h5>
                                            <p class="text-muted mb-0">No se han registrado cambios en el bono para este CAS.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    @if($cas->observaciones || $cas->archivo_cas)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-file-alt"></i> Información Adicional
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @if($cas->observaciones)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <strong>Observaciones:</strong><br>
                                            <p class="mt-2">{{ $cas->observaciones }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($cas->archivo_cas)
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <strong>Archivo CAS:</strong><br>
                                            <a href="{{ asset('storage/' . $cas->archivo_cas) }}"
                                               target="_blank"
                                               class="btn btn-outline-primary btn-sm mt-2">
                                                <i class="fas fa-download"></i> Descargar Archivo
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Resumen de Antigüedad y Bonos -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-calculator"></i> Resumen de Cálculos
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <div class="border rounded p-3">
                                                <h5>Antigüedad Total</h5>
                                                <h3 class="text-primary">
                                                    {{ $cas->anios_servicio ?? 0 }}a
                                                    {{ $cas->meses_servicio ?? 0 }}m
                                                    {{ $cas->dias_servicio ?? 0 }}d
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="border rounded p-3">
                                                <h5>Bono Actual</h5>
                                                <h3 class="text-success">{{ $cas->porcentaje_bono ?? 0 }}%</h3>
                                                <small class="text-muted">
                                                    {{ $cas->rango_antiguedad ?? 'Sin rango' }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="border rounded p-3">
                                                <h5>Monto Bono</h5>
                                                <h3 class="text-info">Bs. {{ number_format($cas->monto_bono ?? 0, 2) }}</h3>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="border rounded p-3">
                                                <h5>Estado</h5>
                                                @if($cas->estado_cas == "vigente")
                                                    <h3><span class="badge bg-success">VIGENTE</span></h3>
                                                @else
                                                    <h3><span class="badge bg-secondary">{{ strtoupper($cas->estado_cas) }}</span></h3>
                                                @endif
                                                <small class="text-muted">
                                                    Nivel: {{ ucfirst($cas->nivel_alerta) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                @if($cas->fecha_registro)
                                <strong>Registrado:</strong> {{ \Carbon\Carbon::parse($cas->fecha_registro)->format('d/m/Y H:i') }}
                                @endif
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                @if($cas->fecha_actualizacion)
                                <strong>Actualizado:</strong> {{ \Carbon\Carbon::parse($cas->fecha_actualizacion)->format('d/m/Y H:i') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Página de detalles de CAS cargada');
    });
</script>
@endsection
