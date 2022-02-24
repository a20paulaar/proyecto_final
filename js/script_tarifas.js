var maxStop = 0; //Para el bucle de rellenar tarifas hay que saber el índice del último elemento

function cargarParadas(){
    $.post("../functions/bd.php", {
        method: "loadStops"
    },
    function(data, status){
        var json = JSON.parse(data);
        for(var p of json.paradas){
            $('#simulador').append(
                $('<div>').prop({
                    id: 'radio_parada_' + p.id,
                    class: 'col-2'
                })
                .append(
                    $('<input>').prop({
                        type: 'radio',
                        id: 'parada_' + p.id,
                        name: 'simulador',
                        value: p.id
                    })
                )
            )
            .append(
                $('<div>').prop({
                    id: 'label_parada_' + p.id,
                    class: 'col-6'
                })
                .append(
                    $('<label>').prop({
                        for: 'parada_' + p.id,
                    }).html(p.name)
                )
            )
            .append(
                $('<div>').prop({
                    id: 'price_parada_' + p.id,
                    class: 'col-4'
                })
            )

            $('#price_parada_' + p.id).attr("data-stop", p.id);

            maxStop = p.id;
        }

        $("input[name='simulador']").on("change", calcular);

    });
}

function calcular(){
    var origen = $(this).val();

    $.post("../functions/bd.php", {
        method: "loadFares"
    },
    function(data, status){
        var listaTarifas = JSON.parse(data);

        /*
        Algoritmo para mostrar precio en la columna de la derecha mediante llamdas MySQL
        Cuando des clic recoge el ID de la parada seleccionada
        Por cada fila ,recoge su ID y calcula el precio 
        mediante sumas recursivas hasta que llegue a dicho ID
        (id_inicio + id_x + ... + id_n + id_final)
        */

        $('[id^="price_parada_"]').each(function(){
            let acumulador = 0;
            var count = false; //Saber cuando hay que contar
            var dataStop = parseInt($(this).attr("data-stop"));

            if(dataStop < origen){
                //Recorrer el array normalmente
                listaTarifas.forEach(element => {
                    if(element.between[0] == $(this).attr("data-stop")){
                        count = true; //Cuenta abierta
                    }
                    if(count) acumulador += parseFloat(element.price);
                    if(element.between[1] == origen.toString()){
                        count = false; //Cuenta cerrada
                    }
                });
            }
            else if(dataStop > origen){
                //Recorrer el array inversamente
                listaTarifas.slice().reverse().forEach(element => {
                    if(element.between[1] == $(this).attr("data-stop")){
                        count = true; //Cuenta abierta
                    }
                    if(count) acumulador += parseFloat(element.price);
                    if(element.between[0] == origen.toString()){
                        count = false; //Cuenta cerrada
                    }
                });
            }
            else{
                acumulador = "";
            }

            if(acumulador === 0){
                $(this).html("<i class='fa fa-ban'>Viaje no posible</i>");
            }
            else{
                $(this).text(acumulador != "" ? (Math.round(acumulador * 100) / 100) + " €" : "");
            }
        });
    });
}

cargarParadas();