$(function(){
    if(!window.location.hash) { //Se necesita una primera recarga para completar el seteo de session storage
        window.location = window.location + '#i';
        window.location.reload();
    }
});

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

cargarCookies();

/**
 * Formatea la fecha para los input date
 * @param {*} dayOffset Diferencia de días con respecto a hoy
 */
function todayToInput(dayOffset = 0) {
    var date = new Date();
    date.setDate(date.getDate() + dayOffset);
    return date.toJSON().slice(0,10);
};

/**
 * En caso de haber cookies para recuperar el último viaje buscado, se recupera
 */
async function cargarCookies(){
    await cargarParadas();
    var cookie_traveldata_i = (document.cookie.split('; ').filter(c => c.startsWith('traveldata_i')))[0];
    var cookie_traveldata_v = (document.cookie.split('; ').filter(c => c.startsWith('traveldata_v')))[0];

    if(cookie_traveldata_i != null){
        var json_traveldata_i = JSON.parse(decodeURIComponent(cookie_traveldata_i).replace(/traveldata_i=/g,''));
        console.log(json_traveldata_i);

        $('#ida').val(json_traveldata_i.date);
        if(cookie_traveldata_v == null){
            $('#i').prop('checked', true);
            $('#formarea_fechavuelta').css('display', 'none');
        }
        else{
            var json_traveldata_v = JSON.parse(decodeURIComponent(cookie_traveldata_v).replace(/traveldata_v=/g,''));
            $('#vuelta').val(json_traveldata_v.date);
        }
        //De traveldata_i, primera posición, recoger stop en 0 y stop en 1 y poner origen y destino
        var key = Object.keys(json_traveldata_i)[0];
        console.log(json_traveldata_i[key][0].stop);
        $('#origen').val(json_traveldata_i[key][0].stop).change();
        $('#destino').val(json_traveldata_i[key][1].stop).change();
        //De traveldata_i, clave people, poner adultos, ancianos y jóvenes
        $('#anc').val(json_traveldata_i.people.anc);
        $('#jov').val(json_traveldata_i.people.jov);
        $('#adu').val(json_traveldata_i.people.adu);
    }
}

/**
 * Carga las paradas como options en el select
 */
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
    return new Promise(resolve => {
        setTimeout(() => {
            resolve(1);
        }, 500);
      });
}

/**
 * Comprueba que todos los campos del formulario de compra sean correctos
 * @returns Mensaje de error (si lo hubiera)
 */
function validarCompra(){
    let msg = "";
    let result = true;
    
    if($('#origen').val()==$('#destino').val()){
        msg += "El origen y el destino no pueden ser el mismo.\n";
        result = false;
    }
    if($('#vuelta').val()<$('#ida').val()){
        msg += "La fecha de regreso no puede ser anterior a la fecha de salida.\n";
        result = false;
    }
    if($('#anc').val()==0 && $('#jov').val()==0 && $('#adu').val()==0){
        msg += "Tiene que haber al menos un viajero.\n";
        result = false;
    }

    if(!result){
        alert(msg);
    }

    return result;
}