cargarHorarios();

/**
 * Carga todas las paradas de la línea
 */
function cargarParadas(){
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

/**
 * Carga todos los horarios (de ida y de vuelta)
 */
function cargarHorarios(){
    cargarParadas();

    $.post("../functions/bd.php", {
        method: "loadSchedule"
    },
    function(data, status){
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