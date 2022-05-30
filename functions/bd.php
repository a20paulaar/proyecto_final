<?php
/*Si se necesita llamar directamente a la BD desde el front (p.ej. AJAX) 
incluir aquí la referencia al método que se llama*/
session_start();
if(isset($_POST['method'])){
    switch($_POST['method']){
        case 'loadStops': loadStops(); break;
        case 'loadSchedule': loadSchedule(); break;
        case 'loadFares': loadFares(); break;
        case 'loadUsers': loadUsers(); break;
        case 'loadPendingReservations': loadPendingReservations(null); break;
        case 'loadNews': loadNews(); break;
        case 'loadNew': loadNew($_POST['id']); break;
        case 'loadHowManyNews' : loadHowManyNews(); break;
        case 'loadProfileInfo': loadProfileInfo(); break;
        case 'loadSchedulesBetween': loadSchedulesBetween($id_origin, $id_destination); break;
        case 'loadOccupiedSeats': loadOccupiedSeats($_POST['date'], $_POST['exp'], $_POST['id_origin'], $_POST['id_destination']); break;
        case 'loadRegister': loadRegister(); break;
        case 'loadLog': loadLog($_POST['type']); break;
        case 'loadPoints': loadPoints(); break;
    }
}

/**
 * Función que obtiene el rol del usuario (anónimo, estándar o administrador) para conectarse a la BD
 *
 * @return void
 */
function getPerfilUsuario(){
    $perfil = isset($_SESSION["rol"]) ? $_SESSION["rol"] : 3;
    switch($perfil){
        case 1:
            return "a9da_admin";
        case 2:
            return "a9da_user";
        default:
            return "a9da_guest";
    }
}

/**
 * Realiza la conexión a la BD
 *
 * @return void
 */
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

/**
 * Undocumented function
 *
 * @param String $fichero_config_BBDD Ruta al fichero XML
 * @param String $esquema Ruta al fichero XSD
 * @param String $usuario Tipo de usuario
 * @return void
 */
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

/**
 * Carga las paradas de la línea desde la BD 
 *
 * @return void
 */
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
        $json['paradas'][] = ['id' => $r['id_parada'], 'name' => encode($r['nombre'])];
    }
    echo json_encode($json);
}

/**
 * Valida el usuario, comprobando que el email y la contraseña coinciden con los guardados en la BD
 *
 * @param String $user Email del usuario
 * @param String $pass Contraseña
 * @return void
 */
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

/**
 * Recoge todos los horarios
 *
 * @return void
 */
function loadSchedule(){
    $bd = loadBBDD();
    $query = "SELECT h.id_expedicion, h.hora, h.id_parada, p.nombre FROM horarios h
    JOIN paradas p ON h.id_parada = p.id_parada";
    $resul = $bd->query($query);

    $json = [];
    foreach ($resul as $r) {
        $json['paradas'][$r['id_expedicion']][] = ['id' => $r['id_parada'], 'name' => encode($r['nombre']), 'hour' => $r['hora']];
    }
    echo json_encode($json);
}

/**
 * Recoge los horarios entre dos paradas especificadas
 *
 * @param String $id_origin Id de la parada de origen
 * @param String $id_destination Id de la parada de destino
 * @return void
 */
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

/**
 * Carga todas las tarifas entre paradas
 *
 * @return void
 */
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
            'between_n' => [encode($r['nombre_parada_origen']), encode($r['nombre_parada_destino'])], 
            'price' => $r['precio']
        ];
    }
    echo json_encode($json);
}

/**
 * Carga el email y el rol de todos los usuarios registrados
 *
 * @return void
 */
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

/**
 * Carga las reservas pendientes de confirmar
 *
 * @param String $email Email del usuario
 * @return void
 */
function loadPendingReservations($email = null){
    $bd = loadBBDD();
    $query_string = "SELECT v.email, a.dni, a.fecha_viaje, a.id_expedicion, 
        a.id_parada_origen, p1.nombre AS nombre_parada_origen, h1.hora AS hora_origen,
        a.id_parada_destino, p2.nombre AS nombre_parada_destino, h2.hora AS hora_destino,
        a.num_asiento
    FROM asignacion_asiento a
    JOIN paradas p1 ON p1.id_parada = a.id_parada_origen
    JOIN paradas p2 ON p2.id_parada = a.id_parada_destino
    JOIN horarios h1 ON h1.id_parada = a.id_parada_origen AND h1.id_expedicion = a.id_expedicion
    JOIN horarios h2 ON h2.id_parada = a.id_parada_destino AND h2.id_expedicion = a.id_expedicion
    JOIN viajeros v ON v.dni = a.dni
    WHERE a.estado_reserva = 1"; // 0: En compra (No se usa) - 1: Reserva pendiente - 2: Reserva confirmada
    if(!is_null($email)){
        $query_string .= " AND v.email = ?";
    }
    $query = $bd->prepare($query_string);
    $query->bindParam(1, $email); //En teoría si no hay parámetros no debería pasar nada
    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);

    $json = [];
    foreach ($resul as $r) {
        $json[] = [
            'email' => $r['email'], 'dni' => $r['dni'], 'date' => $r['fecha_viaje'], 'exped' => $r['id_expedicion'],
            'start_stop' => $r['id_parada_origen'], 'start_stop_name' => encode($r['nombre_parada_origen']), 'start_time' => $r['hora_origen'],
            'end_stop' => $r['id_parada_destino'], 'end_stop_name' => encode($r['nombre_parada_destino']), 'end_time' => $r['hora_destino'],
            'seat' => $r['num_asiento']
        ];
    }
    if(is_null($email)) echo json_encode($json); //Via POST
    else return $json; //Via PHP
}

/**
 * Carga la vista previa de las últimas noticias que se mostrarán en el lateral
 *
 * @return void
 */
function loadNews(){
    $page = ($_POST['p']-1)*10;
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT id_noticia, titulo, noticia, imagen 
        FROM noticias ORDER BY id_noticia DESC LIMIT ? OFFSET ?");
    $query->bindParam(1, $_POST['range'], PDO::PARAM_INT);
    $query->bindParam(2, $page, PDO::PARAM_INT);
    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = [];
    foreach ($resul as $r) {
        $json[] = [
            'id' => $r['id_noticia'], 'title' => encode($r['titulo']), 'text' => encode($r['noticia']), 'has_pic' => $r['imagen'] 
        ];
    }
    echo json_encode($json);
}

/**
 * Carga una noticia en concreto, pasándole el id
 *
 * @param String $id
 * @return void
 */
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
            'title' => encode($r['titulo']), 'text' => encode($r['noticia']), 'has_pic' => $r['imagen'] 
        ];
    }
    echo json_encode($json);
}

function loadHowManyNews(){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT COUNT(*) FROM noticias");
    $query->execute();

    if($result=$query->fetch()){
        echo $result[0];
    }
}

/**
 * Carga la información del usuario activo
 *
 * @return void
 */
function loadProfileInfo(){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT nombre, apellidos, email, dni, fecha_nacimiento, telefono, direccion 
        FROM viajeros WHERE email = ? AND principal = 1");

    $query->bindParam(1, $_SESSION['email']);
    $query->execute();
    $r = $query->fetch(PDO::FETCH_ASSOC);

    $json = [
        'nombre' => encode($r['nombre']), 'apellidos' => encode($r['apellidos']), 'email' => $r['email'], 
        'dni' => $r['dni'], 'fecha_nacimiento' => $r['fecha_nacimiento'], 'telefono' => $r['telefono'], 'direccion' => encode($r['direccion'])
    ];
    if(isset($_POST['method'])){
        echo json_encode($json);
    } else {
        return json_encode($json);
    }
}

/**
 * Carga los asientos que están ocupados para una fecha y expedición entre dos paradas
 *
 * @param String $date Fecha del viaje
 * @param String $expedition Id de expedición
 * @param String $id_origin Id de la parada de origen
 * @param String $id_destination Id de la parada de destino
 * @return void
 */
function loadOccupiedSeats($date, $expedition, $id_origin, $id_destination){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT num_asiento FROM asignacion_asiento 
    WHERE fecha_viaje=? AND id_expedicion=? 
    AND (id_parada_origen BETWEEN ? AND ? OR id_parada_destino BETWEEN ? AND ?)");

    $query->bindParam(1, $date);
    $query->bindParam(2, $expedition);
    if($id_origin > $id_destination){ //BETWEEN no admite comparar entre valor mas alto y valor mas bajo
        $query->bindParam(3, $id_destination);
        $query->bindParam(4, $id_origin);
        $query->bindParam(5, $id_destination);
        $query->bindParam(6, $id_origin);
    }
    else{
        $query->bindParam(3, $id_origin);
        $query->bindParam(4, $id_destination);
        $query->bindParam(5, $id_origin);
        $query->bindParam(6, $id_destination);
    }

    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);

    $seats = [];
    foreach ($resul as $r) {
        $seats[] = $r['num_asiento'];
    };
    echo json_encode($seats);
}

/**
 * Carga el último pago efectuado por el usuario
 *
 * @param String $email Email del usuario que ha pagado
 * @return void
 */
function loadLastPayment($email){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT email, cantidad, metodo, fecha_hora 
    FROM transacciones
    WHERE email=? ORDER BY fecha_hora DESC LIMIT 1");

    $query->bindParam(1, $email);
    $query->execute();
    $r = $query->fetch(PDO::FETCH_ASSOC);

    return $r;
}

/**
 * Carga los últimos 30 registros de actividad de los usuarios (sesiones y modificaciones del perfil)
 *
 * @return void
 */
function loadRegister(){
    $bd = loadBBDD();
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

/**
 * Carga los registros de modificaciones del perfil del usuario activo
 *
 * @return void
 */
function loadLog($type){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT fecha, puntos FROM registro WHERE tipo=? AND usuario=? ORDER BY fecha DESC LIMIT 10");
    $query->bindParam(1, $type);
    $query->bindParam(2, $_SESSION['email']);
    $query->execute();
    $resul = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = [];
    foreach ($resul as $r) {
        $json[] = ['fecha' => $r['fecha'], 'puntos' => $r['puntos']];
    }
    echo json_encode($json);
}

function loadPoints(){
    $bd = loadBBDD();
    $query = $bd->prepare("SELECT puntos FROM usuarios WHERE email=?");
    $query->bindParam(1, $_SESSION['email']);
    $query->execute();

    if($result=$query->fetch()){
        echo $result[0];
    }
}

//ACTUALIZAR DATOS

/**
 * Actualiza las tarifas entre dos paradas
 *
 * @param String $stop1 Parada 1
 * @param String $stop2 Parada 2
 * @param String $value Precio
 * @return void
 */
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

/**
 * Asigna un viaje a un usuario
 *
 * @param String $email Email del viajero principal
 * @param String $date Fecha del viaje
 * @param String $exped Id de la expedición
 * @param String $id_origin Id de la parada de origen
 * @param String $id_destination Id de la parada de destino
 * @param String $seat Número de asiento
 * @param String $dni DNI del viajero si es conocido
 * @return void
 */
function setReservation($email, $date, $exped, $id_origin, $id_destination, $seat, $dni = null){
    $bd = loadBBDD();
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(!is_null($email)){
        $query = $bd->prepare("SELECT dni FROM viajeros WHERE email=? AND principal=1");
        $query->bindParam(1, $email);
        $query->execute();
        $r = $query->fetch(PDO::FETCH_ASSOC);
        $dni = $r['dni'];
    }
    else if(is_null($dni)){
        return 'Ha habido un error con la compra, disculpe las molestias. 
        Si ha sido efectuado un pago y no ha recibido su billete, 
        contacte con Atención al Cliente';
    }

    $bd->beginTransaction();
    try {
        $query = $bd->prepare("INSERT INTO asignacion_asiento VALUES(?,?,?,?,?,?,1)");
        $query->bindParam(1, $dni);
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

/**
 * Actualiza el estado de la reserva
 *
 * @param Boolean $valid Flag para saber si se valida o se deniega la reserva
 * @param String $dni DNI del viajero asignado
 * @param String $date Fecha del viaje
 * @param String $exped Id de la expedición
 * @param String $seat Número de asiento
 * @return void
 */
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

/**
 * Actualiza el rol del usuario (administrador o estándar)
 *
 * @param String $mail Email del usuario
 * @param String $profile Rol al que se cambia el usuario 
 * @return void
 */
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

/**
 * Registra un nuevo usuario en la BD
 *
 * @param String $name Nombre 
 * @param String $surname Apellidos
 * @param String $dni DNI
 * @param String $birth Fecha de nacimiento
 * @param String $phone Número de teléfono
 * @param String $mail Email
 * @param String $pass Contraseña
 * @param String $address Dirección
 * @return void
 */
function registerUser($name, $surname, $dni, $birth, $phone, $mail, $pass, $address){
    $bd = loadBBDD();

    if(!is_null($pass)){ //Si el password es null, no estamos registrando usuario, estamos añadiendo un viajero (SOLO PARA LOS VIAJEROS ADICIONALES EN EL PROCESO DE COMPRA)
        $query_select = $bd->prepare("SELECT COUNT(*) AS cuenta FROM usuarios WHERE email = ?");
        $query_select->bindParam(1, $mail);
        $query_select->execute();
        if($result_select=$query_select->fetch()){
            if($result_select['cuenta']) return "Ya existe una cuenta con este email";
        }

        $query_select2 = $bd->prepare("SELECT COUNT(*) AS cuenta FROM viajeros WHERE dni = ? AND principal = 1");
        $query_select2->bindParam(1, $dni);
        $query_select2->execute();
        if($result_select2=$query_select2->fetch()){
            if($result_select2['cuenta']) return "Ya existe una cuenta vinculada a este dni";
        }
    }

    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bd->beginTransaction();
    try {
        $main = 0;
        if(!is_null($pass)){ //Si el password es null, no estamos registrando usuario, estamos añadiendo un viajero (SOLO PARA LOS VIAJEROS ADICIONALES EN EL PROCESO DE COMPRA)
            $query = $bd->prepare("INSERT INTO usuarios VALUES (?, md5(?), 0, 2)");
            $query->bindParam(1, $mail);
            $query->bindParam(2, $pass);
            $query->execute();

            $main = 1;
        }

        $query2 = $bd->prepare("INSERT INTO viajeros VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $query2->bindParam(1, $dni);
        $query2->bindParam(2, $mail);
        $query2->bindParam(3, utf8_decode($name));
        $query2->bindParam(4, utf8_decode($surname));
        $query2->bindParam(5, $phone);
        $query2->bindParam(6, utf8_decode($address));
        $query2->bindParam(7, $birth);
        $query2->bindParam(8, $main);
        $query2->execute();
        $bd->commit();
        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR EN EL REGISTRO. MOTIVO: ' . $th->getMessage();
    }
}

/**
 * Actualiza la información del usuario registrado
 * Solo permitimos que modifique algunos datos
 *
 * @param String $mail Email del usuario
 * @param String $phone Teléfono
 * @param String $address Dirección
 * @return void
 */
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

/**
 * Añade una transacción monetaria a la BD
 *
 * @param String $mail Email del usuario
 * @param String $quantity Cantidad abonada
 * @param String $method Método de pago
 * @param String $date Fecha y hora de la transacción
 * @return void
 */
function insertPayment($mail, $quantity, $method, $date){
    $bd = loadBBDD();
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("INSERT INTO transacciones VALUES(?, ?, ?, ?)");

        $query->bindParam(1, $mail);
        $query->bindParam(2, $quantity);
        $query->bindParam(3, $method);
        $query->bindParam(4, $date);
        $query->execute();
        $bd->commit();
        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR EN EL REGISTRO. MOTIVO: ' . $th->getMessage();
    }
}

/**
 * Actualiza la tabla de registro de eventos (sesiones y modificaciones de perfil)
 *
 * @param String $mail Email del usuario
 * @param String $date Fecha y hora del evento
 * @param String $type Tipo de evento (sesion/modificacion)
 * @return void
 */
function updateRegister($mail, $date, $type, $points=null){
    $bd = loadBBDD();
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("INSERT INTO registro VALUES(null,?, ?, ?, ?)");

        $query->bindParam(1, $mail);
        $query->bindParam(2, $date);
        $query->bindParam(3, $type);
        $query->bindParam(4, $points); //Usualmente NULL salvo que venga de updatePoints ($type=3)
        $query->execute();
        $bd->commit();
        return 'OK';
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR EN EL REGISTRO. MOTIVO: ' . $th->getMessage();
    }
}
/**
 * Actualiza los puntos que el usuario tiene disponibles en su cuenta
 *
 * @param String $mail
 * @param int $points
 * @return void
 */
function updatePoints($mail, $points){
    $bd = loadBBDD();
    $bd->beginTransaction();
    try {
        $query = $bd->prepare("UPDATE usuarios SET puntos = puntos + ? WHERE email = ?");

        $query->bindParam(1, $points);
        $query->bindParam(2, $mail);
        $query->execute();
        $bd->commit();

        $update = updateRegister($mail, date("Y-m-d H:i:s"), 3, $points);

        return $update;
    } catch (\Throwable $th) {
        $bd->rollBack();
        return 'ERROR EN EL REGISTRO. MOTIVO: ' . $th->getMessage();
    }
}


/**
 * Codifica a utf8 si estamos en prod.
 *
 * @param String $text
 * @return String
 */
function encode($text){
    $prod = false;
    if($prod){
        return utf8_encode($text);
    } else {
        return $text;
    }
}