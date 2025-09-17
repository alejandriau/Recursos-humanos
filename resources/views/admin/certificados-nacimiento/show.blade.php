@extends('dashboard')

@section('title', 'Detalles Certificado de Nacimiento')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles del Certificado de Nacimiento #{{ $certificado->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('certificados-nacimiento.edit', $certificado) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('certificados-nacimiento.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $certificado->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $certificado->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Fecha del Certificado:</strong> {{ $certificado->fecha->format('d/m/Y') }}
                </div>

                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $certificado->estado ? 'success' : 'danger' }}">
                        {{ $certificado->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                    @if($certificado->es_reciente)
                        <span class="badge bg-info ms-1">Reciente</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $certificado->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $certificado->fechaActualización ? $certificado->fechaActualización->format('d/m/Y H:i') : 'Nunca' }}
                </div>

                <div class="mb-3">
                    <strong>Archivo PDF:</strong>
                    @if($certificado->pdfcern)
                        <a href="{{ route('certificados-nacimiento.download', $certificado) }}" class="btn btn-success btn-sm ms-2">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    @else
                        <span class="text-muted">No hay archivo</span>
                    @endif
                </div>

                @if($certificado->persona && $certificado->persona->fechanacimiento && $certificado->edad_en_certificado)
                <div class="mb-3">
                    <strong>Edad en el certificado:</strong> {{ $certificado->edad_en_certificado }} años
                </div>
                @endif
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <strong>Descripción:</strong>
            <p class="text-muted">{{ $certificado->descripcion ?? 'Sin descripción' }}</p>
        </div>
    </div>
</div>
@endsection
