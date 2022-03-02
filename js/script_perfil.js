cargarInformacionPerfil();

function cargarInformacionPerfil(){
    $.post("../functions/bd.php", {
        method: "loadProfileInfo"
    },
    function(data, status){
        var json = JSON.parse(data);
        for(let dato in json[0]){
            if(['nombre', 'apellidos', 'dni', 'fecha_nacimiento'].includes(dato)){
                $('#' + dato).after('<input type="text" class="readonly form-control-plaintext" value="'+ json[0][dato] + '" readonly />');
            } else {
                $('#' + dato).after('<input type="text" class="form-control-plaintext" value="'+ json[0][dato] + '" readonly />');
            }   
        }
    });
}


$('#modificar').click(function(){
    $('input:not(.readonly)').prop('readonly', false);
    $('input:not(.readonly)').removeClass('form-control-plaintext');
    $('input:not(.readonly)').addClass('form-control');
});
