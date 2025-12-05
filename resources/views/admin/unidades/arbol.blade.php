@extends('dashboard')

@section('title', 'Organigrama')
@section('header-title', 'Organigrama de la Empresa')

@section('contenido')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Estructura Organizacional
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Vista interactiva del organigrama con D3.js
                </p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportarOrganigrama()"
                        class="no-print inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-download mr-2"></i>Exportar PNG
                </button>
                <button onclick="resetZoom()"
                        class="no-print inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search-arrows mr-2"></i>Reset Zoom
                </button>
                <button onclick="toggleFullscreen()"
                        class="no-print inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-expand mr-2"></i>Pantalla Completa
                </button>
                <a href="{{ route('unidades.index') }}"
                   class="no-print inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-list mr-2"></i>Ver Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="no-print px-4 py-4 bg-gray-50 border-b border-gray-200">
        <form method="GET" action="{{ route('unidades.arbol') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label for="unidad_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Filtrar por Unidad:
                </label>
                <div class="flex gap-2">
                    <select name="unidad_id" id="unidad_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Todas las unidades (vista completa)</option>
                        @foreach($todasUnidades as $unidad)
                            <option value="{{ $unidad->id }}"
                                    {{ $filtroUnidad == $unidad->id ? 'selected' : '' }}>
                                {{ $unidad->denominacion }}
                                @if($unidad->codigo)
                                    ({{ $unidad->codigo }})
                                @endif
                                - {{ $unidad->tipo }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>
                </div>
            </div>

            @if($filtroUnidad && $unidadSeleccionada)
            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-filter mr-1"></i>
                    Vista de: {{ $unidadSeleccionada->denominacion }}
                    <a href="{{ route('unidades.arbol') }}" class="ml-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            </div>
            @endif
        </form>
    </div>

    <!-- Controles del organigrama -->
    <div class="no-print px-4 py-3 bg-white border-b border-gray-200">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                <span class="text-sm font-medium text-gray-700">Leyenda:</span>
                @foreach([
                    'DIRECCION' => 'bg-blue-600',
                    'SECRETARIA' => 'bg-purple-600',
                    'SERVICIO' => 'bg-indigo-600',
                    'GERENCIA' => 'bg-green-600',
                    'UNIDAD' => 'bg-emerald-600',
                    'AREA' => 'bg-yellow-600',
                    'DEPARTAMENTO' => 'bg-red-600',
                    'COORDINACION' => 'bg-pink-600'
                ] as $tipo => $color)
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 {{ $color }} rounded-sm"></div>
                        <span class="text-xs text-gray-600">{{ $tipo }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex gap-2">
                <button onclick="expandirTodo()"
                        class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-expand-alt mr-1"></i>Expandir Todo
                </button>
                <button onclick="colapsarTodo()"
                        class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-compress-alt mr-1"></i>Colapsar Todo
                </button>
            </div>
        </div>
    </div>

    <!-- Contenedor del Organigrama -->
    <div class="p-4">
        <div id="organigrama-container" style="width: 100%; height: 700px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; position: relative;">
            <svg id="organigrama-svg" style="width: 100%; height: 100%;"></svg>
            <div id="tooltip" class="absolute hidden bg-white border border-gray-200 shadow-2xl rounded-lg p-3 max-w-xs z-50 pointer-events-none transition-opacity duration-200"></div>
        </div>
    </div>
</div>
<script src="https://d3js.org/d3.v7.min.js"></script>

<script>
// Variables globales
let treeData = @json($treeData);
let currentRoot = null;
let treeLayout = null;
let svg = null;
let zoom = null;
let tooltipTimeout = null;
let currentTooltipNode = null;

// Inicializar el organigrama cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initOrganigrama();
    setupSearch();
});

function setupSearch() {
    const selectUnidad = document.getElementById('unidad_id');
    if (selectUnidad) {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Buscar unidad...';
        searchInput.className = 'w-full px-3 py-2 border border-gray-300 rounded-md mb-2';
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const options = selectUnidad.options;

            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const text = option.text.toLowerCase();
                option.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });

        selectUnidad.parentNode.insertBefore(searchInput, selectUnidad);
    }
}

function initOrganigrama() {
    // Limpiar SVG existente
    d3.select('#organigrama-svg').selectAll('*').remove();

    // Configuración
    const margin = { top: 50, right: 120, bottom: 50, left: 120 };
    const container = document.getElementById('organigrama-container');
    const width = container.offsetWidth - margin.left - margin.right;
    const height = container.offsetHeight - margin.top - margin.bottom;

    // Crear SVG principal
    svg = d3.select('#organigrama-svg')
        .attr('width', width + margin.left + margin.right)
        .attr('height', height + margin.top + margin.bottom)
        .append('g')
        .attr('transform', `translate(${margin.left},${margin.top})`);

    // Crear layout de árbol
    treeLayout = d3.tree().size([height, width]);

    // Jerarquizar los datos
    currentRoot = d3.hierarchy(treeData);
    const treeDataHierarchy = treeLayout(currentRoot);

    // Configurar zoom
    zoom = d3.zoom()
        .scaleExtent([0.1, 3])
        .on('zoom', (event) => {
            svg.attr('transform', event.transform);
        });

    d3.select('#organigrama-svg').call(zoom);

    // Dibujar el organigrama
    drawOrganigrama(treeDataHierarchy);

    // Ajustar vista inicial
    centerView();
}

function drawOrganigrama(treeDataHierarchy) {
    // Dibujar conexiones
    const links = svg.selectAll('.link')
        .data(treeDataHierarchy.links())
        .join(
            enter => enter.append('path')
                .attr('class', 'link')
                .attr('d', d3.linkHorizontal()
                    .x(d => d.y)
                    .y(d => d.x)
                ),
            update => update.transition().duration(300)
                .attr('d', d3.linkHorizontal()
                    .x(d => d.y)
                    .y(d => d.x)
                ),
            exit => exit.remove()
        )
        .style('fill', 'none')
        .style('stroke', '#94a3b8')
        .style('stroke-width', 2)
        .style('opacity', 0.7);

    // Crear grupos para los nodos
    const nodeGroups = svg.selectAll('.node-group')
        .data(treeDataHierarchy.descendants())
        .join(
            enter => enter.append('g')
                .attr('class', 'node-group')
                .attr('transform', d => `translate(${d.y},${d.x})`),
            update => update.transition().duration(300)
                .attr('transform', d => `translate(${d.y},${d.x})`),
            exit => exit.remove()
        );

    // Dibujar círculos para los nodos con eventos mejorados
    nodeGroups.selectAll('circle')
        .data(d => [d])
        .join(
            enter => enter.append('circle')
                .attr('r', 10)
                .style('fill', d => d.data.color || '#6b7280')
                .style('stroke', '#fff')
                .style('stroke-width', 3)
                .style('cursor', 'pointer')
                .style('transition', 'all 0.2s ease')
                .on('mouseenter', function(event, d) {
                    // Clear any pending hide timeout
                    if (tooltipTimeout) {
                        clearTimeout(tooltipTimeout);
                        tooltipTimeout = null;
                    }

                    currentTooltipNode = d;
                    showTooltip(event, d);

                    // Efecto visual en el nodo
                    d3.select(this)
                        .transition()
                        .duration(150)
                        .attr('r', 14)
                        .style('filter', 'brightness(1.2)');
                })
                .on('mousemove', function(event, d) {
                    if (currentTooltipNode === d) {
                        updateTooltipPosition(event);
                    }
                })
                .on('mouseleave', function(event, d) {
                    // Efecto visual en el nodo
                    d3.select(this)
                        .transition()
                        .duration(150)
                        .attr('r', 10)
                        .style('filter', 'brightness(1)');

                    // Delay para ocultar tooltip (evita parpadeo al pasar entre nodos)
                    tooltipTimeout = setTimeout(() => {
                        if (currentTooltipNode === d) {
                            hideTooltip();
                            currentTooltipNode = null;
                        }
                    }, 100);
                })
                .on('click', function(event, d) {
                    event.stopPropagation();
                    // Clear tooltip timeout on click
                    if (tooltipTimeout) {
                        clearTimeout(tooltipTimeout);
                        tooltipTimeout = null;
                    }
                    hideTooltip();
                    currentTooltipNode = null;
                    toggleNode(d);
                }),
            update => update,
            exit => exit.remove()
        );

    // Añadir etiquetas de texto
    nodeGroups.selectAll('.node-text')
        .data(d => [d])
        .join(
            enter => enter.append('text')
                .attr('class', 'node-text')
                .attr('dy', 4)
                .attr('x', d => d.children ? -15 : 15)
                .style('text-anchor', d => d.children ? 'end' : 'start')
                .style('font-size', '12px')
                .style('font-weight', '600')
                .style('fill', '#1f2937')
                .style('pointer-events', 'none')
                .text(d => truncateText(d.data.name, 18)),
            update => update
                .attr('x', d => d.children ? -15 : 15)
                .style('text-anchor', d => d.children ? 'end' : 'start')
                .text(d => truncateText(d.data.name, 18)),
            exit => exit.remove()
        );

    // Añadir tipo como subtítulo
    nodeGroups.selectAll('.node-subtitle')
        .data(d => [d])
        .join(
            enter => enter.append('text')
                .attr('class', 'node-subtitle')
                .attr('dy', 20)
                .attr('x', d => d.children ? -15 : 15)
                .style('text-anchor', d => d.children ? 'end' : 'start')
                .style('font-size', '10px')
                .style('fill', '#6b7280')
                .style('pointer-events', 'none')
                .text(d => d.data.title),
            update => update
                .attr('x', d => d.children ? -15 : 15)
                .style('text-anchor', d => d.children ? 'end' : 'start')
                .text(d => d.data.title),
            exit => exit.remove()
        );

    // Añadir indicador de expandible
    nodeGroups.selectAll('.node-expandable')
        .data(d => [d])
        .join(
            enter => enter.append('text')
                .attr('class', 'node-expandable')
                .attr('dy', -12)
                .attr('x', 0)
                .style('text-anchor', 'middle')
                .style('font-size', '10px')
                .style('fill', '#6b7280')
                .style('pointer-events', 'none')
                .text(d => {
                    if (d.children) return '−';
                    if (d._children) return '+';
                    return '';
                }),
            update => update
                .text(d => {
                    if (d.children) return '−';
                    if (d._children) return '+';
                    return '';
                }),
            exit => exit.remove()
        );
}

function showTooltip(event, d) {
    const tooltip = document.getElementById('tooltip');

    const tooltipContent = `
        <div class="space-y-2 min-w-48">
            <div class="font-bold text-gray-900 text-sm leading-tight">${d.data.name}</div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-sm flex-shrink-0" style="background: ${d.data.color}"></div>
                <span class="text-xs text-gray-600 font-medium">${d.data.title}</span>
            </div>
            ${d.data.codigo ? `<div class="text-xs text-gray-600"><strong>Código:</strong> ${d.data.codigo}</div>` : ''}
            <div class="text-xs text-gray-600"><strong>Jefe:</strong> ${d.data.jefe || 'No asignado'}</div>
            <div class="flex gap-4 text-xs text-gray-500 pt-2 border-t border-gray-100">
                <span class="flex items-center gap-1">
                    <i class="fas fa-sitemap text-xs"></i>
                    ${d.data.subunidades} sub.
                </span>
                <span class="flex items-center gap-1">
                    <i class="fas fa-users text-xs"></i>
                    ${d.data.totalPuestos} puestos
                </span>
            </div>
            <div class="text-xs text-blue-500 font-medium italic mt-1">
                Click para ${d.children ? 'colapsar' : 'expandir'}
            </div>
        </div>
    `;

    tooltip.innerHTML = tooltipContent;
    updateTooltipPosition(event);
    tooltip.classList.remove('hidden');
    tooltip.style.opacity = '0';

    // Pequeña animación de entrada
    setTimeout(() => {
        tooltip.style.opacity = '1';
    }, 10);
}

function updateTooltipPosition(event) {
    const tooltip = document.getElementById('tooltip');
    const container = document.getElementById('organigrama-container');
    const containerRect = container.getBoundingClientRect();

    // Posición más cercana al cursor (5px de distancia)
    let x = event.pageX + 5;
    let y = event.pageY + 5;

    // Dimensiones del tooltip
    const tooltipWidth = tooltip.offsetWidth;
    const tooltipHeight = tooltip.offsetHeight;

    // Ajustar si se sale por la derecha
    if (x + tooltipWidth > containerRect.right - 10) {
        x = event.pageX - tooltipWidth - 10;
    }

    // Ajustar si se sale por abajo
    if (y + tooltipHeight > containerRect.bottom - 10) {
        y = event.pageY - tooltipHeight - 10;
    }

    // Asegurar que no se salga por arriba o izquierda
    x = Math.max(containerRect.left + 10, x);
    y = Math.max(containerRect.top + 10, y);

    tooltip.style.left = (x - containerRect.left) + 'px';
    tooltip.style.top = (y - containerRect.top) + 'px';
}

function hideTooltip() {
    const tooltip = document.getElementById('tooltip');
    tooltip.style.opacity = '0';

    setTimeout(() => {
        if (parseFloat(tooltip.style.opacity) === 0) {
            tooltip.classList.add('hidden');
        }
    }, 200);
}

function toggleNode(d) {
    if (d.children) {
        d._children = d.children;
        d.children = null;
    } else {
        d.children = d._children;
        d._children = null;
    }

    const updatedTree = treeLayout(currentRoot);
    drawOrganigrama(updatedTree);
}

function expandirTodo() {
    currentRoot.each(d => {
        if (d._children) {
            d.children = d._children;
            d._children = null;
        }
    });
    const updatedTree = treeLayout(currentRoot);
    drawOrganigrama(updatedTree);
}

function colapsarTodo() {
    currentRoot.each(d => {
        if (d.children && d.depth > 0) {
            d._children = d.children;
            d.children = null;
        }
    });
    const updatedTree = treeLayout(currentRoot);
    drawOrganigrama(updatedTree);
}

function centerView() {
    const bounds = svg.node().getBBox();
    const container = document.getElementById('organigrama-container');
    const fullWidth = container.offsetWidth;
    const fullHeight = container.offsetHeight;

    const scale = Math.min(0.8, 0.8 / Math.max(bounds.width / fullWidth, bounds.height / fullHeight));
    const transform = d3.zoomIdentity
        .translate(fullWidth / 2 - (bounds.x + bounds.width / 2) * scale,
                  fullHeight / 2 - (bounds.y + bounds.height / 2) * scale)
        .scale(scale);

    d3.select('#organigrama-svg').transition().duration(500).call(zoom.transform, transform);
}

function resetZoom() {
    centerView();
}

function toggleFullscreen() {
    const container = document.getElementById('organigrama-container');
    if (!document.fullscreenElement) {
        container.requestFullscreen().then(() => {
            setTimeout(initOrganigrama, 300);
        });
    } else {
        document.exitFullscreen().then(() => {
            setTimeout(initOrganigrama, 300);
        });
    }
}

function exportarOrganigrama() {
    const svgElement = document.getElementById('organigrama-svg');
    const svgData = new XMLSerializer().serializeToString(svgElement);
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();

    const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(svgBlob);

    img.onload = function() {
        canvas.width = svgElement.clientWidth;
        canvas.height = svgElement.clientHeight;
        ctx.drawImage(img, 0, 0);

        const pngUrl = canvas.toDataURL('image/png');
        const downloadLink = document.createElement('a');
        downloadLink.download = 'organigrama.png';
        downloadLink.href = pngUrl;
        downloadLink.click();
        URL.revokeObjectURL(url);
    };

    img.src = url;
}

function truncateText(text, maxLength) {
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

// Event listener para redimensionamiento
window.addEventListener('resize', function() {
    if (!document.fullscreenElement) {
        setTimeout(initOrganigrama, 300);
    }
});

// Limpiar timeouts cuando se sale de la página
window.addEventListener('beforeunload', function() {
    if (tooltipTimeout) {
        clearTimeout(tooltipTimeout);
    }
});
</script>

<style>
.link {
    transition: d 0.3s ease;
}

.node-group {
    cursor: pointer;
}

.node-group circle {
    transition: all 0.15s ease;
}

#tooltip {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(229, 231, 235, 0.8);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transition: opacity 0.2s ease;
}

/* Eliminar cualquier transición que cause parpadeo */
.node-group text {
    transition: none !important;
}

/* Scrollbar personalizado */
#organigrama-container::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

#organigrama-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

#organigrama-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

#organigrama-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Mejoras para pantalla completa */
#organigrama-container:fullscreen {
    padding: 20px;
    background: white;
}

#organigrama-container:fullscreen #organigrama-svg {
    background: #f8fafc;
    border-radius: 8px;
}
</style>
@endsection
