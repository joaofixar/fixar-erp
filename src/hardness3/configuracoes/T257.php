<?php

$conf['restricaoIncluirUpdate']['T257_Quantidade'] = <<<'EOT'
    $retorno = rNumeroMaiorQueZero($valor);
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T257'] = <<<'EOT'
	$retorno = true;

	if($operacao == 'incluir'){
		$sql = mysql_query("SELECT T255_Flag_Status FROM T256 LEFT JOIN T255 ON T255_Id=T256_T255_Id WHERE T256_Id = '{$valorPkParent}'");
		$row = mysql_fetch_array($sql);
		if($row['T255_Flag_Status'] == '99'){
			$retorno = "Esta ordem de produção já está finalizada";
		} else if($row['T255_Flag_Status'] == '98'){
			$retorno = "Esta ordem de produção está cancelada";
		}
	} else if($operacao == 'excluir' || $operacao == 'alterar'){
		$sql = mysql_query("SELECT T001_Id FROM T257 LEFT JOIN T001 ON T257_Id=T001_T257_Id WHERE T257_Id = '{$valorPk}' AND T001_Id IS NOT NULL");
		if(mysql_num_rows($sql) > 0){
			$retorno = "Não é possível modificar esta entrada, pois o estoque já foi processado.";
		}
	}
EOT;
