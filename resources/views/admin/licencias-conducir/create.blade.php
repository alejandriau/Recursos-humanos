@extends('dashboard')

@section('title', 'Crear Licencia de Conducir')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nueva Licencia de Conducir</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('licencias-conducir.store') }}" method="POST" enctype="multipart/form-data">
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
                                    {{ $persona->nombre }}
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
                        <label for="categoria" class="form-label">Categoría *</label>
                        <select class="form-select @error('categoria') is-invalid @enderror"
                                id="categoria" name="categoria" required>
                            <option value="">Seleccionar Categoría</option>
                            <option value="A" {{ old('categoria') == 'A' ? 'selected' : '' }}>A - Motocicletas</option>
                            <option value="B" {{ old('categoria') == 'B' ? 'selected' : '' }}>B - Vehículos particulares</option>
                            <option value="C" {{ old('categoria') == 'C' ? 'selected' : '' }}>C - Vehículos de carga</option>
                            <option value="D" {{ old('categoria') == 'D' ? 'selected' : '' }}>D - Transporte público</option>
                            <option value="E" {{ old('categoria') == 'E' ? 'selected' : '' }}>E - Maquinaria pesada</option>
                        </select>
                        @error('categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fechavencimiento" class="form-label">Fecha de Vencimiento *</label>
                        <input type="date" class="form-control @error('fechavencimiento') is-invalid @enderror"
                               id="fechavencimiento" name="fechavencimiento" value="{{ old('fechavencimiento') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        @error('fechavencimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">La fecha debe ser posterior a hoy</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion" name="descripcion" rows="3" maxlength="500"
                          placeholder="Observaciones o restricciones de la licencia">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdflicc" class="form-label">Archivo PDF de la Licencia</label>
                <input type="file" class="form-control @error('pdflicc') is-invalid @enderror"
                       id="pdflicc" name="pdflicc" accept=".pdf">
                @error('pdflicc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Máximo 2MB, solo archivos PDF</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('licencias-conducir.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validación de fecha mínima
    document.addEventListener('DOMContentLoaded', function() {
        const fechaVencimiento = document.getElementById('fechavencimiento');
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);

        // Establecer el mínimo como mañana
        const minDate = tomorrow.toISOString().split('T')[0];
        fechaVencimiento.min = minDate;
    });
</script>
@endpush
