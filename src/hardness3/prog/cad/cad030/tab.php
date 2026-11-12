<?php
namespace hardness;
	/**
	* Weiberlan
	* 28/11/2011
	*/

	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: cad030Content01
		*/

		case 'cad030TabPrincipal':
			global $g;
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad030/grid/cad030GridUsuarios/', 'Usuários', "&acaoId=" . urlencode($r_acaoId), false, true),
				array(uniqid(), '/cad/cad032/grid/cad032GridPerfis/', 'Perfis', "&acaoId=" . urlencode($r_acaoId), false, true),
				array(uniqid(), '/cad/cad030/grid/cad030GridUsuariosInativos/', 'Usuários Inativos', "&acaoId=" . urlencode($r_acaoId), false, true),
				array(uniqid(), '/cad/cad030/grid/cad030GridUsuariosFaixaAcessoIP/', 'IP Acesso', "&acaoId=" . urlencode($r_acaoId), false, true),
				array(uniqid(), '/cad/cad030/grid/cad030PerfilEmail/', 'Perfil Email', "&acaoId=" . urlencode($r_acaoId), false, true),
				array(uniqid(), '/cad/cad030/grid/cad030Feriados/', 'Feriados', "&acaoId=" . urlencode($r_acaoId), false, true),
			);

			if(isset($g['c029Ids'][248])){
				$tabsList[] = array(uniqid(), '/cad/cad030/content/cad030ContentUsuariosApi/', 'Usuários API', "&acaoId=" . urlencode($r_acaoId), false, true);
			}

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;


		case 'cad030TabCadastroUsuario':
            global $g;
			// Definição das Tabs
			$tabs = array();
			if(empty($r_acaoId)){
				$tabsList = array(
					// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
					//array(uniqid(), '/cad/cad030/form/cad030FormCadastroUsuario/', 'Usuário', "&tab=geral&acaoId=" . urlencode($g['usuarioAtual'])),
					array(uniqid(), '/cad/cad030/form/cad030FormCadastroUsuario/', 'Usuário', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				);
			}else{
				$tabsList = array(
					array(uniqid(), '/cad/cad030/form/cad030FormCadastroUsuario/', 'Usuário', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
					array(uniqid(), '/cad/cad030/content/cad030ContentPerfilUsuario/', 'Perfil', "&acaoId=" . urlencode($r_acaoId)),
					array(uniqid(), '/cad/cad030/form/cad030FormParametrosUsuario/', 'Parâmetros', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
					array(uniqid(), '/cad/cad030/form/cad030FormIntegracao/', 'Integração', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				);
			}
			

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;
			
		case 'cad030TabCadastroUsuarioAtual':
            global $g;
			// Definição das Tabs
			$tabs = array();
				$tabsList = array(
					// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
					array(uniqid(), '/cad/cad030/form/cad030FormCadastroUsuarioSimples/', 'Usuário', "&tab=geral&acaoId=" . urlencode($g['usuarioAtual'])),
					//array(uniqid(), '/cad/cad030/form/cad030FormCadastroUsuario/', 'Usuário', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				);
			
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;
			
		case 'cad030TabPastasEmail':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad030/form/cad030FormPastasEmail/', 'Pastas do Email', "&acaoId=" . urlencode($r_acaoId) . '&tab=geral'),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

		case 'cad030TabUsuariosB2B':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad030/form/cad030FormUsuariosB2B/', 'Usuário' ,"&tab=geral&acaoId=". urlencode($r_acaoId)),
			);

			if(!empty($r_acaoId)){
				$tabsList[] = array(uniqid(), '/cad/cad030/grid/cad030GridTokensB2B/', 'Tokens',"&acaoId=". urlencode($r_acaoId));
			}

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

		case 'cad030PerfilEmailTab':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad030/form/cad030PerfilEmailForm/', 'Perfil', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

		case 'cad030FeriadosTab':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad030/form/cad030FeriadosForm/', 'Perfil', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

		case 'cad030FeriadosTabEdicao':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad030/grid/cad030DataFeriados/', 'Datas', "&tab=geral&D206_Id=" . urlencode($r_acaoId)),
				array(uniqid(), '/cad/cad030/form/cad030FeriadosForm/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

		case 'cad030DataFeriadosTabEdicao':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad030/form/cad030DataFeriadosForm/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($r_acaoId))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
	}

?>
