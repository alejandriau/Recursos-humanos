<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Información del Período Temporal</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            @if($periodo->fecha_probable_parto)
                            <tr>
                                <th width="50%">Fecha Probable Parto:</th>
                                <td>{{ \Carbon\Carbon::parse($periodo->fecha_probable_parto)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            @if($periodo->fecha_nacimiento)
                            <tr>
                                <th>Fecha Nacimiento:</th>
                                <td>{{ \Carbon\Carbon::parse($periodo->fecha_nacimiento)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            @if($periodo->fecha_inicio_lactancia)
                            <tr>
                                <th>Inicio Lactancia:</th>
                                <td>{{ \Carbon\Carbon::parse($periodo->fecha_inicio_lactancia)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            @if($periodo->fecha_fin_lactancia)
                            <tr>
                                <th>Fin Lactancia:</th>
                                <td>{{ \Carbon\Carbon::parse($periodo->fecha_fin_lactancia)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Días Prenatales:</th>
                                <td>{{ $periodo->dias_prenatales }} días</td>
                            </tr>
                            <tr>
                                <th>Días Postnatales:</th>
                                <td>{{ $periodo->dias_postnatales }} días</td>
                            </tr>
                            <tr>
                                <th>Días Lactancia:</th>
                                <td>{{ $periodo->dias_lactancia }} días</td>
                            </tr>
                            <tr>
                                <th>Días Paternidad:</th>
                                <td>{{ $periodo->dias_paternidad }} días</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h6>Consumo de Días:</h6>
                            <p>
                                <strong>Días Usados:</strong> {{ $periodo->dias_usados }}<br>
                                <strong>Días Restantes:</strong> {{ $periodo->dias_restantes }}<br>
                                <strong>Total Permitido:</strong> {{ $periodo->getTotalDiasPermitidos() }}
                            </p>
                        </div>
                        @if($periodo->certificado_embarazo_path)
                        <div class="mb-2">
                            <i class="fas fa-file-medical"></i>
                            <a href="{{ Storage::url($periodo->certificado_embarazo_path) }}" target="_blank">
                                Ver Certificado de Embarazo
                            </a>
                        </div>
                        @endif
                        @if($periodo->certificado_nacimiento_path)
                        <div class="mb-2">
                            <i class="fas fa-file-alt"></i>
                            <a href="{{ Storage::url($periodo->certificado_nacimiento_path) }}" target="_blank">
                                Ver Certificado de Nacimiento
                            </a>
                        </div>
                        @endif
                        @if($periodo->certificado_lactancia_path)
                        <div class="mb-2">
                            <i class="fas fa-file-medical"></i>
                            <a href="{{ Storage::url($periodo->certificado_lactancia_path) }}" target="_blank">
                                Ver Certificado de Lactancia
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
