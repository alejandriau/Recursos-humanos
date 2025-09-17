@csrf

<div class="row">
    <div class="col-md-7">
        <div class="mb-3">
            <label for="denominacion" class="form-label">Denominación</label>
            <input type="text" name="denominacion" class="form-control" value="{{ old('denominacion', $puesto->denominacion ?? '') }}">
        </div>
    </div>
    <div class="col-md-5">
        <div class="mb-3">
            <label for="nivelgerarquico" class="form-label">Nivel Jerárquico *</label>
            <input type="text" name="nivelgerarquico" class="form-control" value="{{ old('nivelgerarquico', $puesto->nivelgerarquico ?? '') }}" required>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <div class="mb-3">
            <label for="item" class="form-label">Item</label>
            <input type="text" name="item" class="form-control" value="{{ old('item', $puesto->item ?? '') }}">
        </div>
    </div>
    <div class="col-md-2">
        <div class="mb-3">
            <label for="haber" class="form-label">Haber</label>
            <input type="number" step="0.01" name="haber" class="form-control" value="{{ old('haber', $puesto->haber ?? '') }}">
        </div>
    </div>
    <div class="col-md-2">
        <div class="mb-3">
            <label for="maual" class="form-label">Nivel</label>
            <input type="text" name="nivel" class="form-control" value="{{ old('nivel', $puesto->nivel ?? '') }}">
        </div>
    </div>
    <div class="col-md-6">
        {{-- Puedes reemplazar estos campos por selects si hay relaciones --}}
        <div class="mb-3">
            <label for="asignacion" class="form-label">Asignación del Puesto</label>

        </div>

    </div>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-3 mb-1">
                <label for="secretaria">Secretaría:</label>
                <select name="secretaria" id="secretaria" class="itmes-sel form-select">
                    <option value="">--Seleccione--</option>
                </select>
            </div>
            <div class="col-md-3 mb-1">
                <label for="direccion">Dirección</label>
                <select name="direccion" id="direccion" class="itmes-sel form-select">

                    <option value="">--Seleccione--</option>
                </select><br>
                <label for="unidadsecre" class="mt-1">Unidad</label>
                <select name="unidadsecre" id="unidadsecre" class="itmes-sel form-select">

                    <option value="">--Seleccione--</option>
                </select><br>
                <label for="areasecre" class="mt-1">Área</label>
                <select name="areasecre" id="areasecre" class="itmes-sel form-select">
                    <option value="">--Seleccione--</option>
                </select>
            </div>
            <div class="col-md-3 mb-1">
                <label for="unidad">Unidad</label>
                <select name="unidad" id="unidad" class="itmes-sel form-select">
                    <option value="">--Seleccione--</option>
                </select><br>
                <label for="areadire">Área </label>
                <select name="areadire" id="areadire" class="itmes-sel form-select">
                    <option value="">--Seleccione--</option>
                </select>
            </div>
            <div class="col-md-3 mb-1">
                <label for="area" class="mt-2">Área</label>
                <select name="area" id="area" class="itmes-sel form-select">
                    <option value="">--Seleccione--</option>
                </select>
            </div>
        </div>
        <div class="mb-1 item-cont">
            <div class="row">
                <div class="col-md-3">
                    <label for="items" class="mt-2">Items</label>
                </div>
                <div class="col-md-9">
                    <select name="items" id="items" class="itmes-sel">
                        <option value="">--Seleccione--</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>



@if (!empty($puesto) && isset($puesto->id))
<div class="mb-3">
    <label for="estado" class="form-label">Estado</label>
    <select name="estado" class="form-control">
        <option value="1" {{ old('estado', $puesto->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('estado', $puesto->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
    </select>
</div>
@endif

<button type="submit" class="btn btn-primary">
    {{ isset($puesto) ? 'Actualizar' : 'Guardar' }}
</button>
