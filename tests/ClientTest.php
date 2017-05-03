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
            '13' => 'SP'
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

    /**
     * @test
     */
    public function append_default_values_to_params()
    {

        $params = array(
            '12' => '00193000180',
            '13' => 'PA'
        );

        $client = new Client('code', 'password');

        $this->assertEquals(array(
            '12' => '00193000180',
            '13' => 'PA',
            '05' => 'code',
            '06' => 'password',
            '11' => '1',
            '07' => 'BVSNET4F',
            '14' => 'XX',
        ), $client->appendDefaults($params));

        $params = array(
            '12' => '84726475263763',
            '13' => 'SP'
        );

        $this->assertEquals(array(
            '12' => '84726475263763',
            '13' => 'SP',
            '05' => 'code',
            '06' => 'password',
            '11' => '2',
            '07' => 'BVSNET4J',
            '14' => 'FI'
        ), $client->appendDefaults($params));

    }

}