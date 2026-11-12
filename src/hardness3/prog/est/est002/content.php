<?php
namespace hardness;
	/**
	* Weiberlan
	* 12/11/2011
	*/
	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		 case 'est002ContentPrincipal':
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
			array($content1, '/est/est002/tab/est002TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est002ContentProdutosEmSeparacao':
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
				array($content1, '/est/est002/tab/est002TabProdutosEmSeparacao/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est002ContentPedido':
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
			array($content1, '/est/est002/tab/est002TabPedido/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est002ContentEntregaInterna':
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
			array($content1, '/est/est002/tab/est002TabEntregaInterna/', '&acaoId=' . urlencode($r_acaoId))
			);
			
		/**
		* Botões
		*/
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est002ConfirmarEntregaBtn">Confirmar Entrega</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		$T81_Codigo = uniqid();
		if ($g['C031']['retiradaInternaUtilizarLeitorBiometrico'] == 'S') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				var t;
				var tentativas = 0;
				$('#est002ConfirmarEntregaBtn').unbind('click').bind('click', function(data) {
				    // Inicia leitor de impressão digital
				    iniciarLeitorDigital();
					// Fica aguardando a resposta
					$('#{$g['divId']}').showLoading();
					t = setTimeout("lerDigital()",1000);
				});
				function lerDigital() {
					// Carrega Ajax
					$('#{$g['divId']}').showLoading();
					$.getJSON('/sistema/funcoes/integracaoLeitorDigital/tryIdentify/?ajax=true&key=' + encodeURIComponent('{$T81_Codigo}') + '&callback=?', function(data) {
						if (data.code) {
							//console.log('OK: ' + data.data);
							$('#{$g['divId']}').hideLoading();
							clearTimeout(t);
							
							if (data.data == -1) {
								dialogConfirm('Erro', 'Usuário não identificado! Por favor, tente novamente.');
							} else {
								$.getJSON('/est/est002/grid_func-ajax/confirmarEntregaInterna/?ajax=true&D109_Id='+encodeURIComponent(data.data)+'&callback=?', function(request) {
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
							}
							return false;
						} else {
							// tentando
						}
					});
					tentativas++;
					if (tentativas == 10) {
						$('#{$g['divId']}').hideLoading();
						clearTimeout(t);
						dialogConfirm('Erro', 'Tempo esgotado! Tente novamente.');
						return false;
					} else {
						t = setTimeout("lerDigital()",1000);
					}
				}
				} </script></div>
EOT;
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					function iniciarLeitorDigital() {
						netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
						var exe = window.Components.classes['@mozilla.org/file/local;1'].createInstance(Components.interfaces.nsILocalFile);
						exe.initWithPath("C:\\hardness\\hardnessLerDigital.exe");
						var run = window.Components.classes['@mozilla.org/process/util;1'].createInstance(Components.interfaces.nsIProcess);
						run.init(exe);
EOT;
						echo "var parameters = [\"{$T81_Codigo}\"];";
			echo <<<EOT
						run.run(false, parameters, parameters.length);
					}
				} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est002ConfirmarEntregaBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione o Funcionário', '/est/est002/content/est002ContentSelecionarFuncionario/', '', [400,150]);
				});
			} </script></div>
EOT;
		}
			
			echo gProcessaAcoes($actions);
		break;	
		
		case 'est002ContentSelecionarFuncionario':
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
			array($content1, '/est/est002/tab/est002TabSelecionarFuncionario/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'est002ContentHistoricoEntregaInterna':
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
			array($content1, '/est/est002/tab/est002TabHistoricoEntregaInterna/', '&acaoId=' . urlencode($r_acaoId))
			);
			
			/**
			 * Botões
			 */
			$botoesDir = '';			
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');				
			
			echo gProcessaAcoes($actions);
		break;		
		
		case 'est002ContentEstoque':
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
			array($content1, '/est/est002/tab/est002TabEstoque/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est002ContentEtiquetas':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '30px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/est/est002/form/est002SelecionarModelo/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
				array($content2, '/est/est002/grid/est002GridEtiquetas/', "&acaoId=" . urlencode($r_T005_Id))				
			);
			echo gProcessaAcoes($actions);
			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesDir = '';
			//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');	
		break;
		
		case 'est002ImprimirPedido':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)

			$rowT005 = mysql_fetch_assoc(mysql_query("SELECT T005_Flag_Status,
												             T005_Nome_Status 
													    FROM T005 
													   WHERE T005_Id = '{$r_T005_Id}'"));

			if($rowT005['T005_Flag_Status'] != '1' && $r_T005_Id > 0){
				$msg = "Pedido {$r_T005_Id} já foi impresso, está com o status <b>{$rowT005['T005_Nome_Status']}</b>";
				$alert = gGeraAlertMsg('Atenção', $msg, false);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
							setTimeout(function() {
							{$alert}
							divRefresh('{$g['divIdParent']}');
							}, 150);
						});
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					} </script></div>
EOT;
				die();
			}


			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
				array($content1, '/est/est002/form/est002FormImprimirPedido/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id))
			);
			echo gProcessaAcoes($actions);

			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="est002FormImprimirPedidoBtn">Executar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		break;

		case 'est002SeparacaoPedido':
			/*
			 * Divisão da Area em Conteúdos
			 */
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo  $g['smarty']->fetch('content-full.tpl');		
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/est/est002/tab/est002TabSeparacaoPedido/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id))
			);
			echo  gProcessaAcoes($actions);

			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="est002FormPreencherSeparadosBtn">Preencher separados</button>';
			$botoesDir .= '<button type="submit" id="est002FormLimparSeparadosBtn">Limpar separados</button>';
			$botoesDir .= '<button type="submit" id="est002FormSeparacaoPedidoBtn">Continuar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');

			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est002FormPreencherSeparadosBtn').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est002/grid_func-ajax/est002PreencherSeparados/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
						if (request.code) {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		//dialogConfirm('Ok', "Operação realizada com sucesso.", \$buttons);
							divRefresh('{$content1}');
						} else {
							
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				$('#est002FormLimparSeparadosBtn').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est002/grid_func-ajax/est002LimparSeparados/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
						if (request.code) {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		//dialogConfirm('Ok', "Operação realizada com sucesso.", \$buttons);
							divRefresh('{$content1}');
						} else {
							
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

			} </script></div>
EOT;
		break;
		
		case 'est002SeparacaoPedido2Rows':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '150px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
				array($content1, '/est/est002/form/est002FormSeparacaoPedido/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id)),
				array($content2, '/est/est002/grid/est002GridProdutosPedidoConfirmarSeparacao/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est002FaturamentoPedido':
			/*
			 * Divisão da Area em Conteúdos
			 */
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)

			$rowT005 = mysql_fetch_assoc(mysql_query("SELECT T005_Flag_Status,
												             T005_Nome_Status 
													    FROM T005 
													   WHERE T005_Id = '{$r_T005_Id}'"));

			if($rowT005['T005_Flag_Status'] != '10'){
				$msg = "Pedido {$r_T005_Id} já foi conferido, está com o status <b>{$rowT005['T005_Nome_Status']}</b>";
				$alert = gGeraAlertMsg('Atenção', $msg, false);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
							setTimeout(function() {
							{$alert}
							divRefresh('{$g['divIdParent']}');
							}, 150);
						});
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					} </script></div>
	EOT;
				die();
			}

			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo  $g['smarty']->fetch('content-full.tpl');		
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/est/est002/tab/est002TabFaturamentoPedido/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id))
			);
			echo  gProcessaAcoes($actions);

	break;
			
	case 'est002ContentConfirmarEmprestimo':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est002/tab/est002TabConfirmarEmprestimo/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'est002ContentCadastroEmprestimo':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est002/tab/est002TabCadastrarEmprestimo/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;

	case 'est002ContentFaturamentoPedido':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '150px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
			array($content1, '/est/est002/form/est002FormFaturamentoPedido/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id)),
			array($content2, '/est/est002/grid/est002GridFaturamentoPedido/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id))
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est002FormFaturamentoPedidoBtnImprimir">Imprimir</button>';
		$botoesDir .= '<button type="submit" id="est002FormFaturamentoPedidoBtn">Continuar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est002FormFaturamentoPedidoBtnImprimir').unbind('click').bind('click', function(data) {
				nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_conferencia.php?T005_Id='+encodeURIComponent('{$r_T005_Id}'),0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido')
			});			
		} </script></div>
EOT;

	break;

	case 'est002SeparacaoPorLocacao':
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
			array($content1, '/est/est002/tab/est002SeparacaoPorLocacao/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

		$rowT005 = mysql_fetch_assoc(mysql_query("SELECT T005_Estoque_Box,
														 T005_Flag_Status,
														 T005_Nome_Status 
												    FROM T005 
												   WHERE T005_Id = '{$r_T005_Id}'"));

		if($rowT005['T005_Flag_Status'] > 2 && $g['C031']['modeloExpedicao'] == 1){
			$msg = "Pedido {$r_T005_Id} já foi separado, está com o status <b>{$rowT005['T005_Nome_Status']}</b>";
			$alert = gGeraAlertMsg('Atenção', $msg, false);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
						{$alert}
						divRefresh('{$g['divIdParent']}');
						}, 150);
					});
					$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
				} </script></div>
EOT;
			die();
		}

		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est002SeparacaoPorLocacaoContinuar">Continuar</button>';
		$botoesEsq .= '<button type="submit" id="est002SeparacaoPorLocacaoPSeparados">Preencher Separados</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ação dos botões
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est002SeparacaoPorLocacaoContinuar').unbind('click').bind('click', function() {
					var buttons = {
						"Finalizar": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est002/form_func-ajax/est002SeparacaoPorLocacaoContinuar/?ajax=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&T005_Estoque_Box=' + encodeURIComponent($('#est002SeparacaoPorLocacaoContinuarBox').val()) + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									fecharJanela("{$g['divId']}");
									divRefresh('{$g['divIdParent']}', true);
									dialogConfirm('Operação OK!', 'Pedido enviado para o box com sucesso!');
								} else {
									dialogConfirm('Erro!', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Finalizar Separação', '<div style="margin: 5px;">Informe o BOX: <br/><input type="text" id="est002SeparacaoPorLocacaoContinuarBox" value="{$rowT005['T005_Estoque_Box']}"/></div>', buttons);
					setTimeout(function() {
						$('#est002SeparacaoPorLocacaoContinuarBox').focus();
					}, 100)
				});
				$('#est002SeparacaoPorLocacaoPSeparados').unbind('click').bind('click', function() {
					var buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est002/form_func-ajax/est002SeparacaoPorLocacaoCPSeparados/?ajax=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									dialogConfirm('Operação OK!', 'Quantidade separadas preenchidas!');
								} else {
									dialogConfirm('Erro!', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Atenção', 'Este processo irá preencher automaticamente as <b>quantidades</b> para separar dos produtos na <b>primeira locação</b> encontrada.<br/>Deseja continuar!?', buttons);
				});
			} </script></div>
EOT;
	break;

	case 'est002SeparacaoPorLocacao2rows':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '100px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est002/form/est002SeparacaoPorLocacao/', "&tab=geral&T005_Id=" . urlencode($r_acaoId) . '&divRefresh=' . $content2),
			array($content2, '/est/est002/grid/est002SeparacaoPorLocacao/', "&T005_Id=" . urlencode($r_acaoId) . '&divRefresh=' . $content1),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002ContentSeparacaoBalcao':
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
			array($content1, '/est/est002/tab/est002TabSeparacaoBalcao/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002ContentSeparacaoComercial':
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
			array($content1, '/est/est002/tab/est002TabSeparacaoComercial/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002ContentCadastroPacote':
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
			array($content1, '/est/est002/tab/est002TabCadastroPacote/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002ContentIdSeparacaoBox':
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
			array($content1, '/est/est002/form/est002FormIdSeparacaoBox', '&tab=geral'),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002ContentSepararProdutos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$contentGridsId = uniqid();
		$contentFormId = uniqid();
		$g['smarty']->assign('content1', $contentFormId);
		$g['smarty']->assign('content2', $contentGridsId);
		$g['smarty']->assign('content1Height', '40px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?

		$idBox = urlencode($r_idBox);

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($contentFormId, '/est/est002/form/est002FormSepararProdutos/', "&tab=geral&idBox={$idBox}&divGrid={$contentGridsId}"),
			array($contentGridsId, '/est/est002/content/est002ContentSepararProdutosGrids/', "&idBox={$idBox}&divForm={$contentFormId}")
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002ContentSepararProdutosGrids':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$contentGridProdutos = uniqid();
		$contentGridImpEtiquetas = uniqid();
		$g['smarty']->assign('content1', $contentGridProdutos);
		$g['smarty']->assign('content2', $contentGridImpEtiquetas);
		$g['smarty']->assign('content1Height', '500px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?

		$idBox = urlencode($r_idBox);
		$divContentFormId = $r_divForm;

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($contentGridProdutos, '/est/est002/grid/est002GridSepararProdutos/', "&idBox={$idBox}&divForm={$divContentFormId}&divGridEtiquetas={$contentGridImpEtiquetas}"),
			array($contentGridImpEtiquetas, '/est/est002/grid/est002GridSepararProdutosEtiqueta/', "&idBox={$idBox}&divForm={$divContentFormId}")
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002VolumesConferencia':
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
			array($content1, '/est/est002/tab/est002VolumesConferencia/', '&acaoId=' . urlencode($r_acaoId) . '&divJanela=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

		$rowT005 = mysql_fetch_assoc(mysql_query("SELECT T005_Estoque_Box,
														 T005_Flag_Status,
														 T005_Nome_Status 
												    FROM T005 
												   WHERE T005_Id = '{$r_T005_Id}'"));

		if($rowT005['T005_Flag_Status'] != '2' && $g['C031']['modeloExpedicao'] == 1){
			$msg = "Pedido {$r_T005_Id} já foi separado, está com o status <b>{$rowT005['T005_Nome_Status']}</b>";
			$alert = gGeraAlertMsg('Atenção', $msg, false);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
						{$alert}
						divRefresh('{$g['divIdParent']}');
						}, 150);
					});
					$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
				} </script></div>
EOT;
			die();
		}

		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est002VolumesConferenciaGerarVolume">Gerar Volume</button>';
		$botoesEsq .= '<button type="submit" id="est002VolumesConferenciaReiniciarSeparacao">Reiniciar Separação</button>';
		$botoesEsq .= '<button type="submit" id="est002VolumesConferenciaReiniciarConferencia">Reiniciar Conferência</button>';
		$botoesEsq .= '<button type="submit" id="est002VolumesConferenciaFinalizar">Finalizar Processo</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ação dos botões
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est002VolumesConferenciaGerarVolume').unbind('click').bind('click', function() {
					var buttons = {
						"Gerar": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est002/grid_func-ajax/est002VolumesConferenciaGerarVolume/?ajax=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								} else {
									dialogConfirm('Erro!', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Gerar volume?', 'Este processo irá gerar um volume com as quantidades conferidas.<br>Deseja continuar?', buttons);
				});
				$('#est002VolumesConferenciaFinalizar').unbind('click').bind('click', function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est002/grid_func-ajax/est002VolumesConferenciaValidar/?ajax=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Finalizar conferência', '/est/est002/content/est002VolumesConferenciaFinalizarContent/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&parcial=' + encodeURIComponent(request.parcial) + '&divGrid=' + encodeURIComponent('{$g['divIdParent']}'), [500, 220]);
						} else {
							dialogConfirm('Erro!', request.data);
						}
					});
				});
				$('#est002VolumesConferenciaReiniciarConferencia').unbind('click').bind('click', function() {
					var buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est002/grid_func-ajax/est002VolumesConferenciaReiniciarConferencia/?ajax=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								} else {
									dialogConfirm('Erro!', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Atenção', 'Este processo irá reiniciar toda a conferência já realizada para este pedido, removendo todos os volumes já gerados.<br/>Deseja continuar!?', buttons);
				});
				$('#est002VolumesConferenciaReiniciarSeparacao').unbind('click').bind('click', function() {
					var buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est002/grid_func-ajax/est002VolumesConferenciaReiniciarSeparacao/?ajax=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								} else {
									dialogConfirm('Erro!', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Atenção', 'Este processo irá reiniciar a separação atual, cancelando apenas as quantidades atuais conferidas.<br/>Deseja continuar!?', buttons);
				});
			} </script></div>
EOT;
	break;

	case 'est002VolumesConferenciaContent':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$contentGridConferencia = uniqid();
		$contentGridLegenda     = uniqid();
		$g['smarty']->assign('content1', $contentGridLegenda);
		$g['smarty']->assign('content2', $contentGridConferencia);
		$g['smarty']->assign('content1Height', '25px');
		$g['smarty']->display('content-2rowsInverso.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($contentGridLegenda,     '/est/est002/outro/est002GridConferenciaVolumesLegenda/', "&T005_Id=" . urlencode($r_acaoId) . '&divRefresh=' . $contentGridLegenda     . '&divJanela=' . urlencode($_REQUEST['divJanela'])),
			array($contentGridConferencia, '/est/est002/grid/est002GridConferenciaVolumes/',         "&T005_Id=" . urlencode($r_acaoId) . '&divRefresh=' . $contentGridConferencia . '&divJanela=' . urlencode($_REQUEST['divJanela'])),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002VolumesConsultaContent':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$contentGridVolumes      = uniqid();
		$contentGridVolumesItens = uniqid();
		$g['smarty']->assign('content1', $contentGridVolumes);
		$g['smarty']->assign('content2', $contentGridVolumesItens);
		$g['smarty']->assign('content1Height', '155px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($contentGridVolumes,      '/est/est002/grid/est002VolumesConsultaCapa/',  '&divReload=' . urlencode($contentGridVolumesItens) . '&divRefresh=' . $contentGridVolumes      . '&divJanela=' . urlencode($_REQUEST['divJanela'])),
			array($contentGridVolumesItens, '/est/est002/grid/est002VolumesConsultaItems/', '&divReload=' . urlencode($contentGridVolumes)      . '&divRefresh=' . $contentGridVolumesItens . '&divJanela=' . urlencode($_REQUEST['divJanela'])),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est002VolumesConferenciaFinalizarContent':
		$content1 = uniqid();
		$content2 = uniqid();

		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '35px');
		$g['smarty']->display('content-2rowsInverso.tpl');

		$actions = array(
			array($content2, '/est/est002/tab/est002VolumesConferenciaFinalizarTab/',     '&acaoId=' . urlencode($r_acaoId) . '&parcial=' . urlencode($_REQUEST['parcial']) . '&divGrid=' . urlencode($_REQUEST['divGrid'])),
			array($content1, '/est/est002/outro/est002VolumesConferenciaFinalizarOutro/', '&acaoId=' . urlencode($r_acaoId) . '&parcial=' . urlencode($_REQUEST['parcial']) . '&divGrid=' . urlencode($_REQUEST['divGrid'])),
		);

		echo gProcessaAcoes($actions);
	break;
}
