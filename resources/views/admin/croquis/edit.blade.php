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

            <!-- Campos del formulario (persona, etc.) -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="idPersona" class="form-label">Persona *</label>
                        <select class="form-select" id="idPersona" name="idPersona" required>
                            <option value="">Seleccionar Persona</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}">{{ $persona->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Buscador -->
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
                        Ej: "Lima, Perú", "Avenida Javier Prado", "Miraflores"
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección *</label>
                <input type="text" class="form-control" id="direccion" name="direccion"
                       required maxlength="500">
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion"
                          rows="3" maxlength="500" placeholder="Descripción adicional"></textarea>
            </div>

            <!-- Mapa con Leaflet -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Seleccionar Ubicación en el Mapa</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Haga clic en el mapa para seleccionar las coordenadas.
                        Puede arrastrar el marcador para ajustar.
                    </div>
                    <div id="map" style="height: 400px; width: 100%; border-radius: 5px; border: 1px solid #ccc;"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="latitud" class="form-label">Latitud *</label>
                        <input type="number" step="any" class="form-control"
                               id="latitud" name="latitud" required readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="longitud" class="form-label">Longitud *</label>
                        <input type="number" step="any" class="form-control"
                               id="longitud" name="longitud" required readonly>
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
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        cursor: pointer;
    }
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map;
    let marker;
    let defaultCenter = [-12.046374, -77.042793]; // Lima, Perú

    // Inicializar el mapa cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        setupEventListeners();
    });

    function initMap() {
        // Crear mapa con OpenStreetMap
        map = L.map('map').setView(defaultCenter, 12);

        // Capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Crear marcador inicial
        marker = L.marker(defaultCenter, {
            draggable: true,
            title: 'Arrastre para ajustar la ubicación'
        }).addTo(map);

        // Evento al hacer clic en el mapa
        map.on('click', function(e) {
            updateMarkerPosition(e.latlng);
            updateAddressFromCoordinates(e.latlng);
        });

        // Evento al arrastrar el marcador
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateCoordinates(position);
            updateAddressFromCoordinates(position);
        });

        // Inicializar coordenadas
        updateCoordinates(defaultCenter);
    }

    function setupEventListeners() {
        // Buscar dirección
        document.getElementById('btnSearch').addEventListener('click', searchAddress);

        document.getElementById('searchAddress').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchAddress();
            }
        });
    }

    function updateMarkerPosition(latlng) {
        marker.setLatLng(latlng);
        map.panTo(latlng);
        updateCoordinates(latlng);
        enableSubmit();
    }

    function updateCoordinates(latlng) {
        document.getElementById('latitud').value = latlng.lat;
        document.getElementById('longitud').value = latlng.lng;
    }

    function enableSubmit() {
        document.getElementById('btnSubmit').disabled = false;
    }

    function searchAddress() {
        const query = document.getElementById('searchAddress').value;
        if (!query) {
            alert('Por favor, ingrese una dirección para buscar.');
            return;
        }

        // Usar Nominatim (geocodificador gratuito de OpenStreetMap)
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const result = data[0];
                    const latlng = [parseFloat(result.lat), parseFloat(result.lon)];

                    // Actualizar mapa y marcador
                    map.setView(latlng, 15);
                    updateMarkerPosition(latlng);

                    // Actualizar dirección
                    document.getElementById('direccion').value = result.display_name;
                    enableSubmit();
                } else {
                    alert('No se encontró la dirección: ' + query);
                }
            })
            .catch(error => {
                console.error('Error en la búsqueda:', error);
                alert('Error al buscar la dirección. Intente nuevamente.');
            });
    }

    function updateAddressFromCoordinates(latlng) {
        // Geocodificación inversa con Nominatim
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('direccion').value = data.display_name;
                }
            })
            .catch(error => {
                console.error('Error en geocodificación inversa:', error);
            });
    }

    // Validación del formulario
    document.getElementById('croquisForm').addEventListener('submit', function(e) {
        const latitud = parseFloat(document.getElementById('latitud').value);
        const longitud = parseFloat(document.getElementById('longitud').value);

        if (!latitud || !longitud) {
            e.preventDefault();
            alert('Por favor, seleccione una ubicación en el mapa.');
            return false;
        }

        if (latitud < -90 || latitud > 90) {
            e.preventDefault();
            alert('La latitud debe estar entre -90 y 90 grados.');
            return false;
        }

        if (longitud < -180 || longitud > 180) {
            e.preventDefault();
            alert('La longitud debe estar entre -180 y 180 grados.');
            return false;
        }
    });
</script>
@endpush
