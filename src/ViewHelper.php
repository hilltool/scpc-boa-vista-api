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
     * Checks if the response is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {

        $isEmpty = true;

        foreach ($this->response['12'] as $section) {

            if ($section[0]['03'] == 'S') {
                $isEmpty = false;
                break;
            }

        }

        return $isEmpty && !$this->hasError();

    }

    /**
     * Get the current response.
     *
     * @return array
     */
    public function getResponse()
    {

        return $this->response;

    }

    /**
     * Checks if the item nº12 has the given section.
     *
     * @param $section
     * @return bool
     */
    public function has($section)
    {

        return !empty($this->response['12'][$section]) && $this->response['12'][$section][0]['03'] != 'N';

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
        return $this->response['12']['999'][0]['03'];
    }

    /**
     * Get the formated content from a section of the item nº12.
     *
     * @param $name
     * @return array
     */
    public function get($name)
    {

        $formatedSection = array();

        foreach ($this->response['12'][$name] as $index => $section)
            $formatedSection[] = $this->format($name, $index);

        return $formatedSection;

    }

    /**
     * Format the content of a section.
     *
     * @param $section
     * @param $index
     * @return array
     */
    public function format($section, $index)
    {

        switch ($section) {

            case '249':
                return array(
                    'nome' => $this->getValue($section, $index, '04'),
                    'cpf' => $this->getValue($section, $index, '05'),
                    'data_de_nascimento' => $this->formatDate($this->getValue($section, $index, '06')),
                    'nome_da_mae' => $this->getValue($section, $index, '07'),
                    'titulo_de_eleitor' => $this->getValue($section, $index, '08'),
                    'condicao' => $this->convertCondition($this->getValue($section, $index, '09')),
                    'data_da_consulta' => $this->formatDate($this->getValue($section, $index, '10')),
                    'hora_da_consulta' => $this->formatHour($this->getValue($section, $index, '11')),
                    'protocolo' => $this->getValue($section, $index, '12'),
                );
            case '123':
                return array(
                    'texto' => $this->getValue($section, $index, '04'),
                    'tipo' => $this->convertAlertType($this->getValue($section, $index, '05')),
                );
            case '124':
                return array(
                    'tipo_de_ocorrencia' => $this->convertDebitOccurrenceType($this->getValue($section, $index, '04')),
                    'contrato' => $this->getValue($section, $index, '05'),
                    'data_da_ocorrencia' => $this->formatDate($this->getValue($section, $index, '06')),
                    'data_da_disponibilizacao' => $this->formatDate($this->getValue($section, $index, '07')),
                    'moeda' => $this->getValue($section, $index, '08'),
                    'valor' => $this->formatDecimalPoint($this->getValue($section, $index, '09')),
                    'situacao' => $this->convertSituation($this->getValue($section, $index, '10')),
                    'informante' => $this->getValue($section, $index, '11'),
                    'informado_pelo_consulente' => $this->convertOption($this->getValue($section, $index, '12')),
                    'cidade' => $this->getValue($section, $index, '13'),
                    'uf' => $this->getValue($section, $index, '14'),
                    'condicao' => $this->convertDebitCondition($this->getValue($section, $index, '15')),
                );
            case '141':
                return array(
                    'total' => $this->getValue($section, $index, '04'),
                    'data_primeiro_debito' => $this->formatDate($this->getValue($section, $index, '05')),
                    'data_ultimo_debito' => $this->formatDate($this->getValue($section, $index, '06')),
                    'moeda' => $this->getValue($section, $index, '07'),
                    'valor_acumulado' => $this->formatDecimalPoint($this->getValue($section, $index, '08')),
                );
            case '146':
                return array(
                    'total' => $this->getValue($section, $index, '04'),
                    'uf' => $this->getValue($section, $index, '05'),
                    'periodo_inicial' => $this->formatDate($this->getValue($section, $index, '06')),
                    'periodo_final' => $this->formatDate($this->getValue($section, $index, '07')),
                    'moeda' => $this->getValue($section, $index, '08'),
                    'valor_acumulado' => $this->formatDecimalPoint($this->getValue($section, $index, '09')),
                );
            case '111':
                return array(
                    'total' => $this->getValue($section, $index, '04'),
                    'data_primeira_consulta' => $this->formatDate($this->getValue($section, $index, '05')),
                    'data_ultima_consulta' => $this->formatDate($this->getValue($section, $index, '06')),
                );
            case '211':
                return array(
                    'tipo_de_ocorrencia' => $this->convertOccurrenceType($this->getValue($section, $index, '04')),
                    'tipo_de_documento' => $this->convertDocumentType($this->getValue($section, $index, '05')),
                    'numero_do_documento' => $this->getValue($section, $index, '06'),
                    'banco' => $this->getValue($section, $index, '07'),
                    'agencia' => $this->getValue($section, $index, '08'),
                    'conta_corrente' => $this->getValue($section, $index, '09'),
                    'cheque' => $this->getValue($section, $index, '10'),
                    'alinea' => $this->getValue($section, $index, '11'),
                    'data_da_ocorrencia' => $this->formatDate($this->getValue($section, $index, '12')),
                    'data_da_disponibilizacao' => $this->formatDate($this->getValue($section, $index, '13')),
                    'informante' => $this->formatDate($this->getValue($section, $index, '14')),
                    'indicador' => $this->convertIndicator($this->getValue($section, $index, '15')),
                );
            case '254':
                return array(
                    'tipo_do_documento' => $this->convertDocumentType($this->getValue($section, $index, '04')),
                    'numero_documento' => $this->getValue($section, $index, '05'),
                    'total' => $this->getValue($section, $index, '06'),
                    'data_primeira_ocorrencia' => $this->formatDate($this->getValue($section, $index, '07')),
                    'data_ultima_ocorrencia' => $this->formatDate($this->getValue($section, $index, '08')),
                );
            case '268':
                return array(
                    'tipo_do_documento' => $this->convertDocumentType($this->getValue($section, $index, '04')),
                    'numero_documento' => $this->getValue($section, $index, '05'),
                    'total' => $this->getValue($section, $index, '06'),
                    'data_primeira_devolucao' => $this->formatDate($this->getValue($section, $index, '07')),
                    'data_ultima_devolucao' => $this->formatDate($this->getValue($section, $index, '08')),
                );
            case '256':
                return array(
                    'tipo_do_documento' => $this->convertDocumentType($this->getValue($section, $index, '04')),
                    'numero_documento' => $this->getValue($section, $index, '05'),
                    'total' => $this->getValue($section, $index, '06'),
                    'periodo_inicial' => $this->formatDate($this->getValue($section, $index, '07')),
                    'periodo_final' => $this->formatDate($this->getValue($section, $index, '08')),
                );
            case '127':
                return array(
                    'ddd' => $this->getValue($section, $index, '04'),
                    'telefone' => $this->getValue($section, $index, '05'),
                );
            case '601':
                return array(
                    'tipo_de_score' => $this->convertScoreType($this->getValue($section, $index, '04')),
                    'score' => $this->getValue($section, $index, '05'),
                    'plano_de_execucao' => $this->convertOption($this->getValue($section, $index, '06')),
                    'modelo_plano' => $this->getValue($section, $index, '07'),
                    'nome_plano' => $this->getValue($section, $index, '08'),
                    'modelo_score' => $this->getValue($section, $index, '09'),
                    'nome_score' => $this->getValue($section, $index, '10'),
                    'classificacao_numerica' => $this->getValue($section, $index, '11'),
                    'classificacao_alfabetica' => $this->getValue($section, $index, '12'),
                    'probabilidade' => $this->formatDecimalPoint($this->getValue($section, $index, '13')),
                    'texto_probabilidade' => $this->getValue($section, $index, '14'),
                    'codigo_natureza_modelo' => $this->getValue($section, $index, '15'),
                    'descricao_natureza' => $this->getValue($section, $index, '16'),
                    'texto_natureza' => $this->getValue($section, $index, '17'),
                );
            case '126':
                return array(
                    'tipo_de_ocorrencia' => $this->convertQueryOccurrenceType($this->getValue($section, $index, '04')),
                    'data' => $this->formatDate($this->getValue($section, $index, '05')),
                    'informante' => $this->getValue($section, $index, '06'),
                );
            case '222':
                return array(
                    'cnpj' => $this->getValue($section, $index, '04'),
                    'razao_social' => $this->getValue($section, $index, '05'),
                    'nome_fantasia' => $this->getValue($section, $index, '06'),
                    'condicao' => $this->convertCompanyCondition($this->getValue($section, $index, '07')),
                    'data_de_fundacao' => $this->formatDate($this->getValue($section, $index, '08')),
                );
            case '300':
                return array(
                    'quantidade' => $this->getValue($section, $index, '04'),
                    'moeda' => $this->getValue($section, $index, '05'),
                    'valor_acumulado' => $this->formatDecimalPoint($this->getValue($section, $index, '06')),
                    'data_primeiro_debito' => $this->formatDate($this->getValue($section, $index, '07')),
                    'data_ultimo_debito' => $this->formatDate($this->getValue($section, $index, '08')),
                );
            case '301':
                return array(
                    'tipo' => $this->getValue($section, $index, '04'),
                    'informante' => $this->getValue($section, $index, '05'),
                    'doc_de_origem' => $this->getValue($section, $index, '06'),
                    'data_de_debito' => $this->formatDate($this->getValue($section, $index, '07')),
                    'data_de_disponibilizacao' => $this->formatDate($this->getValue($section, $index, '08')),
                    'cidade' => $this->getValue($section, $index, '09'),
                    'uf' => $this->getValue($section, $index, '10'),
                    'moeda' => $this->getValue($section, $index, '11'),
                    'valor' => $this->getValue($section, $index, '12'),
                    'situacao' => $this->convertSituation($this->getValue($section, $index, '13')),
                    'tipo_do_documento' => $this->convertDocumentType($this->getValue($section, $index, '14')),
                    'numero_do_documento' => $this->getValue($section, $index, '15'),
                );
            case '142':
                return array(
                    'tipo_de_ocorrencia' => 'Título protestado',
                    'cartorio' => $this->getValue($section, $index, '05'),
                    'data_de_ocorrencia' => $this->formatDate($this->getValue($section, $index, '06')),
                    'moeda' => $this->getValue($section, $index, '07'),
                    'valor' => $this->formatDecimalPoint($this->getValue($section, $index, '08')),
                    'cidade' => $this->getValue($section, $index, '09'),
                    'uf' => $this->getValue($section, $index, '10'),
                );
            case '303':
                return array(
                    'quantidade_de_consultas' => $this->getValue($section, $index, '04'),
                    'data_primeira_consulta' => $this->formatDate($this->getValue($section, $index, '05')),
                    'data_ultima_consulta' => $this->formatDate($this->getValue($section, $index, '06')),
                );
            case '304':
                return array(
                    'tipo_de_ocorrencia' => $this->convertQueryOccurrenceType($this->getValue($section, $index, '04')),
                    'data_da_consulta' => $this->formatDate($this->getValue($section, $index, '05')),
                    'informante' => $this->getValue($section, $index, '06'),
                );
            case '242':
                return array(
                    'tipo_de_documento' => $this->convertDocumentType($this->getValue($section, $index, '04')),
                    'numero_do_documento' => $this->getValue($section, $index, '05'),
                    'nome' => $this->getValue($section, $index, '06'),
                    'banco' => $this->getValue($section, $index, '07'),
                    'agencia' => $this->getValue($section, $index, '08'),
                    'motivo_12' => $this->getValue($section, $index, '09'),
                    'data_da_ultima_ocorrencia_12' => $this->formatDate($this->getValue($section, $index, '10')),
                    'motivo_13' => $this->getValue($section, $index, '11'),
                    'data_da_ultima_ocorrencia_13' => $this->formatDate($this->getValue($section, $index, '12')),
                    'motivo_14' => $this->getValue($section, $index, '13'),
                    'data_da_ultima_ocorrencia_14' => $this->formatDate($this->getValue($section, $index, '14')),
                    'motivo_99' => $this->getValue($section, $index, '15'),
                    'data_da_ultima_ocorrencia_99' => $this->formatDate($this->getValue($section, $index, '16')),
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
     * @param $index
     * @return string
     */
    public function getValue($section, $index, $field)
    {

        return $this->response['12'][$section][$index][$field];

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
        $formatedValue = substr($value, 0, -2) . ',' . substr($value, -2);
        return ltrim($formatedValue, '0');
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

    /**
     * Convert debit occurrence type.
     *
     * @param $type
     * @return string
     */
    public function convertDebitOccurrenceType($type)
    {
        switch ($type){
            case 'RG':
                return 'Registrado';
            case 'IA':
                return 'Imob. e Administração de Bens';
            case 'EC':
                return 'Consórcio';
            case 'AL':
                return 'Locadora';
            case 'SF':
                return 'Emp. Crédito Imobiliário';
            case 'OJ':
                return 'Outras Ativ. Econômicas';
            default:
                return 'Título protestado';
        }
    }

    /**
     * Convert situation from letter to word.
     *
     * @param $situtaion
     * @return string
     */
    public function convertSituation($situtaion)
    {
        if ($situtaion == 'C')
            return 'Comprador';
        return 'Avalista';
    }

    /**
     * Convert debi condition from letter to word.
     *
     * @param $condition
     * @return string
     */
    public function convertDebitCondition($condition)
    {
        if ($condition == 'A')
            return 'Ativo';
        return 'Inibido';
    }

    /**
     * Convert query occurrence.
     *
     * @param $type
     * @return string
     */
    public function convertQueryOccurrenceType($type)
    {
        switch ($type){
            case 'CC':
                return 'Cartão de crédito';
            case 'CD':
                return 'Crédtio direto';
            case 'CG':
                return 'Crédito consignado';
            case 'CH':
                return 'Cheque';
            case 'CP':
                return 'Crédito pessoal';
            case 'CV':
                return 'Crédito veículos';
            default:
                return 'Outros';
        }
    }

    /**
     * Convert the company condition from int to string.
     *
     * @param $condition
     * @return string
     */
    public function convertCompanyCondition($condition)
    {
        switch ($condition){
            case '0':
                return 'Ativo';
            case '1':
                return 'Inapto';
            case '2':
                return 'Suspenso';
            case '6':
                return 'Baixado';
            case '7':
                return 'Nulo';
            default:
                return 'Cancelado';
        }
    }

}