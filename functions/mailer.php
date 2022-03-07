<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/autoload.php";

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