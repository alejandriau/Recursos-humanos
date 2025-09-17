@extends('dashboard')

@section('contenidouno')
    <title>Puestos Vacíos</title>
    <style>
        .vacancy-card {
            border-left: 4px solid #28a745;
        }
        .table-responsive {
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <!-- Alertas -->
    <div class="row g-4">
        <div class="col-sm-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-plus me-2"></i>Puestos Vacíos
                        </h4>
                        <div>
                            <a href="{{ route('historial') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver a Designaciones
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-0">Lista de puestos disponibles para asignar personal. Total: <strong>{{ $puestos->count() }}</strong> puestos vacíos</p>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('historial.vacios') }}">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}"
                                           placeholder="Buscar por item, denominación...">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6>Total Puestos</h6>
                    <h3>{{ $puestos->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6>Nivel Ejecutivo</h6>
                    <h3>{{ $puestos->where('nivelgerarquico', 'like', '%ejecutivo%')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6>Nivel Técnico</h6>
                    <h3>{{ $puestos->where('nivelgerarquico', 'like', '%tecnico%')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h6>Nivel Operativo</h6>
                    <h3>{{ $puestos->where('nivelgerarquico', 'like', '%operativo%')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Puestos Vacíos -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Lista de Puestos Disponibles</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ITEM</th>
                            <th>NIVEL</th>
                            <th>DENOMINACIÓN</th>
                            <th>HABER</th>
                            <th>DEPENDENCIA</th>
                            <th>TIEMPO VACANTE</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($puestos as $puesto)
                            <tr>
                                <td><strong>{{ $puesto->item ?? 'N/A' }}</strong></td>
                                <td>{{ $puesto->nivelgerarquico ?? 'N/A' }}</td>
                                <td>{{ $puesto->denominacion ?? 'N/A' }}</td>
                                <td>${{ number_format($puesto->haber ?? 0, 2) }}</td>
                                <td style="font-size: 0.8rem;">
                                    @php
                                        $niveles = [];
                                        if ($puesto->area?->denominacion) $niveles[] = $puesto->area->denominacion;
                                        if ($puesto->unidad?->denominacion) $niveles[] = $puesto->unidad->denominacion;
                                        if ($puesto->direccion?->denominacion) $niveles[] = $puesto->direccion->denominacion;
                                        if ($puesto->secretaria?->denominacion) $niveles[] = $puesto->secretaria->denominacion;
                                        echo implode(' → ', $niveles);
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        // Calcular tiempo vacante (última designación concluida)
                                        $ultimaDesignacion = \App\Models\Historial::where('puesto_id', $puesto->id)
                                            ->where('estado', 'concluido')
                                            ->orderBy('fecha_fin', 'desc')
                                            ->first();

                                        if ($ultimaDesignacion && $ultimaDesignacion->fecha_fin) {
                                            $diasVacante = now()->diffInDays($ultimaDesignacion->fecha_fin);
                                            echo $diasVacante . ' días';
                                        } else {
                                            echo 'Nuevo';
                                        }
                                    @endphp
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('historial.create', $puesto->id) }}"
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-user-plus me-1"></i>Asignar
                                        </a>
                                        <a href="#" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <p>¡Excelente! No hay puestos vacíos</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación si es necesaria -->
            @if($puestos->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $puestos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
