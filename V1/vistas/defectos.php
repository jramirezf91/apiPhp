<?php

/**
 * Created by PhpStorm.
 * User: Juan
 * Date: 18/06/2017
 * Time: 14:24
 */
class defectos
{
    const ESTADO_URL_INCORRECTA = 2;
    const ESTADO_EXITO = 1;
    const ESTADO_ERROR = 4;
    const ESTADO_FALLA_DESCONOCIDA = 5;
    const ESTADO_ERROR_BD = 6;
    const ESTADO_PARAMETROS_INCORRECTOS = 7;
    const ESTADO_NO_ENCONTRADO = 8;


    const NOMBRE_TABLA = "defecto";
    const ID_DEFECTO = "idDefecto";
    const NOMBRE = "Nombre";
    const TIPO = "TipoDefecto";
    const LIMITINF = "LimitInf";
    const LIMITSUP = "LimitSup";
    const USUARIO = "Usuario";
    const DESCRIPCION = "Descripcion";



    public static function post($peticion)
    {
        if ($peticion[0] == 'registro') {
            return self::registrar();
        } else if ($peticion[0] == 'obtenerDefectoId') {
            return self::obtenerDefectoId();
        } else if ($peticion[0] == 'obtenerDefectoUser') {
            return self::obtenerDefectoUser();
        }else if($peticion[0] == 'obtenerDefecto') {
            return self::obtenerDefecto();
        }else if($peticion[0] == 'obtenerDefectoNom'){
            return self::obtenerDefectoNom();
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function get($peticion){
        if($peticion[0] == 'obtenerDefectos'){
            return self::obtenerDefectos();
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function put($peticion){
        if(!empty($peticion[0])){
            $body = file_get_contents('php://input');
            $defecto = json_decode($body);

            if(self::actualizar($defecto, $peticion[0]) > 0){
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Defecto actualizado correctamente"
                ];
            }else{
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "El defecto al que intentas acceder no existe", 404);
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
                    "mensaje" => "Defecto eliminado correctamente"
                ];
            }else{
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "El defecto al que intentas acceder no existe", 404);
            }
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function registrar(){
        $cuerpo = file_get_contents('php://input');
        $defecto = json_decode($cuerpo);


        $resultado = self::crear($defecto);

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

    public static function crear($defecto){

        $nombre = $defecto->Nombre;
        $tipo = $defecto->tipo;
        $limitinf = $defecto->limitinf;
        $limitsup = $defecto->limitsup;
        $descripcion = $defecto->descripcion;
        $usuario = $defecto->usuario;

        try{
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            //Sentencia Insert

            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE . "," .
                self::TIPO . "," .
                self::LIMITINF . "," .
                self::LIMITSUP . "," .
                self::DESCRIPCION . "," .
                self::USUARIO . ")" .
                " VALUES(?,?,?,?,?,?)";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $tipo);
            $sentencia->bindParam(3, $limitinf);
            $sentencia->bindParam(4, $limitsup);
            $sentencia->bindParam(5, $descripcion);
            $sentencia->bindParam(6, $usuario);

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

    public static function obtenerDefectoId(){

        $respuesta = array();
        $body = file_get_contents('php://input');
        $defecto = json_decode($body);
        $idDefec = $defecto->idDefecto;

        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::ID_DEFECTO . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $idDefec, PDO::PARAM_INT);

            if($sentencia->execute()){
                if($sentencia->rowCount() >0) {
                    $usuarioBD = $sentencia->fetch(PDO::FETCH_ASSOC);
                    http_response_code(200);

                    $respuesta["idDefecto"] = $usuarioBD["idDefecto"];
                    $respuesta["Nombre"] = $usuarioBD["Nombre"];
                    $respuesta["Tipo"] = $usuarioBD["Tipo"];
                    $respuesta["LimitInf"] = $usuarioBD["LimitInf"];
                    $respuesta["LimitSup"] = $usuarioBD["LimitSup"];
                    $respuesta["Descripcion"] = $usuarioBD["Descripcion"];
                    return
                        [
                            "estado" => self::ESTADO_EXITO,
                            "datos" => $respuesta
                        ];
                }else{
                    return
                        [
                            "estado" => self::ESTADO_NO_ENCONTRADO,
                            "datos" => "No existe la defecto"
                        ];
                }
            }else{
                throw  new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }

    public static function obtenerDefectoUser(){

        $respuesta = array();
        $body = file_get_contents('php://input');
        $defecto = json_decode($body);
        $idUser = $defecto->idUsuario;

        try{
            $comando = "SELECT ". self::ID_DEFECTO . "," . self::NOMBRE . " FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::USUARIO . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $idUser, PDO::PARAM_INT);

            if($sentencia->execute()){
                if($sentencia->rowCount() > 0) {
                    $usuarioBD = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                    http_response_code(200);

                    return
                        [
                            "estado" => self::ESTADO_EXITO,
                            "datos" => $usuarioBD
                        ];
                }else{
                    return
                        [
                            "estado" => self::ESTADO_NO_ENCONTRADO,
                            "mensaje" => "El usuario no tiene defectos asociados",
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

    public static function obtenerDefectos(){
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

    public static function obtenerDefectoNom(){


        $body = file_get_contents('php://input');
        $defecto = json_decode($body);
        $NomDefect = $defecto->NomEstructura;

        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::NOMBRE . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $NomDefect);

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
        $defecto = json_decode($body);
        $idDefec = $defecto->idEstructura;

        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::ID_DEFECTO . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $idDefec, PDO::PARAM_INT);


            if($sentencia->execute()){

                $usuarioBD= $sentencia->fetch(PDO::FETCH_ASSOC);
                http_response_code(200);

                $respuesta["idDefecto"] = $usuarioBD["idDefecto"];
                $respuesta["Nombre"] = $usuarioBD["Nombre"];
                $respuesta["Tipo"] = $usuarioBD["Tipo"];
                $respuesta["LimiteInf"] = $usuarioBD["LimiteInf"];
                $respuesta["LimiteSup"] = $usuarioBD["LimiteSup"];
                $respuesta["Descripcion"] = $usuarioBD["Descripcion"];

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

    public static function actualizar($defecto, $idDefecto){

        $Nombre = $defecto->Nombre;
        $Tipo = $defecto->Tipo;
        $LimiteInf = $defecto->LimiteInf;
        $LimiteSup = $defecto->LimiteSup;
        $Descripcion = $defecto->Descripcion;

        try{
            $consulta = "UPDATE " . self::NOMBRE_TABLA .
                " SET " .  self::NOMBRE . "=?," .
                self::TIPO . "=?," .
                self::LIMITINF . "=?," .
                self::LIMITSUP . "=?" .
                self::DESCRIPCION . "=?" .
                " WHERE " . self::ID_ESTRUCTURA . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $Nombre);
            $sentencia->bindParam(2, $Tipo);
            $sentencia->bindParam(3, $LimiteInf);
            $sentencia->bindParam(4, $LimiteSup);
            $sentencia->bindParam(5, $Descripcion);
            $sentencia->bindParam(6, $idDefecto);

            $sentencia->execute();

            return $sentencia->rowCount();
        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }

    public static function eliminar($idDefecto){
        try{
            $comando = "DELETE FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::ID_DEFECTO . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $idDefecto);

            $sentencia->execute();


        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

}