<?php
/**
 * Tabela T002

 */
 
$conf['restricaoIncluirUpdate']['T316_T014_Id'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T316_Descricao_Utilizacao_Bem'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T316_Data_Inicio_Apropriacao_Credito'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T316_Numero_Parcelas_Apropriacao'] = <<<'EOT'
    $retorno = rRegx('naoVazio', $valor);
EOT;

// restricaoTravar
$conf['restricaoTravar']['T316'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = "SELECT T316_Id FROM T316 WHERE T316_Id = '{$valorPk}' AND T316_Flag_Ativo_Baixado = 'S'";
		$res = mysql_query($sql);
		$rows = mysql_numrows($res);
		if ($rows > 0) {
			$retorno = 'Lançamento do CIAP já está baixado, não pode ser mais alterado.';
		}
	} else {
		$retorno = true;
	}
EOT;


?>