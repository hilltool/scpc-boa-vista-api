<?php

use Artenes\SCPCBoaVista\ResponseParser;
use PHPUnit\Framework\TestCase;

class ResponseParsesTest extends TestCase
{

    /**
     * Raw response for tests.
     * Do no edit!
     * @var string
     */
    protected $rawResponse = '<PRE>
CSR61   01                              00000045BVSNET4F062000056031052181249SCELISMAR JOSE DA SILVA                                      0019300018022111982                                                  0000000000000102022017144043D214.BC23.FBD8.D129 004123N045141S000000011812201418122014R$  0000000123123130124SRGXXX2222               1812201418122014R$  00000123123CITAU UNIBANCO S/A                   NSCPC SAO PAULO                SPA004146N025111S000102302201702052017050126SCD02052017CODIGO DE TESTE                     050126SCD12042017CODIGO DE TESTE                     050126SCD11042017CODIGO DE TESTE                     050126SCD10042017CODIGO DE TESTE                     050126SCD03042017CODIGO DE TESTE                     050126SOU24032017CODIGO DE TESTE                     050126SCD17032017CODIGO DE TESTE                     050126SCD16032017CODIGO DE TESTE                     050126SOU24022017CODIGO DE TESTE                     050126SCC23022017CODIGO DE TESTE                     004211N004254N004268N004256N017127S0064036088800017127S0064036082401017127S0064036084059017127S0064036089593004601N
</PRE>';

    /**
     * @test
     */
    public function return_empty_results_when_raw_response_is_not_provided()
    {

        $parser = new ResponseParser('');

        $response = $parser->parse();

        $this->assertEquals(array(
            '01' => '',
            '02' => '',
            '03' => '',
            '04' => '',
            '05' => '',
            '06' => '',
            '07' => '',
            '08' => '',
            '09' => '',
            '10' => '',
            '11' => '',
            '12' => array(),
        ), $response);

    }

    /**
     * @test
     */
    public function parse_response_meta_information()
    {

        $parser = new ResponseParser($this->rawResponse);

        $response = $parser->parse();

        $this->assertArraySubset(array(
            '01' => 'CSR61',
            '02' => '01',
            '03' => '',
            '04' => '',
            '05' => '00000045',
            '06' => 'BVSNET4F',
            '07' => '06',
            '08' => '2',
            '09' => '0',
            '10' => '0005603',
            '11' => '1052',
        ), $response);

    }

    /**
     * @test
     */
    public function parse_response_text_data()
    {

        $parser = new ResponseParser($this->rawResponse);

        $response = $parser->parse();

        $this->assertArraySubset(array(
            '12' => array(
                '249' => array(
                    '01' => '181',
                    '02' => '249',
                    '03' => 'S',
                    '04' => 'CELISMAR JOSE DA SILVA',
                    '05' => '00193000180',
                    '06' => '22111982',
                    '07' => '',
                    '08' => '0000000000000',
                    '09' => '1',
                    '10' => '02022017',
                    '11' => '144043',
                    '12' => 'D214.BC23.FBD8.D129',
                )
            ),
        ), $response);

    }

}