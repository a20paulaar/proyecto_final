<?php
include 'mailer.php';

if(isset($_POST["atencion"])){
    if(enviar_email($_POST["email"], $_POST["nombre"], "Atención al cliente", "atencionalcliente@transminho.es", $_POST["mensaje"])){
        header('Location:../pages/atencion.php?exito=true');
    } else {
        header('Location: ../pages/atencion.php?error=true');
    } 
}