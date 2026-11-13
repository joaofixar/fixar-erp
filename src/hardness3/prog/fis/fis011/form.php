<?php
namespace hardness;

require_once("bibliotecas/classes/FIS011.php");
$FIS011 = new FIS011();

/**
 * Ações
 */
switch ($g['r'][3]) {	
	case 'fis011FormSpedContribuicoes':
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
			'geral' => '{"dataInicial":{"top":7,"left":34,"width":74,"hidden":0},"dataFinal":{"top":30,"left":34,"width":74,"hidden":0},"tipoEscrituracao":{"top":53,"left":2,"width":127,"hidden":0},"numeroRecibo":{"top":76,"left":40,"width":120,"hidden":0},"situacaoEspecial":{"top":99,"left":1,"width":127,"hidden":0},"C007_Id_Contabilista":{"top":122,"left":45,"width":127,"hidden":0},"matriz":{"top":145,"left":58,"width":159,"hidden":0},"filiais":{"top":168,"left":60,"width":152,"hidden":1},"fis011FormSpedContribuicoes_btn":{"top":212,"left":80,"width":null,"hidden":0}}',
        );

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis011FormSpedContribuicoes_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis011/form/fis011FormSpedContribuicoes/';		
		// novo formulário
		$form = new Formulario('fis011FormSpedContribuicoes');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'dataInicial' => array('name' => 'dataInicial','label'=>'Data Inicial', 'type' => 'data', 'reject' => true),
					'dataFinal' => array('name' => 'dataFinal','label'=>'Data Inicial', 'type' => 'data', 'reject' => true),					
					'tipoEscrituracao' => array('name' => 'tipoEscrituracao','label'=>'Tipo Escrituração', 'type' => 'select', 'reject' => true, 'select' => array(
							array('title' => 'Original', 'value' => '0', 'selected' => true),
							array('title' => 'Retificadora', 'value' => '1'),
						)
					),
					'numeroRecibo' => array('name' => 'numeroRecibo','label'=>'Nº Recibo', 'type' => 'text', 'reject' => true),
					'situacaoEspecial' => array('name' => 'situacaoEspecial','label'=>'Situação Especial', 'type' => 'select', 'reject' => true, 'select' => array(
							array('title' => 'Nenhuma', 'value' => '', 'selected' => true),
							array('title' => 'Abertura', 'value' => '0'),
							array('title' => 'Cisão', 'value' => '1'),
							array('title' => 'Fusão', 'value' => '2'),
							array('title' => 'Incorporação', 'value' => '3'),
							array('title' => 'Encerramento', 'value' => '4')
						)
					),
					'C007_Id_Contabilista' => array('name' => 'C007_Id_Contabilista', 'label' => 'Contador', 'type' => 'select', 'select' => 
						gGeraSelect('C007', 'C007_Id', 'C007_Nome', "", true)
					),
					'matriz' => array('name' => 'matriz', 'label' => 'Matriz', 'type' => 'select', 'select' => 
						gGeraSelect('C004', 'C004_Id', 'C004_Id,C004_Nome_Abreviado', "", true,$g['empresaAtual'])
					),
					'filiais' => array('name' => 'filiais','label'=>'Filiais', 'type' => 'text', 'reject' => true),
				)
			)
		);
		$formArray['campos']['geral']['fis011FormSpedContribuicoes_btn'] = array('name' => 'fis011FormSpedContribuicoes_btn', 'type'=>'button', 'reload' => false, 'value' => 'Gerar', 'reject' => true);
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if($formCodigo == 2) {
				//Parametros para o funcao geradora do Sped
				$dataInicial = gCorrigeDataInverte($form->campoValorEnviado('dataInicial'));
				$dataFinal = gCorrigeDataInverte($form->campoValorEnviado('dataFinal'));
				$tipoEscrituracao = $form->campoValorEnviado('tipoEscrituracao');
				$numeroRecibo = $form->campoValorEnviado('numeroRecibo');
				$situacaoEspecial = $form->campoValorEnviado('situacaoEspecial');
				$C007_Id_Contabilista = $form->campoValorEnviado('C007_Id_Contabilista');
				$matriz = $form->campoValorEnviado('matriz');				
				$idsFiliais = !empty($form->campoValorEnviado('filiais')) ? explode(",", $form->campoValorEnviado('filiais')) : array();				
				
				if(!empty($dataInicial) && !empty($dataFinal)){
					if($tipoEscrituracao == '0' || ($tipoEscrituracao == '1' && !empty($numeroRecibo))){
						//$retorno = $FIS011->gerarSpedContribuicoes($dataInicial, $dataFinal, $tipoEscrituracao, $numeroRecibo, $situacaoEspecial);
						include_once('bibliotecas/SPED/EFD/contribuicoes.php');
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							window.location.href = '/fis/fis011/download/?ajax=true&sped=EFDCONTRIBUICOES&dataInicial={$dataInicial}&dataFinal={$dataFinal}';
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Erro', 'Para uma retificação, o número do recibo deve estar preenchido!');
					}
				} else {
					echo gGeraAlertMsg('Erro', 'Os campos data inicial e data final devem ser preenchidos!');
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

	case 'fis011FormSpedFiscal':
		global $g;	
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		// $tipoDocumento = isset($r_tipoDocumento) ? $r_tipoDocumento : "K";

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
        if($tipoDocumento=="K"){
			$form_editor = array('geral' => '{"dataInicial":{"top":7,"left":35.46875,"width":86,"hidden":0},"dataFinal":{"top":30,"left":39.75,"width":86,"hidden":0},"tipoDocumento":{"top":53,"left":9.578125,"width":160,"hidden":0},"tipoEscrituracao":{"top":76,"left":4.703125,"width":160,"hidden":1},"numeroRecibo":{"top":99,"left":40.625,"width":152,"hidden":1},"situacaoEspecial":{"top":122,"left":2.4375,"width":160,"hidden":1},"C007_Id_Contabilista":{"top":145,"left":44.625,"width":159,"hidden":1},"fis011FormSpedFiscal_btn":{"top":74,"left":96,"width":null,"hidden":0}}');
        }else{
			$form_editor = array(
			'geral' => '{"dataInicial":{"top":6.95465087890625,"left":52.29779052734375,"width":89.03921600000001,"hidden":0},"dataFinal":{"top":29.963241577148438,"left":57.64398193359375,"width":88.03921600000001,"hidden":0},"dataInventario":{"top":52.98713684082031,"left":32.6593017578125,"width":88.03921600000001,"hidden":0},"tipoDocumento":{"top":75.99571228027344,"left":25.61273193359375,"width":144,"hidden":0},"tipoEscrituracao":{"top":98.95832824707031,"left":20.98651123046875,"width":144,"hidden":0},"numeroRecibo":{"top":121.96690368652344,"left":57.56744384765625,"width":136.039216,"hidden":0},"situacaoEspecial":{"top":144.9601593017578,"left":20.0521240234375,"width":144,"hidden":0},"C007_Id_Contabilista":{"top":167.96873474121094,"left":61.3358154296875,"width":143,"hidden":0},"gerarK200":{"top":190.99266052246094,"left":51.91485595703125,"width":95,"hidden":0},"dataBlocoHText":{"top":210.99876403808594,"left":318.0606689453125,"width":null,"hidden":1},"gerarBlocoH":{"top":213.9859161376953,"left":38.9705810546875,"width":95,"hidden":0},"dataBlocoH":{"top":213.9859161376953,"left":223.989013671875,"width":88.03921600000001,"hidden":1},"gerarBlocoG":{"top":236.99449157714844,"left":39.32293701171875,"width":95,"hidden":0},"gerarBlocoEDetalhado":{"top":259.9877471923828,"left":11.9632568359375,"width":124,"hidden":1},"tipoCusto":{"top":282.9877471923828,"left":29.9632568359375,"width":163,"hidden":1},"fis011FormSpedFiscal_btn":{"top":303.9895782470703,"left":303.9981689453125,"width":null,"hidden":0}}');
        }
		
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis011FormSpedFiscal_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis011/form/fis011FormSpedFiscal/';		
		// novo formulário
		$form = new Formulario('fis011FormSpedFiscal');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'dataInicial' => array('name' => 'dataInicial','label'=>'Data Inicial', 'type' => 'data', 'reject' => true),
					'dataFinal' => array('name' => 'dataFinal','label'=>'Data Final', 'type' => 'data', 'reject' => true),					
					'dataInventario' => array('name' => 'dataInventario','label'=>'Data Inventário', 'type' => 'data', 'reject' => true),	
					'dataBlocoH' => array('name' => 'dataBlocoH','label'=>'Data', 'type' => 'data', 'reject' => true),				
					'tipoEscrituracao' => array('name' => 'tipoEscrituracao','label'=>'Tipo Escrituração', 'type' => 'select', 'reject' => true, 'select' => array(
							array('title' => 'Original', 'value' => '0', 'selected' => true),
							array('title' => 'Retificadora', 'value' => '1'),
						)
					),
					'numeroRecibo' => array('name' => 'numeroRecibo','label'=>'Nº Recibo', 'type' => 'text', 'reject' => true),
					'situacaoEspecial' => array('name' => 'situacaoEspecial','label'=>'Situação Especial', 'type' => 'select', 'reject' => true, 'select' => array(
							array('title' => 'Nenhuma', 'value' => '', 'selected' => true),
							array('title' => 'Abertura', 'value' => '0'),
							array('title' => 'Cisão', 'value' => '1'),
							array('title' => 'Fusão', 'value' => '2'),
							array('title' => 'Incorporação', 'value' => '3'),
							array('title' => 'Encerramento', 'value' => '4')
						)
					),
					'tipoCusto' => array('name' => 'tipoCusto','label'=>'Custo BLOCO H', 'type' => 'select', 'reject' => true, 'select' => array(
							array('title' => 'Custo última compra', 'value' => '1', 'selected' => true),
							array('title' => 'Custo médio', 'value' => '2'),
							array('title' => 'Custo manual', 'value' => '3')
						)
					),
					'C007_Id_Contabilista' => array('name' => 'C007_Id_Contabilista', 'label' => 'Contador', 'type' => 'select', 'select' => 
						gGeraSelect('C007', 'C007_Id', 'C007_Nome', "", true)
					),
					'gerarK200' => array('name' => 'gerarK200', 'label' => 'Gerar K200', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'gerarBlocoH' => array('name' => 'gerarBlocoH', 'label' => 'Gerar Bloco H', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true)
						)
					),
					'gerarBlocoG' => array('name' => 'gerarBlocoG', 'label' => 'Gerar Bloco G', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true)
						)
					),
					'gerarBlocoEDetalhado' => array('name' => 'gerarBlocoEDetalhado', 'label' => 'Gerar Bloco E Det.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true)
						)
					),
				)
			)
		);
		
		$selectedS = false;
		$selectedK = false;
		if($tipoDocumento=="S"){
			$selectedS = true;
		}else{
			$selectedK = true;
		}

		$formArray['campos']['geral']['tipoDocumento'] = array('name' => 'tipoDocumento', 'extra' => "onchange=\"divSetarAcaoId('{$g['divId']}', this.value, 'tipoDocumento'); divRefresh('{$g['divId']}');\"", 'label' => 'Tipo Documento', 'type' => 'select', 'reject' => true, 'select' => array(
				array('title' => 'SPED completo', 'value' => 'S', 'selected' => $selectedS),
				// array('title' => 'somente Bloco K', 'value' => 'K', 'selected' => $selectedK),
			)
	 	);

		if($g['C031']['SeparaProdutoPorEmpresa'] == 'S'){
			$formArray['campos']['geral']['apenasProdutoEmpresa'] = array('name' => 'apenasProdutoEmpresa', 'label' => 'Apenas produtos da empresa', 'type' => 'select', 'reject' => true, 'select' => array(
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N', 'selected' => true)
				)
			);
		}

		$formArray['campos']['geral']['fis011FormSpedFiscal_btn'] = array('name' => 'fis011FormSpedFiscal_btn', 'type'=>'button', 'reload' => false, 'value' => 'Gerar', 'reject' => true);
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if($formCodigo == 2) {
				//Parametros para o funcao geradora do Sped
				$dataInicial 		  = gCorrigeDataInverte($form->campoValorEnviado('dataInicial'));
				$dataFinal 			  = gCorrigeDataInverte($form->campoValorEnviado('dataFinal'));
				$dataInventario       = gCorrigeDataInverte($form->campoValorEnviado('dataInventario'));
				$dataInventarioBlocoH = gCorrigeDataInverte($form->campoValorEnviado('dataBlocoH'));
				$tipoEscrituracao 	  = $form->campoValorEnviado('tipoEscrituracao');
				$numeroRecibo 		  = $form->campoValorEnviado('numeroRecibo');
				$situacaoEspecial	  = $form->campoValorEnviado('situacaoEspecial');
				$C007_Id_Contabilista = $form->campoValorEnviado('C007_Id_Contabilista');
				$tipoDocumento        = $form->campoValorEnviado('tipoDocumento');
				$tipoCusto            = $form->campoValorEnviado('tipoCusto');	
				$gerarK200     	      = $form->campoValorEnviado('gerarK200');								
				$gerarBlocoH          = $form->campoValorEnviado('gerarBlocoH');		
				$gerarBlocoG 		  = $form->campoValorEnviado('gerarBlocoG');
				$gerarBlocoEDetalhado = $form->campoValorEnviado('gerarBlocoEDetalhado');
				$apenasProdutoEmpresa = $form->campoValorEnviado('apenasProdutoEmpresa');
						
				$erros = '';
				if(!empty($dataInicial) && !empty($dataFinal)){
					if($tipoEscrituracao == '0' || ($tipoEscrituracao == '1' && !empty($numeroRecibo))){
						if(!empty($C007_Id_Contabilista)){
							//$retorno = $FIS011->gerarSpedContribuicoes($dataInicial, $dataFinal, $tipoEscrituracao, $numeroRecibo, $situacaoEspecial);
							$dataInventario = !empty($dataInventario) ? $dataInventario : $dataFinal;
							include_once('bibliotecas/SPED/Fiscal/fiscal.php');
							$erro = 'false';
							// if(!empty(trim($erros))){
							// 	$erro = 'true';
							// }
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								window.location.href = '/fis/fis011/download/?ajax=true&sped=FISCAL&dataInicial={$dataInicial}&dataFinal={$dataFinal}';
							} </script></div>
EOT;
						}else{
							echo gGeraAlertMsg('Erro', 'O campo contador deve ser preenchido!');
						}
					} else {
						echo gGeraAlertMsg('Erro', 'Para uma retificação, o número do recibo deve estar preenchido!');
					}
				} else {
					echo gGeraAlertMsg('Erro', 'Os campos data inicial e data final devem ser preenchidos!');
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
