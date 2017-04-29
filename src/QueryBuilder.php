<?php

namespace Artenes\SCPCBoaVista;

/**
 * Builds a query from a array of params.
 * @package Artenes\SCPCBoaVista
 */
class QueryBuilder
{

    /**
     * The params to be used in the query.
     *
     * @var
     */
    protected $params;

    /**
     * The compiled query.
     *
     * @var
     */
    protected $query;

    /**
     * QueryBuilder constructor.
     *
     * @param $params
     */
    public function __construct($params)
    {

        $this->params = $params;

    }

    /**
     * Build the query.
     *
     * @return string
     */
    public function build()
    {

        $this->query = '';

        $this->addString('01', 8, 'CSR60');
        $this->addString('02', 2, '01');
        $this->addString('03', 10);
        $this->addString('04', 20);
        $this->addInteger('05', 8);
        $this->addString('06', 8);
        $this->addString('07', 8, 'BVSNET4F');
        $this->addString('08', 2, '06');
        $this->addString('09', 1, '2');
        $this->addString('10', 1, 'T');
        $this->addString('11', 1, '1');
        $this->addInteger('12', 14);
        $this->addString('13', 2);
        $this->addString('14', 2, 'XX');
        $this->addString('15', 1);

        if ($this->getValue('15') == 'C') {

            $this->addInteger('16.1', 8);
            $this->addInteger('16.2', 10);
            $this->addInteger('16.3', 12);
            $this->addString('16.4', 3);

        } else {

            $this->addInteger('16.1', 3);
            $this->addInteger('16.2', 5);
            $this->addInteger('16.3', 15);
            $this->addInteger('16.4', 1);
            $this->addInteger('16.5', 8);
            $this->addInteger('16.6', 1);

        }

        $this->addInteger('17', 8);
        $this->addInteger('18', 2, '1');
        $this->addInteger('19', 11);
        $this->addString('20', 1, 'N');

        if ($this->getValue('20') == 'S') {

            $this->addString('21.1', 2, '07');
            $this->addString('21.2', 1, 'N');
            $this->addInteger('21.3', 4);
            $this->addSpaces(63);

        } else {

            $this->addSpaces(70);

        }

        $this->addInteger('22', 8);
        $this->addInteger('23', 4);
        $this->addInteger('24', 9);
        $this->addInteger('25', 8);
        $this->addInteger('26', 8);
        $this->addString('27', 1);
        $this->addString('28', 1, 'X"0D"');

        return $this->query;

    }

    /**
     * Add a string to the query.
     *
     * @param $param
     * @param $length
     * @param string $default
     * @return $this
     */
    protected function addString($param, $length, $default = '')
    {

        $this->query .= str_pad($this->getValue($param, $default), $length);

        return $this;

    }

    /**
     * Add an integer to the query.
     *
     * @param $param
     * @param $length
     * @param string $default
     * @return $this
     */
    protected function addInteger($param, $length, $default = '')
    {

        $this->query .= str_pad($this->getValue($param, $default), $length, '0', STR_PAD_LEFT);

        return $this;

    }

    /**
     * Add white spaces in the query.
     *
     * @param $amount
     * @return $this
     */
    protected function addSpaces($amount)
    {

        $this->query .= str_pad('', $amount);

        return $this;

    }

    /**
     * Get a value from the params array.
     *
     * @param $param
     * @param string $default
     * @return string
     */
    protected function getValue($param, $default = '')
    {

        return !empty($this->params[$param]) ? $this->params[$param] : $default;

    }

}