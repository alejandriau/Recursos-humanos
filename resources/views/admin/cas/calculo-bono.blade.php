@extends('dashboard')

@section('title', 'Cálculo de Bono')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calculator"></i>
                        Cálculo de Bono de Antigüedad
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('cas.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información de la Persona -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-user"></i> Información de la Persona</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Nombre:</strong> {{ $persona->nombre }} {{ $persona->paterno }} {{ $persona->materno }}<br>
                                        <strong>CI:</strong> {{ $persona->ci }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Fecha Ingreso:</strong> {{ \Carbon\Carbon::parse($persona->fecha_ingreso)->format('d/m/Y') }}<br>
                                        <strong>Estado:</strong> <span class="badge badge-success">Activo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cálculo de Antigüedad -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-clock"></i> Cálculo de Antigüedad
                                    </h4>
                                </div>
                                <div class="card-body text-center">
                                    <h2 class="text-primary">
                                        {{ $antiguedad['anios'] }}a {{ $antiguedad['meses'] }}m {{ $antiguedad['dias'] }}d
                                    </h2>
                                    <p class="text-muted">
                                        @if($antiguedad['tiene_cas'])
                                            <i class="fas fa-file-contract text-success"></i>
                                            Calculado desde CAS: {{ $antiguedad['antiguedad_base'] }}
                                        @else
                                            <i class="fas fa-calendar-alt text-warning"></i>
                                            Calculado desde fecha de ingreso
                                        @endif
                                    </p>
                                    <small class="text-info">
                                        Fecha base: {{ \Carbon\Carbon::parse($antiguedad['fecha_base'])->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resultado del Bono -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header {{ $calculoBono['aplica_bono'] ? 'bg-success' : 'bg-secondary' }} text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-money-bill-wave"></i> Resultado del Cálculo de Bono
                                    </h4>
                                </div>
                                <div class="card-body text-center">
                                    @if($calculoBono['aplica_bono'])
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h3 class="text-success">{{ $calculoBono['porcentaje'] }}%</h3>
                                                <p class="text-muted">Porcentaje</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h2 class="text-primary">Bs. {{ number_format($calculoBono['monto'], 2) }}</h2>
                                                <p class="text-muted">Monto del Bono</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h4 class="text-info">{{ $calculoBono['rango'] }}</h4>
                                                <p class="text-muted">Rango de Antigüedad</p>
                                            </div>
                                        </div>

                                        @if(isset($calculoBono['escala']))
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="alert alert-warning">
                                                    <strong>Base Legal:</strong> {{ $calculoBono['escala']->base_legal }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @else
                                        <h3 class="text-secondary">No aplica para bono de antigüedad</h3>
                                        <p class="text-muted">Se requiere mínimo 2 años de servicio</p>
                                        <div class="alert alert-info mt-3">
                                            <strong>Antigüedad actual:</strong> {{ $antiguedad['anios'] }}a {{ $antiguedad['meses'] }}m {{ $antiguedad['dias'] }}d
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-light">
                                <h5><i class="fas fa-info-circle"></i> Información del Cálculo</h5>
                                <ul class="mb-0">
                                    <li>El cálculo se realiza sobre el <strong>salario mínimo nacional vigente</strong></li>
                                    <li>Los porcentajes siguen la escala establecida en el D.S. N° 20862</li>
                                    <li>El bono se paga anualmente según la antigüedad certificada</li>
                                    @if($antiguedad['tiene_cas'])
                                        <li>La antigüedad incluye el tiempo del CAS más el tiempo transcurrido</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="text-center">
                        <a href="{{ route('cas.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                        @if(!$antiguedad['tiene_cas'] || !$antiguedad['cas_vigente'])
                            <a href="{{ route('cas.create', ['persona' => $persona->id]) }}" class="btn btn-success">
                                <i class="fas fa-file-contract"></i> Registrar CAS
                            </a>
                        @endif
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        .card-tools, .card-footer {
            display: none !important;
        }
        .alert {
            border: 1px solid #ccc !important;
        }
    }
</style>
@endsection
