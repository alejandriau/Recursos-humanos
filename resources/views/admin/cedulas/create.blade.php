@extends('dashboard')

@section('title', 'Crear Cédula de Identidad')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nueva Cédula de Identidad</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('cedulas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="idPersona" class="form-label">Persona *</label>
                        <select class="form-select @error('idPersona') is-invalid @enderror"
                                id="idPersona" name="idPersona" required>
                            <option value="">Seleccionar Persona</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}" {{ old('idPersona') == $persona->id ? 'selected' : '' }}>
                                    {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}
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
                        <label for="ci" class="form-label">Número de C.I.</label>
                        <input type="text" class="form-control @error('ci') is-invalid @enderror"
                               id="ci" name="ci" value="{{ old('ci') }}" maxlength="45"
                               placeholder="Ej: 1234567 LP">
                        @error('ci')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fechanacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control @error('fechanacimiento') is-invalid @enderror"
                               id="fechanacimiento" name="fechanacimiento" value="{{ old('fechanacimiento') }}">
                        @error('fechanacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fechaVencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="date" class="form-control @error('fechaVencimiento') is-invalid @enderror"
                               id="fechaVencimiento" name="fechaVencimiento" value="{{ old('fechaVencimiento') }}">
                        @error('fechaVencimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="expedido" class="form-label">Expedido en</label>
                        <input type="text" class="form-control @error('expedido') is-invalid @enderror"
                               id="expedido" name="expedido" value="{{ old('expedido') }}" maxlength="100"
                               placeholder="Ej: La Paz, Bolivia">
                        @error('expedido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="nacido" class="form-label">Lugar de Nacimiento</label>
                <input type="text" class="form-control @error('nacido') is-invalid @enderror"
                       id="nacido" name="nacido" value="{{ old('nacido') }}" maxlength="1500"
                       placeholder="Lugar completo de nacimiento">
                @error('nacido')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="domicilio" class="form-label">Domicilio</label>
                <textarea class="form-control @error('domicilio') is-invalid @enderror"
                          id="domicilio" name="domicilio" rows="3" maxlength="1500"
                          placeholder="Dirección completa de domicilio">{{ old('domicilio') }}</textarea>
                @error('domicilio')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="observacion" class="form-label">Observación</label>
                <textarea class="form-control @error('observacion') is-invalid @enderror"
                          id="observacion" name="observacion" rows="2" maxlength="300"
                          placeholder="Observaciones adicionales">{{ old('observacion') }}</textarea>
                @error('observacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdfcedula" class="form-label">Archivo PDF de la Cédula</label>
                <input type="file" class="form-control @error('pdfcedula') is-invalid @enderror"
                       id="pdfcedula" name="pdfcedula" accept=".pdf">
                @error('pdfcedula')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Máximo 2MB, solo archivos PDF</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('cedulas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validación de fechas
    document.addEventListener('DOMContentLoaded', function() {
        const fechaNacimiento = document.getElementById('fechanacimiento');
        const fechaVencimiento = document.getElementById('fechaVencimiento');

        if (fechaNacimiento && fechaVencimiento) {
            fechaNacimiento.addEventListener('change', function() {
                if (this.value && fechaVencimiento.value && fechaVencimiento.value < this.value) {
                    alert('La fecha de vencimiento no puede ser anterior a la fecha de nacimiento.');
                    fechaVencimiento.value = '';
                }
            });

            fechaVencimiento.addEventListener('change', function() {
                if (this.value && fechaNacimiento.value && this.value < fechaNacimiento.value) {
                    alert('La fecha de vencimiento no puede ser anterior a la fecha de nacimiento.');
                    this.value = '';
                }
            });
        }
    });
</script>
@endpush
