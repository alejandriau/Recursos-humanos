@extends('dashboard')

@section('title', 'Vista Previa del Reporte')

@section('contenido')
<div class="container-fluid px-4">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-eye me-2"></i>Vista Previa del Reporte
                </h4>
                <div class="btn-group">
                    <a href="{{ route('reportes.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Resumen de Filtros -->
            @if(count($filtrosAplicados) > 0)
            <div class="alert alert-info mb-4">
                <h6 class="alert-heading">
                    <i class="fas fa-filter me-1"></i> Filtros Aplicados:
                </h6>
                <div class="row">
                    @foreach($filtrosAplicados as $filtro)
                    <div class="col-auto">
                        <span class="badge bg-primary">{{ $filtro }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Registros</h6>
                            <h3 class="text-primary">{{ $personas->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Columnas Seleccionadas</h6>
                            <h3 class="text-success">{{ count($columnasMostrar) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Fecha Generación</h6>
                            <h5 class="text-warning">{{ now()->format('d/m/Y H:i') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-danger">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Acciones</h6>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-success" onclick="exportarDesdeVista('excel')">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="exportarDesdeVista('pdf')">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="exportarDesdeVista('csv')">
                                    <i class="fas fa-file-csv"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Vista Previa -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
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
                            <td colspan="{{ count($columnasMostrar) }}" class="text-center text-muted py-4">
                                <i class="fas fa-database fa-2x mb-3"></i>
                                <p>No se encontraron registros con los filtros aplicados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación (si fuera necesaria) -->
            @if($personas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="d-flex justify-content-center mt-4">
                {{ $personas->links() }}
            </div>
            @endif
        </div>

        <!-- Botones de Exportación -->
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> Modificar Reporte
                    </a>
                </div>
                <div class="btn-group">
                    <form id="exportExcelForm" action="{{ route('reportes.exportar.excel') }}" method="POST" class="d-inline">
                        @csrf
                        @foreach(request('columnas') as $columna)
                        <input type="hidden" name="columnas[]" value="{{ $columna }}">
                        @endforeach
                        @foreach(request()->except(['_token', 'columnas']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Exportar Excel
                        </button>
                    </form>

                    <form id="exportPdfForm" action="{{ route('reportes.exportar.pdf') }}" method="POST" class="d-inline">
                        @csrf
                        @foreach(request('columnas') as $columna)
                        <input type="hidden" name="columnas[]" value="{{ $columna }}">
                        @endforeach
                        @foreach(request()->except(['_token', 'columnas']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                        </button>
                    </form>

                    <form id="exportCsvForm" action="{{ route('reportes.exportar.csv') }}" method="POST" class="d-inline">
                        @csrf
                        @foreach(request('columnas') as $columna)
                        <input type="hidden" name="columnas[]" value="{{ $columna }}">
                        @endforeach
                        @foreach(request()->except(['_token', 'columnas']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-file-csv me-1"></i> Exportar CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportarDesdeVista(formato) {
    let formId = '';
    switch(formato) {
        case 'excel':
            formId = 'exportExcelForm';
            break;
        case 'pdf':
            formId = 'exportPdfForm';
            break;
        case 'csv':
            formId = 'exportCsvForm';
            break;
    }

    if (formId) {
        document.getElementById(formId).submit();
    }
}

// Imprimir vista previa
function imprimirVista() {
    window.print();
}
</script>

<style>
@media print {
    .card-header, .card-footer, .alert, .btn, .no-print {
        display: none !important;
    }

    .table {
        font-size: 12px;
    }

    h4 {
        font-size: 16px;
    }
}
</style>
@endpush
