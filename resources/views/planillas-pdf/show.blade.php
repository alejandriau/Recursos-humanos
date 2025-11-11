@extends('dashboard')

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Visualizador de Planilla</h1>
                <p class="text-muted mb-0">{{ $planilla->nombre_original }}</p>
                <small class="text-info">Período: {{ $planilla->periodo_pago }}</small>
            </div>
            <div>
                <a href="{{ route('planillas-pdf.download', $planilla->id) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Descargar
                </a>
                <a href="{{ route('planillas-pdf.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Buscar por nombre, CI, puesto..."
                                aria-label="Buscar en el PDF">
                            <button class="btn btn-primary" type="button" id="searchButton">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary" id="zoomOut" title="Zoom Out">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <button class="btn btn-outline-dark disabled" id="zoomLevel">
                                100%
                            </button>
                            <button class="btn btn-outline-secondary" id="zoomIn" title="Zoom In">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <div id="searchResults" class="d-flex align-items-center gap-2 justify-content-end">
                                <span class="text-muted" id="matchInfo">
                                    <span id="matchCounter">0</span> coincidencias
                                </span>
                                <button class="btn btn-sm btn-outline-primary" id="prevMatch" disabled>
                                    <i class="fas fa-chevron-up"></i> Anterior
                                </button>
                                <button class="btn btn-sm btn-outline-primary" id="nextMatch" disabled>
                                    <i class="fas fa-chevron-down"></i> Siguiente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- Visor de PDF optimizado -->
                <div id="pdfViewer" style="height: 800px; overflow: auto; background: #525659; position: relative;">
                    <div id="loadingMessage" class="text-center text-white p-5">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Cargando PDF...</span>
                        </div>
                        <p class="mt-3 h5">Cargando PDF, por favor espere...</p>
                    </div>

                    <div id="errorMessage" class="alert alert-danger m-4 d-none">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error</h5>
                        <p id="errorText"></p>
                        <button class="btn btn-sm btn-outline-danger" id="retryButton">Reintentar</button>
                    </div>

                    <div id="pdfContainer" class="d-none">
                        <!-- Contenedor para páginas renderizadas -->
                        <div id="pagesContainer" style="position: relative;"></div>

                        <!-- Marcador de altura para virtualización -->
                        <div id="pagePlaceholders"></div>
                    </div>

                    <!-- Indicador de búsqueda -->
                    <div id="searchLoading" class="d-none" style="position: absolute; top: 10px; right: 10px; z-index: 1000;">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Buscando en el documento...
                            <button type="button" class="btn-close" id="cancelSearch"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.search-word-highlight {
    transition: background-color 0.3s ease, transform 0.2s ease;
}
.search-word-highlight.current-match {
    background-color: rgba(255, 87, 34, 0.8) !important;
    box-shadow: 0 0 0 2px rgba(255, 87, 34, 0.5);
    z-index: 5 !important;
    transform: scale(1.02);
}
.text-layer {
    font-family: inherit !important;
}
.hover-area:hover {
    background-color: rgba(255, 4, 12, 0.433) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar PDF.js dinámicamente
    if (typeof pdfjsLib === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js';
        script.onload = initializePDFViewer;
        document.head.appendChild(script);
    } else {
        initializePDFViewer();
    }
});

function initializePDFViewer() {
    // Configurar PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    // Variables globales optimizadas
    let pdfDoc = null;
    let currentScale = 1.0;
    let searchText = '';
    let allMatches = [];
    let currentMatchIndex = -1;
    let pageViewports = [];
    let renderedPages = new Map();
    let visiblePages = new Set();
    let pageHeights = [];
    let totalHeight = 0;
    let isSearching = false;
    let searchController = null;
    let currentHoveredLine = null;
    let textContentByPage = {};

    // Configuración de rendimiento
    const BUFFER_PAGES = 2;
    const RENDER_DEBOUNCE_MS = 100;

    // Elementos DOM
    const pagesContainer = document.getElementById('pagesContainer');
    const pagePlaceholders = document.getElementById('pagePlaceholders');
    const loadingMessage = document.getElementById('loadingMessage');
    const errorMessage = document.getElementById('errorMessage');
    const pdfContainer = document.getElementById('pdfContainer');
    const pdfViewer = document.getElementById('pdfViewer');
    const searchLoading = document.getElementById('searchLoading');

    // URL del PDF
    const pdfUrl = "{{ route('planillas-pdf.view.pdf', $planilla->id) }}";

    // Cargar PDF
    function loadPDF() {
        showLoading();
        hideError();

        const loadingTask = pdfjsLib.getDocument({
            url: pdfUrl,
            withCredentials: true
        });

        loadingTask.promise.then(
            function(pdf) {
                console.log('PDF cargado. Páginas:', pdf.numPages);
                pdfDoc = pdf;
                hideLoading();
                showPDF();
                initializeVirtualization();
            },
            function(error) {
                console.error('Error al cargar PDF:', error);
                showError('Error al cargar el PDF: ' + error.message);
            }
        );
    }

    // Inicializar virtualización
    async function initializeVirtualization() {
        if (!pdfDoc) return;

        const totalPages = pdfDoc.numPages;
        pageHeights = [];
        totalHeight = 0;

        // Calcular alturas de todas las páginas
        for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
            try {
                const page = await pdfDoc.getPage(pageNum);
                const viewport = page.getViewport({ scale: currentScale });
                pageViewports[pageNum] = viewport;

                const pageHeight = viewport.height + 20;
                pageHeights[pageNum] = pageHeight;
                totalHeight += pageHeight;

                page.cleanup();
            } catch (error) {
                console.error('Error calculando altura página', pageNum, ':', error);
                pageHeights[pageNum] = 800;
                totalHeight += 800;
            }
        }

        // Configurar contenedores
        pagesContainer.innerHTML = '';
        pagePlaceholders.innerHTML = '';
        pagePlaceholders.style.height = totalHeight + 'px';

        // Renderizar páginas visibles inicialmente
        checkVisiblePages();

        // Configurar scroll con debounce
        let scrollTimeout;
        pdfViewer.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(checkVisiblePages, RENDER_DEBOUNCE_MS);
        });

        updateZoomDisplay();
    }

    // Verificar páginas visibles
    async function checkVisiblePages() {
        if (!pdfDoc || isSearching) return;

        const viewerRect = pdfViewer.getBoundingClientRect();
        const scrollTop = pdfViewer.scrollTop;
        const viewerHeight = pdfViewer.clientHeight;

        const newVisiblePages = new Set();
        let currentTop = 0;

        for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
            const pageHeight = pageHeights[pageNum];
            const pageBottom = currentTop + pageHeight;

            if (pageBottom >= scrollTop - (viewerHeight * BUFFER_PAGES) &&
                currentTop <= scrollTop + viewerHeight + (viewerHeight * BUFFER_PAGES)) {
                newVisiblePages.add(pageNum);
            }

            currentTop = pageBottom;
        }

        // Eliminar páginas no visibles
        for (let pageNum of visiblePages) {
            if (!newVisiblePages.has(pageNum)) {
                removePage(pageNum);
            }
        }

        // Renderizar nuevas páginas visibles
        for (let pageNum of newVisiblePages) {
            if (!visiblePages.has(pageNum)) {
                await renderPage(pageNum);
            }
        }

        visiblePages = newVisiblePages;
    }

    // Renderizar una página específica
    async function renderPage(pageNum) {
        if (renderedPages.has(pageNum)) return;

        try {
            const page = await pdfDoc.getPage(pageNum);
            const viewport = pageViewports[pageNum] || page.getViewport({ scale: currentScale });

            // Crear contenedor para la página
            const pageDiv = document.createElement('div');
            pageDiv.className = 'pdf-page';
            pageDiv.id = `pdf-page-${pageNum}`;
            pageDiv.style.position = 'absolute';
            pageDiv.style.width = viewport.width + 'px';
            pageDiv.style.height = viewport.height + 'px';
            pageDiv.style.boxShadow = '0 2px 10px rgba(0,0,0,0.3)';
            pageDiv.setAttribute('data-page-number', pageNum);

            // Posicionar la página
            let pageTop = 0;
            for (let i = 1; i < pageNum; i++) {
                pageTop += pageHeights[i];
            }
            pageDiv.style.top = pageTop + 'px';
            pageDiv.style.left = '50%';
            pageDiv.style.transform = 'translateX(-50%)';
            pageDiv.style.marginBottom = '20px';

            // Crear canvas
            const canvas = document.createElement('canvas');
            canvas.className = 'pdf-canvas';
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.display = 'block';

            const ctx = canvas.getContext('2d');
            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            // Renderizar página
            await page.render(renderContext).promise;
            pageDiv.appendChild(canvas);

            // Obtener y almacenar contenido de texto
            const textContent = await page.getTextContent();
            textContentByPage[pageNum] = textContent;

            // Añadir capas de texto optimizadas
            await addOptimizedTextLayers(pageDiv, page, viewport, textContent);

            pagesContainer.appendChild(pageDiv);
            renderedPages.set(pageNum, pageDiv);

            page.cleanup();

        } catch (error) {
            console.error('Error renderizando página', pageNum, ':', error);
        }
    }

    // Añadir capas de texto optimizadas para selección y hover
    async function addOptimizedTextLayers(pageDiv, page, viewport, textContent) {
        const pageNum = parseInt(pageDiv.getAttribute('data-page-number'));

        // Capa de texto principal - para selección precisa
        const textLayer = document.createElement('div');
        textLayer.className = 'text-layer';
        textLayer.style.position = 'absolute';
        textLayer.style.left = '0';
        textLayer.style.top = '0';
        textLayer.style.width = '100%';
        textLayer.style.height = '100%';
        textLayer.style.pointerEvents = 'none';
        textLayer.style.zIndex = '2';
        textLayer.style.userSelect = 'text';
        textLayer.style.cursor = 'text';

        // Capa de hover - para resaltado suave
        const hoverLayer = document.createElement('div');
        hoverLayer.className = 'hover-layer';
        hoverLayer.style.position = 'absolute';
        hoverLayer.style.left = '0';
        hoverLayer.style.top = '0';
        hoverLayer.style.width = '100%';
        hoverLayer.style.height = '100%';
        hoverLayer.style.pointerEvents = 'none';
        hoverLayer.style.zIndex = '1';

        // Agrupar texto en líneas lógicas
        const lines = groupTextItemsIntoLines(textContent.items);

        // Crear elementos de texto y áreas de hover
        lines.forEach((line, lineIndex) => {
            // Elemento de texto para selección
            const textSpan = createTextSpan(line, viewport);
            textLayer.appendChild(textSpan);

            // Área de hover para resaltado
            const hoverArea = createHoverArea(line, viewport, lineIndex);
            hoverLayer.appendChild(hoverArea);
        });

        pageDiv.appendChild(hoverLayer);
        pageDiv.appendChild(textLayer);

        // Añadir capa de resaltado de búsqueda si hay texto de búsqueda
        if (searchText && allMatches.length > 0) {
            await addSearchHighlightLayer(pageDiv, pageNum, viewport);
        }
    }

    // Crear span de texto optimizado para selección
    function createTextSpan(line, viewport) {
        const textSpan = document.createElement('span');
        textSpan.className = 'text-span';
        textSpan.style.position = 'absolute';
        textSpan.style.left = `${line.transform[4]}px`;
        textSpan.style.top = `${line.transform[5]}px`;
        textSpan.style.width = `${line.width}px`;
        textSpan.style.height = `${line.height}px`;
        textSpan.style.whiteSpace = 'pre';
        textSpan.style.fontSize = `${line.fontSize}px`;
        textSpan.style.lineHeight = `${line.height}px`;
        textSpan.style.color = 'transparent';
        textSpan.style.pointerEvents = 'none';
        textSpan.style.userSelect = 'text';
        textSpan.style.webkitUserSelect = 'text';
        textSpan.style.mozUserSelect = 'text';
        textSpan.style.msUserSelect = 'text';
        textSpan.style.cursor = 'text';
        textSpan.textContent = line.text;

        return textSpan;
    }

    // Crear área de hover para resaltado
    function createHoverArea(line, viewport, lineIndex) {
        const hoverArea = document.createElement('div');
        hoverArea.className = 'hover-area';
        hoverArea.style.position = 'absolute';
        hoverArea.style.left = '0';
        hoverArea.style.top = `${line.transform[5] - 2}px`;
        hoverArea.style.width = '100%';
        hoverArea.style.height = `${line.height + 4}px`;
        hoverArea.style.pointerEvents = 'auto';
        hoverArea.style.cursor = 'text';
        hoverArea.style.zIndex = '1';
        hoverArea.style.transition = 'background-color 0.15s ease';
        hoverArea.setAttribute('data-line-index', lineIndex);

        // Eventos de hover
        hoverArea.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(173, 216, 230, 0.3)';
        });

        hoverArea.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
        });

        return hoverArea;
    }

    // Función mejorada para agrupar elementos de texto en líneas
    function groupTextItemsIntoLines(textItems) {
        const lines = [];

        // Primero ordenar por posición Y (de arriba a abajo)
        const sortedItems = [...textItems].sort((a, b) => {
            const aY = a.transform[5];
            const bY = b.transform[5];
            return bY - aY; // Orden descendente porque Y=0 es la parte superior
        });

        let currentLine = null;
        const LINE_THRESHOLD = 5; // Reducido para mejor agrupación

        sortedItems.forEach(item => {
            const itemY = item.transform[5];
            const itemX = item.transform[4];
            const itemWidth = item.width;
            const itemHeight = item.height;
            const fontSize = Math.sqrt(item.transform[0] * item.transform[0] + item.transform[1] * item.transform[1]);

            if (!currentLine || Math.abs(itemY - currentLine.transform[5]) > LINE_THRESHOLD) {
                // Nueva línea
                if (currentLine) {
                    lines.push(currentLine);
                }
                currentLine = {
                    transform: item.transform,
                    width: itemWidth,
                    height: itemHeight,
                    fontSize: fontSize,
                    text: item.str,
                    items: [item]
                };
            } else {
                // Misma línea - agregar texto
                currentLine.text += item.str;
                currentLine.width = itemX + itemWidth - currentLine.transform[4];
                currentLine.items.push(item);
            }
        });

        // Agregar la última línea
        if (currentLine) {
            lines.push(currentLine);
        }

        return lines;
    }

    // Añadir capa de resaltado de búsqueda
    async function addSearchHighlightLayer(pageDiv, pageNum, viewport) {
        let highlightLayer = pageDiv.querySelector('.highlight-layer');
        if (!highlightLayer) {
            highlightLayer = document.createElement('div');
            highlightLayer.className = 'highlight-layer';
            highlightLayer.style.position = 'absolute';
            highlightLayer.style.left = '0';
            highlightLayer.style.top = '0';
            highlightLayer.style.width = '100%';
            highlightLayer.style.height = '100%';
            highlightLayer.style.pointerEvents = 'none';
            highlightLayer.style.zIndex = '3';
            pageDiv.appendChild(highlightLayer);
        }

        // Aplicar resaltados específicos para esta página
        const pageMatches = allMatches.filter(match => match.page === pageNum);
        await applyHighlightsToPage(pageDiv, pageMatches, viewport);
    }

    // Búsqueda optimizada - CORREGIDA
    async function searchInPDF() {
        if (!pdfDoc || isSearching) return;

        isSearching = true;
        showSearchLoading();

        if (searchController) {
            searchController.abort();
        }
        searchController = new AbortController();

        try {
            clearAllHighlights();
            allMatches = [];
            currentMatchIndex = -1;

            const searchTerm = searchText.toLowerCase();
            let matchCount = 0;

            for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                if (searchController.signal.aborted) break;

                try {
                    const page = await pdfDoc.getPage(pageNum);
                    const textContent = await page.getTextContent();

                    // Buscar en todos los elementos de texto
                    textContent.items.forEach((textItem, itemIndex) => {
                        const text = textItem.str;
                        const lowerText = text.toLowerCase();
                        let startIndex = 0;

                        while ((startIndex = lowerText.indexOf(searchTerm, startIndex)) !== -1) {
                            allMatches.push({
                                page: pageNum,
                                textItem: textItem,
                                itemIndex: itemIndex,
                                matchIndex: startIndex,
                                matchLength: searchTerm.length,
                                globalIndex: matchCount++
                            });
                            startIndex += searchTerm.length;
                        }
                    });

                    page.cleanup();
                } catch (error) {
                    if (!searchController.signal.aborted) {
                        console.error('Error buscando en página', pageNum, ':', error);
                    }
                }
            }

            if (!searchController.signal.aborted) {
                console.log(`Encontradas ${allMatches.length} coincidencias`);
                updateMatchCounter();
                updateNavigationControls();

                if (allMatches.length > 0) {
                    await highlightAllMatches();
                    goToMatch(0); // Ir a la primera coincidencia
                }
            }

        } catch (error) {
            if (!searchController.signal.aborted) {
                console.error('Error en la búsqueda:', error);
            }
        } finally {
            isSearching = false;
            hideSearchLoading();
            searchController = null;
        }
    }

    // Resaltar todas las coincidencias - CORREGIDA
    async function highlightAllMatches() {
        const matchesByPage = {};
        allMatches.forEach(match => {
            if (!matchesByPage[match.page]) {
                matchesByPage[match.page] = [];
            }
            matchesByPage[match.page].push(match);
        });

        for (const pageNum in matchesByPage) {
            if (searchController && searchController.signal.aborted) break;

            const pageNumInt = parseInt(pageNum);

            // Asegurarse de que la página esté renderizada
            if (!renderedPages.has(pageNumInt)) {
                await renderPage(pageNumInt);
            }

            const pageDiv = document.getElementById(`pdf-page-${pageNumInt}`);
            if (pageDiv) {
                const viewport = pageViewports[pageNumInt];
                await applyHighlightsToPage(pageDiv, matchesByPage[pageNum], viewport);
            }
        }
    }

    // Aplicar resaltados a una página - CORREGIDA
    async function applyHighlightsToPage(pageDiv, pageMatches, viewport) {
        const pageNum = parseInt(pageDiv.getAttribute('data-page-number'));
        if (!viewport) {
            viewport = pageViewports[pageNum];
        }

        let highlightLayer = pageDiv.querySelector('.highlight-layer');
        if (!highlightLayer) {
            highlightLayer = document.createElement('div');
            highlightLayer.className = 'highlight-layer';
            highlightLayer.style.position = 'absolute';
            highlightLayer.style.left = '0';
            highlightLayer.style.top = '0';
            highlightLayer.style.width = '100%';
            highlightLayer.style.height = '100%';
            highlightLayer.style.pointerEvents = 'none';
            highlightLayer.style.zIndex = '3';
            pageDiv.appendChild(highlightLayer);
        }

        // Limpiar solo los resaltados existentes
        const existingHighlights = highlightLayer.querySelectorAll('.search-word-highlight');
        existingHighlights.forEach(el => el.remove());

        pageMatches.forEach((match) => {
            const textItem = match.textItem;

            // Calcular posición usando la transformada del texto
            const tx = pdfjsLib.Util.transform(viewport.transform, textItem.transform);

            // Calcular dimensiones del resaltado
            const charWidth = textItem.width / Math.max(textItem.str.length, 1);
            const highlightLeft = tx[4] + (charWidth * match.matchIndex);
            const highlightWidth = charWidth * match.matchLength;

            const wordHighlight = document.createElement('div');
            wordHighlight.className = 'search-word-highlight';
            wordHighlight.style.position = 'absolute';
            wordHighlight.style.left = highlightLeft + 'px';
            wordHighlight.style.top = (tx[5] - textItem.height * 0.1) + 'px';
            wordHighlight.style.width = Math.max(highlightWidth, 2) + 'px';
            wordHighlight.style.height = (textItem.height * 1.2) + 'px';
            wordHighlight.style.backgroundColor = 'rgba(255, 235, 59, 0.7)';
            wordHighlight.style.borderRadius = '2px';
            wordHighlight.style.zIndex = '4';
            wordHighlight.setAttribute('data-match-index', match.globalIndex);

            highlightLayer.appendChild(wordHighlight);
        });
    }

    // Limpiar resaltados
    function clearAllHighlights() {
        renderedPages.forEach((pageDiv) => {
            const highlightLayer = pageDiv.querySelector('.highlight-layer');
            if (highlightLayer) {
                highlightLayer.innerHTML = '';
            }
        });

        // También limpiar el resaltado actual
        const allHighlights = document.querySelectorAll('.search-word-highlight.current-match');
        allHighlights.forEach(el => {
            el.classList.remove('current-match');
            el.style.backgroundColor = 'rgba(255, 235, 59, 0.7)';
        });
    }

    // Navegar a coincidencia - CORREGIDA (esta era la función principal con problemas)
    function goToMatch(index) {
        if (index < 0 || index >= allMatches.length) {
            console.warn('Índice de coincidencia fuera de rango:', index);
            return;
        }

        console.log(`Navegando a coincidencia ${index + 1} de ${allMatches.length}`);

        // Limpiar resaltado anterior
        clearCurrentHighlight();

        // Actualizar índice actual
        currentMatchIndex = index;
        const match = allMatches[index];

        // Resaltar la coincidencia actual
        const highlightElements = document.querySelectorAll(`[data-match-index="${index}"]`);

        if (highlightElements.length > 0) {
            highlightElements.forEach(element => {
                element.classList.add('current-match');
                element.style.backgroundColor = 'rgba(255, 87, 34, 0.8)';
            });

            // Hacer scroll a la primera ocurrencia de esta coincidencia
            scrollToMatch(highlightElements[0]);
        } else {
            console.warn('No se encontró elemento de resaltado para el índice:', index);
            // Intentar renderizar la página si no está visible
            ensurePageRendered(match.page).then(() => {
                // Reintentar después de renderizar
                setTimeout(() => goToMatch(index), 100);
            });
        }

        updateMatchCounter();
        updateNavigationControls();
    }

    // Asegurar que una página esté renderizada
    async function ensurePageRendered(pageNum) {
        if (!renderedPages.has(pageNum)) {
            await renderPage(pageNum);
        }
        return true;
    }

    // Scroll a coincidencia - MEJORADA
    function scrollToMatch(highlightElement) {
        const pageDiv = highlightElement.closest('.pdf-page');
        if (pageDiv) {
            const pageTop = parseInt(pageDiv.style.top);
            const highlightTop = parseInt(highlightElement.style.top);
            const totalOffset = pageTop + highlightTop;

            console.log('Haciendo scroll a posición:', totalOffset);

            // Scroll suave a la posición calculada con margen adecuado
            pdfViewer.scrollTo({
                top: Math.max(0, totalOffset - 150), // Margen aumentado para mejor visibilidad
                behavior: 'smooth'
            });
        } else {
            console.warn('No se pudo encontrar el contenedor de página para el resaltado');
        }
    }

    function clearCurrentHighlight() {
        const currentHighlights = document.querySelectorAll('.search-word-highlight.current-match');
        currentHighlights.forEach(el => {
            el.classList.remove('current-match');
            el.style.backgroundColor = 'rgba(255, 235, 59, 0.7)';
        });
    }

    // Navegación - CORREGIDA
    function goToNextMatch() {
        if (allMatches.length === 0) return;

        let nextIndex = currentMatchIndex + 1;
        if (nextIndex >= allMatches.length) {
            nextIndex = 0; // Circular: volver al inicio
        }

        goToMatch(nextIndex);
    }

    function goToPreviousMatch() {
        if (allMatches.length === 0) return;

        let prevIndex = currentMatchIndex - 1;
        if (prevIndex < 0) {
            prevIndex = allMatches.length - 1; // Circular: ir al final
        }

        goToMatch(prevIndex);
    }

    // Zoom (sin cambios)
    function zoomIn() {
        currentScale = Math.min(2.5, currentScale + 0.25);
        updateZoom();
    }

    function zoomOut() {
        currentScale = Math.max(0.5, currentScale - 0.25);
        updateZoom();
    }

    async function updateZoom() {
        updateZoomDisplay();
        if (pdfDoc) {
            renderedPages.clear();
            visiblePages.clear();
            textContentByPage = {};
            await initializeVirtualization();

            // Re-aplicar búsqueda si existe
            if (searchText) {
                setTimeout(() => searchInPDF(), 500);
            }
        }
    }

    function updateZoomDisplay() {
        document.getElementById('zoomLevel').textContent = Math.round(currentScale * 100) + '%';
    }

    // Búsqueda
    async function performSearch() {
        const newSearchText = document.getElementById('searchInput').value.trim();

        if (!newSearchText) {
            clearSearch();
            return;
        }

        searchText = newSearchText;

        if (pdfDoc) {
            await searchInPDF();
        }
    }

    function clearSearch() {
        searchText = '';
        document.getElementById('searchInput').value = '';
        allMatches = [];
        currentMatchIndex = -1;
        clearAllHighlights();
        updateMatchCounter();
        updateNavigationControls();

        if (searchController) {
            searchController.abort();
        }
        hideSearchLoading();
    }

    function updateMatchCounter() {
        const counter = document.getElementById('matchCounter');
        const info = document.getElementById('matchInfo');

        if (allMatches.length > 0) {
            info.className = 'text-warning fw-bold';
            info.innerHTML = `<span id="matchCounter">${allMatches.length}</span> coincidencias (${currentMatchIndex + 1}/${allMatches.length})`;
        } else if (searchText) {
            info.className = 'text-danger';
            info.innerHTML = '<span id="matchCounter">0</span> coincidencias';
        } else {
            info.className = 'text-muted';
            info.innerHTML = '<span id="matchCounter">0</span> coincidencias';
        }
    }

    function updateNavigationControls() {
        const prevMatchBtn = document.getElementById('prevMatch');
        const nextMatchBtn = document.getElementById('nextMatch');

        const hasMatches = allMatches.length > 0;
        prevMatchBtn.disabled = !hasMatches;
        nextMatchBtn.disabled = !hasMatches;
    }

    // Gestión de estados de UI (sin cambios)
    function showLoading() {
        loadingMessage.classList.remove('d-none');
        pdfContainer.classList.add('d-none');
        errorMessage.classList.add('d-none');
        hideSearchLoading();
    }

    function hideLoading() {
        loadingMessage.classList.add('d-none');
    }

    function showPDF() {
        pdfContainer.classList.remove('d-none');
    }

    function showError(message) {
        loadingMessage.classList.add('d-none');
        pdfContainer.classList.add('d-none');
        errorMessage.classList.remove('d-none');
        document.getElementById('errorText').textContent = message;
        hideSearchLoading();
    }

    function hideError() {
        errorMessage.classList.add('d-none');
    }

    function showSearchLoading() {
        searchLoading.classList.remove('d-none');
    }

    function hideSearchLoading() {
        searchLoading.classList.add('d-none');
    }

    // Configurar event listeners
    function setupEventListeners() {
        document.getElementById('zoomIn').addEventListener('click', zoomIn);
        document.getElementById('zoomOut').addEventListener('click', zoomOut);
        document.getElementById('searchButton').addEventListener('click', performSearch);
        document.getElementById('clearSearch').addEventListener('click', clearSearch);
        document.getElementById('prevMatch').addEventListener('click', goToPreviousMatch);
        document.getElementById('nextMatch').addEventListener('click', goToNextMatch);

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        document.getElementById('cancelSearch').addEventListener('click', function() {
            if (searchController) {
                searchController.abort();
                hideSearchLoading();
                isSearching = false;
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('searchInput').focus();
                document.getElementById('searchInput').select();
            }
            else if (e.ctrlKey && e.key === '+') {
                e.preventDefault();
                zoomIn();
            }
            else if (e.ctrlKey && e.key === '-') {
                e.preventDefault();
                zoomOut();
            }
            else if (e.key === 'F3') {
                e.preventDefault();
                goToNextMatch();
            }
            else if (e.shiftKey && e.key === 'F3') {
                e.preventDefault();
                goToPreviousMatch();
            }
        });

        document.getElementById('retryButton').addEventListener('click', loadPDF);

        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(checkVisiblePages, 250);
        });
    }

    // Inicializar
    setupEventListeners();
    loadPDF();
}
</script>
@endsection
