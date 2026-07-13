// funcion para listar vacaciones
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('listVac').addEventListener('click', async () => {
        try {

            const response = await axios.post(
                '/list-vacacion', {
                idserv: idserv
            });

            console.log('Respuesta del servidor:', response.data);
            const tbody = document.querySelector('#idvac tbody');
            const estadoClass = {
                espera: 'text-warning',
                validado: 'text-success',
                rechazado: 'text-danger'

            };
            const voboClass = {
                pendiente: 'text-warning',
                aprobado: 'text-success',
                rechazado: 'text-danger'
            };
            tbody.innerHTML = '';
            // Recorremos el array y agregamos filas a la tabla
            response.data.personal.forEach((item, index) => {
                const [yearSol, monthSol, daySol] = item.fechasol.split('-');
                const fechaSol = `${daySol}-${monthSol}-${yearSol}`;

                const [yearSal, monthSal, daySal] = item.fechasal.split('-');
                const fechaSal = `${daySal}-${monthSal}-${yearSal}`;

                const [yearRet, monthRet, dayRet] = item.fecharet.split('-');
                const fechaRet = `${dayRet}-${monthRet}-${yearRet}`;

                const row = document.createElement('tr');

                row.innerHTML = `
                <td>${index + 1}</td>
                <td>${fechaSol}</td>
                <td>${fechaSal}</td>
                <td>${fechaRet}</td>
                <td>${item.cantidad}</td>
                <td class="${voboClass[item.vobo] || ''}">${item.vobo}</td>
                <td class="${estadoClass[item.estado] || ''}">${item.estado}</td>
                `;
                tbody.appendChild(row);
            });

        } catch (error) {
            console.error('Error al leer datos:', error);

        }



    });
});
// ***************************************************************************************************
// funcion para mostrar lista de comisiones ***************************************************


document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('listCom').addEventListener('click', async () => {
        try {
            const response = await axios.post('/list-comision', {
                idserv
            });

            console.log('Respuesta del servidor:', response.data);
            const tbody = document.querySelector('#idcom tbody');
            const estadoClass = {
                espera: 'text-warning',
                validado: 'text-success',
                rechazado: 'text-danger'
            };
            const voboClass = {
                pendiente: 'text-warning',
                aprobado: 'text-success',
                rechazado: 'text-danger'
            };
            tbody.innerHTML = '';

            response.data.personal.forEach((item, index) => {
                const [yearSol, monthSol, daySol] = item.fechasol.split('-');
                const fechaSol = `${daySol}-${monthSol}-${yearSol}`;

                const [yearSal, monthSal, daySal] = item.fechasal.split('-');
                const fechaSal = `${daySal}-${monthSal}-${yearSal}`;

                const [yearRet, monthRet, dayRet] = item.fecharet.split('-');
                const fechaRet = `${dayRet}-${monthRet}-${yearRet}`;

                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${fechaSol}</td>
                    <td>${fechaSal}</td>
                    <td>${item.horasal}</td>
                    <td>${fechaRet}</td>
                    <td>${item.horaret}</td>
                    <td>${item.motivo}</td>
                    <td class="${voboClass[item.vobo] || ''}">${item.vobo}</td>
                    <td class="${estadoClass[item.estado] || ''}">${item.estado}</td>
                    <td>
                        ${item.estado.toLowerCase() !== 'rechazado'
                        ? `<button class="btn btn-info rounded-pill btn-detalle" data-index="${index}">Ver</button>`
                        : ''
                    }
                    </td>
                `;
                tbody.appendChild(row);
            });

            document.querySelectorAll('.btn-detalle').forEach(button => {
                button.addEventListener('click', (e) => {
                    const index = e.target.getAttribute('data-index');
                    const item = response.data.personal[index];
                    generarPDF(item);
                });
            });

        } catch (error) {
            console.error('Error al leer datos:', error);
        }
    });
});

function generarPDF(item) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Crear ambas imágenes
    const escudo = new Image();  // para la esquina izquierda (original)
    escudo.src = '/img/escudo-cbba.jpg';
    
    const logoComision = new Image(); // para la esquina derecha
    logoComision.src = '/img/cbba.jpg';

    let imagenesCargadas = 0;

    function verificarYGenerar() {
        imagenesCargadas++;
        if (imagenesCargadas === 2) {
            // Agregar escudo en la posición original (izquierda)
            doc.addImage(escudo, 'JPG', 15, 10, 15, 15);
            // Agregar logo de comisión en la otra esquina (derecha)
            doc.addImage(logoComision, 'JPG', 180, 10, 15, 15);

            // Formatear fechas
            const [yearSol, monthSol, daySol] = item.fechasol.split('-');
            const fechaSol = `${daySol}-${monthSol}-${yearSol}`;
            const [yearSal, monthSal, daySal] = item.fechasal.split('-');
            const fechaSal = `${daySal}-${monthSal}-${yearSal}`;
            const [yearRet, monthRet, dayRet] = item.fecharet.split('-');
            const fechaRet = `${dayRet}-${monthRet}-${yearRet}`;

            // Obtener datos personales
            const personal = item.personal || {};
            const primerNombre = personal.nombre || '';
            const apellidoPaterno = personal.apellidopat || '';
            const apellidoMaterno = personal.apellidomat || '';

            let nombreCompleto = primerNombre;
            if (apellidoPaterno) nombreCompleto += ` ${apellidoPaterno}`;
            if (apellidoMaterno) nombreCompleto += ` ${apellidoMaterno}`;
            if (!nombreCompleto.trim()) nombreCompleto = 'Sin nombre';

            // Encabezados
            doc.setFontSize(12);
            doc.text('Gobierno Autónomo Departamental de Cochabamba', 105, 18, { align: 'center' });
            doc.setFontSize(11);
            doc.text('FORMULARIO DE AUTORIZACIÓN DE SALIDA DE COMISIÓN', 105, 26, { align: 'center' });

            doc.setFontSize(9);
            let y = 35;

            // Fila 1: Nombre
            doc.rect(15, y, 180, 6);
            doc.text(`Nombre: ${nombreCompleto}`, 20, y + 4);
            y += 8;

            // Fila 2: Fecha de solicitud
            doc.rect(15, y, 180, 6);
            doc.text(`Fecha de solicitud: ${fechaSol}`, 20, y + 4);
            y += 8;

            // Fila 3: Salida
            doc.rect(15, y, 180, 6);
            doc.text(`Fecha de salida: ${fechaSal}`, 20, y + 4);
            doc.text(`Hora de salida: ${item.horasal}`, 120, y + 4);
            y += 8;

            // Fila 4: Retorno
            doc.rect(15, y, 180, 6);
            doc.text(`Fecha de retorno: ${fechaRet}`, 20, y + 4);
            doc.text(`Hora de retorno: ${item.horaret}`, 120, y + 4);
            y += 8;

            // Motivo
            const motivoAltura = 30;
            doc.rect(15, y, 180, motivoAltura);
            doc.text('MOTIVO:', 20, y + 5);
            doc.setFont('times', 'italic');
            const motivoText = doc.splitTextToSize(item.motivo, 170);
            doc.text(motivoText, 20, y + 11, { maxWidth: 170, lineHeightFactor: 1.1 });
            doc.setFont('helvetica', 'normal');
            y += motivoAltura + 5;

            // Firmas
            doc.rect(15, y, 85, 12);
            doc.text('Firma del Servidor', 40, y + 8);
            doc.rect(110, y, 85, 12);
            doc.text('Firma del Responsable', 135, y + 8);

            doc.save(`Comision_${item.id}.pdf`);
        }
    }

    // Manejar carga de ambas imágenes
    escudo.onload = verificarYGenerar;
    logoComision.onload = verificarYGenerar;
    
    // Opcional: manejar errores para no quedarse colgado
    escudo.onerror = () => { console.error('Error cargando escudo-cbba.jpg'); verificarYGenerar(); };
    logoComision.onerror = () => { console.error('Error cargando cbba.jpg'); verificarYGenerar(); };
}




// **********************************************************************************
// funcion para mostrar lista de salida medica ******************************
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('listSalud').addEventListener('click', async () => {
        try {

            const response = await axios.post(
                '/list-salud', {
                idserv: idserv
            });

            console.log('Respuesta del servidor:', response.data);
            const tbody = document.querySelector('#idsal tbody');
            const estadoClass = {
                espera: 'text-warning',
                validado: 'text-success',
                rechazado: 'text-danger'
            };
            const voboClass = {
                pendiente: 'text-warning',
                aprobado: 'text-success',
                rechazado: 'text-danger'
            };
            tbody.innerHTML = '';
            // Recorremos el array y agregamos filas a la tabla
            response.data.personal.forEach((item, index) => {
                const [yearSol, monthSol, daySol] = item.fechasol.split('-');
                const fechaSol = `${daySol}-${monthSol}-${yearSol}`;

                const [yearSal, monthSal, daySal] = item.fechasal.split('-');
                const fechaSal = `${daySal}-${monthSal}-${yearSal}`;

                const [yearRet, monthRet, dayRet] = item.fecharet.split('-');
                const fechaRet = `${dayRet}-${monthRet}-${yearRet}`;

                const row = document.createElement('tr');

                row.innerHTML = `
            <td>${index + 1}</td>
            <td>${fechaSol}</td>
            <td>${fechaSal}</td>
             <td>${item.horasal}</td>
            <td>${fechaRet}</td>
            <td>${item.horaret}</td>
            <td class="${voboClass[item.vobo] || ''}">${item.vobo}</td>
            <td class="${estadoClass[item.estado] || ''}">${item.estado}</td>`;
                tbody.appendChild(row);
            });

        } catch (error) {
            console.error('Error al leer datos:', error);

        }



    });
});

// ************************** listar salida particular del personal *************************
// funcion para mostrar lista de salida particular ******************************
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('listParticular').addEventListener('click', async () => {
        try {
            const response = await axios.post('/list-particular', { idserv: idserv });
            console.log('Respuesta del servidor:', response.data);

            const tbody = document.querySelector('#idsalpar tbody');
            const estadoClass = {
                espera: 'text-warning',
                validado: 'text-success',
                rechazado: 'text-danger'
            };
            const voboClass = {
                pendiente: 'text-warning',
                aprobado: 'text-success',
                rechazado: 'text-danger'
            };
            tbody.innerHTML = '';

            response.data.personal.forEach((item, index) => {
                console.log('Fila procesada:', item); // <-- Ver cada registro
                const [yearSol, monthSol, daySol] = item.fechasol.split('-');
                const fechaSol = `${daySol}-${monthSol}-${yearSol}`;

                const [yearSal, monthSal, daySal] = item.fechasal.split('-');
                const fechaSal = `${daySal}-${monthSal}-${yearSal}`;

                const [yearRet, monthRet, dayRet] = item.fecharet.split('-');
                const fechaRet = `${dayRet}-${monthRet}-${yearRet}`;

                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.tiposalida.descripcion}</td>
                    <td>${fechaSol}</td>
                    <td>${fechaSal}</td>
                    <td>${item.horasal}</td>
                    <td>${fechaRet}</td>
                    <td>${item.horaret}</td>
                    <td>${item.cantidad}</td>
                    <td class="${voboClass[item.vobo] || ''}">${item.vobo}</td>
                    <td class="${estadoClass[item.estado] || ''}">${item.estado}</td>
                `;
                tbody.appendChild(row);
            });

        } catch (error) {
            console.error('Error al leer datos:', error);
        }
    });
});

// ********************************************************************************************
// funcion para aprobar el visto bueno de las salidas solicitadas *****************************

document.addEventListener('DOMContentLoaded', () => {
    const listSolButton = document.getElementById('listSol');
    const aprobarTodoButton = document.getElementById('aprobarTodo');

    listSolButton.addEventListener('click', async () => {
        try {
            const response = await axios.get('/listar-solicitudes/usuario', { idserv: idserv });
            
            // ---- Opción 2: Crear contenedor y tabla si no existen ----
            let container = document.getElementById('IdListSol');
            if (!container) {
                container = document.createElement('div');
                container.id = 'IdListSol';
                container.className = 'table-responsive mt-4';
                // Lo insertamos donde quieras, por ejemplo después del banner de bienvenida
                const banner = document.querySelector('.welcome-banner');
                if (banner) {
                    banner.parentNode.insertBefore(container, banner.nextSibling);
                } else {
                    document.querySelector('.main-content').appendChild(container);
                }
            }

            let table = container.querySelector('table');
            if (!table) {
                table = document.createElement('table');
                table.className = 'table table-modern';
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha Solicitud</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Fecha Salida</th>
                            <th>Hora Salida</th>
                            <th>Fecha Retorno</th>
                            <th>Hora Retorno</th>
                            <th>Motivo</th>
                            <th>Cantidad</th>
                            <th>Visto Bueno</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                `;
                container.appendChild(table);
            }

            const tbody = table.querySelector('tbody');
            // ---- Fin de creación dinámica ----

            const voboClass = {
                pendiente: 'text-warning',
                aprobado: 'text-success',
                rechazado: 'text-danger'
            };
            tbody.innerHTML = '';

            response.data.salidas.forEach((item, index) => {
                // ... (todo igual que antes)
                const [yearSol, monthSol, daySol] = item.fechasol.split('-');
                const fechaSol = `${daySol}-${monthSol}-${yearSol}`;

                const [yearSal, monthSal, daySal] = item.fechasal.split('-');
                const fechaSal = `${daySal}-${monthSal}-${yearSal}`;

                const [yearRet, monthRet, dayRet] = item.fecharet.split('-');
                const fechaRet = `${dayRet}-${monthRet}-${yearRet}`;

                const row = document.createElement('tr');

                let voboContent = `<span class="${voboClass[item.vobo] || ''}">${item.vobo}</span>`;
                if (item.vobo === 'pendiente') {
                    voboContent = `
                    <div class="d-flex gap-1">
                        <button class="btn btn-warning btn-aprobar btn-sm" data-id="${item.id}">
                            Aprobar
                        </button>
                        <button class="btn btn-danger btn-rechazar btn-sm" data-id="${item.id}">
                            Rechazar
                        </button>
                    </div>
                    `;
                }

                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${fechaSol}</td>
                    <td>${item.nombre}</td>
                    <td>${item.descripcion}</td>
                    <td>${fechaSal}</td>
                    <td>${item.horasal}</td>
                    <td>${fechaRet}</td>
                    <td>${item.horaret}</td>
                    <td>${item.motivo}</td>
                    <td>${item.cantidad}</td>
                    <td>${voboContent}</td>`;
                tbody.appendChild(row);
            });

            // Escuchar clic en los botones individuales (igual)
            tbody.addEventListener('click', async (event) => {
                if (event.target.classList.contains('btn-aprobar')) {
                    const id = event.target.getAttribute('data-id');
                    try {
                        await axios.post('/aprobar-solicitud', { id: id });
                        showToast('Solicitud aprobada');
                        setTimeout(() => window.location.href = '/homeusr', 1100);
                    } catch (error) {
                        console.error('Error al aprobar solicitud:', error);
                        alert('Error al aprobar la solicitud');
                    }
                }

                if (event.target.classList.contains('btn-rechazar')) {
                    const id = event.target.getAttribute('data-id');
                    try {
                        await axios.post('/rechazar-solicitud', { id: id });
                        showToast('Solicitud rechazada');
                        setTimeout(() => window.location.href = '/homeusr', 1100);
                    } catch (error) {
                        console.error('Error al rechazar solicitud:', error);
                        alert('Error al rechazar la solicitud');
                    }
                }
            });

        } catch (error) {
            console.error('Error al leer datos:', error);
        }
    });

    // Escuchar clic en el botón "Aprobar Todo"
    aprobarTodoButton.addEventListener('click', async () => {
        const botonesAprobar = document.querySelectorAll('.btn-aprobar');
        if (botonesAprobar.length === 0) {
            Swal.fire('No hay solicitudes pendientes por aprobar');
            return;
        }

        const confirmacion = await Swal.fire({
            title: '¿Aprobar todas las solicitudes?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, aprobar todas',
            cancelButtonText: 'Cancelar'
        });

        if (confirmacion.isConfirmed) {
            try {
                for (let boton of botonesAprobar) {
                    const id = boton.getAttribute('data-id');
                    await axios.post('/aprobar-solicitud', { id: id });
                }
                Swal.fire('¡Listo!', 'Todas las solicitudes fueron aprobadas', 'success');
                setTimeout(() => window.location.href = '/homeusr', 1100);
            } catch (error) {
                console.error('Error al aprobar todas las solicitudes:', error);
                Swal.fire('Error', 'Ocurrió un error al aprobar todas las solicitudes', 'error');
            }
        }
    });

    // Función para mostrar el Toast
    function showToast(message) {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({ icon: "success", title: message });
    }
});


// ******************************************************************************************
