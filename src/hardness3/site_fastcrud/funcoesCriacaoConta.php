<?php
set_time_limit(0);
/**
* Funções para criação automática de novas contas no sistema novo
*/

/**
* Verifica se um domínio é válido para uso
* @param $dominio
* @return string/true
*/
function verificarDominioValido($dominio, $server = "hardness.com.br.conf") {
	if (empty($dominio)) {
		return 'Domínio não pode ser vazio.';
	} else if (strlen($dominio) < 3) {
		return 'Domínio deve ter no mínimo 3 caracteres.';
	} else if (!preg_match('#^[a-z]([a-z]|[0-9])+$#i', $dominio)) {
		return 'Não é um domínio válido: utilize somente letras e números.';
	} else if (strpos($dominio, "www") !== false) {
		return 'Não é um domínio válido: utilize somente letras e números.';
	}
	$arqApache = "/etc/apache2/sites-enabled/{$dominio}.{$server}.conf";
	if (file_exists($arqApache)) {
		return 'Domínio já em uso.';
	}
	return true;
}

/**
* Cria uma nova conta (bancod de dados, dominio, etc) pronto para usar
* @param $D024_Id
* @param $dominio
* @param $login
* @param $senha
* @return string/true
*/
function criarConta($form) {
	//$_REQUEST['Dominio'], $_REQUEST['login'], $_REQUEST['senha'], $_REQUEST['D024_E_Mail']
	global $conERP, $conNOTA, $confUsuarioERP;
	$form['formaPagamento'] = '1';
	$form['Dominio'] = strtolower($form['Dominio']);

	if($form['pacote'] == '1'){
		$valorMensalidade = '149.00';
		$modulos = "1,2,3,5";
	} else if($form['pacote'] == '2'){
		$valorMensalidade = '239.00';
		$modulos = "1,2,3,4,5,6";
	} else {
		$valorMensalidade = '149.00';
		$modulos = "1,2,3,5";
	}

	$modulos = (!empty($modulos)) ? explode(",", $modulos) : array();
	$D024_Opcoes_Sistema = implode(",", $modulos);
	/************************************************************************
	*						VALIDAÇÃO DOS DADOS
	************************************************************************/
	$form['D024_Cargo_Responsavel']  = trim($form['D024_Cargo_Responsavel']);
	$form['D024_Contador'] 			 = trim($form['D024_Contador']);
	$form['D024_Telefone_Contador']  = trim($form['D024_Telefone_Contador']);
	$form['D024_Inscricao_Estadual'] = trim($form['D024_Inscricao_Estadual']);
	$form['D024_Nome_Empresa'] 		 = trim($form['D024_Nome_Empresa']);
	$form['D024_Endereco'] 			 = trim($form['D024_Endereco']);
	$form['D024_Numero'] 			 = trim($form['D024_Numero']);
	$form['D024_Complemento'] 	     = trim($form['D024_Complemento']);
	$form['D024_D018_Id'] 			 = trim($form['D024_D018_Id']);
	$form['D024_D020_Id'] 			 = trim($form['D024_D020_Id']);
	$form['D024_Bairro'] 			 = trim($form['D024_Bairro']);
	$form['D024_Cep'] 				 = trim($form['D024_Cep']);
	$form['D024_E_Mail'] 			 = trim($form['D024_E_Mail']);
	$form['D024_DDD_Telefone'] 		 = trim($form['D024_DDD_Telefone']);
	$form['D024_Telefone'] 			 = trim($form['D024_Telefone']);
	$form['D024_Dias_Vencimento'] 	 = trim($form['D024_Dias_Vencimento']);

	/************************************************************************
	*				CADASTRANDO CLIENTE NA BASE DA HARDNESS
	************************************************************************/
	$D024_Observacoes="RESPONSÁVEL: $D024_Responsavel\nCPF: {$form['D024_CPF_Responsavel']}\nCARGO: {$form['D024_Cargo_Responsavel']}\nCONTADOR: {$form['D024_Contador']}\nTELEFONE: {$form['D024_Telefone_Contador']}";
	// Insere os dados do cliente na base (Hardness)
	$sql = "INSERT INTO D024 (
						D024_Cnpj,
						D024_Inscricao_Estadual,
						D024_Nome_Empresa,
						D024_Nome_Fantasia,
						D024_Endereco,
						D024_Numero,
						D024_Complemento,
						D024_D018_Id,
						D024_D020_Id,
						D024_Bairro,
						D024_Cep,
						D024_E_Mail,
						D024_DDD_Telefone_1,
						D024_Telefone_1,
						D024_Dias_Vencimento,
						D024_Opcoes_Sistema,
						D024_Periodo_Teste,
						D024_Data_Cadastro,
						D024_Observacoes
					) VALUES (
						'{$form['D024_Cnpj']}',
						'{$form['D024_Inscricao_Estadual']}',
						'{$form['D024_Nome_Empresa']}',
						'{$form['D024_Nome_Empresa']}',
						'{$form['D024_Endereco']}',
						'{$form['D024_Numero']}',
						'{$form['D024_Complemento']}',
						'{$form['D024_D018_Id']}',
						'{$form['D024_D020_Id']}',
						'{$form['D024_Bairro']}',
						'{$form['D024_Cep']}',
						'{$form['D024_E_Mail']}',
						'{$form['D024_DDD_Telefone']}',
						'{$form['D024_Telefone']}',
						'{$form['D024_Dias_Vencimento']}',
						'{$D024_Opcoes_Sistema}',
						'{$periodoTeste}',
						current_date(),
						'{$D024_Observacoes}'
					);";
	mysqli_query($conNOTA, $sql);
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 1)";
	}

	// D024
	$D024_Id = mysqli_insert_id($conNOTA);
	$res = mysqli_query($conNOTA, "SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'");
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		rollbackCriacaoConta($D024_Id);
		return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 2)";
	}
	$rowD024 = mysqli_fetch_assoc($res);

	$sql = <<<EOT
		INSERT INTO T135 (
			T135_D024_Id,
			T135_Data_Inicio,
			T135_Data_Termino,
			T135_Valor_Contrato,
			T135_Tipo,
			T135_Dia_Vencimento
		) VALUES (
			'{$D024_Id}',
			CURRENT_DATE(),
			DATE_ADD(CURRENT_DATE(), INTERVAL 1 YEAR),
			'{$valorMensalidade}',
			'M',
			DATE_ADD(CURRENT_DATE(), INTERVAL 5 DAY)
		);
EOT;
	mysqli_query($conNOTA, $sql);
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 3)";
	}

	// Criar novo banco na C025
	$res = mysqli_query($conNOTA, "SELECT LOWER(C025_Banco_Dados) as C025_Banco_Dados FROM C025 ORDER BY LOWER(C025_Banco_Dados) DESC LIMIT 1");
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		rollbackCriacaoConta($D024_Id);
		return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 4)";
	}
	if(mysqli_num_rows($res) > 0){
		$rowC025 = mysqli_fetch_assoc($res);
		$rowC025['C025_Banco_Dados'] = !empty($rowC025['C025_Banco_Dados']) ? $rowC025['C025_Banco_Dados'] : 'e009';
	} else {
		$rowC025['C025_Banco_Dados'] = 'e009';
	}

	// insert
	$C025_Banco_Dados = 'e' . str_pad((substr($rowC025['C025_Banco_Dados'], 1, 3) + 1), '3', '0', STR_PAD_LEFT);
	$login = mysqli_real_escape_string($conNOTA, $form['login']);
	$senha = mysqli_real_escape_string($conNOTA, $form['senha']);
	$email = mysqli_real_escape_string($conNOTA, $rowD024['D024_E_Mail']);
	$CNPJ = str_replace(array('.', '/', '-', ' '), '', $rowD024['D024_Cnpj']);
	
	$res = mysqli_query($conNOTA, "INSERT INTO C025 (C025_D024_Id, C025_Banco_Dados, C025_Login, C025_Senha, C025_Email, C025_C026_Id, C025_Dominio, C025_Flag_Transferido) VALUES ('{$D024_Id}', '{$C025_Banco_Dados}', '{$CNPJ}', '{$senha}', '{$email}', 0, '{$form['Dominio']}', 1)");
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		rollbackCriacaoConta($D024_Id);
		return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 5)";
	}

	// Associar banco ao cliente (C024)
	$res = mysqli_query($conNOTA, "INSERT INTO C024 (C024_Banco_Dados, C024_D024_Id, C024_C026_Id) VALUES  ('{$C025_Banco_Dados}', '{$D024_Id}', 0)");
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		rollbackCriacaoConta($D024_Id);
		return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 6)";
	}

	//Se foi especificado um dominio, a criação da base de dados é local
	if(!empty($form['Dominio']) && !empty($form['Site'])){
		$retorno = criarDatabaseLocal($C025_Banco_Dados, $rowD024, $login, $senha, $email, $form['Dominio'], $form['Site']);
		if($retorno !== true){
			rollbackCriacaoConta($D024_Id, $C025_Banco_Dados);
			return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 7)";
		}
		if ($form['Site'] == 'fastcrud.com.br') {
            gerarMenuSimples($D024_Id, $form['tipoCadastro']);
        }
		return true;
	}

	/*
	10 - Produtos (Cadastros de dep, setor, grupo, sub grupo, unidades, marcas, aplicações, Locações)
	11 - Bancos/Caixas
	12 - Portadores Receber
	13 - Transportadoras
	14 - Menu fiscal
	15 - Clientes/Fornecedores	
	*/
	$modulosAuxiliares = array(
		'1' => '10,12,13,14,15',
		'2' => '11,12,15',
		'3' => '10',
		'5' => '11,12,15',
	);

	foreach ($modulos as $modulo) {
		$D024_Opcoes_Sistema .= ",".$modulosAuxiliares[$modulo];		
	}

	$retorno = criarDatabase($C025_Banco_Dados, $rowD024, $login, $senha, $email, $D024_Opcoes_Sistema);
	if($retorno !== true){
		rollbackCriacaoConta($D024_Id, $C025_Banco_Dados);
		return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 7)";
	}

	/********************************************************************
	*					ENVIO DO EMAIL DE CONFIRMAÇÃO
	*********************************************************************/
	if(true){ //$form['buy']
		//Calculo dos módulos selecionados e geração do boleto
		$D007_Id = 18;
		$D027_Id = 5;
		$D014_Id = 60;
		$D032_Id = 9;
		$T117_Id = gerarBoleto($valorMensalidade, $D024_Id, $D007_Id, $D027_Id, $D014_Id, $D032_Id);
		if(is_numeric($T117_Id)){
			$retorno = enviarEmailConfirmacao($D024_Id, $T117_Id, $modulos, $valorMensalidade);
			if($retorno !== true){
				rollbackCriacaoConta($D024_Id, $C025_Banco_Dados);
				return "Não foi possível enviar o email de confirmação, por favor verifique o seu e-mail e tente novamente.";
			}
		} else {
			rollbackCriacaoConta($D024_Id, $C025_Banco_Dados);
			return "Ocorreu um erro ao cadastrar sua empresa. Por favor, contate o suporte tecnico. (Código 8)";
		}
	} else {
		$retorno = enviarEmailConfirmacao($D024_Id, false, $modulos);
		if($retorno !== true){
			rollbackCriacaoConta($D024_Id, $C025_Banco_Dados);
			return "Não foi possível enviar o email de confirmação, por favor verifique o seu e-mail e tente novamente.";
		}
	}
	return true;
}


/**
* Gera o menu de opções do sistema com base nas opções compradas (D024_Opcoes_Sistema)
* @param $D024_Id
* @return true/string
*/
function gerarMenu($D024_Id) {
	global $conERP, $conNOTA;
	
	// opcoes
	$sql = "SELECT D024_Opcoes_Sistema FROM D024 WHERE D024_Id = '{$D024_Id}'";
	$res = mysqli_query($conNOTA, $sql);
	if (mysqli_num_rows($res) != 1) {
		return 'D024_Id não existe';
	}
	$row = mysqli_fetch_assoc($res);
	$opcoes = explode(',', $row['D024_Opcoes_Sistema']);
	
	// cria menu
	$menu = <<<'EOT'
<?php
namespace hardness;
$menu = array();
$menu['Cadastros']['Cliente/Fornecedor'][1] = array('(F2)', "abrirJanela(event, divIdRootAberto(), '', menuId('cad001'), 'Clientes e Fornecedores', '.: Clientes e Fornecedores :.', '/cad/cad001/content/cad001ContentPrincipal/', '', 'auto', false, 'center');");
$menu['Cadastros']['Produtos'][5] = array('(F3)', "abrirJanela(event, divIdRootAberto(), '', menuId('cad002'), 'Produtos', '.:Produtos:.', '/cad/cad002/content/grid1/', '', 'auto', false, 'center');");
$menu['Configuração']['Produtos - Tipos'][63]			= "abrirJanela(event, divIdRootAberto(), '', menuId('cad023'), 'Tipos', '.: Tipos :.', '/cad/cad023/content/cad023ContentTipos/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Produtos - Unidades'][64]		= "abrirJanela(event, divIdRootAberto(), '', menuId('cad024'), 'Tipos', '.: Unidades :.', '/cad/cad024/content/cad024ContentUnidades/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Produtos - Locações'][65]		= "abrirJanela(event, divIdRootAberto(), '', menuId('cad019'), 'Locações', '.: Locacações :.', '/cad/cad019/content/cad019ContentLocacoes/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Produtos - Marcas'][66]			= "abrirJanela(event, divIdRootAberto(), '', menuId('cad025'), 'Marcas', '.: Marcas :.', '/cad/cad025/content/cad025ContentPrincipal/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Produtos - Linhas'][67]			= "abrirJanela(event, divIdRootAberto(), '', menuId('cad026'), 'Linhas', '.: Linhas :.', '/cad/cad026/content/cad026ContentPrincipal/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Produtos - Grupos'][68]			= "abrirJanela(event, divIdRootAberto(), '', menuId('cad027'), 'Grupos', '.: Grupos :.', '/cad/cad027/content/cad027ContentGrupos/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Produtos - SubGrupos'][69]		= "abrirJanela(event, divIdRootAberto(), '', menuId('cad028'), 'SubGrupos', '.: SubGrupos :.', '/cad/cad028/content/cad028ContentSubGrupos/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Produtos - Aplicações'][70]		= "abrirJanela(event, divIdRootAberto(), '', menuId('cad029'), 'Aplicações', '.: Aplicações :.', '/cad/cad029/content/cad029ContentAplicacoes/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Usuário'][86]					= "abrirJanela(event, divIdRootAberto(), '', menuId('cad030b'), 'Usuário', '.: Usuário:.', '/cad/cad030/content/cad030ContentCadastroUsuarioAtual/', '', [800,600], false, false, 'center');";
EOT;
	// 1 NF-e - Saída
	if (in_array(1, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Vendas']['Notas Fiscais'][16] 					= "abrirJanela(event, divIdRootAberto(), '', menuId('ven003'), 'Notas Fiscais', '.:Notas Fiscais:.', '/ven/ven003/content/ven003ContentPrincipal/', '', 'auto', false, 'center');";
$menu['Vendas']['Emitir Notas Fiscais'][17]				= "abrirJanela(event, divIdRootAberto(), '', menuId('ven012'), 'Gerar Notas Fiscais', '.:Gerar Notas Fiscais:.', '/ven/ven012/content/ven012ContentPrincipal/', '', 'auto', false, 'center');";
EOT;
	}
	// 2 Vendas - Orçamentos e Pedidos
	if (in_array(2, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Vendas']['Orçamentos'][14]						= "abrirJanela(event, 'ven001', '', menuId('ven001'), 'Orçamentos', '.:Orçamentos:.', '/ven/ven001/content/ven001ContentPrincipal/', '', 'full', false, 'topleft', false);";
$menu['Vendas']['Pedidos'][15]							= "abrirJanela(event, 'ven002', '', menuId('ven002'), 'Pedidos', '.:Pedidos:.', '/ven/ven002/content/ven002content01/', '', 'full', false, 'topleft', false);";
EOT;
	}
	// 3 Estoque - NF-e de Entrada / DE
	if (in_array(3, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Estoque']['Notas Fiscais Recebidas'][21]			= "abrirJanela(event, 'est004', '', menuId('est004'), 'Notas Fiscais Recebidas', '.: Notas Fiscais Recebidas :.', '/est/est004/content/est004ContentPrincipal/', '', 'full', false, 'topleft', false);";
$menu['Estoque']['Divergências de Estoque'][19]			= "abrirJanela(event, 'est001', '', menuId('est001'), 'Divergências de Estoque', '.:Divergências de Estoque:.', '/est/est001/content/est001ContentDivergenciasEstoque/', '', 'full', false, 'topleft', false);";
EOT;
	}
	// 4 Contas a Pagar
	if (in_array(4, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Financeiro']['Contas a Pagar'][36]				= "abrirJanela(event, 'fin002', '', menuId('fin002'), 'Contas a Pagar', '.: Contas a Pagar :.', '/fin/fin002/content/fin002content01/', '', 'full', false, 'topleft', false);";
EOT;
	}
	// 5 Contas a Receber
	if (in_array(5, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Financeiro']['Contas a Receber'][35]				= "abrirJanela(event, 'fin001', '', menuId('fin001'), 'Contas a Receber', '.: Contas a Receber :.', '/fin/fin001/content/fin001content01/', '', 'full', false, 'topleft', false);";
EOT;
	}
	// 6 Caixa e Bancos
	if (in_array(6, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Financeiro']['Caixa e Bancos'][37]				= "abrirJanela(event, 'fin003', '', menuId('fin003'), 'Caixa e Bancos', '.: Caixa e Bancos :.', '/fin/fin003/content/fin003ContentGeral/', '', 'full', false, 'topleft', false);";
EOT;
	}
	// 7 Boletos
	if (in_array(7, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Financeiro']['Boleto Bancário'][38]				= "abrirJanela(event, 'fin005', '', menuId('fin005'), 'Boletos Bancários', '.: Boletos Bancários :.', '/fin/fin005/content/fin005ContentPrincipal/', '', 'full', false, 'topleft', false);";
EOT;
	}
	// 8 Remessa Bancária
	if (in_array(8, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Financeiro']['Contas a Receber'][35]				= "abrirJanela(event, 'fin001', '', menuId('fin001'), 'Contas a Receber', '.: Contas a Receber :.', '/fin/fin001/content/fin001content01/', '', 'full', false, 'topleft', false);";
EOT;
	}
	// 9 Retorno Bancário
	if (in_array(9, $opcoes)) {
		$menu .= <<<'EOT'
$menu['Financeiro']['Contas a Receber'][35]				= "abrirJanela(event, 'fin001', '', menuId('fin001'), 'Contas a Receber', '.: Contas a Receber :.', '/fin/fin001/content/fin001content01/', '', 'full', false, 'topleft', false);";
EOT;
	}
	$menu .= "\n";

	// salvar menu nas personalizações da empresa
	$sql = "SELECT LOWER(C024_Banco_Dados) as C024_Banco_Dados FROM C024 WHERE C024_D024_Id = '{$D024_Id}'";
	$res = mysqli_query($conNOTA, $sql);
	if (mysqli_num_rows($res) != 1) {
		return 'Não encontrado banco de dados para esta empresa.';
	}
	$row = mysqli_fetch_assoc($res);
    mkdir("/var/www/dados/v3/dados_usuarios/{$row['C024_Banco_Dados']}/personalizacaoConf", 0777, true);
	$arqMenuPerso = "/var/www/dados/v3/dados_usuarios/{$row['C024_Banco_Dados']}/personalizacaoConf/GLOBAL-menu.php";
    file_put_contents($arqMenuPerso, $menu);
}

/**
 * Gera o menu com poucas opções, utilizado pelo fastcrud.com.br
 * @param $D024_Id
 * @param $tipoCadastro PF ou PJ
 * @return true/string
 */
function gerarMenuSimples($D024_Id, $tipoCadastro) {
    global $conERP, $conNOTA;

    // cria menu
    if ($tipoCadastro == 'PJ') {
        $menu = <<<'EOT'
<?php
namespace hardness;
$menu = array();
$menu['Configuração']['Builder']  		                                = "var win = window.open('/hardness3/outros/builder/index.php', '_blank'); win.focus();";
if (isset($g['usuarioAtual']) && $g['usuarioAtual'] == 63) {
	$menu['Outros']['Atualização BD']									= "abrirJanela(event, divIdRootAberto(), '', menuId('atualizarBase'), 'Atualizar', '.: Atualizar Base :.', '/sistema/scripts/atualizacaoBase/', '', false, false, 'center');"; //[53]
	$menu['Outros']['Atualização BD Auditoria']							= "abrirJanela(event, divIdRootAberto(), '', menuId('atualizarBaseAuditoria'), 'Atualizar', '.: Atualizar Base Auditoria :.', '/sistema/scripts/atualizacaoBaseAuditoria/', '', false, false, 'center');"; //[53]
	$menu['Outros']['MySQL']											= "window.open('/hardness/config/cad_executa_sql.php3?Oauth=true', '', 'width=750, height=550, titlebar=no,status=no,menubar=no');"; // [118]
	$menu['Outros']['Teclas de Atalho']									= "abrirJanela(event, divIdRootAberto(), '', menuId('cad055'), 'Teclas de Atalho', '.: Teclas de Atalho :.', '/cad/cad055/content/principal/', '', false, false, 'center');"; // [161]
	$menu['Outros']['Exportar/Importar Personalizações'] 				= "abrirJanela(event, divIdRootAberto(), '', menuId('impExpPers'), 'Exportar/Importar Personalizações', '.: Exportar/Importar Personalizações :.', '/sistema/funcoes/expImpPersonalizacao/content/', '', false, false, 'center');";       
	$menu['Outros']['Copiar Tabelas'] 									= "abrirJanela(event, divIdRootAberto(), '', menuId('copiarTabelas'), 'Copiar Tabelas', '.: Copiar Tabelas :.', '/sistema/funcoes/copiarTabelas/content/', '', false, false, 'center');";
	$menu['Outros']['Layout Exportações']	 							= "abrirJanela(event, divIdRootAberto(), '', menuId('DadosPDV'), 'Exportar Dados PDV', '.: Layouts de Exportação :.', '/outros/layoutExportacao/content/layout/', '', [1000,600], false, false, 'center');";
	$menu['Outros']['Log Geral']										= "abrirJanela(event, divIdRootAberto(), '', menuId('LogGeral'), 'Log Geral', '.: Log Geral :.', '/sistema/logSistema/outro/contentGeral/', '', [1024,600], false, 'full');";
	$menu['Outros']['Sistema']											= "abrirJanela(event, divIdRootAberto(), '', menuId('Sistema'), 'Sistema', '.: Sistema :.', '/outros/dadosSistema/content/dadosSistema/', '', [1024,600], false, 'full');";
	$menu['Outros']['Script atualizar Contatos']						= "abrirJanela(event, divIdRootAberto(), '', menuId('LogGeral'), 'Atualizar contatos', '.: Atualizar Contatos :.', '/outros/atualizarContatosE001/scriptConexaoBases/scriptConexaoBases/', '', [1024,600], false, 'full');";
	$menu['Outros']['B2B']  											= "abrirJanela(event, divIdRootAberto(), '', menuId('b2b001'), 'B.2.B', '.: B.2.B :.', '/b2b/b2b001/content/contentB2BPrincipal/', '', [1024,600], false, 'full');";
}
$menu['Configuração']['Usuários/Perfis'][84]							= "abrirJanela(event, divIdRootAberto(), '', menuId('cad030'), 'Usuários', '.: Usuários :.', '/cad/cad030/content/cad030ContentPrincipal/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Usuário'][86]									= "abrirJanela(event, divIdRootAberto(), '', menuId('cad030b'), 'Usuário', '.: Usuário:.', '/cad/cad030/content/cad030ContentCadastroUsuarioAtual/', '', [650,550], false, false, 'center');";
$menu['Configuração']['Empresas'][57]									= "abrirJanela(event, divIdRootAberto(), '', menuId('cad031'), 'Empresas', '.: Empresas :.', '/cad/cad031/content/cad031ContentEmpresas/', '', [950,550], false, false, 'center');";
if($g['usuarioAtual'] == 63){
	$menu['Configuração']['Personalizações']          					= "abrirJanela(event, divIdRootAberto(), '', menuId('Personalizacao'), '', '', '/sistema/personalizacaoConf/content/sistemaPConfPrincipal/', '', 'full', false, 'topleft', false);";

$menu['Configuração']['Sobre']											= "abrirJanela(event, divIdRootAberto(), '', menuId('sobre'), 'Sobre o Sistema', 'Sobre o Sistema', '/sistema/outros/sobre/content/', '', [530,370], false, 'center');";
EOT;
    } else {
        $menu = <<<'EOT'
<?php
namespace hardness;
$menu = array();
$menu['Configuração']['Builder']  		                                = "var win = window.open('/hardness3/outros/builder/index.php', '_blank'); win.focus();";
if (isset($g['usuarioAtual']) && $g['usuarioAtual'] == 63) {
	$menu['Outros']['Atualização BD']									= "abrirJanela(event, divIdRootAberto(), '', menuId('atualizarBase'), 'Atualizar', '.: Atualizar Base :.', '/sistema/scripts/atualizacaoBase/', '', false, false, 'center');"; //[53]
	$menu['Outros']['Atualização BD Auditoria']							= "abrirJanela(event, divIdRootAberto(), '', menuId('atualizarBaseAuditoria'), 'Atualizar', '.: Atualizar Base Auditoria :.', '/sistema/scripts/atualizacaoBaseAuditoria/', '', false, false, 'center');"; //[53]
	$menu['Outros']['MySQL']											= "window.open('/hardness/config/cad_executa_sql.php3?Oauth=true', '', 'width=750, height=550, titlebar=no,status=no,menubar=no');"; // [118]
	$menu['Outros']['Teclas de Atalho']									= "abrirJanela(event, divIdRootAberto(), '', menuId('cad055'), 'Teclas de Atalho', '.: Teclas de Atalho :.', '/cad/cad055/content/principal/', '', false, false, 'center');"; // [161]
	$menu['Outros']['Exportar/Importar Personalizações'] 				= "abrirJanela(event, divIdRootAberto(), '', menuId('impExpPers'), 'Exportar/Importar Personalizações', '.: Exportar/Importar Personalizações :.', '/sistema/funcoes/expImpPersonalizacao/content/', '', false, false, 'center');";       
	$menu['Outros']['Copiar Tabelas'] 									= "abrirJanela(event, divIdRootAberto(), '', menuId('copiarTabelas'), 'Copiar Tabelas', '.: Copiar Tabelas :.', '/sistema/funcoes/copiarTabelas/content/', '', false, false, 'center');";
	$menu['Outros']['Layout Exportações']	 							= "abrirJanela(event, divIdRootAberto(), '', menuId('DadosPDV'), 'Exportar Dados PDV', '.: Layouts de Exportação :.', '/outros/layoutExportacao/content/layout/', '', [1000,600], false, false, 'center');";
	$menu['Outros']['Log Geral']										= "abrirJanela(event, divIdRootAberto(), '', menuId('LogGeral'), 'Log Geral', '.: Log Geral :.', '/sistema/logSistema/outro/contentGeral/', '', [1024,600], false, 'full');";
	$menu['Outros']['Sistema']											= "abrirJanela(event, divIdRootAberto(), '', menuId('Sistema'), 'Sistema', '.: Sistema :.', '/outros/dadosSistema/content/dadosSistema/', '', [1024,600], false, 'full');";
	$menu['Outros']['Script atualizar Contatos']						= "abrirJanela(event, divIdRootAberto(), '', menuId('LogGeral'), 'Atualizar contatos', '.: Atualizar Contatos :.', '/outros/atualizarContatosE001/scriptConexaoBases/scriptConexaoBases/', '', [1024,600], false, 'full');";
	$menu['Outros']['B2B']  											= "abrirJanela(event, divIdRootAberto(), '', menuId('b2b001'), 'B.2.B', '.: B.2.B :.', '/b2b/b2b001/content/contentB2BPrincipal/', '', [1024,600], false, 'full');";
}
$menu['Configuração']['Usuários/Perfis'][84]							= "abrirJanela(event, divIdRootAberto(), '', menuId('cad030'), 'Usuários', '.: Usuários :.', '/cad/cad030/content/cad030ContentPrincipal/', '', [950,550], false, false, 'center');";
$menu['Configuração']['Usuário'][86]									= "abrirJanela(event, divIdRootAberto(), '', menuId('cad030b'), 'Usuário', '.: Usuário:.', '/cad/cad030/content/cad030ContentCadastroUsuarioAtual/', '', [650,550], false, false, 'center');";
$menu['Configuração']['Conta'][57]									    = "abrirJanela(event, divIdRootAberto(), '', menuId('cad031'), 'Empresas', '.: Empresas :.', '/cad/cad031/content/cad031ContentEmpresas/', '', [950,550], false, false, 'center');";
if($g['usuarioAtual'] == 63){
    $menu['Configuração']['Personalizações'] 								= "abrirConfiguracoes();";
}
$menu['Configuração']['Sobre']											= "abrirJanela(event, divIdRootAberto(), '', menuId('sobre'), 'Sobre o Sistema', 'Sobre o Sistema', '/sistema/outros/sobre/content/', '', [530,370], false, 'center');";
EOT;
    }

    $menu .= "\n";

    // salvar menu nas personalizações da empresa
    $sql = "SELECT LOWER(C024_Banco_Dados) as C024_Banco_Dados FROM C024 WHERE C024_D024_Id = '{$D024_Id}'";
    $res = mysqli_query($conNOTA, $sql);
    if (mysqli_num_rows($res) != 1) {
        return 'Não encontrado banco de dados para esta empresa.';
    }
    $row = mysqli_fetch_assoc($res);
    mkdir("/var/www/dados/v3/dados_usuarios/{$row['C024_Banco_Dados']}/personalizacaoConf", 0777, true);
    $arqMenuPerso = "/var/www/dados/v3/dados_usuarios/{$row['C024_Banco_Dados']}/personalizacaoConf/GLOBAL-menu.php";
    file_put_contents($arqMenuPerso, $menu);
}

/**
* Verifica se um domínio é válido para uso
* @param $modulos (array)
* @return array
*/
function calcularPreco($modulos) {
	$precos = array(     //Define o valor de cada modulo
	// 'id' => 'valor'
		'1' => '89.00',
		'2' => '89.00',
		'3' => '89.00',
		'4' => '50.00',
		'5' => '50.00',
		'6' => '50.00',
		'7' => '80.00',
		'8' => '80.00',
		'9' => '80.00'
	);
	$percentualDescontoPorModulo = 2; //Desconto por modulo adicional
	$modulosMinDesconto = 1; //Define quantos modulos são necessários para gerar um desconto


	$valorBruto = 0;
	$valorTotal = 0;
	$valorDesconto = 0;
	$percentualDesconto = 0;
	$modulosDesconto = (count($modulos) > $modulosMinDesconto) ? count($modulos) - $modulosMinDesconto : 0;


	if(count($modulos) > 0){
		foreach($modulos as $id){
			$valorBruto += $precos[$id];
		}

		if($modulosDesconto > 0){
			$percentualDesconto = $modulosDesconto * $percentualDescontoPorModulo;
			$valorDesconto = ($valorBruto*($percentualDesconto/100));
		}
		$valorTotal = $valorBruto - $valorDesconto;
	}

	
	$retorno = array();
	$retorno['valorTotal'] = $valorTotal;
	$retorno['valorBruto'] = $valorBruto;
	$retorno['valorDesconto'] = $valorDesconto;
	$retorno['percentualDesconto'] = $percentualDesconto;
	return $retorno;
}

/**
* Gera os registros em contas a receber e o boleto
* @param valor do Boleto, D024_Id, D027_Id, D007_Id, D014_Id e D032_Id
* @return T117_Id
*/

function gerarBoleto($valorMensalidade, $D024_Id, $D007_Id, $D027_Id, $D014_Id, $D032_Id){
	global $conNOTA,$sys;

	include('../old/lib/funcoes.php');
	include("../hardness/boleto/include/funcoes_341.php");

	mysqli_query($conNOTA, "USE {$sys['dbDatabase']}");
	$D024 = mysqli_query($conNOTA, "SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'");
	$D024 = mysqli_fetch_array($D024);

	
	//Seleciona o ultimo documento com CADSIS no inicio
	$sqlDoc = "SELECT SUBSTRING(T002_Numero_Documento, 7, 1) as numeroDocumento FROM T002 WHERE T002_Numero_Documento LIKE 'CADSIS%' ORDER BY ROUND(SUBSTRING(T002_Numero_Documento, 7, 1)) DESC LIMIT 1";
	$sqlDoc = mysqli_query($conNOTA, $sqlDoc);
	if(!empty($erro)){
		return "Erro cadastrando empresa! Por favor, contate o suporte tecnico. (2)";
	}
	if(mysqli_num_rows($sqlDoc) > 0){
		$resDoc = mysqli_fetch_array($sqlDoc);
		$resDoc['numeroDocumento']++;
		$numeroDocumento = 'CADSIS'.$resDoc['numeroDocumento'];
	} else {
		$numeroDocumento = 'CADSIS1';
	}

	/*
	*	INSERE O TÍTULO A RECEBER
	*/
	$data_vencimento = date('Y-m-d', strtotime(date("Y-m-d"). ' + 7 days'));
	$numeroDuplicata = $numeroDocumento.'A';
	$sql = "INSERT INTO T002 ( 
				T002_D024_Id,
				T002_D027_Id,
				T002_D014_Id,
				T002_D032_Id,
				T002_C004_Id,
				T002_Nome_Cliente,
				T002_Numero_Duplicata,
				T002_Numero_Documento,
				T002_Numero_Parcela,
				T002_Total_Parcelas,
				T002_Data_Emissao,
				T002_Data_Vencimento,
				T002_Data_Vencimento_Original,
				T002_Data_Recebimento,
				T002_Valor_Duplicata,
				T002_Valor_Juros,
				T002_Valor_Desconto,
				T002_Valor_Total,
				T002_Data_Lancamento,
				T002_Hora_Lancamento
			) VALUES ( 
				'{$D024_Id}',
				'{$D027_Id}',
				'{$D014_Id}',
				'{$D032_Id}',
				'1',
				'{$D024['D024_Nome_Empresa']}',
				'{$numeroDuplicata}',
				'{$numeroDocumento}',
				'1',
				'1',
				current_date(),
				'{$data_vencimento}',
				'{$data_vencimento}',
				'0000-00-00',
				'{$valorMensalidade}',
				'0',
				'0',
				'{$valorMensalidade}',
				current_date(), 
				current_time()
			)";
	$res = mysqli_query($conNOTA, $sql);
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		return "Erro cadastrando empresa! Por favor, contate o suporte tecnico. (3)";
	}

	/*
	* SELECT DO TÍTULO A RECEBER INSERIDO
	*/
	$T002_Id = mysqli_insert_id($conNOTA);
	$T002 = mysqli_query($conNOTA, "SELECT * FROM T002 WHERE T002_Id = '{$T002_Id}'");
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		return "Erro cadastrando empresa! Por favor, contate o suporte tecnico. (4)";
	}
	$T002 = mysqli_fetch_array($T002);
	
	/*
	* SELECT DO BANCO RELACIONADO AO TÍTULO A RECEBER
	*/
	$D007 = "SELECT * FROM D007 LEFT JOIN D027 ON D027_D007_Id = D007_Id LEFT JOIN D062 ON D007_D062_Id = D062_Id WHERE D007_Id = '{$D007_Id}'";
	$D007 = mysqli_query($conNOTA, $D007);
	$erro = mysqli_error($conNOTA);
	if(!empty($erro)){
		return "Erro cadastrando empresa! Por favor, contate o suporte tecnico. (5)";
	}

	if(mysqli_num_rows($D007) > 0){
		$D007 = mysqli_fetch_array($D007);
		$sql = "INSERT INTO T117 ( 
					T117_C004_Id, 
					T117_D024_Id, 
					T117_D007_Id, 
					T117_Data_Emissao, 
					T117_Data_Vencimento, 
					T117_Valor_Total, 
					T117_Percentual_Juros_Dia, 
					T117_Percentual_Multa, 
					T117_Instrucoes, 
					T117_Nosso_Numero, 
					T117_Flag_Layout, 
					T117_Flag_Com_Registro, 
					T117_Codigo_Cedente, 
					T117_Aceite, 
					T117_Especie, 
					T117_Especie_Documento, 
					T117_Uso_Banco, 
					T117_T002_Id 
				) VALUES (
					'1', 
					'{$D024_Id}', 
					'{$D007['D007_Id']}', 
					current_date(), 
					'{$T002['T002_Data_Vencimento']}', 
					'{$T002['T002_Valor_Duplicata']}', 
					'{$D007['D007_Percentual_Juros_Dia']}', 
					'{$D007['D007_Percentual_Multa']}', 
					'{$D007['D007_Instrucoes_Boleto']}', 
					'{$D007['D007_Nosso_Numero']}', 
					'S', 
					'S', 
					'{$D007['D007_Codigo_Cedente']}', 
					'{$D007['D007_Aceite']}', 
					'{$D007['D007_Especie']}', 
					'{$D007['D007_Especie_Documento']}', 
					'{$D007['D007_Uso_Banco']}', 
					'{$T002_Id}'
				)";
		mysqli_query($conNOTA, $sql);
		$erro = mysqli_error($conNOTA);
		if(!empty($erro)){
			return "Erro cadastrando empresa! Por favor, contate o suporte tecnico. (6)";
		}
		//Atualiza o título a receber com o T117_Id do boleto criado
		$T117_Id = mysqli_insert_id($conNOTA);
		if(empty($T117_Id_Primeiro_Boleto)){
			$T117_Id_Primeiro_Boleto = $T117_Id;
		}
		mysqli_query($conNOTA, "UPDATE T002 SET T002_T117_Id = '{$T117_Id}' WHERE T002_Id = '{$T002_Id}'");
		$erro = mysqli_error($conNOTA);
		if(!empty($erro)){
			return "Erro cadastrando empresa! Por favor, contate o suporte tecnico. (7)";
		}
		/*
		* Geração do boleto
		*/
		$T117_Data_Vencimento = $T002['T002_Data_Vencimento'];
		$nValor_Total=$T002['T002_Valor_Duplicata'];
		if(empty($D007['D007_Nosso_Numero'])){
		   $D007['D007_Nosso_Numero']=1;
		} else {
		   $D007['D007_Nosso_Numero']++;
		}
		if($D007['D062_Numero_Banco']=='399'){
		   $D007['D007_Nosso_Numero']=STR_PAD($D007['D007_Nosso_Numero'],5,"0",STR_PAD_LEFT);
		}
		mysqli_query($conNOTA, "UPDATE D007 SET D007_Nosso_Numero = '{$D007['D007_Nosso_Numero']}' WHERE D007_Id = '{$D007['D007_Id']}'");
		$erro = mysqli_error($conNOTA);
		if(!empty($erro)){
			return "Erro cadastrando empresa! Por favor, contate o suporte tecnico. (8)";
		}
		
		//Geração do Boleto 
		// CODIGO PADRÃO PARA O BANCO ITAU (341)
		$codigobanco = "341";
		$codigo_banco_com_dv = b341_geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$vencimento = gCorrigeData($T002['T002_Data_Vencimento']);

		//Gerar o codigo de barras
		$dadosboleto["codigo_barras"] = gerarCodigoBarras('341',
		                                          $D007['D007_Numero_Agencia'],
		                                          $D007['D007_Numero_Conta'],
		                                          $D007['D007_Digito_Verificador_Conta'],
		                                          $D007['D007_Tipo_Carteira_Banco_Brasil'],
		                                          $vencimento,
		                                          $valorMensalidade,
		                                          $D007['D007_Nosso_Numero']);
		                                          
		$dadosboleto["linha_digitavel"] = b341_monta_linha_digitavel($dadosboleto["codigo_barras"]);

		$nosso_numero = b341_formata_numero($D007['D007_Nosso_Numero'],8,0);

		$cClausula  = " update T117 set ";
		$cClausula .= " T117_Nosso_Numero='".$nosso_numero."', ";
		$cClausula .= " T117_Linha_Digitavel='".$dadosboleto["linha_digitavel"]."', ";
		$cClausula .= " T117_Codigo_Barras='".$dadosboleto["codigo_barras"]."' ";
		$cClausula .= " where T117_Id='$T117_Id' ";
		mysqli_query($conNOTA, $cClausula);
		$cErro = mysqli_error($conNOTA);
		if (!empty($cErro)){
		   return "Erro cadastrando empresa! Por favor, contate o suporte tecnico (9)";
		}

	} else {
		return "Erro cadastrando empresa! Por favor, contate o suporte tecnico (10)";
	}
	return $T117_Id_Primeiro_Boleto;
}

/**
* Envia email de confirmação
* @param $D024_Id, $T117_Id, $modulos (array)
* @return true / false
*/
function enviarEmailConfirmacao($D024_Id, $T117_Id, $modulos, $valorMensalidade){
	global $conNOTA,$sys;

	mysqli_query($conNOTA, "USE {$sys['dbDatabase']}");
	require_once("../../hardness3/bibliotecas/phpmailer/class.phpmailer.php");

	$C007 = mysqli_query($conNOTA, "SELECT * FROM C007 WHERE C007_Id = '63'");
	$C007 = mysqli_fetch_assoc($C007);
	if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
		return false;
	}

	$monta_['cc'] = '';
	$sqlD024 = mysqli_query($conNOTA, "SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'");
	$rowD024 = mysqli_fetch_array($sqlD024);
	
	$sql = mysqli_query($conNOTA, "SELECT * from C004 where C004_Id='1'");
	$empresaAtual = mysqli_fetch_array($sql);
		

	/*
	*	MONTA VARIAVEL COM MÓDULOS SELECIONADOS
	*/
	$modulosSelecionados = '';
	$arrayModulos = array(
		'1' => 'Nota Fiscal Eletronica - NFe e Nota Fiscal de Consumidor - NFCe',
		'2' => 'Financeiro - (Caixas e Bancos, Contas a Receber e Contas a Pagar)',
		'5' => 'Emissão de boletos',
		'3' => 'Estoque - (NFe Entrada, Divergência de Estoque)',
		'4' => 'Orçamento e pedido de venda',
		'6' => 'Cotação e ordem de compra'
	);
	foreach($modulos as $id){
		$modulosSelecionados .= '<li>'.$arrayModulos[$id].'</li>';
	}

	$valorMensalidade = gCorrigeNumero($valorMensalidade);
	$nomeEmpresa = explode(" ", $rowD024['D024_Nome_Empresa']);
	$nomeEmpresa = $nomeEmpresa[0];
	$CNPJ = str_replace(array('.','-','/'), '', $rowD024['D024_Cnpj']);

	$dataTrintaDias = date('d/m/Y', strtotime("+90 days"));
	$monta_['Assunto'] = "Bem vindo a Hardness Sistemas";
	$monta_['Para'] = $rowD024['D024_E_Mail'];
	$monta_['Corpo'] = <<<EOT
		<div style="width: 600px; color:black; font-family: Lato,Helvetica,Arial; color: #444; font-size: 14px; margin-left: auto; margin-right: auto;">
			<div style="padding: 7px; background-color: #0C4F72; background-image: -webkit-linear-gradient(bottom, #0C4F72, #324A77); background-image: -moz-linear-gradient(bottom, #0C4F72, #324A77); background-image: -ms-linear-gradient(bottom, #0C4F72, #324A77); background-image: -o-linear-gradient(bottom, #0C4F72, #324A77); background-image: linear-gradient(to top, #0C4F72, #324A77); color: white;">
				<img src="http://hardness.hardness.com.br/hardness3/static/img/logo_hardness.png"><br />
			</div>
			<div style="padding: 12px; border: 1px solid #999; border-top: 0;">
				<div style="font-size: 18px; font-weight: bold; margin-top: 10px;">Olá {$nomeEmpresa},</div>
				<div style="font-size: 14px; text-align: justify;">
					Obrigado por utilizar os serviços da Hardness Sistemas, esperamos que a nossa ferramente auxílie nas necessidades de sua empresa. Você poderá acessar o sistema através do nosso site:
					<a href="http://www.hardness.com.br">http://www.hardness.com.br.</a><br />
					<p>Informe {$CNPJ} no campo <i>login</i> e clique em <i>Entrar</i>.</p>
				</div>
EOT;
	if($T117_Id === false){
		$monta_['Corpo'] .= <<<EOT
			<p>O período de teste é de 30 dias, o seus dados serão mantidos até o dia {$dataTrintaDias}, após essa data seus dados serão excluídos automaticamente de nossa base de dados.</p>
			<div style="font-size: 16px; text-align: center; font-weight: bold; margin: 10px 0 5px 0;">Resumo do Teste</div>
			<div style="margin-left: 20px;">
				{$modulosSelecionados}
			</div>
EOT;
	} else {
		$T117 = mysqli_query($conNOTA, "SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id = T117_Id LEFT JOIN T007 ON T007_Id = T002_T007_Id WHERE T117_Id = '{$T117_Id}'");
		$T117 = mysqli_fetch_array($T117);

		$cQuery = md5($T117['T117_Id'].$T117['T117_D024_Id']);
		$path = base64_encode("http://hardness.hardness.com.br/hardness3/dados_usuarios/{$sys['dbDatabase']}/");
		$db = md5("{$sys['dbDatabase']}");
		$urlBoleto = "http://hardness.hardness.com.br/hardness3/hardness/boleto/gerar_boleto_empresa.php?cQuery=".$cQuery."&path=".$path."&query=".$db."&buscarBoletoBanco=suporte";

		$monta_['Corpo'] .= <<<EOT
			<div style="font-size: 16px; text-align: center; font-weight: bold; margin: 10px 0 5px 0;">Resumo da compra</div>
			<div style="margin-left: 20px;">
				{$modulosSelecionados}
			</div>
			<br>
			<br>
			<div style="text-align: left; font-size: 16px; font-weight: bold; margin: 5px 0 -10px 40px;">Valor da mensalidade: R$ {$valorMensalidade}</div>
			<p>Para visualizar e imprimir o boleto, <a target="_blank" href="{$urlBoleto}">clique aqui</a></p>
EOT;
	}
	$monta_['Corpo'] .= <<<EOT
				<div style="text-align: justify; margin-top: 15px;">Caso necessite de auxílio com a utilização do sistema, dúvidas ou envio de sugestões, 
				entre em contato conosco através do email suporte@hardness.com.br ou por telefone <br />41 3344-2938, estamos a sua disposição.</div>
				<p><div style="text-align: center; font-size: 11px; margin-bottom: -15px;">© Hardness Sistemas. Todos os direitos reservados.</div></p>
			</div>
		</div>
EOT;
	
	$monta_['Anexos'] = array();
	if (empty($monta_['Para'])) { 
		return false;
	}
	
	if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
		$monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
	}
	//$monta_['Corpo'] = htmlentities($monta_['Corpo'],ENT_COMPAT,'utf-8');	

	$mail = new \phpmailer;

	// Mail
	$mail->From     = $C007['C007_Email'];
	$mail->FromName = $C007['C007_Primeiro_Nome'];
	$mail->Host     = $C007['C007_Email_SMTP_Servidor'];
	$mail->Mailer   = "smtp";
	$mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
	$mail->Username = $C007['C007_Email_SMTP_Usuario'];
	$mail->Password = $C007['C007_Email_SMTP_Senha'];
	$mail->Subject	= $monta_['Assunto'];
	$mail->Body     = $monta_['Corpo'];
	$mail->AltBody  = "ALT BODY";
	$mail->CharSet  = 'utf-8';
	$mail->SetLanguage("br");
	$mail->IsHTML(true);
	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com") {
		$mail->SMTPSecure   = "ssl";
		$mail->Port         = "465"; 
	}

	if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com") {
		$mail->SMTPSecure   = "ssl";
	}

	if (!empty($C007['C007_Email_SMTP_Porta'])) {
		$mail->Port         = $C007['C007_Email_SMTP_Porta']; 
	}

	if (empty($monta_['Para']) && !empty($monta_['cc'])) {
		$monta_['Para'] = $monta_['cc'];
		$monta_['cc'] = '';
	}
	
	$todosEmailsEnviados = array();
	$mPara=preg_split("/[;,]+/",$monta_['Para']);
	for($x=0;$x<count($mPara);$x++) {
		$email = $mPara[$x];
		if (strpos($email, '<') !== false) {
			$email = explode('<', $email);
			$email = explode('>', $email[1]);
			$email = $email[0];
		}
		$todosEmailsEnviados[] = strtolower(trim($email));
		$mail->AddAddress($email);
	}

	if($mail->Send()){ 
		if($T117_Id !== false){
			mysqli_query($conNOTA, "UPDATE T117 set T117_Data_Envio_Email=now(), T117_Flag_Enviar_Email = 'N' where T117_Id='{$T117_Id}'");
		}
	    
		// Gravar como e-mail enviado
		$T139_De = mysqli_real_escape_string($conNOTA, "\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
		$T139_Estrutura = serialize($T139_Estrutura);
		$T139_Para = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
		$sql = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id, T139_T117_Id) VALUES ('S', 0, '".mysqli_real_escape_string($conNOTA, $monta_['Assunto'])."', '{$T139_De}', '".mysqli_real_escape_string($conNOTA, $T139_Para)."', '".date('Y-m-d H:i:s')."', '" . mysqli_real_escape_string($conNOTA, $T139_Estrutura) . "', '".mysqli_real_escape_string($conNOTA, $monta_['Corpo'])."', '1', '".mysqli_real_escape_string($conNOTA, $T117['T117_D024_Id'])."', '".mysqli_real_escape_string($conNOTA, $g['usuarioAtual'])."', '".mysqli_real_escape_string($conNOTA, $T117_Id)."');";
		mysqli_query($conNOTA, $sql);
		return true;
	} else {
		return false; 
	}
}

/**
* Cria database da nova empresa
* @param $C025_Banco_Dados, $T117_Id, $modulos
* @return true / string
*/
function criarDatabase($C025_Banco_Dados, $rowD024, $login, $senha, $email, $modulos) {
	global $confUsuarioERP, $conERP, $conNOTA;

	/************************************************************************
	*				CRIANDO DATABASE DA EMPRESA
	************************************************************************/
	$sqlD018 = mysqli_query($conNOTA, "SELECT D018_Codigo_IBGE FROM D018 WHERE D018_Id = '{$rowD024['D024_D018_Id']}'");
	$rowD018 = mysqli_fetch_array($sqlD018);
	$sqlD020 = mysqli_query($conNOTA, "SELECT D020_Codigo_IBGE FROM D020 WHERE D020_Id = '{$rowD024['D024_D020_Id']}'");
	$rowD020 = mysqli_fetch_array($sqlD020);

	$D024_Nome_Empresa 		 = base64_encode($rowD024['D024_Nome_Empresa']);
	$D024_Cnpj 				 = base64_encode($rowD024['D024_Cnpj']);
	$D024_Inscricao_Estadual = base64_encode($rowD024['D024_Inscricao_Estadual']);
	$D024_Id 				 = base64_encode($rowD024['D024_Id']);
	$D018_Codigo_IBGE   	 = base64_encode($rowD018['D018_Codigo_IBGE']);
	$D020_Codigo_IBGE   	 = base64_encode($rowD020['D020_Codigo_IBGE']);
	$D024_Bairro 			 = base64_encode($rowD024['D024_Bairro']);
	$D024_Logradouro 		 = base64_encode($rowD024['D024_Endereco']);
	$D024_Numero 	 		 = base64_encode($rowD024['D024_Numero']);
	$D024_Complemento 	 	 = base64_encode($rowD024['D024_Complemento']);
	$D024_Cep 	 		 	 = base64_encode($rowD024['D024_Cep']);
	$D024_DDD_Telefone_1 	 = base64_encode($rowD024['D024_DDD_Telefone_1']);
	$D024_Telefone_1 	 	 = base64_encode($rowD024['D024_Telefone_1']);

	shell_exec("bash /var/www/hardness3/site/scriptDump.sh {$C025_Banco_Dados} {$senha} {$login} {$email} {$modulos} {$D024_Nome_Empresa} {$D024_Cnpj} {$D024_Inscricao_Estadual} {$D024_Id} {$D018_Codigo_IBGE} {$D020_Codigo_IBGE} {$D024_Bairro} {$D024_Logradouro} {$D024_Numero} {$D024_Complemento} {$D024_Cep} {$D024_DDD_Telefone_1} {$D024_Telefone_1} 2>&1");

	// Salva comando como log
	file_put_contents("/var/www/sites/logCriarConta/{$C025_Banco_Dados}.log", "bash /var/www/hardness3/site/scriptDump.sh {$C025_Banco_Dados} {$senha} {$login} {$email} {$modulos} {$D024_Nome_Empresa} {$D024_Cnpj} {$D024_Inscricao_Estadual} {$D024_Id} {$D018_Codigo_IBGE} {$D020_Codigo_IBGE} {$D024_Bairro} {$D024_Logradouro} {$D024_Numero} {$D024_Complemento} {$D024_Cep} {$D024_DDD_Telefone_1} {$D024_Telefone_1} 2>&1"); 

	return true;
}

function rollbackCriacaoConta($D024_Id, $C025_Banco_Dados = ''){
	global $conERP, $conNOTA,$sys;

	if(!empty($D024_Id)){
		mysqli_query($conNOTA, "USE {$sys['dbDatabase']}");
		mysqli_query($conNOTA, "DELETE FROM D024 WHERE D024_Id = '{$D024_Id}'");
		mysqli_query($conNOTA, "DELETE FROM C025 WHERE C025_D024_Id = '{$D024_Id}'");
		mysqli_query($conNOTA, "DELETE FROM C024 WHERE C024_D024_Id = '{$D024_Id}'");
		mysqli_query($conNOTA, "DELETE FROM T002 WHERE T002_D024_Id = '{$D024_Id}'");
		mysqli_query($conNOTA, "DELETE FROM T117 WHERE T117_D024_Id = '{$D024_Id}'");
		mysqli_query($conNOTA, "DELETE FROM T139 WHERE T139_D024_Id = '{$D024_Id}'");
	}

	if(!empty($C025_Banco_Dados)){
		mysqli_query($conNOTA, "USE {$C025_Banco_Dados}");
		mysqli_query($conNOTA, "DROP USER '{$C025_Banco_Dados}'@'localhost';");
		mysqli_query($conNOTA, "FLUSH PRIVILEGES;");
		mysqli_query($conNOTA, "DROP DATABASE {$C025_Banco_Dados};");
	}
}

function criarDatabaseLocal($C025_Banco_Dados, $rowD024, $login, $senha, $email, $dominio, $site)
{
	global $conERP, $conNOTA, $confUsuarioERP;
	$logCriarDatabase = "Iniciando a criação do banco \n";
	/************************************************************************
	*				CRIANDO DATABASE DA EMPRESA
	************************************************************************/
	$sqlD018 = mysqli_query($conNOTA, "SELECT D018_Codigo_IBGE FROM D018 WHERE D018_Id = '{$rowD024['D024_D018_Id']}'");
	$rowD018 = mysqli_fetch_array($sqlD018);
	$sqlD020 = mysqli_query($conNOTA, "SELECT D020_Codigo_IBGE FROM D020 WHERE D020_Id = '{$rowD024['D024_D020_Id']}'");
	$rowD020 = mysqli_fetch_array($sqlD020);

	$C025_Banco_Dados_Senha = md5($C025_Banco_Dados);
	
	$D024_Nome_Empresa 		 = $rowD024['D024_Nome_Empresa'];
	$D024_Cnpj 				 = $rowD024['D024_Cnpj'];
	$D024_Inscricao_Estadual = $rowD024['D024_Inscricao_Estadual'];
	$D024_Id 				 = $rowD024['D024_Id'];
	$D018_Codigo_IBGE   	 = $rowD018['D018_Codigo_IBGE'];
	$D020_Codigo_IBGE   	 = $rowD020['D020_Codigo_IBGE'];
	$D024_Bairro 			 = $rowD024['D024_Bairro'];
	$D024_Logradouro 		 = $rowD024['D024_Endereco'];
	$D024_Numero 	 		 = $rowD024['D024_Numero'];
	$D024_Complemento 	 	 = $rowD024['D024_Complemento'];
	$D024_Cep 	 		 	 = $rowD024['D024_Cep'];
	$D024_DDD_Telefone_1 	 = $rowD024['D024_DDD_Telefone_1'];
	$D024_Telefone_1 	 	 = $rowD024['D024_Telefone_1'];

	$arqDump = '/home/criacaoConta/dump.sql';
	$arqTmp = '/tmp/' . md5(mt_rand() . time()) . '.sql';
	shell_exec("ionice -c3 cp {$arqDump} {$arqTmp}");
	$dump = file_get_contents($arqTmp);

	$dump .= "UPDATE C004 SET ";
	$dump .= "C004_Flag_Ativo = 'S', ";
	$dump .= "C004_Nome_Empresa = '{$D024_Nome_Empresa}', ";
	$dump .= "C004_Nome_Abreviado = '{$D024_Nome_Empresa}', ";
	$dump .= "C004_Cnpj = '{$D024_Cnpj}', ";
	$dump .= "C004_Uf = '{$D018_Codigo_IBGE}', ";
	$dump .= "C004_Cidade = '{$D020_Codigo_IBGE}', ";
	$dump .= "C004_Logradouro = '{$D024_Logradouro}', ";
	$dump .= "C004_Bairro = '{$D024_Bairro}', ";
	$dump .= "C004_Numero = '{$D024_Numero}', ";
	$dump .= "C004_Complemento = '{$D024_Complemento}', ";
	$dump .= "C004_Cep = '{$D024_Cep}', ";
	$dump .= "C004_DDD_Telefone = '{$D024_DDD_Telefone}', ";
	$dump .= "C004_Telefone = '{$D024_Telefone}', ";
	$dump .= "C004_Caminho_Sistema = '{$C025_Banco_Dados}', ";
	$dump .= "C004_Banco_Dados = '{$C025_Banco_Dados}', ";
	$dump .= "C004_Banco_Configuracao = '{$C025_Banco_Dados}', ";
	$dump .= "C004_Inscricao_Estadual = '{$D024_Inscricao_Estadual}', ";
	$dump .= "C004_Localizacao_Certificados = '/var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}/nfe/certs/', ";
	$dump .= "C004_D024_Id_Hardness = '{$D024_Id}'; ";

	//Insert C020
	$dump .= "INSERT INTO C020 (C020_C004_Id, C020_Versao_NFe) VALUES ('1', '3.10');";

	//Insert do usuário
	$senha_ = mysqli_real_escape_string($conNOTA, base64_encode($senha));
	$login_ = strtoupper($login);
	$email = strtolower($email);
	$dump .= "INSERT INTO C007 (C007_Login, C007_Senha, C007_Nome, C007_Primeiro_Nome, C007_Email, C007_Flag_Vendedor) VALUES ('{$login}', '{$senha_}', '{$login_}', '{$login_}', '{$email}', 'D');";

	//$dump .= "DROP USER '{$C025_Banco_Dados}'@'localhost';";
	//$dump .= "FLUSH PRIVILEGES;";
	$dump .= "CREATE USER '{$C025_Banco_Dados}'@'%' IDENTIFIED BY '{$C025_Banco_Dados_Senha}';";
	$dump .= "FLUSH PRIVILEGES;";
	$dump .= "GRANT ALL PRIVILEGES ON `{$C025_Banco_Dados}`.* TO '{$C025_Banco_Dados}'@'%' WITH GRANT OPTION;";
	$dump .= "GRANT ALL PRIVILEGES ON `global`.* TO '{$C025_Banco_Dados}'@'%' WITH GRANT OPTION;";
	$dump .= "FLUSH PRIVILEGES;";

	$dump = str_replace('`e000`@`%`', "`{$C025_Banco_Dados}`@`localhost`", $dump);
	$dump = str_replace('e000', "{$C025_Banco_Dados}", $dump);
	file_put_contents($arqTmp, $dump);

	shell_exec("ionice -c3 mysql --user='{$confUsuarioERP['dbUser']}' --password='{$confUsuarioERP['dbPass']}' < {$arqTmp} > /var/www/sites/logCriarConta/logDump.log");
	shell_exec("ionice -c3 rm -f {$arqTmp}");

	$versao = "trunk";

	/*
    $logErros = '';

	mysql_close($conERP);

	unset($conERP);

	$conERP=mysql_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
	
	if (!$conERP){
		$logErros .= "erro conexao: mysql_connect({$confUsuarioERP['dbHost']}, {$confUsuarioERP['dbUser']}, {$confUsuarioERP['dbPass']}) \n\n";
	} else {
		$logErros .= "Nova conexao: mysql_connect({$confUsuarioERP['dbHost']}, {$confUsuarioERP['dbUser']}, {$confUsuarioERP['dbPass']}) \n\n";
	}
	
	// Cria usuario no banco
	mysql_query("DROP USER '{$C025_Banco_Dados}'@'localhost';", $conERP);
	$logErros .= mysql_error()."\n\n";
	mysql_query("FLUSH PRIVILEGES;", $conERP);
	$logErros .= mysql_error()."\n\n";
	mysql_query("CREATE USER '{$C025_Banco_Dados}'@'localhost' IDENTIFIED BY '{$C025_Banco_Dados_Senha}';", $conERP);
	$logErros .= mysql_error()."\n\n";
	mysql_query("FLUSH PRIVILEGES;", $conERP);
	$logErros .= mysql_error()."\n\n";
	mysql_query("GRANT ALL PRIVILEGES ON `{$C025_Banco_Dados}`.* TO '{$C025_Banco_Dados}'@'localhost' WITH GRANT OPTION;", $conERP);
	$logErros .= mysql_error()."\n\n";
	mysql_query("GRANT ALL PRIVILEGES ON `global`.* TO '{$C025_Banco_Dados}'@'localhost' WITH GRANT OPTION;", $conERP);
	$logErros .= mysql_error()."\n\n";
	mysql_query("FLUSH PRIVILEGES;", $conERP);
	$logErros .= mysql_error()."\n\n";

	mysql_query("USE {$C025_Banco_Dados}", $conERP);
	$logErros .= mysql_error()."\n\n";

	$sql = "UPDATE C004 SET ";
	$sql .= "C004_Nome_Empresa = '{$D024_Nome_Empresa}', ";
	$sql .= "C004_Nome_Abreviado = '{$D024_Nome_Empresa}', ";
	$sql .= "C004_Cnpj = '{$D024_Cnpj}', ";
	$sql .= "C004_Uf = '{$D018_Codigo_IBGE}', ";
	$sql .= "C004_Cidade = '{$D020_Codigo_IBGE}', ";
	$sql .= "C004_Logradouro = '{$D024_Logradouro}', ";
	$sql .= "C004_Bairro = '{$D024_Bairro}', ";
	$sql .= "C004_Numero = '{$D024_Numero}', ";
	$sql .= "C004_Complemento = '{$D024_Complemento}', ";
	$sql .= "C004_Cep = '{$D024_Cep}', ";
	$sql .= "C004_DDD_Telefone = '{$D024_DDD_Telefone}', ";
	$sql .= "C004_Telefone = '{$D024_Telefone}', ";
	$sql .= "C004_Caminho_Sistema = '{$C025_Banco_Dados}', ";
	$sql .= "C004_Banco_Dados = '{$C025_Banco_Dados}', ";
	$sql .= "C004_Banco_Configuracao = '{$C025_Banco_Dados}', ";
	$sql .= "C004_Inscricao_Estadual = '{$D024_Inscricao_Estadual}', ";
	$sql .= "C004_Localizacao_Certificados = '/var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}/nfe/certs/', ";
	$sql .= "C004_D024_Id_Hardness = '{$D024_Id}' ";
	mysql_query($sql, $conERP);
	$logErros .= mysql_error()."\n\n";

	//Insert C020
	mysql_query("INSERT INTO C020 (C020_C004_Id, C020_Versao_NFe) VALUES ('1', '3.10')");
	$logErros .= mysql_error()."\n\n";

	//Insert do usuário
	$senha_ = mysql_real_escape_string(base64_encode($senha));
	$login_ = strtoupper($login);
	$email = strtolower($email);
	mysql_query("INSERT INTO C007 (C007_Login, C007_Senha, C007_Nome, C007_Primeiro_Nome, C007_Email, C007_Flag_Vendedor) VALUES ('{$login}', '{$senha_}', '{$login_}', '{$login_}', '{$email}', 'D')", $conERP);
	$logErros .= mysql_error()."\n\n";

	$logCriarDatabase .= $logErros;

	file_put_contents("/var/www/sites/logCriarConta/erros.log",$logCriarDatabase);

	if (!empty($logErros)){
		return false;
	}*/

	shell_exec("bash /var/www/versoes/v3/trunk/hardness3/site/scriptDominio.sh {$dominio} {$site} {$C025_Banco_Dados} {$C025_Banco_Dados_Senha} 2 > /var/www/sites/logCriarConta/logScriptDominio.log");

	// Ao final do processo, executa o atualização base
	shell_exec("curl -siL --max-redirs 1 'http://{$dominio}.{$site}/sistema/scripts/atualizacaoBase?ajax=true&divId=123&divIdRoot=321' > /dev/null 2>&1 &");
	return true;
}

/**
* Formata uma data (e hora se tiver) mysql para o formato brasileiro (dd/mm/yyyy)
* 
* @param mixed $valor
* @return string
*/
function gCorrigeData($data, $removerTime = false) {
	// para lidar com o callback do grid
	$valor = $data;
	if (is_array($valor)) {
		if (empty($valor['campoAtual'])) { return ''; } 
		$valor = $valor['campoAtual'];
	}
	$valor = explode(' ', $valor);
	$hora = (isset($valor[1])) ? ' ' . $valor[1] : '';
	$valor = explode('-', $valor[0]);
	if ($valor[0] == '0000') { return ''; }
	if (isset($valor[0]) && isset($valor[1]) && isset($valor[2])) {
		$data = mktime(0, 0, 0, $valor[1], $valor[2], $valor[0]);
		if ($removerTime) {
			return date("d/m/Y", $data);
		} else {
			return date("d/m/Y", $data) . $hora;
		}
	} else {
		return $data;
	}
}

/**
* Formata um número do formato (xxxx.xx) para o formato (x.xxx,xx)
* 
* @param mixed $valor
* @return string
*/
function gCorrigeNumero($valor, $decimal = 2) {
	// para lidar com o callback do grid
	if (is_array($valor)) {
		if (empty($valor['campoAtual'])) { return ''; } 
		$valor = $valor['campoAtual'];
	}
	if (!empty($valor) && is_numeric($valor)) {
		return number_format($valor, $decimal, ',', '.');
	} else {
		return str_pad('0,', $decimal+2, "0", STR_PAD_RIGHT);
	}
}
