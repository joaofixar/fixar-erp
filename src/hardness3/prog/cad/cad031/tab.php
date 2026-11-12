<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/11
	*/
	case 'cad031TabEmpresas':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad031/grid/cad031GridEmpresas/', 'Empresas', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad031/grid/cad031GridEmpresasInativas/', 'Inativas', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		    array(uniqid(), '/cad/cad031/content/cad031ContentAvisosIndex/', 'Avisos', ''),	
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad031TabCadastroEmpresas':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		global $confUsuario,$sys;

		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'Parâmetros', '&tab=parametros&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'Impostos', '&tab=impostos&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'Pedido de Venda', '&tab=pedidoDeVenda&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'Configurações', '&tab=configuracoes&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'Juros', '&tab=juros&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroObservacoes/', 'Rodapé', '&tab=rodape&acaoId=' . urlencode($r_acaoId), true, true);
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroLogomarca/', 'Logo', '&tab=logomarca&acaoId=' . urlencode($r_acaoId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroLogoB2B/', 'LogoB2B', '&tab=logomarca&acaoId=' . urlencode($r_acaoId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'Visual', '&tab=visual&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'MDF-e', '&tab=mdfe&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormCadastroEmpresas/', 'CT-e', '&tab=cte&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad031TabCadastroAvisosIndex':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)

		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();

		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormAvisosIndex/', 'Avisos', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&C004_Id=' . urlencode($r_C004_Id));
		if(!empty($r_acaoId)){
			$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormAvisosImagem/', 'Imagem fundo', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_C004_Id));
			$tabsList[] = array(uniqid(), '/cad/cad031/grid/cad031GridAvisosPerfil/', 'Perfil', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_C004_Id));
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad031TabAvisosIndex':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();

		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/cad/cad031/grid/cad031GridAvisosIndex/', 'Avisos', '&C004_Id=' . urlencode($r_C004_Id));	
		$tabsList[] = array(uniqid(), '/cad/cad031/grid/cad031GridAvisosIndexInativos/', 'Avisos Inativos', '&C004_Id=' . urlencode($r_C004_Id));	
				
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;	
	case 'cad031TabFormAvisosPerfil':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();

		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/cad/cad031/form/cad031FormAvisosPerfil/', 'Perfil aviso', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&C032_Id=' . urlencode($r_C032_Id));
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

}

?>
