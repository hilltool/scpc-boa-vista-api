<?php

namespace Artenes\SCPCBoaVista;

use Exception;

class BoaVistaResponseException extends Exception
{

    public function __construct($message, $code)
    {

        parent::__construct($message, $code);

    }

}