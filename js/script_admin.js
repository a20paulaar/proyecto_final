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
        for(var tarifa of listaTarifas){
            $('#lista_tarifas').append(
                '<tr>'+
                '<td>'+tarifa.between_n[0]+'</td>'+
                '<td>'+tarifa.between_n[1]+'</td>'+
                '<td>'+
                    '<form method="post" action="../functions/admin.php">'+
                        '<input type="number" step="any" lang="en" class="admin_price" value="'+tarifa.price+'" name="price" /><br/>'+
                        '<input type="hidden" name="between" value="' + tarifa.between[0] + '_' +tarifa.between[1]+'" />'+
                        '<input type="submit" name="tarifas" value="Modificar" />'+
                    '</form>'+
                '</td>'+
                '</tr>'
            );

            $('.admin_price').css("width", "6em");
        }
    });
}