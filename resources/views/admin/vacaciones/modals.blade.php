<!-- Modal para aprobar -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAprobar" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Aprobar Solicitud de Vacaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentario (Opcional)</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="3"
                                  placeholder="Agregar un comentario..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Rechazar Solicitud de Vacaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="motivo_rechazo" class="form-label">Motivo del Rechazo *</label>
                        <textarea class="form-control" id="motivo_rechazo" name="motivo_rechazo" rows="3"
                                  placeholder="Explica el motivo del rechazo..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>
