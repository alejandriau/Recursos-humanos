@extends('dashboard')

@section('title', 'Mapa de Croquis')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Mapa de Croquis</h5>
        <a href="{{ route('croquis.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
    <div class="card-body">
        <div id="map" style="height: 600px; width: 100%; border-radius: 5px; border: 1px solid #ccc;"></div>

        <div class="mt-3">
            <h6>Leyenda:</h6>
            <div class="d-flex flex-wrap gap-3">
                <span class="badge bg-primary"><i class="fas fa-map-marker-alt"></i> Ubicación registrada</span>
                <span class="badge bg-success"><i class="fas fa-user"></i> Persona asociada</span>
            </div>
        </div>

        <div class="mt-3">
            <h6>Croquis Registrados:</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Persona</th>
                            <th>Dirección</th>
                            <th>Coordenadas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($croquis as $croqui)
                        <tr>
                            <td>{{ $croqui->id }}</td>
                            <td>{{ $croqui->persona->nombre ?? 'N/A' }}</td>
                            <td>{{ Str::limit($croqui->direccion, 40) }}</td>
                            <td>
                                <small>Lat: {{ $croqui->latitud }}</small><br>
                                <small>Lng: {{ $croqui->longitud }}</small>
                            </td>
                            <td>
                                <a href="{{ route('croquis.show', $croqui) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ $croqui->google_maps_link }}" target="_blank" class="btn btn-sm btn-success">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay croquis registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .map-marker {
        color: white;
        font-weight: bold;
        text-align: center;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        line-height: 30px;
        background-color: #dc3545;
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }

    .gm-style .gm-style-iw-c {
        padding: 0;
        border-radius: 8px;
    }

    .gm-style .gm-style-iw-d {
        overflow: auto !important;
    }

    .info-window-content {
        padding: 15px;
    }
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
<script>
    let map;
    let markers = [];
    const infoWindow = new google.maps.InfoWindow();

    function initMap() {
        // Centro inicial del mapa (Lima, Perú por defecto)
        const initialCenter = { lat: -12.046374, lng: -77.042793 };

        // Configuración del mapa
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: initialCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [
                {
                    featureType: 'poi',
                    stylers: [{ visibility: 'off' }]
                },
                {
                    featureType: 'transit',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });

        // Cargar datos de croquis
        loadCroquisData();
    }

    function loadCroquisData() {
        fetch('{{ route('croquis.api.datos') }}')
            .then(response => response.json())
            .then(data => {
                addMarkersToMap(data);

                // Ajustar el zoom para mostrar todos los marcadores
                if (data.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    data.forEach(croqui => {
                        bounds.extend(new google.maps.LatLng(croqui.lat, croqui.lng));
                    });
                    map.fitBounds(bounds);
                }
            })
            .catch(error => {
                console.error('Error loading croquis data:', error);
                showDefaultMap();
            });
    }

    function addMarkersToMap(croquisData) {
        // Limpiar marcadores existentes
        clearMarkers();

        croquisData.forEach((croqui, index) => {
            const marker = new google.maps.Marker({
                position: { lat: croqui.lat, lng: croqui.lng },
                map: map,
                title: croqui.direccion,
                animation: google.maps.Animation.DROP,
                icon: {
                    url: 'data:image/svg+xml;base64,' + btoa(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                            <circle cx="15" cy="15" r="12" fill="#dc3545" stroke="#ffffff" stroke-width="2"/>
                            <text x="15" y="20" text-anchor="middle" fill="white" font-size="12" font-weight="bold">${index + 1}</text>
                        </svg>
                    `),
                    scaledSize: new google.maps.Size(30, 30),
                    anchor: new google.maps.Point(15, 15)
                }
            });

            // Crear contenido para la ventana de información
            const content = `
                <div class="info-window-content">
                    <h6 class="mb-2">Croquis #${croqui.id}</h6>
                    <p class="mb-1"><strong>Persona:</strong> ${croqui.persona}</p>
                    <p class="mb-1"><strong>Dirección:</strong> ${croqui.direccion}</p>
                    <p class="mb-2"><strong>Descripción:</strong> ${croqui.descripcion || 'Sin descripción'}</p>
                    <p class="mb-2"><strong>Coordenadas:</strong><br>
                    Lat: ${croqui.lat.toFixed(6)}<br>
                    Lng: ${croqui.lng.toFixed(6)}</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="${croqui.show_url}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="${croqui.edit_url}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="${croqui.google_maps_link}" target="_blank" class="btn btn-sm btn-success">
                            <i class="fas fa-external-link-alt"></i> Maps
                        </a>
                    </div>
                </div>
            `;

            // Agregar evento de clic al marcador
            marker.addListener('click', () => {
                infoWindow.close();
                infoWindow.setContent(content);
                infoWindow.open(map, marker);

                // Centrar el mapa en el marcador
                map.panTo(marker.getPosition());
            });

            markers.push(marker);
        });
    }

    function clearMarkers() {
        markers.forEach(marker => marker.setMap(null));
        markers = [];
    }

    function showDefaultMap() {
        // Mostrar mensaje si no hay datos
        const content = `
            <div class="info-window-content">
                <h6 class="mb-2">No hay croquis registrados</h6>
                <p class="mb-2">No se encontraron ubicaciones de croquis en la base de datos.</p>
                <a href="{{ route('croquis.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Crear primer croquis
                </a>
            </div>
        `;

        infoWindow.setContent(content);
        infoWindow.setPosition(map.getCenter());
        infoWindow.open(map);
    }

    // Manejar errores de Google Maps
    window.gm_authFailure = function() {
        alert('Error de autenticación con Google Maps. Verifique la API key.');
    };

    // Recargar el mapa si hay error de carga
    setTimeout(() => {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            alert('Error al cargar Google Maps. Verifique su conexión a internet.');
        }
    }, 5000);
</script>
@endpush
