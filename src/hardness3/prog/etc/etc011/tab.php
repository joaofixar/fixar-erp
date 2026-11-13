<?php
namespace hardness;

switch ($g['r'][3]) {	
	case 'etc010TabPivot':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabBI = uniqid();
		$tabsList = array();

		$tabsList[] = array(uniqid(), '/etc/etc011/grid/etc011GridSave/', 'Todos', '&acaoId=' . urlencode($r_acaoId) . '&tabBI=' . urlencode($tabBI),false,true);
		$tabsList[] = array($tabBI, '/etc/etc011/content/etc011ContentPivotRelatorio/', 'BI', '&bi=conf&acaoId=' . urlencode($r_acaoId) . '&D132_Id=' . @urlencode($r_D132_Id));
		$tabsList[] = array(uniqid(), '/etc/etc015/content/etc015CriarRelatorio/', 'Sql', '&acaoId=' . urlencode($r_acaoId));
		$tabsList[] = array(uniqid(), '/etc/etc015/grid/etc015Relatorios/', 'Sql salvos', '&acaoId=' . urlencode($r_acaoId));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc010TabCockpit':
		global $g;
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabBI = uniqid();
		$tabsList = array();

		// Avisos
		$tabsList[] = array(uniqid(), '/etc/etc011/outros/etc011Avisos/', 'Início', '&tab=geral');

		// Mostrar dashdoards disponíveis para a alçada atual
		$D132 = mysql_query("SELECT D132_Grupo FROM D132
			                  WHERE D132_Flag_Exibir_Dashboard = 'S'
			                    AND D132_Flag_Exibir_Alcada <= '{$g['nivelAlcada']}'
						   GROUP BY D132_Grupo
		                   ORDER BY D132_Grupo");

		while ($mD132 = mysql_fetch_assoc($D132)) {
			$tabsList[] = array(uniqid(), '/etc/etc011/formDash/etc011FormDash'.$mD132['D132_Grupo'].'/', "{$mD132['D132_Grupo']}", '&grupo=' . urlencode($mD132['D132_Grupo']) . '&tab=geral');
		}

		$D177 = mysql_query("SELECT D177_Id, D176_Id, D177_C021_Id, D176_Titulo, D176_Descricao, D176_Link 
							FROM D177 
						LEFT JOIN D176 ON D177_D176_Id=D176_Id 
						LEFT JOIN C021 ON D177_C021_Id=C021_Id 
							WHERE (D177_C021_Id='{$g['perfilUsuario']}' 
								OR IFNULL(D177_C021_Id, 0) = 0) 
								AND (D177_C004_Id='{$g['empresaAtual']}' 
								OR IFNULL(D177_C004_Id, 0) = 0) 
								AND (D177_C007_Id='{$g['usuarioAtual']}' 
								OR IFNULL(D177_C007_Id, 0) = 0)
							ORDER BY D176_Ordem");
		echo mysql_error();
		while ($mD177 = mysql_fetch_assoc($D177)){
			log("Entrou");
			$tabsList[] = array(uniqid(), '/bi/bi002/outro/bi002OutroTelaRelatorios/', "{$mD177['D176_Titulo']}",  '&D176_Id=' . urlencode($mD177['D177_Id']) . '&tabela=D177&tab=geral');
		}


		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		echo "<script>(function(){var atalho=document.getElementById('dashboardPersonalizadoAtalho');if(atalho){atalho.remove();}var estilo=document.getElementById('dashboardPersonalizadoAtalhoEstilo');if(estilo){estilo.remove();}})();</script>";

	break;
	
	case 'etc011TabSalvar':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
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
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc011/form/etc011FormSalvar/', 'Relatório', '&tab=geral' . $param),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc011TabNovaVisaoRel':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/etc/etc011/grid/etc011GridNovaVisao/', 'Relatórios', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc011TabNovaVisao':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		if (empty($r_D132_Id)) {
			$nome = 'BI - Bussiness Inteligence';
		} else {
			$sql = "SELECT D132_Descricao FROM D132 WHERE D132_Id = '{$r_D132_Id}'";
			$res = mysql_query($sql);
			$row = mysql_fetch_assoc($res);
			$nome = $row['D132_Descricao'];
		}
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/etc/etc011/content/etc011ContentPivotRelatorio/', $nome, '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc011FiltroGlobal':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/etc/etc011/grid/etc011FiltroGlobal/', 'Filtro', '&tab=' . $r_tab . '&divIdReload=' . $r_divIdReload)
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;

    case 'etc011IncluirIndicador':
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            array(uniqid(), '/etc/etc011/form/etc011IncluirIndicador/', 'Indicador', '&tab=geral&acaoId=' . urlencode($r_acaoId))
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
        break;
}
