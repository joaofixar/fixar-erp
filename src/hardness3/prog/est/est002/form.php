<?php
namespace hardness;
require_once("bibliotecas/classes/EST002.php");
$EST002 = new EST002();

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'est002FormImprimirPedido':
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;	
		
		$T005_Id = array();
		$D024_Nome_Empresa = array();
		$T005_Id[0] = (isset($r_T005_Id)) ? $r_T005_Id : false;
		// Caso o $T005_Id vier da impressão única
		if($T005_Id[0])
		{
			$result = mysql_query("SELECT D024_Nome_Empresa 
			FROM T005
			LEFT JOIN D024 ON D024_Id = T005_D024_Id
			WHERE T005_Id='{$T005_Id[0]}'");
			$retorno = mysql_fetch_array($result);

			$D024_Nome_Empresa[0] = $retorno['D024_Nome_Empresa'];
		}
		// Caso do grid o usuário queira os pedidos selecionados
		else if(isset($r_selectT005))
		{
			$selectT005 = base64_decode($r_selectT005);
			$T005_Id = explode(',', $selectT005);

			$T005 = mysql_query("SELECT D024_Nome_Empresa
								 FROM T005
								 LEFT JOIN D024 ON D024_Id = T005_D024_Id
								 WHERE T005_Id IN ({$selectT005})");
			$mT005 = mysqli_fetch_all($T005);

			log("varexp" . var_export($mT005, true));

			$D024_Nome_Empresa = array_column($mT005, 0);
		}

  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
				
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		
		
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002FormImprimirPedidoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002FormImprimirPedido/';

		//form editor
		$form_editor = array('geral' =>'{"Pedido":{"top":7,"left":10,"width":125,"hidden":0},"Cliente":{"top":30,"left":10,"width":325,"hidden":0},"Login":{"top":53,"left":16,"width":100,"hidden":0},"Senha":{"top":76,"left":11,"width":100,"hidden":0}}' , );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Pedido' => array('name' => 'Pedido', 'label' => 'Pedido', 'value'=> (count($T005_Id) > 1 ? 'SELECIONADOS' : $T005_Id[0]), 'type' => 'text', 'disabled'=>true, 'extra' => "title='" . implode('; ', $T005_Id) . "'"),
					'Cliente' => array('name' => 'Cliente', 'label' => 'Cliente', 'value'=> (count($D024_Nome_Empresa) > 1 ? 'SELECIONADOS' : $D024_Nome_Empresa[0]), 'type' => 'text', 'length' => array(60, 60), 'disabled'=>true, 'extra' => "title='" . implode('; ', $D024_Nome_Empresa) . "'"),
					'Login' => array('name' => 'Login', 'label' => 'Login', 'type' => 'text','uppercase' => false, 'length' => array(15, 20)),
					'Senha' => array('name' => 'Senha', 'label' => 'Senha', 'type' => 'password','uppercase' => false, 'length' => array(15, 20)),
					'impressao_id' => array('name' => 'impressao_id', 'label' => 'impressao_id', 'value'=> implode(';' ,$T005_Id), 'type' => 'hidden'),
				)
			)
		);
        $form = new Formulario('formEst002ImprimirPedido');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					// Gerar Boleto
					//$form->campoValorEnviado(T117_D024_Id)
					$retorno = $EST002->ImprimirPedido($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);

						$T005_Id_64 = base64_encode($form->campoValorEnviado('impressao_id'));

						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							fecharJanela("{$g['divId']}");
							divRefresh('{$g['divIdParent']}', true);
							// Ação: abrir nova janela do browser 
							// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
							nova_janela('/est/est002/report/pedidoAlmoxarifado/?ajax=true&T005_Id_64='+encodeURIComponent('$T005_Id_64'),0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
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
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setTimeout(function(){
					$('.itemForm-Login').focus();
				}, 200);
			} </script></div>
EOT;
	break;
	
	case 'est002FormSeparacaoPedido':
	
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;	
		
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		
  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		
		
		/**
		 * ALGUMAS QUERYS NECESSÁRIAS PARA EXIBIR OS CAMPOS CORRETAMENTE
		 */
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		$sql = "SELECT * FROM T005 LEFT JOIN D022 ON D022_Id=T005_D022_Id WHERE T005_Id='{$T005_Id}'";
		
		$result = mysql_query($sql);
		$retorno = mysql_fetch_array($result);
		
		
		$sql2 = "SELECT * FROM D024 WHERE D024_Id='{$retorno['T005_D024_Id']}'";
		
		$result2 = mysql_query($sql2);
		$retorno2 = mysql_fetch_array($result2);
		
		$D024_Nome_Empresa = $retorno2['D024_Nome_Empresa'];
		
		$sql3 = "SELECT * FROM D018 WHERE D018_Id='{$retorno2['D024_D018_Id']}'";
		$result3 = mysql_query($sql3);
		$estado = mysql_fetch_array($result3);
		
		$sql4 = "SELECT * FROM D020 WHERE D020_Id='{$retorno2['D024_D020_Id']}'";
		$result4 = mysql_query($sql4);
		$cidade = mysql_fetch_array($result4);
		
		$sql5 = "SELECT * FROM D006 WHERE D006_Id='{$retorno['T005_D006_Id']}'";
		$result5 = mysql_query($sql5);
		$CFOP = mysql_fetch_array($result5);
		$CFOPCodigo = $CFOP['D006_Codigo_CFOP'];
		$CFOPDescricao = $CFOP['D006_Descricao'];
		
		//$retorno['T005_Data_Entrega'];
		
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		
		
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002FormSeparacaoPedidoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002FormSeparacaoPedido/';

		//form editor
		$form_editor = array('geral' => '{"Pedido":{"top":7,"left":74,"width":125,"hidden":0},"Cliente":{"top":30,"left":74,"width":328,"hidden":0},"Cidade":{"top":53,"left":73,"width":328,"hidden":0},"CFOP":{"top":76,"left":79,"width":328,"hidden":0},"Transportadora":{"top":99,"left":28,"width":328,"hidden":0},"T005_Estoque_Box":{"top":122,"left":20,"width":328,"hidden":0}}' , );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Pedido' => array('name' => 'Pedido', 'label' => 'Pedido', 'value'=>$T005_Id, 'type' => 'text', 'disabled'=>true),
					'Cliente' => array('name' => 'Cliente', 'label' => 'Cliente', 'value'=>$D024_Nome_Empresa, 'type' => 'text', 'length' => array(60, 60), 'disabled'=>true),
					'Cidade' => array('name' => 'Cidade', 'label' => 'Cidade', 'value'=>$cidade['D020_Nome_Cidade'].' - '.$estado['D018_UF'], 'type' => 'text', 'disabled'=>true),
					'CFOP' => array('name' => 'CFOP', 'label' => 'CFOP', 'value'=>$CFOPCodigo.' - '.$CFOPDescricao, 'type' => 'text', 'disabled'=>true, 'length' => array(60, 60)),
					//'Data' => array('name' => 'Data', 'label' => 'Data Entrega:', 'value'=>gCorrigeData($retorno['T005_Data_Entrega']), 'type' => 'text', 'disabled'=>true, 'length' => array(60, 60)),
					'Transportadora' => array('name' => 'Transportadora', 'label' => 'Transportadora:', 'value'=>$retorno['D022_Nome_Empresa'], 'type' => 'text', 'disabled'=>true, 'length' => array(60, 60)),
					'T005_Estoque_Box' => array('name' => 'T005_Estoque_Box', 'label' => 'Aguardar no BOX', 'value' => $retorno['T005_Estoque_Box'], 'type' => 'text', 'length' => array(5, 8)),
					'T005_Id' => array('name' => 'T005_Id', 'label' => 'Senha', 'value'=>$T005_Id, 'type' => 'hidden', 'length' => array(15, 20)),
				)
			)
		);
        $form = new Formulario('formEst002ImprimirPedido');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					// Gerar Boleto
					//$form->campoValorEnviado(T117_D024_Id)
					$retorno = $EST002->SeparacaoPedido($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Pedido enviado para o box com sucesso!.', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							fecharJanela("{$g['divId']}");
							divRefresh('{$g['divIdParent']}', true);
							// Ação: abrir nova janela do browser 
							// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
							//nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id='+encodeURIComponent('{$form->campoValorEnviado('T005_Id')}'),0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
					}
				}
			}


		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, false, false, false);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
	
	
	case 'est002FormFaturamentoPedido':
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;	
		
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		
  		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		
		
		/**
		 * ALGUMAS QUERYS NECESSÁRIAS PARA EXIBIR OS CAMPOS CORRETAMENTE
		 */
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		$sql = "SELECT * FROM T005 LEFT JOIN D022 ON D022_Id=T005_D022_Id WHERE T005_Id='{$T005_Id}'";
		
		$result = mysql_query($sql);
		$retorno = mysql_fetch_array($result);
		
		
		$sql2 = "SELECT * FROM D024 WHERE D024_Id='{$retorno['T005_D024_Id']}'";
		
		$result2 = mysql_query($sql2);
		$retorno2 = mysql_fetch_array($result2);
		
		$D024_Nome_Empresa = $retorno2['D024_Nome_Empresa'];
		
		$sql3 = "SELECT * FROM D018 WHERE D018_Id='{$retorno2['D024_D018_Id']}'";
		$result3 = mysql_query($sql3);
		$estado = mysql_fetch_array($result3);
		
		$sql4 = "SELECT * FROM D020 WHERE D020_Id='{$retorno2['D024_D020_Id']}'";
		$result4 = mysql_query($sql4);
		$cidade = mysql_fetch_array($result4);
		
		$sql5 = "SELECT * FROM D006 WHERE D006_Id='{$retorno['T005_D006_Id']}'";
		$result5 = mysql_query($sql5);
		$CFOP = mysql_fetch_array($result5);
		$CFOPCodigo = $CFOP['D006_Codigo_CFOP'];
		$CFOPDescricao = $CFOP['D006_Descricao'];

		$retorno['T005_Peso_Bruto'] = gCorrigeNumero($retorno['T005_Peso_Bruto'], 3);
		$retorno['T005_Peso_Liquido'] = gCorrigeNumero($retorno['T005_Peso_Liquido'], 3);
		
		//$retorno['T005_Data_Entrega'];
		
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		
		
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002FormFaturamentoPedidoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002FormFaturamentoPedido/';

		// form editor
		$form_editor = array('geral' => '{"Pedido":{"top":7,"left":56.546875,"width":125,"hidden":0},"T005_Quantidade_Volumes":{"top":7,"left":504.25,"width":135,"hidden":0},"T005_Flag_Pdv_Importado":{"top":7,"left":720.375,"width":63,"hidden":0},"Cliente":{"top":30,"left":56.78125,"width":328,"hidden":0},"T005_Especie":{"top":30,"left":516.421875,"width":136,"hidden":0},"Cidade":{"top":53,"left":56.546875,"width":328,"hidden":0},"T005_Peso_Bruto":{"top":53,"left":500.84375,"width":136,"hidden":0},"CFOP":{"top":76,"left":62.859375,"width":328,"hidden":0},"T005_Peso_Liquido":{"top":76,"left":491.140625,"width":136,"hidden":0},"Data":{"top":99,"left":24.921875,"width":328,"hidden":0},"Login":{"top":99,"left":527.5,"width":136,"hidden":0},"Transportadora":{"top":122,"left":12.5625,"width":328,"hidden":0},"Senha":{"top":122,"left":524.140625,"width":136,"hidden":0},"T005_Estoque_Box":{"top":283,"left":404,"width":50,"hidden":1}}' , );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Pedido' => array('name' => 'Pedido', 'label' => 'Pedido', 'value'=>$T005_Id, 'type' => 'text', 'disabled'=>true),
					'Cliente' => array('name' => 'Cliente', 'label' => 'Cliente', 'value'=>$D024_Nome_Empresa, 'type' => 'text', 'length' => array(60, 60), 'disabled'=>true),
					'Cidade' => array('name' => 'Cidade', 'label' => 'Cidade', 'value'=>$cidade['D020_Nome_Cidade'].' - '.$estado['D018_UF'], 'type' => 'text', 'disabled'=>true),
					'CFOP' => array('name' => 'CFOP', 'label' => 'CFOP', 'value'=>$CFOPCodigo.' - '.$CFOPDescricao, 'type' => 'text', 'disabled'=>true, 'length' => array(60, 60)),
					'Data' => array('name' => 'Data', 'label' => 'Data Entrega:', 'value'=>gCorrigeData($retorno['T005_Data_Entrega']), 'type' => 'text', 'disabled'=>true, 'length' => array(60, 60)),
					'Transportadora' => array('name' => 'Transportadora', 'label' => 'Transportadora:', 'value'=>$retorno['D022_Nome_Empresa'], 'type' => 'text', 'disabled'=>true, 'length' => array(60, 60)),
					'T005_Quantidade_Volumes' => array('name' => 'T005_Quantidade_Volumes', 'label' => 'Volumes', 'value' => $retorno['T005_Quantidade_Volumes'], 'type' => 'text', 'length' => array(30, 20)),
					'T005_Especie' => array('name' => 'T005_Especie', 'label' => 'Espécie', 'value' => $retorno['T005_Especie'], 'type' => 'text', 'length' => array(30, 20)),
					'T005_Peso_Bruto' => array('name' => 'T005_Peso_Bruto', 'label' => 'Peso Bruto', 'value' => $retorno['T005_Peso_Bruto'], 'type' => 'moeda', 'length' => array(5, 20), 'casasDecimais' => '3'),
					'T005_Peso_Liquido' => array('name' => 'T005_Peso_Liquido', 'label' => 'Peso Líquido', 'value' => $retorno['T005_Peso_Liquido'], 'type' => 'moeda', 'length' => array(5, 20), 'casasDecimais' => '3'),
					'T005_Estoque_Box' => array('name' => 'T005_Estoque_Box', 'label' => 'Aguardar no BOX', 'value' => $retorno['T005_Estoque_Box'], 'type' => 'text', 'length' => array(5, 10)),
					'T005_Id' => array('name' => 'T005_Id', 'label' => 'T005_Id', 'value'=>$T005_Id, 'type' => 'hidden', 'length' => array(15, 20)),
					'T005_Flag_ACP' => array('name' => 'T005_Flag_ACP', 'label' => 'T005_Flag_ACP', 'value'=>$retorno['T005_Flag_ACP'], 'type' => 'hidden', 'length' => array(15, 20)),
					'Login' => array('name' => 'Login', 'label' => 'Login', 'type' => 'text','uppercase' => false, 'length' => array(15, 20)),
					'Senha' => array('name' => 'Senha', 'label' => 'Senha', 'type' => 'password','uppercase' => false, 'length' => array(15, 20)),
					'T005_Flag_Pdv_Importado' => array('name' => 'T005_Flag_Pdv_Importado', 'label' => 'PDV (DAV)', 'value' => $retorno['T005_Flag_Pdv_Importado'], 'extra' => "title='Ao selecionar como SIM o pedido será enviado direto para aguardando coleta'",'type' => 'select', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
				)
			)
		);
		if(empty($retorno['T005_Quantidade_Volumes'])){
			unset($formArray['campos']['geral']['T005_Quantidade_Volumes']['value']);
		}

		if($g['C031']['totalizarPesoVenda'] == 'S'){
			$formArray['campos']['geral']['T005_Flag_Totalizar_Peso'] = array('name' => 'T005_Flag_Totalizar_Peso', 'label' => 'Totalizar peso', 'value' => $retorno['T005_Flag_Totalizar_Peso'], 'type' => 'select', 'select' => 
				array(
					array('title' => 'SIM', 'value' => 'S'),
					array('title' => 'NÃO', 'value' => 'N'),
				)
			);
		}
        $form = new Formulario('formEst002FaturamentoPedido');
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} #T005_Flag_ACP').change(function() {
				if (($(this).val() == '8')) {
					$('#{$g['divId']} #T005_Flag_Pdv_Importado').closest('.formEditor').show();
					setTimeout(function() {
						$('#{$g['divId']} #T005_Flag_Pdv_Importado').focus();
					}, 100);
				}else{
					$('#{$g['divId']} #T005_Flag_Pdv_Importado').val('');
					$('#{$g['divId']} #T005_Flag_Pdv_Importado').closest('.formEditor').hide();
				}
			});
			$('#{$g['divId']} #T005_Flag_ACP').trigger('change');
		} </script></div>
EOT;


		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					// Gerar Boleto
					//$form->campoValorEnviado(T117_D024_Id)
					$retorno = $EST002->FaturamentoPedido($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação concluída', 'Conferência concluída com sucesso.', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
					}
				}
			}


		/**
		 * PARTE 4: Gerar o formulário
		 */
        $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, false, false, false);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
	
	case 'est002FormConfirmarEmprestimo':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002FormConfirmarEmprestimoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002FormConfirmarEmprestimo/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D131_D116_Id' => array('name' => 'D131_D116_Id', 'label' => 'Veículo', 'type' => 'select', 
						// 'select' =>  gGeraSelect('D116','D116_Id','D116_Modelo','',true),
					// ),
					'Senha' => array('name' => 'Senha', 'label' => 'Senha', 'type' => 'password', 'reject'=>true, 'length' => array(40,80)),
				)
			)
		);
        $form = new Formulario('form-cad046FormPrincipal');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				//if (!empty($r_acaoId)) {
				$msg = $EST002->confirmarEmpretismo($form->campoValorEnviado('Senha'), $r_acaoId);
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
				}
			}
		} //else {
			// // Carrega os valores caso seja um update
			// if (!empty($r_acaoId)) {
				// $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// // Mostra erros de formulário uteis ao desenvolvedor
				// if ($processado !== true) {
					// reset($form->formArray['campos']);
					// if ($tab == key($form->formArray['campos'])) {
						// echo $processado;
					// }
				// }
			// }
		// }

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

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
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est002FormConfirmarEmprestimoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est002FormCadastrarEmprestimo':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T175';
		$form_campo_pk = 'T175_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002FormCadastrarEmprestimoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002FormCadastrarEmprestimo/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'selecionaTipoUsuario' => array('name' => 'selecionaTipoUsuario', 'label'=>'Tipo','type' => 'select', 'extra'=>'style="width:132px;"','select' => 
						array(
							array('title' => 'Funcionário', 'value' => 'Funcionario', 'selected' => true),
							array('title' => 'Usuário', 'value' => 'Usuario'),
						)
					),
					'T175_D109_Id' => array('name' => 'T175_D109_Id', 'label' => 'Funcionário', 'extra'=>'style="width:280px;"', 'type' => 'pesquisa', 'pesquisa' => 
						array('D109','D109_Id','D109_Nome',''), 'length' => array(30,30)
					),
					'T175_C007_Id' => array('name' => 'T175_C007_Id', 'label' => 'Usuário', 'extra'=>'style="width:280px;"', 'type' => 'pesquisa', 'pesquisa' => 
						array('C007','C007_Id','C007_Primeiro_Nome',''), 'length' => array(30,30)
					),
					'T175_D009_Id' => array('name' => 'T175_D009_Id', 'label' => 'Produto', 'extra'=>'style="width:280px;"', 'type' => 'pesquisa', 'pesquisa' => 
						array('D001','D009_Id','D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca','left join D049 on D049_D001_Id=D001_Id left join D009 on D009_D049_Id=D049_Id left join D082 on D082_Id=D049_D082_Id left join D037 on D037_Id=D001_D037_Id where (D009_C004_Id = '.$g['empresaAtual'].' and D001_Flag_Pre_Cadastro != "S" and D001_Flag_Ativo != "N") group by D009_Id order by D001_Codigo_Produto'), 'length' => array(30,30)
					),
					'quantidade' => array('name' => 'quantidade', 'label' => 'Quantidade', 'type' => 'text', 'reject'=>true, 'length' => array(10,80)),
					'Senha' => array('name' => 'Senha', 'label' => 'Senha', 'type' => 'password', 'reject'=>true, 'length' => array(15,80)),
				)
			)
		);
        $form = new Formulario('form-est002FormCadastrarEmprestimo');
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #selecionaTipoUsuario').change(function() {
					if ($(this).val() == 'Funcionario') {
						$('#{$g['divId']} #T175_C007_Id').val('')
						$('#{$g['divId']} #T175_C007_Id_Pesquisa').attr('disabled', 'disabled');
						$('#{$g['divId']} #T175_D109_Id_Pesquisa').removeAttr('disabled');
						$('#{$g['divId']} #T175_D109_Id_Pesquisa').focus();
					} else {
						$('#{$g['divId']} #T175_D109_Id').val('')
						$('#{$g['divId']} #T175_D109_Id_Pesquisa').attr('disabled', 'disabled');
						$('#{$g['divId']} #T175_C007_Id_Pesquisa').removeAttr('disabled');
						$('#{$g['divId']} #T175_C007_Id_Pesquisa').focus();
					}
				});
				$('#{$g['divId']} #selecionaTipoUsuario').trigger('change');
				$('#{$g['divId']} #selecionaTipoUsuario').focus();	
		    } </script></div>
EOT;

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				//if (!empty($r_acaoId)) {
				$msg = $EST002->validaInclusaoEmprestimo($form);
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Registro incluído com sucesso!');
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
				}
			}
		} //else {
			// // Carrega os valores caso seja um update
			// if (!empty($r_acaoId)) {
				// $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// // Mostra erros de formulário uteis ao desenvolvedor
				// if ($processado !== true) {
					// reset($form->formArray['campos']);
					// if ($tab == key($form->formArray['campos'])) {
						// echo $processado;
					// }
				// }
			// }
		// }

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

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
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est002FormCadastrarEmprestimoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est002FormSelecionarFuncionario':
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;	
		
		$tab = (isset($r_tab)) ? $r_tab : false;
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		
		
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002FormSelecionarFuncionarioBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002FormSelecionarFuncionario/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D109_Id' => array('name' => 'D109_Id','reject' => true, 'label' => 'Funcionário', 'type' => 'select','extra' => 'style="width:300px;"',
						'select'=> gGeraSelect('D109','D109_Id','D109_Nome','',true),
					),
				)
			)
		);
        $form = new Formulario('formest002FormSelecionarFuncionario');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					// Gerar Boleto
					$D109_Id = $form->campoValorEnviado('D109_Id');
					$retorno = $EST002->confirmarEntregaInterna($D109_Id);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Operação realizada com sucesso!', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
									fecharJanela('{$g['divIdParent']}');
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							//divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
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
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est002FormSelecionarFuncionarioBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est002SelecionarModelo':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;	
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

		$form_editor = array('geral' => '{"est002SelecionarModelo_btn":{"top":5,"left":256,"width":null,"hidden":0},"D138_Id":{"top":7,"left":5,"width":200,"hidden":0}}' , );

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D138';
		$form_campo_pk = 'D138_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002SelecionarModelo_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002SelecionarModelo/';

		// novo formulário
		$form = new Formulario('est002SelecionarModelo_form');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D138_Id' => array('name' => 'D138_Id', 'label'=>'Modelo','type' => 'select', 'extra'=>'style="width:200px;"','select' => 
						gGeraSelect('D138','D138_Id','D138_Modelo'," WHERE D138_Flag_Tipo='S' AND D138_Flag_Mostrar='S'"),
					),					
				)
			)
		);
        //$formArray['campos']['geral']['btnFechar'] = array('name' => 'btnFechar', 'type' => 'button', 'reload' => false, 'value' => 'Cancelar', 'align' => 'right', 'extra' => 'onclick="fecharJanela(\''.$g['divId'].'\');"','style' => 'float:right;', 'reject' => true);
        $formArray['campos']['geral']['est002SelecionarModelo_btn'] = array('name' => 'est002SelecionarModelo_btn', 'type' => 'submit', 'reload' => false, 'value' => 'Imprimir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						imprimirEtiquetasProdutosPedido('{$g['divId']}', '{$D138_Id}', '{$T005_Id}');
					} </script></div>
EOT;
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
		$gerado = gGeraCamposForm($form, $tab, $D138_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'est002SeparacaoPorLocacao':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		$T238_Id = (isset($T238_Id)) ? $T238_Id : false;

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

		$form_editor = array('geral' => '{"T238_T006_Id":{"top":7,"left":76.28125,"width":543,"hidden":0},"T238_T066_Id":{"top":30,"left":73.1875,"width":415,"hidden":0},"alterarLocacao":{"top":28,"left":560,"width":null,"hidden":0},"T238_Quantidade_Separacao":{"top":53,"left":11.703125,"width":72,"hidden":0},"est002SeparacaoPorLocacaoSepProd_btn":{"top":74,"left":336,"width":null,"hidden":0}}');

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T238';
		$form_campo_pk = 'T238_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002SeparacaoPorLocacaoSepProd_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002SeparacaoPorLocacao/';

		// novo formulário
		$form = new Formulario('est002SeparacaoPorLocacaoSepProd_form');

		// campos
		$disabled = false;
		$extraD004 = 'LEFT JOIN D004 ON T066_D004_Id = D004_Id';
		$T238_T006_Id = gGeraSelect('T006','T006_Id','T006_Descricao_Produto,IFNULL(T006_Quantidade_Separacao[[virgula]]0),T006_Quantidade',"left join D009 on D009_Id=T006_D009_Id left join T066 on T066_D009_Id=D009_Id left join D004 on D004_Id=T066_D004_Id left join T005 on T005_Id=T006_T005_Id LEFT JOIN T238 ON T238_T006_Id=T006_Id WHERE T006_T005_Id='{$r_T005_Id}' AND (IFNULL(T006_Quantidade_Separacao, 0) < T006_Quantidade || IFNULL(T238_T006_Id,0) <= 0) AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id group by T006_Id ORDER BY D004_Local,T006_Id", false, false, "%1 (%2 / %3)");
		if (empty($T238_T006_Id)) {
			$disabled = true;
			$T238_T006_Id[] = array(
				'title' => 'Todos os produtos já foram separados!',
				'value' => 0
			);
			$extraD004 = "LEFT JOIN D004 ON D004_Id  = T066_D004_Id
			              LEFT JOIN D009 ON D009_Id  = T066_D009_Id
			              LEFT JOIN D049 ON D049_Id  = D009_D049_Id
			              LEFT JOIN D001 ON D001_Id  = D049_D001_Id
			              LEFT JOIN D001A ON D001_Id = D001A_D001_Id
			              LEFT JOIN T066A ON T066_Id = T066A_T066_Id
			                  WHERE (1=2)";
		} else {
			$sql = "SELECT T006_D009_Id, T006_T066_Id FROM T006 WHERE T006_Id = '{$T238_T006_Id[0]['value']}'";
			$resT006 = mysql_query($sql);
			$rowT006 = mysql_fetch_assoc($resT006);
			//$extraD004 = "WHERE D004_Id IN (SELECT D004_Id FROM T006 LEFT JOIN T066 ON T066_D009_Id=T006_D009_Id LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T006_Id = '{$T238_T006_Id[0]['value']}')";
			$extraD004 = "LEFT JOIN D004 ON D004_Id  = T066_D004_Id 
						  LEFT JOIN D009 ON D009_Id  = T066_D009_Id
						  LEFT JOIN D049 ON D049_Id  = D009_D049_Id
						  LEFT JOIN D001 ON D001_Id  = D049_D001_Id
						  LEFT JOIN D001A ON D001_Id = D001A_D001_Id
						  LEFT JOIN T066A ON T066_Id = T066A_T066_Id
					          WHERE T066_D009_Id = '{$rowT006['T006_D009_Id']}'
				           ORDER BY IF(T066_Id='{$rowT006['T006_T066_Id']}',1,0) DESC, T066_Id ASC";
		}
		$T238_T066_Id = gGeraSelect('T066','T066_Id','D004_Local,FORMAT(T066_Quantidade_Estoque[[virgula]]2),T066_Id,if(T066A_Data_Validade = "0000-00-00","", concat("- VAL: ",DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y")))', $extraD004, false, false, "%1 (%2) - %3 %4");
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T238_T006_Id' => array('name' => 'T238_T006_Id', 'label' => 'Produto', 'type' => 'select', 'select' => $T238_T006_Id, 'disabled' => $disabled, 'extra' => "onchange=\"separacaoProdutoPorLocacao(this, '{$g['divId']}');\""),
					'T238_T066_Id' => array('name' => 'T238_T066_Id', 'label' => 'Locação', 'type' => 'select', 'select' => $T238_T066_Id, 'disabled' => $disabled),
					'alterarLocacao' => array('name' => 'alterarLocacao', 'type'=>'button', 'reload' => false, 'value' => 'Alterar Locação', 'reject' => true),
					'T238_Quantidade_Separacao' => array('name' => 'T238_Quantidade_Separacao', 'label' => 'Quantidade Separação', 'type' => 'moeda', 'disabled' => $disabled, 'casasDecimais' => '4'),
					'T238_T005_Id' => array('name' => 'T238_T005_Id', 'type' => 'hidden', 'value' => $r_T005_Id),
					'T238_D004_Id' => array('name' => 'T238_D004_Id', 'type' => 'hidden'),
				)
			)
		);
        //$formArray['campos']['geral']['btnFechar'] = array('name' => 'btnFechar', 'type' => 'button', 'reload' => false, 'value' => 'Cancelar', 'align' => 'right', 'extra' => 'onclick="fecharJanela(\''.$g['divId'].'\');"','style' => 'float:right;', 'reject' => true);
        $formArray['campos']['geral']['est002SeparacaoPorLocacaoSepProd_btn'] = array('name' => 'est002SeparacaoPorLocacaoSepProd_btn', 'type' => 'submit', 'reload' => false, 'value' => 'Separar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#alterarLocacao').attr('D009_Id', '{$rowT006['T006_D009_Id']}');
				$('#alterarLocacao').unbind('click').bind('click', function(data) {
					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/est/est003/content/est003ContentLocacoes/','&acaoId=' + encodeURIComponent($('#alterarLocacao').attr('D009_Id')) + '&D009_Id=' + encodeURIComponent($('#alterarLocacao').attr('D009_Id')) + '&separacao=true', [700,350]);
				});
		    } </script></div>
EOT;
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
					// 
				} else {					
					// Insert
					$sqlT066 = mysql_query("SELECT T066_Quantidade_Estoque,
												   D006_Flag_Estoque 
											  FROM T066 
									 	 LEFT JOIN T006 ON T006_Id='{$form->campoValorEnviado('T238_T006_Id')}'
									     LEFT JOIN D006 ON D006_Id=T006_D006_Id 
										     WHERE T066_Id = '{$form->campoValorEnviado('T238_T066_Id')}'");
					$rowT066 = mysql_fetch_array($sqlT066);		

					$quantidadeSeparacao = gCorrigeNumeroInverte($form->campoValorEnviado('T238_Quantidade_Separacao'));
					if($rowT066['T066_Quantidade_Estoque'] < $quantidadeSeparacao  && $g['C031']['permitirFaturarSemEstoque'] == 'N' && $rowT066['D006_Flag_Estoque'] == 'D'){
						echo gGeraAlertMsg("Erro", "A locação selecionada não possui estoque suficiente para a quantidade informada");
						// refresh
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$g['divId']}');
						} </script></div>
EOT;
					} else {
						$sqlD004 = mysql_query("SELECT T066_Id FROM T066 WHERE T066_Id='{$form->campoValorEnviado('T238_T066_Id')}' and ifnull(T066_D004_Id,0)=0");
				        if(mysql_num_rows($sqlD004) > 0) {
				            echo gGeraAlertMsg("Erro", "Não foi informado a LOCAÇÃO de todos os produtos.<br />VERIFIQUE!");
				        } else {			
							$sql = "SELECT T006_Quantidade_Separacao, T006_Quantidade FROM T006 WHERE T006_Id = '{$form->campoValorEnviado('T238_T006_Id')}'";
							$resT006 = mysql_query($sql);
							$rowT006 = mysql_fetch_assoc($resT006);
							if ( ( gCorrigeNumeroInverte($form->campoValorEnviado('T238_Quantidade_Separacao')) + $rowT006['T006_Quantidade_Separacao'] ) > $rowT006['T006_Quantidade'] ) {
								echo gGeraAlertMsg('Erro', "Quantidade separada informada é maior que a quantidade total necessária para separar");
							} else {
								$form->campoSetarValor('T238_D004_Id', getD004($form->campoValorEnviado('T238_T066_Id')), true);
								if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
									// refresh
									echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											divRefresh('{$g['divId']}');
											divRefresh('{$r_divRefresh}');
										} </script></div>
EOT;
								}
							}
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
						echo $processado;
					}
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $T238_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'est002FormCadastroPacote':
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
		$form_tabela = 'T268';
		$form_campo_pk = 'T268_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002FormCadastroPacoteBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002FormCadastroPacote/';
		$form_editor = array(
			'geral' => '{"T268_Servico":{"top":53,"left":45.859375,"width":88,"hidden":1},"T268_D024_Id":{"top":7,"left":23.84375,"width":394,"hidden":0},"T268_D148_Id":{"top":30,"left":42.796875,"width":416,"hidden":0},"T268_D036_Id":{"top":53,"left":53.203125,"width":200,"hidden":0},"T268_Recebedor":{"top":53,"left":324.0625,"width":120,"hidden":0},"T268_Peso":{"top":76,"left":64.8125,"width":72,"hidden":0},"T268_Centro_Custo":{"top":76,"left":300.21875,"width":104,"hidden":0},"T268_Comprimento":{"top":99,"left":23.859375,"width":72,"hidden":0},"T268_Servico_Adicional_MP":{"top":99,"left":383.1875,"width":64,"hidden":0},"T268_Largura":{"top":122,"left":51.96875,"width":72,"hidden":0},"T268_Servico_Adicional_AR":{"top":122,"left":336.296875,"width":64,"hidden":0},"T268_Altura":{"top":145,"left":61.140625,"width":72,"hidden":0},"T268_Servico_Adicional_VD":{"top":145,"left":365.640625,"width":64,"hidden":0},"T268_Codigo_Rastreio":{"top":168,"left":0,"width":146,"hidden":0},"T268_Valor_Declarado":{"top":168,"left":301.875,"width":88,"hidden":0},"est002FormCadastroPacoteBtn":{"top":189,"left":448,"width":null,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		if(!empty($acaoId)){
			$sqlT268 = mysql_query("SELECT * FROM T268 WHERE T268_Id = '{$acaoId}'");
			$rowT268 = mysql_fetch_array($sqlT268);

			$arrayEndereco = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$rowT268['T268_D024_Id'].' ORDER BY D148_Id',true);
			$arrayEndereco[0]['title'] = 'PADRÃO';
		}

		$form = new Formulario('est002FormCadastroPacote');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T268_D024_Id' => array('name' => 'T268_D024_Id', 'label' => 'Destinatário', 'extra' => "onchange=\"carregaListaEnderecos(this, '{$g['divId']}');\"", 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]')),
					'T268_D148_Id' => array('name' => 'T268_D148_Id', 'label' => 'Endereço', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$rowT268['T268_D024_Id'].' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T268_Centro_Custo' => array('name' => 'T268_Centro_Custo', 'label' => 'Centro de custo', 'type' => 'text'),
					'T268_Servico' => array('name' => 'T268_Servico', 'label' => 'Serviço', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'PAC', 'value' => 'PAC'),
							array('title' => 'Sedex', 'value' => 'SEDEX'),
							array('title' => 'e-Sedex', 'value' => 'ESEDEX'),
							array('title' => 'Sedex 10', 'value' => 'SEDEX10'),
							array('title' => 'PAX', 'value' => 'PAX'),
						)
					),
					'T268_D036_Id' => array('name' => 'T268_D036_Id', 'label'=>'Serviço','type' => 'select', 'extra'=>'style="width:200px;"','select' => 
						gGeraSelect('D036','D036_Id','D036_Tipo_Transporte'," WHERE D036_Flag_Correios='S'"),
					),
					'T268_Servico_Adicional_AR' => array('name' => 'T268_Servico_Adicional_AR', 'label' => 'Aviso de recebimento', 'type' => 'select', 'select' => 
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S'),
						)
					),
					'T268_Servico_Adicional_VD' => array('name' => 'T268_Servico_Adicional_VD', 'label' => 'Valor declarado', 'type' => 'select', 'select' => 
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S'),
						)
					),
					'T268_Servico_Adicional_MP' => array('name' => 'T268_Servico_Adicional_MP', 'label' => 'Mão própria', 'type' => 'select', 'select' => 
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S'),
						)
					),
					'T268_Codigo_Rastreio' => array('name' => 'T268_Codigo_Rastreio', 'label' => 'Código de rastreio', 'type' => 'text', 'readonly' => true),
					'T268_Valor_Declarado' => array('name' => 'T268_Valor_Declarado', 'label' => 'Valor declarado (R$)', 'type' => 'moeda', 'casasDecimais' => '2'),
					'T268_Recebedor' => array('name' => 'T268_Recebedor', 'label' => 'Recebedor', 'type' => 'text'),
					'T268_Peso' => array('name' => 'T268_Peso', 'label' => 'Peso', 'type' => 'moeda', 'casasDecimais' => '2'),
					'T268_Largura' => array('name' => 'T268_Largura', 'label' => 'Largura', 'type' => 'moeda', 'casasDecimais' => '2'),
					'T268_Altura' => array('name' => 'T268_Altura', 'label' => 'Altura', 'type' => 'moeda', 'casasDecimais' => '2'),
					'T268_Comprimento' => array('name' => 'T268_Comprimento', 'label' => 'Comprimento', 'type' => 'moeda', 'casasDecimais' => '2'),
					'T268_C004_Id' => array('name' => 'T268_C004_Id', 'label' => 'C004_Id', 'type' => 'hidden', 'value' => $g['empresaAtual']),
				),
			)
		);

		$formArray['campos']['geral']['est002FormCadastroPacoteBtn'] = array('name' => 'est002FormCadastroPacoteBtn', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						$alert = gGeraAlertMsg('Operação OK', 'Registro alterado com sucesso!', false);
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
						$alert = gGeraAlertMsg('Operação OK', 'Registro incluído com sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	case 'est002FormRomaneio':
			/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formId = uniqid();

		$sqlT005 = mysql_query("SELECT T005_T275_Id FROM T005 WHERE T005_Id='{$acaoId}'");
		$T005 = mysql_fetch_array($sqlT005);

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002FormRomaneio_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002FormRomaneio/';

		$form_editor = array(
			'geral' => '{"livre":{"top":10,"left":30,"width":null,"hidden":0},"Romaneio":{"top":30,"left":40.71875,"width":88,"hidden":0}}',
		);
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'livre' => array('name' => 'linha', 'type'=>'comentario', 'value' => 'Digite o número do romaneio que será alterado.', 'label' => '', 'reject' => true, 'style' => 'font-weight:bold; font-size: 14px;'),
					'Romaneio' => array('name' => 'Romaneio', 'label'=> 'Nº Romaneio', 'type' => 'text', 'reject'=>true, 'value' => $T005['T005_T275_Id']),
				)
			)
		);

		// novo formulário
		$form = new Formulario('est002FormRomaneio');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
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
				$T005_T275_Id = $form->campoValorEnviado('Romaneio');
				
				$sqlT275 = mysql_query("SELECT T275_Id FROM T275 WHERE T275_Id={$T005_T275_Id}");
				$numT275 = mysql_num_rows($sqlT275);

				if(($numT275<=0)  && ((!empty($T005_T275_Id)) && ($T005_T275_Id>0))){
					$alert = gGeraAlertMsg('Erro', 'Este romaneio não está cadastrado, favor verificar!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
							} </script></div>
EOT;
				}else{

					if($T005_T275_Id != $T005['T005_T275_Id']){

						mysql_query("UPDATE T005 SET T005_T275_Id = {$T005_T275_Id} WHERE T005_Id={$acaoId}");

						$alert = gGeraAlertMsg('Operação OK', 'Registro alterado com sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									{$alert}
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est002FormRomaneio_btn">Alterar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est002FormIdSeparacaoBox':

		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T006_Id = (isset($_REQUEST['T006_Id'])) ? $_REQUEST['T006_Id'] : false;
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
		$form_tabela = 'T006';
		$form_campo_pk = 'T006_Id';
		$enviar_btnId = 'est002FormIdSeparacaoBox';
		$enviar_acaoHref = '/est/est002/form/est002FormIdSeparacaoBox/';

		//form editor
		$form_editor = array(
			'geral' => '{"idBox":{"top":15,"left":15,"width":120,"hidden":0}}',
		);

		// novo formulário
		$form = new Formulario('est002FormIdSeparacaoBox');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'idBox' => array('name' => 'idBox', 'label' => 'Número do Box', 'type' => 'text', 'align' => 'right', 'uppercase' => false),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 *            E carregamento dos valores (se não foi enviado e possui acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$idBox = $form->campoValorEnviado('idBox');

				if (!empty($idBox)) {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							fecharJanela("{$g['divId']}");
							abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Separação dos Produtos - BOX: {$idBox}', '/est/est002/content/est002ContentSepararProdutos?idBox={$idBox}', [900,600]);
						} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Informar campo', 'Informe um número de box para prosseguir!');
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
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) {
			$g["smarty"]->assign("formId", $formId);
		}
		$g['smarty']->display('formEditor.tpl');

	break;

	case 'est002FormSepararProdutos':

		global $g;

		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;

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
		$form_tabela = 'T006';
		$form_campo_pk = 'T006_Id';
		$enviar_btnId = 'est002FormSepararProdutos';
		$enviar_acaoHref = '/est/est002/form/est002FormSepararProdutos/';

		//form editor
		$form_editor = array(
			'geral' => '{"codBarras":{"top":15,"left":15,"width":120,"hidden":0}}',
		);

		// novo formulário
		$form = new Formulario('est002FormSepararProdutos');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'codBarras' => array('name' => 'codBarras', 'label' => 'Inserir código barras', 'type' => 'text', 'align' => 'right', 'uppercase' => false),
					'idBox' => array('name' => 'idBox', 'label' => 'Numero BOX', 'type' => 'hidden', 'value' => $r_idBox),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 *            E carregamento dos valores (se não foi enviado e possui acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {

			// Form com erros
			if ($formCodigo == 1) {

				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {

				$idBox = $form->campoValorEnviado('idBox');
				$codBarras = $form->campoValorEnviado('codBarras');

				if (!empty($codBarras) && !empty($idBox)) {

					$retorno = $EST002->separarItemPedidoProdutoBox($idBox, $codBarras);

					if ($retorno['sucesso']) {

						if ($EST002->isPedidoSeparadoBoxLeitor($retorno['T005_Id'])) {

							$sqlEtiqueta  = " SELECT D175.D175_Nome ";
							$sqlEtiqueta .= "   FROM D175 ";
							$sqlEtiqueta .= "  WHERE D175.D175_Tipo_Impressora = 6 ";
							$sqlEtiqueta .= "  ORDER BY D175.D175_Id ASC ";
							$sqlEtiqueta .= "  LIMIT 1 ";
							$fetchEtiqueta = mysql_query($sqlEtiqueta);
							$dadosEtiqueta = mysql_fetch_array($fetchEtiqueta);
							$impressoraEtiqueta = $dadosEtiqueta['D175_Nome'];

							echo <<<EOT

							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

								divRefresh('{$r_divGrid}'); 

								let digitacaoOk = false;
								let procDTO = {};
								procDTO.T005_Id = {$retorno['T005_Id']};

								while (!digitacaoOk) {
									let qtdeVolumes = prompt("Informe o número de volumes");

									if (qtdeVolumes === null) {
										alert("Cancelado o faturamento do pedido " + procDTO.T005_Id);
										digitacaoOk = true;
									} else {
										if (isNaN(qtdeVolumes) || qtdeVolumes <= 0 || qtdeVolumes > 99) {
											alert('Quantidade de volumes inválida!');
										} else {
											digitacaoOk = true;

											procDTO.idProgressBar = dialogAlert('Processando faturamento do pedido ' + procDTO.T005_Id ,"<span id='progressBar'><b><br>Finalizando separação do pedido</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

											$.getJSON('/est/est002/form_func-ajax/fatPedidoSeparacaoBoxLeitor?ajax=true&T005_Id=' + procDTO.T005_Id + '&T005_Quantidade_Volumes=' + qtdeVolumes + '&callback=?', function(request) {
												validarGeracaoNF(procDTO);
											});
										}
									}
								};

								function procStatus(etapaProcessamento, procDTO) {

									let arrayProc = ['Finalizando separação do pedido', 'Validação formulário', 'Processamento dos produtos do pedido', 'Geração da Nota Fiscal', 'Processamento dos produtos da Nota Fiscal', 'Transmissão da Nota Fiscal', 'Impressão DANFE', 'Impressão etiqueta transporte (Até 2min)'];
									let htmlProc = '';

									if (typeof procDTO.T005_Id !== 'undefined') {
										arrayProc[0] += ': ' + procDTO.T005_Id;
									}

									if (typeof procDTO.T007_Numero_Nota_Fiscal !== 'undefined') {
										arrayProc[3] += ' - Número: ' + procDTO.T007_Numero_Nota_Fiscal;
									}

									for (i=0;i<etapaProcessamento;i++) {
										htmlProc += '<b><font color="green">' + arrayProc[i] + ' &#10004;</font></b><br>';
									}

									if (arrayProc.length >= etapaProcessamento+1) {
										htmlProc += '<br><b>' + arrayProc[etapaProcessamento] + '</b><br>';
									}

									$('#progressBar').html(htmlProc);
								}

								function fimProcSucesso(procDTO) {

									$('#dc-' + procDTO.idProgressBar).dialog("destroy").remove();
									$('#{$g['divId']}').hideLoading();

									divRefresh('{$r_divGrid}'); 
									divRefresh('{$g['divId']}');
									$('#codBarras').focus();
								}

								function fimProcErro(procDTO, mensagemErro) {

									$('#dc-' + procDTO.idProgressBar).dialog("destroy").remove();
									$('#{$g['divId']}').hideLoading();

									if (typeof procDTO.T005_Id !== 'undefined') {
										mensagemErro += "<b><br><br> Número do Pedido: " + procDTO.T005_Id + "</b>";
									}									

									if (typeof procDTO.T007_Numero_Nota_Fiscal !== 'undefined') {
										mensagemErro += "<b><br> Número da Nota Fiscal: " + procDTO.T007_Numero_Nota_Fiscal + "<br></b>";
									}

									let \$buttons = { "Fechar": function() { 
										                        	$(this).dialog("destroy").remove();
																	divRefresh('{$r_divGrid}'); 
																	divRefresh('{$g['divId']}');
																	$('#codBarras').focus();
																} 
													}

									dialogConfirm('Falha no processamento', mensagemErro, \$buttons);
								}

								function validarGeracaoNF(procDTO) {

									procStatus(1, procDTO); // Validação formulário

									/* Este método (gerarNFValidacaoFormularioBoxLeitor) valida os dados das origens da NF a ser gerada (T005/T006/T009) */
									$.getJSON('/ven/ven012/form_func-ajax/gerarNFValidacaoFormularioBoxLeitor?ajax=true&T005_Id=' + procDTO.T005_Id + '&callback=?', function(request) {
										if (request.code) {
											procStatus(2, procDTO); // Processamento do pedido
											ven012TotalizarPedidoLoop(1, procDTO);
										}else{
											let mensagem = '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">'+request.data+request.erroRoolback+'</textarea>';
											fimProcErro(procDTO, mensagem);
										} 
									});
								}

								function ven012TotalizarPedidoLoop(inicio, procDTO){
									/* Este método (gerarNFTotalizarPedido) gerencia o cache para processar na tela o status de progressão da validação do formulário */
									$.getJSON('/ven/ven012/form_func-ajax/gerarNFTotalizarPedido?ajax=true&T005_Id=' + procDTO.T005_Id + '&inicio=' + inicio + '&callback=?', function(request) {
										if (request.concluido == 0) {
											ven012TotalizarPedidoLoop(0, procDTO);
										}else{
											if(request.code){
												gerarNotaFiscalBoxLeitor(procDTO);
											}else{
												let mensagem = "<b><font color='red'>Erro na totalização dos produtos do pedido (" + request.data + ")</font></b>";
												fimProcErro(procDTO, mensagem);
											}	
										} 
									});
								}

								function gerarNotaFiscalBoxLeitor(procDTO) {

									procStatus(3, procDTO); // Geração da Nota Fiscal

									/* Este método (gerarNotaFiscalBoxLeitor) é o que gera a nota - inserindo registros em T007/T007/etc. */
									$.getJSON('/ven/ven012/form_func-ajax/gerarNotaFiscalBoxLeitor?ajax=true&T005_Id=' + procDTO.T005_Id + '&callback=?', function(request) {

										if(request.code){
											procDTO.T007_Id = request.T007_Id;
											procDTO.T007_Numero_Nota_Fiscal = request.T007_Numero_Nota_Fiscal;
											procStatus(4, procDTO); // Processamento dos produtos da Nota Fiscal
											ven012TotalizarLoop(1, procDTO);
										} else {
											let mensagem = request.data;
											fimProcErro(procDTO, mensagem);
										}
									});
								}

								function ven012TotalizarLoop(inicio, procDTO){

									/* Este método (gerarNFTotalizar) gerencia o cache para processar na tela o status de geração da NF */
									$.getJSON('/ven/ven012/form_func-ajax/gerarNFTotalizar?ajax=true&T005_Id=' + procDTO.T005_Id + '&inicio=' + inicio + '&callback=?', function(request) {

										if (request.concluido == 0) {
											ven012TotalizarLoop(0, procDTO); 
										}else{
											if(request.code){
												transmitirNFBoxLeitor(procDTO);
											} else {
												let mensagem = "<b><font color='red'>Erro na totalização dos produtos da nota</font></b>";
												fimProcErro(procDTO, mensagem);
											}
										}
									});
								}

								function transmitirNFBoxLeitor(procDTO){

									procStatus(5, procDTO); // Transmissão da Nota Fiscal

									$.getJSON('/ven/ven012/form_func-ajax/transmitirNFBoxLeitor?ajax=true&T005_Id=' + procDTO.T005_Id + '&T007_Id=' + procDTO.T007_Id + '&callback=?', function(request) {

										if (request.sucesso) {
											impDanfeSimplificada(procDTO);
										} else {
											let mensagem = '';

											if (request.logTransmissao) {
												mensagem = "<b><font color='red'>Falha ao transmitir NF. <br> Log: " + request.logTransmissao + "</font></b>";
											} else {
												mensagem = "<b><font color='red'>Falha ao transmitir NF. <br> Nenhum log foi gerado</font></b>";
											}

											fimProcErro(procDTO, mensagem);
										}

										if (request) {
											console.log('Proc NF-e: ' + JSON.stringify(request));
										}

									}).fail(function(jqXHR, textStatus, errorThrown) {
										let mensagem = "<b><font color='red'>Ocorreu um erro ao transmitir NF</font></b>";
										fimProcErro(procDTO, mensagem);
									});
								}

								function impDanfeSimplificada(procDTO) {
									procStatus(6, procDTO); // Impressão DANFE

									console.log('Imprimindo danfe simplificada de NF id: ' + procDTO.T007_Id);

									$.ajax({
										url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
										type: 'GET',
										dataType: 'jsonp',
										data: {
											'ajax': 'true',
											'acaoId': '900af79255d13da53e5e04418e52a3c5',
											'link': '/sistema/personalizacao/tela/900af79255d13da53e5e04418e52a3c5/?ajax=true&acaoId=' + procDTO.T007_Id,
										},
										success: function(resposta) {
											printSMT(resposta.data, '{$impressoraEtiqueta}');
											impEtiquetaTransporte(procDTO);
										}
									});
								}

								function impEtiquetaTransporte(procDTO) {

									procStatus(7, procDTO); // Impressão etiqueta transporte

									$.getJSON('/ven/ven012/form_func-ajax/getEtiquetaTransporteBoxLeitor?ajax=true&T005_Id=' + procDTO.T005_Id + '&T007_Id=' + procDTO.T007_Id + '&callback=?', function(request) {

										procDTO.tpEtiqueta = request.tpEtiqueta;
										procDTO.T007_Quantidade_Volumes = request.T007_Quantidade_Volumes;

										switch (procDTO.tpEtiqueta) {
											case 'ML':
												impEtiquetaTranspML(procDTO, request.urlEtiquetaML, request.semEtiqueta);
												break;
											case 'PD':
												impEtiquetaTranspPadrao(procDTO);
												break;
											default:
												impEtiquetaControleInterno(procDTO);
										}
									});
								}

								function impEtiquetaControleInterno(procDTO) {

									$.ajax({
										url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
										type: 'GET',
										dataType: 'jsonp',
										data: {
											'ajax': 'true',
											'acaoId': 'c505635934c183fe30c0f3fbd90662cd',
											'link': '/sistema/personalizacao/tela/c505635934c183fe30c0f3fbd90662cd/?ajax=true&T007_Id=' + procDTO.T007_Id,
										},
										success: function(resposta) {
											printSMT(resposta.data, '{$impressoraEtiqueta}');
											setPedidoFlagEtiquetaTransporteImpressa(procDTO);
											fimProcSucesso(procDTO);
										}
									});
								}

								function impEtiquetaTranspPadrao(procDTO) {

									$.ajax({
										url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
										type: 'GET',
										dataType: 'jsonp',
										data: {
											'ajax': 'true',
											'acaoId': 'ab71e3720825c0214e7e11861a3e4f29',
											'link': '/sistema/personalizacao/tela/ab71e3720825c0214e7e11861a3e4f29/?ajax=true&T005_Id=' + procDTO.T005_Id,
										},
										success: function(resposta) {

											execImpPad(1);

											function execImpPad(count) {
												if (count <= procDTO.T007_Quantidade_Volumes) {
													setTimeout(function(){
														console.log('Imprimindo etiqueta padrão do pedido id: ' + procDTO.T005_Id + ' (' + count + '/' + procDTO.T007_Quantidade_Volumes + ')');
														printSMT(resposta.data, '{$impressoraEtiqueta}');
														execImpPad(count+1);
													}, 500);
												} else {
													setTimeout(function(){
														setPedidoFlagEtiquetaTransporteImpressa(procDTO);
														fimProcSucesso(procDTO);
													}, 500);
												}
											}
										}
									});
								}

								function impEtiquetaTranspML(procDTO, urlEtiquetaML, semEtiqueta) {

									if (semEtiqueta == '') {
										execImpML(1);
									} else {
										console.log('Não foi possível buscar a etiqueta do ML para imprimir!');
										fimProcSucesso(procDTO);
									}

									function execImpML(count) {
										if (count <= procDTO.T007_Quantidade_Volumes) {
											setTimeout(function(){
												printSMT(urlEtiquetaML, '{$impressoraEtiqueta}');
												console.log('Imprimindo etiqueta ML do pedido id: ' + procDTO.T005_Id + ' (' + count + '/' + procDTO.T007_Quantidade_Volumes + ')');
												execImpML(count+1);
											}, 500);
										} else {
											setTimeout(function(){
												setPedidoFlagEtiquetaTransporteImpressa(procDTO);
												fimProcSucesso(procDTO);
											}, 500);
										}
									}
								}

								function setPedidoFlagEtiquetaTransporteImpressa(procDTO) {
			
									$.getJSON('/est/est002/form_func-ajax/setPedidoFlagEtiquetaTransporteImpressa?ajax=true&T005_Id=' + procDTO.T005_Id + '&callback=?', function(request) {
										console.log('Baixando status de impressao de etiqueta para o pedido ' + T005_Id);
										console.log(request);
									});
								}

							} </script></div>
EOT;
						} else {
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$r_divGrid}'); 
								divRefresh('{$g['divId']}');
								$('#codBarras').focus();
							} </script></div>
EOT;
						}
					} else {
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

								$('#{$g['divId']}').hideLoading();

								let \$buttons = { "Fechar": function() { 
																$(this).dialog("destroy").remove();
																divRefresh('{$r_divGrid}'); 
																$('#codBarras').focus();
																$('#codBarras').select();
															} 
												}

								dialogConfirm('Falha na operação', '{$retorno['msgErro']}', \$buttons);

							} </script></div>
EOT;
					}
				} else {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							alert("Informe um código de barras para prosseguir com a separação do box {$idBox}!");
							divRefresh('{$r_divGrid}'); 
							$('#codBarras').focus();
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
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) {
			$g["smarty"]->assign("formId", $formId);
		}
		$g['smarty']->display('formEditor.tpl');

	break;
	
	case 'est002VolumesConferenciaFinalizar':
		$acaoId   = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$divGrid = (isset($_REQUEST['divGrid'])) ? $_REQUEST['divGrid'] : false;

		  /**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;

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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário

		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est002VolumesConferenciaFinalizar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est002/form/est002VolumesConferenciaFinalizar/';

		//form editor
		$parcial = $_REQUEST['parcial'];
		$total   = $parcial == 0 ? true : false;

		$form_editor = array('geral' => '{"D047_Id":{"top":7,"left":15.484375,"width":317,"hidden":0},"D109_Id_Primeiro":{"top":30,"left":79.5,"width":319,"hidden":0},"D109_Id_Segundo":{"top":53,"left":79.5,"width":319,"hidden":0}}');
		$Todos       = array('title' => '...', 'value' => '0');
		$motivos     = gGeraSelect('D047', 'D047_Id', 'D047_Descricao_Motivo', " WHERE D047_Flag_Conferencia = 'S' ");
		$separador   = gGeraSelect('D109', 'D109_Id', 'D109_Codigo_Barras, D109_Nome', "WHERE D109_Flag_Ativo = 'S' ORDER BY D109_Nome");
		array_unshift($motivos, $Todos);
		array_unshift($separador, $Todos);
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005_Id'          => array('name' => 'T005_Id',          'label' => 'T005_Id',                  'type' => 'hidden', 'value'  => $acaoId),
					'D047_Id'          => array('name' => 'D047_Id',          'label' => 'Motivo separação parcial', 'type' => 'select', 'select' => $motivos, 'disabled' => $total),
					'D109_Id_Primeiro' => array('name' => 'D109_Id_Primeiro', 'label' => 'Separador 1',              'type' => 'select', 'select' => $separador),
					'D109_Id_Segundo'  => array('name' => 'D109_Id_Segundo',  'label' => 'Separador 2',              'type' => 'select', 'select' => $separador),
				)
			)
		);
		$form = new Formulario('est002VolumesConferenciaFinalizar');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */
	
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				$mensagem         = '';
				$T005_Id          = $form->campoValorEnviado('T005_Id');
				$D047_Id          = $form->campoValorEnviado('D047_Id');
				$D109_Id_Primeiro = $form->campoValorEnviado('D109_Id_Primeiro');
				$D109_Id_Segundo  = $form->campoValorEnviado('D109_Id_Segundo');

				if (!$total and $D047_Id == 0) {
					$mensagem .= '<br>Motivo da conferência parcial não informado.';
				}

				if ($D109_Id_Primeiro == 0) {
					$mensagem .= '<br>Separador 1 não informado.';
				}

				if ($D109_Id_Segundo == 0) {
					$mensagem .= '<br>Separador 2 não informado.';
				}

				if (empty($mensagem)) {
					$retorno = $EST002->volumesConferenciaFinalizar($T005_Id, $D047_Id, $D109_Id_Primeiro, $D109_Id_Segundo);
						if ($retorno !== true) {
							echo gGeraAlertMsg('Erro!', 'Não é possível finalizar a conferência.<br>' . $retorno);
							break;
						}

					$sqlEtiqueta  = " SELECT D175.D175_Nome ";
					$sqlEtiqueta .= "   FROM D175 ";
					$sqlEtiqueta .= "  WHERE D175.D175_Tipo_Impressora = 6 ";
					$sqlEtiqueta .= "  ORDER BY D175.D175_Id ASC ";
					$sqlEtiqueta .= "  LIMIT 1 ";
					$fetchEtiqueta = mysql_query($sqlEtiqueta);
					$dadosEtiqueta = mysql_fetch_array($fetchEtiqueta);
					$impressoraEtiqueta = $dadosEtiqueta['D175_Nome'];

					$volumes = array();
					$sqlVolumes   = " SELECT T314_Id ";
					$sqlVolumes  .= "   FROM T314 ";
					$sqlVolumes  .= "  WHERE T314.T314_T005_Id = '{$T005_Id}' ";
					$sqlVolumes  .= "  ORDER BY T314.T314_Sequencia ASC ";
					$queryVolumes = mysql_query($sqlVolumes);
					while ($volume = mysql_fetch_array($queryVolumes)) {
						array_push($volumes, $volume['T314_Id']);
					}

					$listaVolumes = json_encode($volumes);

					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						fecharJanela("{$g['divId']}");
						fecharJanela('{$g['divIdParent']}');
						divRefresh('{$divGrid}');
						// imprimirEtiquetasPedido({$listaVolumes});
						imprimirEtiquetaPedido('{$T005_Id}');

						function imprimirEtiquetaPedido(T005_Id) {
							window.open('/est/est002/report/etiquetaVolume/?ajax=true&T005_Id=' + T005_Id, '_blank');
						}

						function imprimirEtiquetasPedido(listaVolumes) {
							listaVolumes.forEach(imprimirEtiquetaVolume);
						}

						function imprimirEtiquetaVolume(item) {
							$.ajax({
								url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
								type: 'GET',
								dataType: 'jsonp',
								data: {
									'ajax': 'true',
									'acaoId': item,
									'link': '/est/est002/report/etiquetaVolume/?ajax=true&T314_Id=' + item
								},
								success: function(resposta) {
									printSMT(resposta.data, '{$impressoraEtiqueta}');
								}
							});
						}
					} </script></div>
EOT;
					echo gGeraAlertMsg('Operação OK', 'Conferência finalizada com sucesso.');
				} else {
					echo gGeraAlertMsg('Erro!', 'Não é possivel finalizar a conferência.<br>' . $mensagem);
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
		$botoesDir .= '<button type="submit" id="est002VolumesConferenciaFinalizar_btn">Finalizar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

}
