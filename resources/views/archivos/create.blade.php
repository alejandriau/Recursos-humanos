@extends('dashboard')

@section('title', 'Subir Archivo')

@section('contenido')
<div class="">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-upload mr-2"></i>Subir Nuevo Archivo
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Información del archivo -->
                                <div class="form-group">
                                    <label for="titulo">Título del Archivo *</label>
                                    <input type="text" 
                                           class="form-control @error('titulo') is-invalid @enderror" 
                                           id="titulo" 
                                           name="titulo" 
                                           value="{{ old('titulo') }}"
                                           required
                                           placeholder="Ej: Reporte Financiero Trimestral">
                                    @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="3"
                                              placeholder="Describe brevemente el contenido del archivo">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tema">Tema/Categoría *</label>
                                            <input type="text" 
                                                   class="form-control @error('tema') is-invalid @enderror" 
                                                   id="tema" 
                                                   name="tema" 
                                                   value="{{ old('tema') }}"
                                                   required
                                                   list="temas-list"
                                                   placeholder="Ej: Finanzas, Recursos Humanos, etc.">
                                            <datalist id="temas-list">
                                                @foreach($temas as $tema)
                                                    <option value="{{ $tema }}">
                                                @endforeach
                                            </datalist>
                                            @error('tema')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Los temas más usados aparecerán como sugerencias
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="archivo">Archivo *</label>
                                            <div class="custom-file">
                                                <input type="file" 
                                                       class="custom-file-input @error('archivo') is-invalid @enderror" 
                                                       id="archivo" 
                                                       name="archivo[]" multiple
                                                       required
                                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.txt,.zip,.rar">
                                                <label class="custom-file-label" for="archivo" id="file-label">
                                                    Seleccionar archivo...
                                                </label>
                                                @error('archivo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">
                                                Formatos permitidos: PDF, Word, Excel, imágenes, texto, comprimidos (Max: 10MB)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Panel de ayuda -->
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <i class="fas fa-info-circle mr-2"></i>Información Importante
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled small">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Tamaño máximo: 10MB
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Use títulos descriptivos
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Seleccione el tema adecuado
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Los documentos son visibles para todos
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!-- Vista previa del archivo -->
                                <div class="mt-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <i class="fas fa-eye mr-2"></i>Vista Previa
                                        </div>
                                        <div class="card-body text-center">
                                            <div id="file-preview" class="py-3">
                                                <i class="fas fa-file fa-4x text-muted"></i>
                                                <p class="mt-2 mb-0 text-muted" id="preview-text">
                                                    Seleccione un archivo para previsualizar
                                                </p>
                                                <small id="file-size" class="text-muted"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload mr-2"></i>Subir Archivo
                            </button>
                            <a href="{{ route('documentos.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mostrar nombre del archivo seleccionado
document.getElementById('archivo').addEventListener('change', function(e) {

    var files = e.target.files;
    var preview = document.getElementById('file-preview');
    var fileLabel = document.getElementById('file-label');

    if (files.length === 0) return;

    fileLabel.textContent = files.length + " archivo(s) seleccionados";

    preview.innerHTML = "";

    for (let i = 0; i < files.length; i++) {

        let file = files[i];
        let fileSize = (file.size / 1024).toFixed(2);
        let fileType = file.name.split('.').pop().toLowerCase();

        let iconClass = 'fa-file';

        if (fileType === 'pdf') iconClass = 'fa-file-pdf';
        else if (['doc','docx'].includes(fileType)) iconClass = 'fa-file-word';
        else if (['xls','xlsx','csv'].includes(fileType)) iconClass = 'fa-file-excel';
        else if (['jpg','jpeg','png','gif'].includes(fileType)) iconClass = 'fa-file-image';
        else if (['zip','rar','7z'].includes(fileType)) iconClass = 'fa-file-archive';
        else if (fileType === 'txt') iconClass = 'fa-file-alt';

        preview.innerHTML += `
            <div class="mb-3 border rounded p-2">
                <i class="fas ${iconClass} fa-2x text-primary"></i>
                <p class="mb-0">${file.name}</p>
                <small class="text-muted">${fileSize} KB</small>
            </div>
        `;
    }
});

    
    // Validar tamaño máximo
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        var fileInput = document.getElementById('archivo');
        var maxSize = 10 * 1024 * 1024; // 10MB en bytes
        
        if (fileInput.files[0] && fileInput.files[0].size > maxSize) {
            e.preventDefault();
            alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
            fileInput.value = '';
            document.getElementById('file-label').textContent = 'Seleccionar archivo...';
            document.getElementById('file-preview').innerHTML = `
                <i class="fas fa-file fa-4x text-muted"></i>
                <p class="mt-2 mb-0 text-muted">Seleccione un archivo para previsualizar</p>
            `;
        }
    });
</script>
@endpush