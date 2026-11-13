<?php
namespace hardness;

	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: est004Content01
		*/

		case 'est020TabPrincipal':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)				
				array(uniqid(), '/est/est020/grid/est020GridConhecimentos/', 'CT-e', "&tab=geral&acaoId=" . urlencode($r_acaoId)),				
				array(uniqid(), '/est/est020/content/est020ContentCTeSefaz/', 'Transmitir SEFAZ', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),												
  			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;	

		case 'est020TabCadastroCTe':	
			// Form (caso seja o mesmo form em multiplas tabs) // '&formId=' . urlencode($formId)
			$formId = uniqid();
			$g['smarty']->assign('formId', $formId);
			/**
			* Cabeçalho (Versão Acima da TAB)
			*/
			if (!empty($r_acaoId)) {
				$cabecalhoId = uniqid();
				echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
				$actions = array(
					// identificador, url, paramentros get (opcional)
					array($cabecalhoId, '/est/est020/outro/est020CabecalhoCte/', '&acaoId=' . urlencode($r_acaoId), true),
				);
				echo gProcessaAcoes($actions);
			}
			
			$tabs = array();

			if($r_acao == 'incluir'){		
				// Definição das Tabs
				$tabsList = array(
					array(uniqid(), '/est/est020/form/est020FormPrincipal/', 'Cabeçalho', "&tab=cabecalho&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				);
			} else {
				// Definição das Tabs			
				$tabsList = array(
					array(uniqid(), '/est/est020/grid/est020GridDocumentos/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId) . '&acao=alterar'),
					array(uniqid(), '/est/est020/form/est020FormPrincipal/', 'Cabeçalho', "&tab=cabecalho&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),					
					array(uniqid(), '/est/est020/content/est020ContentInfoCarga/', 'Informações da Carga', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),	
					array(uniqid(), '/est/est020/content/est020ContentValores/', 'Valores e Impostos', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),					
					array(uniqid(), '/est/est020/content/est020ContentAdicionais/', 'Adicionais', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
					array(uniqid(), '/est/est020/grid/est020GridDocumentosAnterior/', 'Documentos Transporte Anterior', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
					array(uniqid(), '/est/est020/content/est020ContentTecnico/', 'Técnico', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),									
					array(uniqid(), '/est/est020/content/est020ContentPagamentos/', 'Pagamentos', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId),true,"divRefresh(\'{$g['divId']}\')"),									
					array(uniqid(), '/est/est020/grid/est020GridPagamentosTitulos/', 'a Receber', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId),true,"divRefresh(\'{$g['divId']}\')"),									
					
				);			
			}
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs, true);

			// Carrega template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;		
		case 'est020TabEnviarCTe':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est020/grid/est020GridEnviarCTe/', 'Enviar', "&tab=geral&acaoId=" . urlencode($r_acaoId)),				
				array(uniqid(), '/est/est020/grid/est020GridEnviadas/', 'Enviadas', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);
	
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);
	
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
	}

?>
