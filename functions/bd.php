<?php
/*Si se necesita llamar directamente a la BD desde el front (p.ej. AJAX) 
incluir aquí la referencia al método que se llama*/
if(isset($_POST['method'])){
    switch($_POST['method']){
        case 'loadStops': loadStops(); break;
        case 'loadSchedule': loadSchedule(); break;
        case 'loadFares': loadFares(); break;
        case 'loadUsers': loadUsers(); break;
        case 'loadPendingReservations': loadPendingReservations(null); break;
        case 'loadNews': loadNews(); break;
        case 'loadNew': loadNew($_POST['id']); break;
        //case 'loadProfileInfo': loadProfileInfo($_SESSION['email']); break;
        case 'loadProfileInfo': loadProfileInfo('usuario@mail.com'); break;
        case 'loadSchedulesBetween': loadSchedulesBetween($id_origin, $id_destination); break;
        case 'loadOccupiedSeats': loadOccupiedSeats($_POST['date'], $_POST['exp'], $_POST['id_origin'], $_POST['id_destination']); break;
        case 'loadRegister': loadRegister(); break;
    }
}

function getPerfilUsuario(){
    $perfil = isset($_SESSION["rol"]) ? $_SESSION["rol"] : 3;
    switch($perfil){
        case 1:
            return "admin";
        case 2:
            return "user";
        default:
            return "guest";
    }
}

function loadBBDD() {
    $usuario = getPerfilUsuario();
    try {
        $res = readConfig(dirname(__FILE__) . "/../config/configuracion.xml", dirname(__FILE__) . "/../config/configuracion.xsd", $usuario);
        $bd = new \PDO($res[0], $res[1], $res[2]);
        return $bd;
    } catch (\PDOException $e) {
        echo $e->getMessage();
        exit();
    }
}

function readConfig($fichero_config_BBDD, $esquema, $usuario) {

    $config = new \DOMDocument();
    $config->load($fichero_config_BBDD);
    $res = $config->schemaValidate($esquema);
    if ($res === FALSE) {
        throw new \InvalidArgumentException("Revise el fichero de configuración");
    }
    $datos = simplexml_load_file($fichero_config_BBDD);   
    $configuracion = $datos->xpath("//configuracion");    
    while(list( , $nodo) = each($configuracion)) {      
        if((string) $nodo->xpath("usuario")[0]==$usuario){          
            $ip = $nodo->xpath("ip")[0];          
            $nombre = $nodo->xpath("nombre")[0];        
            $usu = $nodo->xpath("usuario")[0];            
            $clave = $nodo->xpath("clave")[0];        
        }   
    }
    
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

function loadSchedulesBetween($id_origin, $id_destination){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT h.id_expedicion, h.id_parada, h.hora FROM horarios h
    WHERE h.id_parada IN(?,?) ORDER BY FIELD(h.id_parada, ?,?)");
    $query->bindParam(1, $id_origin);
    $query->bindParam(2, $id_destination);
    $query->bindParam(3, $id_origin);
    $query->bindParam(4, $id_destination);
    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($resul as $r) {
        $data[$r['id_expedicion']][] = [
            'stop' => $r['id_parada'], 
            'time' => $r['hora'] 
        ];
    }
    
    return $data;
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

function loadPendingReservations($email = null){
    $bd = loadBBDD();
    $query_string = "SELECT a.email, a.dni, a.fecha_viaje, a.id_expedicion, 
        a.id_parada_origen, p1.nombre AS nombre_parada_origen, h1.hora AS hora_origen,
        a.id_parada_destino, p2.nombre AS nombre_parada_destino, h2.hora AS hora_destino,
        a.num_asiento
    FROM asignacion_asiento a
    JOIN paradas p1 ON p1.id_parada = a.id_parada_origen
    JOIN paradas p2 ON p2.id_parada = a.id_parada_destino
    JOIN horarios h1 ON h1.id_parada = a.id_parada_origen AND h1.id_expedicion = a.id_expedicion
    JOIN horarios h2 ON h2.id_parada = a.id_parada_destino AND h2.id_expedicion = a.id_expedicion
    WHERE a.estado_reserva = 1"; // 0: En compra (No se usa) - 1: Reserva pendiente - 2: Reserva confirmada
    if(!is_null($email)){
        $query_string .= " AND a.email = ?";
    }
    $query = $bd->prepare($query_string);
    $query->bindParam(1, $email); //En teoría si no hay parámetros no debería pasar nada
    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);

    $json = [];
    foreach ($resul as $r) {
        $json[] = [
            'email' => $r['email'], 'dni' => $r['dni'], 'date' => $r['fecha_viaje'], 'exped' => $r['id_expedicion'],
            'start_stop' => $r['id_parada_origen'], 'start_stop_name' => $r['nombre_parada_origen'], 'start_time' => $r['hora_origen'],
            'end_stop' => $r['id_parada_destino'], 'end_stop_name' => $r['nombre_parada_destino'], 'end_time' => $r['hora_destino'],
            'seat' => $r['num_asiento']
        ];
    }
    if(is_null($email)) echo json_encode($json); //Via POST
    else return $json; //Via PHP
}

function loadNews(){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT id_noticia, titulo, noticia, imagen 
        FROM noticias ORDER BY id_noticia DESC LIMIT ?");
    $query->bindParam(1, $_POST['range'], PDO::PARAM_INT);
    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = [];
    foreach ($resul as $r) {
        $json[] = [
            'id' => $r['id_noticia'], 'title' => $r['titulo'], 'text' => $r['noticia'], 'has_pic' => $r['imagen'] 
        ];
    }
    echo json_encode($json);
}

function loadNew($id){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT titulo, noticia, imagen 
        FROM noticias WHERE id_noticia = ?");
    $query->bindParam(1, $id);
    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);

    $json = [];
    foreach ($resul as $r) {
        $json[] = [
            'title' => $r['titulo'], 'text' => $r['noticia'], 'has_pic' => $r['imagen'] 
        ];
    }
    echo json_encode($json);
}

function loadProfileInfo($email){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT nombre, apellidos, email, dni, fecha_nacimiento, telefono, direccion 
        FROM viajeros WHERE email = ? AND principal = 1");

    $query->bindParam(1, $email);
    $query->execute();
    $r = $query->fetch(PDO::FETCH_ASSOC);

    $json[] = [
        'nombre' => $r['nombre'], 'apellidos' => $r['apellidos'], 'email' => $r['email'], 
        'dni' => $r['dni'], 'fecha_nacimiento' => $r['fecha_nacimiento'], 'telefono' => $r['telefono'], 'direccion' => $r['direccion']
    ];
    echo json_encode($json);
}

function loadOccupiedSeats($date, $expedition, $id_origin, $id_destination){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT num_asiento FROM asignacion_asiento 
    WHERE fecha_viaje=? AND id_expedicion=? 
    AND (id_parada_origen BETWEEN ? AND ? OR id_parada_destino BETWEEN ? AND ?)");

    $query->bindParam(1, $date);
    $query->bindParam(2, $expedition);
    $query->bindParam(3, $id_origin);
    $query->bindParam(4, $id_destination);
    $query->bindParam(5, $id_origin);
    $query->bindParam(6, $id_destination);

    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);

    $seats = [];
    foreach ($resul as $r) {
        $seats[] = $r['num_asiento'];
    };
    echo json_encode($seats);
}

function loadLastPayment($email){
    $query = $bd->prepare("SELECT email, cantidad, metodo, fecha_hora 
    FROM transacciones
    WHERE fecha_hora=? ORDER BY fecha_hora DESC LIMIT 1");

    $query->bindParam(1, $email);
    $query->execute();
    $r = $query->fetch(PDO::FETCH_ASSOC);

    return $r;
}

function loadRegister(){
    $query = $bd->prepare("SELECT usuario, fecha, tipo 
    FROM registro ORDER BY id DESC LIMIT 30");
    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = [];
    foreach ($resul as $r) {
        $json[] = ['usuario' => $r['usuario'], 'fecha' => $r['fecha'], 'tipo' => $r['tipo']];
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

function setReservation($email, $date, $exped, $id_origin, $id_destination, $seat){
    $bd = loadBBDD();
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = $bd->prepare("SELECT dni FROM viajeros WHERE email=?");
    $query->bindParam(1, $email);
    $query->execute();
    $r = $query->fetch(PDO::FETCH_ASSOC);

    $bd->beginTransaction();
    try {
        $query = $bd->prepare("INSERT INTO asignacion_asiento VALUES(?,?,?,?,?,?,1)");
        $query->bindParam(1, $r['dni']);
        $query->bindParam(2, $date);
        $query->bindParam(3, $exped);
        $query->bindParam(4, $id_origin);
        $query->bindParam(5, $id_destination);
        $query->bindParam(6, $seat);
        $query->execute();
        $bd->commit();

        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'Ha habido un error con la compra, disculpe las molestias. 
        Si ha sido efectuado un pago y no ha recibido su billete, 
        contacte con Atención al Cliente';
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

function registerUser($name, $surname, $dni, $birth, $phone, $mail, $pass, $address){
    $bd = loadBBDD();

    $query_select = $bd->prepare("SELECT COUNT(*) AS cuenta FROM usuarios WHERE email = ?");
    $query_select->bindParam(1, $mail);
    $query_select->execute();
    
    if($result_select=$query_select->fetch()){
        if($result_select['cuenta']) return "Ya existe una cuenta con este email";
    }

    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("INSERT INTO usuarios VALUES (?, md5(?), 2)"); //Falta la query de inserción y los binds (no tengo la bd a mano en clase).
        $query->bindParam(1, $mail);
        $query->bindParam(2, $pass);
        $query->execute();

        $query2 = $bd->prepare("INSERT INTO viajeros VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $query2->bindParam(1, $dni);
        $query2->bindParam(2, $mail);
        $query2->bindParam(3, $name);
        $query2->bindParam(4, $surname);
        $query2->bindParam(5, $phone);
        $query2->bindParam(6, $address);
        $query2->bindParam(7, $birth);
        $query2->execute();
        $bd->commit();
        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR EN EL REGISTRO. MOTIVO: ' . $th->getMessage();
    }
}

function updateProfileInfo($mail, $phone, $address){
    $bd = loadBBDD();
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("UPDATE viajeros SET telefono = ? , direccion = ? WHERE email = ? AND principal = 1");
        $query->bindParam(1, $phone);
        $query->bindParam(2, $address);
        $query->bindParam(3, $mail);
        $query->execute();
        $bd->commit();
        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR DE BASE DE DATOS. MOTIVO: ' . $th->getMessage();
    }
}

function insertPayment($email, $cantidad, $metodo, $fecha_hora){
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("INSERT INTO transacciones VALUES(?, ?, ?, ?)");

        $query->bindParam(1, $email);
        $query->bindParam(2, $cantidad);
        $query->bindParam(3, $metodo);
        $query->bindParam(4, $fecha_hora);
        $query->execute();
        $bd->commit();
        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR EN EL REGISTRO. MOTIVO: ' . $th->getMessage();
    }
}

function updateRegister($mail, $date, $type){
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("INSERT INTO registro VALUES(?, ?, ?)");

        $query->bindParam(1, $mail);
        $query->bindParam(2, $date);
        $query->bindParam(3, $type);
        $query->execute();
        $bd->commit();
        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR EN EL REGISTRO. MOTIVO: ' . $th->getMessage();
    }
}