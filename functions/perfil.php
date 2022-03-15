<?php
require 'bd.php';
session_start();
/**
 * Comprueba que no haya errores en la subida del fichero
 *
 * @param String $fichero Ruta del fichero
 * @return void
 */
function comprobarErrorSubida($fichero) {
    if (!isset($fichero['error'])) {
        $error = true;
    }
    switch ($fichero['error']) {
        case UPLOAD_ERR_OK:
            $error = false;
            break;
        case UPLOAD_ERR_NO_FILE:
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
        default:
            $error = true;
    }
    return $error;
}

/**
 * Mueve el fichero, si no hay errores, al directorio del usuario y lo crea si no existe
 *
 * @param String $email Email del usuario
 * @param String $files Ruta del fichero
 * @return void
 */
function moverFichero($email, $file) {
    $result = false;
    $text = "";
    if ($file['size'] > 1000000) {
        $result = true;
        $msg .= "Tamaño del archivo muy grande";
    }

    if (!$result) {
        if (!file_exists('../images/'. $email)) {
            mkdir('../images/'.$email, 0775, true);
        }
        $result = comprobarErrorSubida($file);
        if (!$result) {
            $origin = $file['tmp_name'];
            $destiny = "../images/$email/$file";
            $result = move_uploaded_file($origin, $destiny);
        }
    }
    return $result;
}

if(isset($_POST['method'])){ // NO es una llamada desde el formulario, sino desde AJAX para comprobar si existe imagen
    if(file_exists("../images/" . $_SESSION['email'] . "/user.png")){
        echo $_SESSION['email'];
    } else {
        echo "";
    }
} else {
    $subida = moverFichero($_SESSION['email'], $_POST['subir_img']);

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


