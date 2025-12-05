@extends('dashboard')

@section('title', 'Crear Croquis')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nuevo Croquis</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('croquis.store') }}" method="POST" id="croquisForm">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="idPersona" class="form-label">Persona *</label>
                        <select class="form-select @error('idPersona') is-invalid @enderror"
                                id="idPersona" name="idPersona" required>
                            <option value="">Seleccionar Persona</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}" {{ old('idPersona') == $persona->id ? 'selected' : '' }}>
                                    {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}
                                </option>
                            @endforeach
                        </select>
                        @error('idPersona')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Buscador de Direcciones -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-search"></i> Buscar Ubicación</h6>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchAddress"
                               placeholder="Escriba una dirección, ciudad o región...">
                        <button class="btn btn-primary" type="button" id="btnSearch">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="form-text">
                        Ej: "Cochabamba, Bolivia", "Avenida Aroma, Cochabamba", "Jaihuayco, Cochabamba"
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección *</label>
                <input type="text" class="form-control @error('direccion') is-invalid @enderror"
                       id="direccion" name="direccion" value="{{ old('direccion') }}"
                       placeholder="La dirección se completará automáticamente" required maxlength="500">
                @error('direccion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion" name="descripcion" rows="3" maxlength="500"
                          placeholder="Descripción adicional del lugar">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mapa Interactivo con Leaflet -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Seleccionar Ubicación en el Mapa</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Haga clic en cualquier lugar del mapa para seleccionar las coordenadas.
                    </div>

                    <div id="map" style="height: 400px; width: 100%; border-radius: 5px; border: 1px solid #ccc;"></div>

                    <div class="mt-2">
                        <small class="text-muted" id="coordinatesInfo">
                            Coordenadas: -12.046374, -77.042793
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="latitud" class="form-label">Latitud *</label>
                        <input type="number" step="any" class="form-control @error('latitud') is-invalid @enderror"
                               id="latitud" name="latitud" value="{{ old('latitud') }}"
                               placeholder="Seleccione en el mapa" required readonly>
                        @error('latitud')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="longetud" class="form-label">Longitud *</label>
                        <input type="number" step="any" class="form-control @error('longetud') is-invalid @enderror"
                               id="longetud" name="longetud" value="{{ old('longetud') }}"
                               placeholder="Seleccione en el mapa" required readonly>
                        @error('longetud')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('croquis.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<style>
    #map {
        height: 400px;
        width: 100%;
        border-radius: 5px;
        border: 1px solid #ccc;
        z-index: 1;
    }


    .leaflet-container {
        height: 100%;
        width: 100%;
        font-family: inherit;
    }
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
    // Variables globales
    let map;
    let marker;
    const defaultCenter = [-12.046374, -77.042793]; // Lima, Perú

    // Función para inicializar el mapa
    function initializeMap() {
        console.log('🚀 Inicializando mapa Leaflet...');

        try {
            // 1. Crear el mapa
            map = L.map('map', {
                center: defaultCenter,
                zoom: 12,
                zoomControl: true,
                dragging: true
            });

            // 2. Añadir capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // 3. Configurar ícono personalizado para evitar CORS
            const customIcon = L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // 4. Crear marcador inicial
            marker = L.marker(defaultCenter, {
                draggable: true,
                icon: customIcon,
                title: 'Arrastre para ajustar la ubicación'
            }).addTo(map);

            // 5. Evento para hacer clic en el mapa
            map.on('click', function(e) {
                console.log('📍 Click en:', e.latlng);
                updateMarkerPosition(e.latlng);
                reverseGeocode(e.latlng);
            });

            // 6. Evento para arrastrar el marcador
            marker.on('dragend', function() {
                const position = marker.getLatLng();
                console.log('🔧 Marcador movido:', position);
                updateCoordinates(position);
                reverseGeocode(position);
            });

            // 7. Inicializar coordenadas
            updateCoordinates({ lat: defaultCenter[0], lng: defaultCenter[1] });

            console.log('✅ Mapa inicializado correctamente');

        } catch (error) {
            console.error('❌ Error al inicializar el mapa:', error);
            showMapError('Error técnico: ' + error.message);
        }
    }

    // Función para actualizar la posición del marcador
    function updateMarkerPosition(latlng) {
        marker.setLatLng(latlng);
        map.panTo(latlng);
        updateCoordinates(latlng);
        enableSubmit();
    }

    // Función para actualizar los campos de coordenadas
    function updateCoordinates(latlng) {
        const latInput = document.getElementById('latitud');
        const lngInput = document.getElementById('longetud');
        const coordsInfo = document.getElementById('coordinatesInfo');

        if (latInput && lngInput && coordsInfo) {
            latInput.value = latlng.lat.toFixed(6);
            lngInput.value = latlng.lng.toFixed(6);
            coordsInfo.textContent = `Coordenadas: ${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`;
        }
    }

    // Habilitar el botón de enviar
    function enableSubmit() {
        const btnSubmit = document.getElementById('btnSubmit');
        if (btnSubmit) {
            btnSubmit.disabled = false;
        }
    }

    // Función para buscar dirección
    function searchAddress() {
        const query = document.getElementById('searchAddress').value.trim();
        if (!query) {
            alert('Por favor, ingrese una dirección para buscar.');
            return;
        }

        console.log('🔍 Buscando:', query);

        // Usar Nominatim para geocodificación (gratuito)
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=pe`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    const result = data[0];
                    const latlng = L.latLng(parseFloat(result.lat), parseFloat(result.lon));

                    console.log('✅ Dirección encontrada:', result.display_name);

                    // Mover el mapa a la ubicación encontrada
                    map.setView(latlng, 15);
                    updateMarkerPosition(latlng);

                    // Actualizar la dirección
                    document.getElementById('direccion').value = result.display_name;

                } else {
                    alert('No se encontró la dirección: ' + query);
                }
            })
            .catch(error => {
                console.error('❌ Error en la búsqueda:', error);
                alert('Error al buscar la dirección. Intente nuevamente.');
            });
    }

    // Geocodificación inversa (obtener dirección desde coordenadas)
    function reverseGeocode(latlng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('direccion').value = data.display_name;
                }
            })
            .catch(error => {
                console.log('⚠️ Geocodificación inversa no disponible');
            });
    }

    // Función para mostrar error del mapa
    function showMapError(message = 'Error al cargar el mapa') {
        const mapDiv = document.getElementById('map');
        if (mapDiv) {
            mapDiv.innerHTML = `
                <div style="height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #6c757d; padding: 20px; text-align: center;">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${message}</p>
                    <small>Recargue la página o contacte al administrador</small>
                </div>
            `;
        }
    }

    // Configurar event listeners cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📋 DOM cargado, inicializando...');

        // Inicializar el mapa con un pequeño delay para asegurar la carga
        setTimeout(() => {
            initializeMap();
        }, 100);

        // Configurar event listeners
        const btnSearch = document.getElementById('btnSearch');
        const searchInput = document.getElementById('searchAddress');

        if (btnSearch) {
            btnSearch.addEventListener('click', searchAddress);
        }

        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchAddress();
                }
            });
        }

        // Validación del formulario
        const form = document.getElementById('croquisForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const latitud = parseFloat(document.getElementById('latitud').value);
                const longitud = parseFloat(document.getElementById('longetud').value);

                if (!latitud || !longitud) {
                    e.preventDefault();
                    alert('Por favor, seleccione una ubicación en el mapa.');
                    return false;
                }
            });
        }

        console.log('✅ Event listeners configurados');
    });

    // Manejar errores globales de JavaScript
    window.addEventListener('error', function(e) {
        console.error('❌ Error global:', e.error);
    });

    setTimeout(() => {
        map.invalidateSize();
    }, 500);

</script>
@endsection


