import './bootstrap';

// jQuery ya está cargado globalmente desde el CDN
// Verificar que jQuery esté disponible
if (typeof $ === 'undefined') {
    console.error('jQuery no está disponible');
} else {
    console.log('✅ jQuery cargado:', $.fn.jquery);
}

// Cargar Waypoints y Select2 que dependen de jQuery
import 'waypoints/lib/jquery.waypoints.min.js';
import 'select2';

// Importar TomSelect
import TomSelect from "tom-select";
import 'tom-select/dist/css/tom-select.bootstrap5.css';

console.log("✅ app.js inicializado");

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM cargado - inicializando componentes');

    // Inicializar TomSelect si el elemento existe
    const tomSelectElement = document.getElementById('idPersona');
    if (tomSelectElement) {
        try {
            new TomSelect('#idPersona', {
                maxItems: 1,
                allowEmptyOption: true,
                placeholder: "Seleccione una persona...",
            });
            console.log('✅ Tom Select inicializado');
        } catch (error) {
            console.error('❌ Error inicializando Tom Select:', error);
        }
    }

    // Inicializar Select2 si está disponible
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-basic').select2({
            placeholder: 'Seleccionar...',
            allowClear: true,
            width: '100%'
        });
        console.log('✅ Select2 inicializado');
    }
});
