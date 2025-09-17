@extends('dashboard')

@section('title', 'Detalles Cédula de Identidad')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles de Cédula de Identidad #{{ $cedula->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('cedulas.edit', $cedula) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('cedulas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $cedula->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $cedula->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Número de C.I.:</strong> {{ $cedula->ci ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Fecha de Nacimiento:</strong>
                    {{ $cedula->fechanacimiento ? $cedula->fechanacimiento->format('d/m/Y') : 'N/A' }}
                    @if($cedula->fechanacimiento)
                        <br><span class="text-muted">({{ $cedula->edad }} años)</span>
                    @endif
                </div>

                <div class="mb-3">
                    <strong>Fecha de Vencimiento:</strong>
                    {{ $cedula->fechaVencimiento ? $cedula->fechaVencimiento->format('d/m/Y') : 'N/A' }}
                    @if($cedula->fechaVencimiento)
                        <br>
                        @if($cedula->esta_vencida)
                            <span class="badge bg-danger">Vencida</span>
                        @else
                            <span class="badge bg-success">{{ $cedula->dias_restantes }} días restantes</span>
                        @endif
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Expedido en:</strong> {{ $cedula->expedido ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $cedula->estado ? 'success' : 'danger' }}">
                        {{ $cedula->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $cedula->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $cedula->fechaActualizacion ? $cedula->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>

                <div class="mb-3">
                    <strong>Archivo PDF:</strong>
                    @if($cedula->pdfcedula)
                        <a href="{{ route('cedulas.download', $cedula) }}" class="btn btn-success btn-sm ms-2">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    @else
                        <span class="text-muted">No hay archivo</span>
                    @endif
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Lugar de Nacimiento:</strong>
                    <p class="text-muted">{{ $cedula->nacido ?? 'No especificado' }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Domicilio:</strong>
                    <p class="text-muted">{{ $cedula->domicilio ?? 'No especificado' }}</p>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <strong>Observación:</strong>
            <p class="text-muted">{{ $cedula->observacion ?? 'Sin observaciones' }}</p>
        </div>
    </div>
</div>
@endsection
