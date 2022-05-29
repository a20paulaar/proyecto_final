cargarInformacionPerfil();
cargarRegistroModificaciones();

/**
 * Carga la información del perfil del usuario que tiene la sesión iniciada
 */
function cargarInformacionPerfil(){
   $.post("../functions/bd.php", {
        method: "loadProfileInfo"
    },
    function(data, status){
        var json = JSON.parse(data);
        for(let dato in json){
            if(['nombre', 'apellidos', 'dni', 'fecha_nacimiento', 'email'].includes(dato)){
                $('#' + dato).after('<input type="text" class="readonly form-control-plaintext" value="'+ json[dato] + '" readonly />');
            } else {
                $('#' + dato).after('<input type="text" name="' + dato + '" class="form-control-plaintext" value="'+ json[dato] + '" readonly />');
            }   
        }
    });
    $.post("../functions/perfil.php", {
        method: "loadUserPic"
    }, 
    function(data, status){
        console.log(data);
        if(data){
            $('#profile_img').css('background-image', 'url("../images/' + data + '/user.png")');
        }
    });
}


$('#modificar').click(function(){
    $('input:not(.readonly)').prop('readonly', false);
    $('input:not(.readonly)').removeClass('form-control-plaintext');
    $('input:not(.readonly)').addClass('form-control');
    $('#modificar').css('display', 'none');
    $('#aceptar').css('display', 'block');
    $('#cancelar').css('display', 'block');
    $('#subir_img').css('display', 'block');
});

$('#cancelar').click(function(){
    location.reload();
});

function cargarRegistroModificaciones(){
    $.post("../functions/bd.php", {
        method: "loadLog",
        type: 2
    },
    function(data, status){
        var json = JSON.parse(data);
        for(let dato of json){
            $('#profile_table>tbody').append("<tr><td>" + dato.fecha + "</td></tr>");  
        }
    });
}

function cargarRegistroPuntos(){
    $.post("../functions/bd.php", {
        method: "loadLog",
        type: 3
    },
    function(data, status){
        var json = JSON.parse(data);
        for(let dato of json){
            let puntos = Math.abs(dato.puntos);
            let evento = "";
            if(dato.puntos<0){
                evento = "canjeado";
            } else {
                evento = "obtenido";
            } 
            $('#profile_table>tbody').append("<tr><td>" + dato.fecha + "</td><td>Ha " + evento + " " + puntos + " puntos</td></tr>"); 

        }
    });
}