@extends('dashboard')

@section('contenido')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-search me-2"></i>Buscar persona
            </h5>

            <a href="{{ route('planillas.import.form') }}" class="btn btn-light d-flex align-items-center">
                <i class="fas fa-upload me-2"></i>Subir planilla
            </a>

        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('persona.buscar.planillas') }}">
                <div class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="q" class="form-control" placeholder="Ingrese CI o nombres..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </div>
            </form>

            @if(isset($personas))
                <hr>
                <h5>Resultados ({{ $personas->count() }})</h5>
                @if($personas->count())
                    <div class="list-group mt-3">
                        @foreach($personas as $persona)
                            <a href="{{ route('persona.planillas.mostrar', $persona->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $persona->ci }} - {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h5>
                                    <small>Ingreso: {{ $persona->fechaIngreso }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning mt-3">No se encontraron personas con ese criterio.</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection