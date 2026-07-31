{{-- Esta vista se incluye tanto en el modal de crear como en el de editar --}}
{{-- Variable $edit (boolean) para distinguir si es edición --}}
{{-- Variable $tiposalida (opcional) contiene el modelo cuando se edita --}}

<div class="row">
    {{-- Fila 1: Información básica --}}
    <div class="row">
        <div class="col-md-4">
            <div class="form-floating mb-2">
                <input type="text" class="form-control" 
                       id="{{ $edit ? 'edit_descripcion' : 'descripcion' }}" 
                       name="descripcion" 
                       placeholder="Motivo de salida" 
                       value="{{ old('descripcion', $edit && isset($tiposalida) ? $tiposalida->descripcion : '') }}" required>
                <label for="{{ $edit ? 'edit_descripcion' : 'descripcion' }}">Descripción (*)</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating mb-2">
                <textarea class="form-control" id="{{ $edit ? 'edit_sustLegal' : 'sustLegal' }}" 
                          name="sustLegal" style="height: 58px;">{{ old('sustLegal', $edit && isset($tiposalida) ? $tiposalida->sustLegal : '') }}</textarea>
                <label for="{{ $edit ? 'edit_sustLegal' : 'sustLegal' }}">Sustento legal</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-floating mb-2">
                <select class="form-select" id="{{ $edit ? 'edit_expresa' : 'expresa' }}" name="expresa" required>
                    <option value=""></option>
                    <option value="Dias" {{ old('expresa', $edit && isset($tiposalida) ? $tiposalida->expresa : '') == 'Dias' ? 'selected' : '' }}>Días</option>
                    <option value="Horas" {{ old('expresa', $edit && isset($tiposalida) ? $tiposalida->expresa : '') == 'Horas' ? 'selected' : '' }}>Horas</option>
                </select>
                <label for="{{ $edit ? 'edit_expresa' : 'expresa' }}">Expresa en:</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating mb-2">
                <select class="form-select" id="{{ $edit ? 'edit_id_padre' : 'id_padre' }}" name="id_padre">
                    <option value=""></option>
                    @foreach ($tipoSal as $salida)
                        @if ($salida->id_padre === null)
                            <option value="{{ $salida->id }}" 
                                {{ old('id_padre', $edit && isset($tiposalida) ? $tiposalida->id_padre : '') == $salida->id ? 'selected' : '' }}>
                                {{ $salida->descripcion }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <label for="{{ $edit ? 'edit_id_padre' : 'id_padre' }}">Dependencia</label>
            </div>
        </div>
    </div>

    <hr class="my-2">

    {{-- Fila 2: Configuración de cupo y opciones --}}
    <div class="row align-items-center">
        <div class="col-md-1">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="{{ $edit ? 'edit_tiene_cupo' : 'tiene_cupo' }}" 
                       name="tiene_cupo" value="1" 
                       {{ old('tiene_cupo', $edit && isset($tiposalida) ? $tiposalida->tiene_cupo : false) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $edit ? 'edit_tiene_cupo' : 'tiene_cupo' }}"><b>Cupo</b></label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-floating mb-2">
                <select class="form-select" id="{{ $edit ? 'edit_unidad' : 'unidad' }}" name="unidad">
                    <option value=""></option>
                    <option value="dias" {{ old('unidad', $edit && isset($tiposalida) ? $tiposalida->unidad : '') == 'dias' ? 'selected' : '' }}>Días</option>
                    <option value="horas" {{ old('unidad', $edit && isset($tiposalida) ? $tiposalida->unidad : '') == 'horas' ? 'selected' : '' }}>Horas</option>
                    <option value="mixto" {{ old('unidad', $edit && isset($tiposalida) ? $tiposalida->unidad : '') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                </select>
                <label for="{{ $edit ? 'edit_unidad' : 'unidad' }}">Unidad</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-floating mb-2">
                <select class="form-select" id="{{ $edit ? 'edit_periodicidad' : 'periodicidad' }}" name="periodicidad">
                    <option value="ninguna" {{ old('periodicidad', $edit && isset($tiposalida) ? $tiposalida->periodicidad : '') == 'ninguna' ? 'selected' : '' }}>Ninguna</option>
                    <option value="mensual" {{ old('periodicidad', $edit && isset($tiposalida) ? $tiposalida->periodicidad : '') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                    <option value="anual" {{ old('periodicidad', $edit && isset($tiposalida) ? $tiposalida->periodicidad : '') == 'anual' ? 'selected' : '' }}>Anual</option>
                    <option value="evento" {{ old('periodicidad', $edit && isset($tiposalida) ? $tiposalida->periodicidad : '') == 'evento' ? 'selected' : '' }}>Evento</option>
                </select>
                <label for="{{ $edit ? 'edit_periodicidad' : 'periodicidad' }}">Periodicidad</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-floating mb-2">
                <input type="number" step="0.01" class="form-control" 
                       id="{{ $edit ? 'edit_cantidad_default' : 'cantidad_default' }}" 
                       name="cantidad_default" 
                       placeholder="Ej: 2"
                       value="{{ old('cantidad_default', $edit && isset($tiposalida) ? $tiposalida->cantidad_default : '') }}">
                <label for="{{ $edit ? 'edit_cantidad_default' : 'cantidad_default' }}">Cant. default</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-floating mb-2">
                <input type="number" class="form-control" 
                       id="{{ $edit ? 'edit_max_veces_periodo' : 'max_veces_periodo' }}" 
                       name="max_veces_periodo" 
                       placeholder="Ej: 1"
                       value="{{ old('max_veces_periodo', $edit && isset($tiposalida) ? $tiposalida->max_veces_periodo : '') }}">
                <label for="{{ $edit ? 'edit_max_veces_periodo' : 'max_veces_periodo' }}">Máx veces</label>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="{{ $edit ? 'edit_permite_arrastre' : 'permite_arrastre' }}" 
                       name="permite_arrastre" value="1"
                       {{ old('permite_arrastre', $edit && isset($tiposalida) ? $tiposalida->permite_arrastre : false) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $edit ? 'edit_permite_arrastre' : 'permite_arrastre' }}"><b>Arrastre</b></label>
            </div>
        </div>
    </div>

    <hr class="my-2">

    {{-- Fila 3: Opciones avanzadas (switches) --}}
    <div class="row align-items-center">
        <div class="col-md-2">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="{{ $edit ? 'edit_usa_tabla_antiguedad' : 'usa_tabla_antiguedad' }}" 
                       name="usa_tabla_antiguedad" value="1"
                       {{ old('usa_tabla_antiguedad', $edit && isset($tiposalida) ? $tiposalida->usa_tabla_antiguedad : false) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $edit ? 'edit_usa_tabla_antiguedad' : 'usa_tabla_antiguedad' }}">Antigüedad</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="{{ $edit ? 'edit_requiere_aprobacion_jefe' : 'requiere_aprobacion_jefe' }}" 
                       name="requiere_aprobacion_jefe" value="1"
                       {{ old('requiere_aprobacion_jefe', $edit && isset($tiposalida) ? $tiposalida->requiere_aprobacion_jefe : true) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $edit ? 'edit_requiere_aprobacion_jefe' : 'requiere_aprobacion_jefe' }}">Aprueba Jefe</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="{{ $edit ? 'edit_requiere_aprobacion_rrhh' : 'requiere_aprobacion_rrhh' }}" 
                       name="requiere_aprobacion_rrhh" value="1"
                       {{ old('requiere_aprobacion_rrhh', $edit && isset($tiposalida) ? $tiposalida->requiere_aprobacion_rrhh : true) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $edit ? 'edit_requiere_aprobacion_rrhh' : 'requiere_aprobacion_rrhh' }}">Aprueba RRHH</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="{{ $edit ? 'edit_activo' : 'activo' }}" 
                       name="activo" value="1"
                       {{ old('activo', $edit && isset($tiposalida) ? $tiposalida->activo : true) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $edit ? 'edit_activo' : 'activo' }}">Activo</label>
            </div>
        </div>
    </div>
</div>