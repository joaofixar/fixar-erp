<?php
/**
 * Tabela T147
 */

// camposNaoUsar
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
    'T145_D116_Id'
));

// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T147_T005_Id'] = <<<'EOT'
    $retorno = rVerificaFK('T005_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T147_D116_Id'] = <<<'EOT'
    $retorno = rVerificaFK('D116_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T147_Descricao'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T147_Quantidade'] = <<<'EOT'
    $retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T147_Tempo_Estimado'] = <<<'EOT'
    $retorno = rTempoMaiorQueZero($valor);
EOT;
    $conf['restricaoIncluirUpdate']['T147_Titulo'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;

// triggers
$triggerServico = <<<'EOT'
	$sql = "SELECT T147_T005_Id FROM T147 WHERE T147_Id='{$chaveValor}'";
	$result = mysql_query($sql);
	$linhaT147 = mysql_fetch_array($result);
	
    if ($linhaT147['T147_T005_Id']>0)
    {
    	$sql = "SELECT * FROM T147 WHERE T147_T005_Id='{$linhaT147['T147_T005_Id']}'";
    	$sql .= ($acao == 'deleteBefore') ? " AND T147_Id != '{$chaveValor}'" : '';
    	$result = mysql_query($sql);
    	$total=0;
    	while($linha = mysql_fetch_assoc($result)){
    		$total+=$linha['T147_Valor_Unitario'];
    	}
    	$totalServicos = gCorrigeNumeroInverte($total);
    	$sqlT005 = "UPDATE T005 SET T005_Valor_Total_Servicos='{$totalServicos}' WHERE T005_Id='{$linhaT147['T147_T005_Id']}'";
    	mysql_query($sqlT005);
        
    }
	
EOT;
$conf['triggers']['T147']['insertAfter'] = $triggerServico;
$conf['triggers']['T147']['updateAfter'] = $triggerServico;
$conf['triggers']['T147']['deleteBefore'] = $triggerServico;

// restricaoTravar
$conf['restricaoTravar']['T147'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T147_Id FROM T147 LEFT JOIN T005 ON T005_Id=T147_T005_Id WHERE T147_Id = '{$valorPk}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL)";
        $res = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0) {
            $retorno = 'Este pedido já foi faturado.';
        }
    } elseif ($operacao == 'incluir') {
        $sql = "SELECT T005_Id FROM T005 WHERE T005_Id = '{$valorPkParent}' AND (T005_Flag_Status>0 AND T005_Flag_Status!='' AND T005_Flag_Status IS NOT NULL)";
        $res = mysql_query($sql);
        $rows = mysql_numrows($res);
        if ($rows > 0) {
            $retorno = 'Este pedido já foi faturado.';
        }
    } else {
        $retorno = true;
    }
EOT;
