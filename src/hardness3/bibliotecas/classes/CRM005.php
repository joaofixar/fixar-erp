<?php
namespace hardness;

require_once "old/lib/funcoes.php";

definirClasse('CRM005');
class CRM005_
{
    /**
     * incluirProduto
     *
     * @param Form $form
     * @return true/string
     */
    public function incluirProduto($form)
    {
        global $g;
        
        $T099_D024_Id              = $form->campoValorEnviado('T099_D024_Id');
        $T099_D009_Id              = $form->campoValorEnviado('T099_D009_Id');
        $T099_D037_Id              = $form->campoValorEnviado('T099_D037_Id');
        $T099_Quantidade           = $form->campoValorEnviado('T099_Quantidade');
        $T099_Data                 = mysql_real_escape_string($form->campoValorEnviado('T099_Data'));
        $T099_Valor_Preco_Unitario = $form->campoValorEnviado('T099_Valor_Preco_Unitario');
        $T099_Horario_Entrega      = $form->campoValorEnviado('T099_Horario_Entrega');
        $T099_D009_Id              = $form->campoValorEnviado('T099_D009_Id');
        
        $cClausula = <<<EOT
            SELECT 
                D009_Id,
                D001_D037_Id,
                D041_Valor_Preco_Consumidor,
                D024_Horario_Entrega,
                D001_Peso_Unitario_Kg,
                D001_Peso_Unitario_Kg,
                D001_Peso_Total_Kg,
                D001_Qte_Embalagem,
                D037_Unidade,
                D037_Id 
            FROM D001 
            LEFT JOIN D049 ON D001_Id = D049_D001_Id 
            LEFT JOIN D009 ON D049_Id = D009_D049_Id 
            LEFT JOIN D041 ON D041_D049_Id = D049_Id 
            LEFT JOIN D037 ON D037_Id = '$T099_D037_Id' 
            LEFT JOIN D024 ON D024_Id = '$T099_D024_Id' 
            WHERE
                D009_Id = '$T099_D009_Id' 
EOT;
        $T099 = mysql_query($cClausula);
        
        $mT099 = mysql_fetch_array($T099);
        
        if ($mT099['D001_Peso_Total_Kg'] <= 0) {
            $mT099['D001_Peso_Total_Kg'] = 1;
        }
        if ($mT099['T099_Peso_Unitario'] <= 0) {
            $mT099['T099_Peso_Unitario'] = 1;
        }
        if ($mT099['D001_Qte_Embalagem'] <= 0) {
            $mT099['D001_Qte_Embalagem'] = 1;
        }
        
        if ($mT099['D037_Unidade'] == 'KLG') {
            $Qte_KLG = $mT099['T099_Quantidade'];
            $Qte_PCT = $mT099['T099_Quantidade'] / $mT099['D001_Peso_Total_Kg'];
            $Qte_UND = $mT099['T099_Quantidade'] / $mT099['T099_Peso_Unitario'];
        } else if ($mT099['D037_Unidade'] == 'PCT') {
            $Qte_KLG = $mT099['T099_Quantidade'] * $mT099['D001_Peso_Total_Kg'];
            $Qte_PCT = $mT099['T099_Quantidade'];
            $Qte_UND = $mT099['T099_Quantidade'] * $mT099['D001_Qte_Embalagem'];
        } else if ($mT099['D037_Unidade'] == 'UND') {
            $Qte_KLG = $mT099['T099_Quantidade'] * $mT099['T099_Peso_Unitario'];
            $Qte_PCT = $mT099['T099_Quantidade'] / $mT099['D001_Qte_Embalagem'];
            $Qte_UND = $mT099['T099_Quantidade'];
        }
        
        $T099_Valor_Preco_Unitario = gCorrigeNumeroInverte($T099_Valor_Preco_Unitario);
        $cClausula = <<<EOT
            INSERT INTO T099 (
                T099_Data, 
                T099_D024_Id, 
                T099_D009_Id, 
                T099_D037_Id, 
                T099_C004_Id, 
                T099_Quantidade, 
                T099_Valor_Preco_Unitario, 
                T099_Horario_Entrega, 
                T099_Peso_Unitario 
            ) values ( 
                '$T099_Data', 
                '$T099_D024_Id', 
                '$T099_D009_Id', 
                '$T099_D037_Id', 
                '{$g['empresaAtual']}', 
                '$T099_Quantidade', 
                '$T099_Valor_Preco_Unitario', 
                '$T099_Horario_Entrega', 
                '{$mT099['D001_Peso_Unitario_Kg']}' 
            ) 
EOT;
        mysql_query($cClausula);
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro/p/p$cClausula";
        } else {
            return true;
        }
    }
    
    /**
     * incluirProgramacao
     *
     * @param string $data
     * @param string $dataPedido
     * @param int $D024_Id
     */
    public function incluirProgramacao($data, $dataPedido, $D024_Id)
    {
        global $g;
        
        $sql = <<<EOT
            SELECT 
                T099_D009_Id, 
                T099_D037_Id, 
                T099_Quantidade, 
                T099_Valor_Preco_Unitario, 
                T099_Peso_Unitario, 
                T099_Horario_Entrega, 
                T099_C004_Id 
            FROM T099 
            WHERE 
                T099_Data = '{$dataPedido}' 
                AND T099_D024_Id = '{$D024_Id}'
EOT;
        $T099 = mysql_query($sql);
        
        while ($res = mysql_fetch_array($T099)) {
            $sql = <<<EOT
                INSERT INTO T099 (
                    T099_D024_Id,
                    T099_D009_Id,
                    T099_D037_Id,
                    T099_C004_Id,
                    T099_Data,
                    T099_Quantidade,
                    T099_Valor_Preco_Unitario,
                    T099_Horario_Entrega,
                    T099_Peso_Unitario
                ) VALUES (
                    '{$D024_Id}',
                    '{$res['T099_D009_Id']}',
                    '{$res['T099_D037_Id']}',
                    '{$res['T099_C004_Id']}',
                    '{$data}',
                    '{$res['T099_Quantidade']}',
                    '{$res['T099_Valor_Preco_Unitario']}',
                    '{$res['T099_Horario_Entrega']}',
                    '{$res['T099_Peso_Unitario']}'
                )
EOT;
            mysql_query($sql);
        }
        return true;
    }
    
    /**
     * excluirProduto
     *
     * @param int $T099_Id
     */
    public function excluirProduto($T099_Id)
    {
        $T099_Id = mysql_real_escape_string($T099_Id);
        $sql     = "DELETE FROM T099 WHERE T099_Id='$T099_Id'";
        mysql_query($sql);
        return true;
    }
}
