<?php
include 'bd.php';
//var_dump($_POST); die();
/*EJEMPLO
array(9) { ["trayecto"]=> string(2) "iv" ["origen"]=> string(1) "1" 
    ["destino"]=> string(1) "3" ["ida"]=> string(10) "2022-03-05" 
    ["vuelta"]=> string(10) "2022-03-05" ["anc"]=> string(1) "0" 
    ["adu"]=> string(1) "1" ["jov"]=> string(1) "1" ["step"]=> string(7) "Comprar" } 
*/

if($_POST['step']=='Comprar'){
/* 
    Llamar en bd.php a tabla horarios
*/
    $horarios = loadSchedulesBetween($_POST['origen'], $_POST['destino']);
    $horarios_copy = $horarios;
/*
    por cada dupla de resultados (según su id_expedición)
    comprueba en los que la hora que corresponde a id parada destino sea posterior al
    id parada origen; los que no sean así se borran, no los metes en la cookie
*/
    foreach ($horarios as $exp => $datos) {
        if($datos[0]['time'] > $datos[1]['time']){
            unset($horarios[$exp]);
        }
    }

    $horarios['date'] = $_POST['ida'];
    $horarios['people']['anc'] = $_POST['anc'];
    $horarios['people']['adu'] = $_POST['adu'];
    $horarios['people']['jov'] = $_POST['jov'];

    setcookie("traveldata_i", json_encode($horarios), ['path' => '/']);

    if($_POST['trayecto']=='iv'){
        foreach ($horarios_copy as $exp => $datos) {
            if($datos[0]['time'] < $datos[1]['time']){
                unset($horarios_copy[$exp]);
            }
        }

        $horarios_copy['date'] = $_POST['vuelta'];
        $horarios_copy['people']['anc'] = $_POST['anc'];
        $horarios_copy['people']['adu'] = $_POST['adu'];
        $horarios_copy['people']['jov'] = $_POST['jov'];

        setcookie("traveldata_v", json_encode($horarios_copy), ['path' => '/']);
    }

    header("Location: ../pages/compra.php");
}
else if($_POST['step']=='Pagar'){
    // TODO Repreguntar por si durante el proceso, ya otro usuario asignó el asiento

    //Guardar en la base de datos la reserva
    $values = explode("_", $_POST['h_ida']);
    $result = setReservation($_SESSION['email'], $values[0], $values[1], $values[2], $values[3], $_POST['a_ida']);

    if(isset($_POST['h_vuelta']) && $result == 'OK'){
        $values = explode("_", $_POST['h_vuelta']);
        $result = setReservation($_SESSION['email'], $values[0], $values[1], $values[2], $values[3], $_POST['a_vuelta']);     
    }

    //Hacer el pago y cargar una página de success/error
    if($result == 'OK'){
        //Borrar cookies tras registrar el viaje
        setcookie('traveldata_i', '', time() - 3600);
        setcookie('traveldata_v', '', time() - 3600);

        doPayment($_POST); //Simular alguna manera de pago
    }
    else{
        $result = urlencode($result);
        $result = str_replace('%0D%0A', ' ', $result); //Quitar todos los saltos de línea
        header("Location: ../pages/compra.php?error=".$result);
    }
}

function doPayment($post_data){
    if(insertPayment($_SESSION['email'], str_replace(' €', '', $post_data["total"]), $post_data["forma_pago"], date("Y-m-d H:i:s")) == 'OK'){
        /*
            Un ejemplo de pasarela estaría aquí:
            https://www.jose-aguilar.com/blog/como-implementar-una-pasarela-de-pago-mediante-tarjeta-de-credito-con-php/
            (Solo como referencia)
        */

        // Hay que simular un formulario invisible para mandar datos a una supuesta pasarela
        // ya que necesito mandarlo a una API externa con POST
        ?>
            <form id="realizarPago" action="pseudopasarela.php" method="post">
            <input type='hidden' name='name' value='<?php echo $post_data["nombre"]; ?>'>

        <?php if($post_data["forma_pago"] == 't'){ ?>
            <input type='hidden' name='number' value='<?php echo $post_data["cod_tarjeta"]; ?>'>
            <input type='hidden' name='cvv' value='<?php echo $post_data["cvv"]; ?>'>
            <input type='hidden' name='amount' value='<?php echo $post_data["total"]; ?>'>
        <?php } ?>

            <input type='hidden' name='method' value='<?php echo $post_data["forma_pago"]; ?>'>
            </form>
            <script>
            $(document).ready(function () {
                $("#realizarPago").submit();
            });
            </script>
        <?php
    }
}