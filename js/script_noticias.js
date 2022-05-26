/**
 * Carga una noticia en concreto pasándole su id
 * @param {*} id Id de la noticia a cargar
 */
function cargarNoticia(id){
    $.post("../functions/bd.php", {
        method: "loadNew",
        id: id
    },
    function(data, status){
        var n = JSON.parse(data)[0];
        console.log(n);
        $('section>h2').append(n.title);
        if(n.has_pic) $('#noticia_img').append('<img src="../images/news/'+id+'.png" class="news_pic" />');
        $('#noticia_txt').append(n.text);
    });
}

function cargarNoticias(p){
    let call = (window.location.pathname.indexOf('index.php')!=-1 ? "":"../");
    if(p == null) p = 1;
    $.post(call + "functions/bd.php", {
        method: "loadNews",
        range: 10,
        p: p
    },
    function(data, status){
        console.log(data);
        var noticias = JSON.parse(data);
        for(var n of noticias){
            let text_sample = n.text.substring(0,100) + ". . .";
            $('#noticias').append("<div id='noticia_" + n.id + "'></div>");
            $('#noticia_' + n.id).append(
                '<article>'+
                (parseInt(n.has_pic) ? '<img src="'+call+'images/news/'+n.id+'.png" alt="'+n.title+'" class="news_minipic">': '')+
                '<h4><a class="menu_element" href="'+call+'pages/noticia.php?art='+n.id+'">'+n.title+'</a></h4>'+
                '<p>'+text_sample+'</p>'+
                '</article>'
            );
        }
    });

    $.post(call + "functions/bd.php", {
        method: "loadHowManyNews"
    },
    function(data, status){
        var pages = Math.ceil(data/10);
        for(var i=1;i<=pages;i++){
            $('#paginas').append("<span><a href='noticias.php?p="+ i +"'>" + i + "</a></span>");
        }
    });
}