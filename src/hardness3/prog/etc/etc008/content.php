<?php
namespace hardness;
switch ($g['r'][3]) {

	case 'etc008ContentBancoDadosClientes':
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
			array($content1, '/etc/etc008/tab/etc008TabBancoDadosClientes/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc008ContentFormBancoDadosClientesIncluir':
		$C024_Banco_Dados =(isset($r_C024_Banco_Dados)) ? $r_C024_Banco_Dados : false;
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
			array($content1, '/etc/etc008/tab/etc008TabFormBancoDadosClientes/', '&acaoId=' . urlencode($r_acaoId) . '&C024_Banco_Dados=' . urlencode($C024_Banco_Dados))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'etc008ContentFormCadastroDB':
		echo "<iframe src=\"{$confUsuario['urlRaiz']}/hardness3/site/requisicoes/cadastroDominio.php\" style=\"width: 100%; height: 100%; border: 0px;\"/>";
	break;
}
