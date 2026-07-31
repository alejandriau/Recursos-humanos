{{-- resources/views/dispositivos/index.blade.php --}}
@extends('layouts.baseadm')

@section('content')
<div class="container-fluid py-3">
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="mb-3"><i class="bi bi-download"></i> Importar marcaciones manualmente</h6>
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label mb-0">Desde</label>
                    <input type="date" class="form-control" id="importarFechaInicio">
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0">Hasta</label>
                    <input type="date" class="form-control" id="importarFechaFin">
                </div>
                <div class="col-auto">
                    <button class="btn btn-success" id="btnImportarAhora" onclick="importarAhora()">
                        <i class="bi bi-cloud-download"></i> Importar de todos los dispositivos
                    </button>
                </div>
            </div>
            <div id="resultadoImportacion" class="mt-3"></div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-fingerprint"></i> Dispositivos Biométricos</h4>
        <button class="btn btn-primary" onclick="abrirModalNuevo()">
            <i class="bi bi-plus-lg"></i> Nuevo dispositivo
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover align-middle" id="tablaDispositivos">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>IP</th>
                        <th>Puerto</th>
                        <th>Ubicación</th>
                        <th>Última sincronización</th>
                        <th>Estado</th>
                        <th>Activo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cuerpoTablaDispositivos">
                    @foreach($dispositivos as $d)
                    <tr id="fila-dispositivo-{{ $d->id }}">
                        <td>{{ $d->nombre }}</td>
                        <td>{{ $d->ip }}</td>
                        <td>{{ $d->puerto }}</td>
                        <td>{{ $d->ubicacion ?? '-' }}</td>
                        <td>{{ $d->ultima_sincronizacion ? $d->ultima_sincronizacion->format('d/m/Y H:i') : 'Nunca' }}</td>
                        <td>
                            @if($d->ultimo_estado === 'exito')
                                <span class="badge bg-success">Exitoso</span>
                            @elseif($d->ultimo_estado === 'error')
                                <span class="badge bg-danger">Error</span>
                            @else
                                <span class="badge bg-secondary">Sin datos</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       {{ $d->activo ? 'checked' : '' }}
                                       onchange="toggleActivo({{ $d->id }})">
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary" onclick="probarConexion({{ $d->id }})" title="Probar conexión">
                                <i class="fas fa-wifi"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick='abrirModalEditar(@json($d))' title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="eliminarDispositivo({{ $d->id }})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalDispositivo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDispositivoTitulo">Nuevo dispositivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formDispositivo">
                    <input type="hidden" id="dispositivo_id">

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" required
                               placeholder="Ej. Entrada Principal, Planta Baja RRHH">
                    </div>

                    <div class="row">
                        <div class="col-8 mb-3">
                            <label class="form-label">IP</label>
                            <input type="text" class="form-control" id="ip" required
                                   placeholder="192.168.1.201">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">Puerto</label>
                            <input type="number" class="form-control" id="puerto" value="4370">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ubicación (opcional)</label>
                        <input type="text" class="form-control" id="ubicacion"
                               placeholder="Ej. Edificio A, 2do piso">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Timeout de conexión (segundos)</label>
                        <input type="number" class="form-control" id="timeout" value="60" min="5" max="300">
                        <small class="text-muted">Sube este valor si el dispositivo tiene muchas marcaciones guardadas.</small>
                    </div>

                    <div id="alertaFormDispositivo" class="alert alert-danger d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarDispositivo()">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')


<script>
const modalDispositivo = new bootstrap.Modal(document.getElementById('modalDispositivo'));

function abrirModalNuevo() {
    document.getElementById('formDispositivo').reset();
    document.getElementById('dispositivo_id').value = '';
    document.getElementById('puerto').value = 4370;
    document.getElementById('timeout').value = 60;
    document.getElementById('modalDispositivoTitulo').innerText = 'Nuevo dispositivo';
    ocultarAlertaForm();
    modalDispositivo.show();
}

function abrirModalEditar(d) {
    document.getElementById('dispositivo_id').value = d.id;
    document.getElementById('nombre').value = d.nombre;
    document.getElementById('ip').value = d.ip;
    document.getElementById('puerto').value = d.puerto;
    document.getElementById('ubicacion').value = d.ubicacion ?? '';
    document.getElementById('timeout').value = d.timeout;
    document.getElementById('modalDispositivoTitulo').innerText = 'Editar dispositivo';
    ocultarAlertaForm();
    modalDispositivo.show();
}

function ocultarAlertaForm() {
    const alerta = document.getElementById('alertaFormDispositivo');
    alerta.classList.add('d-none');
    alerta.innerText = '';
}

function mostrarAlertaForm(mensaje) {
    const alerta = document.getElementById('alertaFormDispositivo');
    alerta.innerText = mensaje;
    alerta.classList.remove('d-none');
}

async function guardarDispositivo() {
    const id = document.getElementById('dispositivo_id').value;
    const payload = {
        nombre: document.getElementById('nombre').value,
        ip: document.getElementById('ip').value,
        puerto: document.getElementById('puerto').value,
        ubicacion: document.getElementById('ubicacion').value,
        timeout: document.getElementById('timeout').value,
    };

    const url = id ? `/dispositivos/${id}` : '/dispositivos';
    const method = id ? 'PUT' : 'POST';

    try {
        const resp = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });

        const data = await resp.json();

        if (!data.success) {
            const msg = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : (data.mensaje ?? 'Error al guardar');
            mostrarAlertaForm(msg);
            return;
        }

        modalDispositivo.hide();
        location.reload();

    } catch (e) {
        mostrarAlertaForm('Error de red al guardar el dispositivo.');
    }
}

async function eliminarDispositivo(id) {
    if (!confirm('¿Eliminar este dispositivo? Las marcaciones ya importadas NO se borran.')) return;

    const resp = await fetch(`/dispositivos/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });
    const data = await resp.json();

    if (data.success) {
        document.getElementById(`fila-dispositivo-${id}`).remove();
    } else {
        alert(data.mensaje ?? 'No se pudo eliminar');
    }
}

async function toggleActivo(id) {
    await fetch(`/dispositivos/${id}/toggle-activo`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });
}

async function importarAhora() {
    const fechaInicio = document.getElementById('importarFechaInicio').value;
    const fechaFin = document.getElementById('importarFechaFin').value;
    const boton = document.getElementById('btnImportarAhora');
    const resultadoDiv = document.getElementById('resultadoImportacion');

    boton.disabled = true;
    boton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Importando... puede tardar varios minutos';
    resultadoDiv.innerHTML = '';

    try {
        const resp = await fetch('/zkteco/importar-todos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ fecha_inicio: fechaInicio, fecha_fin: fechaFin }),
        });
        const data = await resp.json();

        if (!data.success) {
            resultadoDiv.innerHTML = `<div class="alert alert-danger">${data.error ?? 'Error al importar'}</div>`;
            return;
        }

        let html = '<table class="table table-sm"><thead><tr><th>Dispositivo</th><th>Resultado</th></tr></thead><tbody>';
        for (const id in data.data) {
            const r = data.data[id];
            if (r.error) {
                html += `<tr><td>${r.dispositivo}</td><td class="text-danger">❌ ${r.error}</td></tr>`;
            } else {
                html += `<tr><td>${r.dispositivo}</td><td class="text-success">✅ ${r.nuevas_importadas} nuevas, ${r.duplicadas} duplicadas, ${r.con_error} con error</td></tr>`;
            }
        }
        html += '</tbody></table>';
        resultadoDiv.innerHTML = html;

    } catch (e) {
        resultadoDiv.innerHTML = '<div class="alert alert-danger">Error de red al importar.</div>';
    } finally {
        boton.disabled = false;
        boton.innerHTML = '<i class="bi bi-cloud-download"></i> Importar de todos los dispositivos';
    }
}

async function probarConexion(id) {
    const boton = event.currentTarget;
    const original = boton.innerHTML;
    boton.disabled = true;
    boton.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    try {
        const resp = await fetch(`/dispositivos/${id}/probar-conexion`, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        const data = await resp.json();
        alert(data.mensaje + (data.informacion ? '\n' + JSON.stringify(data.informacion, null, 2) : ''));
    } catch (e) {
        alert('Error de red al probar la conexión.');
    } finally {
        boton.disabled = false;
        boton.innerHTML = original;
    }
}
</script>
@endpush