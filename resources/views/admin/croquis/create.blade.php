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
                        Ej: "Cochabamba, Bolivia", "Avenida Aroma, Cochabamba", "Plaza 14 de Septiembre"
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

            <!-- Mapa Satelital con Nombres de Calles -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Seleccionar Ubicación (Satélite + nombres de calles)</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Haga clic en el mapa o arrastre el marcador. Verá imagen satelital con nombres de calles superpuestos.
                    </div>
                    <div id="map" style="height: 400px; width: 100%; border-radius: 5px; border: 1px solid #ccc;"></div>
                    <div class="mt-2">
                        <small class="text-muted" id="coordinatesInfo">
                            Coordenadas: -17.3895, -66.1568 (Cochabamba)
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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map { height: 400px; width: 100%; border-radius: 5px; border: 1px solid #ccc; z-index: 1; }
    .leaflet-container { height: 100%; width: 100%; }
</style>

<script>
    let map, marker;
    const COCHABAMBA = [-17.3895, -66.1568];

    function initializeMap() {
        console.log('Inicializando mapa satelital + nombres...');
        try {
            map = L.map('map').setView(COCHABAMBA, 14);

            // Capa 1: Imagen satelital de Esri (gratis)
L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles &copy; Esri',
    maxZoom: 18
}).addTo(map);
            // Capa 2: Solo nombres de calles y lugares (fondo transparente) de Stamen Toner Labels
L.tileLayer('http://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
    maxZoom: 18
}).addTo(map);

            const customIcon = L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            marker = L.marker(COCHABAMBA, { draggable: true, icon: customIcon, title: 'Arrastre para ajustar' }).addTo(map);

            map.on('click', e => { updateMarkerPosition(e.latlng); reverseGeocode(e.latlng); });
            marker.on('dragend', () => { const pos = marker.getLatLng(); updateCoordinates(pos); reverseGeocode(pos); enableSubmit(); });

            updateCoordinates({ lat: COCHABAMBA[0], lng: COCHABAMBA[1] });
            reverseGeocode({ lat: COCHABAMBA[0], lng: COCHABAMBA[1] });
        } catch (error) {
            console.error(error);
            document.getElementById('map').innerHTML = '<div style="padding:20px; text-align:center;">Error al cargar el mapa</div>';
        }
    }

    function updateMarkerPosition(latlng) {
        marker.setLatLng(latlng);
        map.panTo(latlng);
        updateCoordinates(latlng);
        enableSubmit();
    }

    function updateCoordinates(latlng) {
        document.getElementById('latitud').value = latlng.lat.toFixed(6);
        document.getElementById('longetud').value = latlng.lng.toFixed(6);
        document.getElementById('coordinatesInfo').textContent = `Coordenadas: ${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`;
    }

    function enableSubmit() {
        document.getElementById('btnSubmit').disabled = false;
    }

    // Búsqueda de direcciones con Nominatim (OSM)
    function searchAddress() {
        const query = document.getElementById('searchAddress').value.trim();
        if (!query) return alert('Ingrese una dirección.');
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=bo`)
            .then(res => res.json())
            .then(data => {
                if (data && data.length) {
                    const latlng = L.latLng(data[0].lat, data[0].lon);
                    map.setView(latlng, 16);
                    updateMarkerPosition(latlng);
                    document.getElementById('direccion').value = data[0].display_name;
                } else alert('No se encontró la dirección');
            })
            .catch(() => alert('Error en la búsqueda'));
    }

    // Geocodificación inversa (coordenadas -> dirección)
    function reverseGeocode(latlng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}&zoom=18&addressdetails=1`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) document.getElementById('direccion').value = data.display_name;
            })
            .catch(err => console.log('Geocodificación inversa falló:', err));
    }

    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(initializeMap, 100);
        document.getElementById('btnSearch').addEventListener('click', searchAddress);
        document.getElementById('searchAddress').addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); searchAddress(); } });
        document.getElementById('croquisForm').addEventListener('submit', e => {
            if (!parseFloat(document.getElementById('latitud').value) || !parseFloat(document.getElementById('longetud').value)) {
                e.preventDefault();
                alert('Por favor, seleccione una ubicación en el mapa.');
            }
        });
    });
</script>
@endsection