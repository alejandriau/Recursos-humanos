@extends('dashboard')

@section('title', 'Vista Previa - Reporte de Personal')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-eye"></i> Vista Previa del Reporte
                        </h3>
                        <div class="btn-group">
                            <a href="{{ route('reportes.personas') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button onclick="window.print()" class="btn btn-outline-primary">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros aplicados -->
                    @if(!empty($filtrosAplicados))
                    <div class="alert alert-info">
                        <h5><i class="fas fa-filter"></i> Filtros Aplicados:</h5>
                        <div class="mt-2">
                            @foreach($filtrosAplicados as $filtro)
                                <span class="badge badge-info mr-2">{{ $filtro }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5><i class="fas fa-users"></i> Total Registros</h5>
                                    <h3>{{ $personas->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5><i class="fas fa-columns"></i> Columnas</h5>
                                    <h3>{{ count($columnasMostrar) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5><i class="fas fa-download"></i> Exportar</h5>
                                    <form method="POST" action="{{ route('reportes.personas.exportar.excel') }}"
                                          id="exportForm" class="d-inline">
                                        @csrf
                                        @foreach(request()->except(['_token', '_method']) as $key => $value)
                                            @if(is_array($value))
                                                @foreach($value as $val)
                                                    <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                                                @endforeach
                                            @else
                                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                            @endif
                                        @endforeach
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-file-excel"></i> Excel
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('reportes.personas.exportar.pdf') }}"
                                          class="d-inline">
                                        @csrf
                                        @foreach(request()->except(['_token', '_method']) as $key => $value)
                                            @if(is_array($value))
                                                @foreach($value as $val)
                                                    <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                                                @endforeach
                                            @else
                                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                            @endif
                                        @endforeach
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('reportes.personas.exportar.csv') }}"
                                          class="d-inline">
                                        @csrf
                                        @foreach(request()->except(['_token', '_method']) as $key => $value)
                                            @if(is_array($value))
                                                @foreach($value as $val)
                                                    <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                                                @endforeach
                                            @else
                                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                            @endif
                                        @endforeach
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-file-csv"></i> CSV
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de datos -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="tablaReporte">
                            <thead class="thead-dark">
                                <tr>
                                    @foreach($columnasMostrar as $columna)
                                        <th>{{ $columna }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($personas as $persona)
                                    <tr>
                                        @foreach($columnasMostrar as $key => $label)
                                            <td>{{ $persona[$key] ?? 'N/A' }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columnasMostrar) }}" class="text-center">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                No se encontraron registros con los filtros aplicados.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación o total -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            <i class="fas fa-clock"></i> Generado: {{ now()->format('d/m/Y H:i:s') }}
                        </div>
                        <div class="text-muted">
                            Mostrando {{ $personas->count() }} registro(s)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#tablaReporte').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            pageLength: 25,
            responsive: true,
            order: []
        });
    });
</script>
@endpush

@push('styles')
<style>
    @media print {
        .card-header, .btn-group, .alert, .row.mb-4, .d-flex.justify-content-between {
            display: none !important;
        }

        .card {
            border: none !important;
        }

        table {
            font-size: 11px !important;
        }

        h3 {
            text-align: center;
        }
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        white-space: nowrap;
    }
</style>
@endpush
