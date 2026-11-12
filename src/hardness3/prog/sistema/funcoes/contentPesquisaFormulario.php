<?php
namespace hardness;
switch ($g['r'][3]) {	
	case 'contentPesquisaFormulario':
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
			array($content1, '/sistema/funcoes/pesquisaFormulario/' . $r_divIdAberto, '&tab=geral&sql=' . urlencode($r_sql) . '&sqlExtra=' . urlencode($r_sqlExtra) . '&labels=' . urlencode($r_labels) . '&divIdAberto=' . urlencode($r_divIdAberto))
		);
		echo gProcessaAcoes($actions);
	break;
}
?>
