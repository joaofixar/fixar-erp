<?php
namespace hardness;
	switch ($g['r'][3]) {
		/**
		* @author tiagofischer, AndreySliwinski
		* @since 08/11/2011
		*/
		case 'srv003ContentOperacao':
			
			$T147_Id = (isset($r_T147_Id)) ? $r_T147_Id : false;
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo $g['smarty']->fetch('content-full.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/srv/srv003/tab/srv003TabOperacao', '&acaoId=' . urlencode($r_acaoId) . '&T147_Id=' . urlencode($T147_Id))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'srv003ContentOperacaoPendentes':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo $g['smarty']->fetch('content-full.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/srv/srv003/tab/srv003TabOperacaoPendentes', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'contentProcessosServicos':
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
				array($content1, '/srv/srv003/tab/srv003TabProcessosServicos', '&T147_Id=' . urlencode($r_T147_Id))
			);
			echo  gProcessaAcoes($actions);

		break;
		
		case 'contentMateriaisServicos':
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
				array($content1, '/srv/srv003/tab/srv003TabMateriaisServicos', '&T147_Id=' . urlencode($r_T147_Id))
			);
			echo  gProcessaAcoes($actions);

		break;
		
		case 'srv003ContentLogin':
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
				array($content1, '/srv/srv003/tab/srv003TabLogin', '&acaoId=' . urlencode($r_acaoId))
			);
			echo  gProcessaAcoes($actions);

		break;
		
		case 'srv003ContentIniciarServico':
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
			array($content1, '/srv/srv003/tab/srv003TabIniciarServico', '&acaoId=' . urlencode($r_acaoId) . '&T147_Id=' . urlencode($r_T147_Id))
			);
			echo  gProcessaAcoes($actions);

		break;
		
		case 'srv003ContentExecucaoServico':
			$sql = "SELECT * FROM T147 WHERE T147_Id='{$r_T147_Id}'";
			$result = mysql_query($sql);
			$linha = mysql_fetch_array($result);
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo  $g['smarty']->fetch('content-full.tpl');
			// O que será carregado em cada conteúdo?
			if($linha['T147_Status'] == "P"){
				$actions = array(
					// identificador, url, paramentros get (opcional)
					array($content1, '/srv/srv003/tab/srv003TabIniciarServico', '&acaoId=' . urlencode($r_acaoId) . '&T147_Id=' . urlencode($r_T147_Id))
				);
			}else{
				$actions = array(
					// identificador, url, paramentros get (opcional)
					array($content1, '/srv/srv003/tab/srv003TabExecucaoServico', '&acaoId=' . urlencode($r_acaoId) . '&T147_Id=' . urlencode($r_T147_Id))
				);
			}
			echo  gProcessaAcoes($actions);

		break;
		
		case 'srv003ContentOperacaoAprovar':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo $g['smarty']->fetch('content-full.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/srv/srv003/tab/srv003TabOperacaoAprovar', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'srv003ContentAprovarServico':
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
			array($content1, '/srv/srv003/tab/srv003TabAprovarServico', '&acaoId=' . urlencode($r_acaoId) . '&T147_Id=' . urlencode($r_T147_Id))
			);
			echo  gProcessaAcoes($actions);

		break;
	}
?>
