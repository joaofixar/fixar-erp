<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D109_Nome'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D109_Funcao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
//$conf['restricaoIncluirUpdate']['D109_Codigo_Barras'] = <<<'EOT'
	//$retorno = rRegx('naoVazio', $valor);
//EOT;
// $conf['restricaoIncluirUpdate']['D109_Cpf'] = <<<'EOT'
	// if($valores['D109_Cpf'] == ''){
		// $retorno = rRegx('naoVazio', $valor);
		// $retorno = rValidarCpf($valor);
	// }else{
		// $retorno = rValidarCpf($valor);
	// }
// EOT;

/*
$conf['restricaoIncluirUpdate']['D109_Senha'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
	if ($retorno === true) {
		if(empty($acaoId)){
			$sql = "SELECT * FROM D109";
		} else {
			$sql = "SELECT * FROM D109 WHERE D109_Id != '{$acaoId}'";
		} 
		$return = mysql_query($sql);
		while($linha = mysql_fetch_array($return)){
			if($linha['D109_Senha'] == $valor){
				$retorno = "Senha já cadastrada. Por favor, insira uma senha diferente";
			}else if(!is_numeric($valor)){
				$retorno = "Tem que ser um número.";
			}
		}
	}
EOT;
*/

/**
* camposTipo
*/
$conf['camposTipo']['D109_Carga_Horaria'] = array('tempo');
