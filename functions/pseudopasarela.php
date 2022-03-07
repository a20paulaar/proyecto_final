<?php
require 'bd.php';

//Inicio pseudopasarela
if($_POST["method"] == 't'){
    echo $_POST["name"] . " paga con la tarjeta " . $_POST['number'] . ":" . $_POST['cvv'] . " la cantidad de " . $_POST["amount"];
}
if($_POST["method"] == 'p'){
    echo "Se paga con PayPal la cantidad de " . $_POST["amount"];
}
//Fin pseudopasarela

sleep(2);
//Página de retorno OK
header('Location: ../functions/pago.php');
//Página de retorno KO debería borrar la última línea de tabla transacciones