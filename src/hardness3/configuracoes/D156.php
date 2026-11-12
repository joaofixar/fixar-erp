<?php
/*
* camposLabelForm
*/

$conf['restricaoIncluirUpdate']['D156_D001_Id_2'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;

$conf['triggers']['D156']['updateAfter'] = <<<'EOT'
	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();

	$sql = mysql_query("SELECT D156_D001_Id_2,D156_D001_Id_1 FROM D156 WHERE D156_Id = '{$chaveValor}'");
	$mSql = mysql_fetch_array($sql);

	$D001 = mysql_query("SELECT D001_Id, D001_Codigo_Produto FROM D001 WHERE D001_Id = ('{$mSql['D156_D001_Id_2']}' OR '{$mSql['D156_D001_Id_1']}')");
	$mD001 = mysql_fetch_array($D001);
	$padraoSimilares = $CAD002->reprocessaEstoqueSimilares($mD001['D001_Id'], $mD001['D001_Codigo_Produto']);

EOT;

$conf['triggers']['D156']['insertAfter'] = <<<'EOT'
	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();

	$sql = mysql_query("SELECT D156_D001_Id_2,D156_D001_Id_1 FROM D156 WHERE D156_Id = '{$chaveValor}'");
	$mSql = mysql_fetch_array($sql);
	
    $D001 = mysql_query("SELECT D001_Id, D001_Codigo_Produto FROM D001 WHERE D001_Id = ('{$mSql['D156_D001_Id_2']}' OR '{$mSql['D156_D001_Id_1']}')");
	$mD001 = mysql_fetch_array($D001);
	$padraoSimilares = $CAD002->reprocessaEstoqueSimilares($mD001['D001_Id'], $mD001['D001_Codigo_Produto']);

EOT;

$conf['triggers']['D156']['deleteBefore'] = <<<'EOT'
	global $D001_Id_1_Apos_Excluir, $D001_Codigo_Produto_1_Apos_Excluir, $D001_Id_2_Apos_Excluir, $D001_Codigo_Produto_2_Apos_Excluir;

	$sql = mysql_query("SELECT D156_D001_Id_2,D156_D001_Id_1 FROM D156 WHERE D156_Id = '{$chaveValor}'");
	$mSql = mysql_fetch_array($sql);

    $D001_1 = mysql_query("SELECT D001_Id, D001_Codigo_Produto FROM D001 WHERE D001_Id = '{$mSql['D156_D001_Id_1']}'");
	$mD001_1 = mysql_fetch_array($D001_1);
	$D001_Id_1_Apos_Excluir = $mD001_1['D001_Id'];
	$D001_Codigo_Produto_1_Apos_Excluir = $mD001_1['D001_Codigo_Produto'];

	$D001_2 = mysql_query("SELECT D001_Id, D001_Codigo_Produto FROM D001 WHERE D001_Id = '{$mSql['D156_D001_Id_2']}'");
	$mD001_2 = mysql_fetch_array($D001_2);
	$D001_Id_2_Apos_Excluir = $mD001_2['D001_Id'];
	$D001_Codigo_Produto_2_Apos_Excluir = $mD001_2['D001_Codigo_Produto'];

EOT;

$conf['triggers']['D156']['deleteAfter'] = <<<'EOT'
	global $D001_Id_1_Apos_Excluir, $D001_Codigo_Produto_1_Apos_Excluir, $D001_Id_2_Apos_Excluir, $D001_Codigo_Produto_2_Apos_Excluir;
	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$padraoSimilares = $CAD002->reprocessaEstoqueSimilares($D001_Id_1_Apos_Excluir,$D001_Codigo_Produto_1_Apos_Excluir);
	$padraoSimilares = $CAD002->reprocessaEstoqueSimilares($D001_Id_2_Apos_Excluir,$D001_Codigo_Produto_2_Apos_Excluir);
	unset($D001_Id_1_Apos_Excluir);
	unset($D001_Codigo_Produto_1_Apos_Excluir);
	unset($D001_Id_2_Apos_Excluir);
	unset($D001_Codigo_Produto_2_Apos_Excluir);
EOT;

$conf['restricaoTravar']['D156'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar') {
		if (!isset($g['c029Ids'][210])) {
			$retorno = "Você não possui permissão para alterar este registro";
		}
	} else if ($operacao == 'incluir') {
		if (!isset($g['c029Ids'][209])) {
			$retorno = "Você não possui permissão para incluir este registro";
		}
	} else if ($operacao == 'excluir') {
		if (!isset($g['c029Ids'][211])) {
			$retorno = "Você não possui permissão para Excluir este registro";
		}
	}
EOT;
