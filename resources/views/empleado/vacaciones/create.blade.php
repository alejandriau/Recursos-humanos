@extends('dashboard')

@section('title', 'Solicitar Vacaciones')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Solicitar Vacaciones</h5>
            </div>
            <div class="card-body">
                <!-- Resumen de días -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Días Disponibles</h6>
                    <p class="mb-0">Tienes <strong>{{ $diasDisponibles }} días</strong> de vacaciones disponibles para este año.</p>
                </div>

                <form action="{{ route('empleado.vacaciones.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio *</label>
                                <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror"
                                       id="fecha_inicio" name="fecha_inicio"
                                       value="{{ old('fecha_inicio') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin *</label>
                                <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror"
                                       id="fecha_fin" name="fecha_fin"
                                       value="{{ old('fecha_fin') }}">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo (Opcional)</label>
                        <textarea class="form-control @error('motivo') is-invalid @enderror"
                                  id="motivo" name="motivo" rows="3"
                                  placeholder="Describe el motivo de tu solicitud...">{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Resumen de la solicitud -->
                    <div class="card mb-3 d-none" id="resumenSolicitud">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Resumen de la Solicitud</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Días solicitados:</strong> <span id="diasSolicitados">0</span> días hábiles</p>
                            <p><strong>Días disponibles después:</strong> <span id="diasDespues">0</span> días</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('empleado.vacaciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const resumenSolicitud = document.getElementById('resumenSolicitud');
    const diasSolicitados = document.getElementById('diasSolicitados');
    const diasDespues = document.getElementById('diasDespues');
    const diasDisponibles = {{ $diasDisponibles }};

    function calcularDiasHabiles() {
        if (fechaInicio.value && fechaFin.value) {
            const inicio = new Date(fechaInicio.value);
            const fin = new Date(fechaFin.value);

            if (inicio <= fin) {
                // Simulación simple del cálculo (en producción se haría en el backend)
                let dias = 0;
                let currentDate = new Date(inicio);

                while (currentDate <= fin) {
                    const day = currentDate.getDay();
                    if (day !== 0 && day !== 6) { // Excluir fines de semana
                        dias++;
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                diasSolicitados.textContent = dias;
                diasDespues.textContent = diasDisponibles - dias;
                resumenSolicitud.classList.remove('d-none');
            }
        }
    }

    fechaInicio.addEventListener('change', calcularDiasHabiles);
    fechaFin.addEventListener('change', calcularDiasHabiles);
});
</script>
@endpush
