<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D001_Codigo_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
	if ($retorno === true) {
		$retorno = rDuplicidade($campo, $valor, $acaoId, 'Código Produto');
	}
EOT;
$conf['restricaoIncluirUpdate']['D001_Descricao_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_C008_Id'] = <<<'EOT'
	$retorno = rVerificaFK('C008_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_D003_Id'] = <<<'EOT'
	if ($g['C031']['obrigatorioGrupoPreCadastroProduto'] == 'S') {
		$retorno = rVerificaFK('D003_Id', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D001_D037_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D037_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_Flag_Tipo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_D060_Id'] = <<<'EOT'
	if ($g['C031']['obrigatorioSetorPreCadastroProduto'] == 'S') {
		$retorno = rVerificaFK('D060_Id', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D001_D005_Id'] = <<<'EOT'
	if (($valores['D001_Flag_Pre_Cadastro'] == 'S' && $g['C031']['obrigatorioNCMPreCadastroProduto'] == 'S') || ($valores['D001_Flag_Pre_Cadastro'] == 'N')) {
		$retorno = rVerificaFK('D005_Id', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D001_D002_Id'] = <<<'EOT'
	if ($g['C031']['obrigatorioSubGrupoPreCadastroProduto'] == 'S') {
		$retorno = rVerificaFK('D002_Id', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D001_Flag_Tipo_Item'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_Codigo_Barras'] = <<<'EOT'
	if (!empty($valores['D001_Flag_Pre_Cadastro']) && $valores['D001_Flag_Pre_Cadastro'] == 'N') {
		$retorno = rRegx('naoVazio', $valor);
		if ($retorno === true) {
			$retorno = rDuplicidade($campo, $valor, $acaoId, 'EAN');
		}
		if (strlen($valor) > 13) {
			$retorno = "Deve ser composto por até 13 números.";
		}
	}
EOT;
$conf['restricaoIncluirUpdate']['D001_Flag_Ativo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_Flag_Pre_Cadastro'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_Flag_Multimarcas'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
/**
* camposTipo
*/
$conf['camposTipo']['D001_Qte_Embalagem'] = array('numero', 3);
/**
* auditoriaCampos
*/
$conf['auditoriaCampos'] = array_merge($conf['auditoriaCampos'], array(
	'D001_Flag_Ativo',
	'D001_Flag_Pre_Cadastro'
));
/**
* camposNaoUsar
*/
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
    'D001_Foto'
));
/**
* camposValorPadrao
*/
$conf['camposValorPadrao']['D001_Flag_Tipo'] = 'COMPRADO';
$conf['camposValorPadrao']['D001_Flag_Multimarcas'] = 'N';
/**
* camposLabelForm
*/
$conf['camposLabelForm']['D001_D003_Id'] = 'Grupo';
$conf['camposLabelForm']['D001_C008_Id'] = 'Depto';
$conf['camposLabelForm']['D001_Codigo_Barras'] = 'EAN';
$conf['camposLabelForm']['D001_D060_Id'] = 'Setor';
$conf['camposLabelForm']['D001_Flag_Multimarcas'] = 'Sub-itens';
$conf['camposLabelForm']['D001_Flag_Tipo'] = 'Tipo (IPI)';

/**
* triggers
*/
$conf['triggers']['D001']['insertAfter'] = <<<'EOT'
	/*
	$sqlD001 = mysql_query("SELECT D001_Codigo_Barras FROM D001 WHERE D001_Id = '{$chaveValor}'");
	$D001 = mysql_fetch_array($sqlD001);

	if($D001['D001_Codigo_Barras'] == '' || empty($D001['D001_Codigo_Barras'])){
		$sqlEAN = mysql_query("SELECT ifnull(D001_Codigo_Barras,0) AS D001_Codigo_Barras FROM D001 ORDER BY ROUND(D001_Codigo_Barras) DESC LIMIT 1");
		$resEAN = mysql_fetch_array($sqlEAN);
		$resEAN['D001_Codigo_Barras']++;
		mysqli_query("UPDATE D001 SET D001_Codigo_Barras = '{$resEAN['D001_Codigo_Barras']}' WHERE D001_Id = '{$chaveValor}'");
	}
	*/

	require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
	$WMS = new WMS();

	if($WMS->isEnabled()) {
		$WMS->integrarProduto('INSERT', $chaveValor);
	}
EOT;

$conf['triggers']['D001']['updateAfter'] = <<<'EOT'
	require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
	$WMS = new WMS();

	if($WMS->isEnabled()) {
		$WMS->integrarProduto('UPDATE', $chaveValor);
	}
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['D001'] = <<<'EOT'
	if ($operacao == 'incluir') {
		$retorno = true;
	} else {
		$retorno = "Você  não possui permissão para incluir/alterar/excluir produtos";
		
		$sql = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D001 WHERE D001_Id = '{$valorPk}'");
		$row = mysql_fetch_assoc($sql);
		if ($row['D001_Flag_Pre_Cadastro'] == 'S'){
			if(isset($g['c029Ids'][187])){
				$retorno = true;
			}
		} else {
			if(isset($g['c029Ids'][186])){
				$retorno = true;
			}
		}
	}
EOT;
