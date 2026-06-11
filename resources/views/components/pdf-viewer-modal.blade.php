<div class="modal fade" id="pdfViewerModalGlobal" tabindex="-1" aria-labelledby="pdfViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfViewerModalLabel">
                    <i class="fas fa-file-pdf text-danger me-2"></i>Visualizador de Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="pdfIframe" src="" width="100%" height="100%" style="border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a id="descargarPdfLink" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download me-2"></i>Descargar PDF
                </a>
            </div>
        </div>
    </div>
</div>