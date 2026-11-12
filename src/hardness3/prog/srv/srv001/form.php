<?php
namespace hardness;
require_once('prog/srv/srv001/form_func-js.php');
//require_once("bibliotecas/classes/SRV001.php");
switch ($g['r'][3]) {
	case 'form1':
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
			$form_tabela = 'D112';
			$form_campo_pk = 'D112_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'content_form1_btn_servico';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/srv/srv001/form/form1/';
			//form editor
			$form_editor = array(
				'geral' => '{"D112_T212_Id":{"top":168,"left":70,"width":352,"hidden":0},"D112_Titulo":{"top":7,"left":58,"width":320,"hidden":0},"D112_Descricao":{"top":30,"left":41,"height":62,"width":322,"hidden":0},"D112_Tempo_Estimado":{"top":99,"left":13,"width":95,"hidden":0},"D112_Valor_Unitario":{"top":122,"left":26,"width":95,"hidden":0},"D112_D110_Id":{"top":145,"left":29,"width":327,"hidden":0}}',
			);
		
			// Array de definição do formulário
			// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
			$form = new Formulario('form_cad002_form1');
			$formArray = array(
				'campos' => array(
					'geral' => array(
			        	'D112_Titulo' => array('name' => 'D112_Titulo', 'label'=>'Título', 'type' => 'text','length' => array(60,80)),
			        	'D112_Descricao' => array('name' => 'D112_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,57)),
			        	'D112_Tempo_Estimado' => array('name' => 'D112_Tempo_Estimado', 'label'=>'Tempo Estimado', 'type' => 'tempo', 'length' => array(15,20)),
			        	'D112_Valor_Unitario' => array('name' => 'D112_Valor_Unitario', 'label'=>'Valor Unitário', 'type' => 'moeda','length' => array(15,20)),
			        	'D112_D110_Id' => array('name' => 'D112_D110_Id', 'label'=>'Competencia', 'type' => 'pesquisa','length' => array(60,80), 'pesquisa'=>array('D110', 'D110_Id', 'D110_Titulo')),
			        	'D112_T212_Id' => array('name' => 'D112_T212_Id', 'label'=>'Tipo', 'type' => 'select',
			        		'select' => gGeraSelect('T212', 'T212_Id', 'T212_Nome', '')
			        	),
	                ),
	            ) // 'campos'
			);
			
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
							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										}, 150);
	  								});
	  								{$alert}
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
							$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
							$acaoId = $g['mysqlLastId'];
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Cadastro Serviço - ID: ' + {$acaoId}, '/srv/srv001/content/form1/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [650,550]);
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
			echo  $g['smarty']->fetch('formEditor.tpl');
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="content_form1_btn_servico">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário
		break;
		
		case 'srv001form002Processos':
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
			$D112_Id = (isset($r_form_srv001_form1)) ? $r_form_srv001_form1['D113_D112_Id'] : $r_D112_Id;
			
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
			$form_tabela = 'D113';
			$form_campo_pk = 'D113_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = array('srv001ProcessosBtnAdd', 'srv001ProcessosBtnCln', 'srv001FormProcessosBtnNew');
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/srv/srv001/form/srv001form002Processos/';
			// Array de definição do formulário
			// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
			$form = new Formulario('form_srv001_form1');
			$formArray = array(
				'campos' => array(
					'geral' => array(
			        	'D113_Titulo' => array('name' => 'D113_Titulo', 'label'=>'Título', 'type' => 'text','length' => array(30,30)),
			        	//'D113_D110_Id' => array('name' => 'D113_D110_Id', 'label'=>'Competencia', 'type' => 'pesquisa','length' => array(30,30), 'pesquisa'=>array('D110', 'D110_Id', 'D110_Titulo')),
			        	'D113_Descricao' => array('name' => 'D113_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,27)),
			        	'D113_D112_Id'=>array('name'=>'D113_D112_Id', 'type'=>'hidden', 'value'=>$D112_Id),
						'divReload' => array('name' => 'divReload', 'label' => 'divReload', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
	                ),
	            ) // 'campos'
			);
			
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				$formArray['campos']['geral']['srv001FormProcessosBtnNew'] = array('name' => 'srv001FormProcessosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['srv001ProcessosBtnCln'] = array('name' => 'srv001ProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['geral']['srv001ProcessosBtnAdd'] = array('name' => 'srv001ProcessosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			
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
							//echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									divRefresh('{$form->campoValorEnviado('divReload')}', true);
									divRefresh('{$g['divId']}', true);
								} </script></div>
EOT;
						}
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							//echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
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
							echo  $processado;
						}
					}
					
					/**
					 * Modifica campos em caso de update 
					 */
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
			$g['smarty']->display('form.tpl');
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário			
			
		break;
		
		case 'srv001form003Materiais':
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
			$D112_Id = (isset($r_form_srv001_form1)) ? $r_form_srv001_form1['D114_D112_Id'] : $r_D112_Id;
			
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
			$form_tabela = 'D114';
			$form_campo_pk = 'D114_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = array('srv001MateriaisBtnAdd', 'srv001MateriaisBtnCln', 'srv001FormMateriaisBtnNew');
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/srv/srv001/form/srv001form003Materiais/';
			
			// Campo: Procura_Produto
			$pesquisaGridExtra = <<<EOT
				left join D049 on D049_D001_Id=D001_Id
				left join D009 on D009_D049_Id=D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D037 on D037_Id=D001_D037_Id
				where (D009_C004_Id = '{$g['empresaAtual']}')
				  and D001_Flag_Pre_Cadastro!='S'
				group by D009_Id
				order by D001_Codigo_Produto
EOT;
			$pesquisaGrid = array(
				'D001',																								// tabela
				'D009_Id',																							// chave primaria
				'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido',			// campos
				$pesquisaGridExtra,																					// extras sql
				'Pesquisa de Material',																				// título da janela
				'Código,Descrição,Marca,Estoque',																	// descrição dos campos
				'campoProcuraProduto',																				// callback js
				$g['divId'],																						// divId
				'',//(isset($r_ven001FormProdutosForm) ? $r_ven001FormProdutosForm['T004_T003_Id'] : $r_T003_Id),		// parametro extra
				array(																								// parametros para cada campo do grid
					array('colunaAlinhar' => 'center'),
					array(),
					array(),
					array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
				)
			);
					
			// Array de definição do formulário
			// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
			$form = new Formulario('form_srv001_form1');
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Material', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,30), 'reject' => true),
						'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120),'reject' => true),
						'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true),			
			        	//'D114_D111_Id' => array('name' => 'D114_D111_Id', 'label'=>'Material', 'type' => 'pesquisa','length' => array(30,30), 'pesquisa'=>array('D111', 'D111_Id', 'D111_Descricao')),
						'D114_D037_Id' => array('name' => 'D114_D037_Id', 'label'=>'Unidade', 'extra'=>'style="width:165px;"', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true),
						),												
			        	'D114_Quantidade' => array('name' => 'D114_Quantidade', 'label'=>'Quantidade', 'type' => 'moeda','length' => array(15,10)),
			        	'D114_D112_Id' => array('name'=> 'D114_D112_Id', 'value'=>$D112_Id, 'type'=>'hidden'),
						'D114_D009_Id' => array('name' => 'D114_D009_Id', 'label' => 'D114_D009_Id', 'type' => 'hidden'),
			        	'divReload' => array('name' => 'divReload', 'label' => 'divReload', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
	                ),
	            ) // 'campos'
			);
			
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				$formArray['campos']['geral']['srv001FormMateriaisBtnNew'] = array('name' => 'srv001FormMateriaisBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['srv001MateriaisBtnCln'] = array('name' => 'srv001MateriaisBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['geral']['srv001MateriaisBtnAdd'] = array('name' => 'srv001MateriaisBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			
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
							echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									divRefresh('{$g['divId']}', true);
								} </script></div>
EOT;
						}
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
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
							echo  $processado;
						}
					}
					
					/**
					 * Modifica campos em caso de update 
					 */
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
			$g['smarty']->display('form.tpl');
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário
			
			
			
		break;
		
		
	}
?>
