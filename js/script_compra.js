function gestionarDatosCompra(json_ida, json_vuelta = null){
    console.log(json_ida, json_vuelta);

    for(exp in json_ida){
        console.log(json_ida[exp]);

        if(exp != 'date'){
            let value = json_ida.date + "_" + exp + "_" + json_ida[exp][0].stop + "_" + json_ida[exp][1].stop;
            $("#ida table").append(
                '<tr>' +
                    '<td>SALIDA: ' + json_ida[exp][0].time + ' / LLEGADA: ' + json_ida[exp][1].time + '</td>' +
                    '<td><input type="radio" name="h_ida" value="' + value + '" />' +
                '</tr>'
            );
        }
    }

    $("#ida").append(
        '<input type="hidden" name="f_ida" value="' + json_ida.date + '" />'
    )

    if(json_vuelta != null){
        for(exp in json_vuelta){
            console.log(json_vuelta[exp]);
    
            if(exp != 'date'){
                let value = json_vuelta.date + "_" + exp + "_" + json_vuelta[exp][0].stop + "_" + json_vuelta[exp][1].stop;
                $("#vuelta table").append(
                    '<tr>' +
                        '<td>SALIDA: ' + json_vuelta[exp][0].time + ' / LLEGADA: ' + json_vuelta[exp][1].time + '</td>' +
                        '<td><input type="radio" name="h_vuelta" value="' + value + '" />' +
                    '</tr>'
                );
            }
        }
    
        $("#vuelta").append(
            '<input type="hidden" name="f_vuelta" value="' + json_vuelta.date + '" />'
        )
    }
    else{
        $('#vuelta').remove();
    }
}

['ida', 'vuelta'].forEach(element => {
    $('#t_'+element).delegate('input:radio', 'change', function(e){
        $('#a_'+element).find('option').remove();
        $('#a_'+element).append('<option value="">Seleccione asiento</option>');
        for(var i=1; i<=50; i++) $('#a_'+element).append('<option id="a_'+element+'_'+i+'" value="'+i+'">'+i+'</option>'); //50 asientos por bus
    
        let value = $(this).val().split("_");
        
        $.post("../functions/bd.php", {
            method: "loadOccupiedSeats",
            date: value[0],
            exp: value[1],
            id_origin: value[2],
            id_destination: value[3]
        },
        function(data, status){
            var json = JSON.parse(data);
            console.log(json);
            for(var a of json){
                $('#a_'+element+'_'+a).remove();
            }
        });
    });
});

function validarCompra(){
    let msg = "";
    let result = true;
    
    if($('#a_ida').val()=="" || !$("input[name='h_ida']:checked").val()
    || ($('#vuelta').length && ($('#a_vuelta').val()=="" || !$("input[name='h_vuelta']:checked").val()))){
        msg += "Debes completar todos los campos.\n";
        result = false;
    }

    if(!result){
        alert(msg);
    }

    return result;
}