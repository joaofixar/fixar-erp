<?php
namespace hardness;

abstract class TipoCampo
{
    const Texto = 'Texto';
    const Inteiro = 'Inteiro';
    const Decimal = 'Decimal';
    const Data = 'Data';
    const DataHora = 'DataHora';
}

// Define um Campo de uma Tabela
class Campo
{
    public $nome;
    // TipoCampo
    public $tipo;
    public $ehChavePrimaria;
    public $ehChaveEstrangeira;
    public $chaveEstrangeiraTabela;

    /**
     * Campo constructor.
     * @param $nome
     * @param $tipo
     * @param $ehChavePrimaria
     * @param $ehChaveEstrangeira
     * @param $chaveEstrangeiraTabela
     */
    public function __construct($nome, $tipo, $ehChavePrimaria, $ehChaveEstrangeira, $chaveEstrangeiraTabela)
    {
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->ehChavePrimaria = $ehChavePrimaria;
        $this->ehChaveEstrangeira = $ehChaveEstrangeira;
        $this->chaveEstrangeiraTabela = $chaveEstrangeiraTabela;
    }

    static function tipoCampoMysql($tipo) {
        switch ($tipo) {
            case TipoCampo::Texto:
                return "text";
            case TipoCampo::Data:
                return "date";
            case TipoCampo::DataHora:
                return "datetime";
            case TipoCampo::Decimal:
                return "decimal(20,2)";
            case TipoCampo::Inteiro:
            case "Dados":
                return "int";
            default:
                return $tipo;
        }
    }

    static function tipoMysqlCampo($tipo) {
        if (preg_match("/int/", $tipo)) {
            return TipoCampo::Inteiro;
        } else if (preg_match("/(varchar|text|char|enum)/", $tipo)) {
            return TipoCampo::Texto;
        } else if (preg_match("/decimal/", $tipo)) {
            return TipoCampo::Decimal;
        } else if (preg_match("/(datetime|timestamp)/", $tipo)) {
            return TipoCampo::DataHora;
        } else if (preg_match("/date/", $tipo)) {
            return TipoCampo::Data;
        } else {
            return $tipo;
        }
    }
}
