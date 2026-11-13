<?php

namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabMDFe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/grid/fis012GridMDFe/', 'MDF-e', "&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/fis/fis012/content/fis012contentMDFeSefaz/', 'Enviar MDF-e para Sefaz', "&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Raquel Pazini
	*/
	case 'fis012TabEnviarMDFe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/grid/fis012GridEnviarMDFe/', 'Enviar', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/fis/fis012/grid/fis012GridEncerrarMDFe/', 'Encerrar', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/fis/fis012/grid/fis012GridEncerradosMDFe/', 'Encerrados', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabMdfeCadastro':
		// Form (caso seja o mesmo form em multiplas tabs) // '&formId=' . urlencode($formId)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/fis/fis012/outro/fis012CabecalhoMdfe/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}

		if($r_acao == 'incluir'){		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/fis/fis012/form/fis012FormPrincipal/', 'Cabeçalho', "&tab=cabecalho&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			);
		} else {
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/fis/fis012/content/fis012ContentDocumentos/', 'Documentos', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId) . '&T276_Id=' . urlencode($r_acaoId)),
				array(uniqid(), '/fis/fis012/form/fis012FormPrincipal/', 'Cabeçalho', "&tab=cabecalho&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/fis/fis012/content/fis012ContentModal/', 'Rodoviário', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/fis/fis012/content/fis012ContentSeguradora/', 'Seguradora', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/fis/fis012/form/fis012FormPrincipal/', 'Produto Predominante', "&tab=produtoPredominante&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),			
				array(uniqid(), '/fis/fis012/form/fis012FormPrincipal/', 'Adicionais', "&tab=adicionais&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),			
				array(uniqid(), '/fis/fis012/content/fis012ContentLacres/', 'Lacres', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/fis/fis012/content/fis012ContentTecnico/', 'Técnico', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			);
		}
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fis012TabAlterarDescarregamento':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fis/fis012/form/fis012FormAlterarDescarregamento/', 'Descarregamento', '&tab=geral&T276_Id=' . urlencode($r_T276_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Raquel Pazini
	*/
	case 'fis012TabMdfeDocumentos':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/grid/fis012GridIncluirNfe/', 'NFes', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id)),
			array(uniqid(), '/fis/fis012/grid/fis012GridIncluirCte/', 'CTes', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabCadastroNfeMdfe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/form/fis012FormCadastroNfeMdfe/', 'Incluir de NF-e', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


	/**
	* @author Lígia Nakazato
	*/
	case 'fis012TabIncluirVeiculoModal':
	log("T276_Id no fis012TabIncluirVeiculoModal: ".$r_T276_Id);
	log("T283_Id no fis012TabIncluirVeiculoModal: ".$r_acaoId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/form/fis012FormIncluirVeiculoModal/', 'Veículo', "&tab=geral&T276_Id=" .  urlencode($r_T276_Id) . "&acaoId=".urlencode($_REQUEST['acaoId']), true)
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Lígia Nakazato
	*/
	case 'fis012TabRodoviario':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/content/fis012ContentRodoviarioVeiculo/', 'Veículo', "&tab=geral&T276_Id=" . urlencode($r_T276_Id) . "&acaoId=".urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/fis/fis012/content/fis012Content2rowsCadastrarMotoristaMdfe/', 'Motorista', "&tab=geral&T276_Id=" . urlencode($r_T276_Id) . "&acaoId=".urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/fis/fis012/content/fis012ContentLacresModalRodoviario/', 'Lacres', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id)),
			array(uniqid(), '/fis/fis012/content/fis012GridInfoPgtoFrete/', 'Info Pgto. Frete', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId) . '&T276_Id=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

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

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabCadastrarTipoTransporte':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/content/fis012Content2rowsCadastrarTipoTransporte/', 'Tipo Transporte', "&acaoId=" . urlencode($r_acaoId) . '&T284_Id=' . urlencode($r_T284_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabCadastrarMotoristaMdfe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/content/fis012Content2rowsCadastrarMotoristaMdfe/', 'Motorista', "&acaoId=" . urlencode($r_acaoId) . '&276_Id=' . urlencode($r_276_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabCadastrarTipoTransporteCargas':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/content/fis012Content2rowsCadastrarTipoTransporteCargas/', 'Transporte Carga', "&acaoId=" . urlencode($r_acaoId) . '&T281_Id=' . urlencode($r_T281_Id) . '&276_Id=' . urlencode($r_276_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012TabCadastrarAverbacaoSeguradora':
		// Definição das Tabs
		log("tab 1 - T279:".$r_T279_Id);
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/content/fis012Content2rowsCadastrarAverbacaoSeguradora/', 'Averbação', "&acaoId=" . urlencode($r_acaoId) . '&T279_Id=' . urlencode($r_T279_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabAlterarNfeMdfe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/form/fis012FormCadastroNfeMdfe/', 'Cabeçalho NF-e', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id)),
			array(uniqid(), '/fis/fis012/content/fis012Content2rowsCadastrarTipoTransporte/', 'Tipo Transporte', '&T276_Id=' . urlencode($r_T276_Id) . '&T284_Id=' . urlencode($r_T284_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabCadastrarTipoTransporteCargaLacre':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/content/fis012Content2rowsCadastrarTipoTransporteCargaLacre/', 'Carga Lacre', "&acaoId=" . urlencode($r_acaoId) . '&T282_Id=' . urlencode($r_T282_Id) . '&T276_Id=' . urlencode($r_T276_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012TabCadastrarTipoTransporteLacre':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/content/fis012Content2rowsCadastrarTipoTransporteLacre/', 'Transporte Lacre', "&acaoId=" . urlencode($r_acaoId) . '&T281_Id=' . urlencode($r_T281_Id) . '&T276_Id=' . urlencode($r_T276_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* @author Raquel Pazini Cardoso
	*/
	case 'fis012TabConfiguracaoMDFe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad064/content/cad064ContentCadastroVeiculos/', 'Veículos'),
			array(uniqid(), '/cad/cad065/content/cad065ContentCadastroLacres/', 'Lacres'),
			array(uniqid(), '/cad/cad066/content/cad066ContentCadastroSeguradoras/', 'Seguradoras'),

		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fis012TabCadastroCteMdfe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/form/fis012FormCadastroCteMdfe/', 'Incluir de CT-e', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fis012TabAlterarCTeMdfe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/form/fis012FormCadastroCteMdfe/', 'Cabeçalho CT-e', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id)),			
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fis012FormInfoPgtoFrete':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/form/fis012FormInfoPgtoFrete/', 'Info Pgto. Frete', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T276_Id=' . urlencode($r_T276_Id)),			
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList,  $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fis012AlterarInfoPgtoFrete':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/grid/fis012GridInfoPgtoFreteComponente/', 'Componentes', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T318_Id=' . urlencode($r_T318_Id)),
			array(uniqid(), '/fis/fis012/grid/fis012GridInfoPgtoFreteParcelas/', 'Parcelas', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T318_Id=' . urlencode($r_T318_Id)),
			array(uniqid(), '/fis/fis012/form/fis012FormInfoPgtoFrete/', 'Info Pgto. Frete', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T318_Id=' . urlencode($r_T318_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList,  $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fis012FormInfoPgtoFreteComponente':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/form/fis012FormInfoPgtoFreteComponente/', 'Componente', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T318_Id=' . urlencode($r_T318_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList,  $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fis012FormInfoPgtoFreteParcela':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis012/form/fis012FormInfoPgtoFreteParcela/', 'Parcela', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T318_Id=' . urlencode($r_T318_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList,  $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>
