<?php
/**
 * Tabela D022

 */
 
$conf['restricaoIncluirUpdate']['D022_Flag_Pessoa_Fisica_Juridica'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D022_Nome_Empresa'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;


$conf['camposLabelForm']['D022_Complemento'] = 'Contato';
$conf['camposLabelForm']['D022_DDD_Telefone_1'] = 'Contato Direto';
$conf['camposLabelForm']['D022_DDD_Telefone_2'] = 'Coletas';
$conf['camposLabelForm']['D022_Fax'] = 'Email';
$conf['camposLabelForm']['D022_Flag_Pessoa_Fisica_Juridica'] = 'Pessoa';

$conf['camposValorPadrao']['D022_D030_Id'] = '271';
/*
$conf['restricaoIncluirUpdate']['D022_Endereco'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D022_D018_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D018_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D022_D020_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D020_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D022_D036_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D036_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D022_D030_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D030_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D022_Telefone_1'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D022_DDD_Telefone_1'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
*/

$conf['restricaoIncluirUpdate']['D022_Cnpj'] = <<<'EOT'
	if ($valores['D022_Flag_Pessoa_Fisica_Juridica'] == 'J') {
		$retorno = rRegx('naoVazio',$valor);
		if ($retorno === true) {
			$retorno = rValidarCnpj($valor);
			if ($retorno === true) {
				if ($g['C031']['validarCadastroClienteCnpjCpfDuplidade'] != 'N') {
					$retorno = rDuplicidade($campo, $valor, $acaoId);
				}
			}
		} 
	}
EOT;
$conf['restricaoIncluirUpdate']['D022_Cpf'] = <<<'EOT'
	if ($valores['D022_Flag_Pessoa_Fisica_Juridica'] == 'F') {
		$retorno = rRegx('naoVazio',$valor);
		if ($retorno === true) {
			$retorno = rValidarCpf($valor);
			if ($retorno === true) {
				if ($g['C031']['validarCadastroClienteCnpjCpfDuplidade'] != 'N') {
					$retorno = rDuplicidade($campo, $valor, $acaoId);
				}
			}
		} 
	}
EOT;

$conf['triggers']['D022']['insertAfter'] = <<<'EOT'
	require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
	$WMS = new WMS();

	if($WMS->isEnabled()) {
		$WMS->integrarTransportadora('INSERT', $chaveValor);
	}
		
	mysqli_query("INSERT INTO D022A (D022A_D022_Id) VALUES ('{$chaveValor}')");
EOT;

$conf['triggers']['D022']['updateAfter'] = <<<'EOT'
	require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
	$WMS = new WMS();

	if($WMS->isEnabled()) {
		$WMS->integrarTransportadora('UPDATE', $chaveValor);
	}
EOT;
