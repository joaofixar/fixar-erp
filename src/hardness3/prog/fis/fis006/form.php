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
require_once("bibliotecas/classes/FIS006.php");
$FIS006 = new FIS006();

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'fis006FormExportarSintegra':
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
		$enviar_btnId = 'fis006ExportarSintegra_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis006/form/fis006FormExportarSintegra/';

		// novo formulário
		$form = new Formulario('fis006FormExportarSintegra');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Data_Inicio' => array('name' => 'Data_Inicio','label'=>'* Data Inicial', 'type' => 'data', 'length' => array(15,60)),
					'Data_Fim' => array('name' => 'Data_Fim','label'=>'* Data Final', 'type' => 'data', 'length' => array(15,60)),
					'Finalidade' => array('name' => 'Finalidade', 'label' => 'Finalidade', 'type' => 'select', 'select' => 
						array(
							array('title' => '1 - Normal', 'value' => '1'),
							array('title' => '2 - Retificação Total', 'value' => '2'),
							array('title' => '3 - Retificação Aditiva', 'value' => '3'),
							array('title' => '4 - Retificação Corretiva', 'value' => '4'),
							array('title' => '5 - Desfazimento', 'value' => '5'),
						)
					),
					'Registro' => array('name' => 'Registro', 'label' => 'Registro', 'type' => 'select', 'select' => 
						array(
							array('title' => '0 - Todos', 'value' => '0'),
							array('title' => '1 - Notas Fiscais', 'value' => '1'),
							array('title' => '2 - ECF - Cupom Fiscal', 'value' => '2'),
							array('title' => '3 - Conhecimentos', 'value' => '3'),
						)
					),
					'Inventario' => array('name' => 'Inventario', 'label' => 'Gerar Inventário?', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'Data_Inventario' => array('name' => 'Data_Inventario','label'=>'* Data Inventário', 'type' => 'data', 'length' => array(15,60)),
					'uploadArquivoTXT' => array('name' => 'uploadArquivoTXT', 'label' => 'Arquivo ECF', 'type' => 'file', 'length' => array(24, 24), 'reject' => true),
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
				$arquivo = $form->campoValorEnviado('uploadArquivoTXT');
				$Data_Inicio = gCorrigeDataInverte($form->campoValorEnviado('Data_Inicio'));
				$Data_Fim = gCorrigeDataInverte($form->campoValorEnviado('Data_Fim'));
				if (!empty($arquivo['name']) && $arquivo['error'] == UPLOAD_ERR_OK) {
					$dir = $g['pathDados'] . '/arquivosECF/';
					is_dir($dir) or mkdir($dir, 0777, true);
					$arquivoSalvo = 'ECF-' . $arquivo['name'];
					file_put_contents($dir . $arquivoSalvo, file_get_contents($arquivo['tmp_name']));

					$arrayArquivo = file($arquivo['tmp_name']);
					$dataInicialArquivo = substr($arrayArquivo[0],107,4).'-'.substr($arrayArquivo[0],111,2).'-'.substr($arrayArquivo[0],113,2);
					$dataFinalArquivo = substr($arrayArquivo[0],115,4).'-'.substr($arrayArquivo[0],119,2).'-'.substr($arrayArquivo[0],121,2);
					
					if($dataInicialArquivo != $Data_Inicio || $dataFinalArquivo != $Data_Fim){
						echo gGeraAlertMsg('Erro', 'Arquivo selecionado não é referente ao período informado!');
					} else {
						$retorno = $FIS006->exportarSintegra($form, $arquivo);
						if (!is_array($retorno)) {
							echo gGeraAlertMsg('Erro', $retorno);	
						} else {
							$retorno = $retorno[0];
							//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
							echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										window.location = '/fis/fis006/form_func-ajax/fis006abrirSintegra/?ajax=true&path=' + encodeURIComponent('$retorno');
										divRefresh('{$g['divId']}', true);
									} </script></div>
EOT;
						}
					}
				} else {
					$retorno = $FIS006->exportarSintegra($form);
					if (!is_array($retorno)) {
						echo gGeraAlertMsg('Erro', $retorno);	
					} else {
						$retorno = $retorno[0];
						//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									window.location = '/fis/fis006/form_func-ajax/fis006abrirSintegra/?ajax=true&path=' + encodeURIComponent('$retorno');
									divRefresh('{$g['divId']}', true);
								} </script></div>
EOT;
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
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="fis006ExportarSintegra_btn">Enviar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
