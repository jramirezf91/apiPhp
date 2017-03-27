<?php

/**
 * Created by PhpStorm.
 * User: Juanito-PC
 * Date: 16/12/2016
 * Time: 18:37
 */
require_once "VistaApi.php";

class VistaJson extends VistaApi
{

    public function __construct($estado = 400)
    {
        $this->estado = $estado;
    }

    public function imprimir($cuerpo)
    {
        // TODO: Implement imprimir() method.
       // if($this->estado){
         //   http_response_code($this->estado);
       // }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($cuerpo, JSON_PRETTY_PRINT);
        exit;
    }

}