<?php
require 'bd.php';

if(isset($_POST['tarifas'])) actualizarTarifas();





function actualizarTarifas(){
    $paradas = explode("_", $_POST['between']);
    $precio = $_POST['price'];

    $result = setFare($paradas[0], $paradas[1], $precio);

    if($result == 'OK'){
        header('Location: ../pages/admin.php');
    }
    else{
        $result = urlencode($result);
        $result = str_replace('%0D%0A', ' ', $result); //Quitar todos los saltos de línea
        header("Location: ../pages/admin.php?error=".$result);
    }
}