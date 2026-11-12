<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */
 
/**Classe
* 
* require_once("bibliotecas/classes/fis005.php");
* $FIS005 = new FIS005();
*/
require_once("bibliotecas/classes/FIS008.php");
$FIS008 = new FIS008();

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'fis008FormExportarTXTNFe':
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
		$enviar_btnId = 'fis008ExportarTXTNFe_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis008/form/fis008FormExportarTXTNFe/';

		// novo formulário
		$form = new Formulario('fis008FormExportarTXTNFe');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Data_Inicio' => array('name' => 'Data_Inicio','label'=>'* Data Inicial', 'type' => 'data', 'length' => array(15,60)),
					'Data_Fim' => array('name' => 'Data_Fim','label'=>'* Data Final', 'type' => 'data', 'length' => array(15,60)),
					'C004_Id' => array('name' => 'C004_Id','label'=>'* Empresa', 'type' => 'select',
						'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado', "where C004_Flag_Ativo='S' ORDER BY C004_Nome_Abreviado", true)
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
				$retorno = $FIS008->exportarTXTNfe($form);
				if (!is_array($retorno)) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					$retorno = $retorno[0];
					//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								window.location = '/fis/fis008/form_func-ajax/fis008abrirTXT/?ajax=true&path=' + encodeURIComponent('$retorno');
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$botoesDir .= '<button type="submit" id="fis008ExportarTXTNFe_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>