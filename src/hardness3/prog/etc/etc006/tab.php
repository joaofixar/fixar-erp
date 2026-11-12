<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Tab Leitura
	* Chamado em: etc006ContentLeitura
	*/
	case 'etc006TabLeitura':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc006/grid/etc006GridSent/', 'Emails Enviados'),
		);
		if ($g['C031']['recebimentoEmail'] == 'S') {
			$tabsList[] = array(uniqid(), '/etc/etc006/grid/etc006GridInbox/', 'Emails Recebidos');
			$tabsList[] = array(uniqid(), '/etc/etc006/grid/etc006GridSpam/', 'Spam');
		}
		$tabsList[] = array(uniqid(), '/etc/etc006/grid/etc006GridDump/', 'Lixeira');
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		if (!empty($r_spam) && $r_spam == 'true') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				setTimeout(function(){
					$('#{$g['divId']} > .tabs').tabs('select', 1);
				},50);
			}</script></div>
EOT;
		}
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	/**
	* Tab Email
	* Chamado em: etc006ContentEmail
	*/
	case 'etc006TabEmail':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if($r_T259_Id > 0){
			$tabsList[] = array(uniqid(), '/etc/etc006/content/etc006ContentEmailTarefa/', 'Email', "&acaoId=" . urlencode($r_acaoId));
		} else {
			$tabsList[] = array(uniqid(), '/etc/etc006/form/etc006FormEmail/', 'Email', "&acaoId=" . urlencode($r_acaoId));
		}
		$tabsList[] = array(uniqid(), '/etc/etc006/form/etc006FormAssoc/', 'Associações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	/**
	* Tab Email
	* Chamado em: etc006ContentEmailEnviar
	*/
	case 'etc006TabEmailEnviar':
	
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs

		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc006/form/etc006FormEmailEnviar/', 'Email', "&acaoId=" . urlencode($r_acaoId) 
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
	
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'etc006TabCadastroContato':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc006/form/etc006FormCadastroContato/', 'Cadastrar Contato', '&acaoId=' . urlencode($r_acaoId) . '&tab=geral&T139_Id=' . urlencode($r_T139_Id) . '&email=' . urlencode($r_email))
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc006TabLeituraCliente':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if ($g['C031']['recebimentoEmail'] == 'S') {
			$tabsList[] = array(uniqid(), '/etc/etc006/grid/etc006GridInboxCliente/', 'Emails Recebidos', '&D024_Id=' . urlencode($r_D024_Id));
		}
		$tabsList[] = array(uniqid(), '/etc/etc006/grid/etc006GridSentCliente/', 'Emails Enviados', '&D024_Id=' . urlencode($r_D024_Id));
		$tabsList[] = array(uniqid(), '/etc/etc006/grid/etc006GridDumpCliente/', 'Lixeira', '&D024_Id=' . urlencode($r_D024_Id));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc006TabWhitelist':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc006/gridWhitelist/etc006GridWhitelist/', 'Whitelist', ''),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	// TAB OBSOLETO NÃO TEREMOS MAIS INBOX NO SISTEMA 05/04/2023
	case 'etc006TabSpam':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc006/grid/etc006GridSpam/', 'Spam', ''),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc006TabOrcamento':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc006/content/etc006ContentOrcamento/', 'Orçamento', ''),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	case 'etc006TabLeitor':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		// Pega infos
		require_once('sistema/comunicacaoEmail.php');
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
		$anexos = $mail->listaAnexos($r_acaoId);

		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" style="margin-bottom: 0px;" class="contentCabecalho"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/etc/etc006/tab/etc006CabecalhoTabLeitor/', '&acaoId=' . urlencode($r_acaoId) . '&divIdRefresh=' . urlencode($g['divId']) . '&anexos=' . urlencode(serialize($anexos)), true),
		);

		echo gProcessaAcoes($actions);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if($r_file != ''){
			$extensaoAnexo = substr($anexos[$r_file], -4);
			$tabsList[] = array(uniqid(), '/etc/etc006/leituraAnexo/leitor/', '<img src="/hardness3/static/img/tmp/anexo.png" style="width: 15px; height: 15px; margin-right: 5px; position: absolute;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$anexos[$r_file], "&nomeArquivo={$anexos[$r_file]}&extensao={$extensaoAnexo}&acaoId=" . urlencode($r_acaoId) . '&file=' . urlencode($r_file));
		} else {
			$tabsList[] = array(uniqid(), '/etc/etc006/form/etc006FormEmail/', 'Email', "&removerCabecalho=true&acaoId=" . urlencode($r_acaoId));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc006CabecalhoTabLeitor':
		$anexos = unserialize($r_anexos);
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				function alternarLeituraAnexo(divId, anexo){
					if(typeof(anexo) == "undefined"){
						anexo = '';
					}
					divSetarAcaoId(divId, anexo, 'file');
					var divIdContent = $('#' + divId).closest('.ui-dialog-content').attr('id');
					$('#'+divIdContent).data('janelaVars', $('#'+divIdContent).data('janelaVars').replace(/file=[0-9]/, 'file='+anexo));
					$("[id^='etc006AnexoForm']").attr('file', anexo);
					divRefresh(divIdContent);
				}
			} </script></div>
			<div style="clear:both;"></div>
			<div class="contentCabecalho" style="margin-bottom: 0px;">
				<div style="display: inline; float: left; margin: 0 10px;"><a href=# style="color: #000;"; onclick="alternarLeituraAnexo('{$r_divIdRefresh}');">E-mail</a></div>
EOT;
		//Verifica se o anexo foi arquivado em algum produto
		$arrayAnexosArquivados = array();
		$sqlAnexosArquivados = mysql_query("SELECT T140_Id_Anexo, D001_Codigo_Produto, D001_Descricao_Produto FROM T140 LEFT JOIN D001 ON D001_Id=T140_D001_Id WHERE T140_T139_Id = '{$r_acaoId}'");
		while($rowAnexosArquivados = mysql_fetch_assoc($sqlAnexosArquivados)){
			$arrayAnexosArquivados[$rowAnexosArquivados['T140_Id_Anexo']][] = $rowAnexosArquivados['D001_Codigo_Produto'] . ' - ' . $rowAnexosArquivados['D001_Descricao_Produto'];
		}
		if (sizeof($anexos) > 0) {
			for ($j=0;$j<sizeof($anexos);$j++) {
   				$anexos[$j] = mb_strtolower($anexos[$j], 'UTF-8');
   				if(empty($anexos[$j])) { continue; }
   				$nomeCompleto = $anexos[$j];
   				if(strlen($anexos[$j]) > 22){
   					$nomeAnexo = strrev(substr(strrev($anexos[$j]),0,4));
   					$anexos[$j] = substr($anexos[$j], 0, 15) . '..' . $nomeAnexo;
   				}
   				$produtoAnexado = $cssProdutoAnexado = '';
   				if(isset($arrayAnexosArquivados[$j])){
   					foreach ($arrayAnexosArquivados[$j] as $produto) {
   						$produtoAnexado .= trim($produto)."\n";
   					}
   					$produtoAnexado = "\n Anexado ao(s) produto(s): \n" . $produtoAnexado;
   					$cssProdutoAnexado = 'color: blue;';
   				}
				if(preg_match('/[\.pdf|\.png|\.jpg|\.txt|\.xls|\.xlsx|\.doc|\.docx]$/i', $anexos[$j])){
					echo "<div style=\"display: inline; float: left; margin-right: 10px;\" title=\"{$nomeCompleto} {$produtoAnexado}\"><span onclick=\"downloadAnexo('{$r_acaoId}', '{$j}');\"><span class=\"ui-icon ui-icon-arrowthick-1-s\" style=\"float:left; cursor: pointer; margin-right: -4px;\" title=\"Download anexo\"></span></span><span class=\"ui-icon ui-icon-document\" style=\"float: left;\"></span><a href=\"#\" style=\"color: #413a3a; {$cssProdutoAnexado}\" onclick=\"alternarLeituraAnexo('{$r_divIdRefresh}', '{$j}');\">{$anexos[$j]}</a></div>";
				} else {
   					echo "<div style=\"display: inline; float: left; margin-right: 10px;\" title=\"{$nomeCompleto} {$produtoAnexado}\"><span class=\"ui-icon ui-icon-document\" style=\"float: left;\"></span><a href=\"#\" style=\"color: #413a3a; {$cssProdutoAnexado}\" onclick=\"downloadAnexo('{$r_acaoId}', '{$j}')\">{$anexos[$j]}</a></div>";
   				}
   			}
		}
		echo <<<EOT
			</div>
			<div style="clear:both;"></div>
EOT;
	break;

	case 'etc006TabArquivarAnexo':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc006/form/etc006FormArquivarAnexo/', ucfirst($r_tipo), '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc006TabEditarWhitelist':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc006/form/etc006FormEditarWhitelist/', 'Registro', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
