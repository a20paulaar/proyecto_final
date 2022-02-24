<?php
/*Si se necesita llamar directamente a la BD desde el front (p.ej. AJAX) 
incluir aquí la referencia al método que se llama*/
if(isset($_POST['method'])){
    switch($_POST['method']){
        case 'loadStops': loadStops(); break;
        case 'loadSchedule': loadSchedule(); break;
        case 'loadFares': loadFares(); break;
        case 'loadUsers': loadUsers(); break;
        case 'loadPendingReservations': loadPendingReservations(); break;
        case 'loadNews': loadNews(); break;
    }
}

function loadBBDD() {
    try {
        $res = readConfig(dirname(__FILE__) . "/../config/configuracion.xml", dirname(__FILE__) . "/../config/configuracion.xsd");
        $bd = new PDO($res[0], $res[1], $res[2]);
        return $bd;
    } catch (\Exception $e) {
        echo $e->getMessage();
        exit();
    }
}

function readConfig($fichero_config_BBDD, $esquema) {

    $config = new DOMDocument();
    $config->load($fichero_config_BBDD);
    $res = $config->schemaValidate($esquema);
    if ($res === FALSE) {
        throw new InvalidArgumentException("Revise el fichero de configuración");
    }
    $datos = simplexml_load_file($fichero_config_BBDD);
    $ip = $datos->xpath("//ip");
    $nombre = $datos->xpath("//nombre");
    $usu = $datos->xpath("//usuario");
    $clave = $datos->xpath("//clave");
    $cad = sprintf("mysql:dbname=%s;host=%s", $nombre[0], $ip[0]);
    $resul = [];
    $resul[] = $cad;
    $resul[] = $usu[0];
    $resul[] = $clave[0];
    return $resul;
}

// CARGAR DATOS

function loadStops(){
    $bd = loadBBDD();
    $ins = "SELECT id_parada, nombre FROM paradas";
    $resul = $bd->query($ins);
    if (!$resul) {
        return FALSE;
    }
    if ($resul->rowCount() === 0) {
        return FALSE;
    }

    $json = [];
    foreach ($resul as $r) {
        $json['paradas'][] = ['id' => $r['id_parada'], 'name' => $r['nombre']];
    }
    echo json_encode($json);
}

function validUser($user, $pass){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT COUNT(*) AS cuenta, perfil FROM usuarios WHERE email = ? AND contrasenha = md5(?)");
    $query->bindParam(1, $user);
    $query->bindParam(2, $pass);
    $query->execute();
    if($result=$query->fetch()){
        if($result['cuenta']) return $result["perfil"];
        else return null;
    }
}

function getSchedule(){
    $bd = loadBBDD();
    $query = "SELECT h.id_expedicion, h.hora, h.id_parada, p.nombre FROM horarios h
    JOIN paradas p ON h.id_parada = p.id_parada";
    $resul = $bd->query($query);

    $json = [];
    foreach ($resul as $r) {
        $json['paradas'][$r['id_expedicion']][] = ['id' => $r['id_parada'], 'name' => $r['nombre'], 'hour' => $r['hora']];
    }
    echo json_encode($json);
}

function loadFares(){
    $bd = loadBBDD();
    $query = "SELECT t.id_parada_origen, t.id_parada_destino, 
        p1.nombre AS nombre_parada_origen, p2.nombre AS nombre_parada_destino, t.precio FROM tarifas t
    JOIN paradas p1 ON p1.id_parada = t.id_parada_origen
    JOIN paradas p2 ON p2.id_parada = t.id_parada_destino";
    $resul = $bd->query($query);

    $json = [];
    foreach ($resul as $r) {
        $json[] = [
            'between' => [$r['id_parada_origen'], $r['id_parada_destino']],
            'between_n' => [$r['nombre_parada_origen'], $r['nombre_parada_destino']], 
            'price' => $r['precio']
        ];
    }
    echo json_encode($json);
}

function loadUsers(){
    $bd = loadBBDD();
    $query = "SELECT email, perfil FROM usuarios";
    $resul = $bd->query($query);

    $json = [];
    foreach ($resul as $r) {
        $json[] = ['email' => $r['email'], 'profile' => $r['perfil']];
    }
    echo json_encode($json);
}

function loadPendingReservations(){
    $bd = loadBBDD();
    $query = "SELECT a.dni, a.fecha_viaje, a.id_expedicion, 
        a.id_parada_origen, p1.nombre AS nombre_parada_origen, h1.hora AS hora_origen,
        a.id_parada_destino, p2.nombre AS nombre_parada_destino, h2.hora AS hora_destino,
        a.num_asiento
    FROM asignacion_asiento a
    JOIN paradas p1 ON p1.id_parada = a.id_parada_origen
    JOIN paradas p2 ON p2.id_parada = a.id_parada_destino
    JOIN horarios h1 ON h1.id_parada = a.id_parada_origen AND h1.id_expedicion = a.id_expedicion
    JOIN horarios h2 ON h2.id_parada = a.id_parada_destino AND h2.id_expedicion = a.id_expedicion
    WHERE estado_reserva = 1"; // 0: En compra - 1: Reserva pendiente - 2: Reserva confirmada
    $resul = $bd->query($query);

    $json = [];
    foreach ($resul as $r) {
        $json[] = [
            'dni' => $r['dni'], 'date' => $r['fecha_viaje'], 'exped' => $r['id_expedicion'],
            'start_stop' => $r['id_parada_origen'], 'start_stop_name' => $r['nombre_parada_origen'], 'start_time' => $r['hora_origen'],
            'end_stop' => $r['id_parada_destino'], 'end_stop_name' => $r['nombre_parada_destino'], 'end_time' => $r['hora_destino'],
            'seat' => $r['num_asiento']
        ];
    }
    echo json_encode($json);
}

function loadNews(){
    $bd = loadBBDD();
    $query = "SELECT id_noticia, titulo, noticia, imagen 
        FROM noticias ORDER BY id_noticia DESC LIMIT " . $_POST['range'];
    $resul = $bd->query($query);

    $json = [];
    foreach ($resul as $r) {
        $json[] = [
            'id' => $r['id_noticia'], 'title' => $r['titulo'], 'text' => $r['noticia'], 'has_pic' => $r['imagen'] 
        ];
    }
    echo json_encode($json);
}

//ACTUALIZAR DATOS

function setFare($stop1, $stop2, $value){
    $bd = loadBBDD();
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("UPDATE tarifas SET precio = ? 
            WHERE id_parada_origen = ? AND id_parada_destino = ?");
        $query->bindParam(1, $value);
        $query->bindParam(2, $stop1);
        $query->bindParam(3, $stop2);
        $query->execute();
        $bd->commit();

        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR DE BASE DE DATOS. MOTIVO: ' . $th->getMessage();
    }
}

function updateReservation($valid, $dni, $date, $exped, $seat){
    $bd = loadBBDD();
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bd->beginTransaction();
    try {
        if($valid){
            $query = $bd->prepare("UPDATE asignacion_asiento SET estado_reserva = 2 
                WHERE dni = ? AND fecha_viaje = ? AND id_expedicion = ? AND num_asiento = ?");
        }
        else{
            $query = $bd->prepare("DELETE FROM asignacion_asiento
                WHERE dni = ? AND fecha_viaje = ? AND id_expedicion = ? AND num_asiento = ?");
        }
        $query->bindParam(1, $dni);
        $query->bindParam(2, $date);
        $query->bindParam(3, $exped);
        $query->bindParam(4, $seat);
        $query->execute();
        $bd->commit();

        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR DE BASE DE DATOS. MOTIVO: ' . $th->getMessage();
    }
}

function updateUserProfile($mail, $profile){
    $bd = loadBBDD();
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("UPDATE usuarios SET perfil = ? WHERE email = ?");
        $query->bindParam(1, $profile);
        $query->bindParam(2, $mail);
        $query->execute();
        $bd->commit();

        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR DE BASE DE DATOS. MOTIVO: ' . $th->getMessage();
    }
}