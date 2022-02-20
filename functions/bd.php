<?php
/*Si se necesita llamar directamente a la BD desde el front (p.ej. AJAX) 
incluir aquí la referencia al método que se llama*/
if(isset($_POST['method'])){
    switch($_POST['method']){
        case 'loadStops': loadStops(); break;
        case 'getSchedule': getSchedule(); break;
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

    $ret = [];
    foreach ($resul as $r) {
        $json['paradas'][$r['id_expedicion']][] = ['id' => $r['id_parada'], 'name' => $r['nombre'], 'hour' => $r['hora']];
    }
    echo json_encode($json);
}