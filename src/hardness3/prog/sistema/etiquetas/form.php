<?php
namespace hardness;

require_once('prog/cad/cad020/grid_func-js.php');
	
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'sistemaEtiquetasQtdProduto':
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
			$formId = (isset($r_formcad001FormCadastro)) ? $r_formcad001FormCadastro['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D000';
		$form_campo_pk = 'D000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'noBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/etiquetas/form/sistemaEtiquetasQtdProduto/';

		// novo formulário
		$form = new Formulario('sistemaEtiquetasQtdProdutoForm');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tipoEtiqueta' => array('name' => 'tipoEtiqueta', 'label' => 'Tipo', 'type' => 'select', 'select' =>
					array( 
						array('title' => 'Caixa', 'value' => 'C'),
						array('title' => 'Pacote', 'value' => 'P')
					)),
					'impEtiquetas' => array('name' => 'impEtiquetas', 'label' => 'Etiquetas', 'type' => 'text', 'length' => array(25,255), 'value' => '1'),
					'impQuantidade' => array('name' => 'impQuantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(25,255), 'value' => '0,00')
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
				echo <<<EOT
					// ...
EOT;
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
	
	case 'etiquetasGenericaXX000':
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
			$formId = (isset($r_formcad001FormCadastro)) ? $r_formcad001FormCadastro['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D000';
		$form_campo_pk = 'D000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'noBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/etiquetas/form/etiquetasGenericaXX000/';

		// novo formulário
		$form = new Formulario('formetiquetasGenericaXX000');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					/*'tipoEtiqueta' => array('name' => 'tipoEtiqueta', 'label' => 'Tipo', 'type' => 'select', 'select' =>
					array( 
						array('title' => 'Caixa', 'value' => 'C'),
						array('title' => 'Pacote', 'value' => 'P')
					)),*/
					'impLetras' => array('name' => 'impLetras', 'label' => 'Letras', 'type' => 'text', 'length' => array(2,2), 'value' => 'PC'),
					'addZeros' => array('name' => 'addZeros', 'label' => 'Adicionar 0\'s', 'type' => 'select', 'select' => array(array('title' => 'S', 'value' => 1), array('title' => 'N', 'value' => 2))),
					'impInicio' => array('name' => 'impInicio', 'label' => 'Nr. Início', 'type' => 'numero', 'length' => array(25,255), 'value' => '1'),
					'impFim' => array('name' => 'impFim', 'label' => 'Nr. Fim', 'type' => 'numero', 'length' => array(25,255), 'value' => '10'),
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
				echo <<<EOT
					// ...
EOT;
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
	
	case 'formSistemaSelecionarModelo':
			/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$id = (isset($r_D00X_Id)) ? $r_D00X_Id : false;
		$D138_Id = (isset($D138_Id)) ? $D138_Id : false;

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
		$form_tabela = 'D138';
		$form_campo_pk = 'D138_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etiquetasImprimit_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/etiquetas/form/formSistemaSelecionarModelo/';

		// novo formulário
		$form = new Formulario('formSistemaSelecionarModelo');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D138_Id' => array('name' => 'D138_Id', 'label'=>'Modelo','type' => 'select', 'extra'=>'style="width:200px;"','select' => 
						gGeraSelect('D138','D138_Id','D138_Modelo',' WHERE D138_Flag_Tipo="P" AND D138_Flag_Mostrar="S"',true),
					),
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
				$D138_Id = $form->campoValorEnviado('D138_Id');
				$etiquetasProdutoPedirQtd = $g['conf']->ini('etiquetasProdutoPedirQtd');
	
				if (!$etiquetasProdutoPedirQtd) {
					
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		  					cad020ImpTag('{$id}', '{$g['divId']}', false, false, false, '{$D138_Id}');
						} </script></div>
EOT;
				} else {
					
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		  					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Informe a Quantidade', '/sistema/etiquetas/content/sistemaEtiquetasQtdProduto/','&D00X_Id={$id}&D138_Id={$D138_Id}', [300,200]);
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
		$gerado = gGeraCamposForm($form, $tab, $id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

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
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="etiquetasImprimit_btn">Imprimir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		

	break;
	
	
}
?>
