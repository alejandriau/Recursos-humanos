<form action="{{ route('archivos.store', $personas->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-2">
            <div class="mb-3">
                <label for="idPersona" class="form-label">CI</label>
                <input type="text" name="ci" class="form-control" value="{{$personas->ci}}" disabled>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="idPersona" class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{$personas->nombre}}" disabled>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="idPersona" class="form-label">Primer apellido</label>
                <input type="text" name="primerapellido" class="form-control" value="{{$personas->apellidoPat}}" disabled>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="idPersona" class="form-label">Segundo apellido</label>
                <input type="text" name="segundoapellido" class="form-control" value="{{$personas->apellidoMat}}" disabled>
            </div>
        </div>
    </div>

    <div class="mb-3">
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="">
                <select name="subcarpeta" class="form-select">
                    <option value="documentos_incorporacion">documentos_incorporacion</option>
                    <option value="documentos_formacion_academica_experiencia_laboral">documentos_formacion_academica_experiencia_laboral</option>
                    <option value="documentos_varios">documentos_varios</option>
                    <option value="documentos_desvinculacion">documentos_desvinculacion</option>

                </select>
            </div>
            <div class="mb-3">
                <label for="tipoDocumento" class="form-label">Tipo de Documento</label>
                <input type="text" name="tipoDocumento" class="form-control" required>
                <label for="archivo" class="form-label">Archivo</label>
                <input type="file" name="archivo" class="form-control" accept="application/pdf" required onchange="previewPDF(event)">
                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Subir</button>
            </div>
        </div>
        <div class="col-md-7">
            <!-- Vista previa del PDF -->
            <div class="mb-3" id="pdf-preview" style="display: none;">
                <label class="form-label">Vista previa:</label>
                <iframe id="pdf-viewer" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
        </div>
    </div>



</form>
