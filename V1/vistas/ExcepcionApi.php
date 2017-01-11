<?php

/**
 * Created by PhpStorm.
 * User: Juanito-PC
 * Date: 16/12/2016
 * Time: 18:45
 */
class ExcepcionApi extends Exception
{
    public $estado;

    public function __construct($estado, $mensaje, $codigo = 400)
    {
        $this->estado = $estado;
        $this->message = $mensaje;
        $this->code = $codigo;
    }

}