<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<div id="app" data-personal='@json($personal)'></div>

<script>
window.addEventListener('DOMContentLoaded', () => {

    // Obtener datos del login
    const responseData = localStorage.getItem('responseData');

    if (!responseData) {
        location.href = '/';
        return;
    }

    const data = JSON.parse(responseData);

    // Datos de Laravel (Blade)
    const personal = JSON.parse(
        document.getElementById('app').dataset.personal
    );

    let estado = 0;

    // Comparación
    for (let i = 0; i < personal.length; i++) {
        if (data.data[0].idServidor == personal[i].idservidor) {
            estado = 1;
            break;
        }
    }

    // Redirección
    if (estado === 1) {
        location.href = '/homeusr';
    } else {
        location.href = '/form';
    }

});
</script>