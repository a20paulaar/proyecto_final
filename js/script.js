cargarUltimasNoticias();

// Si existe un sessionStorage de nombre, pone el mensaje en el elemento de id username
if(sessionStorage.getItem('nombre') !== null){
    $('#username').text("Estás conectado como: " + sessionStorage.getItem('nombre'));
}

/**
 * Cambia la visibilidad del menú hamburguesa según sea móvil o no
 */
function mobileMenu() {
    var x = document.getElementById("links");
    if (x.style.display === "flex") {
      x.style.display = "none";
    } else {
      x.style.display = "flex";
    }
}

/**
 * Carga las últimas noticias para mostrarlas en el lateral
 */
function cargarUltimasNoticias(){
    let call = (window.location.pathname.indexOf('index.php')!=-1 ? "":"../");
    $.post(call + "functions/bd.php", {
        method: "loadNews",
        range: 3
    },
    function(data, status){
        var noticias = JSON.parse(data);
        for(var n of noticias){
            let text_sample = n.text.substring(0,50) + ". . .";
            $('aside div').append(
                '<article>'+
                (parseInt(n.has_pic) ? '<img src="'+call+'images/news/'+n.id+'.png" alt="'+n.title+'" class="news_minipic">': '')+
                '<h4><a class="menu_element" href="'+call+'pages/noticia.php?art='+n.id+'">'+n.title+'</a></h4>'+
                '<p>'+text_sample+'</p>'+
                '</article>'
            );
        }
    });
}

/**
 * Valida los campos del formulario de registro antes de introducirlos en la BD
 * @returns Mensaje de error (si lo hubiera)
 */
function validarRegistro(){
    let msg = "";
    let result = true;
    let matchName = /^[\u0041-\u005A\u0061-\u007A\u00C0-\u017E \-]+$/;
    let matchEmail = /^[\w]+@([\w-]+\.)+[\w-]{2,4}$/g;
    
    if(!matchName.test($('#nombre').val()) || !matchName.test($('#apellidos').val())){
        msg += "El nombre/apellidos contiene caracteres invalidos\n";
        result = false;
    }
    if($('#pass').val()!=$('#pass2').val()){
        msg += "Las contraseñas no coinciden\n";
        result = false;
    }
    if($('#pass').val()==$('#pass2').val() && $('#pass').val().length<6){
        msg += "La contraseña debe contener al menos 6 caracteres\n";
        result = false;
    }
    if(!matchEmail.test($('#email').val())){
        msg += "Debe introducir una dirección de correo electrónico válida.\n";
        result = false;
    }

    if(!result){
        alert(msg);
    } else {
        alert("Los datos han sido introducidos correctamente.");
    }

    return result;
}