<?php
namespace hardness;
	require_once 'grid_func-callback.php';

/*
 * Grid Divergências de Estoque - Felipe
 */
	switch ($g['r'][3]) {
		case 'est001GridDivergenciasEstoque':
			
		 /**
		 * Definição do Grid (Tabela)
		 */			
		$grid = array(
            array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => '', 'callback' => 'verificaExclusao', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T029_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T029_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T029_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'T029_Flag_Ok', 'colunaTitulo' => 'Status', 'callback' => 'verificaStatus', 'colunaAlinhar' => 'center'),
        );
			
		/**
		* Definição da SQL
		*/
		$from = 'T029';
		$extra = <<<EOT
			left join C007 on T029_C007_Id=C007_Id
			WHERE T029_C004_Id='{$g['empresaAtual']}'
EOT;


		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est003GridDivergenciasEstoqueBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!--<div class="buttonsBottomTop">
				<button type="button" id="est003GridDivergenciasEstoqueBtnIncluir">Incluir</button>
				
			</div>-->
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est003GridDivergenciasEstoqueBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro', '/est/est001/content/est001ContentDivergenciasEstoqueIncluir/', '&tab=geral', [500,320]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T029_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T029_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T029_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T029_Flag_Ok' => array('titulo' => 'Status', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Divergências de Estoque - ID: ' + array['todosCampos']['T029_Id'], '/est/est001/content/est001ContentDivergenciasEstoqueIncluir/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T029_Id']) + '&T029_Id=' + encodeURIComponent(array['todosCampos']['T029_Id']) + '&tabela={$from}', [950,600]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;


		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'est001GridDivergenciasEstoqueProdutos':
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T029_Id = (isset($r_T029_Id)) ? $r_T029_Id : false;

		// Verifica para liberar as colunas e botões referente a divergência gerada para reposição loja
		$T029 = mysql_query("SELECT T029_Descricao, 
									IFNULL(T029A_Flag_Reabastecimento,'1') AS T029A_Flag_Reabastecimento 
							   FROM T029 
						  LEFT JOIN T029A ON T029A_T029_Id=T029_Id 
						      WHERE T029_Id='{$T029_Id}'");
		$mT029 = mysql_fetch_array($T029);	
		if ($mT029['T029_Descricao']=='REABASTECIMENTO LOJA' && $mT029['T029A_Flag_Reabastecimento']=='1') {
			$exibirLoja = true;
		} else {
			$exibirLoja = false;
		} 

		 /**
		 * Definição do Grid (Tabela)
		 */			
		$grid = array(
            array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => '', 'callback' => 'verificaExclusaoProduto'),
            array('colunaValor' => 'D001_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T030_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'font-size:10px'),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false,'chavePrimaria' => true),
			array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold', 'callback' => 'abrirJanela', 'colunaExibirCallbackNivel' => 3, 'callbackParameter' => array(
                 'Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),
            ),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T029_Flag_Ok', 'colunaTitulo' => 'Alterar Custo', 'colunaExibir' => false),
            array('colunaValor' => 'T030_Flag_Alterar_Custo', 'colunaTitulo' => 'A.C.', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'validaCampoAlteraCusto', 'colunaEditarGeraSelect' => array(
                	array('title' => 'Sim', 'value' => 'S'),
                	array('title' => 'Não', 'value' => 'N'),
            	),
            ),
            array('colunaValor' => 'T030_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo R$', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Atual R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T030_Flag_Entrada_Saida', 'colunaTitulo' => 'Oper.', 'callback' => 'validaCampoOperacao', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array(
                array('title' => 'Credita', 'value' => 'E'),
                array('title' => 'Debita', 'value' => 'S'),
                array('title' => 'Balanço', 'value' => 'B'),
				array('title' => 'Não altera', 'value' => 'N'),
            ), 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T030_Quantidade', 'colunaTitulo' => 'Qtd D.E', 'callback' => 'validaCampoQuantidade', 'colunaEditar' => true, 'style' => 'font-weight:bold', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
            array('colunaValor' => 'IF(T029_Flag_Ok=\'S\',(SELECT T001_Quantidade_Saldo FROM T001 AS T001Anterior WHERE T001Anterior.T001_D009_Id=T030_D009_Id AND T001Anterior.T001_Id < T001.T001_Id ORDER BY T001Anterior.T001_Id DESC LIMIT 1),\'FINALIZAR D.E\') as saldoAnterior', 'colunaTitulo' => 'Saldo Anterior', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'colunaExibir' => false),
            array('colunaValor' => 'IF(T029_Flag_Ok=\'S\',(T001_Quantidade_Saldo),\'FINALIZAR D.E\') as saldoPosterior', 'colunaTitulo' => 'Saldo Posterior', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'colunaExibir' => false),
            array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'Estq Físico', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T066_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Líquido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => "CONCAT(D004_Local, ' - ', T066_Id) as D004_Local", 'colunaTitulo' => 'Local', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T030_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true),
            array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'Peso unit.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'T030_Data_Inclusao', 'colunaTitulo' => 'Data inclusão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'Hist', 'callback' => 'insereRegistroT001'),
            array('colunaValor' => 'T066_Id', 'colunaTitulo' => 'T066_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo atual produto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2 ),
			array('colunaValor' => 'D009_Flag_Preco_Loja', 'colunaTitulo' => '', 'callback' => 'gCheckbox', 'callbackParameter' => array('D009_Flag_Preco_Loja', 'N|S'), 'style' => 'width: 1%', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaExibir' => $exibirLoja),
			array('colunaValor' => 'D009_Preco_Loja', 'colunaTitulo' => 'Preço Loja', 'style' => 'width: 1%; background-color: lightblue;', 'colunaAlinhar' => 'right', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço Novo', '/ven/ven001/outros/demonstrativoPrecoVenda/', 'D009_Id', 'right', 2, 'D009', '', '450'),'colunaExibir' => $exibirLoja),
            array('colunaValor' => 'D009_Preco_Loja_Real', 'colunaTitulo' => 'Novo Preço', 'style' => 'background-color: var(--celula-verde); width: 1%;', 'colunaAlinhar' => 'right', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço Novo', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'D009_Id', 'right', 2, 'D009_Real'),'colunaExibir' => $exibirLoja),
            array('colunaValor' => '((D009_Preco_Loja_Real-D009_Preco_Loja)/D009_Preco_Loja)*100', 'colunaTitulo' => '%', 'colunaCasasDecimais' => '2', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right','colunaExibir' => $exibirLoja),
        );
			
		/**
		* Definição da SQL
		*/
		$from = 'T030';
			
		$extra = <<<EOT
			left join T029 on T029_Id=T030_T029_Id
			left join D009 on D009_Id=T030_D009_Id 
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			left join C007 on T030_C007_Id=C007_Id
			left join T001 as T001 on T001_T030_Id=T030_Id 
			LEFT JOIN T066 ON T066_Id=T030_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			where T030_T029_Id='{$r_acaoId}'
			and T030_D009_Id>0
			GROUP BY T030_Id
			ORDER BY T030_Id desc
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
	       $novoIdTelaForm = uniqid();
	       echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				       var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
		               	// somente executa a ação ao clicar na linha ou na coluna
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						// pega o ID da linha
						var id = $(this).closest('tr').attr('id').split('|');
						var D001_Id = id[0].replace(/D001_Id-/, '');
		               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id), [980,550]);
		           });
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D037_Unidade' => array('titulo' => 'UN', 'tipo' => 'texto'),
			'T030_Flag_Alterar_Custo' => array('titulo' => 'Alt. Custo', 'tipo' => 'select','select' => array(
                array('title' => 'Todas', 'value' => ''),
				array('title' => 'Sim', 'value' => 'S'),
				array('title' => 'Não', 'value' => 'N'),
            ), 'naoGerarExtra' => ''),		
			'T030_Valor_Custo_Unitario' => array('titulo' => 'Custo R$', 'tipo' => 'numero'),
			'T030_Flag_Entrada_Saida' => array('titulo' => 'Operação', 'tipo' => 'select','select' => array(
                array('title' => 'Todas', 'value' => ''),
				array('title' => 'Credita', 'value' => 'E'),
				array('title' => 'Debita', 'value' => 'S'),
				array('title' => 'Balanço', 'value' => 'B'),
            ), 'naoGerarExtra' => ''),		
			'T030_Quantidade' => array('titulo' => 'Qtd D.E', 'tipo' => 'numero'),
			'T066_Quantidade_Estoque' => array('titulo' => 'Estq Físico', 'tipo' => 'numero'),

			'T066_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq Líquido', 'tipo' => 'numero'),
			'T030_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T030_Data_Inclusao' => array('titulo' => 'Data Inclusão', 'tipo' => 'data'),
            '((D009_Preco_Loja_Real-D009_Preco_Loja)/D009_Preco_Loja)*100' => array('titulo' => 'Percentual', 'tipo' => 'moeda', 'exibir'=> $exibirLoja),
			'D009_Flag_Preco_Loja' => array('titulo' => 'Marcado? (S/N)', 'tipo' => 'texto','exibir'=> $exibirLoja),
			'D004_Local' => array ('titulo' => 'Locação', 'tipo' => 'texto','exibir'=> $exibirLoja),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Botões
        $botoes = array(
			array('titulo' => 'Atualizar estoque', 'id' => 'est001AtualizarEstoque', 'exibirCallback' => 'return isset($g["c029Ids"][180]);'),
			array('titulo' => 'Aplicar Novos Preços', 'id' => 'est001AplicarNovosPrecos', 'exibirCallback' => 'return '.$exibirLoja.';'),
			array('titulo' => 'Imprimir Etiquetas Novo Preço', 'id' => 'est001ImpNovasEtiquetaPrecoNovo', 'exibirCallback' =>'return '.$exibirLoja.';'),
			array('titulo' => 'Imprimir Etiquetas Preço Loja', 'id' => 'est001ImpNovasEtiqueta', 'exibirCallback' => 'return '.$exibirLoja.';'),
		);
        echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Totais
		 */
		$totais = array(
			array('nome' => 'Total_Itens', 'titulo' => 'Total Itens', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Total_Custo_Credito', 'valor' => "D009_Valor_Custo_Unitario*if(T030_Flag_Entrada_Saida='E',T030_Quantidade,0)", 'titulo' => 'Total custo crédito'),
			array('nome' => 'Total_Custo_Debito', 'valor' => "D009_Valor_Custo_Unitario*if(T030_Flag_Entrada_Saida='S',T030_Quantidade,0)", 'titulo' => 'Total custo débito'),
		);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

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

		$sql = str_replace("GROUP BY T030_Id","GROUP BY D009_Id",$geradoSql);
		$sql = urlencode(base64_encode($sql));

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est001AtualizarEstoque').unbind('click').bind('click', function(data) {
				var buttons = {
					"Sim": function() {
						var T029_Id = $('#{$g['divId']} #T029_Id').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est001/form_func-ajax/atualizarEstoque/?ajax=true&T029_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							if (request.code) {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Ok', request.data, buttons);
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, buttons);
							}
							$('#{$g['divId']}').hideLoading();
							divRefresh('{$g['divId']}');
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Atenção!', 'Este processo irá atualizar o estoque dos produtos lancados nesta D.E.<br /><br /> \
											 Após finalizado, esta D.E. não poderá mais ser alterada!<br /><br />              \
											 Não será possível reverter esse processo!<br /><br />                              \
											 CONFIRMA? ', buttons);
			});
			$('#est001AplicarNovosPrecos').unbind('click').bind('click', function(data) {
				var buttons = { 
					"Sim": function() { 
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est003/grid_func-ajax/est003AplicarNovosPrecos/?ajax=true&sql={$sql}&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Operação Ok', request.data, buttons);
								divRefresh('{$g['divId']}');
								nova_janela('/est/est003/grid/est003GridPrecoLojaHistoricoItem/?ajax=true&generateHTML=true&acaoId=' + encodeURIComponent(request.uniqid) + '&D001_Codigo_Produto=1&D001_Descricao_Produto=1&T239_Preco_Loja_Antigo=1&T239_Preco_Loja_Novo=1&D001_Id=1',0,0,750,500,'yes','no','no','no','yes','no','Relatório de Conferência');
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, buttons);
							}
							$(this).dialog("destroy").remove();
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); }
				};
				dialogConfirm('Aplicar Novos Preços (Itens Selecionados)', 'Este processo irá aplicar nos novos preços para uso na Loja.<br/>Deseja continuar?', buttons);
			});
			$('#est001ImpNovasEtiquetaPrecoNovo').unbind('click').bind('click', function(data) {
				if($impressaoJava == 1){
					var buttons = { 
						"Fechar": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Imprimir Novas Etiquetas (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava"></div>', buttons);
					setTimeout(function() {
						impressaoEtiquetas($('#est003ImpNovasEtiqJava'), '/sistema/impressao/etiquetas/precolojaNovoPreco/', 0, 'precolojaNovoPreco', 1, false, '&sql={$sql}');
					}, 100);
					impressaoPreCarregarApplet();
				}else if($impressaoJava == 2){
					var buttons = { 
						"Imprimir": function() { 
							var impressora = $(this).parent().find('select').val();
							var sql = "$sql";
							$.ajax('/sistema/impressao/etiquetas/precolojaNovoPreco/?ajax=true&acaoId=0&imprimir=precolojaNovoPreco&sql="' + sql + '"&impressora=' + encodeURIComponent(impressora));
							$(this).dialog("destroy").remove();
							dialogConfirm('Impressora', 'Etiqueta enviada para a fila de impresão.');
						},
						"Fechar": function() {
							$(this).dialog("destroy").remove(); 
						}
					};
					dialogConfirm('Imprimir Etiquetas Preço Novo (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava">$select</div>', buttons);
				}
			});
			$('#est001ImpNovasEtiqueta').unbind('click').bind('click', function(data) {
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
					dialogConfirm('Imprimir Etiquetas Preço Loja (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava">$select</div>', buttons);
				}
			});
		} </script></div>
EOT;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
}
?>
