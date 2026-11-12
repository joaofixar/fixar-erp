<?php
/**
 * Tabela T255
 */
// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T256_D009_Id'] = <<<'EOT'
    $retorno = rVerificaFK('D009_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T256_Quantidade'] = <<<'EOT'
    $retorno = rNumeroMaiorQueZero($valor);
EOT;

// triggers
$conf['triggers']['T256']['insertAfter'] = <<<'EOT'
    //require_once('bibliotecas/classes/NOTIFICACOES.php');
    //$NOTIFICACOES = new NOTIFICACOES();
    //$NOTIFICACOES->atualizarNotificacoesT256();
EOT;

$conf['triggers']['T256']['updateAfter'] = <<<'EOT'
    //require_once('bibliotecas/classes/NOTIFICACOES.php');
    //$NOTIFICACOES = new NOTIFICACOES();
    //$NOTIFICACOES->atualizarNotificacoesT256();
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T256'] = <<<'EOT'
    $retorno = true;

    if ($operacao == 'alterar') {
        $sql = mysql_query("SELECT IFNULL(T256_T006_Id,0) T256_T006_Id FROM T256 WHERE T256_Id = '{$valorPk}'");
        $row = mysql_fetch_assoc($sql);
        if($row['T256_T006_Id'] > 0){
            $retorno = 'Este item da ordem de produção está vinculado a um pedido, não é possível realizar modificações';
        }
    }
    if ($operacao == 'alterar' or $operacao == 'excluir') {
        $sql = mysql_query("SELECT T257_Id FROM T257 WHERE T257_T256_Id = '{$valorPk}' LIMIT 1");
        $row = mysql_fetch_assoc($sql);
        if($row['T257_Id'] > 0){
            $retorno = 'Este item da ordem de produção já possui quantidade produzida.';
        }
    }

    if ($operacao == 'incluir') {
        $sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais FROM T255 WHERE T255_Id = '{$valorPkParent}'");
    } else {
        $sql = mysql_query("SELECT T255_Data_Hora_Baixa_Materiais FROM T255 LEFT JOIN T256 ON T255_Id=T256_T255_Id WHERE T256_Id = '{$valorPk}'");
    }
    $row = mysql_fetch_array($sql);
    if ($row['T255_Data_Hora_Baixa_Materiais'] != '0000-00-00 00:00:00') {
        $retorno = 'Este ordem de produção já baixou matéria prima e material de consumo';
    }
    if($campo == 'T256_Data_Previsao_Entrega' || $campo == 'T256_Observacao_Producao'){
        $retorno = true;
    }
EOT;




