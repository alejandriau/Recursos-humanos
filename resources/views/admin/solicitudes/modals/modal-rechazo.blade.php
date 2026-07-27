{{-- resources/views/rrhh/modals/modal-rechazo.blade.php --}}
<!-- Modal Rechazo -->
<div class="modal fade" id="modalRechazo" tabindex="-1" aria-labelledby="modalRechazoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRechazoLabel">
                    <i class="fas fa-times-circle"></i> Rechazar Solicitud
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>¡Atención!</strong> Esta acción rechazará la solicitud y no se descontarán los días.
                </div>

                <form id="formRechazo">
                    @csrf
                    <input type="hidden" id="rechazo_id" name="id">

                    <div class="mb-3">
                        <label for="observacion_rechazo" class="form-label">
                            <i class="fas fa-comment"></i> Motivo del Rechazo <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="observacion_rechazo" name="observacion"
                                  rows="4" required placeholder="Explique el motivo del rechazo..."></textarea>
                        <div class="form-text">Máximo 500 caracteres</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmRechazo" required>
                            <label class="form-check-label" for="confirmRechazo">
                                Confirmo que esta solicitud será rechazada
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmarRechazo()">
                    <i class="fas fa-times"></i> Rechazar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>
