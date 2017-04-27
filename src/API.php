<?php

namespace Artenes\SCPCBoaVista;

use GuzzleHttp\Client;

/**
 * Client for SCPC Boa Vista's API.
 *
 * @package Artenes\SCPCBoaVista
 */
class API
{

    /**
     * Guzzle Http client.
     *
     * @var Client
     */
    protected $httpClient;

    /**
     * Base uri to access the API.
     *
     * @var string
     */
    protected $baseUri = 'https://bvsntt.bvsnet.com.br';

    /**
     * Uri to make the consult.
     *
     * @var string
     */
    protected $consultUri = 'cgi-bin/db2www/netpo028.mbr/string';

    /**
     * These are the fields that must be sent to the API.
     * Each one has a size in bytes, since the request
     * data must be in a positional string.
     *
     * @var array
     */
    protected $requestFields = array(

        'transacao' => 8,
        'versao' => 2,
        'reservado_solicitante' => 10,
        'reservado_boa_vista_servicos' => 20,
        'codigo' => 8,
        'senha' => 8,
        'consulta' => 8,
        'versao_da_consulta' => 2,
        'tipo_de_resposta' => 1,
        'tipo_de_transmissao_da_resposta' => 1,
        'tipo_de_documento' => 1,
        'documento' => 14,
        'uf' => 2,
        'tipo_de_credito' => 2,
        'origem_das_informacoes' => 1,
        'informacoes_do_cheque' => 33,
        'data_do_cheque' => 8,
        'quantidade' => 2,
        'valor' => 11,
        'score_credito' => 1,
        'score' => 70,
        'cep_para_confirmacao' => 8,
        'ddd' => 4,
        'telefone' => 9,
        'cep_de_origem' => 8,
        'facilitador' => 8,
        'opcao_analise_capacidade_de_pagamento' => 1,
        'indicador_de_fim_de_texto' => 1

    );

    /**
     * These are the fields that will be returned by the API.
     * Each one has a size in bytes, since the response
     * data it is in a positional string.
     *
     * @var array
     */
    protected $responseFields = array(

        'transacao' => 8,
        'versao' => 2,
        'reservado_solicitante' => 10,
        'reservado_boa_vista_servicos' => 20,
        'codigo' => 8,
        'consulta' => 8,
        'versao_da_consulta' => 2,
        'tipo_de_resposta' => 1,
        'codigo_de_retorno' => 1,
        'numero_da_consulta' => 7,
        'tamanho_do_texto' => 4,
        'texto' => array(

            'fields' => array(

                'tamanho_do_registro' => 3,
                'tipo_do_registro' => 3,
                'dados_do_registro' => 0,

            ),

        ),

    );

    /**
     * If a field is not informed in a request,
     * we will use one of these default values.
     *
     * @var array
     */
    protected $defaults = array(

        'transacao' => 'CSR60',
        'versao' => '01',
        'reservado_solicitante' => '',
        'reservado_boa_vista' => '',
        'codigo' => '',
        'senha' => '',
        'consulta' => '',
        'versao_da_consulta' => '06',
        'tipo_de_resposta' => '2',
        'tipo_de_transmissao_da_resposta' => 'C',
        'tipo_de_documento' => '1',
        'documento' => '',
        'uf' => '',
        'tipo_de_credito' => '',
        'origem_das_informacoes' => '',
        'informacoes_do_cheque' => '',
        'data_do_cheque' => '',
        'quantidade' => '',
        'valor' => '',
        'score_credito' => 'S',
        'score' => '',
        'cep_para_confirmacao' => '',
        'ddd' => '',
        'telefone' => '',
        'cep_de_origem' => '',
        'facilitador' => '',
        'opcao_analise_capacidade_de_pagamento' => '',
        'indicador_de_fim_de_texto' => 'X"0D"'

    );

    /**
     * Create a new instance.
     *
     * If you don't provide the $code and $password,
     * you MUST provide them in the consult method.
     *
     * @param string $code
     * @param string $password
     */
    public function __construct($code = '', $password = '')
    {

        $this->httpClient = new Client(array(

            'base_uri' => $this->baseUri,
            'verify' => false

        ));

        $this->defaults['codigo'] = $code;
        $this->defaults['senha'] = $password;

    }

    /**
     * @param array $params
     * @return array|string
     * @throws BoaVistaResponseException
     */
    public function consult($params = array())
    {

        $consult = $this->paramsToString($params);

        $response = $this->httpClient->get($this->consultUri, array(

            'query' => array(

                'consulta' => $consult,

            ),

        ))->getBody()->getContents();

        $response = $this->cleanResponse($response);

        $response = $this->responseToArray($response, $this->responseFields);

        $this->throwExceptionIfHasError($response);

        return $response;

    }

    /**
     * Convert a set of parameters in array
     * to a positional string. Formatted in a way
     * that the API can understand.
     *
     * @param $params
     * @return string
     */
    protected function paramsToString($params)
    {

        $query = '';

        foreach ($this->requestFields as $field => $size) {

            $value = isset($params[$field]) ? $params[$field] : $this->defaultValue($field);

            $query .= str_pad($value, $size);

        }

        return $query;

    }

    /**
     * Convert a response from a positional string
     * to an array.
     *
     * @param $response
     * @param $expectedFieldsSize
     * @return array
     */
    protected function responseToArray($response, $expectedFieldsSize)
    {

        $responseArray = array();
        $pointer = 0;

        foreach ($expectedFieldsSize as $field => $size) {

            if (is_array($size)) {

                $hasSize = !empty($size['size']);

                $newResponse = $hasSize ? substr($response, $pointer, $size['size']) : substr($response, $pointer);

                $responseArray[$field] = $this->responseToArray($newResponse, $size['fields']);

                if (!$hasSize)
                    break;

                continue;

            }

            $value = $size <= 0 ? substr($response, $pointer) : substr($response, $pointer, $size);
            $responseArray[$field] = trim($value);
            $pointer += $size;

        }

        return $responseArray;

    }

    /**
     * Trim all white spaces and <PRE> tags
     * from the API's response.
     *
     * @param $rawResponse
     * @return string
     */
    protected function cleanResponse($rawResponse)
    {

        return trim(trim(trim($rawResponse), '</PRE>'));

    }

    /**
     * Get a default value for
     * a given request field.
     *
     * @param $field
     * @return mixed|string
     */
    protected function defaultValue($field)
    {

        if (!isset($this->defaults[$field]))
            return '';

        return $this->defaults[$field];

    }

    /**
     * Throws a exception if the API returns
     * an error code.
     *
     * @param $response
     * @throws BoaVistaResponseException
     */
    protected function throwExceptionIfHasError($response)
    {

        if ($response['codigo_de_retorno'] == '9')
            throw new BoaVistaResponseException(
                $response['texto']['dados_do_registro'],
                $response['texto']['tipo_do_registro']
            );

    }

}