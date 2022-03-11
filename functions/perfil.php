<?php
require 'bd.php';

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

function moverFichero($email, $files) {
    $result = false;
    $text = "";
    foreach ($files as $file) {
        if ($file['size'] > 1000000) {
            $result = true;
            $msg .= "Tamaño del archivo muy grande";
            break;
        }
    }
    if (!$result) {
        if (!file_exists($email)) {
            mkdir($email, 0775, true);
        }
        foreach ($files as $file) {
            $result = comprobarErrorSubida($file);
            if (!$result) {
                $origin = $file['tmp_name'];
                $destiny = "../images/$email/user.png";
                $result = move_uploaded_file($origin, $destiny);
            }
        }
    }
    return $result;
}

if(isset($_POST['method'])){ // NO es una llamada desde el formulario, sino desde AJAX para comprobar si existe imagen
    if(file_exists("../images/" . 'usuario@mail.com' . "/user.png")){
        echo $_SESSION['email'];
    } else {
        echo "";
    }
} else {
    $subida = moverFichero('usuario@mail.com', $_POST['subir_img']);

    $result = updateProfileInfo('usuario@mail.com', $_POST['telefono'], $_POST['direccion']);
    if($result == 'OK'){
        updateRegister($_SESSION["email"],date("Y-m-d H:i:s"),1);
        header('Location: ../pages/perfil.php');
    }
    else{
        $result = urlencode($result);
        $result = str_replace('%0D%0A', ' ', $result);
        header("Location: ../pages/perfil.php?error=".$result);
    }

}


