<?php
/**
* restricaoTravar
*/
$conf['restricaoTravar']['D024'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'excluir' || $operacao == 'alterar') {
		$D024 = mysql_fetch_array(mysql_query("SELECT D024_Flag_Pre_Cadastro FROM D024 WHERE D024_Id = '{$valorPk}'"));
		if($D024['D024_Flag_Pre_Cadastro'] == 'N'){
			if (!isset($g['c029Ids'][103])) {
				$retorno = 'erro';	
			}
		}
	}
EOT;

/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D024_Nome_Empresa'] = <<<'EOT'
 	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Flag_Pessoa_Fisica_Juridica'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Nome_Fantasia'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Flag_Revenda_Consumidor'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Flag_Contribuinte'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
/**$conf['restricaoIncluirUpdate']['D024_Inscricao_Estadual'] = <<<'EOT'
	$retorno = rRegx($valor, 'naoVazio');
	if ($retorno === true) {
		if ($valor != 'ISENTO') {
			$retorno = rRegx('/^[0-9]+(\[0-9]+)*$/i', $valor);
			if ($retorno !== true) {
				$retorno = "Inscrição Estadual deve ter apenas números. (Não use espaços em branco ou ponto e barra. Este campo aceita a palavra ISENTO";
			}
		}
		if ($valor == 'R') {
			$retorno = rDuplicidade($campo, $valor, $acaoId);
		}
	}
EOT;
*/
$conf['restricaoIncluirUpdate']['D024_Flag_Lista_Preco'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Codigo_Regime_Tributario'] = <<<'EOT'
	if($valores['D024_Flag_Pre_Cadastro'] == 'N' && $valores['D024_Flag_Pessoa_Fisica_Juridica'] != 'I'){
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D024_RG'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Cnpj'] = <<<'EOT'
	global $r_fornecedor, $g;
	if ($valores['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
		if(empty($g['C031']['codigoClienteCupomFiscal']) || (!empty($g['C031']['codigoClienteCupomFiscal']) && $acaoId != $g['C031']['codigoClienteCupomFiscal'])) {
			if($valores['D024_Flag_Pre_Cadastro'] == 'N' || isset($r_fornecedor) || $g['C031']['validarCPFCNPJPreCadastro'] == 'S'){
				$retorno = rRegx('naoVazio', $valor);
				if ($retorno === true) {                                                                       
					$retorno = rValidarCnpj($valor);
					if ($retorno === true) {
						//if ($g['c020']['C020_Cliente_Validar_Duplicidade_CNPJ_CPF'] != 'N') {
						if ($g['C031']['validarCadastroClienteCnpjCpfDuplidade'] != 'N') {
							$retorno = rDuplicidade($campo, $valor, $acaoId);
						}
					}
				}
			}else{
				$retorno = rRegx('naoVazio', $valor);
				if ($retorno === true) {                                                                       
					$retorno = rValidarCnpj($valor);
					if ($retorno === true) {
						//if ($g['c020']['C020_Cliente_Validar_Duplicidade_CNPJ_CPF'] != 'N') {
						if ($g['C031']['validarCadastroClienteCnpjCpfDuplidade'] != 'N') {
							$retorno = rDuplicidade($campo, $valor, $acaoId);
						}
					}
				}else{
					$retorno=true;
				}
			}
		} else {
			$retorno=true;
		}
	}
EOT;
$conf['restricaoIncluirUpdate']['D024_Cpf'] = <<<'EOT'
	global $r_fornecedor, $g;
	if ($valores['D024_Flag_Pessoa_Fisica_Juridica'] == 'F') {
		if(empty($g['C031']['codigoClienteCupomFiscal']) || (!empty($g['C031']['codigoClienteCupomFiscal']) && $acaoId != $g['C031']['codigoClienteCupomFiscal'])) {
			if($valores['D024_Flag_Pre_Cadastro'] == 'N' || isset($r_fornecedor) || $g['C031']['validarCPFCNPJPreCadastro'] == 'S'){
				$retorno = rRegx('naoVazio', $valor);
				if ($retorno === true) {                                                                       
					$retorno = rValidarCpf($valor);
					if ($retorno === true) {
						if ($g['c020']['C020_Cliente_Validar_Duplicidade_CNPJ_CPF'] != 'N') {
							$retorno = rDuplicidade($campo, $valor, $acaoId);
						}
					}
				}
			}else{
				$retorno = rRegx('naoVazio', $valor);
				if ($retorno === true) {                                                                       
					$retorno = rValidarCpf($valor);
					if ($retorno === true) {
						if ($g['c020']['C020_Cliente_Validar_Duplicidade_CNPJ_CPF'] != 'N') {
							$retorno = rDuplicidade($campo, $valor, $acaoId);
						}
					}
				}else{
					$retorno=true;
				}
			}
		} else {
			$retorno=true;
		}
	}
EOT;
// $conf['restricaoIncluirUpdate']['D024_Endereco'] = <<<'EOT'
	// $retorno = rRegx('naoVazio', $valor);
// EOT;
//$conf['restricaoIncluirUpdate']['D024_Numero'] = <<<'EOT'
//	$retorno = rRegx('naoVazio', $valor);
//EOT;
$conf['restricaoIncluirUpdate']['D024_D018_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_D020_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_D030_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Bairro'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_DDD_Telefone_1'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Telefone_1'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Flag_Pre_Cadastro'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Data_Cadastro'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Flag_Cliente'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Flag_Fornecedor'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D024_Flag_Ativo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

/**
* camposValorPadrao
*/
$conf['camposValorPadrao']['D024_Flag_Contribuinte'] = '1';
$D030_Id = '0';
$sqlD030 = mysqli_query($g['conexaoBanco'],"SELECT D030_Id FROM D030 WHERE D030_Nome_Pais = 'BRASIL'");
if(mysqli_num_rows($sqlD030) == 1) {
	$rowD030 = mysqli_fetch_array($sqlD030);
	$D030_Id = $rowD030['D030_Id'];
}
$conf['camposValorPadrao']['D024_D030_Id'] = $D030_Id;
$conf['camposValorPadrao']['D024_Flag_Liberado'] = 'S';
$conf['camposValorPadrao']['D024_Flag_Lista_Preco'] = 1;
$conf['camposValorPadrao']['D024_Flag_Pessoa_Fisica_Juridica'] = 'J';
$conf['camposValorPadrao']['D024_Flag_Contribuinte'] = '5';

/**
*	Triggers
*/

$conf['triggers']['D024']['updateBefore'] = <<<'EOT'
	$sql = mysql_query("SELECT D024_C007_Vendedor_Interno FROM D024 WHERE D024_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);
	$g['updateBefore-D024_C007_Vendedor_Interno'] = $row['D024_C007_Vendedor_Interno'];
EOT;

$conf['triggers']['D024']['updateAfter'] = <<<'EOT'
	$sql = mysqli_query("SELECT D024_C007_Vendedor_Interno FROM D024 WHERE D024_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);
	
	if($g['updateBefore-D024_C007_Vendedor_Interno'] != $row['D024_C007_Vendedor_Interno']){
		mysqli_query("UPDATE D024 SET  D024_Data_Ativo = curdate() WHERE D024_Id = '{$chaveValor}'");

		$T162_Extra = "Trigger PHP - Tabela D024"; 	
		$camposAlterados = array();
		$camposAlterados['D024_Data_Ativo'] = array($row['D024_Data_Ativo'], date('Y-m-d'));
		$camposAlterados = mysql_real_escape_string(serialize($camposAlterados));
		mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', 'U', '{$T162_Extra}', '" . date('Y-m-d H:i:s') . "', 'D024', '{$chaveValor}', COMPRESS('{$camposAlterados}'), '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);
	}

	mysqli_query("UPDATE D024 SET D024_Data_Atualizacao = NOW() WHERE D024_Id = '{$chaveValor}'");

	require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
	$WMS = new WMS();

	if($WMS->isEnabled()) {
		$WMS->integrarPessoa('UPDATE', $chaveValor);
	}
EOT;

$conf['triggers']['D024']['insertAfter'] = <<<'EOT'
	require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
	$WMS = new WMS();

	if($WMS->isEnabled()) {
		$WMS->integrarPessoa('INSERT', $chaveValor);
	}
EOT;
