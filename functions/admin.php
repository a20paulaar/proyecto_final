<?php
require 'bd.php';

if(isset($_POST['tarifas'])) actualizarTarifas();
if(isset($_POST['reservas_OK']) || isset($_POST['reservas_NOK'])) actualizarReservas();
if(isset($_POST['usuarios'])) actualizarUsuarios();

function actualizarReservas(){
    $action = isset($_POST['reservas_OK']); //TRUE: Aceptar, actualiza en BD / FALSE: Denegar, borra de BD
    $values = explode("_", $_POST['values']);
    $result = updateReservation($action, $values[0], $values[1], $values[2], $values[3]); //No se pasan las paradas porque lo normal es que una persona no suba 2 veces en el mismo bus
    volver($result);
}

function actualizarTarifas(){
    $paradas = explode("_", $_POST['between']);
    $result = setFare($paradas[0], $paradas[1], $_POST['price']);
    volver($result);
}

function actualizarUsuarios(){
    $result = updateUserProfile($_POST['mail'], $_POST['profile']); 
    volver($result);
}

function volver($result){
    if($result == 'OK'){
        header('Location: ../pages/admin.php');
    }
    else{
        $result = urlencode($result);
        $result = str_replace('%0D%0A', ' ', $result); //Quitar todos los saltos de línea
        header("Location: ../pages/admin.php?error=".$result);
    }
}