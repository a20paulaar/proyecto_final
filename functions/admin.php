<?php
require 'bd.php';
require 'mailer.php';

if(isset($_POST['tarifas'])) actualizarTarifas();
if(isset($_POST['reservas_OK']) || isset($_POST['reservas_NOK'])) actualizarReservas();
if(isset($_POST['usuarios'])) actualizarUsuarios();

/**
 * Función para que el administrador acepte o deniegue una reserva
 *
 * @return void
 */
function actualizarReservas(){
    $action = isset($_POST['reservas_OK']); //TRUE: Aceptar, actualiza en BD / FALSE: Denegar, borra de BD
    $values = explode("_", $_POST['values']);
    $result = updateReservation($action, $values[0], $values[1], $values[2], $values[3]); //No se pasan las paradas porque lo normal es que una persona no suba 2 veces en el mismo bus
    
    $asunto = ""; $mensaje = "";
    if($action){
        $asunto = "Su reserva ha sido aceptada";
        $mensaje = "Su reserva ha sido aceptada, podrá embarcar al autobús presentando el DNI y la documentación adicional si dispone de un descuento.<br/>¡Buen viaje!";
    }
    else{
        $asunto = "Su reserva ha sido denegada";
        $asunto = "Su reserva ha sido denegada. Si necesita más información, por favor póngase en contacto con nosotros por los canales oficiales.<br/>Disculpe las molestias";
    }
    enviar_email("admin@transminho.es", "TransMiño", $asunto, $_POST['email'], $mensaje);

    
    volver($result);
}

/**
 * Función para que el administrador actualice las tarifas
 *
 * @return void
 */
function actualizarTarifas(){
    $paradas = explode("_", $_POST['between']);
    $result = setFare($paradas[0], $paradas[1], $_POST['price']);
    volver($result);
}

/**
 * Función para actualizar el rol del usuario (administrador o usuario estandar)
 *
 * @return void
 */
function actualizarUsuarios(){
    $result = updateUserProfile($_POST['mail'], $_POST['profile']); 
    volver($result);
}

/**
 * Gestión de redirección tras actualizar datos
 *
 * @param String $result Resultado de la BD
 * @return void
 */
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