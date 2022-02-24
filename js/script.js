cargarUltimasNoticias();

function mobileMenu() {
    var x = document.getElementById("links");
    if (x.style.display === "flex") {
      x.style.display = "none";
    } else {
      x.style.display = "flex";
    }
}

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
                '<h4><a class="menu_element" href="'+call+'pages/noticia.php#art-'+n.id+'">'+n.title+'</a></h4>'+
                '<p>'+text_sample+'</p>'+
                '</article>'
            );
        }
    });
}

function validar(){
    let msg = "";
    let result = true;
    let matchName = /^[\u0041-\u005A\u0061-\u007A\u00C0-\u017E \-]+$/;
    if(!matchName.test($('#nombre').val()) || !matchName.test($('#apellido1').val()) || !matchName.test($('#apellido2').val())){
        msg += "El nombre/apellidos contiene caracteres invalidos\n";
        result = false;
    }

    //

    if(!result){
        alert(msg);
    }

    return result;
}