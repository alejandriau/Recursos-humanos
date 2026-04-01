<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Información del Dependiente Discapacitado</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                             <tr>
                                <th width="40%">Nombre Completo:</th>
                                <td>{{ $dependiente->nombre_dependiente }}</td>
                            </tr>
                            <tr>
                                <th>Parentesco:</th>
                                <td>{{ $dependiente->parentesco }}</td>
                            </tr>
                            @if($dependiente->fecha_nacimiento)
                            <tr>
                                <th>Fecha Nacimiento:</th>
                                <td>{{ \Carbon\Carbon::parse($dependiente->fecha_nacimiento)->format('d/m/Y') }} ({{ $dependiente->edad }} años)</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Tipo Discapacidad:</th>
                                <td>{{ $dependiente->tipo_discapacidad }}</td>
                            </tr>
                            <tr>
                                <th>Porcentaje Discapacidad:</th>
                                <td>{{ $dependiente->porcentaje_discapacidad }}%</td>
                            </tr>
                            <tr>
                                <th>Grado Dependencia:</th>
                                <td>{{ $dependiente->grado_dependencia_label }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <i class="fas fa-file-medical"></i>
                            <a href="{{ Storage::url($dependiente->certificado_discapacidad_path) }}" target="_blank">
                                Ver Certificado de Discapacidad
                            </a>
                        </div>
                        @if($dependiente->partida_nacimiento_path)
                        <div class="mb-2">
                            <i class="fas fa-file-alt"></i>
                            <a href="{{ Storage::url($dependiente->partida_nacimiento_path) }}" target="_blank">
                                Ver Partida de Nacimiento
                            </a>
                        </div>
                        @endif
                        @if($dependiente->necesidades_especiales)
                        <div class="mt-3">
                            <strong>Necesidades Especiales:</strong>
                            <p class="text-muted">{{ $dependiente->necesidades_especiales }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
