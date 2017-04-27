<?php

use Artenes\SCPCBoaVista\API;
use PHPUnit\Framework\TestCase;

/**
 * Test for API class.
 *
 * To run the tests make sure to have proper access to Boa Vista's API.
 */
class APITest extends TestCase
{

    /**
     * Code for authentication.
     *
     * @var string
     */
    protected $code = '00000045';

    /**
     * Password for authentication.
     *
     * @var string
     */
    protected $password = 'HOM096';

    /**
     * API's instance.
     *
     * @var API
     */
    protected $api;

    public function setUp()
    {

        $this->api = new API($this->code, $this->password);

    }

    /**
     * @test
     */
    public function throws_exception_when_password_is_invalid()
    {

        $this->expectExceptionMessage('* SENHA INVALIDA');

        $params = [

            'senha' => 'invalida',
            'consulta' => 'BVSNET4F',
            'documento' => '00193000180',
            'uf' => 'PA',
            'tipo_de_credito' => 'XX',

        ];

        $this->api->consult($params);

    }

}