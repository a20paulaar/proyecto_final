cargarReservas();
cargarUsuarios();
cargarTarifas();

function cargarReservas(){
    $.post("../functions/bd.php", {
        method: "loadPendingReservations"
    },
    function(data, status){
        var listaReservas = JSON.parse(data);
        for(var reserva of listaReservas){
            $('#lista_reservas').append(
                '<tr>'+
                '<td>'+reserva.email+'</td>'+
                '<td>'+reserva.dni+'</td>'+
                '<td>'+reserva.date+'</td>'+
                '<td>'+reserva.start_stop_name+' ('+ reserva.start_time +')'+'</td>'+
                '<td>'+reserva.end_stop_name+' ('+ reserva.end_time +')'+'</td>'+
                '<td>'+reserva.seat+'</td>'+
                '<td>'+
                    '<form method="post" action="../functions/admin.php">'+
                        '<input type="hidden" name="values" value="'+reserva.dni+'_'+reserva.date+'_'+reserva.exped+'_'+reserva.seat+'" />'+
                        '<input type="hidden" name="email" value="'+reserva.email+'" />'+
                        '<input type="submit" name="reservas_OK" value="Validar" />'+
                        '<input type="submit" name="reservas_NOK" value="Denegar" />'+
                    '</form>'+
                '</td>'+
                '</tr>'
            );
        }
    });
}

function cargarUsuarios(){
    $.post("../functions/bd.php", {
        method: "loadUsers"
    },
    function(data, status){
        var listaUsuarios = JSON.parse(data);
        console.log(listaUsuarios);
        var col = 0;
        for(var usuario of listaUsuarios){
            $('#lista_usuarios').append(
                '<div class="col-md-4 col-xs-12" style="background-color: #'+(col%2==0 ? 'FFF': 'AAA' )+'">'+
                    '<form method="post" action="../functions/admin.php">'+
                        '<input type="text" name="mail" value="'+usuario.email+'" readonly><br/>'+
                        '<input type="radio" id="'+col+'_1" name="profile" value="1"' + 
                            (usuario.profile == 1 ? 'checked' : '' ) + '>' +
                        '<label for="'+col+'_1">Administrador</label><br/>'+
                        '<input type="radio" id="'+col+'_2" name="profile" value="2"' + 
                            (usuario.profile == 2 ? 'checked' : '' ) + '>' +
                        '<label for="'+col+'_2">Usuario</label><br/>'+
                        '<input type="submit" name="usuarios" value="Cambiar" />'+
                    '</form>'+
                '</div>'
            );
            
            col++;
        }
    });
}

function cargarTarifas(){
    $.post("../functions/bd.php", {
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
                        '<input type="number" step="any" lang="en" class="admin_price" value="'+tarifa.price+'" name="price" />'+
                        '<input type="hidden" name="between" value="'+ tarifa.between[0]+'_'+tarifa.between[1]+'" />'+
                        '<input type="submit" name="tarifas" value="Modificar" />'+
                    '</form>'+
                '</td>'+
                '</tr>'
            );

            $('.admin_price').css("width", "5em");
        }
    });
}
