<?php

namespace Artenes\SCPCBoaVista;

/**
 * Parse the response from the API as an array.
 *
 * @package Artenes\SCPCBoaVista
 */
class ResponseParser
{

    /**
     * Code that indicates error (from the API).
     */
    const ERROR_TYPE = '999';

    /**
     * The raw response received from the API.
     *
     * @var
     */
    protected $rawResponse;

    /**
     * The striped version of the raw response.
     *
     * @var string
     */
    protected $cleanResponse;

    /**
     * The parsed response in array.
     *
     * @var array
     */
    protected $response;

    /**
     * The pointer that indicates the
     * current position to read from
     * the raw response.
     *
     * @var int
     */
    protected $pointer;

    /**
     * ResponseParser constructor.
     *
     * @param $rawResponse
     */
    public function __construct($rawResponse)
    {

        $this->rawResponse = $rawResponse;
        $this->cleanResponse = $this->cleanResponse();
        $this->pointer = 0;
        $this->response = array();

    }

    /**
     * Parse the response to array.
     *
     * @return array
     */
    public function parse()
    {

        $this->response['01'] = $this->extract(8);
        $this->response['02'] = $this->extract(2);
        $this->response['03'] = $this->extract(10);
        $this->response['04'] = $this->extract(20);
        $this->response['05'] = $this->extract(8);
        $this->response['06'] = $this->extract(8);
        $this->response['07'] = $this->extract(2);
        $this->response['08'] = $this->extract(1);
        $this->response['09'] = $this->extract(1);
        $this->response['10'] = $this->extract(7);
        $this->response['11'] = $this->extract(4);
        $this->response['12'] = array();

        $responseLength = strlen($this->cleanResponse);

        while ($this->pointer < $responseLength) {

            $size = $this->extract(3);
            $type = $this->extract(3);

            $this->response['12'][$type] = array();
            $this->response['12'][$type]['01'] = $size;
            $this->response['12'][$type]['02'] = $type;

            if ($type == self::ERROR_TYPE) {
                $this->response['12'][$type]['03'] = $this->extract(95);
                continue;
            }

            $register = $this->extract(1);
            $this->response['12'][$type]['03'] = $register;
            if ($register != 'S')
                continue;

            $this->addGroup($type);

        }

        return $this->response;

    }

    /**
     * Extract a value from the rawResponse
     * and move the pointer.
     *
     * @param null $size
     * @return string
     */
    protected function extract($size = null)
    {

        $value = $this->nextValue($size);
        $this->pointer += $size;
        return $value;

    }

    /**
     * Get the next value from the rawResponse
     * based on the pointer position.
     *
     * @param null $size
     * @return string
     */
    protected function nextValue($size = null)
    {

        $value = $size != null ? substr($this->cleanResponse, $this->pointer, $size) : substr($this->cleanResponse, $this->pointer);
        return trim($value);

    }

    /**
     * Trim all white spaces and <PRE> tags
     * from the API's response.
     *
     * @return string
     */
    protected function cleanResponse()
    {

        return trim(trim(trim($this->rawResponse), '</PRE>'));

    }

    /**
     * Extract a group from rawResponse (item nº12)
     * and add to the response array.
     *
     * @param $type
     * @return $this
     */
    protected function addGroup($type)
    {

        switch ($type) {

            case '100':
                $this->response['12'][$type]['04'] = $this->extract(11);
                $this->response['12'][$type]['05'] = $this->extract(11);
                $this->response['12'][$type]['06'] = $this->extract(5);
                break;
            case '101':
                $this->response['12'][$type]['04'] = $this->extract(10);
                $this->response['12'][$type]['05'] = $this->extract(50);
                $this->response['12'][$type]['06'] = $this->extract(4);
                break;
            case '111':
                $this->response['12'][$type]['04'] = $this->extract(5);
                $this->response['12'][$type]['05'] = $this->extract(8);
                $this->response['12'][$type]['06'] = $this->extract(8);
                break;
            case '123':
                $this->response['12'][$type]['04'] = $this->extract(79);
                $this->response['12'][$type]['05'] = $this->extract(2);
                break;
            case '124':
                $this->response['12'][$type]['04'] = $this->extract(2);
                $this->response['12'][$type]['05'] = $this->extract(22);
                $this->response['12'][$type]['06'] = $this->extract(8);
                $this->response['12'][$type]['07'] = $this->extract(8);
                $this->response['12'][$type]['08'] = $this->extract(4);
                $this->response['12'][$type]['09'] = $this->extract(11);
                $this->response['12'][$type]['10'] = $this->extract(1);
                $this->response['12'][$type]['11'] = $this->extract(36);
                $this->response['12'][$type]['12'] = $this->extract(1);
                $this->response['12'][$type]['13'] = $this->extract(30);
                $this->response['12'][$type]['14'] = $this->extract(2);
                $this->response['12'][$type]['15'] = $this->extract(1);
                break;
            case '126':
                $this->response['12'][$type]['04'] = $this->extract(2);
                $this->response['12'][$type]['05'] = $this->extract(8);
                $this->response['12'][$type]['06'] = $this->extract(36);
                break;
            case '127':
                $this->response['12'][$type]['04'] = $this->extract(4);
                $this->response['12'][$type]['05'] = $this->extract(9);
                break;
            case '128':
                $this->response['12'][$type]['04'] = $this->extract(5);
                $this->response['12'][$type]['05'] = $this->extract(90);
                break;
            case '141':
                $this->response['12'][$type]['04'] = $this->extract(8);
                $this->response['12'][$type]['05'] = $this->extract(8);
                $this->response['12'][$type]['06'] = $this->extract(8);
                $this->response['12'][$type]['07'] = $this->extract(4);
                $this->response['12'][$type]['08'] = $this->extract(13);
                break;
            case '142':
                $this->response['12'][$type]['04'] = $this->extract(2);
                $this->response['12'][$type]['05'] = $this->extract(8);
                $this->response['12'][$type]['06'] = $this->extract(8);
                $this->response['12'][$type]['07'] = $this->extract(4);
                $this->response['12'][$type]['08'] = $this->extract(11);
                $this->response['12'][$type]['09'] = $this->extract(30);
                $this->response['12'][$type]['10'] = $this->extract(2);
                break;
            case '146':
                $this->response['12'][$type]['04'] = $this->extract(8);
                $this->response['12'][$type]['05'] = $this->extract(2);
                $this->response['12'][$type]['06'] = $this->extract(8);
                $this->response['12'][$type]['07'] = $this->extract(8);
                $this->response['12'][$type]['08'] = $this->extract(4);
                $this->response['12'][$type]['09'] = $this->extract(13);
                break;
            case '211':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(1);
                $this->response['12'][$type]['06'] = $this->extract(14);
                $this->response['12'][$type]['07'] = $this->extract(3);
                $this->response['12'][$type]['08'] = $this->extract(4);
                $this->response['12'][$type]['09'] = $this->extract(15);
                $this->response['12'][$type]['10'] = $this->extract(8);
                $this->response['12'][$type]['11'] = $this->extract(2);
                $this->response['12'][$type]['12'] = $this->extract(8);
                $this->response['12'][$type]['13'] = $this->extract(8);
                $this->response['12'][$type]['14'] = $this->extract(36);
                $this->response['12'][$type]['15'] = $this->extract(1);
                break;
            case '212':
                $this->response['12'][$type]['04'] = $this->extract(3);
                $this->response['12'][$type]['05'] = $this->extract(4);
                $this->response['12'][$type]['06'] = $this->extract(15);
                $this->response['12'][$type]['07'] = $this->extract(1);
                $this->response['12'][$type]['08'] = $this->extract(14);
                break;
            case '213':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(14);
                $this->response['12'][$type]['05'] = $this->extract(1);
                break;
            case '219':
                $this->response['12'][$type]['04'] = $this->extract(14);
                $this->response['12'][$type]['05'] = $this->extract(3);
                $this->response['12'][$type]['06'] = $this->extract(4);
                $this->response['12'][$type]['07'] = $this->extract(15);
                $this->response['12'][$type]['08'] = $this->extract(8);
                $this->response['12'][$type]['09'] = $this->extract(8);
                $this->response['12'][$type]['10'] = $this->extract(1);
                $this->response['12'][$type]['11'] = $this->extract(1);
                break;
            case '222':
                $this->response['12'][$type]['04'] = $this->extract(14);
                $this->response['12'][$type]['05'] = $this->extract(55);
                $this->response['12'][$type]['06'] = $this->extract(55);
                $this->response['12'][$type]['07'] = $this->extract(1);
                $this->response['12'][$type]['08'] = $this->extract(10);
                break;
            case '223':
                $this->response['12'][$type]['04'] = $this->extract(4);
                $this->response['12'][$type]['05'] = $this->extract(9);
                $this->response['12'][$type]['06'] = $this->extract(1);
                $this->response['12'][$type]['07'] = $this->extract(14);
                $this->response['12'][$type]['08'] = $this->extract(60);
                $this->response['12'][$type]['09'] = $this->extract(50);
                $this->response['12'][$type]['10'] = $this->extract(28);
                $this->response['12'][$type]['11'] = $this->extract(8);
                $this->response['12'][$type]['12'] = $this->extract(30);
                $this->response['12'][$type]['13'] = $this->extract(2);
                break;
            case '224':
                $this->response['12'][$type]['04'] = $this->extract(3);
                $this->response['12'][$type]['05'] = $this->extract(40);
                $this->response['12'][$type]['06'] = $this->extract(4);
                $this->response['12'][$type]['07'] = $this->extract(40);
                $this->response['12'][$type]['08'] = $this->extract(55);
                $this->response['12'][$type]['09'] = $this->extract(30);
                $this->response['12'][$type]['10'] = $this->extract(8);
                $this->response['12'][$type]['11'] = $this->extract(30);
                $this->response['12'][$type]['12'] = $this->extract(2);
                $this->response['12'][$type]['13'] = $this->extract(4);
                $this->response['12'][$type]['14'] = $this->extract(2);
                $this->response['12'][$type]['15'] = $this->extract(9);
                $this->response['12'][$type]['16'] = $this->extract(9);
                $this->response['12'][$type]['17'] = $this->extract(40);
                break;
            case '227':
                $this->response['12'][$type]['04'] = $this->extract(8);
                $this->response['12'][$type]['05'] = $this->extract(60);
                $this->response['12'][$type]['06'] = $this->extract(28);
                $this->response['12'][$type]['07'] = $this->extract(30);
                $this->response['12'][$type]['08'] = $this->extract(2);
                break;
            case '242':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(14);
                $this->response['12'][$type]['06'] = $this->extract(50);
                $this->response['12'][$type]['07'] = $this->extract(3);
                $this->response['12'][$type]['08'] = $this->extract(4);
                $this->response['12'][$type]['09'] = $this->extract(3);
                $this->response['12'][$type]['10'] = $this->extract(8);
                $this->response['12'][$type]['11'] = $this->extract(3);
                $this->response['12'][$type]['12'] = $this->extract(8);
                $this->response['12'][$type]['13'] = $this->extract(3);
                $this->response['12'][$type]['14'] = $this->extract(8);
                $this->response['12'][$type]['15'] = $this->extract(3);
                $this->response['12'][$type]['16'] = $this->extract(8);
                break;
            case '244':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(14);
                $this->response['12'][$type]['06'] = $this->extract(3);
                $this->response['12'][$type]['07'] = $this->extract(4);
                $this->response['12'][$type]['08'] = $this->extract(15);
                $this->response['12'][$type]['09'] = $this->extract(8);
                $this->response['12'][$type]['10'] = $this->extract(8);
                $this->response['12'][$type]['11'] = $this->extract(2);
                $this->response['12'][$type]['12'] = $this->extract(8);
                $this->response['12'][$type]['13'] = $this->extract(8);
                $this->response['12'][$type]['14'] = $this->extract(4);
                $this->response['12'][$type]['15'] = $this->extract(11);
                $this->response['12'][$type]['16'] = $this->extract(36);
                $this->response['12'][$type]['17'] = $this->extract(20);
                $this->response['12'][$type]['18'] = $this->extract(2);
                break;
            case '245':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(14);
                $this->response['12'][$type]['06'] = $this->extract(3);
                $this->response['12'][$type]['07'] = $this->extract(4);
                $this->response['12'][$type]['08'] = $this->extract(15);
                $this->response['12'][$type]['09'] = $this->extract(8);
                $this->response['12'][$type]['10'] = $this->extract(8);
                $this->response['12'][$type]['11'] = $this->extract(2);
                $this->response['12'][$type]['12'] = $this->extract(8);
                $this->response['12'][$type]['13'] = $this->extract(8);
                $this->response['12'][$type]['14'] = $this->extract(4);
                $this->response['12'][$type]['15'] = $this->extract(11);
                $this->response['12'][$type]['16'] = $this->extract(36);
                break;
            case '246':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(14);
                $this->response['12'][$type]['06'] = $this->extract(3);
                $this->response['12'][$type]['07'] = $this->extract(4);
                $this->response['12'][$type]['08'] = $this->extract(15);
                $this->response['12'][$type]['09'] = $this->extract(8);
                $this->response['12'][$type]['10'] = $this->extract(8);
                $this->response['12'][$type]['11'] = $this->extract(6);
                $this->response['12'][$type]['12'] = $this->extract(1);
                break;
            case '247':
                $this->response['12'][$type]['04'] = $this->extract(3);
                $this->response['12'][$type]['05'] = $this->extract(4);
                $this->response['12'][$type]['06'] = $this->extract(15);
                $this->response['12'][$type]['07'] = $this->extract(1);
                $this->response['12'][$type]['08'] = $this->extract(14);
                $this->response['12'][$type]['09'] = $this->extract(8);
                $this->response['12'][$type]['10'] = $this->extract(6);
                break;
            case '249':
                $this->response['12'][$type]['04'] = $this->extract(60);
                $this->response['12'][$type]['05'] = $this->extract(11);
                $this->response['12'][$type]['06'] = $this->extract(8);
                $this->response['12'][$type]['07'] = $this->extract(50);
                $this->response['12'][$type]['08'] = $this->extract(13);
                $this->response['12'][$type]['09'] = $this->extract(1);
                $this->response['12'][$type]['10'] = $this->extract(8);
                $this->response['12'][$type]['11'] = $this->extract(6);
                $this->response['12'][$type]['12'] = $this->extract(20);
                break;
            case '254':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(14);
                $this->response['12'][$type]['06'] = $this->extract(5);
                $this->response['12'][$type]['07'] = $this->extract(8);
                $this->response['12'][$type]['08'] = $this->extract(8);
                break;
            case '256':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(14);
                $this->response['12'][$type]['06'] = $this->extract(5);
                $this->response['12'][$type]['07'] = $this->extract(8);
                $this->response['12'][$type]['08'] = $this->extract(8);
                break;
            case '268':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(14);
                $this->response['12'][$type]['06'] = $this->extract(5);
                $this->response['12'][$type]['07'] = $this->extract(8);
                $this->response['12'][$type]['08'] = $this->extract(8);
                break;
            case '300':
                $this->response['12'][$type]['04'] = $this->extract(5);
                $this->response['12'][$type]['05'] = $this->extract(4);
                $this->response['12'][$type]['06'] = $this->extract(17);
                $this->response['12'][$type]['07'] = $this->extract(8);
                $this->response['12'][$type]['08'] = $this->extract(8);
                break;
            case '301':
                $this->response['12'][$type]['04'] = $this->extract(2);
                $this->response['12'][$type]['05'] = $this->extract(50);
                $this->response['12'][$type]['06'] = $this->extract(25);
                $this->response['12'][$type]['07'] = $this->extract(8);
                $this->response['12'][$type]['08'] = $this->extract(8);
                $this->response['12'][$type]['09'] = $this->extract(20);
                $this->response['12'][$type]['10'] = $this->extract(2);
                $this->response['12'][$type]['11'] = $this->extract(4);
                $this->response['12'][$type]['12'] = $this->extract(15);
                $this->response['12'][$type]['13'] = $this->extract(1);
                $this->response['12'][$type]['14'] = $this->extract(1);
                $this->response['12'][$type]['15'] = $this->extract(14);
                break;
            case '303':
                $this->response['12'][$type]['04'] = $this->extract(5);
                $this->response['12'][$type]['05'] = $this->extract(8);
                $this->response['12'][$type]['06'] = $this->extract(8);
                break;
            case '304':
                $this->response['12'][$type]['04'] = $this->extract(2);
                $this->response['12'][$type]['05'] = $this->extract(8);
                $this->response['12'][$type]['06'] = $this->extract(40);
                break;
            case '601':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(4);
                $this->response['12'][$type]['06'] = $this->extract(1);
                $this->response['12'][$type]['07'] = $this->extract(2);
                $this->response['12'][$type]['08'] = $this->extract(40);
                $this->response['12'][$type]['09'] = $this->extract(2);
                $this->response['12'][$type]['10'] = $this->extract(40);
                $this->response['12'][$type]['11'] = $this->extract(2);
                $this->response['12'][$type]['12'] = $this->extract(1);
                $this->response['12'][$type]['13'] = $this->extract(5);
                $this->response['12'][$type]['14'] = $this->extract(200);
                $this->response['12'][$type]['15'] = $this->extract(3);
                $this->response['12'][$type]['16'] = $this->extract(55);
                $this->response['12'][$type]['17'] = $this->extract(90);
                break;
            case '901':
                $this->response['12'][$type]['04'] = $this->extract(1);
                $this->response['12'][$type]['05'] = $this->extract(130);
                break;
            case '940':
                $this->response['12'][$type]['04'] = $this->extract(3);
                $this->response['12'][$type]['05'] = $this->extract(200);
                break;

        }

        return $this;

    }

}