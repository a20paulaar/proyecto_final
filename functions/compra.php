<?php
include 'bd.php';
//var_dump($_POST); die();
/*EJEMPLO
array(6) { ["trayecto"]=> string(2) "iv" ["origen"]=> string(1) "1" 
    ["destino"]=> string(1) "4" ["ida"]=> string(10) "2022-03-01" 
    ["vuelta"]=> string(10) "2022-03-03" ["step"]=> string(7) "Comprar" } 
*/
if($_POST['step']=='Comprar'){
/* 
    Llamar en bd.php a tabla horarios 

    SELECT h.id_expedicion, h.id_parada, h.hora FROM horarios h
    WHERE h.id_parada IN(<id parada origen>,<id parada destino>)
*/
    $horarios = loadSchedulesBetween($_POST['origen'], $_POST['destino']);
    $horarios_copy = $horarios;
/*
    por cada dupla de resultados (según su id_expedición)
    comprueba en los que la hora que corresponde a id parada destino sea posterior al
    id parada origen; los que no sean así se borran, no los metes en la cookie
*/
    foreach ($horarios as $exp => $datos) {
        if($datos[0]['time'] > $datos[1]['time']){
            unset($horarios[$exp]);
        }
    }

    $horarios['date'] = $_POST['ida'];

    setcookie("traveldata_i", json_encode($horarios), ['path' => '/']);

    if($_POST['trayecto']=='iv'){
        foreach ($horarios_copy as $exp => $datos) {
            if($datos[0]['time'] < $datos[1]['time']){
                unset($horarios_copy[$exp]);
            }
        }

        $horarios_copy['date'] = $_POST['vuelta'];

        setcookie("traveldata_v", json_encode($horarios_copy), ['path' => '/']);
    }

    header("Location: ../pages/compra.php");
}
else if($_POST['step']=='Pagar'){

}
else{
    /*
TODO, recibiendo llamada del pages/compra
    Cuando seleccionen un viaje, evento:
    Para la asignación de asiento, ponle en la vista un select-option de, por ejemplo, 
    50 asientos
    Haz una llamada en bd.php a tabla asignacion_asiento de

    SELECT num_asiento FROM asignacion_asiento 
    WHERE fecha_viaje=<fecha viaje>
    AND id_expedicion=<id expedicion seleccionada> 
    AND (id_parada_origen BETWEEN <id parada origen> AND <id parada destino> 
        OR id_parada_destino BETWEEN <id parada origen> AND <id parada destino>)

    Y las que devuelva en teoría tienen que ser options inhabilitadas

    [De momento solo para ida. 
    OJO, para ida/vuelta tiene que haber dos formularios en lugar de uno]
    */
}