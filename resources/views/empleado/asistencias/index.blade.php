{{-- resources/views/asistencia/mi-asistencia.blade.php --}}
@extends('layouts.baseusr')

@section('cuerpo')
<div class="container-fluid py-3">
    <h4 class="mb-3"><i class="bi bi-person-check"></i> Mi Asistencia</h4>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label mb-0">Desde</label>
                    <input type="date" class="form-control" id="fFechaInicio">
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0">Hasta</label>
                    <input type="date" class="form-control" id="fFechaFin">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" onclick="buscarReporte()">
                        <i class="bi bi-search"></i> Consultar
                    </button>
                </div>
            </div>
            <small class="text-muted">Por defecto se muestra desde el día 21 del corte vigente hasta hoy.</small>
        </div>
    </div>

    <div id="contenidoReporte"></div>
</div>
@endsection

@push('scripts')
<script>
async function buscarReporte() {
    const params = new URLSearchParams();
    if (document.getElementById('fFechaInicio').value) params.set('fecha_inicio', document.getElementById('fFechaInicio').value);
    if (document.getElementById('fFechaFin').value) params.set('fecha_fin', document.getElementById('fFechaFin').value);

    document.getElementById('contenidoReporte').innerHTML = '<div class="text-center py-4"><span class="spinner-border"></span></div>';

    const resp = await fetch(`/mi-asistencia/datos?${params}`);
    const data = await resp.json();

    if (!data.success) {
        document.getElementById('contenidoReporte').innerHTML = `<div class="alert alert-warning">${data.mensaje}</div>`;
        return;
    }

    renderReporte(data.data);
}

function renderReporte(r) {
    const p = r.persona;
    const t = r.totales;
    
    document.getElementById('fFechaInicio').value = r.fecha_inicio;
    document.getElementById('fFechaFin').value = r.fecha_fin;

    const formatoTiempo = (minutos) => {
        if (minutos === null || minutos === undefined || minutos === 0) return '00:00';
        const absMin = Math.round(Math.abs(minutos));
        const hh = Math.floor(absMin / 60);
        const mm = absMin % 60;
        return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
    };

    // Badge de estado del día
    const badgeEstado = (estado, esHoy) => {
        if (estado === 'pendiente') {
            return `<span class="badge bg-warning text-dark" title="Aún no cierra el día">⏳ Pendiente</span>`;
        }
        if (estado === 'completo') return `<span class="badge bg-success">✓ Completo</span>`;
        if (estado === 'tardanza') return `<span class="badge bg-info text-dark">⚠ Tardanza</span>`;
        if (estado === 'falta_injustificada') return `<span class="badge bg-danger">✗ Falta</span>`;
        if (estado === 'falta_justificada') return `<span class="badge bg-primary">✓ Justificado</span>`;
        if (estado === 'no_laborable') return `<span class="badge bg-secondary">Sin laborar</span>`;
        return `<span class="badge bg-light text-dark">${estado}</span>`;
    };

    const filasDias = r.dias.map(d => {
        // Si es hoy y la salida está pendiente, mostrar "Pendiente" en vez de "No marcó"
        const entradaHtml = d.entrada 
            ? d.entrada 
            : (d.es_hoy && d.estado === 'pendiente' 
                ? '<span class="text-muted fst-italic">Pendiente</span>' 
                : '<span class="text-danger fw-bold">No marcó</span>');

        const salidaHtml = d.salida 
            ? d.salida 
            : (d.es_hoy && d.estado === 'pendiente' 
                ? '<span class="text-muted fst-italic">Pendiente</span>' 
                : '<span class="text-danger fw-bold">No marcó</span>');

        // Resaltar fila del día en curso
        const filaClase = d.es_hoy ? 'table-warning' : '';

        return `
            <tr class="${filaClase}">
                <td class="fw-bold">
                    ${d.fecha}
                    <div class="mt-1">${badgeEstado(d.estado, d.es_hoy)}</div>
                </td>
                <td class="small">${d.turno}</td>
                <td class="text-center">${entradaHtml}</td>
                <td class="text-center">${salidaHtml}</td>
                <td class="text-center">${formatoTiempo(d.atraso_min)}</td>
                <td class="text-center">${formatoTiempo(d.sal_ant_min)}</td>
                <td class="text-center">${d.ausen > 0 ? d.ausen : '00:00'}</td>
                <td class="small text-muted">${d.justificacion}</td>
                <td class="text-center">${formatoTiempo(d.ext_min)}</td>
                <td class="text-center fw-bold">${formatoTiempo(d.jor_min)}</td>
            </tr>
        `;
    }).join('');

    document.getElementById('contenidoReporte').innerHTML = `
        <div class="card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h4 class="mb-1">
                    <i class="bi bi-person-vcard-fill"></i> 
                    ${p.nombre} ${p.apellidoPat ?? ''} ${p.apellidoMat ?? ''}
                </h4>
                <small class="text-muted">ITEM: ${p.ci}</small>
                <div class="float-end small">
                    Desde el ${r.fecha_inicio} <br> 
                    Hasta el ${r.fecha_fin}
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm mb-0" style="font-size: 0.9rem;">
                        <thead class="table-success text-center align-middle">
                            <tr>
                                <th style="min-width: 160px;">Fecha / Estado</th>
                                <th>Turnos</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Atraso</th>
                                <th>Sal Ant</th>
                                <th>Ausen</th>
                                <th>Justificaciones</th>
                                <th>Hrs. Ext</th>
                                <th>Jor</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filasDias || '<tr><td colspan="10" class="text-center text-muted py-3">No hay días evaluados en este rango</td></tr>'}
                        </tbody>
                            <tfoot class="table-secondary fw-bold text-center align-middle">
                                <tr>
                                    <td colspan="4" class="text-end">TOTALES</td>
                                    <td>${formatoTiempo(t.atraso)}</td>
                                    <td>${formatoTiempo(t.sal_ant)}</td>
                                    <td>${t.ausen}</td>
                                    <td></td>
                                    <td>${formatoTiempo(t.ext)}</td>
                                    <td>${formatoTiempo(t.jor)}</td>
                                </tr>
                            </tfoot>
                    </table>
                </div>
            </div>
        </div>
    `;
}
document.addEventListener('DOMContentLoaded', buscarReporte);
</script>
@endpush