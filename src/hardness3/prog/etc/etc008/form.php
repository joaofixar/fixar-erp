<?php
namespace hardness;
set_time_limit(0);

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 04/01/2012  
	*/
	case 'etc008FormBancoDadosClientes':
		global $g;
		
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'C025';
		$form_campo_pk = 'C025_Banco_Dados';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc008FormBancoDadosClientesBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc008/form/etc008FormBancoDadosClientes/';
		// Form Editor
		$form_editor = array(
			'geral' => '{"C025_Banco_Dados":{"top":7,"left":10,"width":70,"hidden":0},"C025_D024_Id":{"top":30,"left":58,"width":235,"hidden":0},"C025_C026_Id":{"top":53,"left":46,"width":255,"hidden":0},"C025_Dominio":{"top":76,"left":52,"width":120,"hidden":0},"comentario":{"top":76,"left":232,"width":null,"hidden":0},"tipo":{"top":99,"left":70,"width":64,"hidden":0},"versao":{"top":122,"left":33,"width":60,"hidden":0}}'
		);

		// Próximo número banco de dados
		$sql = "SELECT LOWER(C025_Banco_Dados) as C025_Banco_Dados FROM C025 ORDER BY LOWER(C025_Banco_Dados) DESC LIMIT 1";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$proxBancoDados = str_replace('e', '', $row['C025_Banco_Dados']);
		$proxBancoDados += 1;
		$proxBancoDados = str_pad($proxBancoDados, 3, "0", STR_PAD_LEFT);
		$proxBancoDados = 'e' . $proxBancoDados;
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D131_D116_Id' => array('name' => 'D131_D116_Id', 'label' => 'Veículo', 'type' => 'select', 
						// 'select' =>  gGeraSelect('D116','D116_Id','D116_Modelo','',true),
					// ),
					'C025_Banco_Dados' => array('name' => 'C025_Banco_Dados', 'label' => 'Banco de Dados', 'type' => 'text', 'length' => array(10,50), 'uppercase' => false, 'value' => $proxBancoDados),
					'C025_D024_Id' => array('name' => 'C025_D024_Id', 'label' => 'Cliente', 'extra'=>'style="width:235px;"', 'type' => 'select', 
						'select' =>  gGeraSelect('D024', 'D024_Id','D024_Nome_Empresa', "ORDER BY D024_Nome_Empresa",true),
					),
					'C025_C026_Id' => array('name' => 'C025_C026_Id', 'label' => 'Contador', 'type' => 'select', 
						'select' =>  gGeraSelect('C026', 'C026_Id','C026_Razao_Social', 'ORDER BY C026_Razao_Social', true),
					),
					'C025_Dominio' => array('name' => 'C025_Dominio', 'label' => 'Domínio', 'type' => 'text', 'length' => array(20,100), 'uppercase' => false),
					'comentario' => array('name' => 'comentario', 'label' => '', 'type' => 'comentario', 'reject' => true, 'value' => '.hardness.com.br'),
					'tipo' => array('name' => 'tipo', 'reject' => 'true', 'label' => 'Tipo', 'type' => 'select', 
						'select' => array(
							array('title' => 'NOTA', 'value' => 'NOTA'),
							array('title' => 'ERP', 'value' => 'ERP'),
						),
						'extra' => 'onchange="if ($(this).val() == \'NOTA\') { $(\'#versao\').attr(\'disabled\', \'disabled\'); } else { $(\'#versao\').removeAttr(\'disabled\'); }"'
					),
					'versao' => array('name' => 'versao', 'reject' => 'true', 'label' => 'Versão ERP', 'extra' => 'disabled = "disabled"', 'type' => 'select', 
						'select' => array(
							array('title' => '3.0.0', 'value' => '3.0.0'),
							array('title' => '3.0.1', 'value' => '3.0.1'),
							array('title' => '3.0.2', 'value' => '3.0.2'),
							array('title' => '3.0.3', 'value' => '3.0.3', 'selected' => true),
							array('title' => '3.0.4', 'value' => '3.0.4'),
						)
					),
				)
			)
		);
        $form = new Formulario('form-etc008FormPrincipal');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo gGeraAlertMsg('Operação OK', $sql);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					$retorno = criarNovaEmpresa($form);
					if ($retorno !== true) {
						echo gGeraAlertMsg('Erro', $retorno);
					} else {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert = gGeraAlertMsg('OK', 'Nova empresa criada com sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="etc008FormBancoDadosClientesBtn">Criar!</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}

function criarNovaEmpresa($form) {
	global $g, $confUsuario,$sys;
	set_time_limit(0);
	
	$C025_Banco_Dados = $form->campoValorEnviado('C025_Banco_Dados');
	$C025_D024_Id = $form->campoValorEnviado('C025_D024_Id');
	$C025_C026_Id = $form->campoValorEnviado('C025_C026_Id');
	$C025_Dominio = $form->campoValorEnviado('C025_Dominio');
	$tipo = $form->campoValorEnviado('tipo');
	$versao = $form->campoValorEnviado('versao');
	
	/*
	* Verificações
	*/
	// Banco já existe?
	$C025_Banco_Dados = trim($C025_Banco_Dados);
	$senhaBanco = base64_encode($C025_Banco_Dados);
	if (empty($C025_Banco_Dados)) {
		return 'Banco de dados obrigatório.';
	}
	$sql = "SELECT LOWER(C025_Banco_Dados) as C025_Banco_Dados FROM C025 WHERE LOWER(C025_Banco_Dados) = LOWER('{$C025_Banco_Dados}')";
	$res = mysql_query($sql);
	if (mysql_num_rows($res) > 0) {
		return 'Banco de dados já existe.';
	}
	// Domínimo já existe?
	$C025_Dominio = trim($C025_Dominio);
	if (empty($C025_Dominio)) {
		return 'Domínio obrigatório.';
	}
	$sql = "SELECT LOWER(C025_Dominio) as C025_Dominio FROM C025 WHERE LOWER(C025_Dominio) = LOWER('{$C025_Dominio}')";
	$res = mysql_query($sql);
	if (mysql_num_rows($res) > 0) {
		return 'Domínio já existe.';
	}
	// Banco já existe na C024?
	$sql = "SELECT LOWER(C024_Banco_Dados) as C024_Banco_Dados FROM C024 WHERE LOWER(C024_Banco_Dados) = LOWER('{$C025_Banco_Dados}')";
	$res = mysql_query($sql);
	if (mysql_num_rows($res) > 0) {
		return 'Banco de dados já existe na C024 e não na C025.';
	}
	// D024
	$sql = "SELECT * FROM D024 WHERE D024_Id = '$C025_D024_Id'";
	$res = mysql_query($sql);
	if (mysql_num_rows($res) == 0) {
		return 'Selecione um cliente.';
	}
	$D024 = mysql_fetch_assoc($res);
	// C026
	$sql = "SELECT * FROM C026 WHERE C026_Id = '$C025_C026_Id'";
	$res = mysql_query($sql);
	if (mysql_num_rows($res) == 0) {
		return 'Selecione um contador.';
	}
	$C026 = mysql_fetch_assoc($res);

	// C025
	$login = trim(str_replace('.','',str_replace('-','',str_replace('/','',$D024['D024_Cnpj']))));
	if (empty($login)) {
		return 'Cliente sem CNPJ cadastrado.';
	}
	$senha = substr($login, 0, 8);
	$login = mysql_real_escape_string($login);
	$senha_ = mysql_real_escape_string($senha);
	$D024['D024_E_Mail'] = mysql_real_escape_string($D024['D024_E_Mail']);
	$sql = <<<EOT
INSERT INTO C025 
(C025_D024_Id, C025_Banco_Dados, C025_Login, C025_Senha, C025_Email, C025_C026_Id, C025_Dominio, C025_Flag_Transferido)
VALUES
('{$C025_D024_Id}', '{$C025_Banco_Dados}', '{$login}', '{$senha_}', '{$D024['D024_E_Mail']}', '{$C025_C026_Id}', '{$C025_Dominio}', 1)
EOT;
	//echo $sql . "<br/>\n";
	mysql_query($sql);
	$lastIdC025 = mysql_insert_id();
	//$lastIdC025 = mt_rand();

	// C024
	$sql = <<<EOT
INSERT INTO C024 
(C024_Banco_Dados, C024_D024_Id, C024_C026_Id)
VALUES
('{$C025_Banco_Dados}', '{$C025_D024_Id}', '{$C025_C026_Id}')
EOT;
	//echo $sql . "<br/>\n";
	mysql_query($sql);

	// DUMP
	$cmd = 'ionice -c3 rm -f /tmp/tmpNovo.sql';
	shell_exec($cmd);
	if ($tipo == 'NOTA') {
		$tmpSql = file_get_contents('prog/etc/etc008/NOTA.sql');
	} else {
		$tmpSql = file_get_contents('prog/etc/etc008/ERP.sql');
	}
	$tmpSql = str_replace('userCreateDefiner', $C025_Banco_Dados, $tmpSql);
	$tmpSql = "CREATE DATABASE IF NOT EXISTS {$C025_Banco_Dados};\nUSE {$C025_Banco_Dados};\n" . $tmpSql;
	file_put_contents('/tmp/tmpNovo.sql', $tmpSql);
	$cmd = "ionice -c3 mysql --user='root' --password='\$\$hard@@0982' < /tmp/tmpNovo.sql";
	//echo $cmd . "<br/>\n";
	shell_exec($cmd);


	// Conexão MySQL local
	// mysql_close();
	$con=@mysqli_connect('127.0.0.1', 'root', '$$hard@@0982',$C025_Banco_Dados);
	$Error=mysqli_connect_error($con);
	if (!empty($Error)) {
		echo $Error;
		die();
	}
	// $dbs=mysql_select_db($C025_Banco_Dados, $con) or die (mysql_error());
	mysql_set_charset('utf8',$con);
	mysqli_query('SET NAMES utf8;',$con);

	// C004
	$D024['D024_D018_Id'] = mysql_real_escape_string($D024['D024_D018_Id'],$con);
	$D024['D024_D020_Id'] = mysql_real_escape_string($D024['D024_D020_Id'],$con);
	$D024['D024_Cnpj'] = mysql_real_escape_string($D024['D024_Cnpj'],$con);
	$D024['D024_Inscricao_Estadual'] = mysql_real_escape_string($D024['D024_Inscricao_Estadual'],$con);
	$D024['D024_Nome_Empresa'] = mysql_real_escape_string($D024['D024_Nome_Empresa'],$con);
	$D024['D024_Endereco'] = mysql_real_escape_string($D024['D024_Endereco'],$con);
	$D024['D024_Numero'] = mysql_real_escape_string($D024['D024_Numero'],$con);
	$D024['D024_Bairro'] = mysql_real_escape_string($D024['D024_Bairro'],$con);
	$D024['D024_Cep'] = mysql_real_escape_string($D024['D024_Cep'],$con);
	$D024['D024_Contador'] = mysql_real_escape_string($C026['C026_Razao_Social'],$con);
	$D024['D024_DDD_Telefone'] = '';
	$D024['D024_Telefone'] = mysql_real_escape_string($D024['D024_Telefone_1'],$con);
	$sql = <<<EOT
INSERT INTO `{$C025_Banco_Dados}`.C004 
(C004_Id, C004_D018_Id, C004_D020_Id, C004_Cnpj, C004_Inscricao_Estadual, C004_Nome_Empresa, C004_Logradouro, C004_Numero, C004_Bairro, C004_Cep, C004_Banco_Configuracao, C004_Banco_Dados, C004_Caminho_Sistema, C004_Responsavel_Contato, C004_Telefone, C004_Localizacao_Certificados)
VALUES
(1, '{$D024['D024_D018_Id']}', '{$D024['D024_D020_Id']}', '{$D024['D024_Cnpj']}', '{$D024['D024_Inscricao_Estadual']}', '{$D024['D024_Nome_Empresa']}', '{$D024['D024_Endereco']}', '{$D024['D024_Numero']}', '{$D024['D024_Bairro']}', '{$D024['D024_Cep']}', '{$C025_Banco_Dados}', '{$C025_Banco_Dados}', 'E003', '{$D024['D024_Contador']}', '{$D024['D024_DDD_Telefone']}{$D024['D024_Telefone']}', '/var/www/dados/v2/dados_usuarios/{$C025_Banco_Dados}/nfe/certs/')
EOT;
	//echo $sql . "<br/>\n";
	mysqli_query($sql,$con);
	$Error=mysql_error($con);
	if (!empty($Error)) {
		echo $Error;
		die();
	}
		
	// C020
	$sql = <<<EOT
INSERT INTO `{$C025_Banco_Dados}`.C020 
(C020_C004_Id, C020_Pre_Pedido_Horario_Entrega_Obrigatorio, C020_Pre_Pedido_Vendedor_Obrigatorio, C020_Pre_Pedido_Prazos_Obrigatorio, C020_Cliente_Telefone_Obrigatorio, C020_Pedido_Gerar_Pedido_Sem_Estoque, C020_NFe_Tipo_DANFE, C020_NFe_Obrigatorio_Cadastro_Produto)
VALUES
(1, 'N', 'N', 'N', 'N', 'S', 'PDF', 'N')
EOT;
	//echo $sql . "<br/>\n";
	mysqli_query($sql,$con);

	// C007
	$senha_ = mysql_real_escape_string(base64_encode($senha),$con);
	$sql = <<<EOT
INSERT INTO `{$C025_Banco_Dados}`.C007 
(C007_Id, C007_Login, C007_Senha, C007_Email, C007_Especial, C007_Ativo, C007_Usuario_Ancora, C007_Flag_Mostrar_Boleto)
VALUES
('$lastIdC025', '$login', '{$senha_}', '{$D024['D024_E_Mail']}', 'N','S', 'S', 'S')
EOT;
	//echo $sql . "<br/>\n";
	mysqli_query($sql,$con);

	// C021
	$sql = <<<EOT
INSERT INTO `{$C025_Banco_Dados}`.C021 
(C021_Id, C021_Nome_Perfil)
VALUES
(1, 'ADMINISTRACAO')
EOT;
	//echo $sql . "<br/>\n";
	mysqli_query($sql,$con);

	// C019
	$sql = <<<EOT
INSERT INTO `{$C025_Banco_Dados}`.C019 
(C019_C021_Id, C019_C004_Id, C019_C007_Id)
VALUES
(1, 1, '{$lastIdC025}')
EOT;
	//echo $sql . "<br/>\n";
	mysqli_query($sql,$con);

	// C007
	$senhaRoot = base64_encode('root');
	$sql = <<<EOT
INSERT INTO `{$C025_Banco_Dados}`.C007 
(C007_Id, C007_Login, C007_Senha, C007_Email, C007_Especial, C007_Ativo, C007_Usuario_Ancora, C007_Usuario_Suporte, C007_Usuario_Administracao)
VALUES
('63', 'root', '{$senhaRoot}', '', 'S','S', 'S', 'S', 'S' )
EOT;
	//echo $sql . "<br/>\n";
	mysqli_query($sql,$con);

	// C019
	$sql = <<<EOT
INSERT INTO `{$C025_Banco_Dados}`.C019 
(C019_C021_Id, C019_C004_Id, C019_C007_Id)
VALUES
(1, 1, '63')
EOT;
	//echo $sql . "<br/>\n";
	mysqli_query($sql,$con);

	// Permissões dos perfis
	$mControleAcessos = array();
	$mControleAcessos[]="99,,1,,LIBERAR IPV MINIMO";
	$mControleAcessos[]="99,,2,,LIBERAR CREDITO";
	$mControleAcessos[]="99,,3,,RETORNAR PEDIDO DO ALMOXARIFADO";
	$mControleAcessos[]="99,,4,,CANCELAR PEDIDO VENDA";
	$mControleAcessos[]="99,,5,,CANCELAR PEDIDO PENDENTE";
	$mControleAcessos[]="99,,6,,ALTERAR OBSERVACAO COMPRA EM PRODUTOS";
	$mControleAcessos[]="99,,7,,CANCELAR NOTA FISCAL EMITIDA";
	$mControleAcessos[]="99,,8,,INCLUSAO E ALTERAÇÃO DE ITENS (D001)";
	$mControleAcessos[]="99,,9,,ATUALIZAR D.E.";
	$mControleAcessos[]="99,,10,,ALTERAÇÃO DO CAMPO COMPLEMENTO (D001)";
	$mControleAcessos[]="99,,11,,ALTERAÇÂO DO CAMPO PESO (D001)";
	$mControleAcessos[]="99,,12,,MENS.MOTIVO PEDIDO NEGADO";
	$mControleAcessos[]="99,,13,,LIBERAR NOTA DE EXPOSIÇÃO";
	$mControleAcessos[]="99,,14,,CANCELAR NOTA FISCAL RECEBIDA";
	$mControleAcessos[]="99,,15,,LIBERAR TRANSFERENCIA ABAIXO DO IPV";
	$mControleAcessos[]="99,,16,,ATIVAR / INATIVAR CLIENTE/FORNECEDOR";
	$mControleAcessos[]="99,,17,,ATIVAR / INATIVAR TRANSPORTADORA";
	$mControleAcessos[]="99,,18,,MENSAGEM PIN";
	$mControleAcessos[]="99,,19,,ALTERAÇÃO DO CAMPO FOTO (D001)";
	$mControleAcessos[]="99,,20,,CONTROLE DE REPRESENTANTE";
	$mControleAcessos[]="99,,21,,ALTERAR CFOP NOTAS";
	$mControleAcessos[]="99,,22,,EXCLUIR CLIENTE";
	$mControleAcessos[]="99,,32,,PROCESSAR MONTAGEM";
	$mControleAcessos[]="99,,33,,MENSAGEM PEDIDO PARA IMPRIMIR";
	$mControleAcessos[]="99,,34,,ALTERAR PRECOS NO ORCAMENTO/PEDIDOS";
	$mControleAcessos[]="99,,35,,IMPRIMIR COPIA NF OFICIAL";
	$mControleAcessos[]="99,,36,,MUDAR PRE-CADASTRO CLIENTE";
	$mControleAcessos[]="99,,37,,PERMITE IMPRIMIR A COLUNA CUSTOS NO INVENTARIO";
	$mControleAcessos[]="99,,38,,ALTERAR PEDIDO PENDENTE";
	$mControleAcessos[]="99,,90,,EXCLUIR NOTA REJEITADA PELO SEFAZ";
	$mControleAcessos[]="99,,104,,ATIVAR / DESATIVAR SCAN";
	$mControleAcessos[]="2,,39,,CLIENTES/FORNECEDORES";
	$mControleAcessos[]="2,,40,,REPRESENTANTES";
	$mControleAcessos[]="2,,41,,TRANSPORTADORA";
	$mControleAcessos[]="2,,42,,PRODUTOS";
	$mControleAcessos[]="2,,101,,TABELA DE PRECOS";
	$mControleAcessos[]="2,,102,,TABELA FORNECEDOR";
	$mControleAcessos[]="2,,43,,C.F.O.P";
	$mControleAcessos[]="2,,44,,CLASS.FISCAL";
	$mControleAcessos[]="2,,45,,BANCOS/CAIXAS";
	$mControleAcessos[]="2,,46,,PORTADORES";
	$mControleAcessos[]="2,,47,,CENTRO CUSTO";
	$mControleAcessos[]="2,,48,,EMPRESA";
	$mControleAcessos[]="2,,49,,PERFIL";
	$mControleAcessos[]="2,,50,,VENDEDORES";
	$mControleAcessos[]="2,,51,,USUARIOS";
	$mControleAcessos[]="3,,52,,LISTA DE PRECOS";
	$mControleAcessos[]="3,,89,,CARTEIRA CLIENTES";
	$mControleAcessos[]="3,,53,,PRE PEDIDO";
	$mControleAcessos[]="3,,98,,C.R.M.";
	$mControleAcessos[]="3,,54,,ORCAMENTOS";
	$mControleAcessos[]="3,,55,,PEDIDOS";
	$mControleAcessos[]="3,,97,,APROVACAO DE ORCAMENTOS";
	$mControleAcessos[]="3,,56,,NOTAS FISCAIS";
	$mControleAcessos[]="3,,57,,EMITIR NOTAS FISCAIS";
	$mControleAcessos[]="3,,58,,INUTILIZAR NF-e";
	$mControleAcessos[]="3,,59,,COMODATO";
	$mControleAcessos[]="3,,60,,ORDEM DE SERVIÇO";
	$mControleAcessos[]="3,,95,,CURVA ABC VENDEDORES";
	$mControleAcessos[]="4,,61,,CONTAS A RECEBER";
	$mControleAcessos[]="4,,62,,CONTAS A PAGAR";
	$mControleAcessos[]="4,,63,,CAIXA E BANCOS";
	$mControleAcessos[]="4,,64,,BOLETO BANCARIO";
	$mControleAcessos[]="4,,65,,FATURAR ROMANEIO";
	$mControleAcessos[]="4,,66,,PROJECAO ORCAMENTARIA";
	$mControleAcessos[]="4,,96,,ANALISE DE CRÉDITO";
	$mControleAcessos[]="5,,67,,CONTROLE DE ESTOQUE";
	$mControleAcessos[]="5,,103,,CURVA ABC PRODUTOS";
	$mControleAcessos[]="5,,68,,NOTAS FISCAIS RECEBIDAS";
	$mControleAcessos[]="5,,69,,EXPEDICAO MERCADORIA";
	$mControleAcessos[]="5,,70,,DIVERGENCIA ESTOQUE";
	$mControleAcessos[]="5,,71,,MONTAGEM";
	$mControleAcessos[]="6,,72,,FLUXO DE CAIXA";
	$mControleAcessos[]="6,,73,,BALANCETE";
	$mControleAcessos[]="6,,74,,RELATORIO CONTAS A PAGAR";
	$mControleAcessos[]="6,,75,,RELATORIO CONTAS A RECEBER";
	$mControleAcessos[]="6,,76,,MAPA DE VENDA";
	$mControleAcessos[]="6,,77,,INVENTARIO ESTOQUE";
	$mControleAcessos[]="6,,78,,COMISSAO REPRESENTANTES";
	$mControleAcessos[]="6,,79,,FECHAMENTOS";
	$mControleAcessos[]="7,,80,,GERAR SINTEGRA";
	$mControleAcessos[]="7,,81,,XML NFe";
	$mControleAcessos[]="8,,82,,UF(ICMS)";
	$mControleAcessos[]="8,,83,,CIDADES";
	$mControleAcessos[]="8,,99,,IPV";
	$mControleAcessos[]="8,,84,,LAYOUT NOTA FISCAL";
	$mControleAcessos[]="8,,100,,MOTIVOS CANCELAMENTO VENDAS";
	$mControleAcessos[]="8,,85,,MYSQL";
	$mControleAcessos[]="8,,86,,PARAMETRIZAÇOES";
	$mControleAcessos[]="9,,87,,MAILING";
	$mControleAcessos[]="9,,88,,RECLAME JÁ";
	$mControleAcessos[]="5,,91,,COTAÇÃO";
	$mControleAcessos[]="5,,92,,COMPRAS EMERGÊNCIA";
	$mControleAcessos[]="5,,93,,COMPRAS ESTOQUE";
	$mControleAcessos[]="5,,94,,ORDEM COMPRA";
	for ($x=0;$x<count($mControleAcessos);$x++)
	{
	    list($C009_Grupo,$C009_Id,$C009_Descricao_Acesso)=explode(",,",$mControleAcessos[$x]);
	    $cClausula="select * from `{$C025_Banco_Dados}`.C009 where C009_Id='$C009_Id'";
	    $cSql_C009=mysqli_query($cClausula,$con);
	    if (mysqli_num_rows($cSql_C009)==0)
	    {
	       $cClausula  = " insert into `{$C025_Banco_Dados}`.C009 ( ";
	       $cClausula .= " C009_Id,";
	       $cClausula .= " C009_Grupo, ";
	       $cClausula .= " C009_Descricao_Acesso ";
	       $cClausula .= " ) values ( ";
	       $cClausula .= " '$C009_Id',";
	       $cClausula .= " '$C009_Grupo',";
	       $cClausula .= " '$C009_Descricao_Acesso' ";
	       $cClausula .= " ) ";
	       mysqli_query($cClausula,$con);
	       $mAlteracoes[]="$cClausula";
	    }
	    else
	    {
	       $cClausula  = " update `{$C025_Banco_Dados}`.C009 set ";
	       $cClausula .= " C009_Descricao_Acesso='$C009_Descricao_Acesso',C009_Grupo='$C009_Grupo' ";
	       $cClausula .= " where C009_Id='$C009_Id' ";
	       mysqli_query($cClausula,$con);
	       //$mAlteracoes[]="$cClausula";
	    }

		// C010
		$sql = <<<EOT
INSERT INTO `{$C025_Banco_Dados}`.C010 
(C010_C021_Id, C010_C009_Id, C010_Id_Perfil, C010_C007_Id, C010_Acesso_Liberado)
VALUES
('1', '$C009_Id', '1', '$lastIdC025', 'S')
EOT;
		//echo $sql . "<br/>\n";
		mysqli_query($sql,$con);
	}

	// Apache
	if ($tipo == 'NOTA') {
		$caminho = "/var/www/nota";
	} else {
		$caminho = "/var/www/versoes/v3/{$versao}";
	}
	shell_exec("ionice -c3 rm -f /etc/apache2/sites-enabled/{$C025_Dominio}.hardness.com.br");
	$arquivo = <<<EOT
<VirtualHost *:80>
        ServerName {$C025_Dominio}.hardness.com.br
        ServerAdmin marcelo@hardness.com.br
        DocumentRoot {$caminho}
        ErrorLog /var/log/apache2/{$C025_Dominio}.hardness.com.br-error.log
        <Directory />
                Options -Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>
</VirtualHost>
EOT;
	file_put_contents("/etc/apache2/sites-enabled/{$C025_Dominio}.hardness.com.br", $arquivo);

	// Conexão v2 e v3
	shell_exec("ionice -c3 rm -f /var/www/sites/{$C025_Dominio}.hardness.com.br-confUsuario.php");
$arquivo = <<<EOT
<?php
namespace hardness;
\$confUsuario = array(
        // Banco de dados
        'dbDatabase' => '{$C025_Banco_Dados}',
        'dbHost' => '127.0.0.1',
        'dbUser' => '{$C025_Banco_Dados}',
        'dbPass' => '{$senhaBanco}',
        // Caminho
        'pathRaiz' => '{$caminho}/hardness3/',
        // WWW
        'urlRaiz' => 'http://{$C025_Dominio}.hardness.com.br/'
);
?>
EOT;
	file_put_contents("/var/www/sites/{$C025_Dominio}.hardness.com.br-confUsuario.php", $arquivo);
	shell_exec("ionice -c3 rm -f /var/www/sites/{$C025_Dominio}.hardness.com.br-conexao.inc");
	$arquivo = <<<EOT
<?php

########################################################################################
#
#       - facilitar o processo de conexão com o Mysql
#       - Atravez do dominio verifico o tipo de conexao a ser usada
#
########################################################################################

    \$Banco_Dados='{$C025_Banco_Dados}';

    \$Banco_Dados=strtolower(\$Banco_Dados);

    \$cBd_Padrao="\$Banco_Dados";

    if (substr(\$_SERVER["DOCUMENT_ROOT"],strlen(\$_SERVER["DOCUMENT_ROOT"]),1)!='/')
    {
       DEFINE(DIR_WWW,\$_SERVER["DOCUMENT_ROOT"].'/hardness');
    }
    else
    {
       DEFINE(DIR_WWW,\$_SERVER["DOCUMENT_ROOT"].'hardness');
    }
    if (!file_exists(DIR_WWW)) {
           echo "Falha no caminho DIR_WWW ".DIR_WWW;
           exit;
        }

    //\$CAMINHO_IMAGEM = "C:\Arquivos de programas\VertrigoServ\www\imagens_hardness";
    \$cFusoHorario='00:00:00';
    \$Entidade=StrToUpper(\$Entidade);

    \$servidor=\$_SERVER['SERVER_NAME'];

	\$host='127.0.0.1';
    \$db=\$cBd_Padrao;
    \$user='{$C025_Banco_Dados}';
    \$pass='{$senhaBanco}';

    // abre uma conexão no servidor
    \$con=@mysqli_connect(\$host,\$user,\$pass,\$db);
    \$Error=mysqli_connect_error(\$con);
    if (!empty(\$Error)) {
        echo "<table border=0 width=100% height=60%>";
          echo "<tr>";
            echo "<td align=center>";
              echo "<table border=0 width=500 height=100 bgcolor=#EBE9E0 cellpadding=0 cellspacing=1  style='border: 1px solid;border-color:  white #B0B0B0 #B0B0B0 white;'>";
                echo "<tr>";
                  echo "<td align=center>";
                    echo "<img src='../imagens/gif/proibido.gif'>";
                  echo "</td>";
                  echo "<td align=left>";
                    echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 24;font-weight:bold;'>Acesso negado para o usuário:\$servidor - \$host,\$user,\$pass);</font>";
                    echo "<br>";
                    echo "<br>";
                    echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 16;font-weight:bold;'>\$Nome_Usuario</font>";
                  echo "</td>";
                echo "</tr>";
              echo "</table>";
            echo "</td>";
          echo "</tr>";
        echo "</table>";
        exit;
    }

    // \$dbs=mysql_select_db("\$db",\$con) or die (mysql_error());

    // Busca os dados do usuario
    \$resultado=mysqli_query(\$con,"select * from C007 where C007_Id='\$Usuario_Atual'");
    \$mUsuario=mysqli_fetch_array(\$resultado);
?>
EOT;
	file_put_contents("/var/www/sites/{$C025_Dominio}.hardness.com.br-conexao.inc", $arquivo);

	// Permissões
	mysql_query("DROP USER '{$C025_Banco_Dados}'@'localhost';");
	mysql_query("FLUSH PRIVILEGES;");
	mysql_query("CREATE USER '{$C025_Banco_Dados}'@'localhost' IDENTIFIED BY '{$senhaBanco}';");
	mysql_query("FLUSH PRIVILEGES;");
	mysql_query("GRANT ALL PRIVILEGES ON `{$C025_Banco_Dados}`.* TO '{$C025_Banco_Dados}'@'localhost' WITH GRANT OPTION;");
	mysql_query("GRANT ALL PRIVILEGES ON `global`.* TO '{$C025_Banco_Dados}'@'localhost' WITH GRANT OPTION;");
	mysql_query("GRANT ALL PRIVILEGES ON `{$sys['dbDatabase']}`.A001 TO '{$C025_Banco_Dados}'@'localhost' WITH GRANT OPTION;");
	mysql_query("FLUSH PRIVILEGES;");

	// reload não está funcionando, e restart não pode dentro do script
	//shell_exec("sudo -u root /usr/sbin/service apache2 reload");
	file_put_contents('/tmp/restartApache', '1');

	return true;
}