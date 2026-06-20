
function compararFecha() {
    // domingo=0, lunes=1, martes=2 ........ sabado=6
    var fsalida = document.getElementById("fsalida").value;
    var fretorno = document.getElementById("fretorno").value;
    var timeStart = new Date(fsalida);
    var timeEnd = new Date(fretorno);
    var findSemana = 0;
    var combo = document.getElementById("salida");
    var tipoSalida = combo.options[combo.selectedIndex].text;
    if (tipoSalida == "VACACION") {

        if (timeEnd >= timeStart) {
            var diff = timeEnd.getTime() - timeStart.getTime();
            var resultado = Math.round(diff / (1000 * 60 * 60 * 24));
            resultado=resultado+1;
            if (document.getElementById("mdia").checked == true) {
                resultado = resultado - 0.5;
            }

            while (timeEnd.getTime() >= timeStart.getTime()) {
                timeStart.setDate(timeStart.getDate() + 1);
                if (timeStart.getDay() == 0)
                    findSemana = findSemana + 1;
                if (timeStart.getDay() == 6)
                    findSemana = findSemana + 1;
            }
            document.getElementById("dias").value = resultado - findSemana;
        }

        else if (timeEnd != null || timeEnd <= timeStart) {
            document.getElementById("dias").value = 0;
        }
        document.getElementById("totaldias").value='';
    }

}
  // ******************* funciones para desabilitar fechas pasadas *****************************

  function fechaInicio() {
    let hoy = new Date();
    let dd = hoy.getDate();
    let mm = hoy.getMonth() + 1;
    let yyyy = hoy.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }

    hoy = yyyy + '-' + mm + '-' + dd;
    let maxmum = "2100-01-01";
    let fecha = document.getElementById("fsalida");
    fecha.min = hoy;
    fecha.max = maxmum;
}
function fechaFin() {
    let hoy = new Date();
    let dd = hoy.getDate();
    let mm = hoy.getMonth() + 1;
    let yyyy = hoy.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    hoy = yyyy + '-' + mm + '-' + dd;
    let maxmum = "2100-01-01";
    let fecha = document.getElementById("fretorno");
    fecha.min = hoy;
    fecha.max = maxmum;
}
function fechaActual() {
    let hoy = new Date();
    let dd = hoy.getDate();
    let mm = hoy.getMonth() + 1;
    let yyyy = hoy.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    hoy = yyyy + '-' + mm + '-' + dd;
    let maxmum = "2100-01-01";
    let fecha = document.getElementById("fechasol");
    fecha.min = hoy;
    fecha.max = maxmum;
}

// **********************************************************************************


