{{-- resources/views/reportes/detalle.blade.php --}}
@extends('layouts.baseadm')

@section('contenido')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4>Certificado de Aportes</h4>
            <div>
                <a href="{{ route('persona.exportar.aportes.pdf', $persona->id) }}" class="btn btn-danger btn-sm">📄 Exportar PDF</a>
                <a href="{{ route('persona.exportar.planilla.word', $persona->id) }}" class="btn btn-primary btn-sm">📝 Exportar Word</a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <p><strong>CI:</strong> {{ $persona->ci }}</p>
                <p><strong>Nombre completo:</strong> {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</p>
                <p><strong>Fecha de nacimiento:</strong> {{ optional($persona->fechaNacimiento)->format('d/m/Y') }}</p>
            </div>

            @foreach($planillasPorAnio as $anio => $planillas)
                <div class="mb-5">
                    <h5 class="bg-secondary text-white p-2">Gestión {{ $anio }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Días Trab.</th>
                                    <th>Haber Básico</th>
                                    <th>Total Ganado</th>
                                    <th>APORTE A LA SEGURIDAD SOCIAL DE LARGO PLAZO</th>
                                    <th>Nivel jerarquico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planillas as $p)
                                    @php
                                        $mesNombre = \Carbon\Carbon::create()->month($p->mes)->locale('es')->monthName;
                                    @endphp
                                    <tr>
                                        <td>{{ $mesNombre }}</td>
                                        <td>{{ $p->dia_trab ?? '-' }}</td>
                                        <td>{{ number_format($p->h_basico ?? 0, 2) }}</td>
                                        <td>{{ number_format($p->neto ?? 0, 2) }}</td>
                                        <td>{{ number_format($p->t_afp ?? 0, 2) }}</td>
                                        <td>{{ $p->cargo ?? '_' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            @if($planillasPorAnio->isEmpty())
                <div class="alert alert-info">No se encontraron registros de planillas para esta persona.</div>
            @endif
        </div>
        <div class="card-footer text-muted">
            Certificación generada el {{ now()->format('d/m/Y') }}
        </div>
    </div>
</div>
@endsection