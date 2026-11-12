<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D083_D024_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
	if ($retorno === true && !$acaoId) {
		$sql = "SELECT D083_Id FROM D083 WHERE D083_D001_Id = '{$valores['D083_D001_Id']}' AND D083_D024_Id = '{$valores['D083_D024_Id']}' AND D083_D049_Id = '{$valores['D083_D049_Id']}'";
		$res = mysql_query($sql);
		if (mysql_num_rows($res) > 0) {
			$retorno = 'Já existe uma registro cadastrado para esta empresa neste produto.';
		} else {
			$retorno = true;
		}
	}
EOT;

$conf['restricaoIncluirUpdate']['D083_D049_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D083_Codigo_Barras'] = <<<'EOT'
	if(empty($valores['D083_D024_Id'])){
		$row = mysql_fetch_assoc(mysql_query("SELECT * FROM D083 WHERE D083_Id = '{$acaoId}'"));
		$valores['D083_D024_Id'] = $row['D083_D024_Id'];
	}
	$retorno = rDuplicidade($campo, $valor, $acaoId, 'Código de Barras', true, 'D083_D024_Id', $valores['D083_D024_Id']);
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['D083'] = <<<'EOT'
	if ($operacao == 'incluir') {
		$retorno = true;
	} else {
		$retorno = "Você  não possui permissão para incluir/alterar/excluir produtos";

		$sql = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D083 LEFT JOIN D001 ON D001_Id=D083_D001_Id WHERE D083_Id = '{$valorPk}'");
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
