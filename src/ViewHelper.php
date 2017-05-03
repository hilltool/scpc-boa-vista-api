<?php

namespace Artenes\SCPCBoaVista;

/**
 * Class to help format the response from the API.
 * @package Artenes\SCPCBoaVista
 */
class ViewHelper
{

    /**
     * The response from the API.
     *
     * @var array
     */
    protected $response;

    /**
     * ViewHelper constructor.
     *
     * @param array $response
     */
    public function __construct(array $response = array())
    {

        $this->response = $response;

    }

    /**
     * Checks if the item nº12 has the given section.
     *
     * @param $section
     * @return bool
     */
    public function has($section)
    {

        return !empty($this->response['12'][$section]) && $this->response['12'][$section]['03'] != 'N';

    }

    /**
     * Checks if the response has an error.
     *
     * @return bool
     */
    public function hasError()
    {

        return !empty($this->response['12']['999']);

    }

    /**
     * Gets the error message.
     *
     * @return string
     */
    public function getError()
    {
        return $this->response['12']['999']['3'];
    }

    /**
     * Get the formated content from a section of the item nº12.
     *
     * @param $section
     * @return array
     */
    public function get($section)
    {

        switch ($section) {

            case '249':
                return array(
                    'nome' => $this->getValue($section, '04'),
                    'cpf' => $this->getValue($section, '05'),
                    'data_de_nascimento' => $this->formatDate($this->getValue($section, '06')),
                    'nome_da_mae' => $this->getValue($section, '07'),
                    'titulo_de_eleitor' => $this->getValue($section, '08'),
                    'condicao' => $this->convertCondition($this->getValue($section, '09')),
                    'data_da_consulta' => $this->formatDate($this->getValue($section, '10')),
                    'hora_da_consulta' => $this->formatHour($this->getValue($section, '11')),
                    'protocolo' => $this->getValue($section, '12'),
                );
            case '123':
                return array(
                    'texto' => $this->getValue($section, '04'),
                    'tipo' => $this->convertAlertType($this->getValue($section, '05')),
                );
            case '141':
                return array(
                    'total' => $this->getValue($section, '04'),
                    'data_primeiro_debito' => $this->formatDate($this->getValue($section, '05')),
                    'data_ultimo_debito' => $this->formatDate($this->getValue($section, '06')),
                    'moeda' => $this->getValue($section, '07'),
                    'valor_acumulado' => $this->formatDecimalPoint($this->getValue($section, '08')),
                );
            case '146':
                return array(
                    'total' => $this->getValue($section, '04'),
                    'uf' => $this->getValue($section, '05'),
                    'periodo_inicial' => $this->formatDate($this->getValue($section, '06')),
                    'periodo_final' => $this->formatDate($this->getValue($section, '07')),
                    'moeda' => $this->getValue($section, '08'),
                    'valor_acumulado' => $this->formatDecimalPoint($this->getValue($section, '09')),
                );
            case '111':
                return array(
                    'total' => $this->getValue($section, '04'),
                    'data_primeira_consulta' => $this->formatDate($this->getValue($section, '05')),
                    'data_ultima_consulta' => $this->formatDate($this->getValue($section, '06')),
                );
            case '211':
                return array(
                    'tipo_de_ocorrencia' => $this->convertOccurrenceType($this->getValue($section, '04')),
                    'tipo_de_documento' => $this->convertDocumentType($this->getValue($section, '05')),
                    'numero_do_documento' => $this->getValue($section, '06'),
                    'banco' => $this->getValue($section, '07'),
                    'agencia' => $this->getValue($section, '08'),
                    'conta_corrente' => $this->getValue($section, '09'),
                    'cheque' => $this->getValue($section, '10'),
                    'alinea' => $this->getValue($section, '11'),
                    'data_da_ocorrencia' => $this->formatDate($this->getValue($section, '12')),
                    'data_da_disponibilizacao' => $this->formatDate($this->getValue($section, '13')),
                    'informante' => $this->formatDate($this->getValue($section, '14')),
                    'indicador' => $this->convertIndicator($this->getValue($section, '15')),
                );
            case '254':
                return array(
                    'tipo_do_documento' => $this->convertDocumentType($this->getValue($section, '04')),
                    'numero_documento' => $this->getValue($section, '05'),
                    'total' => $this->getValue($section, '06'),
                    'data_primeira_ocorrencia' => $this->formatDate($this->getValue($section, '07')),
                    'data_ultima_ocorrencia' => $this->formatDate($this->getValue($section, '08')),
                );
            case '268':
                return array(
                    'tipo_do_documento' => $this->convertDocumentType($this->getValue($section, '04')),
                    'numero_documento' => $this->getValue($section, '05'),
                    'total' => $this->getValue($section, '06'),
                    'data_primeira_devolucao' => $this->formatDate($this->getValue($section, '07')),
                    'data_ultima_devolucao' => $this->formatDate($this->getValue($section, '08')),
                );
            case '256':
                return array(
                    'tipo_do_documento' => $this->convertDocumentType($this->getValue($section, '04')),
                    'numero_documento' => $this->getValue($section, '05'),
                    'total' => $this->getValue($section, '06'),
                    'periodo_inicial' => $this->formatDate($this->getValue($section, '07')),
                    'periodo_final' => $this->formatDate($this->getValue($section, '08')),
                );
            case '127':
                return array(
                    'ddd' => $this->getValue($section, '04'),
                    'telefone' => $this->getValue($section, '05'),
                );
            case '601':
                return array(
                    'tipo_de_score' => $this->convertScoreType($this->getValue($section, '04')),
                    'score' => $this->getValue($section, '05'),
                    'plano_de_execucao' => $this->convertOption($this->getValue($section, '06')),
                    'modelo_plano' => $this->getValue($section, '07'),
                    'nome_plano' => $this->getValue($section, '08'),
                    'modelo_score' => $this->getValue($section, '09'),
                    'nome_score' => $this->getValue($section, '10'),
                    'classificacao_numerica' => $this->getValue($section, '11'),
                    'classificacao_alfabetica' => $this->getValue($section, '12'),
                    'probabilidade' => $this->formatDecimalPoint($this->getValue($section, '13')),
                    'texto_probabilidade' => $this->getValue($section, '14'),
                    'codigo_natureza_modelo' => $this->getValue($section, '15'),
                    'descricao_natureza' => $this->getValue($section, '16'),
                    'texto_natureza' => $this->getValue($section, '17'),
                );

            default:
                return array();

        }

    }

    /**
     * Get a field value from the given section from item nº12.
     *
     * @param $section
     * @param $field
     * @return string
     */
    public function getValue($section, $field)
    {

        return $this->response['12'][$section][$field];

    }

    /**
     * Format a date from
     * DDMMYYYY to DD/MM/YYYY.
     *
     * @param $date
     * @return string
     */
    public function formatDate($date)
    {
        return substr($date, 0, 2) . '/' . substr($date, 2, 2) . '/' . substr($date, 4);
    }

    /**
     * Format hour from
     * HHMMSS to HH:MM:SS.
     *
     * @param $hour
     * @return string
     */
    public function formatHour($hour)
    {
        return substr($hour, 0, 2) . ':' . substr($hour, 2, 2) . ':' . substr($hour, 4);
    }

    /**
     * Format a integer as a decimal point.
     * From 123456 to 1234,56.
     *
     * @param $value
     * @return string
     */
    public function formatDecimalPoint($value)
    {
        return substr($value, 0, -2) . ',' . substr($value, -1, 2);
    }

    /**
     * Convert condition from integer to string.
     *
     * @param $condition
     * @return string
     */
    public function convertCondition($condition)
    {
        switch ($condition) {
            case '1':
                return 'Regular';
            case '2':
                return 'Cancelado';
            case '3':
                return 'Pendente';
            case '4':
                return 'Suspenso';
            case '5':
                return 'Inexistente';
            case '6':
                return 'Dados Incompletos';
            case '7':
                return 'Nula';
            case '8':
                return 'Não especificado';
            default:
                return 'Indefinido';
        }
    }

    /**
     * Convert alert type from integer to string.
     *
     * @param $type
     * @return string
     */
    public function convertAlertType($type)
    {
        switch ($type){
            case '01':
            case '90':
                return 'Alerta de documentos';
            case '02':
                return 'Observações';
            default:
                return 'Erro ao acessar a base operadora';
        }
    }

    /**
     * Convert occurrence from integer to string.
     *
     * @param $type
     * @return string
     */
    public function convertOccurrenceType($type)
    {
        if ($type == '1')
            return 'Cheque';
        return 'Talão';
    }

    /**
     * Convert document type from integer to string.
     *
     * @param $type
     * @return string
     */
    public function convertDocumentType($type)
    {
        if ($type == '1')
            return 'CPF';
        return 'CNPJ';
    }

    /**
     * Convert indicator from integer to string.
     *
     * @param $indicator
     * @return string
     */
    public function convertIndicator($indicator)
    {
        switch($indicator){
            case '1':
                return 'o Docto, Bco, Ag, C/C e Chq Informados';
            case '2':
                return 'o Docto, Bco, C/C e Chq informados, com a ag diferente da Informada';
            case '3':
                return 'o Docto e Bco Informados, com a Ag e/ou c/c diferente da informada';
            default:
                return 'o Bco, Ag e C/C informados, com outro documento diferente do informado';
        }
    }

    /**
     * Convert score from integer to string.
     *
     * @param $type
     * @return string
     */
    public function convertScoreType($type)
    {
        if ($type == '1')
            return 'PF';
        return 'PJ';
    }

    /**
     * Convert S or N to Sim or Não.
     *
     * @param $option
     * @return string
     */
    public function convertOption($option)
    {
        if ($option == 'S')
            return 'Sim';
        return 'Não';
    }

}