<?php
namespace hardness;
	
switch ($g['r'][3]) {
	
	case 'etc011ContentPivot':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('class', 'bi');
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc011/tab/etc010TabPivot/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'etc011ContentCockpit':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('class', 'bi');
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc011/tab/etc010TabCockpit/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'etc011ContentSalvar':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		
		$param = '&acaoId=' . urlencode($r_acaoId) . '&fato=' . urlencode($r_fato) . '&fato2=' . urlencode($r_fato2) . '&dimensao1=' . urlencode($r_dimensao1) . '&dimensao1ord=' . urlencode($r_dimensao1ord) . '&dimensao2=' . urlencode($r_dimensao2) . '&dimensao2ord=' . urlencode($r_dimensao2ord) . '&medida=' . urlencode($r_medida) . '&mostrarCurvaABC=' . urlencode($r_mostrarCurvaABC) . '&ocultarColunasDashboard=' . urlencode($r_ocultarColunasDashboard) . '&incluirLinhaVazia=' . urlencode($r_incluirLinhaVazia) . '&incluirColunaVazia=' . urlencode($r_incluirColunaVazia) . '&tipodata=' . urlencode($r_tipodata) . '&exibir=' . urlencode($r_exibir) . '&md5=' . urlencode($r_md5) . '&incluirGraficoLabel=' . urlencode($r_incluirGraficoLegend) . '&incluirGraficoLegend=' . urlencode($r_incluirGraficoLabel) . '&numMetricas=' . urlencode($r_numMetricas) . '&ocultarSempreColunas=' . urlencode($r_ocultarSempreColunas) . '&ocultarPorcentagem=' . urlencode($r_ocultarPorcentagem) . '&ocultarTotais=' . urlencode($r_ocultarTotais);
		for ($i = 1; $i < 10; $i++) {
			$var = 'r_fatoa' . $i;
			if (isset($$var)) {
				$param .= "&fatoa{$i}=" . urlencode($$var);
				$var = 'r_fato2a' . $i;
				$param .= "&fato2a{$i}=" . urlencode($$var);
				$var = 'r_tipodataa' . $i;
				$param .= "&tipodataa{$i}=" . urlencode($$var);
				$var = 'r_medidaa' . $i;
				$param .= "&medidaa{$i}=" . urlencode($$var);
			}
		}
		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc011/tab/etc011TabSalvar/', $param)
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'etc011ContentPivotRelatorio':
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
			array($content1, '/etc/etc011/grid/etc011GridPivot/', "&bi={$r_bi}&acaoId=" . urlencode($r_acaoId) . '&D132_Id=' . urlencode($r_D132_Id) . '&grid=' . @$r_grid . '&tab=' . @$r_tab)
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'etc011ContentGrafico':
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
			array($content1, '/etc/etc011/outros/etc011OutrosGrafico/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'etc011ContentNovaVisao':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Width', '250px');
		$g['smarty']->assign('tipo', '2colunas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc011/tab/etc011TabNovaVisaoRel/', '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($content2)),
			array($content2, '/etc/etc011/tab/etc011TabNovaVisao/', '&acaoId=' . urlencode($r_acaoId) . '&D132_Id=&bi=visu&novavisao=semfiltro')
		);
		echo gProcessaAcoes($actions);
	break;

    case 'etc011FiltroGlobal':
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
            array($content1, '/etc/etc011/tab/etc011FiltroGlobal/', '&tab=' . $r_tab . '&divIdReload=' . $r_divIdReload)
        );
        echo gProcessaAcoes($actions);
        break;

    case 'etc011IncluirIndicador':
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
            array($content1, '/etc/etc011/tab/etc011IncluirIndicador/', "&acaoId=" . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);
        break;
}
