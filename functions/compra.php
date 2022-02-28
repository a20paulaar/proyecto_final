<?php
include 'bd.php';
var_dump($_POST);
if($_POST['step']=='Comprar'){
    /* TODO: Llamar en bd.php a tabla horarios 

    SELECT h.id_expedicion, h.id_parada, h.hora FROM horarios h
    WHERE h.id_parada IN(<id parada origen>,<id parada destino>)

    por cada dupla de resultados (según su id_expedición)
    comprueba en los que la hora que corresponde a id parada destino sea posterior al
    id parada origen; los que no sean así no los metes en el json de vuelta

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
else if($_POST['step']=='Pagar'){

}