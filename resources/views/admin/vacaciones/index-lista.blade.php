{{-- resources/views/admin/vacaciones/index-lista.blade.php --}}
@extends('layouts.baseadm')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Gestión de Vacaciones</h1>
                <div>
                    <button onclick="window.location.href='{{ route('admin.vacaciones.dashboard') }}'" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Buscar</label>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Nombre, Apellido, CI..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unidad Organizacional</label>
                                <select name="unidad_id" class="form-control" id="filterUnidad">
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
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estado de Vacaciones</label>
                                <select name="estado_vacacion" class="form-control">
                                    @foreach($estadosFiltro as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ request('estado_vacacion') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.vacaciones.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de empleados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Empleado</th>
                                    <th>CI</th>
                                    <th>Puesto</th>
                                    <th>Unidad Organizacional</th>
                                    <th>Antigüedad</th>
                                    <th>Saldo Disponible</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($empleados as $empleado)
                                @php
                                    $saldoTotal = $empleado->vacacionPeriodos->sum('saldo_disponible');
                                    $tieneVencidos = $empleado->vacacionPeriodos->where('periodo_vencido', true)->isNotEmpty();
                                    $enVacaciones = $empleado->salidas()
                                        ->where('tiposalida_id', function($q) {
                                            $q->select('id')->from('tiposalidas')
                                              ->where('usa_tabla_antiguedad', true);
                                        })
                                        ->where('estado', 'aprobado')
                                        ->where('fechasal', '<=', now())
                                        ->where('fecharet', '>=', now())
                                        ->exists();

                                    // Obtener puesto y unidad actual
                                    $historialActual = $empleado->historials->first();
                                    $puesto = $historialActual ? $historialActual->puesto : null;
                                    $unidad = $puesto ? $puesto->unidadOrganizacional : null;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $empleado->nombre }} {{ $empleado->apellidoPat }} {{ $empleado->apellidoMat }}</strong>
                                    </td>
                                    <td>{{ $empleado->ci }}</td>
                                    <td>{{ $puesto ? $puesto->denominacion : 'N/A' }}</td>
                                    <td>
                                        @if($unidad)
                                            <span class="badge badge-info"
                                                  title="Código: {{ $unidad->codigo ?? 'N/A' }} | Tipo: {{ $unidad->tipo ?? 'N/A' }}">
                                                {{ $unidad->denominacion }}
                                            </span>
                                            @if($unidad->sigla)
                                                <small class="text-muted">({{ $unidad->sigla }})</small>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $fechaIngreso = $empleado->fechaIngreso ? \Carbon\Carbon::parse($empleado->fechaIngreso) : null;
                                        @endphp
                                        {{ $fechaIngreso ? $fechaIngreso->diffInYears(now()) : 0 }} años
                                    </td>
                                    <td>
                                        <span class="badge {{ $saldoTotal > 0 ? 'badge-success' : 'badge-danger' }} badge-lg">
                                            {{ number_format($saldoTotal, 1) }} días
                                        </span>
                                    </td>
                                    <td>
                                        @if($enVacaciones)
                                            <span class="badge badge-warning">EN VACACIONES</span>
                                        @elseif($tieneVencidos)
                                            <span class="badge badge-danger">CON VENCIDOS</span>
                                        @elseif($saldoTotal > 0)
                                            <span class="badge badge-success">DISPONIBLE</span>
                                        @else
                                            <span class="badge badge-secondary">SIN SALDO</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.vacaciones.show', $empleado->id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No se encontraron empleados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $empleados->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge-lg {
        font-size: 14px;
        padding: 8px 12px;
    }

    select option {
        padding: 5px 10px;
    }

    .badge-info {
        cursor: help;
    }
</style>
@endpush
@endsection
