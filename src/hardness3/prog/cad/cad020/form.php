<?php
namespace hardness;
/**
* Formulário
* /cad/cad002/form/
*/

require_once("bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();
require_once("bibliotecas/classes/CAD020.php");
$CAD020 = new CAD020();


/**
* Ações
*/
switch ($g['r'][3]) {

	case 'cad020form1':
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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad020content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad020/form/cad020form1/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad020_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label'=>'Tipo', 'extra'=>'style="width:182px;"', 'extra'=>'style="width:182px;"', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('C008', 'C008_Id', 'C008_Tipo', '', true)
					),
		        	'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label'=>'Código Produto', 'type' => 'text','length' => array(30,20)),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'label'=>'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Codigo_Barras' => array('name' => 'D001_Codigo_Barras', 'label'=>'Código de barras', 'type' => 'text', 'length' => array(64, 60)),					
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D001_D060_Id' => array('name' => 'D001_D060_Id', 'label'=>'Grupo', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo, D060_Codigo_Padrao', ' order by D060_Descricao_Grupo ', true)
					),					
					'D001_D002_Id' => array('name' => 'D001_D002_Id', 'label'=>'Sub-Grupo', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto, D002_Codigo_Padrao', ' order by D002_Descricao_Produto', true)
					),					
					'D001_D015_Id' => array('name' => 'D001_D015_Id', 'label'=>'Aplicação', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D015', 'D015_Id', 'D015_Nome_Grupo, D015_Codigo_Padrao', 'order by D015_Nome_Grupo', true)
					),					
					'D001_Peso_Unitario_Kg' => array('name' => 'D001_Peso_Unitario_Kg', 'label'=>'Peso Unitário Kg', 'type' => 'text', 'length' => array(20, 30)),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'label'=>'Qtde por embalagem ', 'type' => 'text', 'length' => array(15, 30), 'align' => 'right', 'style'=>'float:right;'),
					'D001_Peso_Total_Kg' => array('name' => 'D001_Peso_Total_Kg', 'label'=>'Peso Total Emb. Kg', 'type' => 'text', 'length' => array(20, 30)),
					'D001_D037_Id' => array('name' => 'D001_D037_Id', 'label'=>'Unidades', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true)
					),
					'D001_D005_Id' => array('name' => 'D001_D037_Id', 'label'=>'NCM', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal', 'order by D005_Classificacao_Fiscal', true)
					),
					'D001_Especificacoes' => array('name' => 'D001_Especificacoes', 'label'=>'Especificações', 'type' => 'textarea', 'length' => array(5, 61)),
					'D001_Flag_Tipo' => array('name' => 'D001_Flag_Tipo', 'label'=>'Tipo', 'type' => 'select', 'extra' => 'style="width:85px;"', 'select' =>
						array(
							array('title' => 'Produzido', 'value'=>'PRODUZIDO', 'selected'=>true),
							array('title' => 'Comprado', 'value' =>'COMPRADO')
						)
					),
					'D001_Flag_Ativo' => array('name' => 'D001_Flag_Ativo', 'label'=>'Item ativo', 'type' => 'select', 'extra' => 'style="width:60px;"', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
                ),
				
            ) // 'campos'
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
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
				
				$cClausula = '';
				if ($form->campoValor('D001_C008_Id') > 9999)
				{
                    $cClausula .= " and D003_C008_Id='" . $form->campoValor('D001_C008_Id') . "' ";
				}
                if ($form->campoValor('C008_Inclusao_Produto_Mostrar_Codigo_Grupos') == 'S')
                {
		   		    $cClausula .= " order by D003_Codigo_Padrao ";
                }
                else
                {
		   		    $cClausula .= " order by D003_Nome_Linha ";
                }
				$form->campoSetarSelect('D001_D003_Id', gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', $cClausula));
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
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad020content_form1_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'cad020form2':
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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad020content_form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad020/form/cad020form2/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad020_form2');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label'=>'Tipo', 'extra'=>'style="width:182px;"', 'type' => 'select', 'select'=>
						//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
						gGeraSelect('C008', 'C008_Id', 'C008_Tipo', '', true)
					),
		        	'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label'=>'Código Produto', 'type' => 'text','length' => array(30,20)),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'label'=>'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Codigo_Barras' => array('name' => 'D001_Codigo_Barras', 'label'=>'Código de barras', 'type' => 'text', 'length' => array(64, 60)),					
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D001_D060_Id' => array('name' => 'D001_D060_Id', 'label'=>'Grupo', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo, D060_Codigo_Padrao', ' order by D060_Descricao_Grupo ', true)
					),					
					'D001_D002_Id' => array('name' => 'D001_D002_Id', 'label'=>'Sub-Grupo', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto, D002_Codigo_Padrao', ' order by D002_Descricao_Produto', true)
					),					
					'D001_D015_Id' => array('name' => 'D001_D015_Id', 'label'=>'Aplicação', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D015', 'D015_Id', 'D015_Nome_Grupo, D015_Codigo_Padrao', 'order by D015_Nome_Grupo', true)
					),					
					'D001_Peso_Unitario_Kg' => array('name' => 'D001_Peso_Unitario_Kg', 'label'=>'Peso Unitário Kg', 'type' => 'text', 'length' => array(20, 30)),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'label'=>'Qtde por embalagem ', 'type' => 'text', 'length' => array(15, 30), 'align' => 'right', 'style'=>'float:right;'),
					'D001_Peso_Total_Kg' => array('name' => 'D001_Peso_Total_Kg', 'label'=>'Peso Total Emb. Kg', 'type' => 'text', 'length' => array(20, 30)),
					'D001_D037_Id' => array('name' => 'D001_D037_Id', 'label'=>'Unidades', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true)
					),
					'D001_D005_Id' => array('name' => 'D001_D037_Id', 'label'=>'NCM', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal', 'order by D005_Classificacao_Fiscal', true)
					),
					'D001_Especificacoes' => array('name' => 'D001_Especificacoes', 'label'=>'Especificações', 'type' => 'textarea', 'length' => array(5, 61)),
					'D001_Flag_Tipo' => array('name' => 'D001_Flag_Tipo', 'label'=>'Tipo', 'type' => 'select', 'extra' => 'style="width:85px;"', 'select' =>
						array(
							array('title' => 'Produzido', 'value'=>'PRODUZIDO', 'selected'=>true),
							array('title' => 'Comprado', 'value' =>'COMPRADO')
						)
					),
					'D001_Flag_Ativo' => array('name' => 'D001_Flag_Ativo', 'label'=>'Item ativo', 'type' => 'select', 'extra' => 'style="width:60px;"', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
                ),
				
            ) // 'campos'
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
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
  								divRefresh("{$g['divIdParent']}", true);
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
				$cClausula = '';
				if ($form->campoValor('D001_C008_Id') > 9999)
				{
                    $cClausula .= " and D003_C008_Id='" . $form->campoValor('D001_C008_Id') . "' ";
				}
                if ($form->campoValor('C008_Inclusao_Produto_Mostrar_Codigo_Grupos') == 'S')
                {
		   		    $cClausula .= " order by D003_Codigo_Padrao ";
                }
                else
                {
		   		    $cClausula .= " order by D003_Nome_Linha ";
                }
				$form->campoSetarSelect('D001_D003_Id', gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', $cClausula));
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
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad020content_form2_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		
	break;
		
	case 'cad020tributos':
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
		$form_tabela = 'D009';
		$form_campo_pk = 'D009_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002AcaoJuntarMarcas';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/juntarMarcas/';	
		//Criar a subdivisão do form conforme sistema antigo.
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D009_Flag_Isento_IPI' => array('name' => 'D009_Flag_Isento_IPI', 'label' => 'Isento IPI', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2),
						),
					),
					'D009_Percentual_Reducao_IPI' => array('name' => 'D009_Percentual_Reducao_IPI', 'label' => 'Percentual Redução IPI', 'type'=>'text'),
					'D009_Aliquota_IPI' => array('name' => 'D009_Aliquota_IPI', 'label' => 'Alíquota IPI', 'type'=>'text'),
					'D009_Flag_Isento_ICMS' => array('name' => 'D009_Flag_Isento_ICMS', 'label' => 'Isento ICMS', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2),
						),
					),
					'D009_Percentual_Reducao_ICMS' => array('name' => 'D009_Percentual_Reducao_ICMS', 'label' => 'Percentual Redução ICMS', 'type'=>'text'),
					'D009_Aliquota_ICMS' => array('name' => 'D009_Aliquota_ICMS', 'label' => 'Alíquota ICMS', 'type'=>'text'),
					'D009_Flag_Isento_ICMS_Venda' => array('name' => 'D009_Flag_Isento_ICMS_Venda', 'label' => 'Isento ICMS', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2),
						),
					),
					'D009_Percentual_Reducao_ICMS_Venda' => array('name' => 'D009_Percentual_Reducao_ICMS_Venda', 'label' => 'Redução ICMS', 'type'=>'text'),
					'D009_Aliquota_ICMS_Venda' => array('name' => 'D009_Aliquota_ICMS_Venda', 'label' => 'Alíquota ICMS', 'type'=>'text'),
					'D009_D059_Id' => array('name' => 'D009_D059_Id', 'label'=>'Situação Tributária', 'extra'=>'style="width:350px;"', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST, D059_Descricao_Origem', 'where D059_Simples_Nacional=\'S\' order by D059_Codigo_ST,D059_Descricao_Origem' , true),
					),
					'D009_Flag_Base_ICMS_Cheia_NF' => array('name' => 'D009_Flag_Base_ICMS_Cheia_NF', 'label' => 'Emitir Base ICMS Cheia na NF', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2),
						),
					),
					'D009_Id' => array('name' => 'D009_Id', 'type' => 'hidden'),
					'D009_D001_Id' => array('name' => 'D009_D001_Id', 'type' => 'hidden'),
					'D009_D024_Id' => array('name' => 'D009_D024_Id', 'type' => 'hidden'),
					'D009_Codigo_Produto_Fornecedor' => array('name' => 'D009_Codigo_Produto_Fornecedor', 'type' => 'hidden'),
					'D009_C004_Id' => array('name' => 'D009_C004_Id', 'type' => 'hidden'),
					'D009_Peso_Unitario_Kg' => array('name' => 'D009_Peso_Unitario_Kg', 'type' => 'hidden'),
					'D009_Aplicacao' => array('name' => 'D009_Aplicacao', 'type' => 'hidden'),
					'D009_Marca' => array('name' => 'D009_Marca', 'type' => 'hidden'),
					'D009_Prazo_Entrega' => array('name' => 'D009_Prazo_Entrega', 'type' => 'hidden'),
					'D009_Quantidade_Estoque' => array('name' => 'D009_Peso_Unitario_Kg', 'type' => 'hidden'),
					'D009_Quantidade_Maxima_Estoque' => array('name' => 'D009_Quantidade_Maxima_Estoque', 'type' => 'hidden'),
					'D009_Quantidade_Comprar' => array('name' => 'D009_Quantidade_Comprar', 'type' => 'hidden'),
					'D009_Quantidade_Minima_Estoque' => array('name' => 'D009_Quantidade_Minima_Estoque', 'type' => 'hidden'),
					'D009_Quantidade_Seguranca_Estoque' => array('name' => 'D009_Quantidade_Seguranca_Estoque', 'type' => 'hidden'),
					'D009_Valor_Custo_Unitario' => array('name' => 'D009_Valor_Custo_Unitario', 'type' => 'hidden'),
                ),
				
            ) // 'campos'
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
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$msg = $CAD002->juntarMarcas($form->campoValorEnviado('D049_Antigo_Id'), $form->campoValorEnviado('D049_Novo_Id'));
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
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
				$cClausula = '';
				if ($form->campoValor('D001_C008_Id') > 9999)
				{
                    $cClausula .= " and D003_C008_Id='" . $form->campoValor('D001_C008_Id') . "' ";
				}
                if ($form->campoValor('C008_Inclusao_Produto_Mostrar_Codigo_Grupos') == 'S')
                {
		   		    $cClausula .= " order by D003_Codigo_Padrao ";
                }
                else
                {
		   		    $cClausula .= " order by D003_Nome_Linha ";
                }
				$form->campoSetarSelect('D001_D003_Id', gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', $cClausula));
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
		$botoesDir .= '<button type="submit" id="cad002AcaoJuntarMarcas" style="margin-right:20px;">Enviar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'juntarMarcas':
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
		$form_tabela = 'D009';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002AcaoJuntarMarcas';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/juntarMarcas/';
		$extra = "left join D049 on D049_Id=D009_D049_Id
				  left join D001 on D001_Id=D049_D001_Id
                  left join D082 on D049_D082_Id=D082_Id
                where D001_Id=$D001_Id
		        and D009_C004_Id='{$g['empresaAtual']}'";
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D049_Antigo_Id' => array('name' => 'D049_Antigo_Id', 'label'=>'Marca Antiga', 'extra'=>'style="width:350px;"', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D009', 'D049_Id', 'D082_Marca, D049_Modelo', $extra, true),
					),
					'D049_Novo_Id' => array('name' => 'D049_Novo_Id', 'label'=>'Marca Nova', 'extra'=>'style="width:350px;"', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D009', 'D049_Id', 'D082_Marca, D049_Modelo', $extra, true),
					),
                ),
				
            ) // 'campos'
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
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$msg = $CAD002->juntarMarcas($form->campoValorEnviado('D049_Antigo_Id'), $form->campoValorEnviado('D049_Novo_Id'));
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
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
		$g['smarty']->display('form.tpl');
		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002AcaoJuntarMarcas">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'cad020editarMarca':
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
		$form_tabela = 'D049';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002AcaoJuntarMarcas';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/juntarMarcas/';	
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D049_D082_Id' => array('name' => 'D049_D082_Id', 'label'=>'Marca', 'extra'=>'style="width:165px;"', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D082', 'D082_Id', 'D082_Marca', 'order by D082_Marca', true),
					),
					'D049_Flag_Tipo' => array('name' => 'D049_Flag_Tipo', 'label' => 'Tipo', 'type'=>'select','extra'=>'style="width:165px;"', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'COMPRADO', 'value' => 'CP'),
							array('title' => 'PRODUZIDO', 'value' => 'PD'),
							array('title' => 'MONTADO', 'value' => 'MT'),
							array('title' => 'MODIFICADO', 'value' => 'MD'),
						),
					),
					'D049_Codigo_Produto_Fornecedor' => array('name' => 'D049_Codigo_Produto_Fornecedor', 'label' => 'Código Original', 'type'=>'text', 'length' => array(26,30)),
					'D049_Valor_Preco_Tabela' => array('name' => 'D049_Valor_Preco_Tabela', 'label' => 'Preço Tabela', 'type'=>'moeda', 'length' => array(10,15)),
					'D049_Percentual_Desconto_Tabela' => array('name' => 'D049_Percentual_Desconto_Tabela', 'label' => 'Desconto Tabela', 'type'=>'text', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_Aliquota_ICMS_Tabela' => array('name' => 'D049_Aliquota_ICMS_Tabela', 'label' => 'ICMS Tabela', 'type'=>'text', 'length' => array(10,15)),
					'D049_ICF' => array('name' => 'D049_ICF', 'label' => 'ICF', 'type'=>'text', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_IPV' => array('name' => 'D049_IPV', 'label' => 'IPV', 'type'=>'text', 'length' => array(10,15)),
					'D049_Data_Cadastro' => array('name' => 'D049_Data_Cadastro', 'label' => 'Data Cadastro', 'type'=>'data', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_Observacao_Compra' => array('name' => 'D049_Observacao_Compra', 'label' => 'Observação Compra', 'type'=>'textarea', 'length' => array(3,55)),
					'D049_Flag_Nacional_Importado' => array('name' => 'D049_Flag_Nacional_Importado', 'label' => 'Nacional/Importado', 'type'=>'select','extra'=>'style="width:120px;"', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'NACIONAL', 'value' => 'N'),
							array('title' => 'IMPORTADO', 'value' => 'I'),
						),
					),
					'D049_Flag_Ativo' => array('name' => 'D049_Flag_Ativo', 'label' => 'Ativo', 'type'=>'select', 'extra'=>'style="width:80px;"', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						),
					),
					'D049_Flag_Substituicao_Tributaria' => array('name' => 'D049_Flag_Substituicao_Tributaria', 'label' => 'Substituição Tributária', 'type'=>'select', 'align' => 'right', 'extra'=>'style="width:80px; float:right;"', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						),
					),
					'D049_Origem_Mercadoria' => array('name' => 'D049_Origem_Mercadoria', 'label' => 'Origem mecadoria', 'style'=>'width:280px;', 'type'=>'select', 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7')
						)
					),
				),
            ) // 'campos'
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
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$msg = $CAD002->juntarMarcas($form->campoValorEnviado('D049_Antigo_Id'), $form->campoValorEnviado('D049_Novo_Id'));
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
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
		if($acaoId){
			$g['smarty']->display('form.tpl');	
			$g['smarty']->display('buttons-floatBottom.tpl');
		}else{
			echo '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002" style="margin-right:20px;">Executar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	//Form Cadasatrar Marca
	case 'cadastroMarca':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
				
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
		$form_tabela = 'D049';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002cadastroMarcas';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cadastroMarca/';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D049_D082_Id' => array('name' => 'D049_D082_Id', 'label'=>'Marca', 'extra'=>'style="width:165px;"', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D082', 'D082_Id', 'D082_Marca', 'order by D082_Marca', true),
					),
					'D049_Flag_Tipo' => array('name' => 'D049_Flag_Tipo', 'label' => 'Tipo', 'type'=>'select','extra'=>'style="width:165px;"', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'COMPRADO', 'value' => 'CP'),
							array('title' => 'PRODUZIDO', 'value' => 'PD'),
							array('title' => 'MONTADO', 'value' => 'MT'),
							array('title' => 'MODIFICADO', 'value' => 'MD'),
						),
					),
					'D049_Modelo' => array('name' => 'D049_Modelo', 'label' => 'Modelo', 'type'=>'text', 'length' => array(26,30)),
					'D049_Codigo_Produto_Fornecedor' => array('name' => 'D049_Codigo_Produto_Fornecedor', 'label' => 'Código Original', 'type'=>'text', 'length' => array(26,30)),
					'D049_Valor_Preco_Tabela' => array('name' => 'D049_Valor_Preco_Tabela', 'label' => 'Preço Tabela', 'type'=>'moeda', 'length' => array(10,15)),
					'D049_Percentual_Desconto_Tabela' => array('name' => 'D049_Percentual_Desconto_Tabela', 'label' => 'Desconto Tabela', 'type'=>'text', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_Aliquota_ICMS_Tabela' => array('name' => 'D049_Aliquota_ICMS_Tabela', 'label' => 'ICMS Tabela', 'type'=>'text', 'length' => array(10,15)),
					'D049_ICF' => array('name' => 'D049_ICF', 'label' => 'ICF', 'type'=>'text', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_IPV' => array('name' => 'D049_IPV', 'label' => 'IPV', 'type'=>'text', 'length' => array(10,15)),
					'D049_Data_Cadastro' => array('name' => 'D049_Data_Cadastro', 'label' => 'Data Cadastro', 'type'=>'data', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_Observacao_Compra' => array('name' => 'D049_Observacao_Compra', 'label' => 'Observação Compra', 'type'=>'textarea', 'length' => array(3,55)),
					'D049_Flag_Nacional_Importado' => array('name' => 'D049_Flag_Nacional_Importado', 'label' => 'Nacional/Importado', 'type'=>'select','extra'=>'style="width:120px;"', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'NACIONAL', 'value' => 'N'),
							array('title' => 'IMPORTADO', 'value' => 'I'),
						),
					),
					'D049_Flag_Ativo' => array('name' => 'D049_Flag_Ativo', 'label' => 'Ativo', 'type'=>'select', 'extra'=>'style="width:80px;"', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						),
					),
					'D049_Flag_Substituicao_Tributaria' => array('name' => 'D049_Flag_Substituicao_Tributaria', 'label' => 'Substituição Tributária', 'type'=>'select', 'align' => 'right', 'extra'=>'style="width:80px; float:right;"', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						),
					),
					'D049_D001_Id' => array('name' => $D001_Id, 'type'=>'text', 'type'=>'hidden'),
					'D049_Aplicacao' => array('name' => 'D049_Aplicacao', 'type'=>'hidden'),
					'D049_Peso_Unitario_Kg' => array('name' => 'D049_Peso_Unitario_Kg', 'type'=>'hidden'),
					'D049_Origem_Mercadoria' => array('name' => 'D049_Origem_Mercadoria', 'label' => 'Origem mecadoria', 'style'=>'width:280px;', 'type'=>'select', 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7')
						)
					),
				),
            ) // 'campos'
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
				// Insert
				if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
					
					$return = $CAD002->cadastroMarca($form);
					
					$form->campoValorEnviado('T0014_Id');
					
					// Imprime uma mensagem e fecha a janela
					$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
					echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		$g['smarty']->display('form.tpl');		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '<button type="submit" id="cad002cadastroMarcas">Cadastrar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->display('buttons-floatBottom.tpl');	

	break;
	
	/**
	 * TAB certificados
	 */
	case 'certificados':
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
		$form_tabela = 'T140';
		$form_campo_pk = 'T140_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/certificados/';
		// Array de definição do formulário
		$r_D001_Id = isset($r_form_cad002_form1) ? $r_form_cad002_form1['T140_D001_Id'] : $r_D001_Id;
		$r_D024_Id = isset($r_form_cad002_form1) ? $r_form_cad002_form1['T140_D024_Id'] : $r_D024_Id;
		
		$tmp = gGeraSelect('D001', 'D001_Id', 'D001_Descricao_Produto', "WHERE D001_Id = $r_D001_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['T140_D024_Id'] 			= array('name' => 'T140_D024_Id', 'label'=>'Fornecedor', 'type' => 'pesquisa', 'pesquisa'=>array('D024','D024_Id','D024_Nome_Empresa',""),'length' => array(70,60));
		$formArray['campos']['geral']['__T140_D001_Id'] 		= array('name' => '__T140_D001_Id', 'label'=>'Produto', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
			$tmp
		);
		$formArray['campos']['geral']['T140_D001_Id'] 			= array('name' => 'T140_D001_Id', 'type' => 'hidden', 'value' => $r_D001_Id);
		$formArray['campos']['geral']['T140_Data_Emissao'] 		= array('name' => 'T140_Data_Emissao', 'label'=>'Data Emissão', 'type' => 'data', 'length' => array(8, 8));
		$formArray['campos']['geral']['T140_Data_Validade'] 	= array('name' => 'T140_Data_Validade', 'label'=>'Data Validade', 'type' => 'data', 'length' => array(8, 8));
		$formArray['campos']['geral']['T140_Tipo_Certificado'] 	= array('name' => 'T140_Tipo_Certificado', 'label'=>'Tipo', 'type' => 'text','length' => array(70,70));
		$formArray['campos']['geral']['T140_Certificadora'] 	= array('name' => 'T140_Certificadora', 'label'=>'Empresa Certificadora', 'type' => 'text','length' => array(70,70));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo certificado', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['T140_Nome_Arquivo'] 		= array('name' => 'T140_Nome_Arquivo', 'type' => 'hidden');
		$formArray['campos']['geral']['content_form2_btn'] 		= array('name' => 'content_form2_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir Certificado', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:0px;margin-top:0px;padding-left:10px;padding-right:0px;padding-top:0px;height:20px;"', 'reject'=>true);
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
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								if (typeof jQuery != 'undefined') {  
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											{$alert}
										}, 150);
  									});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo = $form->campoValorEnviado('uploadArquivo');
					if ($arquivo['error'] == UPLOAD_ERR_OK) {
						// Neste caso, guardamos o nome do arquivo no banco
						$form->campoSetarValor('T140_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "dados_usuarios/{$confUsuario['dbDatabase']}/certificados";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
									}
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
		
		// acrescenta o div com o grid
		$gridId = uniqid();		
		echo '<div id="'.$gridId.'"></div>';		
		 
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($gridId, '/cad/cad002/grid/certificados/', "&acaoId=" . urlencode($r_D001_Id). '&D024_Id=' . urlencode($r_D024_Id)),
		);
		echo  gProcessaAcoes($actions);
		
		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'cad020substituicao':
		//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA!
		
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
		$form_tabela = 'D009';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002AcaoJuntarMarcas';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/juntarMarcas/';
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					'' => array('name' => '', 'label'=>'Incluir', 'type' => 'text','length' => array(40,40)),
					'content_form2_btn' => array('name' => 'content_form2_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'style'=> 'float:right;','extra'=>'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true),
                ),
				
            ) // 'campos'
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
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$msg = $CAD002->juntarMarcas($form->campoValorEnviado('D049_Antigo_Id'), $form->campoValorEnviado('D049_Novo_Id'));
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
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
		$g['smarty']->display('form.tpl');
		
		
		// acrescenta o div com o grid
		$gridId = uniqid();		
		echo '<div id="'.$gridId.'"></div>';		
		 
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($gridId, '/cad/cad002/grid/substituicao/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002AcaoJuntarMarcas">Executar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		
	break;
	
	case 'form2Comum':
		log(time());
		global $g;
		gHabilitaNavegacao($g['divId'], 'CAD002', 'form2Comum');
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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form2_btnEditarProduto';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad020/form/form2Comum/';
		// Form Editor
		$form_editor = array(
			'geral' => '{"D001_Codigo_Produto":{"top":7,"left":40,"width":72,"hidden":0},"D001_Codigo_Barras":{"top":7,"left":237,"width":72,"hidden":0},"D001_Aplicacao":{"top":7,"left":357,"width":104,"hidden":0},"D049_D082_Id":{"top":7,"left":612,"width":255,"hidden":0},"D001_D015_Id":{"top":7,"left":1066,"width":182,"hidden":1},"D001_Descricao_Produto":{"top":30,"left":65,"width":392,"hidden":0},"D049_Flag_Tipo":{"top":30,"left":622,"width":256,"hidden":0},"D001_Flag_Tipo_Item":{"top":30,"left":949,"width":400,"hidden":1},"D009_Aliquota_ICMS_Tabela":{"top":30,"left":1056,"width":75,"hidden":1},"D001_C008_Id":{"top":53,"left":86,"width":399,"hidden":0},"D001_D037_Id":{"top":53,"left":597,"width":255,"hidden":0},"D049_Modelo":{"top":53,"left":1080,"width":174,"hidden":1},"D001_D060_Id":{"top":76,"left":89,"width":399,"hidden":0},"D049_IPV":{"top":76,"left":1169,"width":78,"hidden":1},"D049_Flag_Ativo":{"top":76,"left":1281,"width":80,"hidden":1},"D001_D003_Id":{"top":99,"left":91,"width":399,"hidden":0},"D009_Quantidade_Estoque":{"top":99,"left":610,"width":72,"hidden":0},"Pedido":{"top":99,"left":787,"width":72,"hidden":0},"D049_IPV2":{"top":99,"left":1089,"width":78,"hidden":1},"D001_D002_Id":{"top":122,"left":68,"width":399,"hidden":0},"D009_Quantidade_Estoque_Liquido":{"top":122,"left":616,"width":72,"hidden":0},"Separado":{"top":122,"left":779,"width":72,"hidden":0},"D049_Flag_Substituicao_Tributaria":{"top":122,"left":991,"width":80,"hidden":1},"D049_Observacao_Compra":{"top":122,"left":1238,"height":15,"width":88,"hidden":1},"D001_D005_Id":{"top":145,"left":90,"width":399,"hidden":0},"D009_Quantidade_Estoque_Loja":{"top":145,"left":630,"width":72,"hidden":0},"D009_Quantidade_OC":{"top":145,"left":809,"width":72,"hidden":0},"D009_Data_Alteracao_Custo":{"top":145,"left":977,"width":72,"hidden":1},"D049_Codigo_Produto_Fornecedor":{"top":145,"left":1154,"width":174,"hidden":1},"D049_Origem_Mercadoria":{"top":168,"left":25,"width":400,"hidden":0},"D009_Quantidade_Estoque_Fora":{"top":168,"left":627,"width":72,"hidden":0},"D009_Percentual_Desconto_Tabela":{"top":168,"left":1034,"width":75,"hidden":1},"D049_Percentual_Acrescimo_Tabela":{"top":191,"left":1017,"width":72,"hidden":1},"D009_Valor_Custo_Tabela":{"top":214,"left":589,"width":72,"hidden":1},"D001_Especificacoes":{"top":213.953125,"left":48,"height":83,"width":392,"hidden":0},"D009_Data_Atualizacao_Custo_Fornecedor":{"top":214,"left":754,"width":72,"hidden":1},"D009_Data_Ultima_Entrada":{"top":236,"left":754,"width":72,"hidden":1},"D009_Valor_Custo_Ultima_Compra":{"top":237,"left":550,"width":72,"hidden":1},"D049_Flag_Nacional_Importado":{"top":236.953125,"left":1023,"width":120,"hidden":1},"D009_ICF_1":{"top":259,"left":808,"width":72,"hidden":1},"D009_Valor_Custo_Medio_Unitario":{"top":260,"left":589,"width":72,"hidden":1},"D009_Valor_Preco_Tabela":{"top":259.953125,"left":1070,"width":72,"hidden":1},"D001_Qte_Embalagem":{"top":259.953125,"left":1268,"width":56,"hidden":1},"infoCustoVendaProduto":{"top":282,"left":683,"width":null,"hidden":1},"D009_Valor_Custo_Unitario":{"top":282,"left":767,"width":72,"hidden":1},"D009_ICF_3":{"top":282.953125,"left":1090,"width":75,"hidden":1},"D009_ICF_2":{"top":305.953125,"left":1090,"width":75,"hidden":1},"D001_Peso_Total_Kg":{"top":305.953125,"left":1295,"width":56,"hidden":1},"D001_Peso_Unitario_Kg":{"top":329,"left":41,"width":88,"hidden":0},"D001_Flag_Ativo":{"top":329,"left":404,"width":64,"hidden":0},"D009_IPV_1":{"top":329,"left":624,"width":72,"hidden":1},"D009_Preco_Loja":{"top":329,"left":774,"width":72,"hidden":1},"D009_Valor_Preco_Importado":{"top":328.953125,"left":1036,"width":72,"hidden":1},"D049_Data_Cadastro":{"top":352,"left":51,"width":88,"hidden":0},"D001_Flag_Pre_Cadastro":{"top":352,"left":387,"width":64,"hidden":0},"D009_IPV_2":{"top":352,"left":624,"width":72,"hidden":1},"D009_Preco_Loja_Real":{"top":352,"left":749,"width":72,"hidden":1},"D009_Valor_Custo_Compra":{"top":351.921875,"left":1053,"width":72,"hidden":1},"D009_Flag_Usar_Custo":{"top":351.953125,"left":1246,"width":256,"hidden":1},"D001_Flag_Multimarcas":{"top":375,"left":405,"width":64,"hidden":0},"D009_IPV_3":{"top":375,"left":624,"width":72,"hidden":1},"D009_Data_Ultima_Venda":{"top":375,"left":763,"width":72,"hidden":1},"D001_Descricao_Ingles":{"top":374.921875,"left":1050,"width":392,"hidden":1},"D009_IPV_4":{"top":398,"left":624,"width":72,"hidden":1},"D001_Descricao_Comercial":{"top":397.953125,"left":1159,"width":168,"hidden":1},"cad002GridHistoricoPrecos":{"top":418.921875,"left":624,"width":null,"hidden":1},"D009_Preco_1":{"top":421,"left":756,"width":72,"hidden":1},"D009_Preco_2":{"top":421,"left":852,"width":72,"hidden":1},"D009_Preco_3":{"top":421,"left":964,"width":72,"hidden":1},"D009_D024_Id":{"top":420.921875,"left":1091,"width":2,"hidden":1},"D049_IPV1":{"top":443.96875,"left":1249,"width":78,"hidden":1},"D041_Valor_Base":{"top":466.921875,"left":21,"width":78,"hidden":1},"D001_Flag_Tipo":{"top":466.9375,"left":810,"width":400,"hidden":1},"D041_Valor_Preco_Consumidor":{"top":489.921875,"left":21,"width":78,"hidden":1},"D041_Valor_Preco_Revenda":{"top":512.921875,"left":21,"width":78,"hidden":1},"D041_Valor_Custo":{"top":535.921875,"left":19,"width":78,"hidden":1},"D009_Flag_Promocao":{"top":581.984375,"left":826,"width":13,"hidden":1}}'
		);
		
		$D009 = mysql_query("SELECT D009_Id, D009_Flag_Preco_Tabelado, D009_Flag_Promocao FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id WHERE D049_D001_Id='{$acaoId}' AND D009_C004_Id='{$g['empresaAtual']}'");
		$mD009 = mysql_fetch_array($D009);
		if($mD009['D009_Flag_Preco_Tabelado'] == 'S'){
			$checkedTabelado="checked";
		} else {
			$checkedTabelado="";
		}

		if($mD009['D009_Flag_Promocao'] == 'S'){
			$checked="checked";
		} else {
			$checked="";
		}

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form2Comum');
		$formArray = array(
			'campos' => array(
				'geral' => array(
		        	'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label'=>'Código Produto', 'type' => 'text','length' => array(30,20)),
					'D001_Flag_Pre_Cadastro' => array('name' => 'D001_Flag_Pre_Cadastro', 'label'=>'Pré-cadastro', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						),
						//'extra' => "onchange=\"if ($(this).val() == 'S') { $('#{$g['divId']} .itemForm-D001_Codigo_Barras').val('').attr('readonly', 'readonly'); } else { $('#{$g['divId']} .itemForm-D001_Codigo_Barras').removeAttr('readonly'); } \""
					),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'label'=>'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Codigo_Barras' => array('name' => 'D001_Codigo_Barras', 'label'=>'Código de barras', 'type' => 'text', 'length' => array(6, 6), 'extra' => 'onchange="if (!isempty($(this).val())) { $(this).val(strPad($(this).val(), 6, \'0\')); }"', 'readonly' => false),
		        	'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label'=>'Tipo', 'type' => 'select', 'select'=>
	                	gGeraSelect('C008', 'C008_Id', 'C008_Id,C008_Tipo', 'order by C008_Id', true)
	                ),
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'restricao' => false, 'type' => 'select',  'select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D001_D060_Id' => array('name' => 'D001_D060_Id', 'label'=>'Grupo', 'type' => 'select', 'select' => 
                		gGeraSelect('D060', 'D060_Id', 'D060_Id, D060_Descricao_Grupo', ' order by D060_Id ', true)
                	),					
					'D001_D002_Id' => array('name' => 'D001_D002_Id', 'label'=>'Sub-Grupo', 'restricao' => false, 'type' => 'select', 'select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto, D002_Codigo_Padrao', ' order by D002_Descricao_Produto', true)
					),					
					'D001_D015_Id' => array('name' => 'D001_D015_Id', 'label'=>'Aplicação', 'type' => 'select','select' =>
						gGeraSelect('D015', 'D015_Id', 'D015_Nome_Grupo, D015_Codigo_Padrao', 'order by D015_Nome_Grupo', true)
					),					
					'D001_Peso_Unitario_Kg' => array('name' => 'D001_Peso_Unitario_Kg', 'casasDecimais' => 3,'label'=>'Peso Unitário Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'casasDecimais' => 3,'label'=>'Qtde por embalagem ', 'type' => 'moeda', 'value' => '0,000', 'length' => array(15, 30), 'align' => 'right', 'style'=>'float:right;'),
					'D001_Peso_Total_Kg' => array('name' => 'D001_Peso_Total_Kg','casasDecimais' => 3, 'label'=>'Peso Total Emb. Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_D037_Id' => array('name' => 'D001_D037_Id', 'label'=>'Unidades', 'type' => 'select','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true)
					),
					'D001_D005_Id' => array('name' => 'D001_D005_Id', 'label'=>'NCM', 'type' => 'select', 'select' =>
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D005_Observacao, D149_Aliquota_IPI', "LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
                	),
					'D001_Especificacoes' => array('name' => 'D001_Especificacoes', 'label'=>'Especificações', 'type' => 'textarea', 'length' => array(3, 61) ),
					'D001_Flag_Tipo' => array('name' => 'D001_Flag_Tipo', 'label'=>'Tipo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'PRODUZIDO/IMPORTADO', 'value'=>'PRODUZIDO', 'selected'=>true),
							array('title' => 'ADQUIRIDO MERCADO NACIONAL', 'value' =>'COMPRADO')
						)
					),
					'D001_Flag_Ativo' => array('name' => 'D001_Flag_Ativo', 'label'=>'Item ativo', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_Aplicacao' => array('name' => 'D001_Aplicacao', 'label'=>'Referência', 'type' => 'text', 'length' => array(64, 40)),
					'D049_Id' => array('name' => 'D049_Id', 'type' => 'hidden', 'reject' => true),
					'D049_D082_Id' => array('name' => 'D049_D082_Id', 'label'=>'Marca', 'type' => 'select', 'select'=>
						//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
						gGeraSelect('D082', 'D082_Id', 'D082_Marca', 'order by D082_Marca', true), 'reject' => true,
					),
					'D049_Flag_Tipo' =>  array('name' => 'D049_Flag_Tipo', 'label' => 'Tipo', 'type'=>'select','align' => 'right', 'reject' => true, 'select' => 
					    array(
					       array('title' => '...', 'value' => ''),
					       array('title' => 'COMPRADO', 'value' => 'CP', 'selected' => true),
					       array('title' => 'PRODUZIDO', 'value' => 'PD'),
					       array('title' => 'MONTADO', 'value' => 'MT'),
					       array('title' => 'MODIFICADO', 'value' => 'MD'),
					    ),
					),
					'D001_Flag_Tipo_Item' => array('name' => 'D001_Flag_Tipo_Item', 'label' => 'Categoria', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Mercadoria para Revenda', 'value'=>'00', 'selected'=>true),
							array('title' => 'Matéria-Prima', 'value' =>'01'),
							array('title' => 'Embalagem', 'value' =>'02'),
							array('title' => 'Produto em Processo', 'value' =>'03'),
							array('title' => 'Produto Acabado', 'value' =>'04'),
							array('title' => 'Subproduto', 'value' =>'05'),
							array('title' => 'Produto Intermediário', 'value' =>'06'),
							array('title' => 'Material de Uso e Consumo', 'value' =>'07'),
							array('title' => 'Ativo Imobilizado', 'value' =>'08'),
							array('title' => 'Serviços', 'value' =>'09'),
							array('title' => 'Outros insumos', 'value' =>'10'),
							array('title' => 'Outras', 'value' =>'99')
						)						
					),
					'D009_D024_Id' => array('name' => 'D009_D024_Id','label'=>'Fornecedor', 'type' => 'pesquisa', 'reject'=>true, 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa',''), 'length' => array(80,30)),
					'D049_Data_Cadastro' => array('name' => 'D049_Data_Cadastro', 'label' => 'Data Cadastro', 'type'=>'data', 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_Data_Ultima_Entrada' => array('name' => 'D009_Data_Ultima_Entrada', 'label' => 'Data entrada', 'type'=>'data', 'length' => array(10,15), 'reject' => true, 'disabled' => true),
					'D009_Quantidade_Estoque_Liquido' => array('name' => 'D009_Quantidade_Estoque_Liquido', 'label' => 'Estoque Liquido', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_Estoque_Loja' => array('name' => 'D009_Quantidade_Estoque_Loja', 'label' => 'Loja', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_Estoque_Fora' => array('name' => 'D009_Quantidade_Estoque_Fora', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [700,400]); return false;\">Fora</a>", 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_Estoque' => array('name' => 'D009_Quantidade_Estoque', 'label' => 'Estoque', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_OC' => array('name' => 'D009_Quantidade_OC', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent('{$mD009['D009_Id']}'), [700,400]); return false;\">OC</a>", 'type' => 'moeda', 'reject' => true, 'disabled' => true),
					'Pedido' => array('name' => 'Pedido', 'label' => 'Pedidos', 'type'=>'moeda', 'value' => '0', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'Separado' => array('name' => 'Separado', 'label' => 'Separado', 'type'=>'texto', 'value' => '0', 'reject' => true, 'length' => array(10,15), 'disabled' => true),					
					'D049_Origem_Mercadoria' => array('name' => 'D049_Origem_Mercadoria', 'label' => 'Origem mecadoria', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7')
						)
					),
					'D001_Flag_Multimarcas' => array('name' => 'D001_Flag_Multimarcas', 'label'=>'Multimarcas', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'infoCustoVendaProduto' => array('name' => 'infoCustoVendaProduto', 'label' => '', 'type' => 'titulo', 'length' => array(5,20), 'reject' => true, 'value' => ''),
                ),
				
            ) // 'campos'
		);

		if($g['C031']['multimarcas'] == 'ambas'){
			$sql = mysql_query("SELECT * FROM D049 WHERE D049_D001_Id = '{$r_acaoId}'");
			if(mysql_num_rows($sql) == 1){
				$formArray['campos']['geral']['D001_Flag_Multimarcas'] = array('name' => 'D001_Flag_Multimarcas', 'label'=>'Multimarcas', 'type' => 'select', 'select' =>
					array(
						array('title' => 'Sim', 'value'=>'S'),
						array('title' => 'Não', 'value' =>'N')
					)
				);
			}
		}
		
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #D001_Flag_Multimarcas').change(function() {
					if (($(this).val() == 1)) {
						$.getJSON('/cad/cad002/form_func-ajax/flagMultimarcas/?ajax=true&D001_Id=' + encodeURIComponent({$acaoId}) + '&Flag=S&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$r_divIdMain}', true);
							}
						});
					}
				});
				$('#{$g['divId']} #D001_Flag_Multimarcas').trigger('change');
				$('#cad002GridHistoricoPrecos').unbind('click');
				$('#cad002GridHistoricoPrecos').bind('click', function(data) {
					var id = '{$mD009['D009_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Histórico Preços', '/cad/cad002/content/cad002contentHistóricoPrecos/', '&D009_Id=' + encodeURIComponent(id), [1000,553]);
					return false;
				});

				$('#{$g['divId']} .itemForm-D001_Flag_Pre_Cadastro').trigger('onchange');
			} </script></div>
EOT;
		
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
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					$retorno = $CAD002->verificarCodigoProduto($form,$acaoId);
					if($retorno !== true){
						echo gGeraAlertMsg('Erro!', $retorno);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#D001_Codigo_Produto').focus();
							} </script></div>
EOT;
					}else{
						$D001_D005_Id = $form->campoValorEnviado('D001_D005_Id');
						$D001_Flag_Pre_Cadastro = $form->campoValorEnviado('D001_Flag_Pre_Cadastro');
						if((!empty($D001_D005_Id) && $D001_Flag_Pre_Cadastro == 'N') || $D001_Flag_Pre_Cadastro == 'S'){
							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
								$CAD020->atualizarMarca($form, $acaoId);
								$retorno = $CAD002->D001_reprocessa_historico($r_acaoId);
								if ($retorno == TRUE)
								{
									echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
									echo  <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											//divRefresh('{$g['divId']}', true);
											//console.log('{$r_divIdContentTabProduto}');
											//divSetarAcaoId('{$r_divIdContentTabProduto}', 'true', 'modoFormLeve');
											divRefresh('{$r_divIdMain}', true);
										}</script></div>
EOT;
								}
								else
								{
									echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso! Problemas na atualizacao do estoque!');
								}
							}
						} else {
							echo gGeraAlertMsg('Erro no campo do formulário', '<b>NCM</b>: Não pode ser vazio.');
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									divRefresh("{$g['divId']}", true);
								} </script></div>
EOT;
						}
					}
				} else {
					// Insert
					$retorno = $CAD002->verificarCodigoProduto($form);
					if($retorno !== true){
						echo gGeraAlertMsg('Erro!', $retorno);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#D001_Codigo_Produto').focus();
							} </script></div>
EOT;
					}else{
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							$CAD002->incluirMarca($form);
							// Imprime uma mensagem e fecha a janela
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
							echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
						}
				 	}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				/**
				* Modifica campos em caso de update 
				*/
				$cClausula = '';
				if ($form->campoValor('D001_C008_Id') > 9999)
				{
                    $cClausula .= " and D003_C008_Id='" . $form->campoValor('D001_C008_Id') . "' ";
				}
                if ($form->campoValor('C008_Inclusao_Produto_Mostrar_Codigo_Grupos') == 'S')
                {
		   		    $cClausula .= " order by D003_Codigo_Padrao ";
                }
                else
                {
		   		    $cClausula .= " order by D003_Nome_Linha ";
                }
				$form->campoSetarSelect('D001_D003_Id', gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', $cClausula, true));
				
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				$sql = mysql_query("SELECT *, D009_Quantidade_Pedido(D009_Id,1) as Pedido,
											  D009_Quantidade_Separado_3(D009_Id) as Separado
				                      FROM D049 
				                 LEFT JOIN D009 ON D009_D049_Id=D049_Id
				                 LEFT JOIN D041 ON D041_D049_Id=D049_Id     
				                 LEFT JOIN D024 ON D024_Id=D009_D024_Id
				                     WHERE D049_D001_Id = '{$acaoId}' AND D009_C004_Id = '{$g['empresaAtual']}'");
				$res = mysql_fetch_array($sql);

				$form->campoSetarValor('D049_Id', $res['D049_Id']);
				$form->campoSetarValor('D049_D082_Id', $res['D049_D082_Id']);
				$form->campoSetarValor('D049_Flag_Tipo', $res['D049_Flag_Tipo']);
				$form->campoSetarValor('D049_Origem_Mercadoria', $res['D049_Origem_Mercadoria']);
				$form->campoSetarValor('D049_Data_Cadastro', gCorrigeData($res['D049_Data_Cadastro']));
				$form->campoSetarValor('D009_Quantidade_Estoque', gCorrigeNumero($res['D009_Quantidade_Estoque']));
				$form->campoSetarValor('D009_Quantidade_OC', gCorrigeNumero($res['D009_Quantidade_OC']));
				$form->campoSetarValor('D009_Quantidade_Estoque_Liquido', gCorrigeNumero($res['D009_Quantidade_Estoque_Liquido']));
				$form->campoSetarValor('D009_Quantidade_Estoque_Loja', gCorrigeNumero($res['D009_Quantidade_Estoque_Loja']));
				$form->campoSetarValor('D009_Quantidade_Estoque_Fora', gCorrigeNumero($res['D009_Quantidade_Estoque_Fora']));
				$form->campoSetarValor('D009_Data_Ultima_Entrada', gCorrigeData($res['D009_Data_Ultima_Entrada']));
				$form->campoSetarValor('D009_Data_Atualizacao_Custo_Fornecedor', gCorrigeData($res['D009_Data_Atualizacao_Custo_Fornecedor']));
				$form->campoSetarValor('D009_D024_Id', $res['D009_D024_Id']);
    			$form->formArray['campos']['geral']['D009_D024_Id']['value_Pesquisa'] = $res['D024_Nome_Empresa'];
				$form->campoSetarValor('Separado', gCorrigeNumero($res['Separado']));
				$form->campoSetarValor('Pedido', gCorrigeNumero($res['Pedido']));
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
		echo $g['smarty']->fetch('formEditor.tpl');
		
		$id = 'popover-' . uniqid();
		$id2 = 'popover-' . uniqid();
		$id3 = 'popover-' . uniqid();
		$id4 = 'popover-' . uniqid();
		$id5 = 'popover-' . uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('.formEditor-D009_Valor_Custo_Ultima_Compra').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Ultima_Compra').unbind('click').bind('click', function(data) {
					console.log($(this));
					$('.formEditor-D009_Valor_Custo_Ultima_Compra').attr('id', '{$id}');
					if ($('#{$id}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCusto/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&acaoId=' + encodeURIComponent('{$res['D009_T001_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id}').popover('destroy');
								$('#{$id}').popover({
									html: true,
									placement: 'left',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id}').popover('show');
								$('#{$id}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								var left = $('.popover').position().left - 85;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});
				$('.formEditor-D009_Valor_Custo_Medio_Unitario').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Medio_Unitario').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Valor_Custo_Medio_Unitario').attr('id', '{$id2}');
					if ($('#{$id2}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCusto/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&acaoId=' + encodeURIComponent('{$res['D009_T001_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id2}').popover('destroy');
								$('#{$id2}').popover({
									html: true,
									placement: 'left',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id2}').popover('show');
								$('#{$id2}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id2}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								var left = $('.popover').position().left - 85;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});
				
				$('.formEditor-D009_Valor_Custo_Tabela').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Tabela').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Valor_Custo_Tabela').attr('id', '{$id3}');
					if ($('#{$id3}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCustoTabela/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&D009_Id=' + encodeURIComponent('{$res['D009_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id3}').popover('destroy');
								$('#{$id3}').popover({
									html: true,
									placement: 'left',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id3}').popover('show');
								$('#{$id3}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id3}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								var left = $('.popover').position().left - 85;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});

/*				$('.formEditor-D009_Preco_Loja').css('cursor', 'pointer');
				$('.formEditor-D009_Preco_Loja').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Preco_Loja').attr('id', '{$id4}');
					if ($('#{$id4}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/ven/ven001/outros/demonstrativoPrecoVenda/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&extra=D009&acaoId=' + encodeURIComponent('{$res['D009_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id4}').popover('destroy');
								$('#{$id4}').popover({
									html: true,
									placement: 'top',
									title: 'Demonstrativo Preço',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id4}').popover('show');
								$('#{$id4}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id4}').next().find('input:visible:last').focus();
								$('.popover').css('width', 560);
								var left = $('.popover').position().left - 34;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 12);
							}
						});
					}
				});  

*/

				$('.formEditor-D009_Preco_Loja_Real').css('cursor', 'pointer');
				$('.formEditor-D009_Preco_Loja_Real').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Preco_Loja_Real').attr('id', '{$id5}');
					if ($('#{$id5}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/ven/ven001/outros/demonstrativoPrecoVenda/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&extra=D009_Real&acaoId=' + encodeURIComponent('{$res['D009_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id5}').popover('destroy');
								$('#{$id5}').popover({
									html: true,
									placement: 'left',
									title: 'Demonstrativo Preço',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id5}').popover('show');
								$('#{$id5}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id5}').next().find('input:visible:last').focus();
								$('.popover').css('width', 450);
								var left = $('.popover').position().left - 215;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 12);
							}
						});
					}
				});
				$.getJSON('/cad/cad002/form_func-ajax/infoCustoVendaProduto/?ajax=true&D009_Data_Alteracao_Custo=' + encodeURIComponent('{$res['D009_Data_Alteracao_Custo']}') + '&D009_Data_Ultima_Venda=' + encodeURIComponent('{$res['D009_Data_Ultima_Venda']}') + '&D009_Flag_Custo_Utilizado=' + encodeURIComponent('{$res['D009_Flag_Custo_Utilizado']}') + '&callback=?', function(request) {
					if (request.code) {
						//$('.formEditor-infoCustoVendaProduto').html(request.data);
					}
				});
			} </script></div>
EOT;
	break;	
}
