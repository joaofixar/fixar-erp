<?php
namespace hardness;
// Classes
require_once("bibliotecas/classes/FIN006.php");
$FIN006 = new FIN006();
// Carrega funções do ajax
require_once("prog/fin/fin001/form_func-js.php");

/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Form meta comissões
	* Chamado em: fin006tab02
	*/
	case 'fin006form01':		
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
		$form_tabela = 'T137';
		$form_campo_pk = 'T137_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin006/form/fin006form01/';
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('form_fin001_form1');  
		$formArray = array(
			'campos' => array(
				'comissoes' => array(
					'T137_Valor_Meta' => array('name' => 'T137_Valor_Meta', 'label'=>'Meta', 'type' => 'moeda','length' => array(15,10)),
					'T137_Formula_Comissao' => array('name' => 'T137_Formula_Comissao', 'label'=>'Fórmula', 'type' => 'textarea','length' => array(10,63)),					
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
				$retorno = $FIN006->gravarMeta($form,$acaoId);
				if ($retorno === true) {
			    	$alert = gGeraAlertMsg('Sucesso!', 'Informações gravadas com sucesso!', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								divRefresh('{$g['divIdParent']}', true);
								setTimeout(function() {
									{$alert}
								}, 150);
	  						});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
		        } else {
		        	echo gGeraAlertMsg("Erro!", $retorno);
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
		echo '<br />';
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
	break;		
	
	/**
	* Form totalizar comissões
	* Chamado em: fin006tab03
	*/
	case 'fin006form02':		
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
		$form_tabela = 'T137';
		$form_campo_pk = 'T137_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)		
		$enviar_btnId = 'content_form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin006/form/fin006form02/';
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('form_fin001_form1');  
		
		// gera array de anos
		$ano=array();
		$anoCount=date("Y",time());
		for($x=2003;$x<=$anoCount;$x++){
			$ano[$x]['title']=$x;
			$ano[$x]['value']=$x;
			if ($x==$anoCount)
				$ano[$x]['selected']=true;
		} 
					
		$formArray = array(
			'campos' => array(
				'totalizar_comissoes' => array(
					'Mes' => array('name' => 'Mes', 'label'=>'Mês', 'type' => 'select','extra'=>'style="width:110px;"','restricao' => 'globalNaoSelecionado','select' => 
						array(
							array('title' => '...', 'value' => 0, 'selected' => true),
							array('title' => 'Janeiro', 'value' => 1),
							array('title' => 'Fevereiro', 'value' => 2),
							array('title' => 'Março', 'value' => 3),
							array('title' => 'Abril', 'value' => 4),
							array('title' => 'Maio', 'value' => 5),
							array('title' => 'Junho', 'value' => 6),
							array('title' => 'Julho', 'value' => 7),
							array('title' => 'Agosto', 'value' => 8),
							array('title' => 'Setembro', 'value' => 9),
							array('title' => 'Outubro', 'value' => 10),
							array('title' => 'Novembro', 'value' => 11),
							array('title' => 'Dezembro', 'value' => 12),							
						)
					),
					'Ano' => array('name' => 'Ano', 'label'=>'Ano', 'type' => 'select', 'extra'=>'style="width:110px;"','select' => 
						$ano
					),				
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
				$retorno = $FIN006->totalizarComissoes($form);
				if ($retorno === true) {
			    	$alert = gGeraAlertMsg('Sucesso!', 'Totalização realizada com sucesso!', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								divRefresh('{$g['divIdParent']}', true);
								setTimeout(function() {
									{$alert}
								}, 150);
	  						});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
		        } else {
		        	echo gGeraAlertMsg("Erro!", $retorno);
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
		echo '<br />';
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form2_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
	break;			
}
?>