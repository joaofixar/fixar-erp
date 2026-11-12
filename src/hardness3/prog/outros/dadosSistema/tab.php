<?php
namespace hardness;

switch ($g['r'][3]) {

	case 'dadosSistemaTab':

		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/outros/dadosSistema/outro/dadosDisco/', 'Disco'),
			array(uniqid(), '/outros/dadosSistema/outro/dadosEmpresasServidor/', 'Dados Empresas'),
			array(uniqid(), '/outros/dadosSistema/outro/dadosEmpresaAtual/', 'Empresa Atual'),
			array(uniqid(), '/outros/dadosSistema/outro/dadosUsuarios/', 'Usuários Empresa'),
			array(uniqid(), '/outros/dadosSistema/outro/dadosBancoDados/', 'Banco Dados'),
			array(uniqid(), '/outros/dadosSistema/outro/dadosBancoDadosEmpresaAtual/', 'Banco Dados Atual'),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

}
