cargarReservas();
cargarTarifas();

function cargarReservas(){
    $.post("../functions/bd.php",
    {
        method: "loadPendingReservations"
    },
    function(data, status){
        var json = JSON.parse(data);
        console.log(json);
        /*for(var p of json.paradas){
            $('#ida tr:last').after('<tr class="stop_' + p.id + '"><th>' + p.name + '</th></tr>');
            $('#vuelta tr:first').before('<tr class="stop_' + p.id + '"><th>' + p.name + '</th></tr>');
        }*/
    });
}

function cargarTarifas(){
    $.post("../functions/bd.php",
    {
        method: "loadFares"
    },
    function(data, status){
        var listaTarifas = JSON.parse(data);
        console.log(listaTarifas);
    });
}