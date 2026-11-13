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

		case 'cad030ContentPrincipal':
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
			array($content1, '/cad/cad030/tab/cad030TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);

		break;

		case 'cad030contentCadastroUsuario':
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
			array($content1, '/cad/cad030/tab/cad030TabCadastroUsuario/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);

		break;


		case 'cad030ContentRecriarSenha':
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
			array($content1, '/cad/cad030/form/cad030FormRecriarSenha/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			);
			echo gProcessaAcoes($actions);

		break;

		case 'cad030ContentUsuariosB2B':
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
				array($content1, '/cad/cad030/grid/cad030GridUsuariosB2B/','')
			);
			echo gProcessaAcoes($actions);

		break;

		case 'cad030ContentUsuarioB2B':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			
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
				array($content1, '/cad/cad030/tab/cad030TabUsuariosB2B/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			);
			
			echo gProcessaAcoes($actions);

			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário

		break;

		case 'cad030ContentUsuariosApi':
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
			array($content1, '/cad/cad030/grid/cad030GridUsuariosApi/','')
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'cad030ContentUsuarioApi':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			
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
				array($content1, '/cad/cad030/form/cad030FormUsuariosApi/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			);
			
			echo gProcessaAcoes($actions);

			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário

		break;

		case 'cad030ContentPerfilUsuario':
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
			array($content1, '/cad/cad030/form/cad030FormPerfilUsuario/', '&tab=geral&C007_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/cad/cad030/grid/cad030GridPerfilUsuario/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
			
		case 'cad030ContentCadastroUsuarioAtual':
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
			array($content1, '/cad/cad030/tab/cad030TabCadastroUsuarioAtual/','')
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'cad030ContentPastasEmail':
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
				array($content1, '/cad/cad030/tab/cad030TabPastasEmail/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);

		break;

		case 'cad030PerfilEmailContent':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(array($content1, '/cad/cad030/tab/cad030PerfilEmailTab/', '&acaoId=' . urlencode($r_acaoId)));
			echo gProcessaAcoes($actions);
		break;

		case 'cad030FeriadosContent':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(array($content1, '/cad/cad030/tab/cad030FeriadosTab/', '&acaoId=' . urlencode($r_acaoId)));
			echo gProcessaAcoes($actions);
		break;

		case 'cad030FeriadosContentEdicao':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(array($content1, '/cad/cad030/tab/cad030FeriadosTabEdicao/', '&acaoId=' . urlencode($r_acaoId)));
			echo gProcessaAcoes($actions);
		break;

		case 'cad030DataFeriadosContent':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(array($content1, '/cad/cad030/tab/cad030DataFeriadosTabEdicao/', '&acaoId=' . urlencode($r_acaoId)));
			echo gProcessaAcoes($actions);
		break;
	}

?>
