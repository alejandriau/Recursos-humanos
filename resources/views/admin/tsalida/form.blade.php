{{--
    Partial: _form.blade.php
    Variables esperadas: $tiposalida (puede ser null en create), $unidades, $periodicidades
--}}

@php
    $t = $tiposalida ?? null;
@endphp

{{-- Descripción --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
    <input type="text" name="descripcion"
           class="form-control @error('descripcion') is-invalid @enderror"
           value="{{ old('descripcion', $t?->descripcion) }}"
           placeholder="Ej: VACACION, COMISION, 2 HORAS..."
           required>
    @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Sustento legal --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Sustento Legal</label>
    <textarea name="sustLegal" rows="2"
              class="form-control @error('sustLegal') is-invalid @enderror"
              placeholder="Ej: PREVISTO POR EL ARTICULO 13 DEL RIP...">{{ old('sustLegal', $t?->sustLegal) }}</textarea>
    @error('sustLegal')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Unidad --}}
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Unidad de medida <span class="text-danger">*</span></label>
        <select name="unidad" id="unidad"
                class="form-select @error('unidad') is-invalid @enderror" required>
            @foreach($unidades as $key => $label)
                <option value="{{ $key }}" {{ old('unidad', $t?->unidad) === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('unidad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Flujo de aprobación --}}
    <div class="col-md-8">
        <label class="form-label fw-semibold">Flujo de aprobación</label>
        <div class="d-flex gap-4 mt-1">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="requiere_aprobacion_jefe"
                       id="req_jefe" value="1"
                       {{ old('requiere_aprobacion_jefe', $t?->requiere_aprobacion_jefe ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="req_jefe">
                    <i class="fas fa-user-tie text-warning me-1"></i>Jefe inmediato
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="requiere_aprobacion_rrhh"
                       id="req_rrhh" value="1"
                       {{ old('requiere_aprobacion_rrhh', $t?->requiere_aprobacion_rrhh ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="req_rrhh">
                    <i class="fas fa-users text-info me-1"></i>RRHH
                </label>
            </div>
        </div>
    </div>
</div>

{{-- ¿Tiene cupo? --}}
<div class="mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="tiene_cupo"
               id="tiene_cupo" value="1"
               {{ old('tiene_cupo', $t?->tiene_cupo) ? 'checked' : '' }}>
        <label class="form-check-label fw-semibold" for="tiene_cupo">
            ¿Este tipo de salida tiene cupo limitado?
        </label>
    </div>
    <small class="text-muted">
        Sin cupo: comisión, salud, particular (solo se registra el hecho, sin descontar saldo).<br>
        Con cupo: vacación, 2 horas/mes, 2 días/año (se descuenta de un saldo asignado).
    </small>
</div>

{{-- Sección cupo (visible solo si tiene_cupo = true) --}}
<div id="seccion-cupo" class="{{ old('tiene_cupo', $t?->tiene_cupo) ? '' : 'd-none' }}">
    <div class="card bg-light border-0 p-3 mb-3">
        <div class="row g-3">

            {{-- Periodicidad --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold">Periodicidad de renovación</label>
                <select name="periodicidad" id="periodicidad"
                        class="form-select @error('periodicidad') is-invalid @enderror">
                    @foreach($periodicidades as $key => $label)
                        <option value="{{ $key }}" {{ old('periodicidad', $t?->periodicidad) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('periodicidad')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Cantidad default --}}
            <div class="col-md-4" id="col-cantidad">
                <label class="form-label fw-semibold">Cantidad por período</label>
                <input type="number" name="cantidad_default" step="0.5" min="0.5"
                       class="form-control @error('cantidad_default') is-invalid @enderror"
                       value="{{ old('cantidad_default', $t?->cantidad_default) }}"
                       placeholder="Ej: 2">
                <small class="text-muted" id="hint-cantidad">Días u horas que se asignan cada período</small>
                @error('cantidad_default')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Max veces (solo evento) --}}
            <div class="col-md-4" id="col-max-veces">
                <label class="form-label fw-semibold">Máximo de veces por período</label>
                <input type="number" name="max_veces_periodo" min="1"
                       class="form-control @error('max_veces_periodo') is-invalid @enderror"
                       value="{{ old('max_veces_periodo', $t?->max_veces_periodo) }}"
                       placeholder="Ej: 1">
                <small class="text-muted">Cuántas veces puede usarse este permiso por período</small>
            </div>

            {{-- Switches --}}
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="permite_arrastre"
                           id="permite_arrastre" value="1"
                           {{ old('permite_arrastre', $t?->permite_arrastre) ? 'checked' : '' }}>
                    <label class="form-check-label" for="permite_arrastre">
                        Permite arrastre de saldo al siguiente período
                    </label>
                </div>
                <small class="text-muted">Vacación: sí. 2 horas/mes: no (se pierde al fin de mes).</small>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="usa_tabla_antiguedad"
                           id="usa_tabla_antiguedad" value="1"
                           {{ old('usa_tabla_antiguedad', $t?->usa_tabla_antiguedad) ? 'checked' : '' }}>
                    <label class="form-check-label" for="usa_tabla_antiguedad">
                        Días según antigüedad del empleado (15/20/30)
                    </label>
                </div>
                <small class="text-muted">Solo para VACACIÓN. Ignora la "Cantidad por período".</small>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tieneCupo       = document.getElementById('tiene_cupo');
    const seccionCupo     = document.getElementById('seccion-cupo');
    const periodicidad    = document.getElementById('periodicidad');
    const colMaxVeces     = document.getElementById('col-max-veces');
    const colCantidad     = document.getElementById('col-cantidad');
    const usaAntiguedad   = document.getElementById('usa_tabla_antiguedad');

    function toggleCupo() {
        seccionCupo.classList.toggle('d-none', !tieneCupo.checked);
        togglePeriodicidad();
    }

    function togglePeriodicidad() {
        const esEvento = periodicidad.value === 'evento';
        colMaxVeces.classList.toggle('d-none', !esEvento);

        // Si usa tabla de antigüedad, ocultar cantidad_default
        const ocultarCantidad = usaAntiguedad.checked;
        colCantidad.classList.toggle('d-none', ocultarCantidad);
    }

    tieneCupo.addEventListener('change', toggleCupo);
    periodicidad.addEventListener('change', togglePeriodicidad);
    usaAntiguedad.addEventListener('change', togglePeriodicidad);

    // Estado inicial
    toggleCupo();
});
</script>
@endpush