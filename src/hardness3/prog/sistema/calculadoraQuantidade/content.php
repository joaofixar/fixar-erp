<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'calculadoraQuantidade':
		$contentId = uniqid();
		$g['smarty']->assign('content1', $contentId);
		$g['smarty']->assign('tipo', 'unico');
		$parametros = '&formId=' . urlencode(isset($_REQUEST['formId']) ? $_REQUEST['formId'] : '')
			. '&evento=' . urlencode(isset($_REQUEST['evento']) ? $_REQUEST['evento'] : '')
			. '&altura=' . urlencode(isset($_REQUEST['altura']) ? $_REQUEST['altura'] : '')
			. '&largura=' . urlencode(isset($_REQUEST['largura']) ? $_REQUEST['largura'] : '')
			. '&comprimento=' . urlencode(isset($_REQUEST['comprimento']) ? $_REQUEST['comprimento'] : '')
			. '&resultado=' . urlencode(isset($_REQUEST['resultado']) ? $_REQUEST['resultado'] : '');
		echo gProcessaAcoes(array(array($contentId, '/sistema/calculadoraQuantidade/tab/calculadoraQuantidade/', $parametros)));
	break;
}
