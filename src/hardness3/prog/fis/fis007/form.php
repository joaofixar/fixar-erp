<?php
namespace hardness;
/**Classe
* 
* require_once("bibliotecas/classes/fis005.php");
* $FIS005 = new FIS005();
*/
require_once("bibliotecas/classes/FIS007.php");
$FIS007 = new FIS007();

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'fis007FormExportarXMLNFe':
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
		$form_tabela = 'D020';
		$form_campo_pk = 'D020_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis007ExportarXMLNFe_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis007/form/fis007FormExportarXMLNFe/';

		// novo formulário
		$form = new Formulario('fis007FormCadastroCidade');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)

		$form_editor = array(
			'geral' => '{"Data_Inicio":{"top":7,"left":14,"width":72,"hidden":0},"Data_Fim":{"top":30,"left":18,"width":72,"hidden":0},"C004_Id":{"top":53,"left":23,"width":127,"hidden":0},"D018_Id":{"top":76,"left":59,"width":47,"hidden":0},"IncluirCanceladas":{"top":122,"left":220,"width":13,"hidden":0},"D024_Id":{"top":99,"left":42,"width":249,"hidden":0},"SepararCFOP":{"top":122,"left":8,"width":41,"hidden":0}}'
		);

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Data_Inicio' => array('name' => 'Data_Inicio','label'=>'* Data Inicial', 'type' => 'data', 'length' => array(15,60)),
					'Data_Fim' => array('name' => 'Data_Fim','label'=>'* Data Final', 'type' => 'data', 'length' => array(15,60)),
					'C004_Id' => array('name' => 'C004_Id','label'=>'* Empresa', 'type' => 'select',
						'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado', 'ORDER BY C004_Nome_Abreviado', true)
					),
					'IncluirCanceladas' => array('name' => 'IncluirCanceladas','label'=>'Incluir as NF <br>canceladas no arquivo', 'type' => 'checkbox', 'length' => array(30,60)),
					'SepararCFOP' => array('name' => 'SepararCFOP','label'=>'Separar <br>por CFOP', 'type' => 'checkbox', 'length' => array(30,60)),
					'D024_Id' => array('name' => 'D024_Id', 'label' => 'Cliente', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%4/%5] - %6 Tel:(%8)%9'), 'length' => array(45,100)),
					'D018_Id' => array('name' => 'D018_Id', 'label' => 'UF', 'type' => 'select',
						'select' => gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
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
				$retorno = $FIS007->exportarXMLNfe($form);
				if (!is_array($retorno)) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					$arquivo = $retorno[0];
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								window.location = '{$arquivo}';
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					if (!empty($retorno[1])) {
						echo gGeraAlertMsg('Alerta', $retorno[1]);	
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$botoesDir .= '<button type="submit" id="fis007ExportarXMLNFe_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'fis007FormExportarXMLNFeEntrada':
		global $g;	
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

		//form editor
        $form_editor = array(
			'geral' => '{"dataInicial":{"top":7,"left":20.31939697265625,"width":88,"hidden":0},"dataFinal":{"top":30,"left":25.63885498046875,"width":88,"hidden":0},"empresa":{"top":53,"left":31.44439697265625,"width":208,"hidden":0},"SepararCFOP":{"top":76,"left":9.31939697265625,"width":42,"hidden":0},"fis007FormExportarXMLNFeEntrada_btn":{"top":97,"left":304,"width":null,"hidden":0}}',
        );

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis007FormExportarXMLNFeEntrada_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis007/form/fis007FormExportarXMLNFeEntrada/';		
		// novo formulário
		$form = new Formulario('fis007FormExportarXMLNFeEntrada');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'dataInicial' => array('name' => 'dataInicial','label'=>'Data Inicial', 'type' => 'data', 'reject' => true),
					'dataFinal' => array('name' => 'dataFinal','label'=>'Data Final', 'type' => 'data', 'reject' => true),					
					'empresa' => array('name' => 'empresa','label'=>'Empresa', 'type' => 'select', 'reject' => true, 
						'select' => gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado', '', '', $g['empresaAtual']),),
					'SepararCFOP' => array('name' => 'SepararCFOP','label'=>'Separar <br>por CFOP', 'type' => 'checkbox', 'length' => array(30,60)),
				)
			)
		);
		$formArray['campos']['geral']['fis007FormExportarXMLNFeEntrada_btn'] = array('name' => 'fis007FormExportarXMLNFeEntrada_btn', 'type'=>'button', 'reload' => false, 'value' => 'Gerar', 'reject' => true);
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if($formCodigo == 2) {
				$retorno = $FIS007->exportarXMLNfeEntrada($form);
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
	break;
}
?>
