<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * Formulários dos Pedidos
 */
//require_once('prog/srv/srv002/form_func-js.php');

//Classes
require_once("bibliotecas/classes/SRV002.php");
$SRV002 = new SRV002();
require_once('prog/srv/srv002/form_func-js.php');
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
		* Form Principal
		* Chamado em: srv002tab02
		*/
	case 'srv002form01':
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/srv/srv002/form/srv002form01/';

		// novo formulário
		$form = new Formulario('form_fin002_form1');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
			'editar_pedido' => array(
			'T005_D024_Id' => array('name' => 'Consulta_T005_D024_Id','label'=>'Cliente', 'type' => 'pesquisa','pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Id',""), 'length' => array(60,20)),
			'T005_Data_Emissao' => array('name' => 'T005_Data_Emissao','label'=>'Emissão', 'type' => 'data', 'length' => array(20,10)),
		)
		)
		);

		$form->definirFormularioArray($formArray);

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(esrvt, ui) {
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
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(esrvt, ui) {
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
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;

		/**
		 * Formulário Produtos
		 */
	case 'srv002form02':
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
		$form_tabela = 'T006';
		$form_campo_pk = 'T006_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('srv002FormProdutosBtnAdd', 'srv002FormProdutosBtnCln', 'srv002FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/srv/srv002/form/srv002form02/';

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where D009_C004_Id = '{$g['empresaAtual']}'
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',		// campos
		$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Un,Estoque',																// descrição dos campos
			'campoProcuraProduto',																				// callback js
		$g['divId']																							// divId
		);

		// novo formulário
		$form = new Formulario('srv002FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'editar_item' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(30,30), 'reject' => true),
					'T006_Codigo_Substituto' => array('name' => 'T006_Codigo_Substituto', 'label' => 'Código Substituição', 'type' => 'text', 'length' => array(15,255), 'align' => 'right', 'style' => 'float:right;'),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(64,255), 'reject' => true, 'disabled' => true),
					'T006_Descricao_Produto' => array('name' => 'T006_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'T006_Quantidade' => array('name' => 'T006_Quantidade', 'label' => 'Quantidade', 'type' => 'text', 'length' => array(15, 30)),
					'T006_D037_Id' => array('name' => 'T006_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
		gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', '', true)
		),
					'T006_Valor_Custo_Unitario' => array('name' => 'T006_Valor_Custo_Unitario', 'label' => 'Custo', 'type' => 'moeda', 'length' => array(15, 30)),
					'T006_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço', 'type' => 'moeda', 'length' => array(15, 30)),
					'T006_IPV' => array('name' => 'T006_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(15, 30), 'align' => 'right', 'style' => 'float:right;'),
		// Campos Hidden
					'T006_T005_Id' => array('name' => 'T006_T005_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T005_Id),
					'T006_C004_Id' => array('name' => 'T006_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
		)
		)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['editar_item']['srv002FormProdutosBtnNew'] = array('name' => 'srv002FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['editar_item']['srv002FormProdutosBtnCln'] = array('name' => 'srv002FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['editar_item']['srv002FormProdutosBtnAdd'] = array('name' => 'srv002FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
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
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
			

		break;

		/**
		 * Formulário Serviços
		 */
	case 'srv002form03':
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
		$form_tabela = 'T147';
		$form_campo_pk = 'T147_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('srv002FormServicosBtnAdd', 'srv002FormServicosBtnCln', 'srv002FormServicosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/srv/srv002/form/srv002form03/';

		// Campo: Procura_Servico
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where D009_C004_Id = '{$g['empresaAtual']}'
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D112',																								// tabela
			'D112_Id',																							// chave primaria
			'D112_Titulo,D112_Descricao',																		// campos
			'',																									// extras sql
			'Pesquisa de Serviço',																				// título da janela
			'Título,Descrição',																					// descrição dos campos
			'campoProcuraServico',																				// callback js
		$g['divId']																							// divId
		);

		// novo formulário
		$form = new Formulario('srv002FormServicosForm');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'editar_servico' => array(
				'Procura_Servico' => array('name' => 'Procura_Servico' , 'label' => 'Serviço', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(50,30), 'reject' => true),
					'T147_Titulo' => array('name' => 'T147_Titulo','label'=>'Título','type' => 'text', 'length' => array(50,30)),
					'T147_Descricao' => array('name' => 'T147_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(2,46)),
					'T147_Quantidade' => array('name' => 'T147_Quantidade','label'=>'Quantidade','type' => 'text', 'length' => array(10,8)),
					'T147_Valor_Unitario' => array('name' => 'T147_Valor_Unitario','label'=>'Valor Unitário','type' => 'moeda', 'length' => array(15,10)),
					'T147_Valor_Total' => array('name' => 'T147_Valor_Total','label'=>'Valor Total','type' => 'moeda', 'length' => array(15,10)),
					'T147_Tempo_Estimado' => array('name' => 'T147_Tempo_Estimado','label'=>'Tempo Estimado','type' => 'text', 'length' => array(10,10)),
		//'T147_Hora_Inicio' => array('name' => 'T147_Hora_Inicio','label'=>'Hora Início','type' => 'text', 'length' => array(10,10)),
		//'T147_Hora_Fim' => array('name' => 'T147_Hora_Fim','label'=>'Hora Fim','type' => 'text', 'length' => array(10,10)),
					'T147_T005_Id' => array('name' => 'T147_T005_Id', 'type' => 'hidden', 'value' => @$r_T005_Id, 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
		)
		)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['editar_servico']['srv002FormServicosBtnNew'] = array('name' => 'srv002FormServicosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['editar_servico']['srv002FormServicosBtnCln'] = array('name' => 'srv002FormServicosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['editar_servico']['srv002FormServicosBtnAdd'] = array('name' => 'srv002FormServicosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		


		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
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
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
			
		break;

		/**
		 * Formulário Materiais
		 */
	case 'srv002form04':
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
		$form_tabela = 'T149';
		$form_campo_pk = 'T149_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form4_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/srv/srv002/form/srv002form04/';

		// novo formulário
		$form = new Formulario('form_fin002_form4');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
			'editar_material' => array(
			'T149_T147_Id' => array('name' => 'T149_T147_Id','label'=>'', 'type' => 'hidden','value' => @$r_T147_Id, 'length' => array(60,20)),
			'T149_D111_Id' => array('name' => 'Consulta_T149_D111_Id','label'=>'Material', 'type' => 'pesquisa', 'pesquisa' => array('D111', 'D111_Id', 'D111_Descricao') ,'length' => array(50,10)),
			'T149_Quantidade' => array('name' => 'T149_Quantidade','label'=>'Quantidade', 'type' => 'text', 'length' => array(10,10)),
			'T149_D037_Id' => array('name' => 'Consulta_T149_D037_Id','label'=>'Unidade', 'type' => 'select', 'select' => gGeraSelect('D037','D037_Id','D037_Descricao_Unidade'),),
			'content_form4_btn' => array('name' => 'content_form4_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
			'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
		
		)
		)
		);

		$form->definirFormularioArray($formArray);

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
						// Atualiza o Grid
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Atualiza o Grid
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="content_form4_btn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
			
	case 'srv002form05':
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
		$form_tabela = 'T148';
		$form_campo_pk = 'T148_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form5_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/srv/srv002/form/srv002form05/';

		// novo formulário
		$form = new Formulario('form_fin002_form5');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
			'geral' => array(
			'T148_T147_Id' => array('name' => 'T148_T147_Id','label'=>'', 'type' => 'hidden','value' => @$r_T147_Id),
			'T148_Titulo' => array('name' => 'T148_Titulo','label'=>'Processo', 'type' => 'text','length' => array(50,30)),
			'T148_Descricao' => array('name' => 'T148_Descricao','label'=>'Descrição', 'type' => 'textarea','length' => array(2,40)),
			'content_form5_btn' => array('name' => 'content_form5_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
			'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
		
		)
		)
		);

		$form->definirFormularioArray($formArray);

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Atualiza o Grid
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="content_form5_btn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
		
		case 'srv002FormControle':
		/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		$retorno = $SRV002->verificaServicoNovo($r_acaoId);
		
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
		$form_tabela = 'T147';
		$form_campo_pk = 'T147_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'srv002FormControleBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/srv/srv002/form/srv002FormControle/';

		$sql = <<<EOT
		select * from T147
		left join T005 on T005_Id=T147_T005_Id
		left join D024 on D024_Id=T005_D024_Id
		left join D116 on D116_Id=T147_D116_Id
		left join T006 on T006_Id=T147_T006_Id
		LEFT JOIN D009 ON D009_Id=T006_D009_Id 
		LEFT JOIN D049 ON D049_Id=D009_D049_Id 
		LEFT JOIN D001 ON D001_Id=D049_D001_Id
		WHERE T147_Id='{$acaoId}'
EOT;
		$result = mysql_query($sql);
		
		$linha = mysql_fetch_assoc($result);
		
		$TempoEstimado = $linha['T147_Tempo_Estimado'];
		$HoraInicio = strtotime($linha['T147_Hora_Inicio']);
		
		$D109_Id = $linha['T147_D109_Id'];
		$D110_Id = $linha['T147_D110_Id'];
		
		$separar = explode(':', $TempoEstimado);
		$horas = $separar[0]*3600;
		$minutos = $separar[1]*60;
		$segundos = $separar[2];
		$seconds = ($horas + $minutos + $segundos);
		$HoraInicioFormatada='';
		$HoraFimFormatada='';
		if ($HoraInicio>0){
			$HoraInicioFormatada=date("d/m/Y H:i", $HoraInicio);
			$HoraFimFormatada = date("d/m/Y H:i", strtotime("+".$seconds." seconds", $HoraInicio));
		}
		
		$status="";
		if($linha['T147_Status']=="P"){
			$status = "Pendente";
		}else if($linha['T147_Status']=="E"){
			$status = "Execução";
		}else if($linha['T147_Status']=="C"){
			$status = "Concluído";
		}
		
		if($linha['T147_Pausado']=="S"){
			$status = "Pausado";
		}

		// novo formulário
		$form = new Formulario('srv002FormControleNew');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T147_Titulo' => array('name' => 'T147_Titulo','label'=>'Título', 'type' => 'text', 'length' => array(50,30), 'readonly'=>true,  'extra'=>'style=background:#E8E8E8;'),
					'Produto' => array('name' => 'Produto','label'=>'Produto', 'type' => 'text', 'value'=>$linha['D001_Descricao_Produto'], 'length' => array(50,30), 'disabled'=>true, 'readonly'=>true, 'reject'=>true,  'extra'=>'style=background:#E8E8E8;'),
					'ModeloVeiculo' => array('name' => 'ModeloVeiculo','label'=>'Veículo', 'type' => 'text', 'value'=>$linha['D116_Placa'].' - '.$linha['D116_Modelo'].' - '.$linha['D116_Cor'], 'length' => array(50,30), 'disabled'=>true, 'readonly'=>true, 'reject'=>true,  'extra'=>'style=background:#E8E8E8;'),
					'NomeCliente' => array('name' => 'NomeCliente','label'=>'Cliente', 'type' => 'text', 'value'=>$linha['D024_Nome_Fantasia'], 'readonly'=>true,'length' => array(50,30), 'disabled'=>true, 'readonly'=>true, 'reject'=>true,  'extra'=>'style=background:#E8E8E8;'),
					'T147_Hora_Inicio' => array('name' => 'T147_Hora_Inicio','label'=>'Hora Início','value'=>$HoraInicioFormatada,'reject'=>true, 'type' => 'text', 'length' => array(50,30), 'disabled'=>true,  'extra'=>'style=background:#E8E8E8;'),
					'previsaoFim' => array('name' => 'previsaoFim','label'=>'Pevisão Fim', 'type' => 'text', 'length' => array(50,30), 'value'=>$HoraFimFormatada, 'disabled'=>true,'readonly'=>true, 'reject'=>true,  'extra'=>'style=background:#E8E8E8;'),
					// 'T147_Hora_Fim' => array('name' => 'T147_Hora_Fim','label'=>'Hora Fim', 'type' => 'text', 'length' => array(50,30), 'disabled'=>true),
					'Status' => array('name' => 'Status','label'=>'Status', 'type' => 'text', 'length' => array(50,30), 'value'=>$status, 'disabled'=>true, 'readonly'=>true, 'reject'=>true,  'extra'=>'style=background:#E8E8E8;'),
					'T147_Descricao' => array('name' => 'T147_Descricao','label'=>'Descrição', 'type' => 'textarea', 'length' => array(4,46), 'readonly'=>true,  'extra'=>'style=background:#E8E8E8;'),					
					'T147_Tempo_Estimado' => array('name' => 'T147_Tempo_Estimado','label'=>'Tempo Estimado', 'type' => 'tempo', 'length' => array(50,30)),
					'T147_D109_Id' => array('name' => 'T147_D109_Id', 'label' => 'Funcionário', 'type' => 'select','extra'=>'style="width:287px;"', 'select' => 
						gGeraSelect('D109','D109_Id','D109_Nome','left join D115 on D115_D109_Id=D109_Id WHERE D115_D110_Id='.$D110_Id.' AND D109_Flag_Ativo=\'S\'', true),
					),		
					'acaoId' => array('name' => 'acaoId','label'=>'acaoId', 'type' => 'hidden', 'length' => array(50,30), 'value'=>$acaoId, 'disabled'=>true, 'readonly'=>true, 'reject'=>true),
					'srv002FormControleBtn' => array('name' => 'srv002FormControleBtn', 'type'=>'submit','reload'=>false,'value'=>'Salvar','align' => 'right', 'style'=>'float:right;', 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
				$D109_Id = $form->campoValorEnviado('T147_D109_Id');
				$retorno = $SRV002->controleServico($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Alterações Salvas com sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');


$D109_Id;
		/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		if($linha['T147_Hora_Inicio'] != "0000-00-00 00:00:00"){
			$botoesEsq .= '<button type="submit" id="srv003ServicoImprimir">Imprimir</button>';
			if($linha['T147_Pausado'] == "S"){
				$botoesEsq .= '<button type="submit" id="srv002ServicoReiniciar">Reiniciar</button>';
			}else{
				$botoesEsq .= '<button type="submit" id="srv002ServicoPausar">Pausar</button>';
			}
			$botoesEsq .= '<button type="submit" id="srv002ServicoCancelar">Cancelar Execucao</button>';
			$botoesEsq .= '<button type="submit" id="srv002ServicoConcluir">Concluir</button>';
		}else{
			$botoesEsq .= '<button type="submit" id="srv002ServicoIniciar">Iniciar</button>';
		}
		$botoesEsq .= '<button type="submit" id="srv002ServicoCancelarServico">Cancelar Serviço</button>';
		//$botoesDir .= '<button type="submit" id="srv002FormControleBtn">Salvar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário
			//window.location = "/srv/srv003/content/srv003ContentLogin/?oneWindow=true";
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#srv003ServicoImprimir').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv002/form_func-ajax/ImprimirServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId}") + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() { 
										$(this).dialog("destroy").remove(); 
									} 
								}
					    		dialogConfirm("Impressão", "Por favor, retire a ordem de serviço impressa.", \$buttons, true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					
					$('#srv002ServicoCancelarServico').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$(this).dialog("destroy").remove();
								$.getJSON('/srv/srv002/form_func-ajax/cancelarServicoGeral/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId}") + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										var \$buttons = {
											 "Fechar": function() {
											 	$(this).dialog("destroy").remove();
											 	fecharJanela('{$g['divId']}');
											 	divRefresh('{$g['divIdParent']}', true);
											 } 
										};
							    		dialogConfirm("Cancelamento do Serviço", "Serviço Cancelado com Sucesso.", \$buttons, true);
										divRefresh('{$g['divIdParent']}', true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Erro', request.data, \$buttons);
									}
								});
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Cancelar Serviço', 'Você tem certeza que deseja cancelar o serviço? ', \$buttons);
					});
					
					$('#srv002ServicoIniciar').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv002/form_func-ajax/iniciarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Gerente' + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() { 
										$(this).dialog("destroy").remove(); 
									} 
								}
					    		dialogConfirm("Serviço Iniciado", "O serviço foi iniciado!<br />Por favor, retire a ordem de serviço impressa.", \$buttons, true);
								divRefresh('{$g['divIdParent']}', true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					$('#srv002ServicoPausar').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv002/form_func-ajax/pausarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Gerente' + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() {
										$(this).dialog("destroy").remove();
									} 
								}
								divRefresh('{$g['divId']}', true);
								divRefresh('{$g['divIdParent']}', true);
					    		dialogConfirm("Serviço Pausado", "Serviço Pausado com sucesso", \$buttons, true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					
					$('#srv002ServicoCancelar').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$(this).dialog("destroy").remove();
								$.getJSON('/srv/srv002/form_func-ajax/cancelarServicoGeral/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId}") + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										var \$buttons = {
											 "Fechar": function() {
											 	$(this).dialog("destroy").remove();
											 	fecharJanela('{$g['divId']}');
											 	divRefresh('{$g['divIdParent']}', true);
											 } 
										};
							    		dialogConfirm("Cancelamento do Serviço", "Execução Cancelada com Sucesso.", \$buttons, true);
										divRefresh('{$g['divIdParent']}', true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    		dialogConfirm('Erro', request.data, \$buttons);
									}
								});
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Cancelar Serviço', 'Você tem certeza que deseja cancelar a execução do serviço? ', \$buttons);
					});
					
					$('#srv002ServicoReiniciar').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv002/form_func-ajax/reiniciarServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Gerente' + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() {
										$(this).dialog("destroy").remove();
									} 
								}
								divRefresh('{$g['divId']}', true);
					    		dialogConfirm("Serviço Reiniciado", "Serviço reiniciado com sucesso", \$buttons, true);
								divRefresh('{$g['divIdParent']}', true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					
					$('#srv002ServicoConcluir').unbind('click').bind('click', function(data) {
						$.getJSON('/srv/srv002/form_func-ajax/concluirServico/?ajax=true&T147_Id=' + encodeURIComponent("{$acaoId}") + '&D109_Id=' + encodeURIComponent("{$D109_Id}") + '&Usuario=Gerente' + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { 
									"Fechar": function() {
										$(this).dialog("destroy").remove(); 
									} 
								}
								divRefresh('{$g['divId']}', true);
					    		dialogConfirm("Serviço Concluído", "Serviço concluído com sucesso", \$buttons, true);
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons, true);
							}
						});
					});
					
				} </script></div>
EOT;

		break;
		
		case 'srv002FormularioInclusao':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		//$r_T005_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
		$form_tabela = 'T147';
		$form_campo_pk = 'T147_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'srv002FormServicosBtnAdd';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/srv/srv002/form/srv002FormularioInclusao/';

		// Campo: Procura_Servico
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where D009_C004_Id = '{$g['empresaAtual']}'
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D112',																								// tabela
			'D112_Id',																							// chave primaria
			'D112_Titulo,D112_Descricao',																		// campos
			'',																									// extras sql
			'Pesquisa de Serviço',																				// título da janela
			'Título,Descrição',																					// descrição dos campos
			'campoProcuraServico',																				// callback js
			$g['divId']																							// divId
		);

		// novo formulário
		$form = new Formulario('srv002FormServicosForm');
		//$r_T005_Id = isset($r_srv002FormServicosForm) ? $r_srv002FormServicosForm['T147_T005_Id'] : $r_T005_Id;

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
				    'Procura_Servico' => array('name' => 'Procura_Servico' , 'label' => 'Serviço', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(50,30), 'reject' => true),
					'T147_Titulo' => array('name' => 'T147_Titulo','label'=>'Título','type' => 'text', 'length' => array(50,30)),
					'T147_D112_Id' => array('name' => 'T147_D112_Id','type' => 'hidden'),
					'T147_Descricao' => array('name' => 'T147_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(2,46)),
					//'T147_Quantidade' => array('name' => 'T147_Quantidade','label'=>'Quantidade','type' => 'text', 'length' => array(10,8)),
					'T147_Valor_Unitario' => array('name' => 'T147_Valor_Unitario','label'=>'Valor Unitário','type' => 'moeda', 'casasDecimais' => 2,'length' => array(15,10)),
					//'T147_Valor_Total' => array('name' => 'T147_Valor_Total','label'=>'Valor Total','type' => 'moeda', 'length' => array(15,10)),
					'T147_Tempo_Estimado' => array('name' => 'T147_Tempo_Estimado','label'=>'Tempo Estimado','type' => 'tempo', 'length' => array(10,10)),
					'T147_D110_Id' => array('name' => 'T147_D110_Id','label'=>'Serviço','type' => 'hidden', 'length' => array(10,10)),
					//'T147_D024_Id' => array('name' => 'T147_D110_Id','label'=>'Cliente','type' => 'pesquisa','extra' => "onchange=\"carregaVeiculo(this, '{$g['divId']}');\"", 'length' => array(50,60),
					//	'pesquisa' => array('D024', 'D024_Id', 'D024_Id,D024_Nome_Empresa',"")
					//),
					//'T147_D116_Id' => array('name' => 'T147_D116_Id', 'label'=>'Veículo', 'extra'=>'style="width:280px;"','restricao' => false, 'type' => 'select', 'select'=>
					//		gGeraSelect('D116', 'D116_Id', 'D116_Placa,D116_Modelo,D116_Cor', 'WHERE D116_D024_Id=0', true)
					//),
					//'T147_Hora_Inicio' => array('name' => 'T147_Hora_Inicio','label'=>'Hora Início','type' => 'text', 'length' => array(10,10)),
					//'T147_Hora_Fim' => array('name' => 'T147_Hora_Fim','label'=>'Hora Fim','type' => 'text', 'length' => array(10,10)),
					//'T147_T005_Id' => array('name' => 'T147_T005_Id', 'type' => 'hidden', 'value'=> @$r_T005_Id, 'length' => array(50,30)),
					'T147_Quantidade' => array('name' => 'T147_Quantidade','label'=>'Quantidade', 'value'=>'1', 'type' => 'hidden'),
					'T147_Valor_Total' => array('name' => 'T147_Valor_Total','label'=>'Valor total','type' => 'hidden'),
					'T147_Status' => array('name' => 'T147_Status','label'=>'Status', 'value'=>'P', 'type' => 'hidden'),
					'T147_Hora_Inicio' => array('name' => 'T147_Hora_Inicio','label'=>'Hora Início', 'value'=>'0000-00-00 00:00:00', 'type' => 'hidden'),
		)
		)
		);
		$form->definirFormularioArray($formArray);

		


		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
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
						$retorno = $SRV002->insereServico($form, $g['mysqlLastId']);
						$alert = ($retorno === true) ? gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false) : gGeraAlertMsg('Erro', "{$retorno}", false);
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
						$retorno = $SRV002->insereServico($form, $g['mysqlLastId']);
						$alert = ($retorno === true) ? gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false) : gGeraAlertMsg('Erro', "{$retorno}", false);
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
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="srv002FormServicosBtnAdd">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
			
		break;
}
?>
