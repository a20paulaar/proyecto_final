<?php
if($_POST["method"] == 't'){
    echo $_POST["name"] . " paga con la tarjeta " . $_POST['number'] . ":" . $_POST['cvv'] . " la cantidad de " . $_POST["amount"];
}
if($_POST["method"] == 'p'){
    echo "Se paga con PayPal la cantidad de " . $_POST["amount"];
}

sleep(2);
header('Location: ../pages/pago.php');