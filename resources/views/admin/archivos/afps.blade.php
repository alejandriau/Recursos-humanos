@extends('dashboard')
@section('contenido')
<!-- Sale & Revenue Start -->
<div class="container-fluid pt-4 px-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-sm-12">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('successdelete'))
                    <div class="alert alert-success">
                        {{ session('successdelete') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="row  mt-4">
            <div class="">
                <div class=" bg-primary text-white">
                    <h5 class="mb-0">Registrar AFP</h5>
                </div>
                <div class="">
                    <form action="{{ route('afps.store', $persona->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Persona:</label>
                            <p class="form-control-plaintext">{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</p>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cua" class="form-label">CUA</label>
                                    <input type="text" class="form-control" id="cua" name="cua" required maxlength="45" value="">
                                    <input type="text" class="form-control" id="idPersona" name="idPersona" required maxlength="45" value="{{$persona->id}}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="observacion" class="form-label">Observación</label>
                                    <textarea class="form-control" id="observacion" name="observacion" maxlength="500" rows="3">{{ old('observacion') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pdfafps" class="form-label">PDF de AFP</label>
                            <input type="file" class="form-control" id="pdfafps" name="pdfafps" accept="application/pdf">
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="estado">
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Guardar AFP</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Sale & Revenue End -->
@endsection

