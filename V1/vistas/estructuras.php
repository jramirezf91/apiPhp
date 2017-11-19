<?php

/**
 * Created by PhpStorm.
 * User: Juanito-PC
 * Date: 22/12/2016
 * Time: 18:01
 */
class estructuras
{
    const ESTADO_URL_INCORRECTA = 2;
    const ESTADO_EXITO = 1;
    const ESTADO_ERROR = 4;
    const ESTADO_FALLA_DESCONOCIDA = 5;
    const ESTADO_ERROR_BD = 6;
    const ESTADO_PARAMETROS_INCORRECTOS = 7;
    const ESTADO_NO_ENCONTRADO = 8;


    const NOMBRE_TABLA = "estructura";
    const NOMBRE_TABLA2 = "vibracion";
    const ID_ESTRUCTURA = "idEstructura";
    const NOMBRE = "Nombre";
    const DIRECCION = "Direccion";
    const LATITUD = "Latitud";
    const LONGITUD = "Longitud";
    const USUARIO = "Usuario";
    const FECHA = "Fecha";
    const HORA = "Hora";
    const ESTRUCTURA = "Estructura";
    const FOTO = "Foto";

    public static function post($peticion)
    {
        if ($peticion[0] == 'registro') {
            return self::registrar();
        } else if ($peticion[0] == 'obtenerEstructurasId') {
            return self::obtenerEstructurasId();
        } else if ($peticion[0] == 'obtenerEstructurasUser') {
            return self::obtenerEstructurasUser();
        }else if($peticion[0] == 'anadirEstrUser'){
            return self::anadirEstrUser();
        }else if($peticion[0] == 'eliminarEstrUser'){
            return self::eliminarEstrUser();
        }else if($peticion[0] == 'obtenerEstruc') {
            return self::obtenerEstruc();
        }else if($peticion[0] == 'obtenerEstrNom'){
            return self::obtenerEstructurasNom();
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function get($peticion){
        if($peticion[0] == 'obtenerEstructuras'){
            return self::obtenerEstructuras();
        }else if($peticion[0] == 'estructurasSinUsuario'){
            return self::estructurasSinUsuario();
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function put($peticion){
        if(!empty($peticion[0])){
            $body = file_get_contents('php://input');
            $estructura = json_decode($body);

            if(self::actualizar($estructura, $peticion[0]) > 0){
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Estructura actualizada correctamente"
                ];
            }else{
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "La estructura a la que intentas acceder no existe", 404);
            }
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function delete($peticion){
        if(!empty($peticion[0])){
            if(self::eliminar($peticion[0]) > 0){
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Estructura eliminada correctamente"
                ];
            }else{
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "La estructura a la que intentas acceder no existe", 404);
            }
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function registrar(){
        $cuerpo = file_get_contents('php://input');
        $estructura = json_decode($cuerpo);


        $resultado = self::crear($estructura);

        switch ($resultado){
            case self::ESTADO_EXITO:
                http_response_code(200);
                return
                    [
                        "estado" => self::ESTADO_EXITO,
                        "mensaje" => utf8_encode("Registro con exito!")
                    ];
                break;
            case self::ESTADO_ERROR:
                throw new ExcepcionApi(self::ESTADO_ERROR, "Ha ocurrido un error");
                break;
            default:
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Falla desconocida", 400);
        }

    }

    public static function crear($datosUsuario){

        $nombre = $datosUsuario->Nombre;
        $direccion = $datosUsuario->Direccion;
        $latitud = $datosUsuario->Latitud;
        $longitud = $datosUsuario->Longitud;
        $foto = $datosUsuario->Foto;

        try{
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            //Sentencia Insert

            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE . "," .
                self::DIRECCION . "," .
                self::LATITUD . "," .
                self::LONGITUD . "," .
                self::FOTO . ")" .
                " VALUES(?,?,?,?,?)";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $direccion);
            $sentencia->bindParam(3, $latitud);
            $sentencia->bindParam(4, $longitud);
            $sentencia->bindParam(5, $foto);

            $resultado = $sentencia->execute();

            if($resultado){
                return self::ESTADO_EXITO;
            }else{
                return self::ESTADO_ERROR;
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD,
                $e->getMessage());
        }

    }

    public static function obtenerEstructurasId(){

        $respuesta = array();
        $body = file_get_contents('php://input');
        $estructura = json_decode($body);
        $idEstruc = $estructura->idEstructura;

        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::ID_ESTRUCTURA . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $idEstruc, PDO::PARAM_INT);

            if($sentencia->execute()){
                if($sentencia->rowCount() >0) {
                    $usuarioBD = $sentencia->fetch(PDO::FETCH_ASSOC);
                    http_response_code(200);

                    $respuesta["idEstructura"] = $usuarioBD["idEstructura"];
                    $respuesta["Nombre"] = $usuarioBD["Nombre"];
                    $respuesta["Direccion"] = $usuarioBD["Direccion"];
                    $respuesta["Latitud"] = $usuarioBD["Latitud"];
                    $respuesta["Longitud"] = $usuarioBD["Longitud"];
                    return
                        [
                            "estado" => self::ESTADO_EXITO,
                            "datos" => $respuesta
                        ];
                }else{
                    return
                        [
                            "estado" => self::ESTADO_NO_ENCONTRADO,
                            "datos" => "No existe la estructura"
                        ];
                }
            }else{
                throw  new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }

    public static function obtenerEstructurasNom(){


        $body = file_get_contents('php://input');
        $estructura = json_decode($body);
        $NomEstruc = $estructura->NomEstructura;

        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::NOMBRE . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $NomEstruc);

            if($sentencia->execute()){
                if($sentencia->rowCount() == 0) {
                    return
                        [
                            "estado" => 0
                        ];
                }else{
                    return
                        [
                            "estado" => 1
                        ];
                }
            }else{
                throw  new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }

    public static function obtenerEstruc(){

        $respuesta = array();
        $body = file_get_contents('php://input');
        $estructura = json_decode($body);
        $idEstruc = $estructura->idEstructura;

        $hoy = date("Y-m-d");
        $anterior = date("Y-m-d", strtotime('-1 month') );

        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::ID_ESTRUCTURA . "=?";

            $comando2 =  "SELECT * FROM " . self::NOMBRE_TABLA2 .
                " WHERE " .self::ESTRUCTURA . "=? AND " .
                self::FECHA . "<=? AND " . self::FECHA . ">=? ORDER BY "
                . self::FECHA . " ASC";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $idEstruc, PDO::PARAM_INT);

            $sentencia2= ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando2);
            $sentencia2->bindParam(1, $idEstruc, PDO::PARAM_INT);
            $sentencia2->bindParam(2, $hoy);
            $sentencia2->bindParam(3, $anterior);

            if($sentencia->execute() && $sentencia2->execute()){

                $usuarioBD= $sentencia->fetch(PDO::FETCH_ASSOC);
                $vib = $sentencia2->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);

                $respuesta["idEstructura"] = $usuarioBD["idEstructura"];
                $respuesta["Nombre"] = $usuarioBD["Nombre"];
                $respuesta["Direccion"] = $usuarioBD["Direccion"];
                $respuesta["Latitud"] = $usuarioBD["Latitud"];
                $respuesta["Longitud"] = $usuarioBD["Longitud"];
                $respuesta["Vibraciones"] = $vib;

                return
                    [
                        "estado" => self::ESTADO_EXITO,
                        "datos" => $respuesta
                    ];
            }else{
                throw  new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }


    }

    public static function obtenerEstructurasUser(){

        $respuesta = array();
        $body = file_get_contents('php://input');
        $estructura = json_decode($body);
        $idUser = $estructura->idUsuario;

        try{
            $comando = "SELECT ". self::ID_ESTRUCTURA . "," . self::NOMBRE . " FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::USUARIO . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $idUser, PDO::PARAM_INT);

            if($sentencia->execute()){
                if($sentencia->rowCount() > 0) {
                    $usuarioBD = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                    http_response_code(200);

                    /*$respuesta["idEstructura"] = $usuarioBD["idEstructura"];
                    $respuesta["Nombre"] = $usuarioBD["Nombre"];
                    $respuesta["Direccion"] = $usuarioBD["Direccion"];
                    $respuesta["Latitud"] = $usuarioBD["Latitud"];
                    $respuesta["Longitud"] = $usuarioBD["Longitud"];*/
                    return
                        [
                            "estado" => self::ESTADO_EXITO,
                            "datos" => $usuarioBD
                        ];
                }else{
                    return
                    [
                        "estado" => self::ESTADO_NO_ENCONTRADO,
                        "mensaje" => "El usuario no tiene estructuras asociadas",
                        "datos" => null

                    ];
                }
            }else{
                throw  new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }

    public static function obtenerEstructuras(){
        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA;

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            if($sentencia->execute()){
                http_response_code(200);
                return
                    [
                        "estado" => self::ESTADO_EXITO,
                        "datos" => $sentencia->fetchAll(PDO::FETCH_ASSOC)
                    ];
            }else{
                throw  new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function estructurasSinUsuario(){
        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                " WHERE " . self::USUARIO . " IS NULL";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            if($sentencia->execute()){
                if($sentencia->rowCount() > 0) {
                    http_response_code(200);
                    return
                        [
                            "estado" => self::ESTADO_EXITO,
                            "datos" => $sentencia->fetchAll(PDO::FETCH_ASSOC)
                        ];
                }else{
                    return
                        [
                            "estado" => self::ESTADO_NO_ENCONTRADO,
                            "mensaje" => "No hay estructuras sin asignar",
                            "datos" => null
                        ];
                }
            }else{
                throw  new ExcepcionApi(self::ESTADO_ERROR, "Error en la sentencia");
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function anadirEstrUser(){

        $cuerpo = file_get_contents('php://input');
        $estructura = json_decode($cuerpo);



        $user = $estructura->idUsuario;
        $estruc = $estructura->estructura;

        try{
            $comando = "UPDATE " . self::NOMBRE_TABLA .
                " SET " .  self::USUARIO . "=?" .
                " WHERE " . self::ID_ESTRUCTURA . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $user);
            $sentencia->bindParam(2,$estruc);

            //echo $sentencia->queryString;

            $sentencia->execute();
            if($sentencia->rowCount() > 0){
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Estructura añadida correctamente"
                ];
            }else{
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "Ha ocurrido un error", 404);
            }


        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function eliminarEstrUser(){

        $cuerpo = file_get_contents('php://input');
        $estructura = json_decode($cuerpo);

        //$user = $estructura->idUsuario;
        $estruc = $estructura->estructura;

        try{
            $comando = "UPDATE " . self::NOMBRE_TABLA .
                " SET " .  self::USUARIO . "=NULL" .
                " WHERE " . self::ID_ESTRUCTURA . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

           // $sentencia->bindParam(1, $user);
            $sentencia->bindParam(1,$estruc);

            $sentencia->execute();
            if($sentencia->rowCount() > 0){
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Estructura eliminada correctamente"
                ];
            }else{
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "Ha ocurrido un error", 404);
            }


        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }

    public static function eliminar($idEstructura){
        try{
            $comando = "DELETE FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::ID_ESTRUCTURA . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $idEstructura);

            $comandovib = "SELECT COUNT(*) FROM " . self::NOMBRE_TABLA2 .
                " WHERE " .self::ESTRUCTURA . "=?";


            $sentencia3 = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comandovib);
            $sentencia3->bindParam(1, $idEstructura);
            $sentencia3->execute();


            if($sentencia3->fetchColumn() > 0){

                $comando2 = "DELETE FROM " .self::NOMBRE_TABLA2 .
                    " WHERE " . self::ESTRUCTURA . "=?";

                $sentencia2 = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando2);
                $sentencia2->bindParam(1, $idEstructura);
                $sentencia2->execute();

            }

            $sentencia->execute();
            return $sentencia->rowCount();


        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function actualizar($estructura, $idEstructura){

        $Nombre = $estructura->Nombre;
        $Direccion = $estructura->Direccion;
        $Latitud = $estructura->Latitud;
        $Longitud = $estructura->Longitud;

        try{
            $consulta = "UPDATE " . self::NOMBRE_TABLA .
                " SET " .  self::NOMBRE . "=?," .
                self::DIRECCION . "=?," .
                self::LATITUD . "=?," .
                self::LONGITUD . "=?" .
                " WHERE " . self::ID_ESTRUCTURA . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $Nombre);
            $sentencia->bindParam(2, $Direccion);
            $sentencia->bindParam(3, $Latitud);
            $sentencia->bindParam(4, $Longitud);
            $sentencia->bindParam(5, $idEstructura);





            $sentencia->execute();

            return $sentencia->rowCount();
        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }


}