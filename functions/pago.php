<?php
require 'bd.php';
require 'mailer.php';
session_start();

$reservation_data = loadPendingReservations($_SESSION['email']);
$payment_data = loadLastPayment($_SESSION['email']);
$points_data = updatePoints($_SESSION['email'], $payment_data['cantidad']*100);

$cuerpo_mail = "<h2>Su compra ha sido efectuada</h2>
<p>En breve recibirá un correo con la aceptación de la reserva.</p>
<h3>Datos de viaje:</h3>";

foreach ($reservation_data as $key => $data) {
    $cuerpo_mail .= "
    <table>
        <tr><th>DNI " . $data['dni'] . "</th></tr>
        <tr><td>Fecha: " . $data['date'] . 
            "<br/> Origen: " . $data['start_stop_name'] . " (" . $data['start_time'] . ")" .
            "<br/> Origen: " . $data['end_stop_name'] . " (" . $data['end_time'] . ")" . "
        </td></tr>
        <tr><td>Asiento: " . $data['seat'] . "</th></tr>
    </table>";
}

$cuerpo_mail .= "<h3>Datos de compra:</h3> <table><tr><td>Total: " . $payment_data['cantidad'] . "<br/>" . ($payment_data['metodo'] == 't' ? "Tarjeta" : "PayPal") . "</td></tr></table>";
$cuerpo_mail .= "<h4>Con esta compra ha ganado un total de " . $payment_data['cantidad']*100 . " puntos</h4>";

enviar_email("admin@transminho.es", "TransMiño", "Su pago ha sido recibido", $_SESSION['email'], $cuerpo_mail);
enviar_email("admin@transminho.es", "TransMiño", "Tiene reservas pendientes", "admin@transminho.es", "Tiene nuevas reservas pendientes referentes al usuario " . $_SESSION['email']);

header('Location: ../pages/pago.php');
