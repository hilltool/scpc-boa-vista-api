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
     * Base uri to access the API for tests.
     *
     * @var string
     */
    protected $baseTestUri = 'https://bvsntt.bvsnet.com.br';

    /**
     * Base uri to access the API for production.
     *
     * @var string
     */
    protected $baseProductionUri = 'https://www.bvsnet.com.br';

    /**
     * Uri to make the consult.
     *
     * @var string
     */
    protected $queryUri = 'cgi-bin/db2www/netpo028.mbr/string';

    /**
     * Defines if the client is for
     * production or not;
     *
     * @var bool
     */
    protected $production;

    /**
     * The last compiled query.
     *
     * @var string
     */
    protected $lastRawQueryArgs;

    /**
     * The last raw response.
     *
     * @var string
     */
    protected $lastRawResponse;

    /**
     * Client constructor.
     *
     * @param string $code
     * @param string $password
     * @param bool $production
     * @param bool $throwsExceptionOnError
     * @param array $config
     * @internal param $httpClient
     */
    public function __construct($code = '', $password = '', $production = false, $throwsExceptionOnError = true, $config = array())
    {

        $this->code = $code;
        $this->password = $password;
        $this->throwsExceptionOnError = $throwsExceptionOnError;
        $this->production = $production;

        $default = array(
            'base_uri' => $this->getUri(),
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

        $params = $this->appendDefaults($params);

        $queryBuilder = new QueryBuilder($params);

        $query = $queryBuilder->build();

        $this->lastRawQueryArgs = $query;

        $rawResponse = $this->httpClient->get($this->queryUri,
            array('query' => array('consulta' => $query))
        )->getBody()->getContents();

        $responseParser = new ResponseParser($rawResponse);

        $response = $responseParser->parse();

        $this->lastRawResponse = $responseParser->cleanResponse();

        $this->throwExceptionIfHasError($response);

        return $response;

    }

    /**
     * Append default values to keys
     * that were not provided.
     *
     * @param $params
     * @return array
     */
    public function appendDefaults($params)
    {

        $params = $this->addCredentialsToParams($params);

        $params = $this->evaluateDocumentType($params);

        return $params;

    }

    /**
     * Gets the last query executed.
     *
     * @return string
     */
    public function getLastQuery()
    {

        return $this->getUri() . '/' . $this->queryUri . '?consulta=' . $this->lastRawQueryArgs;

    }

    /**
     * Gets the last response received.
     *
     * @return string
     */
    public function getLastResponse()
    {

        return $this->lastRawResponse;

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
     * Set default values to some
     * parameters base on document size.
     *
     * @param $params
     * @return mixed
     */
    protected function evaluateDocumentType($params)
    {

        if (empty($params['12']))
            return $params;

        $document = $params['12'];
        $isCpf = strlen($document) == 11;

        if (empty($params['11']))
            $params['11'] = $isCpf ? '1' : '2';

        if (empty($params['07']))
            $params['07'] = $isCpf ? 'BVSNET4F' : 'BVSNET4J';

        if (empty($params['14']))
            $params['14'] = $isCpf ? 'XX' : 'FI';

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
                $response['12']['999'][0]['03'],
                $response['12']['999'][0]['02']
            );

    }

    /**
     * Gets the uri based on the
     * environment (production or test).
     *
     * @return string
     */
    protected function getUri()
    {

        return $this->production ? $this->baseProductionUri : $this->baseTestUri;

    }

}