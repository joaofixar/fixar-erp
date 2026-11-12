<?php
namespace hardness;
/**
* Weiberlan
* 08/11/2011
*/
/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Grid Principal
	* Chamado em: Menu
	*/
	case 'ven002content01':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/ven/ven002/tab/ven002tab01/')
		);
		echo gProcessaAcoes($actions);

	break;

	/**
	* Form Principal
	* Chamado em: ven002grid01
	*/
	case 'ven002content02':
        
		$sql="select T005_Id from T005 where T005_Id='{$r_acaoId}'";
		$consultaT005_Id=mysql_query($sql);
		if (!empty($r_acaoId) && mysql_num_rows($consultaT005_Id)<=0) {
			$alert = gGeraAlertMsg('Erro', 'Este pedido não existe!', false);
			echo <<<EOT
		   		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		   			$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							{$alert}
						}, 150);
					});
					$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
		   		} </script></div>
EOT;
			die();
		}

		// Limpando variável T006_Codigo_Local (ambiente) utilizada para memorizar último ambiente incluído no último produto do orçamento
		echo <<<EOT
			<script type="text/javascript">
				var T006_Codigo_Local = '';
			</script>
EOT;

			require_once('bibliotecas/classes/NOTIFICACOES.php');
			$NOTIFICACOES = new NOTIFICACOES();

			require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
			$WMS = new WMS();

			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');

			$T005A    = mysql_query("SELECT T005A_Id FROM T005A WHERE T005A_T005_Id = '{$r_acaoId}'");
			$mT005A   = mysql_fetch_array($T005A);
			$T005A_Id = $mT005A['T005A_Id'];

			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/ven/ven002/tab/ven002tab02/', '&acaoId=' . urlencode($r_acaoId) . '&divIdMain=' . urlencode($content1). '&T005A_Id=' . urlencode($T005A_Id)),
			);
			echo gProcessaAcoes($actions);
			
				$T005 = mysql_query("SELECT T005_Flag_Primeira_Liberacao_Credito, T005_C007_Id_Vendedor_Interno, T005_Flag_Status, T005_Flag_Expedicao, T005_Flag_ACP, T005_Flag_Reenviado_Almoxarifado, T005_Flag_Reenviado_Almoxarifado_Lido, T005_Flag_Notificacao_Financeiro, T005_Flag_Notificacao_Liberacao, T005_Flag_Notificacao_Estoque,T005_Flag_Plataforma_Ecommerce, T005_D013_Id
					from T005 where T005_Id='{$r_acaoId}'");
			$mT005 = mysql_fetch_array($T005);

			$D013_Id = $mT005['T005_D013_Id'];
			
			//Remoção das notificações do pedido
			if($mT005['T005_C007_Id_Vendedor_Interno'] == $g['usuarioAtual']){
				//Retorno de almoxarifado
				if($mT005['T005_Flag_Reenviado_Almoxarifado_Lido'] == 'N' && $mT005['T005_Flag_Reenviado_Almoxarifado'] == 'S'){
					echo gGeraAlertMsg('Atenção', 'Este pedido retornou do almoxarifado.');
					mysqli_query("UPDATE T005 SET T005_Flag_Reenviado_Almoxarifado_Lido = 'S' WHERE T005_Id = '{$r_acaoId}'");
					//$NOTIFICACOES->pedidoRetornoAlmoxarifado();
				}

				//Analise de credio
				if($mT005['T005_Flag_Notificacao_Financeiro'] != ''){
					if($mT005['T005_Flag_Notificacao_Financeiro'] == 'S'){
						mysqli_query("UPDATE T005 SET T005_Flag_Notificacao_Financeiro = '' WHERE T005_Id = '{$r_acaoId}'");
						//$NOTIFICACOES->pedidoCreditoLiberado();
						echo gGeraAlertMsg('Atenção', 'Este pedido foi <b>aprovado</b> na análise de crédito.');
					} else if($mT005['T005_Flag_Notificacao_Financeiro'] == 'N'){
						mysqli_query("UPDATE T005 SET T005_Flag_Notificacao_Financeiro = '' WHERE T005_Id = '{$r_acaoId}'");
						//$NOTIFICACOES->pedidoCreditoNaoLiberado();
						echo gGeraAlertMsg('Atenção', 'Este pedido foi <b>negado</b> na análise de crédito.');
					}
				}

				//Analise de pedido
				if($mT005['T005_Flag_Notificacao_Liberacao'] != ''){
					if($mT005['T005_Flag_Notificacao_Liberacao'] == 'S'){
						mysqli_query("UPDATE T005 SET T005_Flag_Notificacao_Liberacao = '' WHERE T005_Id = '{$r_acaoId}'");
						//$NOTIFICACOES->pedidoLiberado();
						echo gGeraAlertMsg('Atenção', 'Este pedido foi <b>aprovado</b> na análise comercial.');
					} else if($mT005['T005_Flag_Notificacao_Liberacao'] == 'N'){
						mysqli_query("UPDATE T005 SET T005_Flag_Notificacao_Liberacao = '' WHERE T005_Id = '{$r_acaoId}'");
						//$NOTIFICACOES->pedidoNaoLiberado();
						echo gGeraAlertMsg('Atenção', 'Este pedido foi <b>negado</b> na análise comercial.');
					}
				}

				$mensagem = "Chegou <b>estoque</b> para o pedido pendente <b>{$r_acaoId}</b>.";
				mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Mensagem = '{$mensagem}' AND N001_Flag_Excluido='N'");

				//Entrada de mercadoria
				if($mT005['T005_Flag_Notificacao_Estoque'] == 'S'){
					echo gGeraAlertMsg('Atenção', 'Entrou <b>estoque</b> para este pedido.');
					mysqli_query("UPDATE T005 SET T005_Flag_Notificacao_Estoque = 'N' WHERE T005_Id = '{$r_acaoId}'");
					//$NOTIFICACOES->pedidoEntradaEstoque();
				}
			}

			$disabled2 = false;
			$disabledFaturar = false;
			if($mT005['T005_Flag_Status'] > 0 
			AND $mT005['T005_Flag_Status']!=14 
			AND $mT005['T005_Flag_Status']!=15 
			AND $mT005['T005_Flag_Status']!=9 
			//AND $mT005['T005_Flag_Status']!=26 Se está vencido, precisa reativar o pedido
			AND $mT005['T005_Flag_Status']!=22)
			{
				$disabled2 = true;
				$disabledFaturar = true;
			}

			if(isset($g['c029Ids'][299])){
				$disabledFaturar = true;
			}
			// O botão acompanha somente as restrições normais de edição do pedido.
			// A operação e o tipo fiscal são definidos dentro da janela.
			$disabledNFDC = $disabled2;


			$botoes = array(
				array('titulo' => 'Faturar', 'id' => 'ven002FaturarPedido', 'desabilitar' => $disabledFaturar),
				array('titulo' => 'Desconto', 'id' => 'ven002AplicarDesconto', 'exibirCallback' => 'return ($g["C031"]["botaoAplicarDesconto"] == "S");', 'desabilitar' => $disabled2),
				array('titulo' => 'Faturar Emergência', 'id' => 'ven002FaturarEmergencia', 'exibirCallback' => 'return ($g["C031"]["permissaoFaturarEmergencia"] == "S" && gTemPermissao(87));', 'desabilitar' => $disabled2),
				array('titulo' => 'Importar', 'id' => 'ven002ImportarItens', 'desabilitar' => $disabled2),
				array('titulo' => 'Atualizar', 'id' => 'ven002AtualizarOpcoes', 'desabilitar' => $disabled2),
				array('titulo' => 'E-mail', 'id' => 'ven002EnviarEmail'),
				array('titulo' => 'WhatsApp', 'id' => 'ven002EnviarWhats'),
				array('titulo' => 'Imprimir', 'id' => 'ven002ImprimirPedido'),
				array('titulo' => 'Reativar', 'id' => 'ven002ReativarPedido', 'exibirCallback' => "return ({$mT005['T005_Flag_Status']} == 26);"),
				array('titulo' => 'Compras', 'id' => 'ven002Compras'),
				array('titulo' => 'NF Débito/Crédito', 'id' => 'ven002NFDebitoCredito', 'desabilitar' => $disabledNFDC),
				array('titulo' => 'Pedir Nova Liberação', 'id' => 'ven002NovaLiberacao', 'exibirCallback' => "return {$mT005['T005_Flag_Status']} == 9 || (\$g['C031']['verificaCreditoLiberadoGerarPedido'] == 'S' && {$mT005['T005_Flag_Status']} == '0' && {$mT005['T005_Flag_Primeira_Liberacao_Credito']} == '0');"),
				array('titulo' => 'DAV', 'id' => 'ven002imprimirDav', 'exibirCallback' => "return {$mT005['T005_Flag_ACP']} == 4;"),
				array('titulo' => 'Pluggto', 'id' => 'ven002AtualizarPluggto', 'exibirCallback' => "return (\$g['C031']['plataformaECommerce']=='pluggto' || \$g['C031']['plataformaECommerce2']=='pluggto') && {$mT005['T005_Flag_Plataforma_Ecommerce']} == 1 && empty('{$mT005['T005_Id_Pedido_Externo']}');"),
				array('titulo' => 'Tray', 'id' => 'ven002AtualizarTray', 'exibirCallback' => "return (\$g['C031']['plataformaECommerce']=='tray' || \$g['C031']['plataformaECommerce2']=='tray') && {$mT005['T005_Flag_Plataforma_Ecommerce']} == 2 && empty('{$mT005['T005_Id_Pedido_Externo']}');"),
				array('titulo' => 'Magento', 'id' => 'ven002AtualizarMagentoSoap', 'exibirCallback' => "return (\$g['C031']['plataformaECommerce']=='magentoSoap' || \$g['C031']['plataformaECommerce2']=='magentoSoap') && {$mT005['T005_Flag_Plataforma_Ecommerce']} == 3;"),
				array('titulo' => 'Intelipost', 'id' => 'ven002EnvioIntelipost', 'exibirCallback' => 'return ($g["C031"]["IntelipostPlataformaEnviar"]==2);'),
				array('titulo' => 'Dividir', 'id' => 'ven002DividirPedidoBtn', 'exibir' => false),
				array('titulo' => 'Fechar', 'tipo' => 'codigojs', 'callback' => "function callback($array) { return 'fecharJanela(\'".$g['divId']."\');'; }", 'posicao' => 'direita')
			);

			if($WMS->isEnabled()) {
				$disabled = $WMS->isWarehouseUser() ? '' : 'disabled';

				$botoes[] = array('titulo' => 'Enviar Pedido WMS', 'id' => 'ven002EnviarParaWMS', 'desabilitar' => $disabled);
			}


			echo Botoes::processar($botoes, 'content');

			$usarAlcada = $g['C031']['usarAlcada'];
			$novoIdTelaForm = uniqid();

			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven002Teste').bind('click', function(data) {
					mp2032PreCarregarApplet();
					var buttons = {
						"Sim": function() {
							$.ajax({
								url: '/sistema/impressao/cupom/romaneioestoque/',
								dataType: 'html',
								data: 'ajax=true&T005_Id=' + encodeURIComponent('{$r_acaoId}'),
								type: 'post',
								success: function(response) {
									eval(response);
								}
							});
							$(this).dialog("destroy").remove();
					 	},
						"Não": function() {
							$(this).dialog("destroy").remove();
						}
					};
					dialogConfirm('Impressão', 'Confirmar Impressão?', buttons);
				});
				$('#ven002AplicarIpv').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar IPV', '/ven/ven002/content/ven002ContentAplicarIpv/', '&tab=geral&T005_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
				});
				$('#ven002ImportarItens').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar Ítens', '/ven/ven002/content/ven002ContentImportarItens/', '&tab=geral&T005_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
				});
				$('#ven002NFDebitoCredito').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar NF de Débito ou Crédito', '/ven/ven002/content/ven002ContentNFDebitoCredito/', '&acaoId=' + encodeURIComponent("{$r_acaoId}"), [1100,650]);
				});
				$('#ven002AplicarDesconto').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/ven/ven001/content/ven001ContentAplicarDesconto/', '&tab=geral&idTabelaCabecalho=' + encodeURIComponent("{$r_acaoId}") + '&tabelaCabecalho=T005&tabelaProduto=T006', [600,200]);
				});
				$('#ven002DividirPedidoBtn').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Pedido', '/ven/ven012/content/ven012ContentDividirPedido/', '&tab=geral&T005_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
				});
				$('#ven002ImprimirPedido').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&acao=imprimir&callback=?', function(request) {
						 $('#{$g['divId']}').hideLoading();
						 if (request.code === true) {
						 	$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/ven002VerificarMoedaImpressao/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if(request.code === true){
									imprimirPedido(1);
								} else if(request.code == 'verificarMoeda'){
									dialogConfirm('Imprimir Pedido', "Selecione a moeda para impressão", [
										{
											text: "REAL",
											click: function() {
											  	imprimirPedido(1);
											  	$(this).dialog("destroy").remove(); 
												return false;
											}
										},
										{
											text: request.moeda,
											click: function() {
											  	imprimirPedido(request.data);
											  	$(this).dialog("destroy").remove(); 
												return false;
											}
										},
										{
											text: "Cancelar",
											click: function() {
											  	$(this).dialog("destroy").remove(); 
											}
										}
									]);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
							});
						 }else if(request.code == 'erro') {
								var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Enviar E-mail', request.data, \$buttons);
						 } else {
							var \$buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$(this).dialog("destroy").remove();
									$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=imprimir' + '&callback=?', function(request2) {
										$('#{$g['divId']}').hideLoading();
										if (request2.code) {
											var \$buttons = {
												 "Fechar": function() {
													$(this).dialog("destroy").remove();
													fecharJanela('{$g['divId']}');
													divRefresh('{$g['divIdParent']}', true);
												 } 
											};
											dialogConfirm('Ok', request2.data, \$buttons);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request2.data, \$buttons);
										}
									});
								},
								"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Enviar E-mail', request.data, \$buttons);
						 }
					 });
				});

				$('#ven002EnviarWhats').bind('click', function(data) {
					$.getJSON('/ven/ven002/form_func-ajax/verificarParcelas/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request1) {
						if (request1.code === true) {
							$.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&acao=enviarEmail&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code == true) {
									$.getJSON('/ven/ven002/form_func-ajax/ven002PhoneWhats/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request1) {
										dialogConfirm('Enviar pedido WhatsApp: ' + decodeURIComponent(request1.phoneWhatsApp), "O que deseja fazer? " + decodeURIComponent(request1.notificaSemPhone) , [
											{
												text: "Enviar formato texto",
												click: function() {
													$.getJSON('/ven/ven002/form_func-ajax/pedidoWhats/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&phone=' + decodeURIComponent(request1.phoneWhatsApp) + '&callback=?', function(request) {
														nova_janela('https://api.whatsapp.com/send?phone=' + request1.phoneWhatsApp + '&text=' + request.data,0,0,750,500,'yes','no','no','no','yes','no','WhatsApp');
													});
													$(this).dialog("destroy").remove();
													return false;
												}
											},
											{
												text: "Enviar link PDF",
												click: function() {
													$.getJSON('/ven/ven002/form_func-ajax/pedidoWhatsPDF/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&phone=' + decodeURIComponent(request1.phoneWhatsApp) + '&callback=?', function(request) {
														if (request.code){
															nova_janela('https://api.whatsapp.com/send?phone=' + request1.phoneWhatsApp + '&text=' + request.data,0,0,750,500,'yes','no','no','no','yes','no','WhatsApp');
														}else{
															dialogConfirm('Erro', request.data);
														}
													});
													$(this).dialog("destroy").remove();
													return false;
												}
											},
EOT;
											if (!empty($D013_Id)) {
												echo <<<EOT
												{
													text: "Atualizar celular contato",
													click: function() {
														abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Alterar contato', '/cad/cad001/content/cad001ContentAlterarContato/', '&tab=geral&acaoId=' + encodeURIComponent("{$D013_Id}"), [600,300]);
														$(this).dialog("destroy").remove();
														return false;
													}
												},
EOT;
											}
											echo <<<EOT
											{
												text: "Cancelar",
												click: function() {
													$(this).dialog("destroy").remove();
												}
											}
										]);
									});												
								}else if(request.code == 'erro') {
									   var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
									   dialogConfirm('Enviar E-mail', request.data, \$buttons);
								}else{
								   var \$buttons = {
									   "Sim": function() {
										   $('#{$g['divId']}').showLoading();
										   $(this).dialog("destroy").remove();
										   $.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=email' + '&callback=?', function(request2) {
											   $('#{$g['divId']}').hideLoading();
											   if (request2.code) {
												   var \$buttons = {
														"Fechar": function() {
														   $(this).dialog("destroy").remove();
														   fecharJanela('{$g['divId']}');
														   divRefresh('{$g['divIdParent']}', true);
														} 
												   };
												   dialogConfirm('Ok', request2.data, \$buttons);
											   } else {
												   var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
												   dialogConfirm('Erro', request2.data, \$buttons);
											   }
										   });
									   },
									   "Não": function() { $(this).dialog("destroy").remove(); } 
								   };
								   dialogConfirm('Enviar E-mail', request.data, \$buttons);
								}
							});
						}else{
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    	dialogConfirm('Erro', request1.data, buttons);
				    	}		
					});
				});

				function imprimirPedido(moeda){
					$('#{$g['divId']}').showLoading();
				 	$.getJSON('/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&moeda=' + encodeURIComponent(moeda) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if(request.code===true) {
							var w = window.open(request.data);
							w.addEventListener("load", function(event) {
								setTimeout(function() {
									$.getJSON('/ven/ven002/form_func-ajax/ven002DeletarPDFPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {});
								}, 3000);
							});
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    					dialogConfirm('Erro', request.data, \$buttons);
						}
					});
				}
				
				$('#ven002AtualizarAliquotas').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T005_Id = $('#{$g['divId']} #T005_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
				});

				$('#ven002AtualizarPluggto').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T005_Id = $('#{$g['divId']} #T005_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/atualizarPedidoExterno/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atualizar dados', 'Atualizar dados do faturamento do pedido?  ', \$buttons);
				});

				$('#ven002AtualizarMagentoSoap').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T005_Id = $('#{$g['divId']} #T005_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/atualizarPedidoExternoMagento/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atualizar dados', 'Atualizar dados do faturamento do pedido?  ', \$buttons);
				});

				$('#ven002EnvioIntelipost').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T005_Id = $('#{$g['divId']} #T005_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/criarPedidoIntelipost/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Enviado', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Enviar dados', 'Enviar dados do pedido para intelipost?  ', \$buttons);
				});

				$('#ven002AtualizarTray').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T005_Id = $('#{$g['divId']} #T005_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/atualizarPedidoTray/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atualizar dados', 'Atualizar dados do faturamento do pedido?  ', \$buttons);
				});

				$('#ven002imprimirDav').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven002/form_func-ajax/imprimirDav/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								window.open(request.data);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons);
							}
						});
					});
				
				$('#ven002FaturarEmergencia').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Faturar Emergência', '/ven/ven002/content/ven002ContentFaturarEmergencia/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [450,180]);
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Faturar Emergência?', 'Este pedido será encaminhado direto para emissão de NFe!', \$buttons);
				});
				
				$('#ven002ReativarPedido').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$.getJSON('/ven/ven002/form_func-ajax/reativarPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									if (request.code) {
										divRefresh('{$g['divId']}');
									}
								});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Reativar Pedido?', 'Deseja reativar o pedido?', \$buttons);
				});

				$('#ven002NovaLiberacao').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$.getJSON('/ven/ven002/form_func-ajax/novaLiberacao/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									//divRefresh('{$g['divId']}');
									$('#{$g['divId']}').dialog('refresh');
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Nova Liberação', 'Deseja pedir uma nova liberação para este pedido?', \$buttons);
				});
				
				$('#ven002FaturarPedido').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
				    $.getJSON('/ven/ven002/form_func-ajax/validarCamposNFe/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
				    	$('#{$g['divId']}').hideLoading();
				    	if (request.code === false) {
				    		var buttons = {
				    			"Cancelar": function() { 
									$(this).dialog("destroy").remove();
								},
				    			"Continuar": function() { 
				    				$(this).dialog("destroy").remove(); 
				    				$('#{$g['divId']}').showLoading();
								    $.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&acao=faturar&callback=?', function(request) {
									    $('#{$g['divId']}').hideLoading();
										if (request.code === true) {
											var buttons = {
												"Sim": function() {
													var T005_Id = $('#{$g['divId']} #T005_Id').val();
													$('#{$g['divId']}').showLoading();
													$.getJSON('/ven/ven002/form_func-ajax/faturarPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
														if (request.code) {
															var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    					dialogConfirm('Ok', request.data, buttons);
														} else { 
															var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    						dialogConfirm('Alertas de Validação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' + request.data + '</textarea>', buttons);
														}
														$('#{$g['divId']}').hideLoading();
														divRefresh('{$g['divId']}');
													});
													$(this).dialog("destroy").remove();
												},
												"Não": function() {
													var T005_Id = $('#{$g['divId']} #T005_Id').val();
													$.getJSON('/ven/ven002/form_func-ajax/abortouFaturarPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
													});
													$(this).dialog("destroy").remove();
												},
												//"Não": function() { $(this).dialog("destroy").remove(); } 
											};
											dialogConfirm('Faturar Pedido', 'Deseja faturar o pedido {$r_acaoId}?', buttons);
										}else if(request.code == 'erro') {
											var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
										    dialogConfirm('Faturar Pedido', request.data, \$buttons);
										} else {
											var \$buttons = {
												"Sim": function() {
													$('#{$g['divId']}').showLoading();
													$(this).dialog("destroy").remove();
													$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=faturar' + '&callback=?', function(request2) {
														$('#{$g['divId']}').hideLoading();
														if (request2.code) {
															var \$buttons = {
																 "Fechar": function() {
																	$(this).dialog("destroy").remove();
																	fecharJanela('{$g['divId']}');
																	divRefresh('{$g['divIdParent']}', true);
																 } 
															};
															dialogConfirm('Ok', request2.data, \$buttons);
														} else {
															var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
															dialogConfirm('Erro', request2.data, \$buttons);
														}
													});
												},
												"Não": function() { $(this).dialog("destroy").remove(); } 
											};
											dialogConfirm('Faturar Pedido', request.data, \$buttons);
										}
								    });
								}
				    		}
							dialogConfirm('Alertas de Validação NFe', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' + request.data + '</textarea>', buttons);
						}else if(request.code){
							$('#{$g['divId']}').showLoading();
								    $.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&acao=faturar&callback=?', function(request) {
									    $('#{$g['divId']}').hideLoading();
										if (request.code === true) {
											var buttons = {
												"Sim": function() {
													var T005_Id = $('#{$g['divId']} #T005_Id').val();
													$('#{$g['divId']}').showLoading();
													$.getJSON('/ven/ven002/form_func-ajax/faturarPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
														if (request.code) {
															var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    					dialogConfirm('Pedido Faturado', request.data, buttons);
														} else { 
															var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    						dialogConfirm('Alertas de Validação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' + request.data + '</textarea>', buttons);
														}
														$('#{$g['divId']}').hideLoading();
														divRefresh('{$g['divId']}');
													});
													$(this).dialog("destroy").remove();
												},
												"Não": function() {
													var T005_Id = $('#{$g['divId']} #T005_Id').val();
													$.getJSON('/ven/ven002/form_func-ajax/abortouFaturarPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
													});
													$(this).dialog("destroy").remove();
												},
												//"Não": function() { $(this).dialog("destroy").remove(); } 
											};
											dialogConfirm('Faturar Pedido', 'Deseja faturar o pedido {$r_acaoId}?', buttons);
										}else if(request.code == 'erro') {
											var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
										    dialogConfirm('Faturar Pedido', request.data, \$buttons);
										} else {
											var \$buttons = {
												"Sim": function() {
													$('#{$g['divId']}').showLoading();
													$(this).dialog("destroy").remove();
													$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=faturar' + '&callback=?', function(request2) {
														$('#{$g['divId']}').hideLoading();
														if (request2.code) {
															var \$buttons = {
																 "Fechar": function() {
																	$(this).dialog("destroy").remove();
																	fecharJanela('{$g['divId']}');
																	divRefresh('{$g['divIdParent']}', true);
																 } 
															};
															dialogConfirm('Ok', request2.data, \$buttons);
														} else {
															var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
															dialogConfirm('Erro', request2.data, \$buttons);
														}
													});
												},
												"Não": function() { $(this).dialog("destroy").remove(); } 
											};
											dialogConfirm('Faturar Pedido', request.data, \$buttons);
										}
								    });
						}
					});
				});

				$('#ven002EnviarEmail').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven002/form_func-ajax/verificarParcelas/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request1) {
						$('#{$g['divId']}').hideLoading();
						if(request1.code) {
							 $.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&acao=enviarEmail&callback=?', function(request) {
								 $('#{$g['divId']}').hideLoading();
								 if (request.code == true) {
							 		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T005_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
								 }else if(request.code == 'erro') {
										var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
									    dialogConfirm('Enviar E-mail', request.data, \$buttons);
								 }else{
									var \$buttons = {
										"Sim": function() {
											$('#{$g['divId']}').showLoading();
											$(this).dialog("destroy").remove();
											$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=email' + '&callback=?', function(request2) {
												$('#{$g['divId']}').hideLoading();
												if (request2.code) {
													var \$buttons = {
														 "Fechar": function() {
															$(this).dialog("destroy").remove();
															fecharJanela('{$g['divId']}');
															divRefresh('{$g['divIdParent']}', true);
														 } 
													};
													dialogConfirm('Ok', request2.data, \$buttons);
												} else {
													var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
													dialogConfirm('Erro', request2.data, \$buttons);
												}
											});
										},
										"Não": function() { $(this).dialog("destroy").remove(); } 
									};
									dialogConfirm('Enviar E-mail', request.data, \$buttons);
								 }
							 });
						}else{
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    	dialogConfirm('Erro', request1.data, buttons);
				    	}
					});
				});

				$('#ven002Compras').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Compras', '/ven/ven002/content/ven002ContentCompras/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [850,600]);
				});

				$('#ven002ExcluirItensMarcados').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/excluirItensMarcados/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									dialogConfirm('Ok', request.data);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Deseja excluir os itens marcados?', \$buttons);
				});

				$('#ven002AtualizarOpcoes').bind('click', function(data) {
					var \$buttons = {
						"Atualizar Aliquotas": function() {
							var \$buttons2 = {
								"Sim": function() {
									var T005_Id = $('#{$g['divId']} #T005_Id').val();
									idProgressBar = dialogAlert('Atualizando alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            ven002AtualizarAliquotasLoop(idProgressBar,T005_Id,1);
						            $(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar as aliquotas?', \$buttons2);
						},
						"Atualizar CFOP": function() { 
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/ven/ven002/content/ven002ContentAtualizarCFOP/', '&tab=geral&T005_Id=' + encodeURIComponent("{$r_acaoId}"), [500,250]);
							$(this).dialog("destroy").remove();
						},
						"Atualizar preço tabela": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/form_func-ajax/atualizarPrecoTabela/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Preço(s) tabela atualizado com sucesso!", \$buttons);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar o preço tabela dos produtos?', \$buttons2);
						},
						"Atualizar engenharia": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/form_func-ajax/atualizarComponentesProdutos/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Componentes atualizados com sucesso!", \$buttons);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar a engenharia dos produtos igual ao cadastro de produtos?', \$buttons2);
						},
						"Cancelar": function() {
							$(this).dialog("destroy").remove();
						}
					};
					var dialog = dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
					var buttons = $('#dc-' + dialog).parent().find('.ui-dialog-buttonpane button')
					var botao3 = $(buttons[3]);
					if('{$g['C031']['desabilitarProducaoEmergencia']}' == 'S'){
						$(botao3).remove();
					}
				});

				function ven002AtualizarAliquotasLoop(idProgressBar, T005_Id, inicio){
	                $.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
	                    if (request.concluido == 0) {
	                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
	                    	ven002AtualizarAliquotasLoop(idProgressBar,T005_Id,0);
	                    }else{
	                    	if (request.code) {
	                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
	                    		dialogConfirm('Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
	                            divRefresh('{$g['divId']}');
	                        } else {
	                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
	                            dialogConfirm('Erro!', request.data);
	                        }
	                    } 
	                });	                    
		        }
				
				if ("{$g['C031']['atualizaCFOPAliquotasPrecoAoEntrarVendaPendente']}" == 'S' && "{$r_atualizaDados}" == 'S' && ("{$mT005['T005_Flag_Status']}" == '0' || "{$mT005['T005_Flag_Status']}" == '')) {
					atualizaDadosDaVenda();
					divSetarAcaoId('{$g['divId']}', '', 'atualizaDados');
				}
				
				function atualizaDadosDaVenda() {
					$.getJSON('/ven/ven002/form_func-ajax/atualizarCFOP/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						if (request.code) {
							T005_Id = $r_acaoId;
							idProgressBar = dialogAlert('Atualizando CFOP, Alíquotas, Preço Tabela',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							ven002AtualizarCFOPAliquotasLoop(idProgressBar,T005_Id,1);  
						}
					});
				}
				
				function ven002AtualizarCFOPAliquotasLoop(idProgressBar, T005_Id, inicio){
					$.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
						if (request.concluido == 0) {
							$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
							ven002AtualizarCFOPAliquotasLoop(idProgressBar,T005_Id,0);
						} else {
							if (request.code) {
								// Aqui, após a conclusão da atualização das alíquotas, chamamos a função para atualizar o preço
								atualizarPrecoTabela();
								$('#dc-' + idProgressBar).dialog("destroy").remove();
							} 
						} 
					});                       
				}

				function atualizarPrecoTabela() {
					$.getJSON('/ven/ven002/form_func-ajax/atualizarPrecoTabela/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						// Aqui você pode tratar a resposta, se necessário
					}); 
				}

				$('#ven002EnviarParaWMS').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
					
							$.getJSON('/ven/ven002/form_func-ajax/enviarParaWMS/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									dialogConfirm('Ok', request.data);
								} else {
									dialogConfirm('Erro', request.data);
								}

								$('#{$g['divId']}').hideLoading();
							});

							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};

					dialogConfirm('Enviar para WMS', 'Você tem certeza que deseja enviar o pedido para o WMS?', \$buttons);
				});
			} </script></div>
EOT;

		break;
		
		case 'ven002ContentFaturarEmergencia':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormFaturarEmergencia/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);						

		break;

		case 'ven002ContentNFDebitoCredito':
			$T005_Id = (int) $r_acaoId;
			$pedidoNFDC = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id='{$T005_Id}' AND T005_C004_Id='{$g['empresaAtual']}'");
			if (!$pedidoNFDC || mysql_num_rows($pedidoNFDC) !== 1) {
				echo gGeraAlertMsg('NF de Débito', 'Pedido não encontrado para a empresa atual.');
				break;
			}
			// Um único ciclo content com duas rotas: preserva as opções de personalização do framework.
			$contentFormularioNFDC = 'ven002FormNFDebitoCredito_' . uniqid();
			$contentGridNFDC = 'ven002GridNFDebitoCredito_' . uniqid();
			$g['smarty']->assign('content1', $contentFormularioNFDC);
			$g['smarty']->assign('content2', $contentGridNFDC);
			$g['smarty']->assign('content1Height', '170px');
			$g['smarty']->assign('tipo', '2linhas');
			echo gProcessaAcoes(array(
				array($contentFormularioNFDC, '/ven/ven002/form/ven002FormNFDebitoCredito/', '&tab=principal&T005_Id=' . $T005_Id),
				array($contentGridNFDC, '/ven/ven002/grid/ven002GridNFDebitoCredito/', '&T005_Id=' . $T005_Id)
			));
			echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    var modalNFDC = $('#{$g['divId']}');
    // O framework pode mover o rodapé para fora do content, dentro do diálogo.
    var janelaNFDC = modalNFDC.closest('.ui-dialog');
    var quantidadesNFDC = {};
    var gerandoNFDC = false;
    var concluidoNFDC = false;

    function numeroNFDC(valor) {
        valor = $.trim(String(valor || '0'));
        if (valor.indexOf(',') >= 0) valor = valor.replace(/\./g, '').replace(',', '.');
        var numero = parseFloat(valor);
        return isNaN(numero) ? 0 : numero;
    }
    function guardarQuantidadesNFDC() {
        modalNFDC.find('.qtdNFDebitoCredito').each(function() {
            quantidadesNFDC[$(this).attr('data-t006-id')] = $(this).val();
        });
    }
    // As rotas são assíncronas e podem ser recarregadas pela personalização.
    // Eventos delegados mantêm os botões ativos após a recarga do formulário.
    modalNFDC.undelegate('.qtdNFDebitoCredito', 'change.nfdcPedido input.nfdcPedido')
        .delegate('.qtdNFDebitoCredito', 'change.nfdcPedido input.nfdcPedido', guardarQuantidadesNFDC);
    modalNFDC.unbind('nfdcGridCarregado').bind('nfdcGridCarregado', function() {
        modalNFDC.find('.qtdNFDebitoCredito').each(function() {
            var campo = $(this), itemId = campo.attr('data-t006-id');
            if (Object.prototype.hasOwnProperty.call(quantidadesNFDC, itemId)) campo.val(quantidadesNFDC[itemId]);
        });
    });
    modalNFDC.undelegate('#ven002PreencherPercentualNFDC', 'click.nfdcPedido')
        .delegate('#ven002PreencherPercentualNFDC', 'click.nfdcPedido', function(event) {
            event.preventDefault();
            var percentual = numeroNFDC(modalNFDC.find('#percentualNFDC').val());
            if (percentual <= 0 || percentual > 100) {
                dialogConfirm('Percentual inválido', 'Informe um percentual maior que zero e menor ou igual a 100%.');
                return;
            }
            if (!modalNFDC.find('.qtdNFDebitoCredito').length) {
                dialogConfirm('Itens indisponíveis', 'Aguarde o carregamento da grade e mantenha a coluna Nova quantidade disponível.');
                return;
            }
            modalNFDC.find('.qtdNFDebitoCredito').each(function() {
                var campo = $(this);
                var saldo = Math.max(0, numeroNFDC(campo.attr('data-quantidade-pedido')) - numeroNFDC(campo.attr('data-quantidade-gerada')));
                var casas = parseInt(campo.attr('data-casas-quantidade'), 10);
                var fator = Math.pow(10, casas);
                var quantidade = Math.round((saldo * percentual / 100 + 0.000000001) * fator) / fator;
                campo.val(quantidade.toFixed(casas).replace('.', ','));
            });
            guardarQuantidadesNFDC();
        });
    janelaNFDC.undelegate('#ven002ValidarNFDebitoCredito', 'click.nfdcPedido')
        .delegate('#ven002ValidarNFDebitoCredito', 'click.nfdcPedido', function(event) {
            event.preventDefault();
            var botao = $(this);
            if (gerandoNFDC || concluidoNFDC || botao.prop('disabled')) return;
            if (!modalNFDC.find('#clienteNFDC').length || !modalNFDC.find('.qtdNFDebitoCredito').length) {
                dialogConfirm('Dados indisponíveis', 'Aguarde o carregamento do formulário e da grade de itens.');
                return;
            }
            guardarQuantidadesNFDC();
            // Inclui campos acrescentados na personalização para as extensões locais da rota.
            var dados = {};
            $.each(modalNFDC.find(':input[name]').serializeArray(), function(i, campo) { dados[campo.name] = campo.value; });
            $.extend(dados, {
                ajax: true, T005_Id: '{$T005_Id}', tipoOperacao: 'debito', tipoFiscal: '06',
                D024_Id: modalNFDC.find('#clienteNFDC').val(), D006_Id: modalNFDC.find('#cfopNFDC').val(),
                quantidades: JSON.stringify(quantidadesNFDC)
            });
            gerandoNFDC = true;
            botao.prop('disabled', true);
            modalNFDC.showLoading();
            $.ajax({
                url: '/ven/ven002/form_func-ajax/validarNFDebitoCredito/', dataType: 'jsonp', data: dados,
                success: function(request) {
                    gerandoNFDC = false;
                    concluidoNFDC = !!request.code;
                    modalNFDC.hideLoading();
                    botao.prop('disabled', concluidoNFDC);
                    dialogConfirm(request.code ? 'NF gerada' : 'Verifique os dados', request.data);
                },
                error: function() {
                    gerandoNFDC = false;
                    botao.prop('disabled', false);
                    modalNFDC.hideLoading();
                    dialogConfirm('Erro', 'Não foi possível gerar a NF.');
                }
            });
        });
} </script></div>
EOT;
		break;

		case 'ven002content03':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002tab03/', '&acaoId=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002content04':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$divOutro = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '220px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			//array($content1, '/ven/ven002/form/ven002form02/', "&tab=geral&T005_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2)),
			array($content1, '/ven/ven002/content/ven002contentform/', "&T005_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2) . '&divOutro=' . urlencode($divOutro)),
			array($content2, '/ven/ven002/grid/ven002grid02/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($divOutro)),
			);
			echo gProcessaAcoes($actions);
		break;
					
		case 'ven002contentform':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $r_divOutro);
			$g['smarty']->assign('content1Width', '350px');
			$g['smarty']->display('content-2columnsPedido.tpl');
			echo <<<JS
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#{$content1}').css('width', '660px');
				} </script></div>
			JS;
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002form02/', "&tab=geral&T005_Id=" . urlencode($r_T005_Id) . "&divReload=" . urlencode($r_divReload) . "&divOutro=" . urldecode($r_divOutro)),
			array($r_divOutro, '/ven/ven002/outro/ven002observacoes/', '&acaoId=' . urlencode($r_T005_Id). '&D024_Id=' . urlencode($r_D024_Id)),
			);
			echo gProcessaAcoes($actions);
		break;
			
		case 'ven002contentTrocas':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '190px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002formTrocas/', "&tab=trocas&T005_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002gridTrocas/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);
		break;			

		case 'ven002content05':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '230px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002form03/', "&tab=geral&T005_Id=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002grid03/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002content06':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002form04/', "&tab=editar_material&T147_Id=" . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002grid04/', '&T147_Id=' . urlencode($r_T147_Id)  . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002content07':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002form05/', "&tab=geral&T147_Id=" . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002grid05/', '&acaoId=' . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002content08':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002tab03/', "&tab=geral&T147_Id=" . urlencode($r_T147_Id))
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002ContentEditarProduto':

			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002TabEditarProduto/', '&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id)),
			);
			echo gProcessaAcoes($actions);
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="ven002FormEditarProdutosBtn">Alterar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('buttons-floatBottom.tpl');

	break;
		
		
	case 'ven002ContentAplicarIpv':

		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormAplicarIpv/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="ven002AplicarIpvBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#ven002AplicarIpvBtnExecutar').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T005_Id = $('#{$g['divId']} #T005_Id').val();
						var IPV = $('#{$g['divId']} #IPV').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven002/form_func-ajax/aplicarIPV/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&IPV=' + encodeURIComponent(IPV) + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Ok', request.data, \$buttons);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
							fecharJanela('{$g['divId']}');
							divRefresh('{$g['divIdParent']}');
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Gerar Pedido', 'Você tem certeza? ', \$buttons);
			});
			} </script></div>
EOT;
	break;
	
	case 'ven002ContentImportarItens':

		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormImportarItens/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="ven002ImportarItensBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#ven002ImportarItensBtnExecutar').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T005_Id = $('#{$g['divId']} #T005_Id').val();
						var T005_Id_Origem = $('#{$g['divId']} #T005_Id_Origem').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven002/form_func-ajax/importarItens/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&T005_Id_Origem=' + encodeURIComponent(T005_Id_Origem) + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Ok', request.data, \$buttons);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
							fecharJanela('{$g['divId']}');
							divRefresh('{$g['divIdParent']}');
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Gerar Pedido', 'Você tem certeza? ', \$buttons);
			});
			} </script></div>
EOT;
	break;

		case 'ven002ContentAbrePagamento':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002TabPagamento/', '&T005_Id=' . urlencode($r_T005_Id)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002ContentPagamento':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '160px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
            // "&acaoId=" . urlencode($r_acaoId)
			array($content1, '/ven/ven002/form/ven002FormPagamento/', "&tab=geral&T005_Id=" . urlencode($r_T005_Id) . "&acaoId=" . "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002GridPagamento/', '&acaoId=' . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'ven002ContentEmail':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			//array($content1, '/ven/ven002/grid/ven002GridEmail/', '&acaoId=' . urlencode($r_acaoId)),
			array($content1, '/etc/etc006/grid/etc006GridTodos/', '&T005_Id=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'ven002ContentDocumentos':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '120px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormDocumentos/', "&tab=geral&T005_Id=" . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002GridDocumentos/', '&acaoId=' . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'ven002ContentDetalhesCheque':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormDetalhesCheque/', "&tab=geral&acaoId=" . urlencode($r_T090_Id)),
			);
			echo gProcessaAcoes($actions);			
		
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="ven002FormDetalhesCheque_btn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');			

		break;	
		
    case 'ven002ContentImportarDoc':
        /*
        * Divisão da Area em Conteúdos
        */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
        // identificador, url, paramentros get (opcional)
        array($content1, '/ven/ven002/form/ven002FormImportarDoc/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id) . '&divReload=' . urlencode($r_divReload)),
        );
        echo gProcessaAcoes($actions);

    break;
    
    
    case 'ven002contentCancelarPedido':
            
            /*
            * Divisão da Area em Conteúdos
            */
            // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
            $content1 = uniqid();
            $g['smarty']->assign('content1', $content1);
            $g['smarty']->assign('tipo', 'unico');
            // O que será carregado em cada conteúdo?
            $actions = array(
            	// identificador, url, paramentros get (opcional)
            	array($content1, '/ven/ven002/form/ven002FormCancelarPedido/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
            );
            echo gProcessaAcoes($actions);

    break;
            
    case 'ven002ContentAgregados':
        /*
         * Divisão da Area em Conteúdos
         */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $content2 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('content2', $content2);
        $g['smarty']->assign('content1Height', '90px');
        $g['smarty']->assign('tipo', '2linhas');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/ven/ven002/form/ven002FormAgregados/', '&tab=geral&T006_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
            array($content2, '/ven/ven002/grid/ven002GridAgregados/', '&acaoId=' . urlencode($r_acaoId) . '&T006_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
        );
        echo gProcessaAcoes($actions);
        
        /**
         * Botões
         */
        // Definição dos botões
        $botoesEsq = '';
        //$botoesDir = '';
        //$botoesEsq .= '<button type="submit" id="ven001ContetnProdutosBtnConfirmarTudo">Confirmar Tudo</button>';
        //$botoesEsq .= '<button type="submit" id="ven001ContetnProdutosBtnZerarTudo">Zerar Tudo</button>';
        //$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        //$g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        // Ações dos botões
        $novoIdTelaForm = uniqid();        
    break;
   
	case 'ven002ContentProdutosDefeitos':
        /*
        * Divisão da Area em Conteúdos
        */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
        // identificador, url, paramentros get (opcional)
        array($content1, '/ven/ven002/grid/ven002GridPedidoProdutosDefeito/', '&acaoId=' . urlencode($r_acaoId)),
        );
        echo gProcessaAcoes($actions);
	break;

	case 'ven002contentIncluirPedido':
		$acaoId=isset($r_acaoId) ? $r_acaoId : false;

		if(!empty($g['C031']['horarioLimiteGeracaoPedido']) && $g['C031']['horarioLimiteGeracaoPedido'] != '00:00' && strtotime(date("H:i")) > strtotime($g['C031']['horarioLimiteGeracaoPedido'])){
			$alert = gGeraAlertMsg('Atenção!', "O horário para inclusão de novos pedidos ultrapassou o horário permitido ({$g['C031']['horarioLimiteGeracaoPedido']}).", false);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							{$alert}
						}, 150);
		  			});
					$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
				} </script></div>
EOT;
		} else {
	        /*
	        * Divisão da Area em Conteúdos
	        */
	        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
	        $content1 = uniqid();
	        $g['smarty']->assign('content1', $content1);
	        $g['smarty']->assign('tipo', 'unico');
	        // O que será carregado em cada conteúdo?
	        $actions = array(
	        	// identificador, url, paramentros get (opcional)
	     		array($content1, '/ven/ven002/tab/ven002TabIncluirPedido/', '&acaoId=' . urlencode($r_acaoId)),
	        );
	        echo gProcessaAcoes($actions);
	    }
	break;

	case 'ven002ContentCompras':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002TabCompras/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven002ContentCotar':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002TabCotar/', '&T005_Id=' . urlencode($r_T005_Id) . '&T006_Id=' . urlencode($r_T006_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="ven002FormCotar_btn">Cotar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven002ContentComprar':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002TabComprar/', '&T005_Id=' . urlencode($r_T005_Id) . '&T006_Id=' . urlencode($r_T006_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="ven002FormComprar_btn">Comprar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven002ContentMudarPedidoEmpresa':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormMudarPedidoEmpresa/', '&tab=geral&sqlSelecao='.$r_sqlSelecao),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven002ContentDuplicarPedidoEmpresa':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			array($content1, '/ven/ven002/form/ven002FormMudarPedidoEmpresa/', '&tab=geral&duplicarEmpresa=S&sqlSelecao='.$r_sqlSelecao),
		);
		echo gProcessaAcoes($actions);
	break;


	case 'ven002ContentAtualizarCFOP':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		    // identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormAtualizarCFOP/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven002ContentAtualizarCFOPPedidos':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)

		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		    // identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormAtualizarCFOPPedidos/', '&tab=geral&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'contentMontagemIPV':
		/*
		* Divisão da Area em Conteúdos
		*/

		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
        $content2 = uniqid();
        $g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		if($g['nivelAlcada']==4){   
			$g['smarty']->assign('content1Height', '145px');
		}else{
			$g['smarty']->assign('content1Height', '0px');
		}
     
        $g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/outro/ven002DemonstrativoIPV/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array($content2, '/ven/ven002/tab/ven002TabMontagemIPV/', '&acaoId=' . urlencode($r_acaoId) . '&total=' . urlencode($r_total))
		);
		echo gProcessaAcoes($actions);

		$botoesDir = '<button type="button" id="ven002GravarAliquota" style="float: left; margin: 5px 10px 0 0;">Gravar e atualizar IPV</button>';
        $botoesEsq = '<button type="button" id="ven002AtualizarMontagemIPV" style="float: left; margin: 5px 10px 0 0;">Retornar valor padrão</button>';
		$botoesMid = '';
		
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesEsq', $botoesEsq);

		$g['smarty']->display('buttons-floatBottom.tpl');
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				$('#ven002GravarAliquota').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/grid_func-ajax/gravarMontagemIPV/?ajax=true&T005_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									dialogConfirm('Gravar alíquotas do IPV', 'Alterações salvas.');
									divRefresh('{$g['divId']}');
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();s
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gravar alíquotas do IPV', '<br>Deseja gravar as alíquotas do IPV?', \$buttons);
				});	
				$('#ven002AtualizarMontagemIPV').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/grid_func-ajax/atualizarMontagemIPV/?ajax=true&T005_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}');
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atualizar montagem do IPV', '<span style="color: var(--BGVermelho)"><center>ATENÇÃO</center><br>Todos as varíaveis do IPV serão removidas e atualizadas conforme a configuração padrão.</span><br><br>Deseja atualizar as varíaveis do IPV?', \$buttons);
				});
			} </script></div>
EOT;
break;

	case 'contentMontagemIPVTotal':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002TabMontagemIPV/', '&acaoId=' . urlencode($r_acaoId) . '&total=' . urlencode($r_total))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven002ContentEngenhariaProduto':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/tab/ven002TabEngenhariaProduto/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
}
