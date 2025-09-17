@extends('dashboard')

@section('contenidouno')
    <title>Confirmar Eliminación</title>
    <style>
        .confirmation-card {
            border-left: 4px solid #dc3545;
        }
    </style>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card confirmation-card">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-circle me-2"></i>¡Advertencia!</h5>
                        <p class="mb-0">Está a punto de eliminar una designación. Esta acción no se puede deshacer.</p>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">Información de la Designación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Puesto:</strong> {{ $historial->puesto->denominacion }}</p>
                                    <p><strong>Item:</strong> {{ $historial->puesto->item }}</p>
                                    <p><strong>Persona:</strong> {{ $historial->persona->nombre }} {{ $historial->persona->apellidoPat }} {{ $historial->persona->apellidoMat }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Tipo:</strong> {{ ucfirst(str_replace('_', ' ', $historial->tipo_movimiento)) }}</p>
                                    <p><strong>Estado:</strong>
                                        <span class="badge bg-{{ $historial->estado == 'activo' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($historial->estado) }}
                                        </span>
                                    </p>
                                    <p><strong>Fecha Inicio:</strong> {{ $historial->fecha_inicio->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-info-circle me-2"></i>Consecuencias de la eliminación:</h6>
                        <ul class="mb-0">
                            <li>La designación será marcada como eliminada (soft delete)</li>
                            <li>El puesto quedará disponible para nuevas designaciones</li>
                            <li>El historial de la persona se verá afectado</li>
                            <li>Los reportes ya no incluirán esta designación</li>
                        </ul>
                    </div>

                    <form action="{{ route('historial.destroy', $historial->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="confirmacion" class="form-label">Para confirmar, escriba "ELIMINAR" en el siguiente campo:</label>
                            <input type="text" class="form-control" id="confirmacion" name="confirmacion"
                                   placeholder="Escriba ELIMINAR aquí" required>
                        </div>

                        <div class="mb-3">
                            <label for="motivo_eliminacion" class="form-label">Motivo de la eliminación:</label>
                            <textarea class="form-control" id="motivo_eliminacion" name="motivo_eliminacion"
                                      rows="3" placeholder="Explique el motivo de la eliminación..." required></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('historial') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-danger" id="btnEliminar" disabled>
                                <i class="fas fa-trash me-2"></i>Eliminar Definitivamente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputConfirmacion = document.getElementById('confirmacion');
    const btnEliminar = document.getElementById('btnEliminar');

    inputConfirmacion.addEventListener('input', function() {
        btnEliminar.disabled = this.value.toUpperCase() !== 'ELIMINAR';
    });

    // Validación antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
        if (inputConfirmacion.value.toUpperCase() !== 'ELIMINAR') {
            e.preventDefault();
            alert('Debe escribir "ELIMINAR" para confirmar la eliminación');
            return false;
        }

        if (!confirm('¿Está absolutamente seguro de eliminar esta designación? Esta acción es irreversible.')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection
