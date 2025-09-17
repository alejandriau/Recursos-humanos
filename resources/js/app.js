import './bootstrap';

import TomSelect from "tom-select";
import 'waypoints/lib/noframework.waypoints.min.js';
import 'tom-select/dist/css/tom-select.bootstrap5.css';



document.addEventListener('DOMContentLoaded', function() {
    console.log('Tom Select inicializado');

    new TomSelect('#idPersona', {
        maxItems: 1,
        allowEmptyOption: true,
        placeholder: "Seleccione una persona...",
        // opciones extra si quieres...
    });
});

// Importar jQuery y exponerlo globalmente
import $ from 'jquery';
window.$ = window.jQuery = $;

// Importar Waypoints y Select2 después de jQuery
import 'waypoints/lib/jquery.waypoints.min.js';
import 'select2/dist/js/select2.min.js';
import 'select2/dist/css/select2.min.css';

// Importar tu script personalizado
import './main.js';

console.log("✅ app.js inicializado con jQuery, Waypoints y Select2");
