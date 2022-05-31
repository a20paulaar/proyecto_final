/**
 * Carga todas las paradas de la línea
 */
function cargarParadasEnTabla(){
    $.post("../functions/bd.php", {
        method: "loadStops"
    },
    function(data, status){
        var json = JSON.parse(data);
        for(var p of json.paradas){
            $('#ida tr:last').after('<tr class="stop_' + p.id + '"><th>' + p.name + '</th></tr>');
            $('#vuelta tr:first').before('<tr class="stop_' + p.id + '"><th>' + p.name + '</th></tr>');
        }
    });
}

function cargarParadasEnLista(){
    $.post("../functions/bd.php", {
        method: "loadStops"
    },
    function(data, status){
        var json = JSON.parse(data);
        for(var p of json.paradas){
            $('#paradas').append(new Option(p.name, p.id));
        }
    });
}

/**
 * Carga todos los horarios (de ida y de vuelta)
 */
function cargarHorarios(){
    cargarParadasEnTabla();

    $.post("../functions/bd.php", {
        method: "loadSchedule"
    },
    function(data, status){
        console.log(data);
        var json = JSON.parse(data);
        var paradas = json.paradas;
        for(var i in paradas){
            //Ida o Vuelta
            var startHour = paradas[i][0].hour.split(":");
            var startDate = new Date();
            startDate.setHours(startHour[0],startHour[1],startHour[2]);

            var endHour = paradas[i][(paradas[i]).length-1].hour.split(":");
            var endDate = new Date();
            endDate.setHours(endHour[0],endHour[1],endHour[2]);

            var pointer = "";
            if(startDate < endDate){ //Ida
                pointer = '#ida';
            }
            else{ //Vuelta
                pointer = '#vuelta';
            }

            paradas[i].forEach(parada => {
                $(pointer + ' .stop_' + parada.id).append('<td>' + parada.hour.substring(0, parada.hour.length - 3) + '</td>'); //Quitar segundos
            });

        }
    });
}

$('#paradas').change(function(e) {
    console.log(e, $(this).val());
    $.post("../functions/bd.php", {
        method: "loadNextBus",
        stop: $(this).val()
    },
    function(data, status){
        var json = JSON.parse(data);
        console.log(new Date(Date.now()), json.desc ? json.desc.hora : "");
        $('#asc').text(json.asc ? json.asc.hora + " (" + intervalo(json.asc.hora) + ")" : "No hay próximas expediciones");
        $('#desc').text(json.desc ? json.desc.hora + " (" + intervalo(json.desc.hora) + ")" : "No hay próximas expediciones");
    });
});

function intervalo(hora){
    var horaFrag = hora.split(":");
    var segundosHora = parseInt(horaFrag[2]) + parseInt(horaFrag[1])*60 + parseInt(horaFrag[0])*3600;
    var ahora = new Date();
    var segundosAhora = ahora.getSeconds() + ahora.getMinutes()*60 + ahora.getHours()*3600;
    
    var intervalo = segundosHora - segundosAhora;
    console.log(intervalo);
    var ret = (parseInt(intervalo/3600) > 0 ? parseInt(intervalo/3600) + "h" : "") + (parseInt((intervalo%3600)/60) > 0 ? parseInt((intervalo%3600)/60) + "min" : "");
    return (ret ?? "< 1min");
}