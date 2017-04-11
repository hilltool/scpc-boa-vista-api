<?php

use Artenes\SCPCBoaVista\API;
use Artenes\SCPCBoaVista\BoaVistaResponseException;
use PHPUnit\Framework\TestCase;

class APITest extends TestCase
{

    /**
     * @test
     */
    public function throws_exception_when_password_is_invalid()
    {

        $this->expectException(BoaVistaResponseException::class);

        $code = '00000045';
        $password = 'M2110A';

        $api = new API($code, $password);

        $params = [

            'consulta' => 'BVSNET4F',
            'documento' => '00193000180',
            'uf' => 'PA',
            'tipo_de_credito' => 'XX',

        ];

        $api->consult($params);

    }

}