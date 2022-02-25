<?php
include 'bd.php';
if(isset($_POST["registro"])){
    registrarUsuario();
    header("Location: ../pages/sesion.php");
} else if($_GET["session"]!="close"){
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

function registrarUsuario(){
    $result = registerUser($_POST["nombre"], $_POST["apellido1"], $_POST["apellido2"], $_POST["dni"], $_POST["fecha_nac"], $_POST["movil"], $_POST["email"], $_POST["pass"], $_POST["direccion"]);
    if($result == 'OK'){
        header("Location: ../pages/sesion.php");
    } else {
        $result = urlencode($result);
        $result = str_replace('%0D%0A', ' ', $result);
        header("Location: ../pages/registro.php?error=$result");
    }
}