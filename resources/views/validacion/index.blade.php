@extends('dashboard')

@section('title', 'Validar Perfil - Sistema de Gestión de Perfiles')
@section('header', 'Validación de Perfil para Puesto')

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle text-primary me-2"></i>
                        Validar si un candidato cumple con el perfil del puesto
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('validacion.validar') }}" method="POST" id="formValidacion">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_puesto" class="form-label fw-bold">
                                    <i class="fas fa-briefcase me-1"></i> Seleccionar Puesto
                                </label>
                                <select name="id_puesto" id="id_puesto" class="form-select" required>
                                    <option value="">-- Seleccione un puesto --</option>
                                    @foreach($puestos as $puesto)
                                        <option value="{{ $puesto->id }}" 
                                            data-perfil="{{ $puesto->tienePerfilDefinido ? 'si' : 'no' }}"
                                            {{ old('id_puesto') == $puesto->id ? 'selected' : '' }}>
                                            {{ $puesto->denominacion }} 
                                            @if($puesto->tienePerfilDefinido)
                                                <span class="badge bg-success">Perfil definido</span>
                                            @else
                                                <span class="badge bg-warning">Sin perfil</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div id="perfilInfo" class="mt-2 small text-muted"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="idPersona" class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i> Seleccionar Candidato
                                </label>
                                <select name="idPersona" id="idPersona" class="form-select" required>
                                    <option value="">-- Seleccione un candidato --</option>
                                    @foreach($personas as $persona)
                                        <option value="{{ $persona->id }}" 
                                            {{ old('idPersona') == $persona->id ? 'selected' : '' }}>
                                            {{ $persona->nombre_completo }} 
                                            @if($persona->profesionPrincipal)
                                                - {{ $persona->profesionPrincipal->nombreCompletoTitulo }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div id="candidatoInfo" class="mt-2 small text-muted"></div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle me-2"></i> Validar Perfil
                                </button>
                                <button type="button" class="btn btn-success btn-lg ms-2" id="btnValidarMultiple">
                                    <i class="fas fa-users me-2"></i> Validar Múltiple
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para validación múltiple -->
    <div class="modal fade" id="modalValidarMultiple" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-users me-2"></i>Validación Múltiple
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('validacion.validar-multiple') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Puesto</label>
                            <select name="id_puesto" id="puestoMultiple" class="form-select" required>
                                <option value="">-- Seleccione un puesto --</option>
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}">
                                        {{ $puesto->denominacion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Seleccionar Candidatos</label>
                            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                @foreach($personas as $persona)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="idsPersonas[]" value="{{ $persona->id }}" id="persona{{ $persona->id }}">
                                        <label class="form-check-label" for="persona{{ $persona->id }}">
                                            <strong>{{ $persona->nombre_completo }}</strong>
                                            @if($persona->profesionPrincipal)
                                                <span class="text-muted"> - {{ $persona->profesionPrincipal->nombreCompletoTitulo }}</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Validar Seleccionados</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Mostrar información del puesto seleccionado
        $('#id_puesto').change(function() {
            var selectedOption = $(this).find('option:selected');
            var tienePerfil = selectedOption.data('perfil');
            
            if (tienePerfil == 'si') {
                $('#perfilInfo').html('<i class="fas fa-check-circle text-success"></i> Este puesto tiene perfil definido. La validación será precisa.');
            } else {
                $('#perfilInfo').html('<i class="fas fa-exclamation-triangle text-warning"></i> Este puesto NO tiene perfil definido. Por favor defina el perfil primero.');
            }
            
            // Si no hay puesto seleccionado
            if (!$(this).val()) {
                $('#perfilInfo').html('');
            }
        });
        
        // Mostrar información del candidato
        $('#idPersona').change(function() {
            var selectedOption = $(this).find('option:selected');
            var texto = selectedOption.text();
            
            if (selectedOption.val()) {
                $('#candidatoInfo').html('<i class="fas fa-info-circle text-info"></i> Candidato seleccionado para validación');
            } else {
                $('#candidatoInfo').html('');
            }
        });
        
        // Abrir modal para validación múltiple
        $('#btnValidarMultiple').click(function() {
            $('#modalValidarMultiple').modal('show');
        });
        
        // Disparar eventos iniciales
        $('#id_puesto').trigger('change');
        $('#idPersona').trigger('change');
    });
</script>
@endpush
@endsection