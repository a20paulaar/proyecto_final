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

cargarParadas();

/**
 * Formatea la fecha para los input date
 * @param {*} dayOffset Diferencia de días con respecto a hoy
 */
function todayToInput(dayOffset = 0) {
    var date = new Date();
    date.setDate(date.getDate() + dayOffset);
    return date.toJSON().slice(0,10);
};

function cargarParadas(){
    $.post("functions/bd.php", {
        method: "loadStops"
    },
    function(data, status){
        var json = JSON.parse(data);
        for(var p of json.paradas){
            $('#origen').append(new Option(p.name, p.id));
            $('#destino').append(new Option(p.name, p.id));
        }
    });
}

function validarCompra(){
    let msg = "";
    let result = true;
    
    if($('#origen').val()==$('#destino').val()){
        msg += "El origen y el destino no pueden ser el mismo.\\n";
        result = false;
    }
    if($('#vuelta').val()<$('#ida').val()){
        msg += "La fecha de regreso no puede ser anterior a la fecha de salida.\\n";
        result = false;
    }
    if($('#anc').val==0 && $('#jov').val==0 && $('#adu').val==0){
        msg += "Tiene que haber al menos un viajero.\\n";
        result = false;
    }

    if(!result){
        alert(msg);
    }

    return result;
}