<?php
$conf['restricaoIncluirUpdate']['T066_D004_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D004_Id', $valor);
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T066'] = <<<'EOT'
	if ($operacao == 'incluir') {
		$retorno = true;
	} else {
		$retorno = "Você  não possui permissão para incluir/alterar/excluir produtos";
		
		$sql = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM T066 LEFT JOIN D009 ON D009_Id=T066_D009_Id LEFT JOIN D049 ON D009_D049_Id=D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE T066_Id = '{$valorPk}'");
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
	if($campo == 'T066_Quantidade_Balanco'){
        $retorno = true;        
    }

	$T066=mysql_query("SELECT * FROM T066 WHERE T066_Id='{$valorPk}'");
	$mT066 = mysql_fetch_array($T066);
	if(($mT066['T066_C007_Id_Marcou_Balanco'] != $g['usuarioAtual']) && !empty($mT066['T066_C007_Id_Marcou_Balanco'])){
		if($campo == 'T066_Quantidade_Balanco'){
			$retorno = "Não é possível alterar a quantidade, item está marcado por outro usuário";
		}
	}
EOT;
