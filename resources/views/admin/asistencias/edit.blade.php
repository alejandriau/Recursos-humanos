@extends('dashboard')

@section('title', 'Editar Asistencia')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Registro de Asistencia</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.asistencias.update', $asistencia) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Empleado</label>
                            <input type="text" class="form-control"
                                   value="{{ $asistencia->persona->nombre }} {{ $asistencia->persona->apellidoPat }}"
                                   readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="text" class="form-control"
                                   value="{{ $asistencia->fecha->format('d/m/Y') }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado *</label>
                                <select class="form-select @error('estado') is-invalid @enderror"
                                        id="estado" name="estado" required>
                                    @foreach($estados as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('estado', $asistencia->estado) == $key ? 'selected' : '' }}>
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
                    <div id="camposHorarios" style="{{ in_array($asistencia->estado, ['presente', 'tardanza']) ? 'display: block;' : 'display: none;' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hora_entrada" class="form-label">Hora de Entrada *</label>
                                    <input type="time" class="form-control @error('hora_entrada') is-invalid @enderror"
                                           id="hora_entrada" name="hora_entrada"
                                           value="{{ old('hora_entrada', $asistencia->hora_entrada ? \Carbon\Carbon::parse($asistencia->hora_entrada)->format('H:i') : '08:00') }}">
                                    @error('hora_entrada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hora_salida" class="form-label">Hora de Salida</label>
                                    <input type="time" class="form-control @error('hora_salida') is-invalid @enderror"
                                           id="hora_salida" name="hora_salida"
                                           value="{{ old('hora_salida', $asistencia->hora_salida ? \Carbon\Carbon::parse($asistencia->hora_salida)->format('H:i') : '18:00') }}">
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
                                  id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $asistencia->observaciones) }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información de cálculo -->
                    @if(in_array($asistencia->estado, ['presente', 'tardanza']))
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6>Información de Cálculo</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Minutos de retraso:</strong> {{ $asistencia->minutos_retraso }} min</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Horas extras:</strong> {{ $asistencia->horas_extras }} h</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.asistencias.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Actualizar Registro
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
});
</script>
@endpush
