<?php

$conf['camposTipo']['T001_Data_Validade'] = array('data');
$conf['camposTipo']['T001_Data_Fabricacao'] = array('data');

$conf['restricaoIncluirUpdate']['T001_T066_Id'] = <<<'EOT'
	$retorno = rVerificaFK('T066_Id', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T001_Flag_Operacao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T001_Flag_Operacao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T001_Quantidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T001_Descricao_Operacao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T001_Data_Lancamento'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T001_T066_Id'] = <<<'EOT'
	if($valor == 0)
		$valor = '';
	$retorno = rRegx('naoVazio', $valor);
EOT;
/**
* restricaoTravar
*/
$conf['restricaoTravar']['T001'] = <<<'EOT'
	$retorno = "Você não possui permissão para incluir/alterar/excluir produtos";
	
	if($operacao == 'incluir'){
		if(isset($g['c029Ids'][187])){
			$retorno = true;
		}
		if(isset($g['c029Ids'][186])){
			$retorno = true;
		}
	} else if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM T001 LEFT JOIN D009 ON D009_Id=T001_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE T001_Id = '{$valorPk}'");
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
		if($retorno == true){
			if($g['nivelAlcada'] < 2){
				$retorno = "É necessário nível de alçada superior ou igual a supervisor para alterar/excluir o histórico do produto";
			}
		}
	}
EOT;

