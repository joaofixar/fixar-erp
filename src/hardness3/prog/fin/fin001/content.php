<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: Menu
	*/
	case 'fin001content01':
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
			array($content1, '/fin/fin001/tab/fin001tab01/')
		);
		echo gProcessaAcoes($actions);
	break;
	
	/**
	* Form Principal
	* Chamado em: fin001grid01
	*/
	case 'fin001content02':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');

		$acaoId;
		if (isset($_REQUEST['acaoId']))
			$acaoId=$_REQUEST['acaoId'];
		else
			$acaoId='';
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin001/tab/fin001tab02/', '&acaoId=' . urlencode($acaoId) . '&T002_Id=' . urlencode($acaoId)),
		);
		echo gProcessaAcoes($actions);

		/**
		* Botões
		*/
		// Definição dos botões
		$novoIdTelaForm = uniqid();
		$botoesMid = $botoesEsq = $botoesDir = '';
		// $sql = "SELECT T002_Valor_Recebido FROM T002 WHERE T002_Id='{$acaoId}'";
		// $result = mysql_query($sql);
		// $linha = mysql_fetch_array($result);
		if($r_acaoId <> 0){
			$botoesEsq = "<button onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Nota Promissória', '/fin/fin001/outro/fin001NotaPromissoria/','&T002_Id={$acaoId}', [1000,600]);\">Nota Promissória</button>";
			// $botoesEsq .= "<button onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar Boleto', '/fin/fin005/content/fin005ContentAbreCadastroBoleto/','&T002_Id={$acaoId}', [1000,600]);\">Gerar Boleto</button>";
			$botoesEsq .= '<button type="submit" id="fin001GerarBoleto">Gerar Boleto</button>';
		}
		$botoesDir .= '<button type="submit" id="fin001_content_form1_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001GerarBoleto').unbind('click').bind('click', function(data) {
					$.getJSON('/fin/fin001/grid_func-ajax/verificaValidacaoGerarBoleto/?ajax=true&T002_Id='+ encodeURIComponent('{$acaoId}') +'&callback=?', function(request) {
						if (request.code) {
							abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar Boleto', '/fin/fin005/content/fin005ContentAbreCadastroBoleto/','&T002_Id={$acaoId}', [1000,600]);
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Gerar boleto', request.data, buttons);
						}
					});
				});
			} </script></div>
EOT;
	break;

	/**
	* Remessa
	* Chamado em: fin001grid01
	*/
	case 'fin001content03':
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
			array($content1, '/fin/fin001/tab/fin001tab03/'),
		);
		echo gProcessaAcoes($actions);

	break;
	
	/**
	* Agrupar
	* Chamado em: fin001grid01
	*/
	case 'fin001content04':
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
			array($content1, '/fin/fin001/tab/fin001tab04/', '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	/**
	* Baixar
	* Chamado em: fin001grid01
	*/
	// case 'fin001content05':
	// 	/*
	// 	 * Divisão da Area em Conteúdos
	// 	 */
	// 	 //Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
	// 	$content1 = uniqid();
	// 	$g['smarty']->assign('content1', $content1);
	// 	$g['smarty']->assign('tipo', 'unico');
	// 	 //O que será carregado em cada conteúdo?
	// 	$actions = array(
	// 		 //identificador, url, paramentros get (opcional)
	// 		array($content1, '/fin/fin001/tab/fin001tab05/', '&extra=' . urlencode($r_extra)),
	// 	);
	// 	echo gProcessaAcoes($actions);
	// break;
	case 'fin001content05':

		/*
		 * Divisão da Area em Conteúdos
		 */
		 //Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		 //O que será carregado em cada conteúdo?
		$actions = array(
			 //identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin001/tab/fin001tabBaixaTitulos/', '&extra=' . urlencode($r_extra) . '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'fin001content052rows':

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
			array($content1, '/fin/fin001/grid/fin001grid07/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .'&extra=' . urlencode($r_extra) .'&idDivForm=' . urlencode($content2)),
			array($content2, '/fin/fin001/form/fin001form08/', '&tab=baixar&extra=' . urlencode($r_extra))
		);
		echo gProcessaAcoes($actions);

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_baixar_btn">Baixar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	/**
	* Reparcelar
	* Chamado em: fin001grid01
	*/
	case 'fin001content06':
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
			array($content1, '/fin/fin001/tab/fin001tab06/', '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);

	break;	
	
	/**
	* Reparcelar
	* Chamado em: fin001grid01
	*/
	case 'fin001ContentReparcelar':
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
			array($content1, '/fin/fin001/tab/fin001tabReparcelamento/', '&acaoId=' . urlencode($r_acaoId)),
		);

		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="fin001EnviarEmailReparcelamento">Enviar Email</button>';
		$botoesEsq .= '<button type="submit" id="fin001ImprimirReparcelamento">Imprimir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		$novoIdTelaForm = uniqid();
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001EnviarEmailReparcelamento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T166_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
				});
				$('#fin001ImprimirReparcelamento').unbind('click').bind('click', function() {
					nova_janela('/etc/etc006/form_func-ajax/imprimirReparcelamento/?ajax=true&T166_Id='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','no','no','Imprimir Simulação');
				});
			} </script></div>
EOT;
		

	break;
	
	case 'fin001ContentPagamento':	 
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '200px');
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin001/tab/fin001tabReparcelamentoJanela/', "&tab=geral&T005_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($r_acaoId) . '&extra=' . urlencode($r_extra) . '&simular=' . urlencode(@$r_simular)),
			array($content2, '/fin/fin001/content/fin001ContentReparcelamento/', '&acaoId=' . urlencode($r_acaoId) . '&extra=' . urlencode($r_extra) . '&simular=' . urlencode($r_simular)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	//case 'fin001ContentPagamento':
	case 'fin001ContentReparcelamento':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '80px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin001/form/fin001FormPagamento/', "&tab=geral&T005_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2) . '&extra=' . urlencode($r_extra) . '&simular=' . urlencode(@$r_simular)),
			array($content2, '/fin/fin001/tab/fin001TabGridPagamento/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) . '&extra=' . urlencode($r_extra) . '&simular=' . urlencode(@$r_simular)),
		);
		echo gProcessaAcoes($actions, true);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if(!empty($r_simular)){
			$botoesEsq .= '<button type="submit" id="fin001ImprimirSimulacao">Imprimir</button>';
			$botoesEsq .= '<button type="submit" id="fin001EnviarSimulacao">Enviar Email</button>';
		} else {
			$botoesEsq .= '<button type="submit" id="fin001ReparcelarFinal">Reparcelar</button>';
		}
		$botoesEsq .= '<button type="submit" id="fin001LimparGrid">Limpar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		$sql = mysql_query("SELECT * FROM T002 WHERE T002_Flag_Reparcelar='S'");
		$res = mysql_fetch_array($sql);
		$D024_Id = $res['T002_D024_Id'];
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001EnviarSimulacao').unbind('click').bind('click', function() {
					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Reparcelamento', '/etc/etc006/content/etc006ContentEmailEnviar/','&T180_D024_Id={$D024_Id}', [1000,600]);
				});
				$('#fin001ImprimirSimulacao').unbind('click').bind('click', function() {
					nova_janela('/etc/etc006/form_func-ajax/imprimirSimulacao/?ajax=true&T180_D024_Id='+encodeURIComponent("{$D024_Id}"),0,0,1000,600,'no','no','no','no','no','no','Imprimir Simulação');
				});
			} </script></div>
EOT;

	break;
	
	case 'fin001ContentDetalhesCheque':
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
		array($content1, '/fin/fin001/form/fin001FormDetalhesCheque/', "&tab=geral&acaoId=" . urlencode($r_T180_Id)),
		);
		echo gProcessaAcoes($actions);			
	
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="fin001FormDetalhesCheque_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');			
	break;
	
	case 'fin001content07':
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
			array($content1, '/fin/fin001/form/fin001form02/', "&tab=acrescimos_descontos&acaoId=" . urlencode($r_acaoId) . "&divRefresh=" . urlencode($content2)),
			array($content2, '/fin/fin001/grid/fin001grid02/', "&acaoId=" . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);		
	break;

	case 'fin001ContentRetornoRemessas':
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
			array($content1, '/fin/fin001/form/fin001FormRetornoRemessas/', "&tab=geral&divGridId=" . urlencode($content2)),
			array($content2, '/fin/fin001/grid/fin001GridRetornoRemessas/'),
		);
		echo gProcessaAcoes($actions);
	break; 
	
	case 'fin001contentRelatorioRetorno':
		$T037_Id = (isset($r_T037_Id)) ? $r_T037_Id : false;
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
			array($content1, '/fin/fin001/tab/fin001TabRelatorioRetorno/', "&tab=geral&T037_Id=" . urlencode($T037_Id)),
		);

		echo gProcessaAcoes($actions);

		$botoesEsq = '<button type="submit" id="fin001processarRetorno">Processar Retorno</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');

		$sqlD007 = mysql_query("SELECT D007_Nome_Banco FROM T037 LEFT JOIN D007 ON D007_Id=T037_D007_Id WHERE T037_Id = '{$T037_Id}'");
		$rowD007 = mysql_fetch_array($sqlD007);
		if(!empty($rowD007['D007_Nome_Banco'])){
			$stringBanco = " no banco<br><br><span style='font-size: 13px; font-weight: bold;'>{$rowD007['D007_Nome_Banco']}</span> ";
		}
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001processarRetorno').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							var fin001ProcessarRetornoLimit = 10;
							var idProgressBar = dialogAlert(
								'Processando retorno',
								"<div style='overflow:hidden;'>" +
									"<div id='progressBar' style='line-height:16px;'>Processando 0%</div>" +
									"<div style='margin-top:6px; text-align:center;'>" +
										"<img src='/hardness3/static/img/loading.gif' style='width:20px; height:20px;' />" +
									"</div>" +
								"</div>"
							);
							$('#dc-' + idProgressBar).parent().css('width', '220px');
							fin001ProcessarRetornoLoop(idProgressBar, fin001ProcessarRetornoLimit, 0, 0, 0);
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Processar Retorno', "Deseja baixar os titulos disponíveis{$stringBanco}?", \$buttons);
				});

				function fin001ProcessarRetornoLoop(idProgressBar, limite, lastId, ok, total) {
					var fin001ProcessarRetornoUrl = '/fin/fin001/grid_func-ajax/processarRetorno/?ajax=true&step=1&limit=' + encodeURIComponent(limite) + '&lastId=' + encodeURIComponent(lastId) + '&T037_Id=' + encodeURIComponent("{$T037_Id}") + '&callback=?';
					$.getJSON(fin001ProcessarRetornoUrl, function(request) {
						if (request.code == true) {
							var novoTotal = total;
							if (request.data && request.data.total && (!novoTotal || novoTotal <= 0)) {
								novoTotal = parseInt(request.data.total, 10) || 0;
							}
							var batchRead = 0;
							if (request.data && request.data.batchRead) {
								batchRead = parseInt(request.data.batchRead, 10) || 0;
							}
							var novoOk = ok + batchRead;
							if (novoTotal > 0 && novoOk > novoTotal) {
								novoOk = novoTotal;
							}
							var percentual = 0;
							if (novoTotal > 0) {
								percentual = Math.round((novoOk * 100) / novoTotal);
								if (percentual > 100) { percentual = 100; }
							}
							var doneFront = (novoTotal > 0 && novoOk >= novoTotal);
							if (doneFront) {
								percentual = 100;
							}
							var concluido = ((request.data && request.data.done) || doneFront) ? 1 : 0;
							$('#progressBar').html(
								'<div style="margin-bottom:4px;"><b>Processado ' + percentual + '%</b></div>' +
								'<div>Processados: <b>' + novoOk + '</b></div>' +
								'<div>Total Registros: <b>' + (novoTotal > 0 ? novoTotal : '...') + '</b></div>'
							);

							if (concluido == 0) {
								var novoLastId = lastId;
								if (request.data && request.data.lastId) {
									novoLastId = request.data.lastId;
								}
								fin001ProcessarRetornoLoop(idProgressBar, limite, novoLastId, novoOk, novoTotal);
							} else {
								$('#dc-' + idProgressBar).dialog("destroy").remove();
								dialogConfirm(
									'Retorno processado',
									'<b>Processado ' + percentual + '%</b>' +
									'<br/><br/>Processados: <b>' + novoOk + '</b>' +
									'<br/>Total Registros: <b>' + (novoTotal > 0 ? novoTotal : novoOk) + '</b>'
								);
								divRefresh('{$content1}', true);
							}
						} else {
							$('#dc-' + idProgressBar).dialog("destroy").remove();
							dialogConfirm('Erro!', 'Não foi possível processar o retorno.');
						}
					});
				}
			}</script></div>
EOT;
	break;

	case 'fin001ContentCobrancas':
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
			array($content1, '/fin/fin001/tab/fin001TabCobrancas/', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		echo gProcessaAcoes($actions);

	break;

	case 'fin001ContentAlterarVencimento':
		$extra = isset($r_extra) ? $r_extra : false;

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
			array($content1, '/fin/fin001/tab/fin001TabAlterarVencimento/', '&extra=' . urlencode($extra)),
		);
		
		echo gProcessaAcoes($actions);
	break;

	case 'fin001ContentCarta':	
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
			array($content1, '/fin/fin001/form/fin001FormCarta/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		echo gProcessaAcoes($actions);
	break;

	case 'fin001ContentDuplicarTitulos':
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
			array($content1, '/fin/fin001/form/fin001DuplicarTitulos/', '&tab=geral&extra=' . urlencode($r_extra)),
		);
		
		echo gProcessaAcoes($actions);
	break;

	case 'fin001ContentCancelamento':
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
			array($content1, '/fin/fin001/form/fin001CancelarTitulos/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		echo gProcessaAcoes($actions);
	break;

	/**
	* Agrupar
	* Chamado em: fin001grid01
	*/
	case 'fin001contentAgrupar':
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
			array($content1, '/fin/fin001/grid/fin001grid05/','&extra='.urlencode($r_extra)),
			array($content2, '/fin/fin001/form/fin001form07/', "&tab=agrupar&extra=".urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';		
		$botoesDir .= '<button type="submit" id="content_agrupar_btn">Agrupar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';		
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'fin001ContentSerasaRelato':
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
			array($content1, '/fin/fin001/tab/fin001TabSerasaRelato/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		echo gProcessaAcoes($actions);
	break;

	case 'fin001ContentDocumentos':
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
			array($content1, '/fin/fin001/form/fin001FormDocumentos/', "&tab=geral&divReload=" . urlencode($content2)),
			array($content2, '/fin/fin001/grid/fin001GridDocumentos/', ''),
		);
		echo gProcessaAcoes($actions, true);
	break;

	case 'fin001ContentAgendaCobranca':
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
			array($content1, '/fin/fin001/tab/fin001TabAgendaCobranca/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions, true);
	break;

	case 'fin001ContentCreditoTrocaCentral':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			echo '<div class="msgError">Usuário sem acesso à opção Contas a Receber.</div>';
			break;
		}
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			array($content1, '/fin/fin001/tab/fin001TabCreditoTrocaCentral/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'fin001ContentCreditoTrocaCliente':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			echo '<div class="msgError">Usuário sem acesso à opção Contas a Receber.</div>';
			break;
		}
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			array(
				$content1,
				'/fin/fin001/tab/fin001TabCreditoTrocaCliente/',
				'&D024_Id=' . urlencode($r_D024_Id)
			)
		);
		echo gProcessaAcoes($actions, true);
	break;

	case 'fin001ContentAbaterSaldoCreditoTroca':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			echo '<div class="msgError">Usuário sem acesso à opção Contas a Receber.</div>';
			break;
		}
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			array($content1, '/fin/fin001/tab/fin001TabAbaterSaldoCreditoTroca/', '&D024_Id=' . urlencode($r_D024_Id) . '&divOrigem=' . urlencode($r_divOrigem))
		);
		echo gProcessaAcoes($actions, true);
	break;

	case 'fin001ContentCreditosClientes':
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
			array($content1, '/fin/fin001/tab/fin001TabCreditosClientes/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions, true);
	break;

	case 'fin001ContentTitulosVencidos':
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
			array($content1, '/fin/fin001/tab/fin001TabTitulosVencidos/', "&tab=geral&D024_Id=" . urlencode($r_D024_Id)),
		);
		echo gProcessaAcoes($actions, true);
	break;

	case 'fin001contentCheque':
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T002_Id = (isset($r_T002_Id)) ? $r_T002_Id : false;
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
			array($content1, '/fin/fin001/tab/fin001TabIncluirCheque/', '&acaoId=' . urlencode($r_acaoId) . '&T246_Id=' . urlencode($acaoId) . '&T002_Id=' . urlencode($T002_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'fin001ContentConfiguracoesFinanceiro':
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
			array($content1, '/fin/fin001/tab/fin001TabConfiguracoesFinanceiro/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'fin001ImportarCsv':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '40px;');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);        
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
           	array($content1, '/fin/fin001/form/fin001FormImportarCSV/',"&tab=geral&acaoId=" . urlencode($r_acaoId)),
		    array($content2, '/fin/fin001/outro/fin001ManualCSVCliente/','&tab=geral')
		);
		echo  gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		
	break;

	case 'fin001contentBoletosOutrosPortadores':
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
			array($content1, '/fin/fin001/tab/fin001tabBoletosOutrosPortadores/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'fin001ContentTitulosParaEnvio':
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
			array($content1, '/fin/fin001/tab/fin001TabTitulosParaEnvio/', '&tab=geral&extra=' . urlencode($r_extra))
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesEsq .= '<button type="submit" id="fin001EnviarTitulos">Enviar Padrão</button>';
		$botoesEsq .= '<button type="submit" id="fin001EnviarTitulosLembrete">Enviar Lembrete</button>';
		$botoesEsq .= '<button type="submit" id="fin001EnviarTitulosCobranca">Enviar Cobrança</button>';
		$botoesEsq .= '<button type="submit" id="fin005EnviarDanfes">Enviar Títulos e DANFES</button>';
		//$botoesEsq .= '<button type="submit" id="fin005EnviarACP">Enviar Títulos e ACP</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001EnviarTitulos').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin001/grid_func-ajax/enviarMultiEmailBoleto/?ajax=true&emailPadrao=true&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Atenção', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$content1}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Enviar Boletos Modelo Padrão', 'Você irá enviar o modelo <b>PADRÃO</b> de cobrança, deseja continuar? ', \$buttons);
				});

				$('#fin001EnviarTitulosLembrete').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin001/grid_func-ajax/enviarMultiEmailBoleto/?ajax=true&emailLembrete=true&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Atenção', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$content1}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Enviar Boletos Modelo Lembrete', 'Você irá enviar o modelo <b>LEMBRETE</b> de cobrança, deseja continuar? ', \$buttons);
				});

				$('#fin001EnviarTitulosCobranca').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin001/grid_func-ajax/enviarMultiEmailBoleto/?ajax=true&emailCobranca=true&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Atenção', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$content1}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Enviar Boletos Modelo Cobrança', 'Você irá enviar o modelo de <b>COBRANÇA</b>, deseja continuar? ', \$buttons);
				});

				$('#fin005EnviarDanfes').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin001/grid_func-ajax/enviarMultiEmailBoleto/?ajax=true&emailPadrao=true&extra=' + encodeURIComponent("{$r_extra}") + '&enviarDanfe=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Atenção', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$content1}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Enviar Boletos', 'Você tem certeza? ', \$buttons);
				});

				$('#fin005EnviarACP').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin005/grid_func-ajax/enviarMultiEmailBoletoACP/?ajax=true&extra=' + encodeURIComponent("{$r_extra}") + '&enviarACP=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Atenção', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$content1}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Enviar Boletos e ACP', 'Você tem certeza? ', \$buttons);
				});
			} </script></div>
EOT;
	break;

	case 'fin001ContentDividir':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '170px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin001/tab/fin001TabDividir/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2) . '&extra=' . urlencode($r_extra)),
			array($content2, '/fin/fin001/content/fin001ContentDivisao/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) .  '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions, true);
   break;

   case 'fin001ContentDivisao':
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
			array($content1, '/fin/fin001/form/fin001FormDivisao/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2) . '&extra=' . urlencode($r_extra)),
			array($content2, '/fin/fin001/tab/fin001TabDivisao/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) . '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions, true);

		$novoIdTelaForm = uniqid();
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesEsq .= '<button type="submit" id="fin001DividirBtn">Dividir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		$extra_ = urlencode($r_extra);
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001DividirBtn').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/fin/fin001/grid_func-ajax/fin001Dividir/?ajax=true&&C007_Id=' + encodeURIComponent("{$g['empresaAtual']}") + '&valorSomado=' + encodeURIComponent("{$valorSomado}")+ '&extra={$extra_}&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Ok', 'Processo concluído com sucesso', \$buttons);
								divRefresh('{$g['divIdParent']}', true);
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
				dialogConfirm('Dividir duplicata(s)', 'Você tem certeza? ', \$buttons);
			});
		} </script></div>
EOT;
	break;	
}
