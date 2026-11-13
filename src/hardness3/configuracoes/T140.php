<?php
/**
* restricaoIncluirUpdate
*/

$conf['camposTipo']['T140_Data_Emissao'] = array('data');
$conf['camposTipo']['T140_Data_Validade'] = array('data');

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T140'] = <<<'EOT'
	$retorno = "Você  não possui permissão para incluir/alterar/excluir produtos";
	
	if($operacao == 'incluir'){
		if(isset($g['c029Ids'][187])){
			$retorno = true;
		}
		if(isset($g['c029Ids'][186])){
			$retorno = true;
		}
	} else if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM T140 LEFT JOIN D001 ON D001_Id=T140_D001_Id WHERE T140_Id = '{$valorPk}'");
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
