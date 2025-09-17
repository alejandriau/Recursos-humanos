@extends('dashboard')

@section('contenido')
<div class="container">
    <div class="card shadow rounded-4 border-0 mb-4">
        <div class="card-body">
            <h3 class="card-title mb-4 text-primary fw-bold">
                <i class="bi bi-list-ul me-2"></i>Listado de registros de Pasivodos
            </h3>

            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha de Registro (inicio):</label>
                        <input type="date" name="fecha_inicio" class="form-control rounded-3 shadow-sm" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha de Registro (fin):</label>
                        <input type="date" name="fecha_fin" class="form-control rounded-3 shadow-sm" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha de Actualización (inicio):</label>
                        <input type="date" name="fecha_actualizacion_inicio" class="form-control rounded-3 shadow-sm" value="{{ request('fecha_actualizacion_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha de Actualización (fin):</label>
                        <input type="date" name="fecha_actualizacion_fin" class="form-control rounded-3 shadow-sm" value="{{ request('fecha_actualizacion_fin') }}">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle rounded-3 overflow-hidden">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre completo</th>
                            <th>Fecha Registro</th>
                            <th>Fecha Actualización</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pasivos as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->nombrecompleto }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->fechaRegistro)->format('Y-m-d H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->fechaActualizacion)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay registros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
