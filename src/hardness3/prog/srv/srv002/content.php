<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 */
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
		* Grid Principal
		* Chamado em: Menu
		*/
	case 'srv002content01':
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
		array($content1, '/srv/srv002/tab/srv002tab01/'),
		//array($content2, '/srv/srv002/tab/srv002tab02/', "&acaoId=" . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'srv002content08':
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
		//array($content1, '/srv/srv002/tab/srv002tab01/', "&divReload=" . urlencode($content2)),
		array($content1, '/srv/srv002/tab/srv002tab02/', "&acaoId=" . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* Form Principal
	* Chamado em: srv002grid01
	*/
	case 'srv002content02':
		$status = (isset($r_Status)) ? $r_Status : false;
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/srv/srv002/tab/srv002tab03/', '&acaoId=' . urlencode($r_acaoId) . '&status=' . $status),
		);
		echo gProcessaAcoes($actions);

		break;

	case 'srv002content03':
		/*
			* Divisão da Area em Conteúdos
			*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/srv/srv002/tab/srv002tab03/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

		break;

	case 'srv002content04':
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
		array($content1, '/srv/srv002/grid/srv002grid01/', "&tab=editar_item&T005_Id=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($content2)),
		array($content2, '/srv/srv002/grid/srv002grid02/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

		break;

	case 'srv002content05':
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
		array($content1, '/srv/srv002/form/srv002form03/', "&tab=editar_servico&T005_Id=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($content2)),
		array($content2, '/srv/srv002/grid/srv002grid03/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

		break;

	case 'srv002content06':
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
		array($content1, '/srv/srv002/form/srv002form04/', "&tab=editar_material&T147_Id=" . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content2)),
		array($content2, '/srv/srv002/grid/srv002grid04/', '&T147_Id=' . urlencode($r_T147_Id)  . "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

		break;
			
	case 'srv002content07':
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
		array($content1, '/srv/srv002/form/srv002form05/', "&tab=geral&T147_Id=" . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content2)),
		array($content2, '/srv/srv002/grid/srv002grid05/', '&acaoId=' . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'srv002ContentHistVeiculos':
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
			array($content1, '/srv/srv002/tab/srv002tabHistVeiculo/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'srv002contentPendentes':
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
			array($content1, '/srv/srv002/grid/srv002GridPendentes/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'srv002contentExecucao':
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
			array($content1, '/srv/srv002/grid/srv002GridPendentes/', '&acaoId=' . urlencode($r_acaoId). '&Status=E&Pausado=N'),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'srv002contentPausados':
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
			array($content1, '/srv/srv002/grid/srv002GridPendentes/', '&acaoId=' . urlencode($r_acaoId). '&Status=E&Pausado=S'),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'srv002ContentIncluir':
		$status = (isset($r_Status)) ? $r_Status : false;
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/srv/srv002/tab/srv002TabIncluir/', '&acaoId=' . urlencode($r_acaoId) . '&status=' . $status),
		);
		echo gProcessaAcoes($actions);

		break;
}
?>
