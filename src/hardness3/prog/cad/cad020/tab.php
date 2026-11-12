<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'cad020tabGrid':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad020/grid/cad020lista/', 'Produtos'),

		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad020tabForm1':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad020/form/cad020form1/', 'Novo Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId)),

		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;
	
	case 'cad020tabForm2':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			//array(uniqid(), '/cad/cad020/form/cad020form2/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad020/content/cad020detalhes/', 'Marcas', "&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad020/content/certificados/', 'Certificados', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id)),
			//array(uniqid(), '/etc/etc001/content/etc001_content_urls/', 'Links', "&D110_Id=" . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
		
	case 'cad020tabForm3':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad020/grid/cad020comercial/', 'Comercial', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&idTabForm4=' . urlencode($_REQUEST['idTabForm4'])),
			array(uniqid(), '/cad/cad020/grid/cad020estoque/', 'Estoque', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&idTabForm4=' . urlencode($_REQUEST['idTabForm4'])),
			array(uniqid(), '/cad/cad020/grid/cad020custos/', 'Custos', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&idTabForm4=' . urlencode($_REQUEST['idTabForm4'])),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
		
	case 'cad020tabForm4':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
            array(uniqid(), '/cad/cad002/grid/historico/', 'Histórico', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad020/form/cad020substituicao/', 'Substituição', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad020/grid/cad020empresas/', 'Empresas', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad020/grid/cad020montagem/', 'Montagem', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad020/grid/cad020locacao/', 'Locação', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad002/grid/locacao/', 'Locação', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad020/form/cad020tributos/', 'Tributos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad020/grid/cad020pedidos/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad020/grid/cad020oc/', 'OC', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad020/form/cad020editarMarca/', 'Editar Marca', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'tabForm5CadastroMarca':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad020/form/cadastroMarca/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']))
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad020tabForm2Comum':
		global $g;
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoProduto/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		//array(uniqid(), '/cad/cad002/content/detalhes/', 'Marcas', "&acaoId=" . urlencode($r_acaoId)),
		//array(uniqid(), '/cad/cad002/form/form2/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId)),			
		$sql = mysql_query("SELECT * FROM D001 LEFT JOIN D049 ON D049_D001_Id = D001_Id LEFT JOIN D009 ON D009_D049_Id = D049_Id WHERE D001_Id = '{$r_acaoId}' AND D009_C004_Id = '{$g['empresaAtual']}'");		
		$res = mysql_fetch_array($sql);
		$idTabela = uniqid();
		if($g['C031']['multimarcas'] == 'S'){
			$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentCabecalhoProduto/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain));			
			$tabsList[] = array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id));
			$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentUploadFotoProduto/', 'Fotos', "&D001_Id=" . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/cad/cad002/form/cad002FormEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/etc/etc001/grid/etc001_grid_urls/', 'Links', "&D001_Id=" . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentServicos/', 'Serviços', "&D001_Id=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/empresas/', 'Referências', "&tab=geral&D001_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($res['D049_Id']));
		} else if($g['C031']['multimarcas'] == 'N'){
			$tabsList[] = array(uniqid(), '/cad/cad020/content/cad020ContentCabecalhoProdutoComum/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain));		
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/historico/', 'Histórico', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
			$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentUploadFotoProduto/', 'Fotos', "&D001_Id=" . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/cad/cad002/form/cad002FormEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/etc/etc001/grid/etc001_grid_urls/', 'Links', "&D001_Id=" . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentServicos/', 'Serviços', "&D001_Id=" . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/cad/cad002/form/substituicao/', 'Substituição', "&tab=geral&acaoId=" . urlencode($res['D049_Id']));
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/empresas/', 'Referências', "&tab=geral&D001_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($res['D049_Id']));
			$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentMontagem/', 'Montagem', "&acaoId=" . urlencode($res['D009_Id']) . "&D001_Id=" . urlencode($res['D001_Id']));
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/locacao/', 'Locação', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
			//$tabsList[] = array(uniqid(), '/cad/cad002/form/tributos/', 'Tributos', "&tab=geral&acaoId=" . urlencode($res['D049_Id']));
			$tabsList[] = array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id));
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/pedidosProduto/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridOrdemCompraNovo/', 'OC', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);	
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridFornecedoresProduto/', 'Fornecedores', "&tab=geral&acaoId=" . urlencode($r_acaoId), false, true);	
		} else {
			if($res['D001_Flag_Multimarcas'] != 'N') {
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentCabecalhoProduto/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain));			
				$tabsList[] = array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentUploadFotoProduto/', 'Fotos', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/cad002FormEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
				$tabsList[] = array(uniqid(), '/etc/etc001/grid/etc001_grid_urls/', 'Links', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentServicos/', 'Serviços', "&D001_Id=" . urlencode($r_acaoId));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/empresas/', 'Referências', "&tab=geral&D001_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($res['D049_Id']));
			} else {
				$tabsList[] = array(uniqid(), '/cad/cad020/content/cad020ContentCabecalhoProdutoComum/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain));		
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/historico/', 'Histórico', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/locacao/', 'Locação', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentUploadFotoProduto/', 'Fotos', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/cad002FormEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
				$tabsList[] = array(uniqid(), '/etc/etc001/grid/etc001_grid_urls/', 'Links', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentServicos/', 'Serviços', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/substituicao/', 'Substituição', "&tab=geral&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/empresas/', 'Referências', "&tab=geral&D001_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentMontagem/', 'Montagem', "&acaoId=" . urlencode($res['D009_Id']) . "&D009_Id=" . urlencode($res['D009_Id']) . "&D001_Id=" . urlencode($res['D001_Id']) );
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/tributos/', 'Tributos', "&tab=geral&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/pedidosProduto/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridOrdemCompraNovo/', 'OC', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
			}
		}
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Quando vier do botão (Incluir Contatos, diretamente no combo)
		if (isset($r_abrirTab) && $r_abrirTab == 'tabela') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', '#{$idTabela}');
					}, 100);
				} </script></div>
EOT;
		}
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
