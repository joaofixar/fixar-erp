<?php
namespace hardness;

// Carrega funções callback
require_once('prog/est/est010/grid_func-callback.php');
require_once('prog/est/est010/grid_func-js.php');

switch ($g['r'][3]) {
	/**
	* @author Weiberlan Garcia
	* @since 02/03/2012
	*/                 
	case 'est010GridComprasEmergencia':
		
		$meses = 24; 
		$periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D001_Id', $g['divId'])),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaExibir' => false),
			array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id','colunaExibir' => false),
			array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'D049_Flag_Tipo','colunaExibir' => false),
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaExibir' => false),
			array('colunaValor' => 'D009_Flag_Cotar', 'colunaTitulo' => 'D009_Flag_Cotar','colunaExibir' => false),
			array('colunaValor' => 'D009_Flag_Comprar', 'colunaTitulo' => 'D009_Flag_Comprar','colunaExibir' => false),
			array('colunaValor' => 'T006_Flag_Cotar', 'colunaTitulo' => 'D009_Flag_Cotar','colunaExibir' => false),
			array('colunaValor' => 'T006_Flag_Comprar', 'colunaTitulo' => 'D009_Flag_Comprar','colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style'=>'width:1%;'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque','colunaCasasDecimais' => 2),
			array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Líquido','colunaCasasDecimais' => 2),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde Pedido','colunaCasasDecimais' => 2),
			array('colunaValor' => 'T006_Quantidade_Comprar', 'colunaTitulo' => 'Qte Comprar','colunaEditar' => true,'colunaCasasDecimais' => 2),
			array('colunaValor' => '', 'colunaTitulo' => 'Cotar','colunaTipo' => 'livre','callback' => 'validaCampoCotar', 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => '', 'colunaTitulo' => 'Comprar','colunaTipo' => 'livre','callback' => 'validaCampoComprar', 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => '', 'colunaTitulo' => 'Forn 1','colunaTipo' => 'livre','callback' => 'campoCustoFornecedor', 'callbackParameter' => 'Forn1'),
			array('colunaValor' => '', 'colunaTitulo' => 'Forn 2','colunaTipo' => 'livre','callback' => 'campoCustoFornecedor', 'callbackParameter' => 'Forn2'),
			array('colunaValor' => '', 'colunaTitulo' => 'Forn 3','colunaTipo' => 'livre','callback' => 'campoCustoFornecedor', 'callbackParameter' => 'Forn3'),
			array('colunaValor' => "", 'colunaTitulo' => 'Preço Última Compra','colunaTipo' => 'livre','callback' => 'campoPrecoUltimaCompra'),
			array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Venda','colunaCasasDecimais' => 2),
			array('colunaValor' => 'T006_IPV', 'colunaTitulo' => 'IPV','colunaCasasDecimais' => 2),
			array('colunaValor' => "concat(T005_Id, '-' ,D024_Nome_Empresa)", 'colunaTitulo' => 'Pedido Cliente'),
			array('colunaValor' => 'T006_Flag_Tipo_Produto', 'colunaTitulo'=>'Tipo', 'colunaExibir'=>false, 'colunaEditar' => true,
			'colunaEditarGeraSelect' => array(
											array('title' => 'CP', 'value' => 'CP'), 
											array('title' => 'SV', 'value' => 'MD')
										)
			)
		);

		/**
		* Definição da SQL
		*/
		$from = 'T006';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join T005 on T005_Id=T006_T005_Id
			left join D024 on D024_Id=T005_D024_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join D009 on D009_Id=T006_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join T012 on T012_T006_Id=T006_Id
			left join T059 on T059_T006_Id=T006_Id
			left join T129 on T129_T006_Id=T006_Id
			left join D037 on D037_Id=D001_D037_Id
			left join C008 on C008_Id=D001_C008_Id
			left join T008 on T008_T006_Id=T006_Id
			left join T007 on T007_Id=T008_T007_Id and T007_Flag_Cancelada!='S'
			where T005_C004_Id='{$g['empresaAtual']}'
			and T005_Flag_Status!=8
			and T005_Flag_Status>0
			and T005_Flag_Status!=6
			and T005_Flag_Status!=9
			and T006_Flag_Tipo_Produto='CP'
			and T006_Flag_Status!='3'
			and T012_Id is null
			and T059_Id is null
			and T129_Id is null
			and T007_Id is null
			and D001_Flag_Ativo='S'
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D009_Quantidade_Estoque' => array('titulo' => 'Estoque', 'tipo' => 'numero'),
			//'T006_Flag_Cotar' => array('titulo' => 'Cotar(S/N)', 'tipo' => 'texto'),
			'T006_Flag_Cotar' => array('titulo' => 'Cotar', 'tipo' => 'select', 'select' => array(
					array('title' => 'Todas', 'value' => ''),
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N'),
				), 'naoGerarExtra' => false),
			//'T006_Flag_Comprar' => array('titulo' => 'Comprar(S/N)', 'tipo' => 'texto'),
			'T006_Flag_Comprar' => array('titulo' => 'Comprar', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todas', 'value' => ''),
				array('title' => 'Sim', 'value' => 'S'),
				array('title' => 'Não', 'value' => 'N'),
			), 'naoGerarExtra' => false),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 100);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		//$extra_ = urlencode(base64_encode($extra));
		$sql_ = urlencode(base64_encode($geradoSql));
		// Não executar quando houver o refresh de linha
		if (empty($r_linhaGridId)) {
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est010FlagCotar">Marcar Cotar</button>
				<button type="button" id="est010FlagDesmarcarCotar">Desmarcar Cotar</button>
				<button type="button" id="est010FLagMarcarComprar">Marcar Comprar</button>
				<button type="button" id="est010FLagDesmarcarComprar">Desmarcar Comprar</button>
				<button type="button" id="est010GerarCotacao">Gerar Cotação</button>
				<button type="button" id="est010GerarOC">Gerar OC</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est010GerarOC').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est010/grid_func-ajax/est010GerarOC/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar OC', '/est/est006/content/est006ContentIncluirOrdemCompra/', '&tab=geral&GerarOCEmergencia=true', [500,300]);
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
					dialogConfirm('Gerar Ordem de Compra', 'Gostaria de gerar Ordem de Compra? ', \$buttons);
				});
				
				$('#est010GerarCotacao').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est010/grid_func-ajax/est010GerarCotacao/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est008/content/est008contentCadastroCotacaoCompraNew/', '&tab=geral&GerarCotacaoEmergencia=true', [500,300]);
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
					dialogConfirm('Gerar Ordem de Compra', 'Gostaria de gerar Cotação? ', \$buttons);
				});
				
				$('#est010FlagCotar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est010/grid_func-ajax/est010flagCotar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						}
					});
				});
				
				$('#est010FlagDesmarcarCotar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est010/grid_func-ajax/est010FlagDesmarcarCotar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						}
					});
				});
				$('#est010FLagMarcarComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est010/grid_func-ajax/est010flagCotar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						}
					});
				});
				$('#est010FLagDesmarcarComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est010/grid_func-ajax/est010FlagDesmarcarCotar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						}
					});
				});
			} </script></div>
EOT;
		}
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D047_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Motivos Cancelamento do Orçamento - ID: ' + acaoId, '/cad/cad046/content/cad046ContentMotivosCancelamentoIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
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
}

?>
