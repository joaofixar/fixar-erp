<?php
/**
 * Tabela T145
 */

// camposNaoUsar
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
	'T145_D116_Id'
));

// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T145_Titulo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T145_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T145_Quantidade'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T145_Quantidade_Confirmada'] = <<<'EOT'
	$retorno = rNumero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T145_Valor_Unitario'] = <<<'EOT'
	$retorno = rNumero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T145_Tempo_Estimado'] = <<<'EOT'
	$retorno = rTempoMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T145_D116_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D116_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T145_T003_Id'] = <<<'EOT'
	$retorno = rVerificaFK('T003_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T145_D112_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D112_Id', $valor);
EOT;

// triggers
$triggerServico = <<<'EOT'
	$sql = "SELECT T145_T003_Id FROM T145 WHERE T145_Id='{$chaveValor}'";
	$result = mysql_query($sql);
	$linhaT145 = mysql_fetch_array($result);
	
	
	$sql = "SELECT * FROM T145 WHERE T145_T003_Id='{$linhaT145['T145_T003_Id']}'";
	$sql .= ($acao == 'deleteBefore') ? " AND T145_Id != '{$chaveValor}'" : '';
	$result = mysql_query($sql);
	$total=0;
	while($linha = mysql_fetch_assoc($result)){
		$total+=$linha['T145_Quantidade_Confirmada'] * $linha['T145_Valor_Unitario'];
	}
	$totalServicos = gCorrigeNumeroInverte($total);
	$sqlT003 = "UPDATE T003 SET T003_Valor_Total_Servicos='{$totalServicos}' WHERE T003_Id='{$linhaT145['T145_T003_Id']}'";
	mysql_query($sqlT003);
EOT;
$conf['triggers']['T145']['insertAfter'] = $triggerServico;
$conf['triggers']['T145']['updateAfter'] = $triggerServico;
$conf['triggers']['T145']['deleteBefore'] = $triggerServico;
