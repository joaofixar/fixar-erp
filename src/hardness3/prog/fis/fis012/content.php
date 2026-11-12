<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012ContentPrincipal':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabMDFe/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);	
	break;

	case 'fis012ContentAlterarDescarregamento':

		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabAlterarDescarregamento/', '&T276_Id=' . urlencode($r_T276_Id)),
		);
		echo gProcessaAcoes($actions);
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="fis012AlterarDescarregamento">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	/**
	* @author Raquel Pazini
	* @since ...
	*/
	case 'fis012contentMDFeSefaz':
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
			array($content1, '/fis/fis012/tab/fis012TabEnviarMDFe/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);	
	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012ContentLacres':
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
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarLacreMdfe/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridLacres/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012ContentLacresModalRodoviario':
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
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarLacreRodoviario/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridLacresModalRodoviario/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012ContentTecnico':
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
			array($content1, '/fis/fis012/form/fis012FormTecnico/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array($content2, '/fis/fis012/grid/fis012GridTecnico/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2)),
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012MdfeCadastro':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabMdfeCadastro/', '&acao=' . urlencode($r_acao) . '&acaoId=' . urlencode($r_acaoId) . '&tab=' . @urlencode($r_tab) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);	

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, T276_Flag_Travar_MDFe, T276_C004_Id FROM T276 WHERE T276_Id='{$r_acaoId}'");
			$mT276 = mysql_fetch_assoc($T276);	
		}
		if(empty($mT276['T276_Numero_Protocolo_MDFe'])){
			$botoesEsq .= '<button type="submit" id="fis012PrevisualizarDAMDFE">Pré-visualizar</button>';
			if($mT276['T276_Flag_Travar_MDFe']=='N'){
				$botoesEsq .= '<button type="submit" id="excluirMDFe">Excluir</button>';
				$botoesEsq .= '<button type="submit" id="fis012FinalizarMDFe">Finalizar</button>';
			}else{
				$botoesEsq .= '<button type="submit" id="fis012HabilitarEdicaoMDFe">Habilitar edição</button>';	
			}
		}else{
			$botoesEsq .= '<button type="submit" id="fis012VisualizarDAMDFE">DAMDFe</button>';
		}

		$botoesDir .= '<button type="submit" id="fis012FormPrincipal_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		echo <<<EOT
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	        	$('#fis012VisualizarDAMDFE').unbind('click').bind('click', function(data){
					nova_janela('/hardness3/hardness/damdfe/imprimir_damdfe.php?T276_Id='+encodeURIComponent("{$r_acaoId}")+'&T276_C004_Id='+encodeURIComponent({$mT276['T276_C004_Id']}),0,0,1000,600,'no','no','no','no','no','no','Pre_Danfe');
				});

				$('#excluirMDFe').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fis/fis012/grid_func-ajax/excluirMDFe/?ajax=true&T276_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', "MDF-e Excluido!", \$buttons);
									divRefresh('{$g['divIdParent']}', true);
									fecharJanela("{$g['divId']}");
								} else {
								 	var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								 	dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();					
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atenção', 'Deseja excluir o MDF-e? <font color="red"><br/>Este processo irá excluir o MDF-e e seus itens.</font>', \$buttons);
				});

	            $('#fis012FinalizarMDFe').unbind('click').bind('click', function(data) {
	            	var \$buttons = {
	            		"Sim": function() {
	            			$('#{$g['divId']}').showLoading();
	            			$.getJSON('/fis/fis012/form_func-ajax/finalizarMDFe/?ajax=true&T276_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
	            				if(request.code){
	            					var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Ok', 'MDFe Finalizado', \$buttons);
				    				divRefresh('{$g['divIdParent']}', true);
									fecharJanela("{$g['divId']}");
									$('#{$g['divId']}').hideLoading();
            					}else{
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro ao validar MDFe', request.data, \$buttons);
				    				$('#{$g['divId']}').hideLoading();
            					}
	            			});
	            			$(this).dialog("destroy").remove();
	            		},
	            		"Não": function() { $(this).dialog("destroy").remove(); } 
	               	};
	               	dialogConfirm('Finalizar MDFe', 'Você tem certeza? ', \$buttons);
	            });
	            $('#fis012PrevisualizarDAMDFE').unbind('click').bind('click', function(data){
					nova_janela('/hardness3/hardness/nota_emitida2/gerar_pre_damdfe.php?T276_Id='+encodeURIComponent("{$r_acaoId}")+'&T276_C004_Id='+encodeURIComponent({$mT276['T276_C004_Id']}),0,0,1000,600,'no','no','no','no','no','no','Pre_Danfe');
				});
				$('#fis012HabilitarEdicaoMDFe').unbind('click').bind('click', function(data){
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fis/fis012/form_func-ajax/habilitarEdicaoMDFe/?ajax=true&T276_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									$('#{$g['divId']}').parent().parent().find('.dialog-refresh').trigger('click');
									divRefresh('{$g['divIdParent']}');
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Habilitar edição', 'Deseja habilitar a edição do MDF-e?', \$buttons);
				});
	        } </script></div>
EOT;
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012ContentDocumentos':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabMdfeDocumentos/', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);	
	break;

	/**
	* @author Alexa Mahara
	*/
	case'fis012ContentSeguradora':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '75px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarSeguradora/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridCadastrarSeguradora/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id)  . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012ContentCadastroNfeMdfe':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabCadastroNfeMdfe/', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);	
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012ContentAlterarNfeMdfe':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/fis/fis012/tab/fis012TabAlterarNfeMdfe/', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id) . '&T284_Id=' . urlencode($r_T284_Id))
		);
		echo gProcessaAcoes($actions);	
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012ContentCancelarMdfe':
    	/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCancelarMdfe/', '&tab=geral&T276_Id=' . urlencode($r_T276_Id)),
		);
		echo gProcessaAcoes($actions);
    break;

    /**
	* @author Ricardo Norio Miyata
	*/
	case'fis012ContentCadastrarMotoristaMdfe':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabCadastrarMotoristaMdfe/', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
    case'fis012Content2rowsCadastrarMotoristaMdfe':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '30px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');	
		// O que será carregado em cada conteúdo?
		$actions = array(
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarMotoristaMdfe/', '&tab=geral&T276_Id=' . urlencode($r_T276_Id)  . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridCadastrarMotoristaMdfe/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id)  . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');

	break;
	
	/**
	* @author Lígia Nakazato
	*/
	case'fis012ContentModal':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabRodoviario/', '&T276_Id=' . urlencode($r_acaoId) . '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	/**
	* @author Ricardo Norio Miyata
	*/
	case'fis012ContentCadastrarTipoTransporte':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabCadastrarTipoTransporte/', '&acaoId=' . urlencode($r_acaoId) . '&T284_Id=' . urlencode($r_T284_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case'fis012Content2rowsCadastrarTipoTransporte':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '80px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarTipoTransporte/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T284_Id=' . urlencode($r_T284_Id)  . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridCadastrarTipoTransporte/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id) .  '&T284_Id=' . urlencode($r_T284_Id)  . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	* @author Lígia Nakazato
	*/
	case 'fis012ContentRodoviarioVeiculo':
	log("T276_Id no fis012ContentRodoviarioVeiculo: ".$r_T276_Id);
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
			array($content1, '/fis/fis012/grid/fis012GridRodoviarioVeiculo', '&T276_Id=' . urlencode($r_T276_Id) . '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);	
	break;

	/**
	* @author Lígia Nakazato
	*/
	case 'fis012ContentIncluirVeiculoModal':
	log("T276_Id no fis012ContentIncluirVeiculoModal: ".$r_T276_Id);
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
			array($content1, '/fis/fis012/tab/fis012TabIncluirVeiculoModal', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id='.urlencode($r_T276_Id)),
		);
		echo gProcessaAcoes($actions);	

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="fis012FormIncluirVeiculoModalBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case'fis012ContentCadastrarTipoTransporteCargas':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabCadastrarTipoTransporteCargas/', '&acaoId=' . urlencode($r_acaoId) . '&T281_Id=' . urlencode($r_T281_Id) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* @author Alexa Mahara
	*/
	case'fis012ContentCadastrarAverbacao':
		/*
		* Divisão da Area em Conteúdos
		*/
		log("contetn 1 - T279:".$r_T279_Id);
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabCadastrarAverbacaoSeguradora/', '&acaoId=' . urlencode($r_acaoId) . '&T279_Id=' . urlencode($r_T279_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
    case'fis012Content2rowsCadastrarTipoTransporteCargas':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '80px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');	
		// O que será carregado em cada conteúdo?
		$actions = array(
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarTipoTransporteCargas/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T281_Id=' . urlencode($r_T281_Id) . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridCadastrarTipoTransporteCargas/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T281_Id=' . urlencode($r_T281_Id) . '&T276_Id=' . urlencode($r_T276_Id) . '&divReload=' . urlencode($content1))
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');

	break;

	/**
	* @author Alexa Mahara
	*/
    case'fis012Content2rowsCadastrarAverbacaoSeguradora':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '30px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');	
		// O que será carregado em cada conteúdo?
		$actions = array(
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarAverbacaoSeguradora/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T279_Id=' . urlencode($r_T279_Id)  . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridCadastrarAverbacaoSeguradora/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T279_Id=' . urlencode($r_T279_Id)  . '&divReload=' . urlencode($content1))
		);
		echo gProcessaAcoes($actions);
	break;
	
	/**
	* @author Ricardo Norio Miyata
	*/
	case'fis012ContentCadastrarTipoTransporteCargaLacre':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabCadastrarTipoTransporteCargaLacre/', '&acaoId=' . urlencode($r_acaoId) . '&T282_Id=' . urlencode($r_T282_Id) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
    case'fis012Content2rowsCadastrarTipoTransporteCargaLacre':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '30px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');	
		// O que será carregado em cada conteúdo?
		$actions = array(
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarLacreCarga/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T282_Id=' . urlencode($r_T282_Id) . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridCadastrarTipoTransporteCargaLacre/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T282_Id=' . urlencode($r_T282_Id) . '&T276_Id=' . urlencode($r_T276_Id) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case'fis012ContentCadastrarTipoTransporteLacre':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabCadastrarTipoTransporteLacre/', '&acaoId=' . urlencode($r_acaoId) . '&T281_Id=' . urlencode($r_T281_Id) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
    case'fis012Content2rowsCadastrarTipoTransporteLacre':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '30px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');	
		// O que será carregado em cada conteúdo?
		$actions = array(
			// Identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/form/fis012FormCadastrarLacreTransporte/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T281_Id=' . urlencode($r_T281_Id) . '&T276_Id=' . urlencode($r_T276_Id) . '&divReload=' . urlencode($content2)),
			array($content2, '/fis/fis012/grid/fis012GridCadastrarTipoTransporteLacre/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T281_Id=' . urlencode($r_T281_Id) . '&T276_Id=' . urlencode($r_T276_Id) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	* @author Raquel Pazini 
	*/
	case 'fis012ContentConfiguracaoMDFe':
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
			array($content1, '/fis/fis012/tab/fis012TabConfiguracaoMDFe/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;

	case 'fis012ContentCadastroCTeMdfe':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012TabCadastroCteMdfe/', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);	
	break;

	case 'fis012ContentAlterarCTeMdfe':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/fis/fis012/tab/fis012TabAlterarCTeMdfe/', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id) . '&T284_Id=' . urlencode($r_T284_Id))
		);
		echo gProcessaAcoes($actions);	
	break;

	case 'fis012GridInfoPgtoFrete':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/fis/fis012/grid/fis012GridInfoPgtoFrete/', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);	
		break;

	case 'fis012FormInfoPgtoFrete':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/fis/fis012/tab/fis012FormInfoPgtoFrete/', '&acaoId=' . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id))
		);
		echo gProcessaAcoes($actions);	
		break;

	case 'fis012AlterarInfoPgtoFrete':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012AlterarInfoPgtoFrete/', '&acaoId=' . urlencode($r_acaoId) . '&T318_Id=' . urlencode($r_T318_Id))
		);
		echo gProcessaAcoes($actions);	
		break;

	case 'fis012FormInfoPgtoFreteComponente':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012FormInfoPgtoFreteComponente/', '&acaoId=' . urlencode($r_acaoId) . '&T318_Id=' . urlencode($r_T318_Id))
		);
		echo gProcessaAcoes($actions);	
		break;

	case 'fis012FormInfoPgtoFreteParcela':
		/*
		* Divisão da Area em Conteúdos
		*/
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fis/fis012/tab/fis012FormInfoPgtoFreteParcela/', '&acaoId=' . urlencode($r_acaoId) . '&T318_Id=' . urlencode($r_T318_Id))
		);
		echo gProcessaAcoes($actions);	
		break;
}