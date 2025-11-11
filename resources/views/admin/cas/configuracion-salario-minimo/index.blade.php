{{-- resources/views/configuracion-salario-minimo/index.blade.php --}}
@extends('dashboard')

@section('title', 'Configuración de Salario Mínimo')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
             <!-- MOSTRAR MENSAJES DE ERROR/SUCCESS -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error:</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i>
                    <strong>Éxito:</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <!-- MOSTRAR ERRORES DE VALIDACIÓN -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Errores encontrados:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-money-bill-wave"></i>
                        Configuración de Salario Mínimo Nacional
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalNuevoSalario">
                            <i class="fas fa-plus"></i> Nuevo Salario Mínimo
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Salario Vigente Actual -->
                    @php
                        $salarioVigente = \App\Models\ConfiguracionSalarioMinimo::where('vigente', true)->first();
                    @endphp

                    @if($salarioVigente)
                    <div class="alert alert-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="alert-heading">
                                    <i class="fas fa-check-circle"></i> Salario Mínimo Vigente
                                </h4>
                                <strong>Bs. {{ number_format($salarioVigente->monto_salario_minimo, 2) }}</strong>
                                - Gestión {{ $salarioVigente->gestion }}
                                <br>
                                <small class="text-muted">
                                    Vigente desde: {{ \Carbon\Carbon::parse($salarioVigente->fecha_vigencia)->format('d/m/Y') }}
                                </small>
                            </div>
                            <div class="text-right">
                                <span class="badge bg-success badge-lg">ACTUAL</span>
                                <br>
                                <small class="text-muted">
                                    {{ $salarioVigente->observaciones }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        No hay salario mínimo configurado como vigente.
                    </div>
                    @endif

                    <!-- Historial de Salarios Mínimos -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-history"></i> Historial de Salarios Mínimos
                            </h4>
                        </div>
                        <div class="card-body">
                            @php
                                $salarios = \App\Models\ConfiguracionSalarioMinimo::orderBy('gestion', 'desc')->get();
                            @endphp

                            @if($salarios->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Gestión</th>
                                                <th>Monto</th>
                                                <th>Fecha Vigencia</th>
                                                <th>Estado</th>
                                                <th>Observaciones</th>
                                                <th>Registro</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($salarios as $salario)
                                            <tr>
                                                <td>
                                                    <strong>{{ $salario->gestion }}</strong>
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        Bs. {{ number_format($salario->monto_salario_minimo, 2) }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($salario->fecha_vigencia)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    @if($salario->vigente)
                                                        <span class="badge bg-success">VIGENTE</span>
                                                    @else
                                                        <span class="badge bg-secondary">HISTÓRICO</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $salario->observaciones ? Str::limit($salario->observaciones, 50) : 'Sin observaciones' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($salario->fecha_registro)->format('d/m/Y') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if(!$salario->vigente)
                                                        <button class="btn btn-sm btn-outline-primary"
                                                                onclick="activarSalario({{ $salario->id }})"
                                                                title="Activar este salario">
                                                            <i class="fas fa-check"></i> Activar
                                                        </button>
                                                    @else
                                                        <span class="text-success">
                                                            <i class="fas fa-check-circle"></i> Activo
                                                        </span>
                                                    @endif

                                                    @if($salarios->count() > 1 && !$salario->vigente)
                                                        <button class="btn btn-sm btn-outline-danger ml-1"
                                                                onclick="confirmarEliminacion({{ $salario->id }})"
                                                                title="Eliminar registro">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                    <h5>No hay registros de salario mínimo</h5>
                                    <p class="text-muted">Configure el primer salario mínimo nacional.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Impacto en Bonos -->
                    @if($salarioVigente)
                    <div class="card mt-4">
                        <div class="card-header bg-warning">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-calculator"></i> Impacto en Bonos de Antigüedad
                            </h4>
                        </div>
                        <div class="card-body">
                            @php
                                $totalCas = \App\Models\Cas::where('estado_cas', 'vigente')->count();
                                $casConBono = \App\Models\Cas::where('estado_cas', 'vigente')
                                    ->where('aplica_bono_antiguedad', true)
                                    ->count();
                                $montoTotalBonos = \App\Models\Cas::where('estado_cas', 'vigente')
                                    ->where('aplica_bono_antiguedad', true)
                                    ->sum('monto_bono');
                            @endphp

                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h5>CAS Vigentes</h5>
                                        <h3 class="text-primary">{{ $totalCas }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h5>Con Bono Activo</h5>
                                        <h3 class="text-success">{{ $casConBono }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h5>Total Bonos Mensual</h5>
                                        <h3 class="text-info">Bs. {{ number_format($montoTotalBonos, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoSalario" tabindex="-1" role="dialog" aria-labelledby="modalNuevoSalarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalNuevoSalarioLabel">
                    <i class="fas fa-plus-circle"></i> Registrar Nuevo Salario Mínimo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNuevoSalario" action="{{ route('configuracion-salario-minimo.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Importante:</strong> Al registrar un nuevo salario mínimo, se actualizarán automáticamente
                        todos los bonos de antigüedad de los CAS vigentes.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gestion">Gestión *</label>
                                <input type="number"
                                       class="form-control"
                                       id="gestion"
                                       name="gestion"
                                       value="{{ date('Y') }}"
                                       min="2000"
                                       max="2050"
                                       required>
                                <small class="form-text text-muted">Año de vigencia del salario mínimo</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto_salario_minimo">Monto Salario Mínimo (Bs.) *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Bs.</span>
                                    </div>
                                    <input type="number"
                                           class="form-control"
                                           id="monto_salario_minimo"
                                           name="monto_salario_minimo"
                                           step="0.01"
                                           min="0"
                                           required
                                           placeholder="0.00">
                                </div>
                                <small class="form-text text-muted">Monto mensual del salario mínimo nacional</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_vigencia">Fecha de Vigencia *</label>
                                <input type="date"
                                       class="form-control"
                                       id="fecha_vigencia"
                                       name="fecha_vigencia"
                                       required>
                                <small class="form-text text-muted">Fecha desde cuando es efectivo el nuevo salario</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estado</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="vigente"
                                           name="vigente"
                                           value="1"
                                           checked>
                                    <label class="custom-control-label" for="vigente">
                                        Activar como salario vigente
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Si se activa, desactivará automáticamente el salario anterior
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control"
                                  id="observaciones"
                                  name="observaciones"
                                  rows="3"
                                  placeholder="Ej: Ajuste semestral, Decreto Supremo N° XXX, etc."></textarea>
                        <small class="form-text text-muted">Información adicional sobre el salario mínimo</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Registrar Salario Mínimo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Activación (NUEVO) -->
<div class="modal fade" id="modalConfirmacionActivacion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Activación
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de activar este salario mínimo?</p>
                <div class="alert alert-info">
                    <strong>Impacto:</strong> Se actualizarán todos los bonos de antigüedad de los CAS vigentes.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnConfirmarActivacion">
                    <i class="fas fa-check"></i> Sí, Activar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    console.log('Página cargada - Modal disponible');

    // Establecer fecha mínima como hoy
    const today = new Date().toISOString().split('T')[0];
    $('#fecha_vigencia').attr('min', today);

    // Validación de monto
    $('#monto_salario_minimo').on('change', function() {
        let monto = parseFloat($(this).val());
        if (monto > 0 && monto < 1000) {
            alert('⚠️ El monto parece muy bajo para un salario mínimo. ¿Está seguro?');
        }
    });
});

function activarSalario(salarioId) {
    // Mostrar modal de confirmación
    $('#modalConfirmacionActivacion').modal('show');

    document.getElementById('btnConfirmarActivacion').onclick = function() {
        fetch(`/configuracion-salario-minimo/${salarioId}/activar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al activar el salario');
        });
    };
}

function confirmarEliminacion(salarioId) {
    if (confirm('¿Está seguro de eliminar este registro de salario mínimo?\n\nEsta acción no se puede deshacer.')) {
        fetch(`/configuracion-salario-minimo/${salarioId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el salario');
        });
    }
}

// Función de prueba para verificar que el modal funciona
function probarModal() {
    $('#modalNuevoSalario').modal('show');
    console.log('Modal abierto manualmente');
}
</script>

@endsection


