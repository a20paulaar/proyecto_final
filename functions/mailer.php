<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/autoload.php";

/**
 * Función que crea un nuevo objeto PHPMailer para enviar un correo electrónico
 *
 * @param String $emisor Email del emisor
 * @param String $nombre Nombre del emisor
 * @param String $asunto Asunto del email
 * @param String $receptor Email del receptor
 * @param String $mensaje Cuerpo del mensaje
 * @return void
 */
function enviar_email($emisor, $nombre, $asunto, $receptor, $mensaje) {

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->Username = "";
    $mail->Password = "";
    $mail->SetFrom($emisor, $nombre);
    $mail->Subject = $asunto;
    $mail->MsgHTML($mensaje);
    $address = $receptor;
    $mail->AddAddress($address);
    $resul = $mail->Send();
    return $resul;
}