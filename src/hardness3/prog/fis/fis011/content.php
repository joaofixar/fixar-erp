<?php
namespace hardness;
	switch ($g['r'][3]) {
		case 'fis011ContentSpedFiscal':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Width', '150px');
			$g['smarty']->assign('tipo', '2colunas');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/fis/fis011/outro/fis011LogoSped/', '&spedTipo=Fiscal'),
				array($content2, '/fis/fis011/form/fis011FormSpedFiscal/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'fis011ContentPrincipal':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Width', '200px');
			$g['smarty']->assign('tipo', '2colunas');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/fis/fis011/outro/fis011LogoSped/', '&spedTipo=Contribuições'),
				array($content2, '/fis/fis011/form/fis011FormSpedContribuicoes/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
	}			

?>
