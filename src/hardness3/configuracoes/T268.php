<?php
/**
* restricaoTravar
*/
$conf['restricaoTravar']['T268'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'excluir' || $operacao == 'alterar') {
		$T268 = mysql_fetch_array(mysql_query("SELECT T268_Flag_Status,T268_Codigo_Rastreio FROM T268 WHERE T268_Id = '{$valorPk}'"));
		if($T268['T268_Flag_Status'] == '10' or !empty($T268['T268_Codigo_Rastreio'])){
			$retorno = 'Este pacote já está postado!';
		}
	}
EOT;

/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['T268_Servico'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T268_D036_Id'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T268_D024_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T268_Valor_Declarado'] = <<<'EOT'
	if($valores['T268_Servico_Adicional_VD'] == 'S') {
		$retorno = rNumeroMaiorQueZero($valor);
	}
EOT;
