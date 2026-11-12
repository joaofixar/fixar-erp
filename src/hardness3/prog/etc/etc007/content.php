<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'etc007ContentAuditoria':
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
			array($content1, '/etc/etc007/tab/etc007TabAuditoria/', "&tabela=" . urlencode($r_tabela) . "&id=" . urlencode($r_id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc007ContentAuditoriaGeral':
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
			array($content1, '/etc/etc007/tab/etc007TabAuditoriaGeral/', "&tabela=" . urlencode($r_tabela) . "&id=" . urlencode($r_id))
		);
		echo gProcessaAcoes($actions);
	break;
}
?>