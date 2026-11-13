<?php
namespace hardness;
	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		case 'est020ContentPrincipal':
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
			array($content1, '/est/est020/tab/est020TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
		case 'est020ContentIncluirConhecimento':
			/*
			* Divisão da Area em Conteúdos
			*/
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/est/est020/tab/est020TabCadastroCTe/', '&acao=' . urlencode($r_acao) . '&acaoId=' . urlencode($r_acaoId) . '&tab=' . @urlencode($r_tab))
			);
			echo gProcessaAcoes($actions);	
	
			/**
			* Botões
			*/
			// Definição dos botões				
			if ($r_acaoId){
				$sql="select T084_Numero_Protocolo_Cte,T084_Flag_Travar,T084_Data_Hora_Protocolo_CTe from T084 where T084_Id={$r_acaoId}";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
			}

			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';

			if(empty($row['T084_Numero_Protocolo_Cte'])) {				
				if($row['T084_Flag_Travar'] == 'N'){ 
					$botoesEsq .= '<button type="submit" id="est020FinalizarCTe">Finalizar</button>';
					$botoesEsq .= '<button type="submit" id="est020ExcluirCTe">Excluir</button>';
					$botoesEsq .= '<button type="submit" id="est020AtualizarAliquotas">Atualizar Aliquotas</button>';
				} else {
					if(empty($r_acao))  {
						$botoesEsq .= '<button type="submit" id="est020HabilitarEdicaoCte">Habilitar edição</button>';	
					}
				}				
			} else {				
				$row['T084_Data_Hora_Protocolo_CTe'] = gCorrigeData($row['T084_Data_Hora_Protocolo_CTe']);
				$botoesEsq .= "<span style=\"margin-left: 3px;\">CT-e transmitido com sucesso!  Protocolo: {$row['T084_Numero_Protocolo_Cte']} Data: {$row['T084_Data_Hora_Protocolo_CTe']}</span>";
			}

			$botoesDir .= '<button type="submit" id="est020FormPrincipalBtn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';

			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');		

			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {				
	            $('#est020FinalizarCTe').unbind('click').bind('click', function(data) {
	            	var \$buttons = {
	            		"Sim": function() {
	            			$('#{$g['divId']}').showLoading();
	            			$.getJSON('/est/est020/form_func-ajax/finalizarCTe/?ajax=true&T084_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
	            				if(request.code){
									$.getJSON('/est/est020/form_func-ajax/finalizarCTeContinuarLiberacao/?ajax=true&T084_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {										
										$('#{$g['divId']}').hideLoading();
										if(request.code){
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', 'CT-e Finalizado', \$buttons);
											divRefresh('{$g['divIdParent']}', true);
											fecharJanela("{$g['divId']}");											
										}else{
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    						dialogConfirm('Erro ao liberar CT-e', request.data, \$buttons);		
											divRefresh('{$g['divIdParent']}', true);									
										}
									});	            					
            					}else{
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro ao validar CT-e', request.data, \$buttons);			
									divRefresh('{$g['divIdParent']}', true);			    								    				
            					}
	            			});
	            			$(this).dialog("destroy").remove();
	            		},
	            		"Não": function() { $(this).dialog("destroy").remove(); } 
	               	};
	               	dialogConfirm('Finalizar CT-e', 'Você tem certeza? ', \$buttons);
	            });
				$('#est020HabilitarEdicaoCte').unbind('click').bind('click', function(data){
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est020/form_func-ajax/habilitarEdicaoCTe/?ajax=true&T084_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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
					dialogConfirm('Habilitar edição', 'Deseja habilitar a edição do CT-e?', \$buttons);
				});
				$('#est020AtualizarAliquotas').unbind('click').bind('click', function(data){
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est020/form_func-ajax/atualizarAliquotas/?ajax=true&T084_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									// $('#{$g['divId']}').parent().parent().find('.dialog-refresh').trigger('click');
									// divRefresh('{$g['divIdParent']}');
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
									divRefresh('{$g['divIdParent']}', true);
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
					dialogConfirm('Atualizar Alíquotas', 'Você tem certeza?', \$buttons);
				});
				$('#est020ExcluirCTe').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est020/grid_func-ajax/excluirCTe/?ajax=true&T084_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', "CT-e Excluido!", \$buttons);
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
					dialogConfirm('Atenção', 'Deseja excluir o CT-e? <font color="red"><br/>Este processo irá excluir o CT-e e seus itens.</font>', \$buttons);
				});
			} </script></div>
EOT;
		break;

		case 'est020ContentTecnico':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormTecnico/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),	
			);
			echo gProcessaAcoes($actions);
		break;	

		case 'est020ContentIncluirDocumento':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormSelecionarTipoDocumento/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),	
			);
			echo gProcessaAcoes($actions);
		break;	

		case 'est020ContentCancelarCte':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormCancelarCte/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),	
			);
			echo gProcessaAcoes($actions);
		break;	

		
		case 'est020ContentInfoCarga':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content1Height', '150px');
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormInfoCarga/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),	
				array($content2, '/est/est020/grid/est020GridQuantidadeCarga/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2)),				
			);
			echo gProcessaAcoes($actions);
		break;			

		case 'est020ContentIncluirQuantidadeCarga':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormIncluirQuantidadeCarga/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  '&T084_Id=' . urlencode($r_T084_Id)),	
			);
			echo gProcessaAcoes($actions);
		break;	

		
		case 'est020ContentValores':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content1Height', '150px');
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormValores/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),	
				array($content2, '/est/est020/grid/est020GridComponentePrestacao/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2)),				
			);
			echo gProcessaAcoes($actions);
		break;	

		case 'est020ContentIncluirComponente':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormIncluirComponente/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  '&T084_Id=' . urlencode($r_T084_Id)),	
			);
			echo gProcessaAcoes($actions);
		break;	

		case 'est020ContentAdicionais':
				/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content1Height', '150px');
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/est/est020/form/est020FormAdicionais/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				array($content2, '/est/est020/grid/est020GridObservacoesContribuinte/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),				
			);
			echo gProcessaAcoes($actions);
		break;	
		case 'est020ContentIncluirObservacaoContribuinte':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormIncluirObservacaoContribuinte/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  '&T084_Id=' . urlencode($r_T084_Id)),	
			);
			echo gProcessaAcoes($actions);
		break;	

		case 'est020ContentIncluirObservacaoFisco':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(			
				array($content1, '/est/est020/form/est020FormIncluirObservacaoFisco/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  '&T084_Id=' . urlencode($r_T084_Id)),	
			);
			echo gProcessaAcoes($actions);
		break;	

		case 'est020ContentPagamentos':
			/*
			 * Divisão da Area em Conteúdos
			 */
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$content2 = uniqid();
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '160px');		
			$g['smarty']->assign('tipo', '2linhas');
			
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)				
				array($content1, '/est/est020/form/est020FormPagamentos/', '&tab=geral&T084_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content2).'&acaoId=' . urlencode($r_acaoId)),
				array($content2, '/est/est020/grid/est020GridPagamentos/', '&divReload=' . urlencode($content1).'&acaoId=' . urlencode($r_acaoId)),				
			);
			echo gProcessaAcoes($actions);	    
		
		break;
		case 'est020ContentCTeSefaz':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(							
				array($content1, '/est/est020/tab/est020TabEnviarCTe/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;	

		case 'est020ContentIncluirDocumentoAnterior':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(							
				array($content1, '/est/est020/form/est020FormIncluirDocumentoAnterior/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  '&T084_Id=' . urlencode($r_T084_Id)),	
			);
			echo gProcessaAcoes($actions);
		break;
		
		
		case 'est020ContentIncluirNfeCte':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(							
				array($content1, '/est/est020/form/est020FormIncluirNfeCte/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  '&T084_Id=' . urlencode($r_T084_Id)),	
			);
			echo gProcessaAcoes($actions);
		break;	

		case 'est020ContentIncluirOutroDocumento':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(							
				array($content1, '/est/est020/form/est020FormIncluirOutroDocumento/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  '&T084_Id=' . urlencode($r_T084_Id)),	
			);
			echo gProcessaAcoes($actions);
		break;	
}			
			
?>