@extends('dashboard')

@section('title', 'Registrar Asistencia')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Registrar Asistencia Manual</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.asistencias.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="idPersona" class="form-label">Empleado *</label>
                                <select class="form-select @error('idPersona') is-invalid @enderror"
                                        id="idPersona" name="idPersona" required>
                                    <option value="">Seleccionar empleado...</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}" {{ old('idPersona') == $empleado->id ? 'selected' : '' }}>
                                            {{ $empleado->nombre }} {{ $empleado->apellidoPat }} {{ $empleado->apellidoMat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idPersona')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha *</label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                                       id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado *</label>
                                <select class="form-select @error('estado') is-invalid @enderror"
                                        id="estado" name="estado" required>
                                    <option value="">Seleccionar estado...</option>
                                    @foreach($estados as $key => $value)
                                        <option value="{{ $key }}" {{ old('estado') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Campos condicionales para presente/tardanza -->
                    <div id="camposHorarios" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hora_entrada" class="form-label">Hora de Entrada *</label>
                                    <input type="time" class="form-control @error('hora_entrada') is-invalid @enderror"
                                           id="hora_entrada" name="hora_entrada" value="{{ old('hora_entrada', '08:00') }}">
                                    @error('hora_entrada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hora_salida" class="form-label">Hora de Salida</label>
                                    <input type="time" class="form-control @error('hora_salida') is-invalid @enderror"
                                           id="hora_salida" name="hora_salida" value="{{ old('hora_salida', '18:00') }}">
                                    @error('hora_salida')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                  id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.asistencias.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Guardar Registro
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
    const estadoSelect = document.getElementById('estado');
    const camposHorarios = document.getElementById('camposHorarios');
    const horaEntrada = document.getElementById('hora_entrada');
    const horaSalida = document.getElementById('hora_salida');

    function toggleCamposHorarios() {
        if (estadoSelect.value === 'presente' || estadoSelect.value === 'tardanza') {
            camposHorarios.style.display = 'block';
            horaEntrada.required = true;
        } else {
            camposHorarios.style.display = 'none';
            horaEntrada.required = false;
            horaSalida.required = false;
        }
    }

    estadoSelect.addEventListener('change', toggleCamposHorarios);

    // Ejecutar al cargar la página
    toggleCamposHorarios();
});
</script>
@endpush
