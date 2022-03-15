<?php
include 'bd.php';

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

    setcookie("traveldata_i", json_encode($horarios), time()+3600, "/");

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

        setcookie("traveldata_v", json_encode($horarios_copy), time()+3600, "/");
    }

    header("Location: ../pages/compra.php");
}
else if($_POST['step']=='Pagar'){
    //Guardar en la base de datos la reserva
    $values = explode("_", $_POST['h_ida']);
    $seats = (is_array($_POST["a_ida"]) ? $_POST['a_ida'] : [$_POST['a_ida']]);

    //Primero reservamos para el usuario
    $result = setReservation($_SESSION['email'], $values[0], $values[1], $values[2], $values[3], $seats[0]);

    //Retiro el primer asiento del array porque ya se le ha asignado (No reasignamos para poder coger el mismo indice que los IDs del formulario de viajeros adicionales, que empieza en 1)
    unset($seats[0]);

    //Si hay viajeros adicionales, reservamos para ellos
    foreach ($seats as $id => $seat) {
        //Primero añadimos al viajero a la lista de viajeros. Si el password es null, solo registrará el viajero, no el usuario
        if($result == 'OK') $result = registerUser($_POST["nombre_".$id], $_POST["apellidos_".$id], $_POST["dni_".$id], $_POST["fecha_".$id], $_POST["telefono_".$id], $_SESSION['email'], null, $_POST["direccion_".$id]);
        //Luego asignamos al viajero (mediante su DNI) al asiento
        if($result == 'OK') $result = setReservation(null, $values[0], $values[1], $values[2], $values[3], $seat, $_POST["dni_".$id]);
    }


    if(isset($_POST['h_vuelta']) && $result == 'OK'){
        //Guardar en la base de datos la reserva
        $values = explode("_", $_POST['h_vuelta']);
        $seats = (is_array($_POST["a_vuelta"]) ? $_POST['a_vuelta'] : [$_POST['a_vuelta']]);

        //Primero reservamos para el usuario
        $result = setReservation($_SESSION['email'], $values[0], $values[1], $values[2], $values[3], $seats[0]);

        //Retiro el primer asiento del array porque ya se le ha asignado (No reasignamos para poder coger el mismo indice que los IDs del formulario de viajeros adicionales, que empieza en 1)
        unset($seats[0]);

        //Si hay viajeros adicionales, reservamos para ellos
        foreach ($seats as $id => $seat) {
            //No añadimos al viajero a la lista de viajeros porque se tiene que haber hecho a la ida ya
            //Luego asignamos al viajero (mediante su DNI) al asiento
            if($result == 'OK') $result = setReservation(null, $values[0], $values[1], $values[2], $values[3], $seat, $_POST["dni_".$id]);
        }
    }

    //Hacer el pago y cargar una página de success/error
    if($result == 'OK'){
        //Borrar cookies tras registrar el viaje
        setcookie('traveldata_i', '', time() - 3600);
        setcookie('traveldata_v', '', time() - 3600);

        doPayment($_POST); //Simular alguna manera de pago
        header('Location:../pages/pago.php');
    }
    else{
        $result = urlencode($result);
        $result = str_replace('%0D%0A', ' ', $result); //Quitar todos los saltos de línea
        header("Location: ../pages/compra.php?error=".$result);
    }
}

/**
 * Se efectua un pago mediante un pseudoformulario
 *
 * @param Array $post_data Valores recibidos por POST
 * @return void
 */
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