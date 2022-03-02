<?php
require 'bd.php';
var_dump($_POST);

$result = updateProfileInfo('usuario@mail.com', $_POST['telefono'], $_POST['direccion']);
if($result == 'OK'){
    header('Location: ../pages/perfil.php');
}
else{
    $result = urlencode($result);
    $result = str_replace('%0D%0A', ' ', $result);
    header("Location: ../pages/perfil.php?error=".$result);
}
