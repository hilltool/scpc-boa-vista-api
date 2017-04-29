<?php

use Artenes\SCPCBoaVista\QueryBuilder;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{

    /**
     * @test
     */
    public function builds_query_without_parameters()
    {

        $builder = new QueryBuilder(array());

        $query = $builder->build();

        $expectedQuery = 'CSR60   01                              00000000        BVSNET4F062T100000000000000  XX 000000000000000000000000000000000000000000100000000000N                                                                      0000000000000000000000000000000000000 X"0D"';

        $this->assertEquals($expectedQuery, $query);

    }

    /**
     * @test
     */
    public function builds_query_with_only_documento_and_uf()
    {

        $params = array(

            '12' => '11111111111',
            '13' => 'SP'

        );

        $builder = new QueryBuilder($params);

        $query = $builder->build();

        $expectedQuery = 'CSR60   01                              00000000        BVSNET4F062T100011111111111SPXX 000000000000000000000000000000000000000000100000000000N                                                                      0000000000000000000000000000000000000 X"0D"';

        $this->assertEquals($expectedQuery, $query);

    }

    /**
     * @test
     */
    public function builds_query_for_cnpj()
    {

        $params = array(

            '11' => '2',
            '12' => '11111111111111',

        );

        $builder = new QueryBuilder($params);

        $query = $builder->build();

        $expectedQuery = 'CSR60   01                              00000000        BVSNET4F062T211111111111111  XX 000000000000000000000000000000000000000000100000000000N                                                                      0000000000000000000000000000000000000 X"0D"';

        $this->assertEquals($expectedQuery, $query);

    }

    /**
     * @test
     */
    public function builds_query_with_informacoes_do_cheque_via_digitacao()
    {

        $params = array(

            '16.1' => '999',
            '16.2' => '88888',
            '16.3' => '555555555555555',
            '16.4' => '7',
            '16.5' => '09876543',
            '16.6' => '4',

        );

        $builder = new QueryBuilder($params);

        $query = $builder->build();

        $expectedQuery = 'CSR60   01                              00000000        BVSNET4F062T100000000000000  XX 999888885555555555555557098765434000000000100000000000N                                                                      0000000000000000000000000000000000000 X"0D"';

        $this->assertEquals($expectedQuery, $query);

    }

    /**
     * @test
     */
    public function builds_query_with_informacoes_do_cheque_via_leitura_cmc7()
    {

        $params = array(

            '15' => 'C',
            '16.1' => '93857364',
            '16.2' => '9485736152',
            '16.3' => '637465856743',
            '16.4' => '789',

        );

        $builder = new QueryBuilder($params);

        $query = $builder->build();

        $expectedQuery = 'CSR60   01                              00000000        BVSNET4F062T100000000000000  XXC938573649485736152637465856743789000000000100000000000N                                                                      0000000000000000000000000000000000000 X"0D"';

        $this->assertEquals($expectedQuery, $query);

    }
    
}