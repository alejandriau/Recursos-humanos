{{-- resources/views/admin/certificados/reporte-vencimientos.blade.php --}}
@extends('dashboard')

@section('contenido')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">⚠️ Reporte de Certificados por Vencer y Vencidos</h2>
        <a href="{{ route('certificados.index') }}" class="btn btn-secondary">← Volver</a>
    </div>

    <div class="row">
        <!-- Certificados por vencer -->
        <div class="col-md-6 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">🟡 Certificados de Quechua por Vencer (Próximos 30 días)</h5>
                </div>
                <div class="card-body">
                    @if($quechuasPorVencer->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Persona</th>
                                        <th>Certificado</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Días Restantes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quechuasPorVencer as $cert)
                                        @php
                                            $diasRestantes = now()->diffInDays($cert->fecha_vencimiento, false);
                                        @endphp
                                        <tr>
                                            <td>{{ $cert->persona->nombreCompleto }}</td>
                                            <td>{{ $cert->nombre }}</td>
                                            <td>{{ $cert->fecha_vencimiento->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    {{ $diasRestantes }} días
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay certificados por vencer en los próximos 30 días.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Certificados vencidos -->
        <div class="col-md-6 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">🔴 Certificados de Quechua Vencidos</h5>
                </div>
                <div class="card-body">
                    @if($quechuasVencidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Persona</th>
                                        <th>Certificado</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Días Vencido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quechuasVencidos as $cert)
                                        @php
                                            $diasVencido = now()->diffInDays($cert->fecha_vencimiento);
                                        @endphp
                                        <tr>
                                            <td>{{ $cert->persona->nombreCompleto }}</td>
                                            <td>{{ $cert->nombre }}</td>
                                            <td>{{ $cert->fecha_vencimiento->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    {{ $diasVencido }} días
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay certificados vencidos.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">📊 Resumen de Certificados de Quechua</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h5 class="card-title">Total Certificados Quechua</h5>
                                    <h2 class="text-success">
                                        {{ $quechuasPorVencer->count() + $quechuasVencidos->count() }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Por Vencer</h5>
                                    <h2 class="text-warning">
                                        {{ $quechuasPorVencer->count() }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h5 class="card-title">Vencidos</h5>
                                    <h2 class="text-danger">
                                        {{ $quechuasVencidos->count() }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
