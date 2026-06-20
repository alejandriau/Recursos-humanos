@extends('layouts.baseadm')

@section('title', 'vacaciones')

@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>ASIGNACIÓN DE BENEFICIOS AL PERSONAL</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>
    </div>
@stop

@section('contenido')
    <div class="container-fluid pb-2 pt-3 rounded shadow bg-white ">
        <b>Seleccione datos para asignar beneficios</b>
        <hr>
        <div class="row">
            <div class="col-md-4">
                {{-- Buscador de personal --}}
                <div>
                    <div class="form-floating">
                        <input type="text" class="form-control form-control-sm " id="busquedaPersonal"
                            placeholder="Buscar por nombre, apellido o CI">
                        <label for="busquedaPersonal"><i class="fa-solid fa-magnifying-glass"></i> Buscar por nombre, apellido o CI</label>
                    </div>
                    <div id="resultadoBusqueda" class="list-group mt-2"></div>
                </div>
            </div>
            <div class="col-md-4">
                {{-- Mostrar personal seleccionado --}}
                <div class="form-floating">
                    <input type="text" class="form-control form-control-sm " id="personalSeleccionado"
                        placeholder="Personal seleccionado" readonly>
                    <label for="personalSeleccionado">Personal seleccionado</label>
                    <input type="hidden" id="persona_id">
                </div>
            </div>
            <div class="col-md-4">

                {{-- Selección de gestión habilitada --}}
                <div>
                    <div class="form-floating">
                        <select class="form-select" id="gestion_id" aria-label="Gestion">
                            @foreach ($gestiones as $gestion)
                            <option value="{{ $gestion->id }}">{{ $gestion->anio }}</option>
                        @endforeach
                        </select>
                        <label for="Gestion">Gestión</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="container-fluid">
                    {{-- Selección de tipos de salida con cantidad --}}
                    <div class="mb-3">
                        <label>Tipos de Salidas:</label>
                        <div class="row">
                            @foreach ($tiposalidas as $tipo)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input tipo-check" type="checkbox"
                                            value="{{ $tipo->id }}" id="tipo_{{ $tipo->id }}">
                                        <label class="form-check-label"
                                            for="tipo_{{ $tipo->id }}">{{ $tipo->descripcion }}</label>
                                    </div>
                                    <input type="number" min="0" step="0.1"
                                        class="form-control mt-1 cantidad-input" placeholder="Cantidad"
                                        id="cantidad_{{ $tipo->id }}" disabled>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-3"> {{-- Botón para guardar --}}
                <button class="btn btn-primary" onclick="guardarBeneficios()">
                    Asignar Beneficios <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
        <hr>
        <b>Beneficios Asignados</b>
        <table class="table table-bordered table-striped" id="tablaBeneficios">
            <thead class="table-dark">
                <tr>
                    <th>Tipo de Salida</th>
                    <th>Cantidad</th>
                    <th>Gestión</th>
                    <th>Nombre del Personal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Se llenará dinámicamente -->
            </tbody>
        </table>
    </div>
    <!-- Modal de edición de cantidad -->
    <div class="modal fade" id="modalEditarCantidad" tabindex="-1" aria-labelledby="modalEditarCantidadLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarCantidadLabel">Editar Cantidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarCantidad">
                        <input type="hidden" id="beneficioEditarId">
                        <div class="mb-3">
                            <label for="nuevaCantidad" class="form-label">Nueva Cantidad</label>
                            <input type="number" min="0" step="0.1" class="form-control" id="nuevaCantidad"
                                placeholder="Ingrese nueva cantidad">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEdicionCantidad()">Guardar
                        cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script>

        document.querySelectorAll('.tipo-check').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('cantidad_' + this.value).disabled = !this.checked;
            });
        });

        // Búsqueda de personal
        document.getElementById('busquedaPersonal').addEventListener('input', async function() {

            const valor = this.value;

            if (valor.length >= 2) {

                try {

                    const res = await axios.get(`/buscar-persona/beneficio?q=${valor}`);

                    console.log(res.data);

                    const resultados = res.data;

                    const resultadoDiv = document.getElementById('resultadoBusqueda');

                    resultadoDiv.innerHTML = '';

                    resultados.forEach(p => {

                        const item = document.createElement('a');

                        item.classList.add(
                            'list-group-item',
                            'list-group-item-action'
                        );

                        item.textContent =
                            `${p.nombre} ${p.apellidoPat} (${p.ci})`;

                        item.onclick = () => {

                            document.getElementById('personalSeleccionado').value =
                                item.textContent;

                            document.getElementById('persona_id').value =
                                p.id;

                            resultadoDiv.innerHTML = '';
                        };

                        resultadoDiv.appendChild(item);

                    });


                } catch(error) {

                    console.log(error);

                    if(error.response){
                        console.log(error.response.data);
                    }

                    Swal.fire(
                        'Error',
                        'Error al buscar personal',
                        'error'
                    );
                }
            }
        });

        // Guardar beneficios
        async function guardarBeneficios() {
            const personaId = document.getElementById('persona_id').value;
            const gestionId = document.getElementById('gestion_id').value;
            const tiposSeleccionados = [];

            document.querySelectorAll('.tipo-check:checked').forEach(chk => {
                const id = chk.value;
                const cantidad = document.getElementById('cantidad_' + id).value;
                //if (cantidad && !isNaN(cantidad)) {
                tiposSeleccionados.push({
                    tiposalida_id: id,
                    cantidad: parseFloat(cantidad)
                });
                // }
            });

            if (!personaId || !gestionId || tiposSeleccionados.length === 0) {
                return Swal.fire('Atención',
                    'Debe seleccionar personal, gestión y al menos un tipo de salida con cantidad.', 'warning');
            }

            try {
                const res = await axios.post('/guardar-beneficios', {
                    persona_id: personaId,
                    gestion_id: gestionId,
                    beneficios: tiposSeleccionados
                });

                Swal.fire('¡Éxito!', 'Beneficios asignados correctamente.', 'success');

                mostrarBeneficiosAsignados(res.data);

                limpiarFormulario();

            } catch (error) {

                console.log(error);

                let mensaje = 'Ocurrió un error al guardar';

                if (error.response && error.response.data) {
                    console.log(error.response.data);

                    mensaje = error.response.data.message ?? mensaje;
                }

                Swal.fire({
                    title: 'Error',
                    text: mensaje,
                    icon: 'error'
                });
            }
        }

        function mostrarBeneficiosAsignados(data) {
            const fila = document.createElement('tr');
            const tabla = document.querySelector('#tablaBeneficios tbody');
            tabla.innerHTML = ''; // Limpiar antes de insertar nuevos datos

            data.forEach(beneficio => {
                const fila = document.createElement('tr');
                fila.id = `beneficio-${beneficio.id}`;
                fila.innerHTML = `
            <td>${beneficio.tiposalida.descripcion}</td>
            <td>${beneficio.cantidad !== null ? beneficio.cantidad : 'N/A'}</td>
            <td>${beneficio.gestion.anio}</td>
            <td>${beneficio.persona.nombre} ${beneficio.persona.apellidoPat}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editarBeneficio(${beneficio.id})">Editar</button>
                <button class="btn btn-sm btn-danger" onclick="eliminarBeneficio(${beneficio.id})">Eliminar</button>
            </td>
        `;

                tabla.appendChild(fila);
            });
        }

        function limpiarFormulario() {
            // Limpiar el campo de búsqueda
            document.getElementById('busquedaPersonal').value = '';
            document.getElementById('resultadoBusqueda').innerHTML = '';

            // Limpiar personal seleccionado
            document.getElementById('personalSeleccionado').value = '';
            document.getElementById('persona_id').value = '';

            // Reiniciar la selección de gestión al primer valor habilitado (si quieres)
            document.getElementById('gestion_id').selectedIndex = 0;

            // Desmarcar todos los checkboxes y limpiar cantidades
            document.querySelectorAll('.tipo-check').forEach(checkbox => {
                checkbox.checked = false;
            });

            document.querySelectorAll('.cantidad-input').forEach(input => {
                input.value = '';
                input.disabled = true;
            });
        }
        // funcion para eliminar beneficio
        async function eliminarBeneficio(id) {
            const confirmacion = await Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (confirmacion.isConfirmed) {
                try {
                    await axios.delete(`/beneficio/${id}`);
                    Swal.fire('Eliminado', 'Beneficio eliminado correctamente.', 'success');

                    // Refrescar la tabla después de eliminar
                    // Puedes volver a cargar desde servidor o quitarlo manualmente de la tabla
                    document.querySelector(`#beneficio-${id}`).remove();
                } catch (error) {
                    Swal.fire('Error', 'No se pudo eliminar el beneficio.', 'error');
                }
            }
        }
        // funcion para abril modal para modificar el campo cantidad
        function editarBeneficio(id) {
            // Buscar la fila y obtener la cantidad actual
            const fila = document.querySelector(`#beneficio-${id}`);
            const cantidad = fila.children[1].textContent.trim();

            // Asignar valores al modal
            document.getElementById('beneficioEditarId').value = id;
            document.getElementById('nuevaCantidad').value = cantidad !== 'N/A' ? cantidad : '';

            // Mostrar el modal (usando Bootstrap)
            const modal = new bootstrap.Modal(document.getElementById('modalEditarCantidad'));
            modal.show();
        }
        // funcion para modificar el campo cantidad
        async function guardarEdicionCantidad() {
            const id = document.getElementById('beneficioEditarId').value;
            const nuevaCantidad = document.getElementById('nuevaCantidad').value;

            try {
                await axios.put(`/beneficio/${id}`, {
                    cantidad: nuevaCantidad !== '' ? parseFloat(nuevaCantidad) : null
                });

                Swal.fire('Actualizado', 'La cantidad fue actualizada correctamente.', 'success');

                // Cierra el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarCantidad'));
                modal.hide();

                // Actualiza la fila en la tabla (podrías hacer una consulta a backend si lo prefieres)
                const fila = document.querySelector(`#beneficio-${id}`);
                fila.children[1].textContent = nuevaCantidad !== '' ? nuevaCantidad : 'N/A';
            } catch (error) {
                Swal.fire('Error', 'No se pudo actualizar la cantidad.', 'error');
            }
        }
    </script>
@stop

@section('css')
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
@stop

@section('js')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('js/funcionesJs.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>




@stop
