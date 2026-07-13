@extends('layouts.baseadm')

@section('title', 'Tipo de Salidas')

@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>REGISTRAR TIPO DE SALIDAS</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>
    </div>
@stop

@section('contenido')
    <form action="/tipo-salida/salida" method="post">
        @csrf
        
        <button type="submit" class="btn btn-primary w-25 mb-3">
            <i class="fa-solid fa-plus"></i> Añadir tipo de salida
        </button>

        {{-- ==================== FORMULARIO COMPACTO ==================== --}}
        <div class="container-fluid mt-2 pt-3 pb-2 rounded shadow bg-white">
            
            {{-- Fila 1: Información básica --}}
            <div class="row align-items-end mb-2">
                <div class="col-md-4">
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control form-control-sm" id="descripcion"
                            name="descripcion" placeholder="Motivo de salida" required>
                        <label for="descripcion">Descripción (*)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-2">
                        <textarea class="form-control form-control-sm" id="sustLegal" name="sustLegal" style="height: 58px;" required></textarea>
                        <label for="sustLegal">Sustento legal</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-2">
                        <select class="form-select" id="expresa" name="expresa" required>
                            <option value=""></option>
                            <option value="Dias">Días</option>
                            <option value="Horas">Horas</option>
                        </select>
                        <label for="expresa">Expresa en:</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-2">
                        <select class="form-select" name="id_padre">
                            <option value=""></option>
                            @foreach ($tipoSal as $salida)
                                @if ($salida->id_padre === null)
                                    <option value="{{ $salida->id }}">{{ $salida->descripcion }}</option>
                                @endif
                            @endforeach
                        </select>
                        <label for="id_padre">Dependencia</label>
                    </div>
                </div>
            </div>

            <hr class="my-2">

            {{-- Fila 2: Configuración de cupo y opciones --}}
            <div class="row align-items-center mb-2">
                <div class="col-md-1">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="tiene_cupo" name="tiene_cupo" value="1">
                        <label class="form-check-label" for="tiene_cupo"><b>Cupo</b></label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-2">
                        <select class="form-select" id="unidad" name="unidad">
                            <option value=""></option>
                            <option value="dias">Días</option>
                            <option value="horas">Horas</option>
                            <option value="mixto">Mixto</option>
                        </select>
                        <label for="unidad">Unidad</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-2">
                        <select class="form-select" id="periodicidad" name="periodicidad">
                            <option value="ninguna">Ninguna</option>
                            <option value="mensual">Mensual</option>
                            <option value="anual">Anual</option>
                            <option value="evento">Evento</option>
                        </select>
                        <label for="periodicidad">Periodicidad</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-2">
                        <input type="number" step="0.01" class="form-control form-control-sm" id="cantidad_default"
                            name="cantidad_default" placeholder="Ej: 2">
                        <label for="cantidad_default">Cant. default</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-2">
                        <input type="number" class="form-control form-control-sm" id="max_veces_periodo"
                            name="max_veces_periodo" placeholder="Ej: 1">
                        <label for="max_veces_periodo">Máx veces</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="permite_arrastre" name="permite_arrastre" value="1">
                        <label class="form-check-label" for="permite_arrastre"><b>Arrastre</b></label>
                    </div>
                </div>
            </div>

            <hr class="my-2">

            {{-- Fila 3: Opciones avanzadas (switches) --}}
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="usa_tabla_antiguedad" name="usa_tabla_antiguedad" value="1">
                        <label class="form-check-label" for="usa_tabla_antiguedad">Antigüedad</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="requiere_aprobacion_jefe" name="requiere_aprobacion_jefe" value="1" checked>
                        <label class="form-check-label" for="requiere_aprobacion_jefe">Aprueba Jefe</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="requiere_aprobacion_rrhh" name="requiere_aprobacion_rrhh" value="1" checked>
                        <label class="form-check-label" for="requiere_aprobacion_rrhh">Aprueba RRHH</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                        <label class="form-check-label" for="activo">Activo</label>
                    </div>
                </div>
            </div>

        </div>
    </form>

    {{-- ==================== TABLA COMPACTA ==================== --}}
    <div class="container-fluid mt-4 pt-3 pb-2 rounded shadow bg-white">
        <h5 class="text-center mb-3">
            <span class="text-secondary">Lista de registros - Tipo de salidas</span>
        </h5>

        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Sustento</th>
                        <th>Expresa</th>
                        <th>Dependencia</th>
                        <th>Cupo</th>
                        <th>Unidad</th>
                        <th>Periodicidad</th>
                        <th>Cant.</th>
                        <th>Arrastre</th>
                        <th>Máx.</th>
                        <th>Antigüedad</th>
                        <th>Aprob.</th>
                        <th>Estado</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tipoSal as $sal)
                        <tr>
                            <td><b>{{ $sal->id }}</b></td>
                            <td>{{ $sal->descripcion }}</td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 120px;" title="{{ $sal->sustLegal }}">
                                    {{ $sal->sustLegal }}
                                </span>
                            </td>
                            <td>{{ $sal->expresa }}</td>
                            <td>
                                @if($sal->id_padre)
                                    <span class="badge bg-info">{{ $sal->padre->descripcion ?? $sal->id_padre }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($sal->tiene_cupo)
                                    <span class="badge bg-success"><i class="fa-solid fa-check"></i> Sí</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fa-solid fa-xmark"></i> No</span>
                                @endif
                            </td>
                            <td>{{ $sal->unidad ?? '-' }}</td>
                            <td>
                                <span class="badge 
                                    @if($sal->periodicidad == 'mensual') bg-primary
                                    @elseif($sal->periodicidad == 'anual') bg-success
                                    @elseif($sal->periodicidad == 'evento') bg-warning text-dark
                                    @else bg-light text-dark border
                                    @endif">
                                    {{ ucfirst($sal->periodicidad) }}
                                </span>
                            </td>
                            <td>{{ $sal->cantidad_default ?? '-' }}</td>
                            <td>
                                @if($sal->permite_arrastre)
                                    <i class="fa-solid fa-rotate text-success" title="Sí permite arrastre"></i>
                                @else
                                    <i class="fa-solid fa-xmark text-muted" title="No permite arrastre"></i>
                                @endif
                            </td>
                            <td>{{ $sal->max_veces_periodo ?? '∞' }}</td>
                            <td>
                                @if($sal->usa_tabla_antiguedad)
                                    <span class="badge bg-warning text-dark">Sí</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                    @if($sal->requiere_aprobacion_jefe)
                                        <span class="badge bg-primary"><i class="fa-solid fa-user-tie"></i> Jefe</span>
                                    @endif
                                    @if($sal->requiere_aprobacion_rrhh)
                                        <span class="badge bg-secondary"><i class="fa-solid fa-users"></i> RRHH</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($sal->activo)
                                    <span class="badge bg-success"><i class="fa-solid fa-power-off"></i> Activo</span>
                                @else
                                    <span class="badge bg-danger"><i class="fa-solid fa-power-off"></i> Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="/tipo-salida/salida/{{ $sal->id }}" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                            <td>
                                <form action="/tipo-salida/salida/{{ $sal->id }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('¿Está seguro de eliminar este tipo de salida?')" 
                                        type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $tipoSal->links() }}
        </div>
    </div>
@stop

@section('css')
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        /* Ajustes para compactar */
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(2.5rem + 2px);
            padding: 0.5rem 0.75rem;
        }
        .form-floating > label {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
        .form-check.form-switch {
            padding-left: 2.5rem;
        }
        .form-check-input {
            width: 2rem;
            height: 1rem;
        }
        .table td, .table th {
            padding: 0.4rem 0.5rem;
            vertical-align: middle;
        }
        /* Opcional: ocultar texto de badges en móviles */
        @media (max-width: 768px) {
            .table td .badge .fa-solid {
                margin-right: 0;
            }
            .table td .badge {
                font-size: 0.7rem;
            }
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('js/funcionesJs.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>

    {{-- Mensajes de sesión --}}
    @if (session('status'))
        <script>
            Swal.fire({
                text: "Tipo de salida Registrado",
                icon: "success",
                confirmButtonText: "Aceptar",
                color: "#0e47ec",
            });
        </script>
    @endif
    @if (session('upstatus'))
        <script>
            Swal.fire({
                text: "Tipo de salida Modificado",
                icon: "success",
                color: "#FFB300",
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#FFB300"
            });
        </script>
    @endif
    @if (session('delstatus'))
        <script>
            Swal.fire({
                text: "Tipo de salida Eliminado",
                icon: "success",
                color: "#dc3545",
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#dc3545"
            });
        </script>
    @endif

    {{-- Lógica JS para mostrar/ocultar campos según "tiene_cupo" --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tieneCupo = document.getElementById('tiene_cupo');
            const unidad = document.getElementById('unidad');
            const cantidadDefault = document.getElementById('cantidad_default');
            const periodicidad = document.getElementById('periodicidad');
            const maxVeces = document.getElementById('max_veces_periodo');
            const permiteArrastre = document.getElementById('permite_arrastre');
            const usaTablaAntiguedad = document.getElementById('usa_tabla_antiguedad');

            function toggleCupoFields() {
                const habilitar = tieneCupo.checked;
                
                [unidad, cantidadDefault, periodicidad, maxVeces, permiteArrastre].forEach(el => {
                    el.disabled = !habilitar;
                    // Aplicar opacidad al contenedor padre (col-md-*)
                    const parentCol = el.closest('.col-md-1, .col-md-2, .col-md-3, .col-md-4');
                    if (parentCol) {
                        parentCol.style.opacity = habilitar ? '1' : '0.5';
                    }
                });

                // Si no tiene cupo, deshabilitar también tabla de antigüedad
                if (!habilitar) {
                    usaTablaAntiguedad.checked = false;
                    usaTablaAntiguedad.disabled = true;
                    const parentCol = usaTablaAntiguedad.closest('.col-md-2');
                    if (parentCol) parentCol.style.opacity = '0.5';
                } else {
                    usaTablaAntiguedad.disabled = false;
                    const parentCol = usaTablaAntiguedad.closest('.col-md-2');
                    if (parentCol) parentCol.style.opacity = '1';
                }
            }

            tieneCupo.addEventListener('change', toggleCupoFields);
            toggleCupoFields(); // Estado inicial
        });
    </script>
@stop