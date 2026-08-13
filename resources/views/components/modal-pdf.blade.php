<!-- Modal Único para Ver PDF -->
<div class="modal fade" id="modalPdf" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf me-2"></i>
                    PDF    <span id="pdfNombre"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <a href="#" id="pdfDownloadBtn" class="btn btn-success me-2">
                        <i class="fas fa-download me-1"></i>Descargar PDF
                    </a>
                    <button type="button" class="btn btn-primary" id="pdfPrintBtn">
                        <i class="fas fa-print me-1"></i>Imprimir
                    </button>
                </div>

                <div class="embed-responsive embed-responsive-16by9">
                    <iframe src="" class="w-100"
                            style="height: 80vh; border: none;"
                            id="pdfIframe">
                    </iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>