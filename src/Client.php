<?php

namespace Artenes\SCPCBoaVista;

/**
 * SCPC Boa Vista API client.
 *
 * @package Artenes\SCPCBoaVista
 */
class Client
{

    /**
     * The http client to make requests.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Code for authentication.
     *
     * @var string
     */
    protected $code;

    /**
     * Password for authentication.
     *
     * @var string
     */
    protected $password;

    /**
     * Defines if an exception will be thrown
     * if the API returns an error code.
     *
     * @var
     */
    protected $throwsExceptionOnError;

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
    protected $queryUri = 'cgi-bin/db2www/netpo028.mbr/string';

    /**
     * Client constructor.
     *
     * @param string $code
     * @param string $password
     * @param array $config
     * @param bool $throwsExceptionOnError
     * @internal param $httpClient
     */
    public function __construct($code = '', $password = '', $config = array(), $throwsExceptionOnError = true)
    {

        $this->code = $code;
        $this->password = $password;
        $this->throwsExceptionOnError = $throwsExceptionOnError;

        $default = array(
            'base_uri' => $this->baseUri,
            'verify' => false
        );

        $this->httpClient = new \GuzzleHttp\Client(array_merge($default, $config));

    }

    /**
     * Makes a query.
     *
     * @param $params
     * @return array
     */
    public function query($params)
    {

        $queryBuilder = new QueryBuilder($this->addCredentialsToParams($params));

        $query = $queryBuilder->build();

        $rawResponse = $this->httpClient->get($this->queryUri,
            array('query' => array('consulta' => $query))
        )->getBody()->getContents();

        $responseParser = new ResponseParser($rawResponse);

        $response = $responseParser->parse();

        $this->throwExceptionIfHasError($response);

        return $response;

    }

    /**
     * Add the credentials from the class' attributes
     * to the params array.
     *
     * @param $params
     * @return array
     */
    protected function addCredentialsToParams($params)
    {

        $params['05'] = empty($params['05']) ? $this->code : $params['05'];
        $params['06'] = empty($params['06']) ? $this->password : $params['06'];

        return $params;

    }

    /**
     * Throws an exception if the API returns
     * an error code.
     *
     * @param $response
     * @throws BoaVistaResponseException
     */
    protected function throwExceptionIfHasError($response)
    {

        if (! $this->throwsExceptionOnError)
            return;

        if ($response['09'] == '9')
            throw new BoaVistaResponseException(
                $response['12']['999']['03'],
                $response['12']['999']['02']
            );

    }

}