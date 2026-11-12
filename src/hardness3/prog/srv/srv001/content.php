<?php
namespace hardness;
	switch ($g['r'][3]) {
		case 'srv001content001':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/srv/srv001/tab/srv001tab001/')
		);
		echo  gProcessaAcoes($actions);
		
		break;
		
	case 'form1':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		
		$r_acaoId = isset($r_acaoId) ? $r_acaoId : '';
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/srv/srv001/tab/tabForm1/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	 case 'form2':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');		
		// O que será carregado em cada conteúdo?
		
		$r_acaoId = isset($r_acaoId) ? $r_acaoId : '';
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/srv/srv001/tab/tabForm2/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	 case 'srv001contentProcessos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		
		$r_acaoId = isset($r_acaoId) ? $r_acaoId : '';
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/srv/srv001/form/srv001form002Processos/', '&tab=geral&D112_Id=' . urlencode($r_D112_Id) . '&divReload=' . urlencode($content2)),
			array($content2, '/srv/srv001/grid/srv001gridProcessos/', '&acaoId=' . urlencode($r_D112_Id) . '&divReload=' . urlencode($content1)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'srv001ContentMateriais':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		
		$r_acaoId = isset($r_acaoId) ? $r_acaoId : '';
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/srv/srv001/form/srv001form003Materiais/', '&tab=geral&D112_Id=' . urlencode($r_D112_Id) . '&divReload=' . urlencode($content2)),
			array($content2, '/srv/srv001/grid/srv001gridMateriais/', '&D112_Id=' . urlencode($r_D112_Id) . '&divReload=' . urlencode($content1)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	}
?>
