<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	 case 'est003ContentPrincipal':
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
		array($content1, '/est/est003/tab/est003TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est003ContentPedido':
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
		array($content1, '/est/est003/tab/est003TabPedido/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est003Content':
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
		array($content1, '/est/est003/tab/est003TabProdutos/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est003ContentBalancoImportarCSV':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '40px;');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);        
		echo  $g['smarty']->fetch('content-2rows.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
           	array($content1, '/est/est003/form/est003FormImportarCSV/','&tab=geral'),
		    array($content2, '/est/est003/outros/est003ManualCSVBalanco/','&tab=geral')
		);
		echo  gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		
	break;

	case 'est003ContentDefeito':
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
		array($content1, '/est/est003/form/est003FormProdutosDefeito/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est003ContentProdutosDefeitos':
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
		array($content1, '/est/est003/grid/est003gridDefeito/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est003ContentEstoqueOE':
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
		array($content1, '/est/est003/grid/est003gridEstoqueOE/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est003contentIncluirDE':
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
			array($content1, '/est/est003/tab/est003TabIncluirDE/', '&T006_Id=' . urlencode($r_T006_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est003FormIncluirDE_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est003ContentLoja':
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
		array($content1, '/est/est003/tab/est003TabLoja/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est003ContentPrecoLojaHistoricoItem':
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
			array($content1, '/est/est003/tab/est003TabPrecoLojaHistoricoItem/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est003ImpNovasEtiqItens">Imprimir Etiquetas</button>';
		$botoesDir .= '<button type="submit" id="est003RelatorioConf">Relatório de Conferência</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		// Carregando combo com os dados das impressoras
		$impressaoJava = $g['C031']['impressaoJava'];
		if($impressaoJava == 2){
			$optionValue = "<option value=>[Escolha a impressora]</option>";
			$D175 = mysql_query("SELECT D175_Id, D175_Nome FROM D175 ");
			while($mD175 = mysql_fetch_array($D175)){
				$optionValue .= "<option value=" . $mD175['D175_Id'] . ">" . $mD175['D175_Nome'] . "</option>";
			}
			$select = '<select style="width:250px; height:24px" id="impressoras">' . $optionValue . '</select>';
		}else{
			$select = '';
		}

		// Ãções botões
		$sql = <<<EOT
				SELECT D001_Id,
					   T239_Preco_Loja_Novo as D009_Preco_Loja
				  FROM T239
			 left join D009 on D009_Id=T239_D009_Id
			 left join D049 on D049_Id=D009_D049_Id
			 left join D001 on D001_Id=D049_D001_Id
			     WHERE T239_Processo_Id = '{$r_acaoId}'
			  GROUP BY D009_Id
EOT;
		$sql = urlencode(base64_encode($sql));
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est003ImpNovasEtiqItens').unbind('click').bind('click', function(data) {
					if($impressaoJava == 1){
						var buttons = { 
							"Fechar": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Imprimir Novas Etiquetas (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava"></div>', buttons);
						setTimeout(function() {
							impressaoEtiquetas($('#est003ImpNovasEtiqJava'), '/sistema/impressao/etiquetas/precoloja/', 0, 'precoloja', 1, false, '&sql={$sql}');
						}, 100);
						impressaoPreCarregarApplet();
					}else if($impressaoJava == 2){
						var buttons = { 
							"Imprimir": function() { 
								var impressora = $(this).parent().find('select').val();
								var sql = "$sql";
								$.ajax('/sistema/impressao/etiquetas/precoloja/?ajax=true&acaoId=0&imprimir=precoloja&sql="' + sql + '"&impressora=' + encodeURIComponent(impressora));
								$(this).dialog("destroy").remove();
								dialogConfirm('Impressora', 'Etiqueta enviada para a fila de impresão.');
							},
							"Fechar": function() {
								$(this).dialog("destroy").remove(); 
							}
						};
						dialogConfirm('Imprimir Etiquetas Preço Novo', '<div id="est003ImpNovasEtiqItens">$select</div>', buttons);
					}
				});
				$('#est003RelatorioConf').unbind('click').bind('click', function(data) {
					nova_janela('/est/est003/grid/est003GridPrecoLojaHistoricoItem/?ajax=true&generateHTML=true&generateHTMLTodos=true&acaoId={$r_acaoId}&D001_Codigo_Produto=1&D001_Descricao_Produto=1&T239_Preco_Loja_Antigo=1&T239_Preco_Loja_Novo=1&D001_Id=1',0,0,750,500,'yes','no','no','no','yes','no','Relatório de Conferência');
				});
			} </script></div>
EOT;
	break;

	case 'est003ContentHistoricoLojaPreco':
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
			array($content1, '/est/est003/tab/est003TabHistoricoLojaPreco/')
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est003ContentMovimentacao':
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
			array($content1, '/est/est003/tab/est003TabMovimentacao/'),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est003ContentLocacoes':
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
			array($content1, '/est/est003/tab/est003TabLocacoes/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		if($r_T014_Id > 0) {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$content1}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							divRefresh('{$g['divIdParent']}', true, false, false, '{$r_T014_Id}', 'T014_Id');
						}, 150);
					});
				} </script></div>
EOT;
		} else if($r_T257_Id > 0) {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$content1}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							divRefresh('{$g['divIdParent']}', true, false, false, '{$r_T257_Id}', 'T257_Id');
						}, 150);
					});
				} </script></div>
EOT;
		} else if($r_T008_Id > 0) {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$content1}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							divRefresh('{$g['divIdParent']}', true, false, false, '{$r_T008_Id}', 'T008_Id');
						}, 150);
					});
				} </script></div>
EOT;
		} else if($r_separacao == true){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$content1}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							separacaoProdutoPorLocacao($('#{$g['divIdParent']} #T238_T006_Id'), '{$g['divIdParent']}');
						}, 150);
					});
				} </script></div>
EOT;
		} else if($r_divergencia == true){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$content1}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							campoProcuraProdutoDivergencia('{$g['divIdParent']}', '{$r_acaoId}', $('#{$g['divIdParent']} #T030_D144_Id'));
						}, 150);
					});
				} </script></div>
EOT;
		} else if($r_T048_Id == true){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$content1}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							divRefresh('{$g['divIdParent']}', true, false, false, '{$r_T048_Id}', 'T048_Id');
						}, 150);
					});
				} </script></div>
EOT;
		}else if($r_T071_Id == true){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$content1}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
							divRefresh('{$g['divIdParent']}', true, false, false, '{$r_T071_Id}', 'T071_Id');
						}, 150);
					});
				} </script></div>
EOT;
		}

	break;

	case 'est003ContentColetas':
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
			array($content1, '/est/est003/tab/est003TabColetas/'),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est003ContentGerarPedido':
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
		array($content1, '/est/est003/form/est003FormGerarPedido/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est003ContentParticipante':
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
		array($content1, '/est/est003/tab/est003TabParticipante/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
}
