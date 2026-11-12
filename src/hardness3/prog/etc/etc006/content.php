<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Content Leitura
	* Chamado em: Menu
	*/
	case 'etc006ContentLeitura':
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
			array($content1, '/etc/etc006/tab/etc006TabLeitura/')
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* Content Email
	* Chamado em: etc006GridInbox / etc006GridSent
	*/
	case 'etc006ContentEmail':
		if($r_T259_Id > 0){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$g['divId']}").closest('.ui-dialog-content').bind("dialogclose", function(data, ui) {
						divRefresh('{$g['divIdParent']}');
					});
				} </script></div>
EOT;
		}
		$sql = mysql_query("SELECT T139_C007_Id, T139_Titulo, T139_Data_Hora FROM T139 WHERE T139_Id = '{$r_acaoId}'");
		if(mysql_num_rows($sql) > 0){
			$row = mysql_fetch_assoc($sql);
			if(empty($r_C007_Id)){
				$r_C007_Id = $row['T139_C007_Id'];
			}
			$row['T139_Data_Hora'] = gCorrigeData($row['T139_Data_Hora']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#ui-dialog-title-{$g['divId']}").html('{$row['T139_Titulo']} - {$row['T139_Data_Hora']}');
				} </script></div>
EOT;
		}
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
			array($content1, '/etc/etc006/tab/etc006TabEmail/', "&acaoId=" . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		if (!isset($r_p)) {
			$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$r_C007_Id}';";
			$resultado = mysql_query($sql);
			$C007 = mysql_fetch_assoc($resultado);

			// Pega infos
			require_once('sistema/comunicacaoEmail.php');
			$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
			$campos = $mail->pegarCamposEmail($r_acaoId);

			// Botões
			if($campos['T139_Flag_Spam'] == 'N'){
				if (empty($campos['T139_T003_Id'])) {
					$botoesEsq = "<button id='etc006FormEmailGerarOrca'>Novo orçamento</button>";
				} else {
					$botoesEsq = "<button id='etc006FormEmailGerarOrca'>Orçamento {$campos['T139_T003_Id']}</button>";
				}
			}
			$botoesEsq .= '<button id="etc006FormEmailResponder">Responder</button>';
			//$botoesEsq .= '<button id="etc006PreCadastrarCliente">Pré-Cadastrar Cliente</button>';
			$botoesEsq .= '<button id="etc006ImprimirEmail">Imprimir</button>';
			$botoesEsq .= "<button id='etc006FormEmailCadastrarProduto'>Cadastrar produto</button>";
			$botoesEsq .= "<button id='etc006FormEmailCadastrarCliente'>Cadastrar cliente</button>";
			if($campos['T139_Pasta'] == 'E'){
				$sqlD085 = mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T139_Id = '{$r_acaoId}'");
				if(mysql_num_rows($sqlD085) <= 0) {
					$botoesEsq .= "<button id='etc006CriarTarefaEmail'>Criar tarefa</button>";
				} else {
					$rowD085 = mysql_fetch_array($sqlD085);
					$botoesEsq .= "<button id='etc006AbrirTarefaEmail' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Tarefa - {$rowD085['D085_T259_Id']}', '/etc/etc017/content/etc017ContentTarefa/', '&acaoId=' + encodeURIComponent('{$rowD085['D085_T259_Id']}'), [900,600]);\">Tarefa {$rowD085['D085_T259_Id']}</button>";
				}
			}
			$botoesMid = '';
			$botoesDir = '';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->display('buttons-floatBottom.tpl');

			if (empty($campos['T139_T003_Id'])) {
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#etc006FormEmailGerarOrca').unbind('click').bind('click', function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&T139_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T003', [650,570]);
						});
					} </script></div>
EOT;
			} else {
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#etc006FormEmailGerarOrca').unbind('click').bind('click', function(data) {
							abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&T003_Id=' + encodeURIComponent('{$campos['T139_T003_Id']}'), [900,600]);
							//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$campos['T139_T003_Id']}') + '&tabela=T003', [1000,600]);
						});
					} </script></div>
EOT;
			}
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006FormEmailResponder').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Responder', '/etc/etc006/content/etc006ContentEmailEnviar/', '&acaoId=' + encodeURIComponent('{$campos['T139_Id']}') + '&tabela=T139', [1000,500]);
				});

				$('#etc006FormEmailCadastrarProduto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&C007_Id=' + encodeURIComponent("{$r_C007_Id}") + '&tela=cadastroProduto', [1000,600]);
				});

				$('#etc006FormEmailCadastrarCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&C007_Id=' + encodeURIComponent("{$r_C007_Id}") + '&tela=cadastroCliente', [1000,600]);
				});

				$('#etc006PreCadastrarCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastro de Cliente', '/cad/cad001/content/cad001ContentPreCadastrarCliente/', '&acaoId=' + encodeURIComponent('{$campos['T139_Id']}') + '&tabela=T139', [800,550]);
				});

				$('#etc006CriarTarefaEmail').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/etc/etc006/form_func-ajax/criarInteracaoEmail/?ajax=true&T139_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									$("#{$g['divId']}").parent().find('.dialog-refresh').trigger('click');
									abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Tarefa - ' + request.data, '/etc/etc017/content/etc017ContentTarefa/', '&acaoId=' + encodeURIComponent(request.data), [900,600]);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
		    		dialogConfirm('Atenção', 'Deseja criar tarefa?', \$buttons);
				});

				$('#etc006ImprimirEmail').unbind('click').bind('click', function(data) {
					nova_janela('/etc/etc006/form/etc006FormEmail/?ajax=true&acaoId='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','yes','yes','Imprimir E-mail');
					//function nova_janela(Pagina,Topo,Posicao,Largura,Altura,Fixo,Botoes,Endereco,Status,Rolagem,Menu,Nome) {
				});
			} </script></div>
EOT;
		}
	break;

	/**
	* Content Email Enviar
	* Chamado em: etc006GridInbox / etc006GridSent
	*/
	case 'etc006ContentEmailEnviar':

		$r_D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		$r_T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$r_T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;
		$r_D013_Id = isset($r_D013_Id) ? $r_D013_Id : false;
		$r_senhaB2B  = isset($r_senhaB2B)? $r_senhaB2B: false;
		$r_D117_Id = isset($r_D117_Id) ? $r_D117_Id : false;
		$r_T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$r_T224_Id = isset($r_T224_Id) ? $r_T224_Id : false;
		$r_T011_Id = isset($r_T011_Id) ? $r_T011_Id : false;
		$r_T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
		$r_T117_Id = isset($r_T117_Id) ? $r_T117_Id : false;
		$r_T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$r_T166_Id = isset($r_T166_Id) ? $r_T166_Id : false;
		$r_T196_Id = isset($r_T196_Id) ? $r_T196_Id : false;
		$r_T173_Id = isset($r_T173_Id) ? $r_T173_Id : false;
        $r_T199_Id = isset($r_T199_Id) ? $r_T199_Id : false;
        $r_T010_Id = isset($r_T010_Id) ? $r_T010_Id : false;
        $r_idNF = isset($r_idNF) ? $r_idNF : false;
        $r_T180_D024_Id = isset($r_T180_D024_Id) ? $r_T180_D024_Id : false;
		$r_ETC013OS = isset($r_ETC013OS) ? $r_ETC013OS : false;
		$r_T173_D024_Id = isset($r_T173_D024_Id) ? $r_T173_D024_Id : false;
		$r_sendGriHTML = isset($r_sendGriHTML) ? $r_sendGriHTML : false;
		$r_T173_Id_EmailGravar = isset($r_T173_Id_EmailGravar) ? $r_T173_Id_EmailGravar : false;
		$r_T002_Id = isset($r_T002_Id) ? $r_T002_Id : false;
		$r_emailLink = isset($r_emailLink) ? $r_emailLink : false;
		$r_emailTituloReceber = isset($r_emailTituloReceber) ? $r_emailTituloReceber : false;

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
			array($content1, '/etc/etc006/tab/etc006TabEmailEnviar/', "&acaoId=" . urlencode($r_acaoId) 
																	. "&D024_Id=" . urlencode($r_D024_Id) 
																	. "&T005_Id=" . urlencode($r_T005_Id) 
																	. "&T224_Id=" . urlencode($r_T224_Id) 
																	. "&D013_Id=" . urlencode($r_D013_Id) 
																	. "&senhaB2B=". urlencode($r_senhaB2B)
																	. "&T003_Id=" . urlencode($r_T003_Id) 
																	. "&T218_Id=" . urlencode($r_T218_Id) 
																	. "&D117_Id=" . urlencode($r_D117_Id)
																	. "&T011_Id=" . urlencode($r_T011_Id)
																	. "&T075_Id=" . urlencode($r_T075_Id)
																	. "&T117_Id=" . urlencode($r_T117_Id)
																	. "&T007_Id=" . urlencode($r_T007_Id)
																	. "&T166_Id=" . urlencode($r_T166_Id)
																	. "&T196_Id=" . urlencode($r_T196_Id)
																	. "&T173_Id=" . urlencode($r_T173_Id)
                                                                    . "&T199_Id=" . urlencode($r_T199_Id)
																	. "&ETC013OS=" . urlencode($r_ETC013OS)
																	. "&T010_Id=" . urlencode($r_T010_Id)
																	. "&T002_Id=" . urlencode($r_T002_Id)
																	. "&idNF=" . urlencode($r_idNF)
																	. "&T180_D024_Id=" . urlencode($r_T180_D024_Id)
																	. "&T173_D024_Id=" . urlencode($r_T173_D024_Id)
																	. "&T173_Id_EmailGravar=" . urlencode($r_T173_Id_EmailGravar)
																	. "&sendGriHTML=" . urlencode($r_sendGriHTML)
																	. "&emailLink=" . urlencode($r_emailLink)
																	. "&emailTituloReceber=" . urlencode($r_emailTituloReceber)
																	. "&emailFollowUp=" . urlencode($r_emailFollowUp)
																	));
		echo gProcessaAcoes($actions);
	break;
	
	/**
	* Content Leitura
	* Chamado em: Menu
	*/
	case 'etc006ContentCadastroContato':
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
			array($content1, '/etc/etc006/tab/etc006TabCadastroContato/', '&acaoId=' . urlencode($r_acaoId) . '&T139_Id=' . urlencode($r_T139_Id) . '&email=' . urlencode($r_email))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc006ContentRetornosPendentesAgenda':

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
			array($content1, '/etc/etc006/tab/etc006TabRetornosPendentesAgenda/', '')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc006ContentWhitelist':

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
			array($content1, '/etc/etc006/tab/etc006TabWhitelist/', '')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc006ContentEmailTarefa':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '58px');
		$g['smarty']->display('content-2rowsInverso.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc006/form/etc006FormInserirAgendaTarefa/', "&tab=geral"),
			array($content2, '/etc/etc006/form/etc006FormEmail/', "&acaoId=" . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc006ContentSpam':
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
			array($content1, '/etc/etc006/tab/etc006TabSpam/', "&tab=geral"),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc006ContentLeituraAnexo':
		$sql = mysql_query("SELECT T139_C007_Id, T139_Titulo, T139_Data_Hora, T139_De FROM T139 WHERE T139_Id = '{$r_acaoId}'");
		if(mysql_num_rows($sql) > 0){
			$row = mysql_fetch_assoc($sql);
			if(empty($r_C007_Id)){
				$r_C007_Id = $row['T139_C007_Id'];
			}
			$row['T139_Data_Hora'] = gCorrigeData($row['T139_Data_Hora']);
			//$row['T139_De'] = htmlentities($row['T139_De']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#ui-dialog-title-{$g['divId']}").html('{$row['T139_De']} - {$row['T139_Titulo']} - {$row['T139_Data_Hora']}');
				} </script></div>
EOT;
		}

		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$r_C007_Id}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);

		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
		$campos = $mail->pegarCamposEmail($r_acaoId);

		/*
		* Divisão da Area em Conteúdos
		*/
		if ((!empty($campos['T139_T003_Id']) && (!isset($r_ocultarOrcamento) || $r_ocultarOrcamento != 'true')) || isset($r_tela)) {
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '240px');
			$g['smarty']->display('content-2rowsInverso.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array();
			// identificador, url, paramentros get (opcional)
			$actions[] = array($content2, '/etc/etc006/tab/etc006TabLeitor/', "&acaoId=" . urlencode($r_acaoId));
			switch($r_tela){
				case 'cadastroCliente':
					$actions[] = array($content1, '/cad/cad001/content/cad001contentCadastro/', "&T139_Id=" . urlencode($r_acaoId) . '&acaoId=' . urlencode($r_D001_Id));
					break;
				case 'preCadastroProduto':
					$actions[] = array($content1, '/cad/cad002/content/cad002ContentPreCadastro/', "&T139_Id=" . urlencode($r_acaoId) . '&acaoId=' . urlencode($r_D001_Id));
					break;
				case 'cadastroProduto':
					$actions[] = array($content1, '/cad/cad002/content/form2/', "&T139_Id=" . urlencode($r_acaoId) . '&acaoId=' . urlencode($r_D001_Id));
					break;
				case 'orcamento':
					$actions[] = array($content1, '/etc/etc006/content/etc006ContentOrcamento/', "&acaoId=" . urlencode($r_acaoId));
					break;
				default:
					$actions[] = array($content1, '/etc/etc006/content/etc006ContentOrcamento/', "&acaoId=" . urlencode($r_acaoId));
					break;
			}

			echo gProcessaAcoes($actions);
		} else {
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/etc/etc006/tab/etc006TabLeitor/', "&acaoId=" . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

			// Botões
			if (empty($campos['T139_T003_Id'])) {
				$botoesEsq = "<button id='etc006AnexoFormEmailGerarOrca'>Novo orçamento</button>";
			} else {
				$botoesEsq = "<button id='etc006AnexoFormEmailGerarOrca'>Orçamento {$campos['T139_T003_Id']}</button>";
			}
			$botoesEsq .= '<button id="etc006AnexoFormEmailResponder">Responder</button>';
			//$botoesEsq .= '<button id="etc006AnexoPreCadastrarCliente">Pré-Cadastrar Cliente</button>';
			$botoesEsq .= "<button id=\"etc006AnexoImprimirEmail\" file=\"{$r_file}\">Imprimir</button>";
			$botoesEsq .= "<button id='etc006AnexoFormEmailCadastrarProduto' file=\"{$r_file}\">Cadastrar produto</button>";
			$botoesEsq .= "<button id='etc006AnexoFormEmailCadastrarCliente' file=\"{$r_file}\">Cadastrar cliente</button>";
			$botoesMid = '';
			$botoesDir = '';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->display('buttons-floatBottom.tpl');

			if (empty($campos['T139_T003_Id'])) {
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#etc006AnexoFormEmailGerarOrca').unbind('click').bind('click', function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&T139_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T003', [650,570]);
						});
					} </script></div>
EOT;
			} else {
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$('#etc006AnexoFormEmailGerarOrca').unbind('click').bind('click', function(data) {
							$('#{$g['divId']}').data('janelaVars', $('#{$g['divId']}').data('janelaVars').replace('ocultarOrcamento=true', 'ocultarOrcamento=false'));
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('refresh');
						});
					} </script></div>
EOT;
			}
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#etc006AnexoFormEmailResponder').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Responder', '/etc/etc006/content/etc006ContentEmailEnviar/', '&acaoId=' + encodeURIComponent('{$campos['T139_Id']}') + '&tabela=T139', [1000,500]);
					});

					$('#etc006AnexoPreCadastrarCliente').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastro de Cliente', '/cad/cad001/content/cad001ContentPreCadastrarCliente/', '&acaoId=' + encodeURIComponent('{$campos['T139_Id']}') + '&tabela=T139', [800,550]);
					});

					$('#etc006AnexoImprimirEmail').unbind('click').bind('click', function(data) {
						nova_janela('/etc/etc006/form/etc006FormEmail/?ajax=true&acaoId='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','yes','yes','Imprimir E-mail');
						//function nova_janela(Pagina,Topo,Posicao,Largura,Altura,Fixo,Botoes,Endereco,Status,Rolagem,Menu,Nome) {
					});

					$('#etc006AnexoFormEmailCadastrarProduto').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&file=' + encodeURIComponent($(this).attr('file')) + '&C007_Id=' + encodeURIComponent("{$r_C007_Id}") + '&tela=cadastroProduto', [1000,600]);
					});

					$('#etc006AnexoFormEmailCadastrarCliente').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&file=' + encodeURIComponent($(this).attr('file')) + '&C007_Id=' + encodeURIComponent("{$r_C007_Id}") + '&tela=cadastroCliente', [1000,600]);
					});
				} </script></div>
EOT;
		}
	break;

	case 'etc006ContentOrcamento':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Width', '300px');
		$g['smarty']->display('content-2columnsOrcamento.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven001/form/ven001FormProdutos/', '&tab=produto&telaEmail=true&T139_Id=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($r_T003_Id) . '&divReload=' . urlencode($r_divReload) . "&divOutro=" . urldecode($content2)),
			array($content2, '/ven/ven001/tab/ven001TabProdutosSimples/', '&acaoId=' . urlencode($r_T003_Id)),
		);
		echo gProcessaAcoes($actions);


		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$r_C007_Id}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);

		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
		$campos = $mail->pegarCamposEmail($r_acaoId);

		// Botões
		if (empty($campos['T139_T003_Id'])) {
			$textoBotaoOrcamento = "Novo orçamento";
		} else {
			$textoBotaoOrcamento = "Orçamento {$campos['T139_T003_Id']}";
		}
		$botoesEsq = "<button id='etc006AnexoFormEmailGerarOrca'>{$textoBotaoOrcamento}</button>";
		$botoesEsq .= '<button id="etc006AnexoFormEmailResponder">Responder</button>';
		$botoesEsq .= '<button id="etc006AnexoImprimirEmail">Imprimir</button>';
		$botoesMid = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');

		if (empty($campos['T139_T003_Id'])) {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#etc006AnexoFormEmailGerarOrca').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&T139_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T003', [650,570]);
					});
				} </script></div>
EOT;
		} else {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#etc006AnexoFormEmailGerarOrca').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$campos['T139_T003_Id']}') + '&tabela=T003', [1000,600]);
					});
				} </script></div>
EOT;
		}
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006AnexoFormEmailResponder').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Responder', '/etc/etc006/content/etc006ContentEmailEnviar/', '&acaoId=' + encodeURIComponent('{$campos['T139_Id']}') + '&tabela=T139', [1000,500]);
				});
				$('#etc006AnexoImprimirEmail').unbind('click').bind('click', function(data) {
					nova_janela('/etc/etc006/form/etc006FormEmail/?ajax=true&acaoId='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','yes','yes','Imprimir E-mail');
					//function nova_janela(Pagina,Topo,Posicao,Largura,Altura,Fixo,Botoes,Endereco,Status,Rolagem,Menu,Nome) {
				});
			} </script></div>
EOT;
	break;

	case 'etc006ContentArquivarAnexo':
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
			array($content1, '/etc/etc006/tab/etc006TabArquivarAnexo/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc006ContentEditarWhitelist':
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
			array($content1, '/etc/etc006/tab/etc006TabEditarWhitelist/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
}