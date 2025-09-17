$(function() {
    console.log("✅ main.js cargado con jQuery y Waypoints");

    // Ejemplo: waypoint sobre un div con clase .seccion
    $('.seccion').waypoint(function(direction) {
        console.log('📍 Waypoint activado! Dirección:', direction);
    }, {
        offset: '50%'
    });
});

$(function() {
    console.log("✅ main.js cargado");

    // Inicializar Select2
    $('.mi-select').select2();

    // Waypoints de ejemplo
    $('.seccion').waypoint(function(direction) {
        console.log('📍 Waypoint activado! Dirección:', direction);
    }, {
        offset: '50%'
    });
});
$(document).ready(function() {
    // Select2
    $('.mi-select').select2();

    // Waypoints
    $('.mi-elemento').waypoint(function(direction) {
        console.log('Waypoint activado');
    });
});


