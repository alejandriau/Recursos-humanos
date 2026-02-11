@extends('dashboard')

@section('title', 'Editar Archivo: ' . $archivo->titulo)

@section('contenido')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit mr-2"></i>Editar Archivo
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('documentos.update', $archivo->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="titulo">Título del Archivo *</label>
                            <input type="text" 
                                   class="form-control @error('titulo') is-invalid @enderror" 
                                   id="titulo" 
                                   name="titulo" 
                                   value="{{ old('titulo', $archivo->titulo) }}"
                                   required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4">{{ old('descripcion', $archivo->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="tema">Tema/Categoría *</label>
                            <input type="text" 
                                   class="form-control @error('tema') is-invalid @enderror" 
                                   id="tema" 
                                   name="tema" 
                                   value="{{ old('tema', $archivo->tema) }}"
                                   required
                                   list="temas-list">
                            <datalist id="temas-list">
                                @foreach($temas as $tema)
                                    <option value="{{ $tema }}">
                                @endforeach
                            </datalist>
                            @error('tema')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label>Archivo actual:</label>
                            <div class="alert alert-light border">
                                <div class="d-flex align-items-center">
                                    <i class="fas {{ App\Http\Controllers\ArchivoController::getIcono($archivo->tipo) }} fa-2x mr-3 text-primary"></i>
                                    <div>
                                        <strong>{{ $archivo->nombre_archivo }}</strong><br>
                                        <small class="text-muted">
                                            {{ strtoupper($archivo->extension) }} - 
                                            {{ number_format($archivo->tamano / 1024, 2) }} KB
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> No se puede cambiar el archivo. 
                                Si necesitas subir una nueva versión, elimina este archivo y súbelo nuevamente.
                            </small>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('documentos.show', $archivo->id) }}" class="btn btn-secondary btn-lg">
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