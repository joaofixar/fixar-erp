<?php
namespace hardness;
/**Classes
*
*/
require_once("bibliotecas/classes/EST018.php");
$EST018 = new EST018();
/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'est018FormExportarXmlConhecimentos':
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
		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est018ExportarXMLConhecimentos_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est018/form/est018FormExportarXmlConhecimentos/';

		// novo formulário
		$form = new Formulario('est018FormExportarXmlConhecimentos');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)

		$form_editor = array(
			'geral' => '{"Campo_Data":{"top":7,"left":8.6875,"width":111,"hidden":0},"Data_Inicio":{"top":30,"left":28.828125,"width":104,"hidden":0},"Data_Fim":{"top":53,"left":34.171875,"width":104,"hidden":0},"T084_C004_Id":{"top":76,"left":39.96875,"width":224,"hidden":0},"T084_D022_Id":{"top":99,"left":15.234375,"width":202,"hidden":0}}'
		);

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Data_Inicio' => array('name' => 'Data_Inicio','label'=>'* Data Inicial', 'type' => 'data', 'length' => array(15,60)),
					'Data_Fim' => array('name' => 'Data_Fim','label'=>'* Data Final', 'type' => 'data', 'length' => array(15,60)),
					'T084_C004_Id' => array('name' => 'T084_C004_Id','label'=>'* Empresa', 'type' => 'select',
						'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado', 'ORDER BY C004_Nome_Abreviado', true)
					),
					//'T084_D024_Id' => array('name' => 'T084_D024_Id', 'label' => 'Fornecedor/Cliente', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Cnpj,D024_Id','WHERE D024_Flag_Ativo="S"'), 'length' => array(50,30)),
					'T084_D022_Id' => array('name' => 'T084_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022', 'D022_Id', 'D022_Cnpj,D022_Nome_Empresa,D022_Id'), 'length' => array(50,30)),
					'Campo_Data' => array('name' => 'Campo_Data','label'=>'Buscar pela data', 'type' => 'select', 'select' => 
						array(
							  array('title' => 'Emissão', 'value' => '1', 'selected' => true),
							  array('title' => 'Lançamento', 'value' => '2')
							 )
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
				$retorno = $EST018->exportarXMLconhecimentos($form);
				if (!is_array($retorno)) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					$retorno = $retorno[0];
					//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								window.location = '{$retorno}';
								divRefresh('{$g['divId']}', true);
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$botoesDir .= '<button type="submit" id="est018ExportarXMLConhecimentos_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est018FormExportarXmlConhecimentosEmitidos':
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
	$form_tabela = 'T084';
	$form_campo_pk = 'T084_Id';
	// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
	$enviar_btnId = 'est018ExportarXMLConhecimentosEmitidos_btn';
	// Form que será executado ao clicar no botão
	$enviar_acaoHref = '/est/est018/form/est018FormExportarXmlConhecimentosEmitidos/';

	// novo formulário
	$form = new Formulario('est018FormExportarXmlConhecimentosEmitidos');
	// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)

	$form_editor = array(
		'geral' => '{"Campo_Data":{"top":7,"left":8.6875,"width":111,"hidden":0},"Data_Inicio":{"top":30,"left":28.828125,"width":104,"hidden":0},"Data_Fim":{"top":53,"left":34.171875,"width":104,"hidden":0},"T084_C004_Id":{"top":76,"left":39.96875,"width":224,"hidden":0},"T084_D022_Id":{"top":99,"left":15.234375,"width":202,"hidden":0}}'
	);

	$formArray = array(
		'campos' => array(
			'geral' => array(
				'Data_Inicio' => array('name' => 'Data_Inicio','label'=>'* Data Inicial', 'type' => 'data', 'length' => array(15,60)),
				'Data_Fim' => array('name' => 'Data_Fim','label'=>'* Data Final', 'type' => 'data', 'length' => array(15,60)),
				'T084_C004_Id' => array('name' => 'T084_C004_Id','label'=>'* Empresa', 'type' => 'select',
					'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado', 'ORDER BY C004_Nome_Abreviado', true)
				),
				//'T084_D024_Id' => array('name' => 'T084_D024_Id', 'label' => 'Fornecedor/Cliente', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Cnpj,D024_Id','WHERE D024_Flag_Ativo="S"'), 'length' => array(50,30)),
				//'T084_D022_Id' => array('name' => 'T084_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022', 'D022_Id', 'D022_Cnpj,D022_Nome_Empresa,D022_Id'), 'length' => array(50,30)),
				'Campo_Data' => array('name' => 'Campo_Data','label'=>'Buscar pela data', 'type' => 'select', 'select' => 
					array(
						  array('title' => 'Emissão', 'value' => '1', 'selected' => true),
						  array('title' => 'Lançamento', 'value' => '2')
						 )
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
			$retorno = $EST018->exportarXMLConhecimentosEmitidos($form);
			if (!is_array($retorno)) {
				echo gGeraAlertMsg('Erro', $retorno);	
			} else {
				$retorno = $retorno[0];
				//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
				echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							window.location = '{$retorno}';
							divRefresh('{$g['divId']}', true);
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
	$g['smarty']->display('formEditor.tpl');

	/**
	 * Botões
	 */
	// Definição dos botões
	$botoesDir = '';
	$g['smarty']->assign('botoesId', $g['divId']);
	$botoesDir .= '<button type="submit" id="est018ExportarXMLConhecimentosEmitidos_btn">Enviar</button>';
	$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
	$g['smarty']->assign('botoesDir', $botoesDir);
	$g['smarty']->display('buttons-floatBottom.tpl');
break;
}