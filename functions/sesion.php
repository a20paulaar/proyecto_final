<?php
include 'bd.php';
if($_GET["session"]!="close"){
    $perfil = validUser($_POST['email'], $_POST['pass']);
    if($perfil!=null){
        session_start();
        $_SESSION["rol"] = $perfil;
        $_SESSION["email"] = $_POST["email"];
        header("Location: ../index.php");
    } else {
        header("Location: ../pages/sesion.php?error=true");
    }
} else {
    session_destroy();
    header("Location: ../index.php");
}
