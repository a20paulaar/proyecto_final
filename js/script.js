var today = todayToInput();
var max = todayToInput(90); //Se pueden pedir billetes hasta dentro de 90 días

document.getElementById('ida').value = today;
document.getElementById('vuelta').value = today;

document.getElementById('ida').setAttribute("min", today);
document.getElementById('vuelta').setAttribute("min", today);

document.getElementById('ida').setAttribute("max", max);
document.getElementById('vuelta').setAttribute("max", max);

document.getElementById('i').onclick = function(){
    document.getElementById('formarea_fechavuelta').style.display = "none";
}
document.getElementById('iv').onclick = function(){
    document.getElementById('formarea_fechavuelta').style.display = "block";
}

provisional_rellenarSelects();

/**
 * Formatea la fecha para los input date
 * @param {*} dayOffset Diferencia de días con respecto a hoy
 */
function todayToInput(dayOffset = 0) {
    var date = new Date();
    date.setDate(date.getDate() + dayOffset);
    return date.toJSON().slice(0,10);
};

//Para rellenar los select sin base de datos ahora mismo; esto no vale al hacer la web
function provisional_rellenarSelects(){
    var paradas = [
        "Vigo-Estación Autobuses",
        "Vigo-Hospital Meixoeiro",
        "Vigo-Aeroporto",
        "O Porriño",
        "Tui",
        "Valença do Minho",
        "Viana do Castelo",
        "Póvoa de Varzim",
        "Oporto-Aeroporto",
        "Oporto-Casa da Música"
    ];

    ["origen", "destino"].forEach(elemento =>{
        var sel = document.getElementById(elemento);
        paradas.forEach(parada => {
            var opt = document.createElement('option');
            opt.innerHTML = parada;
            opt.value = parada;
            sel.appendChild(opt);
        });
    });
}

function mobileMenu() {
    var x = document.getElementById("links");
    if (x.style.display === "flex") {
      x.style.display = "none";
    } else {
      x.style.display = "flex";
    }
  }