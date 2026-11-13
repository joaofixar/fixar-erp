<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D070_D024_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D024_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D070_C004_Id'] = <<<'EOT'
	$retorno = rVerificaFK('C004_Id', $valor);
EOT;
/**
* camposTipo
*/
//$conf['camposTipo']['D001_Qte_Embalagem'] = array('numero', 3);
/**
* auditoriaCampos
*/
/*$conf['auditoriaCampos'] = array_merge($conf['auditoriaCampos'], array(
	'D001_Flag_Ativo',
	'D001_Flag_Pre_Cadastro'
)); */
/**
* camposNaoUsar
*/
/*$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
    'D001_Foto'
)); */
/**
* camposValorPadrao
*/
//$conf['camposValorPadrao']['D001_Flag_Tipo'] = 'COMPRADO';
//$conf['camposValorPadrao']['D001_Flag_Multimarcas'] = 'N';
/**
* camposLabelForm
*/
//$conf['camposLabelForm']['D001_D003_Id'] = 'Grupo';

/**
* triggers
*/
/*$conf['triggers']['D001']['insertAfter'] = <<<'EOT'
	$sqlD001 = mysql_query("SELECT D001_Codigo_Barras FROM D001 WHERE D001_Id = '{$chaveValor}'");
	$D001 = mysql_fetch_array($sqlD001);

	if($D001['D001_Codigo_Barras'] == '' || empty($D001['D001_Codigo_Barras'])){
		$sqlEAN = mysql_query("SELECT ifnull(D001_Codigo_Barras,0) AS D001_Codigo_Barras FROM D001 ORDER BY ROUND(D001_Codigo_Barras) DESC LIMIT 1");
		$resEAN = mysql_fetch_array($sqlEAN);
		$resEAN['D001_Codigo_Barras']++;
		mysqli_query("UPDATE D001 SET D001_Codigo_Barras = '{$resEAN['D001_Codigo_Barras']}' WHERE D001_Id = '{$chaveValor}'");
	} 
EOT;*/

/**
* ignorarRelacionamentoExcluir
*/
$conf['ignorarRelacionamentoExcluir'] = array_merge($conf['ignorarRelacionamentoExcluir'], 
	array('D009_D070_Id')
);


/**
* restricaoTravar
*/
$conf['restricaoTravar']['D070'] = <<<'EOT'
	$retorno = "Você  não possui permissão para incluir/alterar/excluir produtos";
	
	if($operacao == 'incluir'){
		if(isset($g['c029Ids'][187])){
			$retorno = true;
		}
		if(isset($g['c029Ids'][186])){
			$retorno = true;
		}
	} else if ($operacao == 'alterar' || $operacao == 'excluir') {
		if(isset($g['c029Ids'][186])){
			$retorno = true;
		}
	}
EOT;
