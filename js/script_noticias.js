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