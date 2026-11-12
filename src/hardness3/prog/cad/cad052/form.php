<?php
namespace hardness;
/**
* Formulário
* /cad/cad052/form/
*/

require_once("bibliotecas/classes/CAD052.php");
$CAD052 = new CAD052();

require_once('prog/cad/cad052/form_func-js.php');
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad052formItensComposicao':

		require_once("bibliotecas/classes/CAD052.php");
		$CAD052 = new CAD052();
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;
		$retorno = $CAD052->engenhariaLiberada($acaoId);
		if($retorno === true){

			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
			$acaoId = isset($r_acaoId) ? $r_acaoId : null;

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
			$form_tabela = 'D074';
			$form_campo_pk = 'D074_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'cad052InserirItem';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/cad/cad052/form/cad052formItensComposicao/';

			$extra = "left join D049 on D049_Id=D009_D049_Id
					left join D001 on D001_Id=D049_D001_Id
					left join D082 on D049_D082_Id=D082_Id
					where D001_Id=$D001_Id
					and D009_C004_Id='{$g['empresaAtual']}'";

			$pesquisaGridExtra = <<<EOT
				left join D049 on D049_D001_Id=D001_Id
				left join D041 on D041_D049_Id=D049_Id
				left join D009 on D009_D049_Id=D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D037 on D037_Id=D001_D037_Id
				where (D009_C004_Id = '{$g['empresaAtual']}'
				and D001_Flag_Ativo='S')
				group by D009_Id
				order by D001_Codigo_Produto
	EOT;
			$pesquisaGrid = array(
				'D001',																											// tabela
				'D009_Id',																										// chave primaria
				'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D041_Valor_Base,D009_Quantidade_Estoque_Liquido,D001_Descricao_Ingles,D009_Id',	// campos
				$pesquisaGridExtra,																								// extras sql
				'Pesquisa de Produto',																							// título da janela
				'Código,Descrição, Marca,Preço,Estoque,Descrição Inglês,Link',														// descrição dos campos
				'cad052CampoProcuraProduto',																					// callback js
				$g['divId'],																									// divId
				'',					// parametro extra
				array(																											// parametros para cada campo do grid
					array('colunaAlinhar' => 'center'),
					array('style' => 'font-weight:bold'),
					array(),
					array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
					array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
					array(),
					array('callback' => 'campoLinkProdutoPesquisaGrid')
				)
			);

			$form_editor = array('geral' => '{"Procura_Produto_D074":{"top":7,"left":20,"width":266,"hidden":0},"D074_Quantidade":{"top":30,"left":2,"width":86,"hidden":0}}');
			// Array de definição do formulário
			// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
			$form = new Formulario('cad052formItensComposicao');
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Procura_Produto_D074' => array('name' => 'Procura_Produto_D074' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(40,110), 'reject' => true),
						'D074_Quantidade' => array('name' => 'D074_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'casasDecimais' => 5),
					),
				) // 'campos'
			);
			$formArray['campos']['geral']['D074_D001_Id_Composicao'] = array('name' => 'D074_D001_Id_Composicao', 'type'=>'hidden');
			$formArray['campos']['geral']['D074_D001_Id'] = array('name' => 'D074_D001_Id', 'type'=>'hidden', 'value'=> $r_D001_Id);
			$form->definirFormularioArray($formArray);

			if(!empty($acaoId)){
				$sqlD009 = mysql_query("SELECT CONCAT(D001_Codigo_Produto, ' - ', D001_Descricao_Produto) as Produto 
										FROM D001 
									LEFT JOIN D049 ON D001_Id = D049_D001_Id 
									LEFT JOIN D009 ON D049_Id = D009_D049_Id 
									LEFT JOIN D074 ON D001_Id = D074_D001_Id_Composicao 
										WHERE D074_Id = '{$acaoId}'
										AND D009_C004_Id='{$g['empresaAtual']}'");
				$D009 = mysql_fetch_array($sqlD009);
				$D009['Produto'] = mysql_real_escape_string($D009['Produto']);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#Procura_Produto_D074_Pesquisa').val('{$D009['Produto']}');
					} </script></div>
	EOT;
			}
			/**
			* PARTE 3: Processamento do formulário (se foi enviado)
			* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
			*/
			(!$g['debug']) ? $form->excluirCamposQueNaoExistem($form_tabela) : $form->verificarCamposQueNaoExistem($form_tabela);
			log('$acaoId Incluir Novo: '.$acaoId);
			$validacao = $CAD052->engenhariaLiberada($acaoId);
					log('$validacao Incluir Novo: '.$validacao);
					if (!is_bool($validacao)) {
						$alert = gGeraAlertMsg('Erroo', $validacao, false);
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
					}else{

					
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
							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
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
						$resposta = $CAD052->engenhariaDuplicada($form_tabela, $form);
						if($resposta){
							$CAD052->incluirEngenharia($form_tabela, $form);
						}
						// Insert
						/*
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
						*/
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
			$botoesEsq = '';
			$botoesDir = '';
			$labelButton = ($acaoId > 0) ? 'Alterar' : 'Incluir';
			$botoesDir .= '<button type="submit" id="cad052InserirItem">'.$labelButton.'</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		}else{
			return $retorno;
		}
	break;

	case 'cad052formProcessos':
		
		/************** CARLOS CARVALHO **********************************/
		require_once("bibliotecas/classes/CAD052.php");
		$CAD052 = new CAD052();
		$retorno = $CAD052->engenhariaLiberadaProcessoIncluir($r_D001_Id);
		if($retorno === true){
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
			$acaoId = isset($r_acaoId) ? $r_acaoId : null;

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
			$form_tabela = 'D075';
			$form_campo_pk = 'D075_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'cad052InserirProcesso';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/cad/cad052/form/cad052formProcessos/';

			$extra = "left join D049 on D049_Id=D009_D049_Id
					left join D001 on D001_Id=D049_D001_Id
					left join D082 on D049_D082_Id=D082_Id
					where D001_Id=$D001_Id
					and D009_C004_Id='{$g['empresaAtual']}'";

			// Array de definição do formulário
			// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
			$Todos = array('title' => '...', 'value' => '');
			$D078_Id = gGeraSelect('D078','D078_Id','D078_Nome',"");
			array_unshift($D078_Id, $Todos);

			$D079_Id = gGeraSelect('D079','D079_Id','D079_Nome',"");
			array_unshift($D079_Id, $Todos);

			$form_editor = array('geral' => '{"D075_Descricao":{"top":30,"left":24,"height":62,"width":248,"hidden":0},"D075_D079_Id":{"top":99,"left":33,"width":128,"hidden":0},"D075_D078_Id":{"top":122,"left":37,"width":128,"hidden":0},"D075_Lead_Time":{"top":145,"left":23,"width":120,"hidden":0},"D075_Numero_Funcionarios":{"top":168,"left":11,"width":120,"hidden":0},"D075_Ordem":{"top":7,"left":40,"width":120,"hidden":0}}');
			$form = new Formulario('cad052formItensComposicao');
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'D075_Descricao' => array('name' => 'D075_Descricao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(4,35), 'align' => 'right'),
						'D075_D079_Id' => array('name' => 'D075_D079_Id', 'label' => 'Máquina', 'type' => 'select',
							'select' => $D079_Id
						),
						'D075_D078_Id' => array('name' => 'D075_D078_Id', 'label' => 'Função', 'type' => 'select',
							'select' => $D078_Id
						),
						'D075_Lead_Time' => array('name' => 'D075_Lead_Time', 'label' => 'Lead Time', 'type' => 'moeda'),
						'D075_Numero_Funcionarios' => array('name' => 'D075_Numero_Funcionarios', 'label' => 'Funcionários', 'type' => 'text'),
						'D075_Ordem' => array('name' => 'D075_Ordem', 'label' => 'Ordem', 'type' => 'text'),
					),
				) // 'campos'
			);

			// Botão Limpar / Novo Registro
			$formArray['campos']['geral']['D075_D001_Id'] = array('name' => 'D075_D001_Id', 'type'=>'hidden', 'value' => $r_D001_Id);

			$form->definirFormularioArray($formArray);

			/**
			* PARTE 3: Processamento do formulário (se foi enviado)
			* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
			*/
			(!$g['debug']) ? $form->excluirCamposQueNaoExistem($form_tabela) : $form->verificarCamposQueNaoExistem($form_tabela);

			log('$acaoId Incluir Novo: '.$acaoId);
				$validacao = $CAD052->engenhariaLiberadaProcesso($acaoId);
						log('$validacao Incluir Novo: '.$validacao);
						if (!is_bool($validacao)) {
							$alert = gGeraAlertMsg('Erroo', $validacao, false);
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
						}else{
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
								$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
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
								// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
								$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
				$botoesEsq = '';
				$botoesDir = '';
				$labelButton = ($acaoId > 0) ? 'Alterar' : 'Incluir';
				$botoesDir .= '<button type="submit" id="cad052InserirProcesso">' . $labelButton . '</button>';
				$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
				$g['smarty']->assign('botoesId', $g['divId']);
				$g['smarty']->assign('botoesEsq', $botoesEsq);
				$g['smarty']->assign('botoesDir', $botoesDir);
				$g['smarty']->display('buttons-floatBottom.tpl');
				// Ações dos botões
				// A ação do botão Enviar é definido dentro do formulário
			}
		}else{
			return $retorno;
		}
	break;

	case 'cad052formConsumiveis':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;

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
		$form_tabela = 'D076';
		$form_campo_pk = 'D076_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad052InserirConsumivel';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad052/form/cad052formConsumiveis/';

		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id 
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo='S')
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																											// tabela
			'D009_Id',																										// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D041_Valor_Base,D009_Quantidade_Estoque_Liquido,D001_Descricao_Ingles,D009_Id',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Produto',																							// título da janela
			'Código,Descrição, Marca,Preço,Estoque,Descrição Inglês,Link',														// descrição dos campos
			'cad052CampoProcuraConsumivel',																					// callback js
			$g['divId'],																									// divId
			'',					// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array(),
				array('callback' => 'campoLinkProdutoPesquisaGrid')
			)
		);
		$form_editor = array('geral' => '{"Procura_Produto_D076":{"top":7,"left":20,"width":250,"hidden":0},"D076_Quantidade":{"top":30,"left":2,"width":86,"hidden":0}}');
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad052formConsumiveis');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto_D076' => array('name' => 'Procura_Produto_D076' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(35,110), 'reject' => true),
					'D076_D009_Id' => array('name' => 'D076_D009_Id', 'label' => 'Nome', 'type' => 'hidden', 'length' => array(10,255), 'align' => 'right'),
                	'D076_D075_Id' => array('name' => 'D076_D075_Id', 'label' => 'Nome', 'type' => 'hidden', 'value' => $r_D075_Id, 'align' => 'right'),
					'D076_Quantidade' => array('name' => 'D079_Custo_Minuto', 'label' => 'Quantidade', 'type' => 'text', 'length' => array(10,255)),
				),

            ) // 'campos'
		);

		// Botão Limpar / Novo Registro
		$form->definirFormularioArray($formArray);

		if(!empty($acaoId)){
			$sqlD009 = mysql_query("SELECT CONCAT(D001_Codigo_Produto, ' - ', D001_Descricao_Produto) as Produto FROM D001 LEFT JOIN D049 ON D001_Id=D049_D001_Id LEFT JOIN D009 ON D049_Id=D009_D049_Id LEFT JOIN D076 ON D076_D009_Id=D009_Id WHERE D076_Id = '{$acaoId}'");
			$D009 = mysql_fetch_array($sqlD009);
			$D009['Produto'] = mysql_real_escape_string($D009['Produto']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#Procura_Produto_D076_Pesquisa').val('{$D009['Produto']}');
				} </script></div>
EOT;
		}
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		(!$g['debug']) ? $form->excluirCamposQueNaoExistem($form_tabela) : $form->verificarCamposQueNaoExistem($form_tabela);
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
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
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
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
		$botoesEsq = '';
		$botoesDir = '';
		$labelButton = ($acaoId > 0) ? 'Alterar' : 'Incluir';
		$botoesDir .= '<button type="submit" id="cad052InserirConsumivel">'.$labelButton.'</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;

	case 'cad052formProduto':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;

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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad052ModificarProduto';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad052/form/cad052formProduto/';

		$form_editor = array('geral' => '{"D001_Lote_Economico":{"top":7,"left":12,"width":104,"hidden":0},"D009_Valor_Custo_Unitario":{"top":30,"left":31,"width":104,"hidden":0},"D037_Unidade":{"top":53,"left":43,"width":111,"hidden":0},"D001A_Flag_Engenharia_Liberada":{"top":76,"left":38.8125,"width":63,"hidden":0}}');
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad052formProduto');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D001_Lote_Economico' => array('name' => 'D001_Lote_Economico', 'label' => 'Lote Economico', 'type' => 'moeda', 'length' => array(12,255)),
					'D009_Valor_Custo_Unitario' => array('name' => 'D009_Valor_Custo_Unitario', 'label' => 'Custo', 'type' => 'moeda', 'length' => array(12,255), 'reject'=>true),
					'D037_Unidade' => array('name' => 'D037_Unidade', 'label' => 'Unidade', 'type' => 'select', 'reject'=>true,
						'select' => gGeraSelect("D037", "D037_Unidade", "D037_Unidade", false, true)
					),
					'D001A_Flag_Engenharia_Liberada' => array('name' => 'D001A_Flag_Engenharia_Liberada', 'label' => 'Engenharia Liberada', 'type' => 'select',
				       'select' => array(array('title' => 'NÃO', 'value' => 'N'), array('title' => 'SIM', 'value' => 'S'))),
 					'D001_Id' => array('name' => 'D001_Id', 'type' => 'hidden', 'value' => @$r_acaoId),
				),
            ) // 'campos'
		);
  
		// Botão Limpar / Novo Registro
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$retorno = $CAD052->alterarProduto($form);
				echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
			}
		}

		$D001=mysql_query("SELECT D001_Lote_Economico, D009_Valor_Custo_Unitario, D037_Unidade, D037_Id,D001A_Flag_Engenharia_Liberada FROM D001
			                   LEFT JOIN D037  on D037_Id=D001_D037_Id
			                   LEFT JOIN D049  on D049_D001_Id=D001_Id
			                   LEFT JOIN D009  on D009_D049_Id=D049_Id
							   LEFT JOIN D001A on D001A_D001_Id=D001_Id
			                   WHERE D001_Id='{$r_acaoId}'");
		$mD001 = mysql_fetch_array($D001);

		$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);

		$form->campoSetarValor('D001_Lote_Economico', gCorrigeNumero($mD001['D001_Lote_Economico']));
		$form->campoSetarValor('D009_Valor_Custo_Unitario', gCorrigeNumero($mD001['D009_Valor_Custo_Unitario']));
		$form->campoSetarValor('D037_Unidade', $mD001['D037_Unidade']);
		$form->campoSetarValor('D001A_Flag_Engenharia_Liberada', $mD001['D001A_Flag_Engenharia_Liberada']);

		// mostra erros de formulário uteis ao desenvolvedor
		if ($processado !== true) {
			reset($form->formArray['campos']);
			if ($tab == key($form->formArray['campos'])) {
				echo  $processado;
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
		$botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad052ModificarProduto">Alterar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
}
?>
