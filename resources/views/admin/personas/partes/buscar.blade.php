@foreach($personas as $persona)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $persona->ci }}</td>
        <td>{{ $persona->nombre }}</td>
        <td>{{ $persona->apellidoPat }}</td>
        <td>{{ $persona->apellidoMat }}</td>
        <td>{{ $persona->fechaIngreso }}</td>
        <td>{{ $persona->fechaNacimiento }}</td>
        <td>{{ $persona->sexo }}</td>
        <td>{{ $persona->telefono }}</td>
        <td>
            <!-- Modal Trigger -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bajaModal{{ $persona->id }}">BAJA</button>
        </td>
    </tr>
    <!-- Modal -->
    <div class="modal fade" id="bajaModal{{ $persona->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"> <!-- Asegúrate de tener esta clase -->
                <form method="POST" action="{{ route('altasbajas.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Dar de baja <p>{{ $persona->apellidoPat." ". $persona->apellidoMat ." ". $persona->nombre }}</p></h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="idPersona" value="{{ $persona->id }}">
                        <input type="hidden" name="apellidopaterno" value="{{ $persona->apellidoPat }}">
                        <input type="hidden" name="apellidomaterno" value="{{ $persona->apellidoMat }}">
                        <input type="hidden" name="nombre" value="{{ $persona->nombre }}">

                        <div class="mb-3">
                            <label class="form-label">Fecha de retiro</label>
                            <input type="date" name="fechafin" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motivo</label>
                            <input type="text" name="motivo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="obser" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">PDF (Renuncia)</label>
                            <input type="file" name="pdffile" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach
