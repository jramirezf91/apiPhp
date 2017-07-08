<?php
/**
 * Created by PhpStorm.
 * User: Juanito-PC
 * Date: 16/12/2016
 * Time: 17:12
 */
require 'vistas/VistaJson.php';
require 'vistas/usuarios.php';
require 'vistas/ExcepcionApi.php';
require 'vistas/estructuras.php';
require 'vistas/defectos.php';


const ESTADO_URL_INCORRECTA = 2;
const ESTADO_EXISTENCIA_RECURSO = 3;
const ESTADO_METODO_NO_PERMITIDO = 4;

$vista = new VistaJson();

set_exception_handler(function ($exception) use ($vista){
    $cuerpo = array(
        "estado" => $exception->estado,
        "mensaje" => $exception->getMessage()
    );
    if($exception->getCode()){
        $vista->estado = $exception->getCode();
    }else{
        $vista->estado = 500;
    }

    $vista->imprimir($cuerpo);
});

if (isset($_GET['PATH_INFO']))
    $peticion = explode('/', $_GET['PATH_INFO']);


else
    throw new ExcepcionApi(ESTADO_URL_INCORRECTA,
        utf8_encode("No se reconoce la petición"));

$recurso = array_shift($peticion);
$recursos_existentes = array('estructuras', 'usuarios', 'defectos');


if(!in_array($recurso, $recursos_existentes)){
    throw new ExcepcionApi(ESTADO_EXISTENCIA_RECURSO,
        "No se reconoce el recurso al que intentas acceder");
}

$metodo = strtolower($_SERVER['REQUEST_METHOD']);

//echo $recurso . " --- " . $peticion[0] . " ---- " . $_POST['data'] .
//" --- ".  $metodo . "\n";

switch ($metodo){
    case 'get':
        //procesar metodo get
        if(method_exists($recurso, $metodo)){
            $respuesta = call_user_func(array($recurso, $metodo), $peticion);
            $vista->imprimir($respuesta);
        }
        //$vista->imprimir(usuarios::get($peticion));
        break;
    case 'post':
        //procesar metodo post
        if(method_exists($recurso, $metodo)){
            $respuesta = call_user_func(array($recurso, $metodo), $peticion);
            $vista->imprimir($respuesta);
        }
        //$vista->imprimir(usuarios::post($peticion));
        break;
    case 'put':
        //procesar metodo put
        if(method_exists($recurso, $metodo)){
            $respuesta = call_user_func(array($recurso, $metodo), $peticion);
            $vista->imprimir($respuesta);
        }
        //$vista->imprimir(usuarios::put($peticion));
        break;
    case 'delete':
        //procesat emtoto delete
        if(method_exists($recurso, $metodo)){
            $respuesta = call_user_func(array($recurso, $metodo), $peticion);
            $vista->imprimir($respuesta);
        }
        //$vista->imprimir(usuarios::delete($peticion));
        break;
    default:
        //metodo no aceptado
        $vista->estado = 405;
        $cuerpo = [
            "estado" => ESTADO_METODO_NO_PERMITIDO,
            "mensaje" => utf8_encode("Metodo no permitido " . $metodo)
        ];
        $vista->imprimir($cuerpo);

}