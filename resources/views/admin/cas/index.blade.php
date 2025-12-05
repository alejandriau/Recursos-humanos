@extends('dashboard')

@section('title', 'Control de CAS - Todas las Personas')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
<div class="card-header">
    <h3 class="card-title">
        <i class="fas fa-users"></i>
        Personas Activas - Control de Antigüedad y Bonos
    </h3>
    <div class="card-tools">
        <!-- Botón Exportar Excel -->
        <form action="{{ route('cas.index') }}" method="GET" class="d-inline">
            @foreach(request()->all() as $key => $value)
                @if($key != 'export' && $key != 'page')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <input type="hidden" name="export" value="excel">
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
        </form>

        <a href="{{ route('cas.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nuevo CAS
        </a>
        <a href="{{ route('configuracion-salario-minimo.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-cog"></i>
            Salario mínimo
        </a>

        <form action="{{ route('cas.actualizar-alertas') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm">
                <i class="fas fa-sync-alt"></i> Actualizar Alertas
            </button>
        </form>
    </div>
</div>

                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('cas.index') }}" id="filterForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tiene_cas" class="form-label">Filtrar por CAS:</label>
                                            <select name="tiene_cas" id="tiene_cas" class="form-control">
                                                <option value="">Todos</option>
                                                <option value="con_cas" {{ request('tiene_cas') == 'con_cas' ? 'selected' : '' }}>Con CAS</option>
                                                <option value="sin_cas" {{ request('tiene_cas') == 'sin_cas' ? 'selected' : '' }}>Sin CAS</option>
                                                <option value="necesita_cas" {{ request('tiene_cas') == 'necesita_cas' ? 'selected' : '' }}>Necesita CAS Urgente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="search" class="form-label">Buscar:</label>
                                            <div class="input-group">
                                                <input type="text" name="search" id="search" class="form-control"
                                                       placeholder="Nombre, apellido o CI..."
                                                       value="{{ request('search') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="btn-group w-100">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-filter"></i> Filtrar
                                                </button>
                                                @if(request('search') || request('tiene_cas'))
                                                <a href="{{ route('cas.index') }}" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-end align-items-end h-100">
                                <div class="text-muted text-right">
                                    <small>
                                        Mostrando <strong>{{ $personas->count() }}</strong> de
                                        <strong>{{ $personas->total() }}</strong> registros
                                        @if(request('tiene_cas') == 'con_cas')
                                            <span class="badge bg-primary ml-2">Con CAS</span>
                                        @elseif(request('tiene_cas') == 'sin_cas')
                                            <span class="badge bg-secondary ml-2">Sin CAS</span>
                                        @elseif(request('tiene_cas') == 'necesita_cas')
                                            <span class="badge bg-danger ml-2">Necesita CAS Urgente</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Personas -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>N°</th>
                                    <th>Persona</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Antigüedad Total</th>
                                    <th>Estado CAS</th>
                                    <th>Bono Actual</th>
                                    <th>Bono CAS</th>
                                    <th>Alerta</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($personas as $persona)
                                @php
                                    $antiguedad = $persona->calculo_antiguedad;
                                    $bono = $persona->calculo_bono;
                                    $alerta = $persona->nivel_alerta_persona;
                                @endphp
                                <tr>
                                    <td>{{ ($personas->currentPage() - 1) * $personas->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</strong>
                                        <br>
                                        <small class="text-muted">CI: {{ $persona->ci }}</small>
                                    </td>
                                    <td>
                                        @if($persona->fechaIngreso)
                                            {{ \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">No registrada</span>
                                        @endif
                                    </td>
<td>
    <span class="badge bg-info text-white">
        {{ $antiguedad['anios'] }}a
        {{ $antiguedad['meses'] }}m
        {{ $antiguedad['dias'] }}d
    </span>
    <div class="mt-2 small text-muted">
        @if($antiguedad['tiene_cas'])
            <div class="d-flex align-items-center mb-1">
                <i class="fas fa-file-contract text-success me-2"></i>
                <span><strong>Base: {{ $antiguedad['antiguedad_base'] }}</strong></span>
            </div>

            <ul class="list-unstyled mb-0 ps-3">
                <li>
                    <i class="fas fa-calendar-check text-primary me-1"></i>
                    <strong>Fecha cálculo:</strong>
                    {{ $persona->ultimoCas?->fecha_calculo_antiguedad
                        ? $persona->ultimoCas->fecha_calculo_antiguedad->format('d/m/Y')
                        : 'No emitido' }}
                </li>
                <li>
                    <i class="fas fa-calendar-day text-info me-1"></i>
                    <strong>Fecha presentación:</strong>
                    {{ $persona->ultimoCas?->fecha_presentacion_rrhh
                        ? $persona->ultimoCas->fecha_presentacion_rrhh->format('d/m/Y')
                        : 'No emitido' }}
                </li>
                <li>
                    <i class="fas fa-calendar-alt text-secondary me-1"></i>
                    <strong>Fecha emisión:</strong>
                    {{ $persona->ultimoCas?->fecha_ingreso_institucion
                        ? $persona->ultimoCas->fecha_ingreso_institucion->format('d/m/Y')
                        : 'No emitido' }}
                </li>
            </ul>
        @else
            <i class="fas fa-calendar-alt text-warning me-1"></i>
            <span>Desde ingreso</span>
        @endif
    </div>
</td>

                                    <td>
                                        @if($antiguedad['tiene_cas'])
                                            @if($antiguedad['cas_vigente'])
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Con CAS
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle"></i> CAS no vigente
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times-circle"></i> Sin CAS
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($bono['aplica_bono'])
                                            <span class="badge bg-success text-white">
                                                {{ $bono['porcentaje'] }}%
                                            </span>
                                            <br>
                                            <small>{{ $bono['rango'] }}</small>
                                            <br>
                                            <strong class="text-success">
                                                Bs. {{ number_format($bono['monto'], 2) }}
                                            </strong>
                                        @else
                                            <span class="badge bg-secondary">No aplica</span>
                                            <br>
                                            <small>Menos de 2 años</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($antiguedad['tiene_cas'] && $bono['escala_cas'])
                                            <span class="badge bg-primary text-white">
                                                {{ $bono['escala_cas']->porcentaje_bono }}%
                                            </span>
                                            <br>
                                            <strong>{{ $bono['escala_cas']->rango_texto }}</strong><br>
                                            <strong class="text-success">
                                                Bs. {{ number_format($persona->ultimoCas->monto_bono, 2) ?? 'No emitido' }}
                                            </strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alerta == 'urgente')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-circle"></i> URGENTE
                                            </span>
                                            <br>
                                            <small>
                                                @if(!$antiguedad['tiene_cas'])
                                                    Sin CAS registrado
                                                @else
                                                    Necesita actualizar CAS
                                                @endif
                                            </small>
                                        @elseif($alerta == 'advertencia')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle"></i> Advertencia
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Normal
                                            </span>
                                        @endif
                                    </td>
<td>
    <div class="btn-group">
        @if($antiguedad['tiene_cas'])
            <!-- Botones para CAS existente -->
            <a href="{{ route('cas.show', $antiguedad['cas_id']) }}"
               class="btn btn-info btn-sm" title="Ver CAS">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('cas.edit', $antiguedad['cas_id']) }}"
               class="btn btn-warning btn-sm" title="Editar CAS">
                <i class="fas fa-edit"></i>
            </a>

            <!-- Botón para registrar NUEVO CAS -->
            <a href="{{ route('cas.create.persona', ['idPersona' => $persona->id]) }}"
               class="btn btn-success btn-sm" title="Registrar Nuevo CAS">
                <i class="fas fa-plus-circle"></i>
            </a>
        @else
            <!-- Botón para primer CAS -->
            <a href="{{ route('cas.create.persona', ['idPersona' => $persona->id]) }}"
               class="btn btn-success btn-sm" title="Registrar CAS">
                <i class="fas fa-file-contract"></i>
            </a>
        @endif

        <a href="{{ route('cas.calcular-bono', $persona->id) }}"
           class="btn btn-primary btn-sm" title="Calcular Bono">
            <i class="fas fa-calculator"></i>
        </a>

        @if($alerta == 'urgente' && $antiguedad['tiene_cas'])
            <a href="{{ route('cas.create.persona', ['idPersona' => $persona->id]) }}"
               class="btn btn-danger btn-sm" title="Actualizar CAS">
                <i class="fas fa-sync-alt"></i>
            </a>
        @endif
    </div>
</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        <i class="fas fa-users fa-2x mb-2"></i><br>
                                        @if(request('search') || request('tiene_cas'))
                                            No se encontraron personas con los filtros aplicados
                                        @else
                                            No se encontraron personas activas
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $personas->appends(request()->query())->links() }}
                    </div>

                    <!-- Resumen -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Sistema de Alertas</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <span class="badge bg-danger">URGENTE</span>
                                        <ul class="mb-0 mt-2">
                                            <li>Sin CAS y aplica para bono</li>
                                            <li>CAS desactualizado (cambio de rango)</li>
                                            <li>Necesita CAS urgente</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="badge bg-warning text-dark">ADVERTENCIA</span>
                                        <ul class="mb-0 mt-2">
                                            <li>CAS no vigente</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="badge bg-success">NORMAL</span>
                                        <ul class="mb-0 mt-2">
                                            <li>CAS vigente y actualizado</li>
                                            <li>No aplica para bono</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Asegurar que los enlaces de paginación mantengan los parámetros
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            const url = new URL(link.href);
            const currentParams = new URLSearchParams(window.location.search);

            // Mantener todos los parámetros existentes
            currentParams.forEach((value, key) => {
                if (key !== 'page') {
                    url.searchParams.set(key, value);
                }
            });

            link.href = url.toString();
        });
    });
</script>

<style>
    .form-group {
        margin-bottom: 0.5rem;
    }
    .form-label {
        font-weight: 500;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

</style>
@endsection
