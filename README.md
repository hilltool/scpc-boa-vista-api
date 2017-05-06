#Contents

- What's this?
- How to install?
- How to use it?
- How to use it in more details?
    - Creating the ``Client`` instance
    - Organizing the query parameters
    - Dealing with errors
    - Processing the response
    - View Helper
- Testing
- Contact

#What's this?

A php client to consume SCPC Boa Vista's API.

This client was made to consume **only** the service "Protestos Nacionais ou Regionalizados", **version 15.08**.

If the version of this service is different at the time you are reading this, this client **might not work as expected**.

#How to install?


    composer require artenes/scpc-boa-vista-api

#How to use it?

1. Create an instance of the Boa Vista client.


    $client = new Artenes\SCPCBoaVista\Client($code, $password);

2. Make a query.


    $params = [
      '12' => '98765432152', //document number
      '13' => 'SP' //district
    ];
     
    $response = $client->query($params);

3. Process the response.


    $returnCode = $response['09'];

#How to use it in more details?

##Creating the ``Client`` instance

The ``Artenes\SCPCBoaVista\Client`` have **5 optinal parameters**:

    $client = new Artenes\SCPCBoaVista\Client($code, $password, $production, $throwsExceptionOnError, $httpClientConfig);  

- ``$code | string | optional``
    - Your authentication code. If you don't provide a code here, you should provide one in your query.
    - Default: empty string.
- ``$password | string | optional``
    - Your authentication code. If you don't provide a password here, you should provide one in your query.
    - Default: empty string.
- ``$production | boolean | optional``
    - Sets the environment to production or not. This will define which URI to use to make the requests.
    - Default: ``false``.
- ``$throwsExceptionOnError | boolean | optional``
    - Defines if the client should throw an ``Artenes\SCPCBoaVista\BoaVistaResponseException`` when the Boa Vista's API returns a response that contains an error code. If it is `false`, the client will just return the response.
    - Default: ``true``.
- ``$httpClientConfig | array | optional``
    - The client uses ``GuzzleHttp`` to make HTTP requests. If you wish to configure `GuzzleHttp`, pass the configuration options through this parameter. They will be sent straight to `GuzzleHttp` client's constructor.
    - Default: empty array.

##Organizing the query parameters

After creating the client, you will perform a query:

    $client->query($params);
    
``$params`` is an array that will have all the possible parameters that you can send to Boa Vista's API.

Each item of this array must have:
- a **key** that correspond to a **order number (Nº ORD.)** for queries in the Boa Vista's API documentation.
- a value.


    $params = [
      '05' => 'your_code',
      '06' => 'your_password',
      '12' => 'document_number',
      '13' => 'district'
    ];
    
If you provided the ``code`` and `password` to the client's constructor, you don't need to provide them in the parameters array:

    $params = [
      '12' => 'document_number',
      '13' => 'district'
    ];
    
Boa Vista expects more parameters to consider this as a valid query. But the client already sets default values for some required parameters, so you don't need to worry about them.
 
Here we have all the required parameters that the client fills with default values, followed by their order number and default value.
 
- Transação (01) = 'CSR60'.
- Versão (02) = '01'.
- Consulta (07) = 'BVSNET4F' or 'BVSNET4J' (depends on document number type).
- Versão da consulta (08) = '06'.
- Tipo de resposta (09) = '2'.
- Tipo de Transmissão da resposta (10) = 'T'.
- Tipo de Documento (11) = '1' or '2' (depends on document number type).
- Tipo de crédito (14) = 'XX' or 'FI' (depends on document number type).
- Score crédito (20) = 'N'.
- Indicador de fim de texto (28) = 'X"0D"'

Some default values depends on the document number type (CPF or CNPJ). If you provide a document number that has **11 digits**, the client will assume the document number is a CPF, otherwise it will be treated as a CNPJ.

You can override any default value by providing it in the query parameters. For instance, if you want to override the default value for the field 'Score crédito', you just have to provide it in the parameters array:
 
    $params = [
       '12' => 'document_number',
       '13' => 'district',
       '20' => 'S',
     ];

##Dealing with errors

By default the client will throw a ``Artenes\SCPCBoaVista\BoaVistaResponseException`` when the response from the API contains an error code.

This exception will have the code of the error (it will always be 9 because this is the code returned by the API when something is wrong) and the error message returned by the API:

    $client = new new Artenes\SCPCBoaVista\Client();
    
    try {
        
        $response = $client->query([]);
        
    } catch (BoaVistaResponseException $exception) {
        
        echo $exception->getCode(); //9
        echo $exception->getMessage(); //*  Código de acesso inválido
        
    }
    
And yes, the error message will start with an asterisk and some white spaces before the message. This is how the API returns the message for some reason.

If you don't want the client to throw an exception but rather just return the response from the API, pass ``false`` in the fourth parameter in the client's constructor:
 
    $client = new Artenes\SCPCBoaVista\Client($code, $password, $production, false);
    
In this case you have to process the response and check for the return code from the API:

    $response = $client->query([]);
    
    //Checks the return code field.
    if ($response['09'] === '9'){
        
        //Gets the error message.
        echo $response['12']['999'][0]['03'];
    
    }
    
##Processing the response

Each item of the array response contains:

- a **key** with the **order number (Nº ORD.)** of the response field.
- a value.

For instance, if you want to get the return code (not to be confused with http status code) of the response:

    $client = new Client();
    $response = $client->query([]);
    $returnCode = $response['09'];
    
You can see the list of order numbers for each field in the Boa Vista API's documentation.

The real "meat" of the response it is in the key "12", that holds the information about the document number you are querying for.

This item can contain a series of information about the informed document number. Each group of information is mapped to a code (that it is available in Boa Vista API's documentation). Each group can has one or more items. So the item in the key "12" will have this structure:
    
    $response['12'] = [
        
        '100' => [
            ['01' => 'size', '02' => 'type', '03' => 'exists', '04' => 'data', ...],
            ...
        ],
        '123' => [
            ['01' => 'size', '02' => 'type', '03' => 'exists', '04' => 'data', ...],
            ...
        ],
        '126' => [
            ['01' => 'size', '02' => 'type', '03' => 'exists', '04' => 'data', ...],
            ...
        ],
        '142' => [
            ['01' => 'size', '02' => 'type', '03' => 'exists', '04' => 'data', ...],
            ...
        ],
        
    ];

If you wish to access some information in key number '142', you would do:

    $response['12']['142'][0]['04']; //To access the key '04' from the first item.

## View Helper

Accessing the array key by key might get very cumbersome. To solve this issue, this package provides a view helper that will make easier to access the items from the key "12".

To make use of it just pass the receiver reponse from the client:

    $client = new Client();
    $response = $client->query([]);
    $viewHelper = new Artenes\SCPCBoaVista\ViewHelper($response);
    
Here are a list of the available methods:

**isEmpty(): bool**

Checks if the response returned some result. It is possible to search for a valid document number that has no data associated with it, so the API will return an empty result set.

**has(string $section): bool**

Checks if the given section exists in the key "12".
 
    $viewHelper->has('142');
    
**hasError(): bool**

Checks if the response has a error code.

**getError(): string**

Gets the error message from the response (if it exists).

**get(string $section): array**

Formats the given section in the key "12".

Instead of doing this:

    $section = $response['12']['249'];
    foreach($section as $item) {
    
        echo $item['04'];
    
    }

You can do this:
    
    $section = $viewHelper->get('249'); 
    foreach ($section as $item) {
        
        echo $item['nome'];
        
    }
    
This method will return an array containing pretty keys to make the code more readable. Instead of using codes to access a section information, you will use the fields name (in portuguese and in lower case). If you have a field named "Data de ocorrência", you will have the key "data_de_ocorrencia".

Not all sections are implemented. An empty array will be returned in these cases.

**getValue(string $section, int $index, string $field): string**

Gets the value from a item in the given section and index.

Using this method is equivalent of manipulating the response array by its keys:
    
    $data = $response['12']['142'][0]['04'];
    //is equal to
    $data = $viewHelper->getvalue('142', 0, '04');

**formatDate(string $date): string**

Format a date in DDMMYYY to DD/MM/YYYY.

**formatHour(string $hour): string**

Format a hour in HHMMSS to HH:MM:SS.

**formatDecimalPoint(string $value): string**

Format the given number as a two decimal point number.

    echo $viewHelper->formatDecimalPoint('123456789');
    //will print 1234567,89

**convertCondition(string $condition): string**

Convert a condition from integer to string (in portuguese).

This condition corresponds to the item '09' from section '249'.

**convertAlertType(string $type): string**

Convert an alert type from integer to string (in portuguese).

This alert type corresponds to the item '05' from section '123'.

**convertOccurrenceType(string $type): string**

Convert a occurrence type from integer to string (in portuguese).

This occurrence type corresponds to the item '04' from section '211'.

**convertDocumentType(string $type): string**

Convert a document type from integer to string (in portuguese).

**convertIndicator(string $indicator): string**

Convert an indicator from integer to string (in portuguese).

This occurrence type corresponds to the item '15' from section '211'.

**convertScoreType(string $type): string**

Convert the score type from integer to string (in portuguese).

**convertOption(string $type): string**

Convert "S" to "Sim" and "N" to "Não".

**convertDebitOccurrenceType(string $type): string**

Convert the debit occurrence type from integer to string (in portuguese).

**convertSituation(string $situation): string**

Convert a situation from integer to string (in portuguese).

This situation corresponds to the item '13' from section '301' and item '10' from section '124'.

**convertDebitCondition(string $condition): string**

Convert the debit condition from integer to string (in portuguese).

**convertQueryOccurrenceType(string $type): string**

Convert the query occurrence type from integer to string (in portuguese).

**convertCompanyCondition(string $condition): string**

Convert the company condition from integer to string (in portuguese).

#Testing

Just run

    php vendor/phpunit/phpunit/phpunit
    
To run phpunit from the vendor folder.

**Important**, the client test it is an integration test that will try to communicate with the Boa Vista API. In that test you have to provide a valid code and password to authenticate in the API. You also need to have your machine IP registered with Boa Vista system to allow you to make request to their API. 

#Contact

Any problems or any doubt just contact me at artenesama@gmail.com.