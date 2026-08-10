{{-- resources/views/admin/vacaciones/index-lista.blade.php --}}
@extends('layouts.baseadm')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-umbrella-beach me-2"></i>Gestión de Vacaciones</h1>
                <a href="{{ route('admin.vacaciones.dashboard') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Buscar</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Nombre, Apellido, CI..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unidad Organizacional</label>
                            <select name="unidad_id" class="form-select" id="filterUnidad">
                                <option value="">Todas las unidades</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}"
                                        {{ request('unidad_id') == $unidad->id ? 'selected' : '' }}
                                        style="padding-left: {{ ($unidad->nivel ?? 0) * 20 }}px">
                                        {{ str_repeat('—', ($unidad->nivel ?? 0)) }} {{ $unidad->denominacion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Estado de Vacaciones</label>
                            <select name="estado_vacacion" class="form-select">
                                @foreach($estadosFiltro as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ request('estado_vacacion') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.vacaciones.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-undo me-1"></i> Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de empleados -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Empleado</th>
                                    <th>CI</th>
                                    <th>Puesto</th>
                                    <th>Unidad</th>
                                    <th>Antigüedad</th>
                                    <th class="text-center">Asignados</th>
                                    <th class="text-center">Usados</th>
                                    <th class="text-center">Vencidos</th>
                                    <th class="text-center">Arrastre</th>
                                    <th class="text-center">Saldo</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($empleados as $empleado)
                                @php
                                    // Periodos activos
                                    $periodos = $empleado->vacacionPeriodos->filter(fn($p) => $p->estado === 'activo');

                                    $totalAsignados = $periodos->sum('dias_asignados');
                                    $totalUsados    = $periodos->sum('dias_usados');
                                    $totalVencidos  = $periodos->sum('dias_vencidos');
                                    $totalArrastre  = $periodos->sum('dias_arrastre');
                                    $totalSaldo     = $periodos->sum('saldo_disponible');

                                    // Estado general
                                    $enVacaciones = $empleado->salidas()
                                        ->where('tiposalida_id', fn($q) => $q->select('id')->from('tiposalidas')->where('usa_tabla_antiguedad', true))
                                        ->where('estado', 'aprobado')
                                        ->where('fechasal', '<=', now())
                                        ->where('fecharet', '>=', now())
                                        ->exists();

                                    $tieneVencidos = $periodos->contains('periodo_vencido', true);

                                    // Puesto y unidad actual
                                    $historialActual = $empleado->historials->first();
                                    $puesto = $historialActual ? $historialActual->puesto : null;
                                    $unidad = $puesto ? $puesto->unidadOrganizacional : null;

                                    // Porcentaje de uso
                                    $porcentajeUso = $totalAsignados > 0 ? round(($totalUsados / $totalAsignados) * 100, 1) : 0;
                                    $barraColor = $porcentajeUso < 50 ? 'success' : ($porcentajeUso < 80 ? 'warning' : 'danger');

                                    // Estado del empleado
                                    if ($enVacaciones) {
                                        $estadoEmpleado = 'En vacaciones';
                                        $estadoBadge = 'warning';
                                    } elseif ($tieneVencidos) {
                                        $estadoEmpleado = 'Con vencidos';
                                        $estadoBadge = 'danger';
                                    } elseif ($totalSaldo > 0) {
                                        $estadoEmpleado = 'Disponible';
                                        $estadoBadge = 'success';
                                    } else {
                                        $estadoEmpleado = 'Sin saldo';
                                        $estadoBadge = 'secondary';
                                    }

                                    // --- NUEVO CÁLCULO DE ANTIGÜEDAD (años y meses) ---
                                    $fechaIngreso = $empleado->fechaIngreso ? \Carbon\Carbon::parse($empleado->fechaIngreso) : null;
                                    if ($fechaIngreso) {
                                        $diff = $fechaIngreso->diff(now());
                                        $anios = $diff->y;
                                        $meses = $diff->m;
                                        $antiguedadTexto = $anios > 0 ? "{$anios} año" . ($anios > 1 ? 's' : '') : '';
                                        $antiguedadTexto .= $meses > 0 ? ($anios > 0 ? ' y ' : '') . "{$meses} mes" . ($meses > 1 ? 'es' : '') : '';
                                        $antiguedadTexto = $antiguedadTexto ?: '0 meses';
                                    } else {
                                        $antiguedadTexto = 'N/A';
                                    }
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $empleado->nombre }} {{ $empleado->apellidoPat }} {{ $empleado->apellidoMat }}</strong>
                                        @if($periodos->count() > 1)
                                            <span class="badge bg-info ms-1" title="Periodos activos: {{ $periodos->count() }}">
                                                {{ $periodos->count() }} per.
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $empleado->ci }}</td>
                                    <td class="text-truncate" style="max-width:120px;" title="{{ $puesto ? $puesto->denominacion : 'N/A' }}">
                                        {{ $puesto ? $puesto->denominacion : 'N/A' }}
                                    </td>
                                    <td>
                                        @if($unidad)
                                            <span class="badge bg-info bg-opacity-10 text-info text-truncate d-inline-block" style="max-width:140px;" title="{{ $unidad->denominacion }} ({{ $unidad->sigla ?? '' }})">
                                                {{ $unidad->denominacion }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $antiguedadTexto }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-semibold">{{ number_format($totalAsignados, 1) }}</td>
                                    <td class="text-center">
                                        <span class="text-{{ $porcentajeUso > 80 ? 'danger' : 'dark' }}">
                                            {{ number_format($totalUsados, 1) }}
                                        </span>
                                        <div class="progress mt-1" style="height:4px; width:60px; margin:0 auto;">
                                            <div class="progress-bar bg-{{ $barraColor }}"
                                                role="progressbar"
                                                style="width: {{ min($porcentajeUso, 100) }}%;"
                                                aria-valuenow="{{ $porcentajeUso }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($totalVencidos > 0)
                                            <span class="badge bg-danger">{{ number_format($totalVencidos, 1) }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($totalArrastre, 1) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $totalSaldo > 0 ? 'bg-success' : 'bg-danger' }} badge-lg">
                                            {{ number_format($totalSaldo, 1) }} días
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $estadoBadge }}">{{ $estadoEmpleado }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.vacaciones.show', $empleado->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                ...
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Mostrando {{ $empleados->firstItem() ?? 0 }} - {{ $empleados->lastItem() ?? 0 }} de {{ $empleados->total() }} empleados
                        </div>
                        <div>
                            {{ $empleados->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 20px;
    }

    .table > :not(caption) > * > * {
        padding: 0.6rem 0.5rem;
    }

    .badge.bg-info.bg-opacity-10 {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }

    .badge.bg-secondary.bg-opacity-10 {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
</style>
@endpush
@endsection