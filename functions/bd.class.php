<?php

class BD {

    private $ip;
    private $nombre;
    private $usuario;
    private $clave;
    private $bd;

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

    function __construct() {

        $datos = $this->readConfig(dirname(__FILE__) . "/../config/configuracion.xml", dirname(__FILE__) . "/../config/configuracion.xsd", $this->getPerfilUsuario());

        $this->nombre = $datos['nombre'];
        $this->ip = $datos['ip'];
        $this->perfil = $datos['usuario'];
        $this->clave = $datos['clave'];

        $this->PDO = $this->loadBBDD();
    }

    function loadBBDD() {
        try {
            $bd = new \PDO("mysql:host={$this->ip};dbname={$this->nombre};charset=utf8", $this->usuario, $this->clave);
            return $bd;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }

    public function readConfig($fichero_config_BBDD, $esquema, $usuario) {

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
        
        $resul = [];
        $resul["ip"] = $ip[0];
        $resul["nombre"] = $nombre[0];
        $resul["usuario"] = $usu[0];
        $resul["clave"] = $clave[0];
        return $resul;
    }
}