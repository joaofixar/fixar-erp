<?php
/**
 * T203
 */

// camposNaoUsar
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
	'T171_D116_Id'
));

// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T203_T007_Id'] = <<<'EOT'
	$retorno = rVerificaFK('T007_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T203_D116_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D116_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T203_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T203_Quantidade'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T203_Tempo_Estimado'] = <<<'EOT'
	$retorno = rTempoMaiorQueZero($valor);
EOT;

// triggers
$triggerServico = <<<'EOT'
	$sql = "SELECT T203_T007_Id FROM T203 WHERE T203_Id='{$chaveValor}'";
	$result = mysql_query($sql);
	$linhaT203 = mysql_fetch_array($result);
	
	
	$sql = "SELECT * FROM T203 WHERE T203_T007_Id='{$linhaT203['T203_T007_Id']}'";
	$sql .= ($acao == 'deleteBefore') ? " AND T203_Id != '{$chaveValor}'" : '';
	$result = mysql_query($sql);
	$total=0;
	while($linha = mysql_fetch_assoc($result)){
		$total+=$linha['T203_Valor_Unitario'];
	}
	$totalServicos = gCorrigeNumeroInverte($total);
	$sqlT007 = "UPDATE T007 SET T007_Valor_Total_Servicos='{$totalServicos}' WHERE T007_Id='{$linhaT203['T203_T007_Id']}'";
	mysql_query($sqlT007);
	
EOT;
$conf['triggers']['T203']['insertAfter'] = $triggerServico;
$conf['triggers']['T203']['updateAfter'] = $triggerServico;
$conf['triggers']['T203']['deleteBefore'] = $triggerServico;  
