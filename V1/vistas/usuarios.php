<?php

/**
 * Created by PhpStorm.
 * User: Juanito-PC
 * Date: 16/12/2016
 * Time: 20:08
 */

require 'ConexionBD.php';
//require 'estructuras.php';

class usuarios
{
    const ESTADO_URL_INCORRECTA = 2;
    const ESTADO_EXITO = 1;
    const ESTADO_ERROR = 4;
    const ESTADO_FALLA_DESCONOCIDA = 5;
    const ESTADO_ERROR_BD = 6;
    const ESTADO_PARAMETROS_INCORRECTOS = 7;
    const ESTADO_NO_ENCONTRADO = 8;

    const NOMBRE_TABLA = "usuario";
    const ID_USUARIO = "idUsuario";
    const NOMBRE = "Nombre";
    const APELLIDO = "Apellido";
    const DNI = "DNI";
    const CONTRASENA = "Password";
    const DIRECCION = "Domicilio";
    const PERMISO = "Permiso";
    const FOTO = "Foto";

    public static function post($peticion){
        if($peticion[0] == 'registro') {
            return self::registrar();
        } else if($peticion[0] == 'login'){
            return self::loguear();
        }else if($peticion[0] == 'obtenerUsuariosId'){
            return self::obtenerUsuarioId();
        }else if($peticion[0] == 'buscarFoto'){
            return self::buscarFoto();
        }
        else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                                    "Url mal formada", 400);
        }
    }

    public static function get($peticion){
       if($peticion[0] == 'obtenerUsuarios'){
            return self::obtenerUsuarios();
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function put($peticion){
        if(!empty($peticion[0])){
            $body = file_get_contents('php://input');
            $usuario = json_decode($body);

            if(self::actualizar($usuario, $peticion[0]) > 0){
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Usuario actualizado correctamente"
                ];
            }else{
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "El contacto al que intentas acceder no existe", 404);
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
                    "mensaje" => "Usuario eliminado correctamente"
                ];
            }else{
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "El contacto al que intentas acceder no existe", 404);
            }
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA,
                "Url mal formada", 400);
        }
    }

    public static function registrar(){
        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);


        $resultado = self::crear($usuario);

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

        //$id = $datosUsuario->idUsuario;
        $dni = $datosUsuario->DNI;
        $nombre = $datosUsuario->nombre;
        $apellido = $datosUsuario->apellido;

        $contrasena = $datosUsuario->contrasena;
        $contrasenaEncriptada = self::encriptarContrasena($contrasena);
        $direccion = $datosUsuario->direccion;

        if(strcmp($datosUsuario->permiso, "Si")==0){
            $permiso = 1;
        }else{
            $permiso = 0;
        }

        //$estructura = 20;


        try{
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            //Sentencia Insert

            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::DNI . "," .
                self::NOMBRE . "," .
                self::APELLIDO . "," .
                self::CONTRASENA . "," .
                self::DIRECCION . "," .
                self::PERMISO . ")" .
                " VALUES(?,?,?,?,?,?)";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $dni);
            $sentencia->bindParam(2, $nombre);
            $sentencia->bindParam(3, $apellido);
            $sentencia->bindParam(4, $contrasenaEncriptada);
            $sentencia->bindParam(5, $direccion);
            $sentencia->bindParam(6, $permiso);


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

    public static function encriptarContrasena($contrasenaPlana)
    {
        if ($contrasenaPlana)
            return password_hash($contrasenaPlana, PASSWORD_DEFAULT);
        else return null;
    }

    public static function loguear(){
        $respuesta = array();

        $body = file_get_contents('php://input');
        $usuario = json_decode($body);

        $dni = $usuario->DNI;
        $contrasena = $usuario->contrasena;

        if(self::autenticar($dni, $contrasena)){
            $usuarioBD = self::obtenerUsuarioPorDni($dni);

            if($usuarioBD != NULL){
                http_response_code(200);
                $respuesta["idUsuario"] = $usuarioBD["idUsuario"];
                $respuesta["DNI"] = $usuarioBD["DNI"];
                $respuesta["Nombre"] = $usuarioBD["Nombre"];
                $respuesta["Apellido"] = $usuarioBD["Apellido"];
                $respuesta["Direccion"] = $usuarioBD["Domicilio"];
                $respuesta["Permiso"] = $usuarioBD["Permiso"];
                $respuesta["Foto"] = $usuarioBD["Foto"];
                return ["estado" => 1, "usuario" =>$respuesta];

            }else{
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA,
                     "Ha ocurrido un error");
            }
        }else {
            throw new ExcepcionApi(self::ESTADO_PARAMETROS_INCORRECTOS,
                utf8_encode("Correo o contrasena invalidos"));
        }

    }

    public static function autenticar($dni, $contrasena){
        $comando = "SELECT Password FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::DNI . "=?";

        try{

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $dni);


            $sentencia->execute();

            if($sentencia){
                $resultado = $sentencia->fetch();

                if(self::validarContrasena($contrasena, $resultado['Password'])){
                    return true;
                } else {
                    return false;
                }
            }else {
                return false;
            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function validarContrasena($contrasenaPlana, $contrasenaHash){

        return password_verify($contrasenaPlana, $contrasenaHash);
    }

    public static function obtenerUsuarioPorDni($dni){
        $comando = "SELECT " .
            self::ID_USUARIO . "," .
            self::DNI . "," .
            self::NOMBRE . "," .
            self::APELLIDO . "," .
            self::DIRECCION . "," .
            self::PERMISO . "," .
            self::FOTO .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::DNI . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $sentencia->bindParam(1, $dni);
        if($sentencia->execute()){
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        }else{
            return null;
        }

    }

    public static function obtenerUsuarios(){
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

    public static function obtenerUsuarioId(){

        $respuesta = array();
        $body = file_get_contents('php://input');
        $usuario = json_decode($body);
        $idUser = $usuario->idUsuario;

        try{
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
            " WHERE " .self::ID_USUARIO . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $idUser, PDO::PARAM_INT);

            if($sentencia->execute()){

                $usuarioBD= $sentencia->fetch(PDO::FETCH_ASSOC);
                http_response_code(200);

                $respuesta["idUsuario"] = $usuarioBD["idUsuario"];
                $respuesta["DNI"] = $usuarioBD["DNI"];
                $respuesta["Nombre"] = $usuarioBD["Nombre"];
                $respuesta["Apellido"] = $usuarioBD["Apellido"];
                $respuesta["Direccion"] = $usuarioBD["Domicilio"];
                $respuesta["Permiso"] = $usuarioBD["Permiso"];
                $respuesta["Foto"] = $usuarioBD["Foto"];
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

    public static function buscarFoto(){
        $respuesta = array();
        $body = file_get_contents('php://input');
        $dni= json_decode($body);
        $dniUser = $dni->dni;

        try{
            $comando = "SELECT Foto FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::DNI . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $dniUser);

            if($sentencia->execute()){

                $usuarioBD= $sentencia->fetch(PDO::FETCH_ASSOC);
                http_response_code(200);

                $respuesta["Foto"] = $usuarioBD["Foto"];
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

    public static function eliminar($idUsuario){
        try{
            $comando = "DELETE FROM " . self::NOMBRE_TABLA .
                " WHERE " .self::ID_USUARIO . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $idUsuario);

            self::quitarEstructuras($idUsuario);

            self::quitarDefectos($idUsuario);

            $sentencia->execute();

            return $sentencia->rowCount();

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function quitarDefectos($idUsuario){
        try{

            $comando2 = "SELECT idDefecto FROM defecto WHERE Usuario =?";

            $sentencia2 = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando2);

            $sentencia2->bindParam(1, $idUsuario);

            $sentencia2->execute();
            if ($sentencia2->rowCount() > 0) {
                $defectos = $sentencia2->fetchAll(PDO::FETCH_ASSOC);

                foreach ($defectos as $idDefec) {
                    //self::modEstru($idDefec["idDefecto"]);

                    $comando3 = "DELETE FROM " . defecto . " WHERE " . idDefecto . "=?";

                    $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando3);
                    $sentencia->bindParam(1,$idDefec["idDefecto"]);
                    $sentencia->execute();
                    if($sentencia->rowCount() <= 0){
                        throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                            "Ha ocurrido un error", 404);
                    }
                }

            }

        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function quitarEstructuras($idUser){
        try {
            $comando2 = "SELECT idEstructura FROM Estructura WHERE Usuario =?";

            $sentencia2 = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando2);

            $sentencia2->bindParam(1, $idUser);

            $sentencia2->execute();
            if ($sentencia2->rowCount() > 0) {
                $estructuras = $sentencia2->fetchAll(PDO::FETCH_ASSOC);

                foreach ($estructuras as $idEstruc) {

                    self::modEstru($idEstruc["idEstructura"]);
                }

            }
        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }

    public static function modEstru($idEstruc){
        try{
            $comando = "UPDATE estructura SET Usuario = NULL WHERE idEstructura =?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            // $sentencia->bindParam(1, $user);
            $sentencia->bindParam(1,$idEstruc);

            $sentencia->execute();
            if($sentencia->rowCount() <= 0){
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "Ha ocurrido un error", 404);
            }
        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function actualizar($usuario, $idUsuario){

        try{

            $DNI = $usuario->DNI;
            $Nombre = $usuario->nombre;
            $Apellido = $usuario->apellido;
            $contrasena = $usuario->contrasena;
            $direccion = $usuario->direccion;

            if(strcmp($usuario->permiso, "Si")==0){
                $permiso = 1;
            }else{
                $permiso = 0;
            }

            if(strcmp($contrasena, "") != 0){
                $contrasenaEncriptada = self::encriptarContrasena($contrasena);
                $consulta2 = "UPDATE " . self::NOMBRE_TABLA .
                    " SET " . self::CONTRASENA . "=?," .
                    " WHERE " . self::ID_USUARIO . "=?";
                $sentencia2 = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta2);

                $sentencia2->bindParam(1, $contrasenaEncriptada);
                $sentencia2->bindParam(2, $idUsuario);
                $sentencia2->execute();

            }

            $consulta = "UPDATE " . self::NOMBRE_TABLA .
                " SET " .  self::DNI . "=?," .
                self::NOMBRE . "=?," .
                self::APELLIDO . "=?," .
                self::DIRECCION . "=?," .
                self::PERMISO . "=?" .
                " WHERE " . self::ID_USUARIO . "=?";

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $DNI);
            $sentencia->bindParam(2, $Nombre);
            $sentencia->bindParam(3, $Apellido);
            $sentencia->bindParam(4, $direccion);
            $sentencia->bindParam(5, $permiso);
            $sentencia->bindParam(6, $idUsuario);

            $sentencia->execute();

            return $sentencia->rowCount();
        }catch (PDOException $e){
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }
}