@extends('layouts.baseadm')

@section('content')
<div class="container-fluid py-3">
    <h4><i class="bi bi-cpu"></i> Generar Asistencia</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('asistencia.generar.store') }}">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" 
                               value="{{ old('fecha_inicio', now()->subDay()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha de fin</label>
                        <input type="date" name="fecha_fin" class="form-control" 
                               value="{{ old('fecha_fin', now()->subDay()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-play-fill"></i> Generar Asistencia
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Este proceso recalcula las asistencias para todas las personas activas en el rango de fechas indicado.
                        Las marcaciones biométricas y salidas aprobadas se cruzan con los horarios asignados.
                    </small>
                </div>
            </form>
            <hr>
            <h5>Programación automática</h5>
            <p>La asistencia se genera automáticamente cada día a las <strong>2:00 AM</strong> para el día anterior.</p>
            <p>Para modificar esta programación, edita el archivo <code>app/Console/Kernel.php</code>.</p>
        </div>
    </div>
</div>
@endsection