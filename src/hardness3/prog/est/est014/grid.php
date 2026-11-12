<?php
namespace hardness;
/**
* EST - EST013
*/

// Carrega funções callback
require_once('prog/est/est014/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est014/grid_func-js.php');

/**
 * Ações
 */
switch($g['r'][3]){
	case 'est014GridRequisicoesdeCompra':
		// $meses = $g['c020']['C020_Numero_Meses_Relatorio_Estoque'];
		// $periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T189_Id', $g['divId'])),
			array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'Req', 'style' => 'width: 1%',  'chavePrimaria'=>true),
			array('colunaValor' => 'T189_Data_Criado', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'concat(if(T174_Id IS NULL, T189_Descricao_Produto, T174_Descricao_Produto), " ", if(D001_Flag_Pre_Cadastro="S","(PRÉ)"," ")," ",if(D001_Id is null,"<font style=color: var(--BGVermelho)>(Sem Cadastro)</font>"," ")) as Descricao', 'colunaTitulo' => 'Descrição Produto'),
			//array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'T189_Quantidade', 'colunaTitulo' => 'Qnt', 'style' => 'width: 1%', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true),
			array('colunaValor' => 'Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipo', 'colunaTipo'=>'livre'),
			array('colunaValor' => 'T189_Flag_Cotar', 'colunaTitulo' => 'Cotar', 'callback' => 'validaCampoCotar', 'style'=>'width:1%;'),
			array('colunaValor' => 'T189_Flag_Comprar', 'colunaTitulo' => 'Comprar', 'callback'=>'validaCampoComprar', 'style'=>'width:1%;'),
			array('colunaValor' => 'T189_Flag_Produzir', 'colunaTitulo' => 'Serviço', 'callback'=>'validaCampoProduzir', 'style'=>'width:1%;'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T189_Observacao', 'colunaTitulo' => 'Observação'),
			array('colunaValor' => 'T189_C007_Id', 'colunaTitulo' => 'Usuário', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_Detalhes', 'colunaTitulo' => 'Detalhes'),
			array('colunaValor' => 'T189_T004_Id', 'colunaTitulo' => 'T189_T004_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_T006_Id', 'colunaTitulo' => 'T189_T006_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_D009_Id', 'colunaTitulo' => 'T189_D009_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_Flag_Tipo', 'colunaTitulo' => 'T189_Flag_Tipo', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_C007_Id_Cotar', 'colunaTitulo' => 'T189_C007_Id_Cotar', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_C007_Id_Comprar', 'colunaTitulo' => 'T189_C007_Id_Comprar', 'colunaExibir'=>false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T189';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		LEFT JOIN C007 ON C007_Id=T189_C007_Id
		LEFT JOIN T059 on T059_T189_Id=T189_Id
		LEFT JOIN T012 on T012_T189_Id=T189_Id or T012_T059_Id=T059_Id
		LEFT JOIN D009 on D009_Id=T189_D009_Id
		LEFT JOIN D049 on D049_Id=D009_D049_Id
		LEFT JOIN D001 on D001_Id=D049_D001_Id
		LEFT JOIN T174 ON T059_Id=T174_T059_Id
		LEFT JOIN D024 ON D024_Id=T189_D024_Id
		WHERE 	(
					(T189_Flag_Tipo='C' 
				     	AND (T012_Id IS NULL OR T012_Id='' OR T012_Id=0) 
				     	AND (T059_Id IS NULL OR T059_Id='' OR T059_Id=0)
				    ) OR 
	                (T189_Flag_Tipo='P' 
	                 	AND (
	                 			(T012_Id IS NULL OR T012_Id='' OR T012_Id=0) AND (T174_Flag_Gerar_OC = '1' OR T174_Id IS NULL)
	                 		)
					) OR 
						(T189_Flag_Tipo='E'
							AND (T012_Id IS NULL OR T012_Id='' OR T012_Id=0) 
				     		AND (T059_Id IS NULL OR T059_Id='' OR T059_Id=0)
						)
					OR (T189_Flag_Tipo='M')
			    ) 
		GROUP BY T189_Id
		ORDER BY T189_Data_Criado ASC
EOT;
		
		/*LEFT JOIN T174 ON T059_Id = T174_T059_Id
		WHERE ((T189_Flag_Tipo='C' AND (T012_Id IS NULL OR T012_Id='' OR T012_Id=0) AND (T059_Id IS NULL OR T059_Id='' OR T059_Id=0)) OR (T189_Flag_Tipo='P' AND ((T012_Id IS NULL OR T012_Id='' OR T012_Id=0) AND (T174_Flag_Gerar_OC = '1' OR T174_Id IS NULL)))) 
		ORDER BY T189_Data_Criado ASC

		WHERE  ((T189_Flag_Tipo='C' and (T012_Id IS NULL OR T012_Id='' OR T012_Id=0) AND (T059_Id IS NULL OR T059_Id='' OR T059_Id=0)) 
         OR (T189_Flag_Tipo='P' and (T012_Id IS NULL OR T012_Id='' OR T012_Id=0)))
		ORDER BY T189_Data_Criado ASC */
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T189_Descricao_Produto' => array('titulo' => 'Descrição Produto', 'tipo' => 'texto'),
			'T189_Data_Criado' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T189_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T189_Detalhes' => array('titulo' => 'Detalhes', 'tipo' => 'texto'),
			//'T189_Flag_Tipo' => array('titulo' => 'Tipo (C/O/P/E)', 'tipo' => 'texto'),
			'T189_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Apenas Cotar', 'value' => 'C'),
				array('title' => 'Orçamento Venda', 'value' => 'O'),
				array('title' => 'Pedido Venda', 'value' => 'P'),
				array('title' => 'Reposição Estoque', 'value' => 'E'),
			), 'naoGerarExtra' => false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

//	echo <<<EOT
//	    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//		    setTimeout(function() {
//		         divRefresh('{$g['divId']}');
//		    }, 30000);

//	    } </script></div>
//EOT;
		
		$sql_ = urlencode(base64_encode($geradoSql));
		// <button type="button" id="est014Incluir">Incluir</button>
		if (!personalizacaoBotoes()) { 
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est014FlagCotar">Marcar Cotar</button>
				<button type="button" id="est014FlagDesmarcarCotar">Desmarcar Cotar</button>
				<button type="button" id="est014FlagMarcarComprar">Marcar Comprar</button>
				<button type="button" id="est014FlagDesmarcarComprar">Desmarcar Comprar</button>
				<button type="button" id="est014FlagMarcarProduzir">Marcar Produzir</button>
				<button type="button" id="est014FlagDesmarcarProduzir">Desmarcar Produzir</button>
				<button type="button" id="est014GerarCotacao">Gerar Cotação</button>
				<button type="button" id="est014GerarOC">Gerar OC</button>
				<button type="button" id="est014GerarCotacaoNovo">Gerar Cotação Novo</button>
				<button type="button" id="est014GerarOCNovo">Gerar OC Novo</button>
				<button type="button" id="est014GerarProducao">Gerar OS</button>
			</div>
EOT;
		}
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$extra_ = urlencode(base64_encode($extra));
		// Não executar quando houver o refresh de linha
		if (empty($r_linhaGridId)) {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est014FlagCotar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014FlagMarcar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#est014FlagDesmarcarCotar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#est014FlagMarcarComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014FlagMarcar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#est014FlagDesmarcarComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});

				$('#est014FlagMarcarProduzir').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014FlagMarcar/?ajax=true&extra={$sql_}&operacao=3&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#est014FlagDesmarcarProduzir').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=3&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				
				$('#est014GerarOC').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014GerarOC/?ajax=true&callback=?', function(request) {
						if (request.code) {
							var \$buttons = {
									"Sim": function() {
										var dialogAnterior = $(this);
										$.getJSON('/est/est014/grid_func-ajax/est014OCIncluirCabecalho/?ajax=true&callback=?', function(request) {
											if(request.code){
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}',unique(), '', 'Editar Ordem de Compra - Número: ' + request.data, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent(request.data) + '&tabela=T010', [900,500]);
												dialogAnterior.dialog("destroy").remove();
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    							dialogConfirm('Erro', request.data, \$buttons);
											}
										});
									},
									"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Confirmação', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				$('#est014GerarOCNovo').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014GerarOC/?ajax=true&callback=?', function(request) {
						if (request.code) {
							var \$buttons = {
									"Sim": function() {
										var dialogAnterior = $(this);
										$.getJSON('/est/est016/grid_func-ajax/est016OCIncluirCabecalho/?ajax=true&callback=?', function(request) {
											if(request.code){
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(request.data), [990,605]);  //950 615
												dialogAnterior.dialog("destroy").remove();
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    							dialogConfirm('Erro', request.data, \$buttons);
											}
										});
									},
									"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Confirmação', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				$('#est014GerarProducao').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014VerificaGerarProducao/?ajax=true&callback=?', function(request) {
						if (request.code) {
							var \$buttons = {
									"Sim": function() {
										var dialogAnterior = $(this);
										$.getJSON('/est/est014/grid_func-ajax/est014IncluirOrdemServico/?ajax=true&callback=?', function(request) {
											if(request.code){
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}',unique(), '', 'Editar Ordem de Serviço - Número: ' + request.data, '/est/est014/content/est014contentOrdemServico/', '&T128_Id=' + encodeURIComponent(request.data), [900,500]);
												divRefresh('{$g['divIdParent']}');
												dialogAnterior.dialog("destroy").remove();
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    							dialogConfirm('Erro', request.data, \$buttons);
											}
										});
									},
									"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Confirmação', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				$('#est014GerarCotacao').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014GerarCotacao/?ajax=true&callback=?', function(request) {
						if (request.code) {
							var \$buttons = {
									"Sim": function() {
										var dialogAnterior = $(this);
										$.getJSON('/est/est014/grid_func-ajax/est014CotacaoIncluirCabecalho/?ajax=true&callback=?', function(request) {
											if(request.code){
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}',unique(), '', 'Editar Cotação de Compra - Número: ' + request.data, '/est/est008/content/est008contentCadastroCotacaoCompra/', '&acaoId=' + encodeURIComponent(request.data) + '&tabela=T010', [900,500]);
												divRefresh('{$g['divIdParent']}');
												dialogAnterior.dialog("destroy").remove();
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    							dialogConfirm('Erro', request.data, \$buttons);
											}
										});
									},
									"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Confirmação', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				$('#est014GerarCotacaoNovo').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est014/grid_func-ajax/est014GerarCotacao/?ajax=true&callback=?', function(request) {
						if (request.code) {
							var \$buttons = {
									"Sim": function() {
										var dialogAnterior = $(this);
										$.getJSON('/est/est015/grid_func-ajax/est015CotacaoIncluirCabecalho/?ajax=true&callback=?', function(request) {
											if(request.code){
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}',unique(), '', 'Editar Cotação de compra - ID: ' + request.data, '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(request.data), [990,605]);
												divRefresh('{$g['divIdParent']}');
												dialogAnterior.dialog("destroy").remove();
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    							dialogConfirm('Erro', request.data, \$buttons);
											}
										});
									},
									"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Confirmação', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
				$('#est014Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est014/content/est014ContentRequisicaoCompraEditarNovo/', '&tab=geral&', [500,300]);
				});
			} </script></div>
EOT;
		}
		
		
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
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T189_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Requisicao de compra - Nº: ' + acaoId, '/est/est014/content/est014ContentRequisicaoCompraEditarNovo/', '&acaoId=' + encodeURIComponent(acaoId), [600,400]);
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
	
	case 'est014GridProdutosCotacoes':	
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array();
			$grid[] = array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria'=>true, 'style'=>'width:1%;', 'colunaAlinhar' => 'center');
			$grid[] = array('colunaValor' => 'T059_Id', 'colunaTitulo' => 'T059_Id', 'chavePrimaria'=>true, 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Id', 'colunaTitulo' => 'T174_Id', 'chavePrimaria'=>true, 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'T189_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T189_Data_Criado', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData');
			$grid[] = array('colunaValor' => 'concat("<b>",if(T174_Id IS NULL, T059_Descricao_Produto, T174_Descricao_Produto),"</b>",if(D001_Flag_Pre_Cadastro="S"," (PRÉ)",""),"<br />",T189_Observacao," ",ifnull(T189_Detalhes,"")) as T059_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style'=>'max-width:350px;white-space:normal;');
			$grid[] = array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor',  'callback' => 'gSubstr', 'callbackParameter' => '30');
			$grid[] = array('colunaValor' => 'T189_Quantidade', 'colunaTitulo' => 'Qnt', 'style'=>'width:1%;', 'colunaCasasDecimais' => '2');
			$grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'Unitário', 'colunaCasasDecimais' => '2', 'colunaEditar'=>true, 'style'=>'width:1%;', 'colunaEditarJSCallback' => 'callbackCalculaCusto', 'colunaAlinhar' => 'right');
			$grid[] = array('colunaValor' => 'T174_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '2', 'colunaEditar'=>true, 'colunaEditarJSCallback' => 'callbackCalculaCusto', 'style'=>'width:1%;', 'colunaAlinhar' => 'right');
			$grid[] = array('colunaValor' => 'T174_Preco_Venda', 'colunaTitulo' => 'Preço Venda', 'colunaCasasDecimais' => '2', 'colunaEditar'=>true, 'colunaEditarJSCallback' => 'callbackCalculaCusto', 'style'=>'width:1%;', 'colunaAlinhar' => 'right');
			$grid[] = array('colunaValor' => 'T174_Prazo_Entrega_Dias', 'colunaTitulo' => 'Prazo entrega', 'colunaEditar'=>true, 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'T174_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true, 'style'=>'max-width:200px;white-space:normal;');
			$grid[] = array('colunaValor' => 'Flag_Email', 'colunaTitulo' => '<span class="ui-icon ui-icon-mail-closed"></span>', 'style'=>'width:1%;', 'callback'=>'validaCampoIconeEmail', 'colunaTipo'=>'livre');
			$grid[] = array('colunaValor' => 'Flag_FInalizado', 'colunaTitulo' => 'Fim', 'style'=>'width:1%;', 'callback'=>'validaCampoIconeFinalizado', 'colunaTipo'=>'livre');
			$grid[] = array('colunaValor' => 'T174_Flag_Indisponivel', 'colunaTitulo' => "Indisp.",'colunaAlinhar' => 'center', 'callback'=>'validaCampoIndisponivel', 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário');
			$grid[] = array('colunaValor' => 'campoOc', 'colunaTitulo' => 'OC', 'colunaTipo'=>'livre', 'callback'=>'validaCampoOC', 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'T189_Observacao', 'colunaTitulo' => 'Obs', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Flag_Gerar_OC', 'colunaTitulo' => 'T174_Flag_Gerar_OC', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Flag_Email_Enviado', 'colunaTitulo' => 'T174_Flag_Email_Enviado', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Hora_Email_Forn', 'colunaTitulo' => 'T174_Flag_Email_Enviado', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T010_Flag_Cotacao_Finalizada', 'colunaTitulo' => 'T010_Flag_Cotacao_Finalizada', 'colunaExibir'=>false);
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T059';

		$extra = <<<EOT
			LEFT JOIN T010 ON T010_Id=T059_T010_Id
            LEFT JOIN D009 on D009_Id=T059_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN T189 on T189_Id=T059_T189_Id
            LEFT JOIN T174 on T174_T059_Id=T059_Id  		
			LEFT JOIN T173 on T173_Id=T174_T173_Id
			LEFT JOIN D024 on D024_Id=T173_D024_Id
     		LEFT JOIN C007 ON C007_Id=T189_C007_Id
			WHERE (T059_T189_Id != 0 AND T059_T189_Id is not null and T059_T189_Id != '')
			AND (T174_Flag_Excluido='N' OR T174_Id is null)
			AND (T010_C004_Id='{$g['empresaAtual']}')
EOT;
			//AND (T010_Flag_Cotacao_Finalizada != 'S' OR T010_Id is null OR T010_Flag_Cotacao_Finalizada IS NULL)
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				function atualizaTela() {
					divRefresh('{$g['divId']}', true);
				}
			} </script></div>
EOT;
			/**
			* Filtro
			*/
			$gridFiltro = array(
				'T189_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
				'T189_Data_Criado' => array('titulo' => 'Emissão', 'tipo' => 'data'),
				'T010_Id' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
				'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
				'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
				'T174_Valor_Custo_Unitario' => array('titulo' => 'Unitário', 'tipo' => 'numero'),
				'T174_Preco_Venda' => array('titulo' => 'Preço Venda', 'tipo' => 'numero'),
				'T174_Flag_Indisponivel' => array('titulo' => 'Indisponível', 'tipo' => 'select', 'select' => array(
						array('title' => '...', 'value' => ''),
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					), 'naoGerarExtra' => false),
			);
			
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T174_Flag_Indisponivel']=='S')
			{
				return 'linhaVermelhoClaro';        
			}
			else if($array['todosCampos']['T174_Valor_Custo_Unitario']>0)
			{
				return 'linhaCinza';
			}
		}

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		// <button type="button" id="est014Incluir">Incluir</button>
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est014GerarOCNovo">Gerar OC</button>
			</div>
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		$extra_ = urlencode(base64_encode($extra));
		// Não executar quando houver o refresh de linha
		if (empty($r_linhaGridId)) {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est014GerarOCNovo').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar ordem de compra', '/est/est014/content/est014contentGerarOC/','&extra=' + '{$extra_}', [850,550]);
				});
			} </script></div>
EOT;
		}

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'B') { return true; }
				 //&& !$(data.target).hasClass('editar-linha')
				 //&& !$(data.target).hasClass('editar-linha')
				var id = $(this).closest('tr').attr('id').split('|');
				var T010_Id = id[0].replace('T010_Id-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de Compra - Número: ' + T010_Id, '/est/est008/content/est008contentCadastroCotacaoCompra/', '&acaoId=' + encodeURIComponent(T010_Id) + '&tabela={$from}', [900,500]);
				
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
			function callbackCalculaCusto(T174_Id, campoEditado, valorAntigo, novoValor) {
				var T174 = T174_Id.split('|');
				var T174_Id = T174 = T174[2].replace('T174_Id-', '');
				$.getJSON('/est/est014/grid_func-ajax/est014aplicarPrecoVenda/?ajax=true&T174_Id='    + encodeURIComponent(T174_Id) + 
																			      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																			      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																			      '&novoValor='			+ encodeURIComponent(novoValor) +  
																			      '&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$g['divId']}');
						//alert("NFe Excluída");
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
			}
		} </script></div>
EOT;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		
		$total=0;
		 foreach ($gridTabela['tbody'] as $linha){
		 	$total+=@gCorrigeNumeroInverte($linha['dados'][13]);
		 }
		 $total = gCorrigeNumero($total);
		 // inserir o total na última linha da tabela
		 //array_push($gridTabela['tbody'], array('dados'=>array('','','','','','','<b>Total</b>','<b style="text-align:right;">'.$total.'</b>')));
		
		
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
  	break;
	
	case 'est014GridGerarOC':
			
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$extra = base64_decode($r_extra);
		$extra = gInsertExtraWhere($extra, "(T174_Flag_Gerar_OC = 'S')");
		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array();
			$grid[] = array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria'=>true, 'style'=>'width:1%;', 'colunaAlinhar' => 'center');
			$grid[] = array('colunaValor' => 'T059_Id', 'colunaTitulo' => 'T059_Id', 'chavePrimaria'=>true, 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Id', 'colunaTitulo' => 'T174_Id', 'chavePrimaria'=>true, 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'T189_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T173_Id', 'colunaTitulo' => 'T173_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T189_Data_Criado', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData');
			$grid[] = array('colunaValor' => 'concat("<b>",T059_Descricao_Produto,"</b>","<br />",T189_Observacao,ifnull(T189_Detalhes,"")) as T059_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style'=>'max-width:350px;white-space:normal;');
			$grid[] = array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor',  'callback' => 'gSubstr', 'callbackParameter' => '30');
			$grid[] = array('colunaValor' => 'T189_Quantidade', 'colunaTitulo' => 'Qnt', 'style'=>'width:1%;', 'colunaCasasDecimais' => '2');
			$grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'Unitário', 'colunaCasasDecimais' => '2', 'style'=>'width:1%;', 'colunaEditarJSCallback' => 'callbackCalculaCusto');
			$grid[] = array('colunaValor' => 'T174_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'callbackCalculaCusto', 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'T174_Preco_Venda', 'colunaTitulo' => 'Preço Venda', 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'callbackCalculaCusto', 'style'=>'width:1%;');
			//$grid[] = array('colunaValor' => 'T174_Prazo_Entrega_Dias', 'colunaTitulo' => 'Prazo entrega', 'style'=>'width:1%;');
			//$grid[] = array('colunaValor' => 'T174_Observacao', 'colunaTitulo' => 'Observação', 'style'=>'max-width:200px;white-space:normal;');
			$grid[] = array('colunaValor' => 'T174_Flag_Indisponivel', 'colunaTitulo' => "Indisp.",'colunaAlinhar' => 'center', 'callback'=>'validaCampoIndisponivel', 'style'=>'width:1%;', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário');
			$grid[] = array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'OC', 'colunaTipo'=>'livre', 'callback'=>'validaCampoOC', 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'T189_Observacao', 'colunaTitulo' => 'Obs', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Flag_Gerar_OC', 'colunaTitulo' => 'T174_Flag_Gerar_OC', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T173_Desconto', 'colunaTitulo' => 'T173_Desconto', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_IPI', 'colunaTitulo' => 'T174_IPI', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_D009_Id', 'colunaTitulo' => 'T174_D009_Id', 'colunaExibir'=>false);
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T174';

			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				function atualizaTela() {
					divRefresh('{$g['divId']}', true);
				}
			} </script></div>
EOT;
			/**
			* Filtro
			*/
			$gridFiltro = array(
				'T189_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
				'T189_Data_Criado' => array('titulo' => 'Emissão', 'tipo' => 'data'),
				'T010_Id' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
				'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
				'T174_Valor_Custo_Unitario' => array('titulo' => 'Unitário', 'tipo' => 'numero'),
				'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			);
			
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T174_Flag_Indisponivel']=='S')
			{
				return 'linhaVermelhoClaro';        
			}
			else if($array['todosCampos']['T174_Valor_Custo_Unitario']>0)
			{
				return 'linhaCinza';
			}
		}

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		// <button type="button" id="est014Incluir">Incluir</button>
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est014GerarOCSelecionados">Gerar OC</button>
			</div>
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		$sql_ = urlencode(base64_encode($geradoSql));
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar ordem de compra', '/est/est014/content/est014contentGerarOC/','&sql=' + '{$sql_}', [700,450]);
				});
			} </script></div>
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T010_Id = id[0].replace('T010_Id-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de Compra - Número: ' + T010_Id, '/est/est008/content/est008contentCadastroCotacaoCompra/', '&acaoId=' + encodeURIComponent(T010_Id) + '&tabela={$from}', [900,500]);
				
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
			function callbackCalculaCusto(T174_Id, campoEditado, valorAntigo, novoValor) {
				var T174 = T174_Id.split('|');
				var T174_Id = T174 = T174[2].replace('T174_Id-', '');
				$.getJSON('/est/est014/grid_func-ajax/est014aplicarPrecoVenda/?ajax=true&T174_Id='    + encodeURIComponent(T174_Id) + 
																			      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																			      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																			      '&novoValor='			+ encodeURIComponent(novoValor) +  
																			      '&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$g['divId']}');
						//alert("NFe Excluída");
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
			}
			$('#est014GerarOCSelecionados').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est014/grid_func-ajax/est014VerificaGerarOC/?ajax=true&callback=?&sql='+ '{$sql_}', function(request) {
								if (request.code) {
									var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar OC', '/est/est006/content/est006ContentIncluirOrdemCompra/', '&tab=geral&gerarCotacaoRequisicaoTabCotacao=true&sql=' + '{$sql_}' + '&D024_Id=' + encodeURIComponent(request.D024_Id), [500,300]);
												$('#{$g['divId']}').hideLoading();
												$(this).dialog("destroy").remove();
											},
											"Não": function() { $(this).dialog("destroy").remove(); } 
										};
										dialogConfirm('Confirmação', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar Cotação', 'Gostaria de gerar uma ordem de compra? ', \$buttons);
				});
		} </script></div>
EOT;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		
		$total=0;
		 foreach ($gridTabela['tbody'] as $linha){
		 	$total+=@gCorrigeNumeroInverte($linha['dados'][13]);
		 }
		 $total = gCorrigeNumero($total);
		 // inserir o total na última linha da tabela
		 //array_push($gridTabela['tbody'], array('dados'=>array('','','','','','','<b>Total</b>','<b style="text-align:right;">'.$total.'</b>')));
		
		
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
  	break;
  	
  	case 'est014GridRequisicoesdeCompraGeral':
		// $meses = $g['c020']['C020_Numero_Meses_Relatorio_Estoque'];
		// $periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
		
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T189_Id', $g['divId'])),
			array('colunaValor' => 'T189_Id', 'colunaTitulo' => '', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			array('colunaValor' => 'T189_Data_Criado', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData', 'style'=>'width:1%;'),
			array('colunaValor' => 'T189_Descricao_Produto', 'colunaTitulo' => 'Descrição do Produto', 'callback'=>'validaCampoDescricaoProduto'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'T189_Quantidade', 'colunaTitulo' => 'Qnt', 'colunaCasasDecimais'=>'2', 'style'=>'width:1%;'),
			array('colunaValor' => 'T189_Quantidade_Estoque_Atual', 'colunaTitulo' => 'Est Atual', 'colunaCasasDecimais'=>'2', 'style'=>'width:1%;'),
			array('colunaValor' => 'T189_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipo'),
			array('colunaValor' => 'concat(C007_Nome, "<br />", T189_Detalhes) as Usuario', 'colunaTitulo' => 'Usuário'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T189';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		LEFT JOIN C007 ON C007_Id=T189_C007_Id
		LEFT JOIN D024 on D024_Id=T189_D024_Id

EOT;
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est014incluir">Incluir</button>
			</div>
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est014incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Reuisição de Compra', '/est/est014/content/est014RequisicaoCompra/','', [500,300]);
				});
			} </script></div>	
EOT;
			
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T189_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T189_Data_Criado' => array('titulo' => 'Data', 'tipo' => 'data'),
			'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),			
		
			'T189_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Apenas Cotar', 'value' => 'C'),
				array('title' => 'Reposição Estoque', 'value' => 'E'),
				array('title' => 'Cancelado', 'value' => 'R'),
			), 'naoGerarExtra' => false),
			);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

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
	
	case 'est014GridEmails':
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array();
			$grid[] = array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria'=>true, 'style'=>'width:1%;', 'colunaAlinhar' => 'center');
			$grid[] = array('colunaValor' => 'T059_Id', 'colunaTitulo' => 'T059_Id', 'chavePrimaria'=>true, 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Id', 'colunaTitulo' => 'T174_Id', 'chavePrimaria'=>true, 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'T189_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T173_Id', 'colunaTitulo' => 'T173_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T189_Data_Criado', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'concat("<b>",if(T174_Id IS NULL, T059_Descricao_Produto, T174_Descricao_Produto),"</b>",if(D001_Flag_Pre_Cadastro="S"," (PRÉ)",""),"<br />",T189_Observacao," ",ifnull(T189_Detalhes,"")) as T059_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style'=>'max-width:350px;white-space:normal;');
			$grid[] = array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor',  'callback' => 'gSubstr', 'callbackParameter' => '30');
			$grid[] = array('colunaValor' => 'DataEnvio', 'colunaTitulo' => 'Dt Envio', 'style'=>'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDtEnvio');
			$grid[] = array('colunaValor' => 'Tempo', 'colunaTitulo' => 'Tempo', 'style'=>'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaCampoTempo');
			$grid[] = array('colunaValor' => 'DataRetorno', 'colunaTitulo' => 'Dt Retorno', 'style'=>'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDtRetorno');
			$grid[] = array('colunaValor' => 'T189_Quantidade', 'colunaTitulo' => 'Qnt', 'style'=>'width:1%;', 'colunaCasasDecimais' => '2');
			$grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'Unitário', 'colunaCasasDecimais' => '2', 'colunaEditar'=>true, 'style'=>'width:1%;', 'colunaEditarJSCallback' => 'callbackCalculaCusto', 'colunaAlinhar' => 'right');
			$grid[] = array('colunaValor' => 'T174_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '2', 'colunaEditar'=>true, 'colunaEditarJSCallback' => 'callbackCalculaCusto', 'style'=>'width:1%;', 'colunaAlinhar' => 'right', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Preco_Venda', 'colunaTitulo' => 'Preço Venda', 'colunaCasasDecimais' => '2', 'colunaEditar'=>true, 'colunaEditarJSCallback' => 'callbackCalculaCusto', 'style'=>'width:1%;', 'colunaAlinhar' => 'right', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Prazo_Entrega_Dias', 'colunaTitulo' => 'Prazo entrega', 'colunaEditar'=>true, 'style'=>'width:1%;', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true, 'style'=>'max-width:200px;white-space:normal;', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Flag_Indisponivel', 'colunaTitulo' => "Indisp.",'colunaAlinhar' => 'center', 'callback'=>'validaCampoIndisponivel', 'style'=>'width:1%;', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'OC', 'colunaTitulo' => 'OC', 'colunaTipo'=>'livre', 'callback'=>'validaCampoOC', 'style'=>'width:1%;', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T189_Observacao', 'colunaTitulo' => 'Obs', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Flag_Gerar_OC', 'colunaTitulo' => 'T174_Flag_Gerar_OC', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Hora_Email_Forn', 'colunaTitulo' => 'T174_Flag_Email_Enviado', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T139_Data_Hora', 'colunaTitulo' => 'T174_Flag_Email_Enviado', 'colunaExibir'=>false);
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T059';

		$extra = <<<EOT
			LEFT JOIN T010 ON T010_Id=T059_T010_Id
            LEFT JOIN D009 on D009_Id=T059_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN T189 on T189_Id=T059_T189_Id
            LEFT JOIN T174 on T174_T059_Id=T059_Id  		
			LEFT JOIN T173 on T173_Id=T174_T173_Id
			LEFT JOIN D024 on D024_Id=T173_D024_Id
     		LEFT JOIN C007 ON C007_Id=T189_C007_Id
     		LEFT JOIN T139 ON T139_T010_Id=T010_Id
			WHERE 
			T174_Flag_Email_Enviado='S'
			GROUP BY T173_Id
EOT;
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				function atualizaTela() {
					divRefresh('{$g['divId']}', true);
				}
			} </script></div>
EOT;
			/**
			* Filtro
			*/
			$gridFiltro = array(
				'T189_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
				'T189_Data_Criado' => array('titulo' => 'Emissão', 'tipo' => 'data', 'exibir'=>false),
				'T010_Id' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
				'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
				'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto', 'exibir'=>false),
				'T174_Valor_Custo_Unitario' => array('titulo' => 'Unitário', 'tipo' => 'numero', 'exibir'=>false),
				'T174_Preco_Venda' => array('titulo' => 'Preço Venda', 'tipo' => 'numero', 'exibir'=>false),
				'T174_Flag_Indisponivel' => array('titulo' => 'Indisponível', 'tipo' => 'select', 'select' => array(
						array('title' => '...', 'value' => ''),
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					), 'naoGerarExtra' => false, 'exibir'=>false),
				'T139_Data_Hora' => array('titulo' => 'Dt Envio', 'tipo' => 'data'),
				'T174_Hora_Email_Forn' => array('titulo' => 'Dt Retorno', 'tipo' => 'data'),
			);
			
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if(empty($array['todosCampos']['T174_Hora_Email_Forn']))
			{
				return 'linhaAzul';        
			}
		}

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		// <button type="button" id="est014Incluir">Incluir</button>
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<button type="button" id="est014GerarOCNovo">Gerar OC</button> -->
			</div>
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		$extra_ = urlencode(base64_encode($extra));
		// Não executar quando houver o refresh de linha
		if (empty($r_linhaGridId)) {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est014GerarOCNovo').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar ordem de compra', '/est/est014/content/est014contentGerarOC/','&extra=' + '{$extra_}', [850,550]);
				});
			} </script></div>
EOT;
		}

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'B') { return true; }
				// && !$(data.target).hasClass('editar-linha')
				// && !$(data.target).hasClass('editar-linha')
				var id = $(this).closest('tr').attr('id').split('|');
				var T010_Id = id[0].replace('T010_Id-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de Compra - Número: ' + T010_Id, '/est/est008/content/est008contentCadastroCotacaoCompra/', '&acaoId=' + encodeURIComponent(T010_Id) + '&tabela={$from}', [900,500]);
				
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
			function callbackCalculaCusto(T174_Id, campoEditado, valorAntigo, novoValor) {
				var T174 = T174_Id.split('|');
				var T174_Id = T174 = T174[2].replace('T174_Id-', '');
				$.getJSON('/est/est014/grid_func-ajax/est014aplicarPrecoVenda/?ajax=true&T174_Id='    + encodeURIComponent(T174_Id) + 
																			      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																			      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																			      '&novoValor='			+ encodeURIComponent(novoValor) +  
																			      '&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$g['divId']}');
						//alert("NFe Excluída");
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
			}
		} </script></div>
EOT;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		
		$total=0;
		 foreach ($gridTabela['tbody'] as $linha){
		 	$total+=@gCorrigeNumeroInverte($linha['dados'][13]);
		 }
		 $total = gCorrigeNumero($total);
		 // inserir o total na última linha da tabela
		 //array_push($gridTabela['tbody'], array('dados'=>array('','','','','','','<b>Total</b>','<b style="text-align:right;">'.$total.'</b>')));
		
		
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
	break;

	case 'est014GridOrdemServico':

		/**
		* Definição do Grid (Tabela)
		*/		
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T128_Id', $g['divId'])),
			array('colunaValor' => 'T128_Id', 'colunaTitulo' => 'Número', 'chavePrimaria'=>true),
			array('colunaValor' => 'T128_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF Remessa'),
			array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF Entrada'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T128';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join D024 on D024_Id=T128_D024_Id
		    left join C007 on C007_Id=T128_C007_Id
		    left join T007 on T007_T128_Id=T128_Id
		    LEFT JOIN T129 ON T129_T128_Id=T128_Id
		    left join T014 on T014_T129_Id=T129_Id
			left join T013 on T013_Id=T014_T013_Id
		    where T128_C004_Id='{$g['empresaAtual']}'
		    GROUP BY T128_Id

EOT;
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est014incluir">Incluir</button>
			</div>
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est014incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Requisição de Compra', '/est/est014/content/est014ContentIncluirOS/','', [500,300]);
				});

			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T128_Id = id[0].replace('T128_Id-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Serviço - Número: ' + T128_Id, '/est/est014/content/est014contentOrdemServico/', '&T128_Id=' + encodeURIComponent(T128_Id) + '&tabela={$from}', [900,500]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
			} </script></div>	
EOT;
			//est008contentCadastroCotacaoCompra
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T128_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
			'T128_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF Remessa', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

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

	case 'est014GridProdutosOrdemServico':

		/**
		* Definição do Grid (Tabela)
		*/		
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T129_Id', $g['divId'])),
			array('colunaValor' => 'T129_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
			array('colunaValor' => 'concat(T129_Codigo_Produto, \' - \', T129_Descricao_Produto)', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T129_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditar'=>true),
			array('colunaValor' => 'T129_Quantidade_Estoque', 'colunaTitulo' => 'Estoque'),
			array('colunaValor' => '(SELECT concat(D001_Codigo_Produto," - ",D001_Descricao_Produto) FROM D001 WHERE D001_Id=D001_1.D001_D001_Id) as Origem', 'colunaTitulo' => 'Origem'),
			array('colunaValor' => 'T129_Quantidade_Falta_Lancar(T129_Id,0) as T129_Quantidade_Pendente', 'colunaTitulo' => 'Pendente'),
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'N. Pedido Venda'),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF Saída'),
			array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF Entrada'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T129';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
				left join T006 on T006_Id=T129_T006_Id
				left join D009 on D009_Id=T129_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 as D001_1 on D049_D001_Id=D001_1.D001_Id
				left join T128 on T128_Id=T129_T128_Id
				left join T008 on T008_T129_Id=T129_Id
				left join T007 on T007_Id=T008_T007_Id
				left join T014 on T014_T129_Id=T129_Id
				left join T013 on T013_Id=T014_T013_Id
					where T129_T128_Id='{$r_T128_Id}'
				order by T129_Id

EOT;
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<button type="button" id="est014incluir">Incluir</button>-->
			</div>
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est014incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Requisição de Compra', '/est/est014/content/est014RequisicaoCompra/','', [500,300]);
				});

			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T128_Id = id[0].replace('T128_Id-', '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Serviço - Número: ' + T128_Id, '/est/est014/content/est014contentOrdemServico/', '&acaoId=' + encodeURIComponent(T128_Id) + '&tabela={$from}', [900,500]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
			} </script></div>	
EOT;
			//est008contentCadastroCotacaoCompra
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T128_Id' => array('titulo' => 'Número', 'tipo' => 'texto', 'exibir'),
			'T128_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF Remessa', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

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
