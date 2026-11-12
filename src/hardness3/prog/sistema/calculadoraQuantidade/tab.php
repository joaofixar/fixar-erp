<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'calculadoraQuantidade':
		$parametros = '&formId=' . urlencode(isset($_REQUEST['formId']) ? $_REQUEST['formId'] : '')
			. '&evento=' . urlencode(isset($_REQUEST['evento']) ? $_REQUEST['evento'] : '')
			. '&altura=' . urlencode(isset($_REQUEST['altura']) ? $_REQUEST['altura'] : '')
			. '&largura=' . urlencode(isset($_REQUEST['largura']) ? $_REQUEST['largura'] : '')
			. '&comprimento=' . urlencode(isset($_REQUEST['comprimento']) ? $_REQUEST['comprimento'] : '')
			. '&resultado=' . urlencode(isset($_REQUEST['resultado']) ? $_REQUEST['resultado'] : '');
		$tabs = array();
		echo gProcessaTabs(array(array(uniqid(), '/sistema/calculadoraQuantidade/form/calculadoraQuantidade/', 'Quantidade', $parametros)), $tabs);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
