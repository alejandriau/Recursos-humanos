// resources/js/main.js
import 'jquery';
import 'waypoints/lib/noframework.waypoints.min.js';
import 'select2';

// Esperar a que el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ main.js cargado correctamente');

    // Inicializar Waypoints solo si existe
    if (typeof Waypoint !== 'undefined') {
        // Tu código de waypoints aquí
        console.log('✅ Waypoints cargado');
    } else {
        console.warn('❌ Waypoints no está disponible');
    }

    // Inicializar Select2 solo si existe
    if ($.fn.select2) {
        // Select2 para elementos básicos
        $('.select2-basic').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    placeholder: $(this).data('placeholder') || 'Seleccionar...',
                    allowClear: true,
                    width: '100%'
                });
            }
        });

        // Select2 para búsquedas con AJAX
        $('.select2-ajax').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    placeholder: $(this).data('placeholder') || 'Buscar...',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: $(this).data('min-length') || 2,
                    ajax: {
                        url: $(this).data('url'),
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.data || data,
                                pagination: {
                                    more: (params.page * 10) < (data.total || 0)
                                }
                            };
                        },
                        cache: true
                    }
                });
            }
        });
        console.log('✅ Select2 inicializado');
    } else {
        console.warn('❌ Select2 no está disponible');
    }
});

// Manejar errores globales
window.addEventListener('error', function(e) {
    console.error('Error global:', e.error);
});
