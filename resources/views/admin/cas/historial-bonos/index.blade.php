{{-- resources/views/historial-bonos/index.blade.php --}}
@extends('dashboard')

@section('title', 'Historial de Cambios de Bono - ' . $cas->persona->nombre_completo)

@section('contenido')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">
                <i class="fas fa-history text-primary"></i>
                Historial de Cambios de Bono
            </h1>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Persona:</strong>
                            <a href="{{ route('personas.show', $cas->id_persona) }}">
                                {{ $cas->persona->nombre_completo }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <strong>CAS Actual:</strong>
                            <span class="badge bg-success">{{ $cas->estado_cas }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Bono Actual:</strong>
                            <span class="badge bg-info">
                                {{ $cas->porcentaje_bono ?? 0 }}% - Bs. {{ number_format($cas->monto_bono ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('cas.show', $cas->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al CAS
            </a>
            <button class="btn btn-outline-primary" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('historial-bonos.index') }}" method="GET">
                <input type="hidden" name="id_cas" value="{{ $cas->id }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Cambio</label>
                        <select name="tipo_cambio" class="form-select">
                            <option value="">Todos los tipos</option>
                            <option value="inicial" {{ request('tipo_cambio') == 'inicial' ? 'selected' : '' }}>
                                Registro Inicial
                            </option>
                            <option value="antiguedad" {{ request('tipo_cambio') == 'antiguedad' ? 'selected' : '' }}>
                                Por Antigüedad
                            </option>
                            <option value="salario" {{ request('tipo_cambio') == 'salario' ? 'selected' : '' }}>
                                Por Salario Mínimo
                            </option>
                            <option value="ambos" {{ request('tipo_cambio') == 'ambos' ? 'selected' : '' }}>
                                Por Ambos
                            </option>
                            <option value="ajuste" {{ request('tipo_cambio') == 'ajuste' ? 'selected' : '' }}>
                                Ajuste Manual
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control"
                               value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control"
                               value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $historial->count() }}</h4>
                            <small>Total Cambios</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exchange-alt fa-2x"></i>
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
                            <h4 class="mb-0">Bs. {{ number_format($historial->sum('monto_bono_nuevo') - $historial->sum('monto_bono_anterior'), 2) }}</h4>
                            <small>Incremento Total</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="mb-0">{{ $historial->where('tipo_cambio', 'antiguedad')->count() }}</h4>
                            <small>Cambios por Antigüedad</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-birthday-cake fa-2x"></i>
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
                            <h4 class="mb-0">{{ $historial->where('tipo_cambio', 'salario')->count() }}</h4>
                            <small>Cambios por Salario</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Historial -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list"></i> Detalle de Cambios
            </h5>
        </div>
        <div class="card-body">
            @if($historial->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
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
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historial as $cambio)
                            <tr>
                                <td>{{ $cambio->fecha_cambio->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $cambio->tipo_badge }}">
                                        {{ $cambio->tipo_texto }}
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
                                    @if($cambio->huboCambioPorcentaje())
                                        <span class="text-danger">
                                            {{ $cambio->porcentaje_bono_anterior ?? 0 }}%
                                        </span>
                                        <i class="fas fa-arrow-right text-muted mx-1"></i>
                                        <span class="text-success">
                                            {{ $cambio->porcentaje_bono_nuevo ?? 0 }}%
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            {{ $cambio->porcentaje_bono_nuevo ?? 0 }}%
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($cambio->huboCambioMonto())
                                        <span class="text-danger">
                                            Bs. {{ number_format($cambio->monto_bono_anterior ?? 0, 2) }}
                                        </span>
                                        <i class="fas fa-arrow-right text-muted mx-1"></i>
                                        <span class="text-success">
                                            Bs. {{ number_format($cambio->monto_bono_nuevo ?? 0, 2) }}
                                        </span>
                                        <br>
                                        <small class="text-info">
                                            <i class="fas fa-caret-{{ $cambio->diferencia_monto >= 0 ? 'up' : 'down' }}"></i>
                                            Bs. {{ number_format($cambio->diferencia_monto, 2) }}
                                        </small>
                                    @else
                                        <span class="text-muted">
                                            Bs. {{ number_format($cambio->monto_bono_nuevo ?? 0, 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($cambio->huboCambioSalario())
                                        <span class="text-danger">
                                            Bs. {{ number_format($cambio->salarioMinimoAnterior->monto_salario_minimo ?? 0, 2) }}
                                        </span>
                                        <i class="fas fa-arrow-right text-muted mx-1"></i>
                                        <span class="text-success">
                                            Bs. {{ number_format($cambio->salarioMinimoNuevo->monto_salario_minimo ?? 0, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            Bs. {{ number_format($cambio->salarioMinimoNuevo->monto_salario_minimo ?? 0, 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {{ $cambio->usuario->name ?? 'Sistema' }}
                                </td>
                                <td>
                                    <small class="text-muted">{{ $cambio->observacion }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('historial-bonos.show', $cambio->id) }}"
                                       class="btn btn-sm btn-outline-info"
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5>No hay registros en el historial</h5>
                    <p class="text-muted">No se han registrado cambios en el bono para este CAS.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    // Implementar exportación a Excel
    window.location.href = '{{ route("historial-bonos.index") }}?id_cas={{ $cas->id }}&export=excel';
}
</script>
@endpush
