<?php
namespace hardness;

use Error;
use Exception;

definirClasse('SMTProduto');
class SMTProduto_{

    /**
     * Adiciona na descrição do produto a quantidade conversão do item
     *
     * @descricao           -> Obrigatório : String
     * @quantidadeConversao -> Obrigatório : Float
     * @unMedida            -> Obrigatório : String
     * 
     */
    public function descricaoConversao($descricao, $quantidadeConversao, $d001_Id) {
        global $g;

        if ($g['C031']['utilizaQuantidadeConversao'] != 'S') {
            return $descricao;
        }

        $sqlProduto  = " SELECT D002A.D002A_Utiliza_Conversao, ";
        $sqlProduto .=        " D037.D037_Unidade ";
        $sqlProduto .=   " FROM D001 ";
        $sqlProduto .=        " LEFT JOIN D037  ON (D037.D037_Id = D001.D001_D037_Id) ";
        $sqlProduto .=        " LEFT JOIN D002  ON (D002.D002_Id = D001.D001_D002_Id) ";
        $sqlProduto .=        " LEFT JOIN D002A ON (D002.D002_Id = D002A.D002A_D002_Id) ";
        $sqlProduto .=  " WHERE D001.D001_Id = {$d001_Id} ";

        $queryProduto = mysql_query($sqlProduto);
        $dadosProduto = mysql_fetch_assoc($queryProduto);

        if ($dadosProduto['D002A_Utiliza_Conversao'] != 'S') {
            return $descricao;
        }

        $unDescricao = $descricao;
        $unConvPos   = strrpos($descricao, '[');
        if ($unConvPos > 0) {
            $unDescricao = trim(substr($descricao, 0, $unConvPos));
        }

        return $unDescricao . ' [' . strval(floor($quantidadeConversao)) . ' UN]';
    }
}