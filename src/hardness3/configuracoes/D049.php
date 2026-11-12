<?php
/**
* camposTipo
*/
$conf['camposTipo']['D049_Data_Validade'] = array('data');
$conf['camposTipo']['D049_Data_Fabricacao'] = array('data');

/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D049_D082_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D082_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D049_Origem_Mercadoria'] = <<<'EOT'
	$sqlD001 = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id WHERE D049_Id='{$valores['D049_Id']}'");
	$resD001 = mysql_fetch_array($sqlD001);
	if($g['C031']['obrigatorioOrigemPreCadastroProduto'] == 'S'){
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D049_Flag_Tipo'] = <<<'EOT'
	$sqlD001 = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id WHERE D049_Id='{$valores['D049_Id']}'");
	$resD001 = mysql_fetch_array($sqlD001);
	if($g['C031']['obrigatorioTipoPreCadastroProduto'] == 'S'){
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;

/**
* ignorarRelacionamentoExcluir
*/
$conf['ignorarRelacionamentoExcluir'] = array_merge($conf['ignorarRelacionamentoExcluir'], 
	array('D009_D049_Id','D041_D049_Id')
);

/**
* camposValorPadrao
*/
$conf['camposValorPadrao']['D049_Flag_Tipo'] = 'CP';
$conf['camposValorPadrao']['D049_Origem_Mercadoria'] = '0';

/**
* camposLabelForm
*/
$conf['camposLabelForm']['D049_Aliquota_ICMS_Tabela'] = '% ICMS Tabela';
$conf['camposLabelForm']['D049_ICF'] = "ICF";
$conf['camposLabelForm']['D049_IPV'] = 'IPV 1';
$conf['camposLabelForm']['D049_Percentual_Acrescimo_Tabela'] = '% Acrescimo tabela';
$conf['camposLabelForm']['D049_Percentual_Desconto_Tabela'] = '% Desconto tabela';
$conf['camposLabelForm']['D049_Valor_Preco_Tabela'] = 'Custo manual';

/**
* triggers
*/
$conf['triggers']['D049']['insertAfter'] = <<<'EOT'
	mysqli_query("UPDATE D049 SET D049_Data_Cadastro = CURDATE() WHERE D049_Id = '{$chaveValor}'");
EOT;

$conf['triggers']['D049']['updateAfter'] = <<<'EOT'
	$sqlD049 = mysql_query("SELECT D009_Id, D049_D024_Id FROM D049 LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D049_Id = '{$chaveValor}' LIMIT 1");
	$rowD049 = mysql_fetch_array($sqlD049);

	require_once("bibliotecas/classes/CAD002.php");
	$CAD002 = new CAD002();
	$CAD002->definirCodigoFornecedor($rowD049['D009_Id'], $rowD049['D049_D024_Id'], '', '', false, true);
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['D049'] = <<<'EOT'
	if ($operacao == 'incluir') {
		$retorno = true;
	} else {
		$retorno = "Você  não possui permissão para incluir/alterar/excluir produtos";
		if ($valorPk) {
			$sql = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D049 LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D049_Id = '{$valorPk}'");
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
	}
EOT;
