<?php

/**
* @author RubiaYwata
* @since 16/11/2011  
*/
	
  $conf['restricaoIncluirUpdate']['C007_Primeiro_Nome'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

  $conf['restricaoIncluirUpdate']['C007_Nome'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

  $conf['restricaoIncluirUpdate']['C007_Flag_Vendedor'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['C007_Cpf'] = <<<'EOT'
	if($valores['C007_Cpf'] == ''){
		$retorno = true;
	}else{
		$retorno = rValidarCpf($valor);
	}
EOT;

$conf['restricaoIncluirUpdate']['C007_Cnpj'] = <<<'EOT'
	if($valores['C007_Cnpj'] == ''){
		$retorno = true;
	}else{
		$retorno = rValidarCnpj($valor);
	}
EOT;

$conf['triggers']['C007']['updateBefore'] = <<<'EOT'
	$sql = mysql_query("SELECT C007_Senha FROM C007 WHERE C007_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);
	$g['updateBefore-C007_Senha'] = $row['C007_Senha'];
EOT;

$conf['triggers']['C007']['updateAfter'] = <<<'EOT'
	$sql = mysql_query("SELECT C007_Senha FROM C007 WHERE C007_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);
	if ($g['updateBefore-C007_Senha'] != $row['C007_Senha']) {
		mysql_query("UPDATE C007 SET C007_Data_Alteracao_Senha = NOW() WHERE C007_Id = '{$chaveValor}'");
	}
EOT;
