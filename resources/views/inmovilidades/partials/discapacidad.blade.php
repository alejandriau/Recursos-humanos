<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Información de Discapacidad</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Tipo de Discapacidad:</th>
                                <td>{{ $discapacidad->tipo }}</td>
                            </tr>
                            <tr>
                                <th>Grado:</th>
                                <td>{{ $discapacidad->grado_label }}</span> ({{ $discapacidad->porcentaje }}%)</span>
                            </td>
                            </tr>
                            <tr>
                                <th>Entidad Certificadora:</th>
                                <td>{{ $discapacidad->entidad_certificadora }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Certificación:</th>
                                <td>{{ \Carbon\Carbon::parse($discapacidad->fecha_certificacion)->format('d/m/Y') }}</td>
                            </tr>
                            @if($discapacidad->fecha_vencimiento)
                            <tr>
                                <th>Fecha Vencimiento:</th>
                                <td>{{ \Carbon\Carbon::parse($discapacidad->fecha_vencimiento)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($discapacidad->certificado_medico_path)
                            <div class="mb-2">
                                <i class="fas fa-file-medical"></i>
                                <a href="{{ Storage::url($discapacidad->certificado_medico_path) }}" target="_blank">
                                    Ver Certificado Médico
                                </a>
                            </div>
                        @endif
                        @if($discapacidad->resolucion_conadis_path)
                            <div class="mb-2">
                                <i class="fas fa-file-pdf"></i>
                                <a href="{{ Storage::url($discapacidad->resolucion_conadis_path) }}" target="_blank">
                                    Ver Resolución CONADIS
                                </a>
                            </div>
                        @endif
                        @if($discapacidad->tutor_id)
                            <hr>
                            <h6>Información del Tutor:</h6>
                            <p>
                                <strong>Nombre:</strong> {{ $discapacidad->tutor->nombres ?? '' }} {{ $discapacidad->tutor->apellidos ?? '' }}<br>
                                <strong>Parentesco:</strong> {{ $discapacidad->parentesco_tutor }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
