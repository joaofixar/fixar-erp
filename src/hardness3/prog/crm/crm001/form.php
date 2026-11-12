<?php
namespace hardness;
require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();
require_once("bibliotecas/classes/VEN002.php");
$VEN002 = new VEN002();

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'crm001FormSetCliente':
		$content1 = (isset($r_content1)) ? $r_content1 : '';
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "WHERE (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}


		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormSetCliente/';

		$pesquisaGridExtra = <<<EOT
			LEFT JOIN D020 ON D020_ID=D024_D020_ID 
			LEFT JOIN D018 ON D018_ID=D024_D018_ID 
			LEFT JOIN C007 ON C007_ID=D024_C007_Vendedor_Interno
			{$clientePorEmpresa}
			ORDER BY D024_Nome_Empresa
EOT;

		$pesquisaGrid = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa, D024_Nome_Fantasia,C007_Primeiro_Nome,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Cliente',																							// título da janela
			'Código,Razão Social,Fantasia,Vendedor,Cidade,UF,CNPJ,DDD,Telefone',															// descrição dos campos
			'crm001ProcuraCliente',																							// callback js
			$g['divId'],																									// divId
			array($content1),																									// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%', 'colunaAlinhar' => 'center'),
				array('style' => 'width:1%')
			)
		);

		/* A linha abaixo era usada para dar nome ao campo e executar o JS comentado a baixo,
		após uma mudança no framework não é mais possível utilizar $uniqid para o
		nome de campos de um form. Por tanto essa parte foi desabilitada e o campo
		foi trocado para nomes fixos -> 'Cliente' => array('name' => 'Cliente'... */
		//$uniqid = uniqid();

		// novo formulário
		$form = new Formulario('crm001FormSetClienteForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Cliente' => array('name' => 'Cliente', 'label' => 'Cliente', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(60,120), 'reject' => true),
				)
			)
		);
		$limite = 0;
		$form->definirFormularioArray($formArray);

		/* Código atualmente redundante, framework já dá focus no 
		primeiro campo do formulário automaticamente */
// 		echo <<<EOT
// 		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
// 		$('#' + '{$uniqid}').focus();
// 		}</script></div>
// EOT;

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad050FormLocacaoLocal_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'crm001FormEditarAgenda':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;

		// Via Orçamento
		if (isset($r_T003_Id) && empty($r_D024_Id)) {
			$sql = "SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$r_T003_Id}'";
			$resT003 = mysql_query($sql);
			$rowT003 = mysql_fetch_assoc($resT003);
			$D024_Id = $r_D024_Id = $rowT003['T003_D024_Id'];
		}

		if(empty($r_D024_Id) && isset($acaoId)){
			$sqlD085 = mysql_query("SELECT D085_D024_Id FROM D085 WHERE D085_Id = '{$acaoId}'");
			$rowD085 = mysql_fetch_assoc($sqlD085);
			$D024_Id = $r_D024_Id = $rowD085['D085_D024_Id'];
		}

		if($r_T139_Id > 0){
			$sqlT139 = mysql_query("SELECT T139_Titulo FROM T139 WHERE T139_Id = '{$r_T139_Id}'");
			$rowT139 = mysql_fetch_assoc($sqlT139);
			$D085_Mensagem = $rowT139['T139_Titulo'];
		}

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormAgenda_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormEditarAgenda/';

		// novo formulário
		$form = new Formulario('crm001FormAgenda');
		$form_editor = array('geral' => '{"D085A_C004_Id":{"top":30,"left":53.078125,"width":511,"hidden":0},"D085_D024_Id":{"top":7,"left":57.78125,"width":489,"hidden":0},"crm001CadastroCliente":{"top":5,"left":608,"width":null,"hidden":0},"D085_C007_Id":{"top":53,"left":53.078125,"width":239,"hidden":0},"D085_D155_Id":{"top":53,"left":404.921875,"width":175,"hidden":0},"D085_Data_Retorno":{"top":30,"left":777,"width":87,"hidden":1},"D085_D013_Id":{"top":76,"left":51.765625,"width":239,"hidden":0},"crm001CadastroContato":{"top":74,"left":352,"width":null,"hidden":0},"D085_Hora_Retorno":{"top":53,"left":784,"width":99,"hidden":1},"D085_Data":{"top":99,"left":67.828125,"width":72,"hidden":0},"D085_Hora":{"top":99,"left":176,"width":48,"hidden":0},"D085_Data_Previsao_Retorno":{"top":99,"left":432.75,"width":72,"hidden":0},"D085_Hora_Previsao_Retorno":{"top":99,"left":560,"width":40,"hidden":0},"D085_Mensagem":{"top":122,"left":28.34375,"height":177,"width":504,"hidden":0},"D085_Retorno":{"top":99,"left":771,"height":140,"width":77,"hidden":1},"D085_D021_Id":{"top":99,"left":995,"width":0,"hidden":1}}');

		if(!empty($r_D024_Id)){
			$whereD024 = "WHERE T003_D024_Id = $r_D024_Id";
		}
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array(
				        'name' => 'D085_D024_Id',
				        'label'=> "Cliente",
				        'type' => 'pesquisa',
				        'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - (%4%5) -  [%2/%3]'), 
				        'length' => array(80,30),
				        'disabled' => (!empty($r_D024_Id) || ($acaoId > 0 && $g['C031']['habilitarEdicaoAgenda'] == 'N')) ? 'disabled' : '',
				        'extra' => "onchange=\"carregarContatosClienteAgenda(this, '{$g['divId']}');\"",
					),
					'D085_D013_Id' => array('name' => 'D085_D013_Id', 'label' => 'Contato', 'type' => 'select', 
						'disabled' => ($acaoId > 0 && $g['C031']['habilitarEdicaoAgenda'] == 'N') ? 'disabled' : '',
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='{$r_D024_Id}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato",true),
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='{$r_D024_Id}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true)
					),
					'D085_D155_Id' => array('name' => 'D085_D155_Id', 'label' => 'Tipo', 'type' => 'select', 
						'disabled' => ($acaoId > 0 && $g['C031']['habilitarEdicaoAgenda'] == 'N') ? 'disabled' : '',
						'selectRefresh' => array('D155','D155_Id','D155_Tipo',"where D155_Flag_Ativo = 'S' ORDER BY D155_Tipo", true),
						'select' => gGeraSelect('D155','D155_Id','D155_Tipo',"where D155_Flag_Ativo = 'S' ORDER BY D155_Tipo", true)
					),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data','label'=>'Data', 'type' => 'data', 'value' => date("d/m/Y"), 'length' => array(10,10), 'readonly' => true),
					'D085_Data_Retorno' => array('name' => 'D085_Data_Retorno','label'=>'Data Retorno', 'type' => 'data', 'length' => array(10,10)),
					'D085_Retorno' => array('name' => 'D085_Retorno','label'=>'Retorno', 'type' => 'textarea', 'length' => array(10,10)),
					'D085_Data_Previsao_Retorno' => array('name' => 'D085_Data_Previsao_Retorno','label'=>'Retornar', 'type' => 'data', 'length' => array(10,10)),
					'D085_Hora' => array('name' => 'D085_Hora', 'label'=>'', 'type' => 'text', 'value' => date('H:i:s'), 'readonly' => true),
					'D085_Hora_Previsao_Retorno' => array('name' => 'D085_Hora_Previsao_Retorno','label'=>'', 'type' => 'tempo'),
					'D085_Hora_Retorno' => array('name' => 'D085_Hora_Retorno','label'=>'', 'type' => 'tempo'),
					'D085_Mensagem' => array('name' => 'D085_Mensagem','label'=>'Mensagem', 'type' => 'textarea','length' => array(3,60), 'value' => @$D085_Mensagem, 'disabled' => ($acaoId > 0 && $g['C031']['habilitarEdicaoAgenda'] == 'N') ? 'disabled' : ''),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'value' => 'S', 'type' => 'hidden'),
					'D085_D021_Id' => array('name' => 'D085_D021_Id','label'=>'Representante', 'type' => 'pesquisa',
						'pesquisa' => array('D021','D021_Id','D021_Nome_Empresa','',), 'length' => array(50,30)
					),
					'D085_C007_Id' => array('name' => 'D085_C007_Id', 'label'=>'Usuário','type' => 'select', 'disabled'=>'disabled','select' => 
						gGeraSelect('C007','C007_Id','C007_Nome',"",true, $g['usuarioAtual']),
					),
					'D085A_C004_Id' => array('name' => 'D085_C004_Id', 'label'=>'Empresa','type' => 'select', 'disabled'=>'disabled','disabled' => ($acaoId > 0 && $g['C031']['habilitarEdicaoAgenda'] == 'N') ? 'disabled' : '','select' => 
						gGeraSelect('C004','C004_Id','C004_Nome_Abreviado',"WHERE C004_Flag_Ativo='S'", true),
					),
					'D085_T002_Id' => array('name' => 'D085_T002_Id', 'type' => 'hidden', 'value' => @$r_T002_Id),
					'D085_T139_Id' => array('name' => 'D085_T139_Id', 'type' => 'hidden', 'value' => @$r_T139_Id),
					'divIdRefresh' => array('name' => 'divIdRefresh', 'type' => 'hidden', 'value' => @$r_divIdRefresh, 'reject' => true),
					'crm001CadastroContato' => array('name' => 'crm001CadastroContato', 'type' => 'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true),
					'crm001CadastroCliente' => array('name' => 'crm001CadastroCliente', 'type' => 'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true),
				),
				'associacoes' => array(
					'D085_T003_Id' => array(
				        'name' => 'D085_T003_Id',
				        'label'=> "Orçamento",
				        'type' => 'pesquisa',
				        'pesquisa' => array('T003','T003_Id',"T003_Id,T003_Data_Emissao,D024_Nome_Empresa,T003_Valor_Total",'LEFT JOIN D024 ON D024_Id=T003_D024_Id '.$whereD024.' ORDER BY T003_Id','%1 - %2 - %3'),
				        'length' => array(80,30),
				        'value' => @$r_T003_Id
					),
				)
			)
		);
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#crm001CadastroContato').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #D085_D024_Id').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos&T139_Id=' + encodeURIComponent('{$r_T139_Id}'), [950,553]);
					}else{
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
					return false;
				});

				$('#crm001CadastroCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Novo Cliente', '/cad/cad001/content/cad001OrcamentoIncluirCliente/', '&T139_Id=' + encodeURIComponent('{$r_T139_Id}'), [570,290]);
				});
			} </script></div>
EOT;
		$form->definirFormularioArray($formArray);
		if(!empty($r_D024_Id)){
			$sqlD024 = mysql_query("SELECT CONCAT(D024_Nome_Empresa, ' - (', D024_Cpf, D024_Cnpj, ') - [', D020_Nome_Cidade,'/',D018_UF,']') as Cliente FROM D024 LEFT JOIN  D020 ON D020_Id=D024_D020_Id LEFT JOIN  D018 ON D018_Id=D024_D018_Id WHERE D024_Id = '{$r_D024_Id}'");
			$D024 = mysql_fetch_array($sqlD024);
			$D024['Cliente'] = mysql_real_escape_string($D024['Cliente']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#D085_D024_Id').val('{$r_D024_Id}');
					$('#D085_D024_Id_Pesquisa_disabled').val('{$D024['Cliente']}');
					$('#D085_D024_Id_Pesquisa').val('{$D024['Cliente']}');
				} </script></div>
EOT;
		}

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {						
							if(strtotime(gCorrigeDataInverte($form->campoValorEnviado('D085_Data_Previsao_Retorno')).' '.$form->campoValorEnviado('D085_Hora_Previsao_Retorno')) > strtotime(date("Y-m-d H:i:s"))){
								mysql_query("UPDATE D085 SET D085_Flag_Lido = 'N' WHERE D085_Id = '{$acaoId}'");
							}
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
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
					// Insert
					global $T259_Id_After_Insert;
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$T259_Id = $T259_Id_After_Insert;
						if($form->campoValorEnviado('D085_T139_Id') > 0){
							require_once("bibliotecas/classes/ETC006.php");
							$ETC006 = new ETC006();
							$retorno = $ETC006->removerEmailSpam($form->campoValorEnviado('D085_T139_Id'), $form->campoValorEnviado('D085_D024_Id'), $form->campoValorEnviado('D085_D013_Id'));
							if($retorno !== false){
								$alert = gGeraAlertMsg('Sucesso', 'Os e-mails foram removidos do spam', false);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											setTimeout(function() {
												{$alert}
											}, 150);
		  								});
										divRefresh('{$form->campoValorEnviado('divIdRefresh')}', true);
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										fecharJanela('{$g['divIdParent']}');
									} </script></div>
EOT;
							} else {
								echo gGeraAlertMsg('Erro', 'Houve um erro ao remover os e-mails da pasta de spam, por favor entre em contato com o suporte técnico.');
							}
						} else {
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Tarefa - {$T259_Id}', '/etc/etc017/content/etc017ContentTarefa/', '&acaoId=' + encodeURIComponent('{$T259_Id}') + '&D024_Id=' + encodeURIComponent('{$form->campoValorEnviado('D085_D024_Id')}'), [900,600]);
										}, 150);
	  								});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
						}
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			} else {
				if (!empty($D024_Id)) {
					$form->campoSetarValor('D085_D024_Id', $D024_Id);
					//$form->campoSetarValor('D085_D024_Id_Pesquisa', '');
				}
			}
		}


		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = '';
		if($r_acaoId > 0){
			$sql = mysql_query("SELECT D085_T003_Id FROM D085 WHERE D085_Id = '{$r_acaoId}'");
			$row = mysql_fetch_assoc($sql);
			if($row['D085_T003_Id'] > 0){
				$botoesEsq .= "<button id=\"crm001OrcamentoAgenda\">Orçamento {$row['D085_T003_Id']}</button>";
			} else {
				$botoesEsq .= '<button id="crm001OrcamentoAgenda">Novo Orçamento</button>';
			}
			$botoesEsq .= '<button id="crm001EnviarEmailAgenda">E-mail</button>';
		} else {
			$botoesEsq = '';
		}
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad001FormAgenda_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#crm001EnviarEmailAgenda').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Responder', '/etc/etc006/content/etc006ContentEmailEnviar/', '&D085_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T139', [1000,500]);
				});
			} </script></div>
EOT;
		if($row['D085_T003_Id'] > 0){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#crm001OrcamentoAgenda').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Agenda', '/etc/etc017/content/etc017ContentOrcamentoAgenda/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&T003_Id=' + encodeURIComponent('{$row['D085_T003_Id']}') + '&tabela=T003', [900,600]);
					});
				} </script></div>
EOT;
		} else {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#crm001OrcamentoAgenda').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D085_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T003', [650,570]);
					});
				} </script></div>
EOT;
		}
	break;

	case 'crm001FormEditarAgendaOrcamento':

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;

		if(!empty($r_flagLido) && $r_flagLido == 'S'){
			mysql_query("UPDATE D085 SET D085_Flag_Lido = 'S' WHERE D085_Id = '{$acaoId}'");
		}
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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormAgendaOrcamento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormEditarAgendaOrcamento/';

		// novo formulário
		$form = new Formulario('crm001FormAgendaOrcamento');
		$form_editor = array('geral' => '{"D085_D024_Id":{"top":7,"left":56.640625,"width":489,"hidden":0},"D085_C007_Id":{"top":30,"left":51.96875,"width":239,"hidden":0},"D085_Data_Retorno":{"top":53,"left":8.453125,"width":87,"hidden":1},"D085_Data":{"top":53,"left":59.3125,"width":72,"hidden":0},"D085_Hora":{"top":53,"left":176,"width":40,"hidden":0},"D085_Retorno":{"top":76,"left":2.75,"height":140,"width":77,"hidden":1},"D085_Data_Previsao_Retorno":{"top":76,"left":47.078125,"width":72,"hidden":0},"D085_Hora_Previsao_Retorno":{"top":76,"left":176,"width":40,"hidden":0},"D085_Hora_Retorno":{"top":99,"left":0,"width":99,"hidden":1},"D085_Mensagem":{"top":99,"left":28.125,"height":200,"width":504,"hidden":0},"D085_D021_Id":{"top":145,"left":2.71875,"width":0,"hidden":1}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array(
				        'name' => 'T003_D024_Id',
				        'label'=> "Cliente",
				        'type' => 'pesquisa',
				        'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - (%4%5) -  [%2/%3]'), 
				        'length' => array(80,30),
				        'disabled' => 'disabled'
					),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data','label'=>'Data', 'type' => 'data', 'value' => date("d/m/Y"), 'length' => array(10,10), 'readonly' => true),
					'D085_Data_Retorno' => array('name' => 'D085_Data_Retorno','label'=>'Data Retorno', 'type' => 'data', 'length' => array(10,10)),
					'D085_Retorno' => array('name' => 'D085_Retorno','label'=>'Retorno', 'type' => 'textarea', 'length' => array(10,10)),
					'D085_Hora' => array('name' => 'D085_Hora','label'=>'', 'type' => 'tempo', 'value' => date('H:i'), 'readonly' => true),
					'D085_Hora_Previsao_Retorno' => array('name' => 'D085_Hora_Previsao_Retorno','label'=>'', 'type' => 'tempo'),
					'D085_Hora_Retorno' => array('name' => 'D085_Hora_Retorno','label'=>'', 'type' => 'tempo'),
					'D085_Data_Previsao_Retorno' => array('name' => 'D085_Data_Previsao_Retorno','label'=>'Retornar', 'type' => 'data', 'length' => array(10,10)),
					'D085_Mensagem' => array('name' => 'D085_Mensagem','label'=>'Mensagem', 'type' => 'textarea','length' => array(3,60)),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'value' => 'S', 'type' => 'hidden'),
					'D085_D021_Id' => array('name' => 'D085_D021_Id','label'=>'Representante', 'type' => 'pesquisa',
						'pesquisa' => array('D021','D021_Id','D021_Nome_Empresa','',), 'length' => array(50,30)
					),
					'D085_C007_Id' => array('name' => 'D085_C007_Id', 'label'=>'Usuário','type' => 'select', 'disabled'=>'disabled','select' => 
						gGeraSelect('C007','C007_Id','C007_Nome',"",true, $g['usuarioAtual']),
					),
					'D085_T003_Id' => array('name' => 'D085_T003_Id','label'=>'', 'type' => 'hidden', 'value' => @$r_T003_Id),
					// 'cad001FormAgenda_btn' => array('name' => 'cad001FormAgenda_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		
		if(!empty($r_D024_Id)){
			$sqlD024 = mysql_query("SELECT CONCAT(D024_Nome_Empresa, ' - (', D024_Cpf, D024_Cnpj, ') - [', D020_Nome_Cidade,'/',D018_UF,']') as Cliente FROM D024 LEFT JOIN  D020 ON D020_Id=D024_D020_Id LEFT JOIN  D018 ON D018_Id=D024_D018_Id WHERE D024_Id = '{$r_D024_Id}'");
			$D024 = mysql_fetch_array($sqlD024);
			$Cliente = str_replace("'", "\'", $D024['Cliente']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#D085_D024_Id').val('{$r_D024_Id}');
					$('#D085_D024_Id_Pesquisa_disabled').val('{$Cliente}');
				} </script></div>
EOT;
		}

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
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
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			} else {
				if (!empty($D024_Id)) {
					$form->campoSetarValor('D085_D024_Id', $D024_Id);
					//$form->campoSetarValor('D085_D024_Id_Pesquisa', '');
				}
			}
		}


		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = '';
		if($r_acaoId > 0){
		$botoesEsq = "<button onclick=\"fecharJanela('".$g['divId']."'); abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/crm/crm001/content/crm001contentEditarAgendaOrcamento/', '&tab=geral&T003_Id={$r_T003_Id}&D024_Id={$r_D024_Id}', [700,450]);\" class=\"btnFechar\">Novo</button>";
		} else {
			$botoesEsq = '';
		}
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad001FormAgendaOrcamento_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
	case 'crm001FormEditarContato':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D013';
		$form_campo_pk = 'D013_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'crm001FormContatosBtnNew';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormEditarContato/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formContatos_crm001');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D013_Nome_Contato' => array('name' => 'D013_Nome_Contato', 'label'=>'Nome', 'type' => 'text','length' => array(50,60)),
					'D013_E_Mail' => array('name' => 'D013_E_Mail', 'label'=>'E-Mail', 'type' => 'text','length' => array(50,90)),
					'D013_Tipo_Contato' => array('name' => 'D013_Tipo_Contato', 'label'=>'Tipo', 'type' => 'text','length' => array(50,90)),
					'D013_Data_Nascimento' => array('name' => 'D013_Data_Nascimento', 'label'=>'Nascimento', 'type' => 'data','length' => array(20,30)),
					'D013_DDD_Telefone_1' => array('name' => 'D013_DDD_Telefone_1', 'label'=>'Telefone', 'type' => 'text','length' => array(2,2)),
					'D013_Telefone_1' => array('name' => 'D013_Telefone', 'label'=>'', 'type' => 'text','length' => array(20,30), 'align'=>'right'),
					'D013_D024_Id' => array('name' => 'D013_D024_Id', 'value' => @$r_D024_Id, 'type' => 'hidden', 'label' => 'D013_D024_Id', 'length' => array(30,30)),
					//'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				),
			) 
		);

		//Botão Limpar / Novo Registro
		/*if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad001FormContatosBtnNew'] = array('name' => 'cad001FormContatosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad001FormContatosBtnCln'] = array('name' => 'cad001FormContatosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		//$formArray['campos']['geral']['cad001FormContatosBtnAdd'] = array('name' => 'cad001FormContatosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		*/
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
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
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
			}
		}


		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);


		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('form.tpl');

		/**
		* Botões
		*/
		// Definição dos botões

		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="crm001FormContatosBtnNew">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;

	case 'crm001FormNotificacao':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;

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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D181';
		$form_campo_pk = 'D181_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'crm001FormNotificacao_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormNotificacao/';

		// novo formulário
		$form = new Formulario('crm001FormNotificacao');
		
		$form_editor = array('geral' => '{"D181_Titulo":{"top":7,"left":30.375,"width":584,"hidden":0},"D181_Subtitulo":{"top":30,"left":14.453125,"width":584,"hidden":0},"D181_Texto":{"top":53,"left":31.59375,"height":154,"width":584,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D181_Titulo' => array('name' => 'D181_Titulo', 'label' => 'Título', 'type' => 'text'),
					'D181_Subtitulo' => array('name' => 'D181_Subtitulo', 'label' => 'Subtítulo', 'type' => 'text'),
					'D181_Texto' => array('name' => 'D181_Texto', 'label' => 'Texto', 'type' => 'textarea', 'length' => array(3,60)),
					'D181_Data_Criacao' => array('name' => 'D181_Data_Criacao', 'label' => '', 'type' => 'hidden', 'value' => date("Y-m-d")),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject' => true),
				),
			),
		);

		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 *            E carregamento dos valores (se não foi enviado e possui acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
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
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
		$g['smarty']->display('formEditor.tpl');

	break;

	case 'crm001FormSelecionarDiasDuplicarPedido':
		/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
			// A qual tabela este form está relacionada, e a chave primária da tabela
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'crm001FormSelecionarDiasDuplicarPedido_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/crm/crm001/form/crm001FormSelecionarDiasDuplicarPedido/';
			// Array de definição do formulário
			// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
			$form = new Formulario('formContatos_crm001');
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Dias_Duplicar' => array('name' => 'Dias_Duplicar', 'label'=>'Dias', 'value'=>'7', 'type' => 'text', 'reject'=>true, 'length' => array(30,60), 'restricao' => 'DiasDuplicar'),
					),
				) 
			);

			//Botão Limpar / Novo Registro
			$form->definirFormularioArray($formArray);

			/**
			* PARTE 3: Processamento do formulário (se foi enviado)
			* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
			*/
			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
						// Insert ou Update?
						if (!empty($acaoId)) {
							// Update
							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
								// Imprime uma mensagem e fecha a janela
								echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											{$alert}
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
						// Insert
						$dias = $form->campoValorEnviado('Dias_Duplicar');
						$retorno = $VEN002->duplicarPedidosSelecionados($dias, $r_extra);
						if($retorno === true){
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										dialogConfirm('Duplicar Pedidos', "Pedido(s) duplicado(s) com sucesso!");
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
						}else{
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								dialogConfirm('Erro!', "Houve algum erro ao duplicar os pedidos {$retorno}");
							} </script></div>
EOT;
						}
					}
				}
			} else {
				// Carrega os valores caso seja um update
				if (!empty($acaoId)) {
					$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
					// mostra erros de formulário uteis ao desenvolvedor
					if ($processado !== true) {
						reset($form->formArray['campos']);
						if ($tab == key($form->formArray['campos'])) {
							echo  $processado;
						}
					}
				}
			}


			/**
			* PARTE 4: Gerar o formulário
			*/
			$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);


			/**
			* PARTE 5: Exibir o formulário
			*/
			$g['smarty']->assign('formularioID', $form->formularioID());
			$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
			echo  $gerado['pagina'];
			if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
			echo  $g['smarty']->fetch('form.tpl');

			/**
			* Botões
			*/
			// Definição dos botões
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="crm001FormSelecionarDiasDuplicarPedido_btn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário
	break;

	case 'crm001FormIncluirAgendaGeral':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;

		$extra = (isset($r_extra)) ? $r_extra : false;

		//die(base64_decode($extra));

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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormAgenda_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormIncluirAgendaGeral/';

		//form editor
		$form_editor = array('geral' =>'{"D085_C007_Id":{"top":7,"left":85,"width":191,"hidden":0},"D085_Data":{"top":30,"left":93,"width":88,"hidden":0},"D085_Data_Retorno":{"top":53,"left":59,"width":88,"hidden":0},"D085_Data_Previsao_Retorno":{"top":76,"left":32,"width":88,"hidden":0},"D085_Mensagem":{"top":122,"left":63,"height":62,"width":342,"hidden":0},"D085_Retorno":{"top":191,"left":84,"height":60,"width":344,"hidden":0}}');

		// novo formulário
		$form = new Formulario('crm001FormAgendaGeral');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data','label'=>'Data', 'type' => 'data', 'value' => date("d/m/Y"), 'length' => array(10,10)),
					'D085_Data_Retorno' => array('name' => 'D085_Data_Retorno','label'=>'Data Retorno', 'type' => 'data', 'length' => array(10,10)),
					'D085_Retorno' => array('name' => 'D085_Retorno','label'=>'Retorno', 'type' => 'textarea', 'length' => array(10,10)),
					'D085_Data_Previsao_Retorno' => array('name' => 'D085_Data_Previsao_Retorno','label'=>'Previsão Retorno', 'type' => 'data', 'length' => array(10,10)),
					'D085_Mensagem' => array('name' => 'D085_Mensagem','label'=>'Mensagem', 'type' => 'textarea','length' => array(3,60)),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'value' => 'S', 'type' => 'hidden'),
					'D085_C007_Id' => array('name' => 'D085_C007_Id', 'label'=>'Usuário','type' => 'select', 'select' => 
						gGeraSelect('C007','C007_Id','C007_Nome',"",true, $g['usuarioAtual']),
					),
					// 'cad001FormAgenda_btn' => array('name' => 'cad001FormAgenda_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
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
					// Insert
					$retorno = $VEN001->CRM001IncluirAgendaGeral($form, $extra);

					if($retorno === true){
						$alert = gGeraAlertMsg('Operação OK', 'Operação efetuada com sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}else{
						$alert = gGeraAlertMsg('Erro!', 'Houve algum erro ao incluir a agenda: '.$erro, false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad001FormAgenda_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'crm001FormInserirAgendaTarefa':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;

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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'crm001FormInserirAgendaTarefa_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormInserirAgendaTarefa/';

		// novo formulário
		$form = new Formulario('crm001FormInserirAgendaTarefa');
		$form_editor = array('geral' => '{"D085_Mensagem":{"top":7,"left":16,"height":85,"width":840,"hidden":0},"crm001FormInserirAgendaTarefa_btn":{"top":100,"left":810,"width":null,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array('name' => 'D085_D024_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $D024_Id),
					'D085_T259_Id' => array('name' => 'D085_T259_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T259_Id),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data', 'label'=>'', 'type' => 'hidden', 'value' => date("Y-m-d")),
					'D085_Hora' => array('name' => 'D085_Hora', 'label'=>'', 'type' => 'hidden', 'value' => date('H:i:s')),
					'D085_Mensagem' => array('name' => 'D085_Mensagem', 'label'=>'', 'type' => 'textarea', 'length' => array(3,60), 'extra' => 'placeholder="INSIRA UMA OBSERVAÇÃO"'),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'type' => 'hidden', 'value' => 'S'),
					'crm001FormInserirAgendaTarefa_btn' => array('name' => 'crm001FormInserirAgendaTarefa_btn', 'type' => 'submit', 'reload' => false,'value' => 'Incluir','align' => 'right', 'reject'=>true),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
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
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$divReload}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'crm001FormCancelamento':
		/**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;

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
        //form editor
		$form_editor = array('geral' =>'{"T005_D047_Id":{"top":7,"left":42,"width":150,"hidden":0},"T005_Observacao_Pedido_Perdido":{"top":30,"left":13,"height":85,"width":216,"hidden":0}}');
        // A qual tabela este form está relacionada, e a chave primária da tabela
        $form_tabela = 'T005';
        $form_campo_pk = 'T005_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'crm001FormCancelamento_Btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/crm/crm001/form/crm001FormCancelamento/';
        $form = new Formulario('crm001FormCancelamento');
        $formArray = array(
            'campos' => array(
                'geral' => array(
                	'T005_D047_Id' => array('name' => 'T005_D047_Id', 'label'=>'Motivo','type' => 'select', 'extra'=>'style="width:150px;"','select' => 
						gGeraSelect('D047', 'D047_Id', 'D047_Descricao_Motivo',''),
					),
                )
            )
        );
        //$formArray['campos']['geral']['T005_D047_Id']     				= 
        $formArray['campos']['geral']['T005_Observacao_Pedido_Perdido'] = array('name' => 'T005_Observacao_Pedido_Perdido', 'label'=>'Observação', 'type' => 'textarea');
        //$formArray['campos']['geral']['divReload']     = array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);

        $form->definirFormularioArray($formArray);
        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
        *            E carregamento dos valores (se não foi enviado e possui acaoId)
        */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
            // Form sem erros
            } else if ($formCodigo == 2) {
				$T005_D047_Id = $form->campoValorEnviado('T005_D047_Id');
				$T005_Observacao_Pedido_Perdido = $form->campoValorEnviado('T005_Observacao_Pedido_Perdido');
				require_once("bibliotecas/classes/VEN002.php");
				$VEN002 = new VEN002();
                $retorno = $VEN002->prepararCancelarPedidos($r_extra, $T005_Observacao_Pedido_Perdido, $T005_D047_Id);
				if($retorno === true){
					echo gGeraAlertMsg('Ok', 'Pedidos cancelados com sucesso');
				} else {
					$msg = <<<EOT
						<div style="overflow: scroll; width: 600px; height: 400px;">
							{$retorno}
						</div>
EOT;
					echo gGeraAlertMsg('Erro', $msg);
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

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
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="crm001FormCancelamento_Btn">Cancelar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'crm001FormIncluirTabelaCliente':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/

		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D161';
		$form_campo_pk = 'D161_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'crm001FormIncluirTabelaCliente_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormIncluirTabelaCliente/';

		// novo formulário
		$form = new Formulario('crm001FormInserirAgendaTarefa');
		$form_editor = array('geral' => '{"D161_D024_Id":{"top":7,"left":9,"width":313,"hidden":0},"crm001FormIncluirTabelaCliente_btn":{"top":35,"left":328,"width":null,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D161_D024_Id' => array('name' => 'D085_D024_Id', 'label'=> "Cliente", 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
					'crm001FormIncluirTabelaCliente_btn' => array('name' => 'crm001FormIncluirTabelaCliente_btn', 'type' => 'submit', 'reload' => false,'value' => 'Incluir','align' => 'right', 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
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
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divIdParent']}', true);
								fecharJanela('{$g['divId']}');
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'crm001FormCSVTabelaPrecos':
		require_once("bibliotecas/classes/CRM001.php");
		$CRM001 = new CRM001();
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D160';
		$form_campo_pk = 'D160';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'crm001FormImportarCSV_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm001/form/crm001FormCSVTabelaPrecos/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('crm001FormCSVTabelaPrecos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVCRM001'] 		= array('name' => 'uploadArquivoCSVCRM001', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['crm001FormImportarCSV_btn'] 		= array('name' => 'crm001FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['D160_Arquivo_Csv'] 				= array('name' => 'D160_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
		echo <<<EOT
						<div class=""></div>
EOT;


		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVCRM001');
				$retorno = $CRM001->importarCSV($arquivo, $acaoId);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
						} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				//$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

	break;

	case 'crm001FormCanalProspecaoCliente':
		$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		$formMultiTab = false;
		$formId = $formMultiTab ? (isset($_REQUEST['formId']) ? $_REQUEST['formId'] : false) : uniqid();
		$form_tabela = 'D201';
		$form_campo_pk = 'D201_Id';
		$enviar_btnId = 'crm001FormCanalProspecaoCliente_btn';
		$enviar_acaoHref = '/crm/crm001/form/crm001FormCanalProspecaoCliente/';

		$form = new Formulario('crm001FormCanalProspecaoCliente');
		$form_editor = array(
			'geral' => '{"D201_Descricao":{"top":7,"left":25,"width":320,"hidden":0}}',
		);
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D201_Descricao' => array('name' => 'D201_Descricao', 'label' => 'Descrição', 'type' => 'text', 'length' => array(60, 300)),
				)
			)
		);
		$form->definirFormularioArray($formArray);

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				if (!empty($acaoId)) {
					$sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form);
					$mensagem = 'Registro alterado com sucesso!';
				} else {
					$sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form);
					$mensagem = 'Registro incluído com sucesso!';
				}
				if ($sql) {
					echo gGeraAlertMsg('Operação OK', $mensagem);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
				}
			}
		} else if (!empty($acaoId)) {
			$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
			if ($processado !== true) {
				reset($form->formArray['campos']);
				if ($tab == key($form->formArray['campos'])) {
					echo $processado;
				}
			}
		}

		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', $form_tabela);
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g['smarty']->assign('formId', $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

}
