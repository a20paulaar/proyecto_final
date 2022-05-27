<?php
require 'bd.php';
session_start();

/**
 * Mueve el fichero, si no hay errores, al directorio del usuario y lo crea si no existe
 *
 * @param String $email Email del usuario
 * @param String $files Ruta del fichero
 * @return void
 */
function moverFichero($email, $file) {
    $error = false;
    if ($file['size'] > 5000000) {
        $error = "Tamaño del archivo muy grande";
    } else {
        if (!file_exists('../images/' . $email)) {
            mkdir('../images/' . $email, 0775, true);
        }
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $origin = $file['tmp_name'];
            $destiny = "../images/" . $email . "/user.png";
            $result = move_uploaded_file($origin, $destiny);
        }
    }
    return $error;
}

if(isset($_POST['method'])){ // NO es una llamada desde el formulario, sino desde AJAX para comprobar si existe imagen
    if(file_exists("../images/" . $_SESSION['email'] . "/user.png")){
        echo $_SESSION['email'];
    } else {
        echo "";
    }
} else {
    $error = moverFichero($_SESSION['email'], $_FILES['subir_img']);
    if($error){
        $error = urlencode($error);
        $error = str_replace('%0D%0A', ' ', $error);
        header("Location: ../pages/perfil.php?error=".$error);
    }

    $result = updateProfileInfo($_SESSION['email'], $_POST['telefono'], $_POST['direccion']);
    if($result == 'OK'){
        updateRegister($_SESSION["email"],date("Y-m-d H:i:s"),2);
        header('Location: ../pages/perfil.php');
    }
    else{
        $result = urlencode($result);
        $result = str_replace('%0D%0A', ' ', $result);
        header("Location: ../pages/perfil.php?error=".$result);
    }

}


