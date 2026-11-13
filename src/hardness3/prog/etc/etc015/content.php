<?php
namespace hardness;
	
switch ($g['r'][3]) {
	
	case 'etc015Reports':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc015/tab/etc015Reports/', "&acaoId=" . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'etc015CriarRelatorio':
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		//$g['smarty']->assign('content1Height', '150px');
		$g['smarty']->assign('tipo', '2linhas');
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc015/outro/etc015CriarViaSql/', "&acaoId=" . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/etc/etc015/outro/etc015GerarGrid/', "&acaoId=" . urlencode($r_acaoId) . "&sql=" . urlencode(@$r_sql))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'etc015VerRelatorio':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc015/tab/etc015VerRelatorio/', "&acaoId=" . urlencode($r_acaoId) . '&sql=' . urlencode(@$r_sql) . '&personalizar=' . urlencode(@$r_personalizar)),
		);
		echo gProcessaAcoes($actions);
	break;
	
}
