{{-- resources/views/empleado/vacaciones/partials/form-vacacion.blade.php --}}
{{-- Este partial se usa tanto para crear como para editar --}}
<div>
    {{-- Campo oculto para ID de persona (se setea desde el script) --}}
    <input type="hidden" id="idpersona" value="{{ auth()->user()->persona_id ?? '' }}">

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <div class="form-floating mb-2">
                    <input type="text" class="form-control form-control-sm" id="nomb"
                           placeholder="Nombres y apellidos" disabled
                           value="{{ auth()->user()->persona->nombre ?? '' }} {{ auth()->user()->persona->apellidoPat ?? '' }} {{ auth()->user()->persona->apellidoMat ?? '' }}">
                    <label for="nomb">Servidor Público:</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-floating mb-2">
                    <select class="form-select tipoSal" id="salida" disabled>
                        @foreach ($tipoSal as $sal)
                            @if ($sal->descripcion == 'VACACION')
                                <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                            @endif
                        @endforeach
                    </select>
                    <label for="salida">Tipo de salida:</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-floating mb-2">
                    <input type="date" class="form-control fechasol" required readonly>
                    <label>Fecha de solicitud:</label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control fsalida" required>
                            <label>Inicio vacación:</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control fretorno" required>
                            <label>Fin vacación:</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control border-warning totaldias" readonly>
                            <label>Cantidad de días solicitadas:</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input mdia" type="checkbox" id="mdia">
                        <label class="form-check-label" for="mdia">Medio día</label>
                    </div>
                </div>
            </div>
            <div class="mb-2 form-floating">
                <input type="text" class="form-control border border-warning observacion"
                       placeholder="Observacion" required>
                <label>Observaciones:</label>
            </div>
            <div class="bg-secondary text-white p-2">
                <p><strong>Días disponibles de vacación:</strong> <span class="diasDisponiblesSpan">0</span></p>
            </div>
        </div>
    </div>
    <hr>

    {{-- Búsqueda de superior --}}
    <div class="row">
        <div class="col-md-6">
            <div class="row mb-3">
                <div class="col-md-10">
                    <div class="form-floating">
                        <input type="text" class="form-control dato" placeholder="Nombre o apellido" required>
                        <label>Buscar inmediato superior (nombre o apellido):</label>
                    </div>
                </div>
                <div class="col-md-2 text-start">
                    <button class="btn btn-success btnBuscarSup"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <input type="hidden" class="idSup">
                <div class="form-floating mb-2">
                    <input type="text" class="form-control nombreSup" readonly>
                    <label>Inmediato Superior Seleccionado:</label>
                </div>
            </div>
        </div>
    </div>

    {{-- Resultados de búsqueda --}}
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <table class="table">
                    <thead>
                        <tr><th>Nombre</th><th>Acción</th></tr>
                    </thead>
                    <tbody class="tablaSup">
                        <!-- Resultados dinámicos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
