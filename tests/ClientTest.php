<?php

use Artenes\SCPCBoaVista\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
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
     * @test
     */
    public function make_request()
    {

        $params = array(
            '12' => '00193000180',
            '13' => 'PA'
        );

        $client = new Client($this->code, $this->password);

        $response = $client->query($params);

        $this->assertEquals('0', $response['09']);

    }

    /**
     * @test
     */
    public function throws_exception_when_credentials_are_invalid()
    {

        $this->expectException('Artenes\SCPCBoaVista\BoaVistaResponseException');

        $params = array(
            '12' => '00193000180',
            '13' => 'PA'
        );

        $client = new Client('invalid', 'invalid');

        $client->query($params);

    }

}