
@if(isset($profesion))
    @method('PUT')
@endif

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="diploma" class="form-label">Título/Diploma</label>
        <input type="text" name="diploma" id="diploma" class="form-control"
               value="{{ old('diploma', $profesion->diploma ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="fechaDiploma" class="form-label">Fecha del Diploma</label>
        <input type="date" name="fechaDiploma" id="fechaDiploma" class="form-control"
               value="{{ old('fechaDiploma', $profesion->fechaDiploma ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="provisionN" class="form-label">N° Provisión Nacional</label>
        <input type="text" name="provisionN" id="provisionN" class="form-control"
               value="{{ old('provisionN', $profesion->provisionN ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="fechaProvision" class="form-label">Fecha de Provisión</label>
        <input type="date" name="fechaProvision" id="fechaProvision" class="form-control"
               value="{{ old('fechaProvision', $profesion->fechaProvision ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="universidad" class="form-label">Universidad</label>
        <input type="text" name="universidad" id="universidad" class="form-control"
               value="{{ old('universidad', $profesion->universidad ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="registro" class="form-label">N° de Registro</label>
        <input type="text" name="registro" id="registro" class="form-control"
               value="{{ old('registro', $profesion->registro ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="cedulaProfesion" class="form-label">Cédula Profesional</label>
        <input type="text" name="cedulaProfesion" id="cedulaProfesion" class="form-control"
               value="{{ old('cedulaProfesion', $profesion->cedulaProfesion ?? '') }}">
    </div>

    <div class="col-md-12 mb-3">
        <label for="observacion" class="form-label">Observaciones</label>
        <textarea name="observacion" id="observacion" class="form-control" rows="3">{{ old('observacion', $profesion->observacion ?? '') }}</textarea>
    </div>

    <div class="col-md-4 mb-3">
        <label for="pdfDiploma" class="form-label">PDF del Diploma</label>
        <input type="file" name="pdfDiploma" id="pdfDiploma" class="form-control" accept="application/pdf">
    </div>

    <div class="col-md-4 mb-3">
        <label for="pdfProvision" class="form-label">PDF de Provisión</label>
        <input type="file" name="pdfProvision" id="pdfProvision" class="form-control" accept="application/pdf">
    </div>

    <div class="col-md-4 mb-3">
        <label for="pdfcedulap" class="form-label">PDF Cédula Profesional</label>
        <input type="file" name="pdfcedulap" id="pdfcedulap" class="form-control" accept="application/pdf">
    </div>

    <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary">
            {{ isset($profesion) ? 'Actualizar Profesión' : 'Registrar Profesión' }}
        </button>
    </div>
</div>
