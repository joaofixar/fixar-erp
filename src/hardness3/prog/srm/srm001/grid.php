<?php
namespace hardness;

require_once('prog/srm/srm001/grid_func-callback.php');
require_once('prog/srm/srm001/grid_func-js.php');

switch($g['r'][3]){
	case 'srm001GridCotacoesFornecedor':
		$filtroObrigatorio = true;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusaoCotacao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusaoCotacao', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'campoCancelamentoCotacao', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamentoCotacao', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'T218_D024_Id', 'colunaTitulo' => 'Cod.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,28)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold;max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            array('colunaValor' => 'concat(CONVERT(substr(orcamento.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(orcamento.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(orcamento.D013_Telefone_1,\'\'),\' \',ifnull(orcamento.D013_DDD_Celular,\'\'),\'-\',ifnull(orcamento.D013_Celular,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;white-space:normal;min-width:190px;width:190px'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'count(distinct T219_Id)', 'colunaTitulo' => 'Itens', 'style' => 'font-weight:bold;width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T218_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'T218_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Valor_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'T218_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct concat(T224_Id," - ",ifnull(T224_Nome_Status,"")," - ",T224_Valor_Total) separator ", ") as Pedido', 'colunaTitulo' => 'O.C.', 'callback' => 'validaCampoPedido', 'style' => 'width:1%;max-width:170px;white-space:normal;min-width:170px;width:170px;word-wrap: break-word; overflow-wrap: break-word;'),
            array('colunaValor' => 'group_concat(distinct T013_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'font-weight:bold;width:1%', 'callback' => 'validaCampoNFCotacoes'),
            array('colunaValor' => 'T218_Data_Finalizado_Fornecedor', 'colunaTitulo' => 'Fornecedor finalizou', 'callback' => 'gCorrigeData', 'style' => 'width:1%;color:blue'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as vendedor', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'globalStatusOrcamento', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Status', 'callback' => 'campoStatus', 'colunaExibir' => false),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T218_Flag_Perdido', 'colunaTitulo' => 'T218_Flag_Perdido', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T218_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T218_Observacao_Orcamento_Perdido', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'T224_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T224_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T218_Flag_ACP', 'colunaTitulo' => 'T218_Flag_ACP', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T218_Flag_Status_Orcamento', 'colunaTitulo' => 'T218_Flag_Status_Orcamento', 'colunaExibir' => false),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
        										FROM T219 
        										LEFT JOIN D009 ON D009_Id = T219_D009_Id
        										LEFT JOIN D049 ON D049_Id = D009_D049_Id
        										LEFT JOIN D082 ON D082_Id = D049_D082_Id
        										WHERE T219_T218_Id=T218_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'T218_Flag_Notificacao', 'colunaTitulo' => 'T218_Flag_Notificacao', 'colunaExibir' => false),
            array('colunaValor' => 'T218_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'T218_C007_Id_Vendedor_Interno', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct T004_T003_Id)', 'colunaTitulo' => 'Orçamento', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            //array('colunaValor' => 'group_concat(distinct T006_T005_Id)', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "if(T224_Id is not NULL,T218_Valor_Total,0) as Valor_Total_Fechado", 'colunaTitulo' => 'Valor_Total_Aberto', 'colunaExibir' => false),
            array('colunaValor' => "if(T224_Id is NULL,T218_Valor_Total,0) as Valor_Total_Aberto", 'colunaTitulo' => 'Valor_Total_Fechado', 'colunaExibir' => false),
			array('colunaValor' => "if(T224_Id is not NULL,1,0) as Qte_Total_Fechado", 'colunaTitulo' => 'Qte_Total_Fechado', 'colunaExibir' => false, 'casasDecimais' => '0'),
			array('colunaValor' => "if(T224_Id is NULL,1,0) as Qte_Total_Aberto", 'colunaTitulo' => 'Qte_Total_Aberto', 'colunaExibir' => false, 'casasDecimais' => '0'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T218';
		//left join T006 on T006_T004_Id=T004_Id OR T006_T219_Id=T219_Id
		$extra = <<<EOT
		    left join D024 on D024_Id=T218_D024_Id
			left join D013 as Orcamento on Orcamento.D013_Id=T218_D013_Id
			left join D013 as Cliente on Cliente.D013_D024_Id=T218_D024_Id
			left join T219 on T219_T218_Id=T218_Id
			left join T225 on T225_T219_Id=T219_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T218_C007_Id_Vendedor_Interno
			left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
			left join T014 on T014_T225_Id = T225_Id
			left join T013 on T013_Id=T014_T013_Id
			left join D006 on D006_Id=T218_D006_Id 
			left join D018 on D018_Id=D024_D018_Id
			left join T004 on T004_T219_Id=T219_Id
			where (T218_C004_Id='{$g['empresaAtual']}'
			and D024_Id='{$r_D024_Id}')
			group by T218_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T218_D024_Id
			left join D013 on D013_Id=T218_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T218_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
			left join D006 on D006_Id=T218_D006_Id
		    where (T218_C004_Id='{$g['empresaAtual']}'
		    and D024_Id='{$r_D024_Id}')
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalAberto = <<<EOT
			left join D024 on D024_Id=T218_D024_Id
			left join D013 on D013_Id=T218_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T218_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
			left join D006 on D006_Id=T218_D006_Id
		    where (T218_C004_Id='{$g['empresaAtual']}'
		    and D024_Id='{$r_D024_Id}'
		    and T218_Flag_Perdido!='S'
		    and (select T224_Id from T224 where T224_T218_Id=T218_Id limit 1) is null)
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalFechado = <<<EOT
			left join D024 on D024_Id=T218_D024_Id
			left join D013 on D013_Id=T218_D013_Id
			left join T219 on T219_T218_Id=T218_Id
			left join T225 on T225_T219_Id=T219_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T218_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T224 on T224_T218_Id=T218_Id
			left join D006 on D006_Id=T218_D006_Id
		    where (T218_C004_Id='{$g['empresaAtual']}'
		    and D024_Id='{$r_D024_Id}'
		    and T225_Id > 0)
		    GROUP BY T218_Id
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalPerdido = <<<EOT
			left join D024 on D024_Id=T218_D024_Id
			left join D013 on D013_Id=T218_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T218_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
			left join D006 on D006_Id=T218_D006_Id
		    where (T218_C004_Id='{$g['empresaAtual']}'
		    and D024_Id='{$r_D024_Id}'
		    and T218_Flag_Perdido = 'S')
		    GROUP BY T218_Id
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T224_Flag_Status']=='5'){
				return 'linhaCinza';
			} else if($array['todosCampos']['T218_Flag_Perdido']=='S'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T224_Id']<=0){
				if ($array['todosCampos']['T218_Flag_Status_Orcamento']=='V') {
					return 'linhaFundoAmareloClaro linhaAzul';
				} else {
					return 'linhaAzul';
				}
			}
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est015IncluirCotacao'),
			array('titulo' => 'Processar Vencidos', 'id' => 'est015ProcessaVencimento'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="est015GridPrincipalOrcamentos">Incluir</button>
				<button type="button" id="est015ProcessarCotacaoVencida">Processar Vencidos</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est015IncluirCotacao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cotação de compra', '/est/est015/content/est015ContentOrcamento/', '&D024_Id_CRM={$r_D024_Id}', [650,500]);
				});
				$('#est015ProcessaVencimento').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est015/grid_func-ajax/est015ProcessarCotacaoVencida/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Processar cotações vencidas', 'Você tem certeza?', \$buttons);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'numero'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'T218_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
            'T218_Flag_Status_Orcamento' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Finalizado', 'value' => 'F'),
                array('title' => 'Pendente', 'value' => 'P'),
                array('title' => 'Cancelado', 'value' => 'C'),
                array('title' => 'Vencido', 'value' => 'V'),
              ),
              'naoGerarExtra' => ''),
            'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto', 'exibir' => false),
            'T218_Flag_Perdido' => array('titulo' => 'Cancelado (S/N)?', 'tipo' => 'texto'),
            'D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal, false, false, true);
		$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
		$extraTotalFechado = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalFechado, false, false, true);
		$extraTotalPerdido = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalPerdido, false, false, true);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
        * Totais Almir
        */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Total', 'valor' => 'T218_Valor_Total', 'titulo' => 'Total R$'),
			array('nome' => 'Qte_Abertos', 'valor' => 'Qte_Total_Aberto', 'titulo' => 'Abertos'),
			array('nome' => 'Valor_Abertos', 'valor' => 'Valor_Total_Aberto', 'titulo' => 'Valor abertos'),
			array('nome' => 'Qte_Fechados', 'valor' => 'Qte_Total_Fechado', 'titulo' => 'Fechados'),
			array('nome' => 'Valor_Fechados', 'valor' => 'Valor_Total_Fechado', 'titulo' => 'Valor Fechados'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Totais(Versão Acima do GRID)
		*/
		/**
		 * Totalizadores
		 */
		/*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct T218_Id)", $from, $extraTotal);
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T218_Valor_Total)", $from, $extraTotal));
		$registrosAberto = $g['sqlAuto']->pegarSqlCampo("count(distinct T218_Id)", $from, $extraTotalAberto);
		$valorTotalAberto = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T218_Valor_Total)", $from, $extraTotalAberto));
		$registrosFechado = $g['sqlAuto']->pegarSqlCampo("count(distinct T224_Id)", $from, $extraTotalFechado, $extraTotalFechado);
		$valorTotalFechado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T225_Valor_Total_Preco + T225_Valor_IPI)", $from, str_replace('GROUP BY T218_Id', '', $extraTotalFechado)));
		$registrosPerdido = $g['sqlAuto']->pegarSqlCampo("count(distinct T218_Id)", $from, $extraTotalPerdido);
		$valorTotalPerdido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T218_Valor_Total)", $from, str_replace('GROUP BY T218_Id', '', $extraTotalPerdido)));
		//T218_Flag_Perdido='S'
		//echo "Registros: <br />" . "count(*)" . $from . $extra . "<br />    Valor Total: <br />" . "sum(T218_Valor_Total)" . $from . $extra;
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Cotações<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosAberto}" readonly></span>
				<span class="item" style="text-align:right">Valor abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalAberto}" readonly></span>
				<span class="item" style="text-align:right">Fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosFechado}" readonly></span>
				<span class="item" style="text-align:right">Valor fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalFechado}" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T218_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
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

	case 'srm001GridOrdensCompraFornecedor':

		$filtroObrigatorio = true;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoCancelamentoOrdemCompra', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamentoOrdemCompra', 'style' => 'width:1%;', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T224_Numero_Pedido_Venda', 'colunaTitulo' => 'Pedido Ecommerce', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
            array('colunaValor' => 'T224_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'T224_D024_Id', 'colunaTitulo' => 'Cód.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'concat(substr(D024_Nome_Empresa,1,30),\'<br><font style="font-weight:normal;font-size:10px"><i>\',ifnull(T224_Observacao_Pedido_Perdido,\'\'))', 'colunaTitulo' => 'Razão Social', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;font-weight:bold'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\'),\' \',ifnull(D013_DDD_Celular,\'\'),\'-\',ifnull(D013_Celular,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;min-width:190px;width:190px;white-space:normal;'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'T224_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id)) as valorPendente', 'colunaTitulo' => 'Valor pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;background-color: var(--celula-amarelo-claro);color: var(--BGVermelho);width:1%'),
            array('colunaValor' => '(sum(distinct concat(ifnull(T014_Quantidade,\'0.00000\'),T014_Id))/sum(distinct concat(T225_Quantidade,T225_Id)))*100', 'colunaTitulo' => '%Receb', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'concat(T224_Nome_Status,\'<font style="font-size:10px;font-weight:normal"> \',ifnull(group_concat(DISTINCT \'Mail:\',T139_Data_Hora),\'\'),\'<br><font style="font-size:11px;font-weight:normal">\',ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\'))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;font-weight:bold'),
            array('colunaValor' => 'group_concat(distinct T013_Id SEPARATOR \'|\')', 'colunaTitulo' => 'NF', 'callback' => 'danfeNFsDaOrdemDeCompra', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Nome_Status as Status', 'colunaTitulo' => 'Status', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat("<span style=\\"font-weight:bold\\">", T224_Nome_Status, "</span> ", ifnull((SELECT date_format(T228_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T228 WHERE T228_T224_Id=T224_Id order by T228_Id desc LIMIT 1), ""), "<br />", if(T224_Flag_Status=9, T224_Motivo_Nao_Liberado,"")) as Status2', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(IF(T225_T075_Id>0,"MP","PE"))) FROM T225 WHERE T225_T224_Id=T224_Id) as Estoque', 'colunaTitulo' => 'Estoque', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
                  									FROM T225 
                  									LEFT JOIN D009 ON D009_Id = T225_D009_Id
                  									LEFT JOIN D049 ON D049_Id = D009_D049_Id
                  									LEFT JOIN D082 ON D082_Id = D049_D082_Id
                  									WHERE T225_T224_Id=T224_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'downloadXMLPedido', 'colunaTipo' => 'livre', 'colunaExibir' => false),
            array('colunaValor' => 'analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),
            array('colunaValor' => 'T224_Liberou_Alcada_Data', 'colunaTitulo' => 'Data análise', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(T013_Numero_Nota_Fiscal)', 'colunaTitulo' => 'T013_Numero_Nota_Fiscal', 'colunaExibir' => false),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'T224';
		
		/*
		left join T225 on T225_Id= T225_T224_Id
    			LEFT JOIN D009 ON D009_Id = T225_D009_Id
				LEFT JOIN D049 ON D049_Id = D009_D049_Id
				LEFT JOIN D082 ON D082_Id = D049_D082_Id
		*/
		$extra = <<<EOT
			left join D024 on D024_Id=T224_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join C007 as comprador on comprador.C007_Id=T224_C007_Id_Vendedor_Interno
			left join C007 as analista on analista.C007_Id=T224_Liberou_Alcada_C007_Id
			left join C004 on C004_Id = T224_C004_Id
			left join D116 on D116_Id = T224_D116_Id
			left join D021 on D021_Id = T224_D021_Id
			left join D022 on D022_Id = T224_D022_Id
			left join D013 on D013_Id=T224_D013_Id
			left join T139 on T139_T224_Id=T224_Id
			left join T225 on T225_T224_Id=T224_Id AND T225_Flag_Cancelada != 'S'
			left join D006 on D006_Id = T225_D006_Id
			left join T014 on T014_T225_Id=T225_Id
			left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
			left join D009 on D009_Id=T225_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			where (T224_C004_Id={$g['empresaAtual']} AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
			and D024_Id='{$r_D024_Id}'
			GROUP BY T224_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
				left join D024 on D024_Id=T224_D024_Id
     		    left join C007 as comprador on comprador.C007_Id=T224_C007_Id_Vendedor_Interno
    			left join D006 on D006_Id=T224_D006_Id
    			left join D116 on D116_Id=T224_D116_Id			
    			left join D021 on D021_Id=T224_D021_Id
    			left join T225 on T225_T224_Id=T224_Id 
    			left join T014 on T014_T225_Id=T225_Id
    			left join T013 on T013_Id=T014_T013_Id 
    			left join D009 on D009_Id=T225_D009_Id
    			left join D049 on D049_Id=D009_D049_Id
    			left join D001 on D001_Id=D049_D001_Id
    			where (T224_C004_Id={$g['empresaAtual']} AND T224_Flag_Status!=8 AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
    			and D024_Id='{$r_D024_Id}'
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T224_Flag_Status']=='5'){
				return 'linhaCinza';
			} else if(!empty($array['todosCampos']['group_concat(T013_Numero_Nota_Fiscal)'])){
				return 'linhaCinza';
			} else if($array['todosCampos']['T224_Flag_Status']<=0){
			} else if($array['todosCampos']['T224_Flag_Status']==14){
				return 'linhaVerde';
			} else if($array['todosCampos']['T224_Flag_Status']==15){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T224_Flag_Status']==13){
				return 'linhaAzul';
			} else if($array['todosCampos']['T224_Flag_Status']=='8'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T224_Flag_Status']=='26'){
				return 'linhaLaranja';
			}
		}
		
		/**
         * Botões Almir
         */
		$botoes = array(
			array('titulo' => 'Processar Vencidos', 'id' => 'est016ProcessarOCVencida'),
        );
        echo Botoes::processar($botoes);
		
		/*
		* Botao Receber Pedidos do ecommerce
		*/
		
			/*<div class="buttonsBottomTop">
				<!--<button type="button" id="est016RecebePedidos">Receber Pedidos</button>-->
				<button type="button" id="est016ProcessarOCVencida">Processar Vencidos</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

				$('#est016RecebePedidos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Receber Pedidos', '/est/est016/content/est016ContentReceberPedidos/', '', [400,200]);
				});

				$('#est016ProcessarOCVencida').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est016/grid_func-ajax/est016ProcessarOCVencida/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Processar ordens de compra vencidas', 'Você tem certeza?', \$buttons);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T224_Id' => array('titulo' => 'OC', 'tipo' => 'numero'),
            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'T224_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
            'T224_Nome_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'texto'),
            'D001_Codigo_Produto,D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
        * Totais Almir
        */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Total', 'valor' => 'T224_Valor_Total', 'titulo' => 'Valor Total'),
            array('nome' => 'Valor_Pendente', 'valor' => 'T224_Valor_Pendente', 'titulo' => 'Valor Pendente'),
        );
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Totalizadores
		 */
		/*if (!personalizacaoTotais()) { 
			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(distinct T224_Id)", $from, $extraTotal);
			$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(distinct concat(T224_Valor_Total,',',T224_Id))", $from, $extraTotal));
			$valorPendente = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id))", $from, $extraTotal));
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<span class="item" style="text-align:right">Valor Pendente<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorPendente}" readonly></span>
					<div style="clear:both;"></div>             
				</div>
EOT;
		}*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T224_Id = id[0].replace(/T224_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela={$from}', [990,605]);  //950 615
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

	case 'srm001GridNotasRecebidasFornecedor':

		$filtroObrigatorio = true;

		$grid = array(
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
            array('colunaValor' => 'T013_Flag_Estoque_Finalizado', 'colunaTitulo' => 'Recebido', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Numero_Controle', 'colunaTitulo' => 'Contr', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'N.F.', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'group_concat(distinct T225_T224_Id)', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'group_concat(distinct substr(C007_Primeiro_Nome,1,10))', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Data_Entrada', 'colunaTitulo' => 'Entrada', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Data_Entrada_Estoque', 'colunaTitulo' => 'Estoque', 'callback' => 'gCorrigeData', 'callbackParameter' => '1'),
            array('colunaValor' => 'T013_Data_Financeiro', 'colunaTitulo' => 'Financeiro', 'callback' => 'gCorrigeData', 'callbackParameter' => '1'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'callback' => 'coloreLinha'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => "if(T013_Tipo='1','NF',if(T013_Tipo='2','ACP',if(T013_Tipo='3','NF Consumidor',if(T013_Tipo='4','NFe','CF'))) ) as Tipo_Documento", 'colunaTitulo' => 'Doc'),
            array('colunaValor' => "group_concat(distinct D006_Codigo_CFOP separator ',')", 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'sum((T014_Quantidade*T014_Valor_Custo_Unitario)-T014_Valor_Total_Desconto) as Produtos', 'colunaTitulo' => 'Produtos', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T013_Valor_ICMS_Substituicao', 'colunaTitulo' => 'ICMS ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T013_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T013_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T013_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'Almoxarifado', 'colunaTitulo' => 'Estoque', 'callback' => 'validaCampoAlmoxarifado', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'Cancelar', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'DANFE', 'colunaTitulo' => 'DANFE', 'callback' => 'validaCampoDanfe', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso'),
            array('colunaValor' => 'T013_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocoloo NFe'),
            array('colunaValor' => 'T013_Retorno_Nfe', 'colunaTitulo' => 'Retorno SEFAZ'),
            array('colunaValor' => 'T013_Flag_Cancelada', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Flag_Travar_Nota', 'colunaTitulo' => '', 'colunaExibir' => false),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T013";
		
		$extra = <<<EOT
		    left join T014 on T014_T013_Id=T013_Id
			left join T012 on T012_Id=T014_T012_Id
			left join T225 on T225_Id=T014_T225_Id
			left join T224 on T224_Id=T225_T224_Id
			left join D009 on D009_Id=T014_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D024 on D024_Id=T013_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D006 on D006_Id=T014_D006_Id
			left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
			where T013_C004_Id='{$g['empresaAtual']}'
			and D024_Id='{$r_D024_Id}'
			GROUP BY T013_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est004IncluirNotaFiscal_Btn'),
			array('titulo' => 'Atualizar Pedidos Venda', 'id' => 'est004AtualizarPedidosVenda_Btn',),
		);
		echo Botoes::processar($botoes);


		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="est004IncluirNotaFiscal_Btn">Incluir</button>
				<!-- <button type="button" onclick="nova_janela('/hardness/nota_recebida_lancamento/atualizar_pedidos_venda.php',0,0,820,600,'no','no','no','no','yes','yes',0);">Atualizar Pedidos Venda</button> -->
				<button type="button" id="est004AtualizarPedidosVenda_Btn">Atualizar Pedidos Venda</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est004IncluirNotaFiscal_Btn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir nota Fiscal', '/est/est004/content/est004ContentCadastroNotaFiscal/', '&tab=geral', [990,550]);
				});
				$('#est004AtualizarPedidosVenda_Btn').unbind('click').bind('click', function(data) {
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/est/est004/grid_func-ajax/atualizarPedidosVenda/?ajax=true&callback=?', function(request) {
					if (request.code) {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Sucesso!", request.data, buttons)
			    		divRefresh('{$g['divId']}'); 
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Erro!", request.data, buttons)
					}
				});
			});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'texto'),
            'T013_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'T013_Numero_Controle' => array('titulo' => 'Controle', 'tipo' => 'texto'),
            'D006_Descricao,D006_Tipo_CFOP, D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'T013_Data_Entrada' => array('titulo' => 'Entrada', 'tipo' => 'data'),
            'T013_Data_Entrada_Estoque' => array('titulo' => 'Estoque', 'tipo' => 'data'),
            'T013_Data_Financeiro' => array('titulo' => 'Financeiro', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'T013_Numero_Protocolo_Nfe' => array('titulo' => 'Protocolo NFe', 'tipo' => 'texto'),
            'T013_Chave_Acesso_Nfe' => array('titulo' => 'Chave NFe', 'tipo' => 'texto'),
            'T013_Flag_Cancelada' => array('titulo' => 'NF Cancelada', 'tipo' => 'select', 'select' => array(
                array('title' => 'TODAS', 'value' => ''),
                array('title' => 'SIM', 'value' => 'S'),
                array('title' => 'NÃO', 'value' => 'N'),
              ),
            ),
            'T224_Id' => array('titulo' => 'OC', 'tipo' => 'numero'),
            'T013_Valor_ICMS_Substituicao' => array('titulo' => 'ICMS ST', 'tipo' => 'numero'),
			'T013_Valor_Frete' => array('titulo' => 'Frete', 'tipo' => 'numero'),
			'T013_Valor_Total_IPI' => array('titulo' => 'IPI', 'tipo' => 'numero'),
			'T013_Valor_Total_ICMS' => array('titulo' => 'ICMS', 'tipo' => 'numero'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		function gridLinha($array) {
			if($array['todosCampos']['T013_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			} else  if($array['todosCampos']['T013_Flag_Estoque_Finalizado']=='S'){
				return 'linhaCinza';
			}
		}

		/**
		 * Totais Almir
		 */
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Valor_Produtos', 'valor' => 'T013_Valor_Total_Produtos', 'titulo' => 'Valor Produtos'),
			array('nome' => 'Valor_Total', 'valor' => 'T013_Valor_Total', 'titulo' => 'Valor Total'),
			array('nome' => 'Valor_ICMS_ST', 'valor' => 'T013_Valor_ICMS_Substituicao', 'titulo' => 'Total ICMS ST'),
			array('nome' => 'Valor_Frete', 'valor' => 'T013_Valor_Frete', 'titulo' => 'Total Frete'),
			array('nome' => 'Valor_IPI', 'valor' => 'T013_Valor_Total_IPI', 'titulo' => 'IPI R$'),
			array('nome' => 'Valor_ICMS', 'valor' => 'T013_Valor_Total_ICMS', 'titulo' => 'ICMS R$'),

		);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	
		//if (!personalizacaoTotais()) {
			/**
			* Totais (Versão Acima do GRID)
			*/
			/*$C004_Id = false;
	        preg_match("/T013_C004_Id=\'([^\'])+\'/", $extra, $matches);
	        if (isset($matches[1])) { $C004_Id = $matches[1]; }
			// O extra utilizados para o total é diferente do utilizado para montar o grid
			$extraTotais1 = <<<EOT
			    left join D024 on D024_Id=T013_D024_Id
			    left join D018 on D018_Id=D024_D018_Id
			    left join D006 on D006_Id=T013_D006_Id
EOT;

			$extraTotais1 .= ($C004_Id > 0 ? " WHERE T013_C004_Id='{$C004_Id}' AND T013_Flag_Cancelada!='S'" : "");
	        $extraTotais1 = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotais1);
			$extraTotais = <<<EOT
			    left join T014 on T014_T013_Id=T013_Id
			    left join T012 on T012_Id=T014_T012_Id
			    left join D009 on D009_Id=T014_D009_Id
			    left join D049 on D049_Id=D009_D049_Id
			    left join D001 on D001_Id=D049_D001_Id
			    left join D024 on D024_Id=T013_D024_Id
			    left join D018 on D018_Id=D024_D018_Id
			    left join D006 on D006_Id=T014_D006_Id
                left join T225 on T225_Id=T014_T225_Id
                left join T224 on T224_Id=T225_T224_Id
                left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
EOT;

			$extraTotais .= ($C004_Id > 0 ? " WHERE T013_C004_Id='{$C004_Id}' AND T013_Flag_Cancelada!='S' AND D024_Id='{$r_D024_Id}'" : " WHERE D024_Id='{$r_D024_Id}'");


	        $extraTotais = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotais);
			$valorTotal  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(distinct concat(T013_Valor_Total,T013_Id))", $from, $extraTotais)));
			$valorTotal1 = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Quantidade*T014_Valor_Custo_Unitario)", $from, $extraTotais)));
			$valorTotal2 = $g['sqlAuto']->pegarSqlCampo("count(distinct T013_Id)", $from, $extraTotais);
			$totalICMSST = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_ICMS_Substituicao)", $from, $extraTotais)));
			$totalFrete  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_Frete)", $from, $extraTotais)));
			$totalIPI    = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_IPI)", $from, $extraTotais)));
			$totalICMS   = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_ICMS)", $from, $extraTotais)));
			
			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Notas Recebidas<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS ST<br><input type="text" value="{$totalICMSST}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total Frete<br><input type="text" value="{$totalFrete}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total IPI<br><input type="text" value="{$totalIPI}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS<br><input type="text" value="{$totalICMS}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			$g['smarty']->assign('totais', $totais);
		}*/
			
		
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'FONT') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T013_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Nota Fiscal: ' + $(this).closest('tr').find('td:eq(1)').text(), '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + id + '&tabela=T013', 'auto', false, false);
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

  	case 'srm001GridConhecimentosFornecedor':
		global $g;
		
		$grid = array(
            array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'font-size:10px'),
            array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'Conhecimento', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T084_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T084_Data_Lancamento', 'colunaTitulo' => 'Lançamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Financeiro', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D022_Id', 'colunaTitulo' => 'Cód'),
            array('colunaValor' => 'substr(D022_Nome_Empresa,1,20)', 'colunaTitulo' => 'Transportadora'),
            array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T084_Valor_ICMS', 'colunaTitulo' => 'ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'T084_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaExibir' => false),
            array('colunaValor' => 'T084_Flag_Fechado', 'colunaTitulo' => 'Notas', 'callback' => 'validaNotasLancadas'),
            array('colunaValor' => 'substr(C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'Cancelar', 'colunaTitulo' => 'Cancelar', 'colunaTipo' => 'livre', 'callback' => 'validaCampoCancelarConhecimento'),
            array('colunaValor' => 'Excluir', 'colunaTitulo' => 'Excluir', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExcluirConhecimento'),
            array('colunaValor' => 'T084_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_C007_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_D022_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T084_D006_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T084";
		
		$extra = <<<EOT
				left join C007 on C007_Id=T084_C007_Id
			    left join D022 on D022_Id=T084_D022_Id
			    left join D024 on D024_Id=T084_D024_Id
			    left join D020 on D020_Id=D024_D020_Id
			    left join D018 on D018_Id=D020_D018_Id
			    left join T015 on T015_T084_Id=T084_Id and T015_Flag_Cancelada!='S'
			    left join D006 on T084_D006_Id=D006_Id
			    left join T085 on T085_T084_Id = T084_Id
			    left join T013 on T085_T013_Id = T013_Id
			    where (T084_C004_Id='{$g['empresaAtual']}') 
			    and D024_Id = '{$r_D024_Id}'   
			    group by T084_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est005IncluirConhecimentoBtn'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="est005IncluirConhecimentoBtn">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est005IncluirConhecimentoBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir conhecimento', '/est/est005/content/est005ContentCadastroConhecimento/', '&tab=geral', [650,400]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T084_Numero_Conhecimento' => array('titulo' => 'Conhecimento', 'tipo' => 'numero'),
            'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'numero'),
            'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
            'T084_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T084_Data_Lancamento' => array('titulo' => 'Lançamento', 'tipo' => 'data'),
            'T015_Data_Vencimento' => array('titulo' => 'Financeiro', 'tipo' => 'data'),
            'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
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
			array('nome' => 'Valor_Total', 'valor' => 'T084_Valor_Conhecimento', 'titulo' => 'Valor Total'),
			array('nome' => 'Valor_ICMS', 'valor' => 'T084_Valor_ICMS', 'titulo' => 'Valor ICMS'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	
		/*if (!personalizacaoTotais()) {
			$extraTotal = gRetirarGroupBy($extra);
			$quantidade = $g['sqlAuto']->pegarSqlCampo("count(distinct T084_Id)", $from, $extraTotal);
			$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T084_Valor_Conhecimento)", $from, $extraTotal));
			$valorICMS = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T084_Valor_ICMS)", $from, $extraTotal));
			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Conhecimentos<br><input type="text" value="{$quantidade}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS<br><input type="text" value="{$valorICMS}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			$g['smarty']->assign('totais', $totais);
		}*/
		$novoIdTelaForm = uniqid();	
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T084_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Conhecimento ' + $(this).closest('tr').find('td:eq(1)').text(), '/est/est005/content/est005ContentEditarConhecimento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,500]);
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

	case 'srm001GridProdutosCotacaoFornecedor':
        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
        
		$grid = array(
            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T218_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Cliente', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cód', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'concat(substr(D024_Nome_Empresa,1,20),\'-\',D018_UF)', 'colunaTitulo' => 'Fornecedor', 'style' => 'font-weight:bold;background:#FCF592'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'Produto', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Descricao_Produto', 'colunaTitulo' => 'Descrição produto', 'style' => 'max-width:300px;min-width:300px;white-space:normal;', 'colunaExibir' => false),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Quant', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;background:#FCF592'),
            array('colunaValor' => 'T219_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T219_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Observacao_Entrega', 'colunaTitulo' => 'Entrega', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;color: var(--BGVermelho)'),
            array('colunaValor' => 'T219_Flag_Comprar', 'colunaTitulo' => '', 'callback' => 'validaCampoComprarCotacao', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T219_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);font-weight:bold;width:1%'),
            array('colunaValor' => 'T219_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
            array('colunaValor' => 'T219_Valor_Preco_Venda', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);font-weight:bold;width:1%'),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Médio', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_ST_Digitado', 'colunaTitulo' => 'ST Forn R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
            array('colunaValor' => 'T219_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T219_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_Percentual_Reducao_ICMS', 'colunaTitulo' => '%Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_Percentual_Frete', 'colunaTitulo' => '%Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_Percentual_Financeiro', 'colunaTitulo' => '%Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_ICMS_Inter_Estadual', 'colunaTitulo' => '%ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_ST_ICMS_Interno', 'colunaTitulo' => '%ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
            array('colunaValor' => 'T219_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Flag_Comprar', 'colunaTitulo' => 'D009_Flag_Comprar', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'concat(T219_Codigo_Produto,\' \',ifnull(D049_Modelo,\'\'),\' - \',T219_Descricao_Produto) as produto', 'colunaTitulo' => 'Produto', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'if(T225_Id is not null,1,0) as Itens_Comprado', 'colunaTitulo' => 'Itens_Comprado', 'colunaExibir' => false),
			array('colunaValor' => 'if(T225_Id is not null,T225_Quantidade,0) as Qtd_Comprado', 'colunaTitulo' => 'Qtd_Comprado', 'colunaExibir' => false),
			array('colunaValor' => 'if(T225_Id is not null,T225_Valor_Total_Preco_Sem_Desconto,0) as Total_Comprado', 'colunaTitulo' => 'Total_Comprado', 'colunaExibir' => false),
		);
        
        /**
         * Definição da SQL
         */
        $from = "T219";

       
        $extra = <<<EOT
                    left join D009 on D009_Id=T219_D009_Id
                    left join D049 on D049_Id=D009_D049_Id
                    left join D082 on D082_Id=D049_D082_Id
                    left join D006 on D006_Id=T219_D006_Id
                    left join D037 on D037_Id=T219_D037_Id
                    left join T218 on T218_Id=T219_T218_Id
                    left join T225 on T225_T219_Id=T219_Id
                    left join D024 on D024_Id=T218_D024_Id
                    left join D018 on D018_Id=D024_D018_Id
                    left join C007 on C007_Id=T218_C007_Id_Vendedor_Interno
                    left join D021 on D021_Id=D024_D021_Id
                    left join C004 on C004_Id=T218_C004_Id
                    left join D116 on D116_Id=T218_D116_Id
                    where D024_Id='{$r_D024_Id}'
EOT;
        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T225_Id']>0){
				return 'linhaCinza';
			} else if($array['todosCampos']['T219_Flag_Cancelado']=='S'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T224_Flag_Status']<=0){
				if ($array['todosCampos']['T218_Flag_Status_Orcamento']=='V') {
					return 'linhaFundoAmareloClaro linhaAzul';
				} else {
					return 'linhaAzul';
				}
			}
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Gerar O.C.', 'id' => 'est015GerarOC'),
		);
		echo Botoes::processar($botoes);
        
       	//Gera o filtro e acrescenta ao $extra
       	 /**
        * Filtro
        */
        $gridFiltro = array(
            'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'numero'),
            'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T219_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'T219_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'T219_Flag_Cancelado' => array('titulo' => 'Cancelado(S/N)', 'tipo' => 'texto'),
            'T225_T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'numero'),
            'T219_Valor_Preco_Sem_Desconto_Unitario' => array('titulo' => 'Preço', 'tipo' => 'numero'),
            'Produto' => array('titulo' => 'Produto', 'campos' => array(
                array('title' => 'produto', 'campoAgrupar' => 'produto', 'campoMostrar' => 'produto', 'campoOrdem' => '', 'campoSelecionado' => true),
            ), 'tipo' => 'agrupar', 'exibir' => false),
        );
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Quantidade_Total', 'valor' => 'T219_Quantidade', 'titulo' => 'Qtd total cotados'),
			array('nome' => 'Valor_Total', 'valor' => 'T219_Valor_Total_Preco_Sem_Desconto', 'titulo' => 'Valor total cotados'),
			array('nome' => 'Itens_Comprado', 'valor' => 'Itens_Comprado', 'titulo' => 'Itens comprado'),
			array('nome' => 'Qtd_Comprado', 'valor' => 'Qtd_Comprado', 'titulo' => 'Qtd comprado'),
			array('nome' => 'Total_Comprado', 'valor' => 'Total_Comprado', 'titulo' => 'Valor total comprado'),
		);
 
         /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1500);    

		/**
		* Botões (Versão Acima do GRID)
		*/
		//$extra_ = urlencode(base64_encode($extra));
		$sql_ = urlencode(base64_encode($geradoSql));
		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est015GerarOC">Gerar O.C.</button>
					<!--<button type="button" id="est015AtualizarCalculoEstoque">Atualizar Estoque</button>-->
				</div>
EOT;
		}*/

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est015AtualizarCalculoEstoque').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est009/grid_func-ajax/processaCalcularEstoque/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
				});
				$('#est015GerarOC').unbind('click').bind('click', function(data) {
					$.getJSON('/est/est015/grid_func-ajax/est015VerificaGerarOC/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						if(request.code){
							var \$buttons = {
								"Sim": function() {
									$(this).dialog("destroy").remove();
									var \$buttons = {
										"Sim": function() {
											$('#{$g['divId']}').showLoading();
											$.getJSON('/est/est015/grid_func-ajax/est015GerarOC/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
												if (request.code) {
													var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
													dialogConfirm('Operação Ok', request.data, \$buttons);
													divRefresh('{$g['divId']}', true);
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
									dialogConfirm('Gerar Ordens de compra', 'Confirma a geração da(s) ordem(ns) de compra', \$buttons);
								},
								"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Ordens de compra', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    dialogConfirm('Erro', request.data, \$buttons);
						}
					});
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T218_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
			});
		} </script></div>
EOT;

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
        /**
        * Botões (Versão Acima do GRID)
        */
        /*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        $valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T219_Quantidade) Total", $from, $extra));
        $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T219_Valor_Total_Preco_Sem_Desconto)", $from, $extra));
        $valorTotalVendido = $g['sqlAuto']->pegarSqlCampo("count(T225_Id)", $from, $extra . " AND T225_Id IS NOT NULL");
        $valorTotal1Vendido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T225_Quantidade) Total", $from, $extra));
        $valorTotal2Vendido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T225_Valor_Total_Preco_Sem_Desconto) Total", $from, $extra));
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Qtd total cotados<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor total cotados<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Itens comprado<br><input type="text" value="{$valorTotalVendido}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Qtd comprado<br><input type="text" value="{$valorTotal1Vendido}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor total comprado<br><input type="text" value="{$valorTotal2Vendido}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

        $novoIdTelaForm = uniqid();
        
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

    case 'srm001GridPrincipalProdutosVsFornecedor':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(T219_Codigo_Produto," ", ifnull(D049_Modelo,""), " - ", T219_Descricao_Produto) as Produto', 'colunaTitulo' => 'Produto', 'style' => 'width: 1%;font-weight:bold'),
        );

		/**
		* Definição da SQL
		*/
		$from = "T219 AS T219A";

		$extra = <<<EOT
			left join D009 on D009_Id=T219A.T219_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=T219A.T219_D006_Id
			left join D037 on D037_Id=T219A.T219_D037_Id
			left join T218 AS T218A on T218A.T218_Id=T219A.T219_T218_Id
			left join T225 on T225_T219_Id=T219A.T219_Id
			left join D024 on D024_Id=T218A.T218_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join C007 on C007_Id=T218A.T218_C007_Id_Vendedor_Interno
			left join D021 on D021_Id=D024_D021_Id
			left join C004 on C004_Id=T218A.T218_C004_Id
			left join D116 on D116_Id=T218A.T218_D116_Id
			where D024_Id='{$r_D024_Id}'
			GROUP BY T219_Codigo_Produto
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'numero'),
            'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T219_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'T219_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'T219_Flag_Cancelado' => array('titulo' => 'Cancelado(S/N)', 'tipo' => 'texto'),
            'T225_T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'numero'),
            'T219_Valor_Preco_Sem_Desconto_Unitario' => array('titulo' => 'Preço', 'tipo' => 'numero'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Usar mesmo extra na subquery
		$extraSub = gRetirarGroupBy($extra);

		// Loop para criar colunas com os fornecedores
		$sql = <<<EOT
			SELECT 
				DISTINCT D024_Id, D024_Id, D024_Nome_Empresa
			FROM
				T219 AS T219A
			{$extraSub}
EOT;
		$res = mysql_query($sql);
		if (mysql_num_rows($res) <= 0) {
			die("<p style='margin: 30px; font-weight: bold; font-size: 1.3em; text-align: center;'>Nenhum registro encontrado!</p>");
		}
		if (mysql_num_rows($res) > 50) {
			die("<p style='margin: 30px; font-weight: bold; font-size: 1.3em; text-align: center;'>Consulta retornou muitos registros.<br />Por favor, utilize o filtro.</p>");
		}
		$cacheLimit = array();
		while($row = mysql_fetch_assoc($res)) {
			$subSqlExtra = str_replace('T219A', 'T219B', $extraSub);
			$subSqlExtra = str_replace('T218A', 'T218B', $subSqlExtra);
			$subSql = <<<EOT
				IFNULL((
				SELECT
					GROUP_CONCAT(T219_Valor_Custo_Unitario, ' - ')
				FROM
					T219 AS T219B
				{$subSqlExtra}
				AND {$row['D024_Id']}=T218B.T218_D024_Id
				AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto
				ORDER BY T219_Valor_Custo_Unitario ASC
				),0)
EOT;
			$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => substr($row['D024_Nome_Empresa'], 0, 10), 'colunaAlinhar' => 'center', 'callback' => 'custosPorFornecedor', 'forcarExibirImpressao' => true, 'style' => 'color:blue');
		}
		// Coluna melhor preço
		$subSql = <<<EOT
			(
			SELECT
				CONCAT(ifnull(T219_Valor_Custo_Unitario,0), '|',ifnull(D024_Nome_Empresa,''), '|',ifnull(T219_Observacao_Entrega,''))
			FROM
				T219 AS T219B
			{$subSqlExtra}
			AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto
			AND T219B.T219_Valor_Custo_Unitario != 0
			ORDER BY T219B.T219_Valor_Custo_Unitario ASC
			LIMIT 0,1
			)
EOT;
		$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => 'Melhor Preço', 'colunaAlinhar' => 'left', 'callback' => 'custosPorFornecedorMelhorPreco', 'forcarExibirImpressao' => true);

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

	case 'srm001GridProdutosOrdensComprasFornecedor':
        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T225_Id', $g['divId']), 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style' => 'font-weight:normal', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Codigo_Produto', 'colunaTitulo' => 'Codigo', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referencia', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Codigo_Substituto', 'colunaTitulo' => 'Cod.Fornec', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T225_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'T225_T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueMP', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
            array('colunaValor' => 'T225_Valor_Preco_Original', 'colunaTitulo' => 'Prc.Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Valor_Preco_Original_Bruto', 'colunaTitulo' => 'Original Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Percentual_Desconto_Bruto', 'colunaTitulo' => 'Desc % Bruto', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
            array('colunaValor' => 'T225_Valor_Preco_Unitario', 'colunaTitulo' => 'Unitario', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => 'font-weight:bold;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
            array('colunaValor' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preco Unitario', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'style' => 'background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T225_Observacao', 'colunaTitulo' => 'Observação', 'style' => 'background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T225_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo Cot', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);font-weight:bold', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#BADCFC', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_Preco_Venda', 'colunaTitulo' => 'Preço venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);font-weight:bold', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tab', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo Medio', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Flag_Origem_Produto', 'colunaTitulo' => 'Origem', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Percentual_Reducao_ICMS', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Percentual_Frete', 'colunaTitulo' => '% Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Percentual_Financeiro', 'colunaTitulo' => '% Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_Base_ICMS', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.Ped.', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Item_Pedido_Compra', 'colunaTitulo' => 'Item.Ped.', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T225_Flag_Excluir', 'colunaTitulo' => 'T225_Flag_Excluir', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
        );
        
        /**
         * Definição da SQL
         */
        $from = "T225";
        
        $extra = <<<EOT
        	left join T224 on T224_Id=T225_T224_Id
			left join D024 on D024_Id=T224_D024_Id
			left join D037 on D037_Id=T225_D037_Id
			left join D009 on D009_Id=T225_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=T225_D006_Id
			left join D005 on D005_Id=T225_D005_Id
			left join T014 on T014_T225_Id=T225_Id
			left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
			left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
			where D024_Id='{$r_D024_Id}'
			  and T224_C004_Id='{$g['empresaAtual']}' 
EOT;
        /*
        * Pode ser definido uma classe para cada linha do grid
        */
        function gridLinha($array) {
			if($array['todosCampos']['T224_Flag_Status']=='5'){
				return 'linhaCinza';
			} else if(!empty($array['todosCampos']['group_concat(T013_Numero_Nota_Fiscal)'])){
				return 'linhaCinza';
			} else if($array['todosCampos']['T224_Flag_Status']<=0){
			
			} else if($array['todosCampos']['T224_Flag_Status']=='14'){
				return 'linhaVerde';
			} else if($array['todosCampos']['T224_Flag_Status']=='15'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T224_Flag_Status']=='13'){
				return 'linhaAzul';
			} else if($array['todosCampos']['T224_Flag_Status']=='8'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T224_Flag_Status']=='26'){
				return 'linhaLaranja';
			}
		}

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);
        
        /**
        * Filtro
        */
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T224_Id' => array('titulo' => 'OC', 'tipo' => 'numero'),
            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo', 'tipo' => 'select', 'naoGerarExtra' => ''),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'T225_Codigo_Produto' => array('titulo' => 'Código produto', 'tipo' => 'numero'),
            'T225_Descricao_Produto' => array('titulo' => 'Descrição produto', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
        );
        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Quantidade_Total', 'valor' => 'T225_Quantidade', 'titulo' => 'Quantidade total'),
            array('nome' => 'Valor_Total', 'valor' => 'T225_Valor_Total_Preco', 'titulo' => 'Valor produtos total'),
        );
        
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);    


        /**
        * Botões (Versão Acima do GRID)
        */
        /*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        $valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T225_Quantidade) Total", $from, $extra));
        $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T225_Valor_Total_Preco_Sem_Desconto)", $from, $extra));
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Quantidade total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor produtos total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false; 
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(data.target).hasClass('editar-linha')) { return true; }
					// pega o ID da linha
					var D001_Id = chavePrimaria($(this).closest('tr').attr('id'), 'D001_Id');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id), [1000,600]);
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

    case 'srm001GridNotasFiscaisRecebidasProdutosFornecedor':
		$grid = array(
            array('colunaValor' => 'T013_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'T014_Id', 'colunaTitulo' => 'T014_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T014_Codigo_Produto', 'colunaTitulo' => 'Cód Forn'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Nosso Cod'),
            array('colunaValor' => 'T014_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'callback' => 'coloreLinhaProdutos'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T014_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T014_D006_Id', 'colunaTitulo' => 'CFOP', 'colunaEditarGeraSelect' => array('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', '', true), 'callback' => 'T014_D006_Id_CFOP'),
            array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Qte', 'colunaCasasDecimais' => '3', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Custo_Unitario', 'colunaTitulo' => 'Preço', 'extra' => 'style="width:300px;"', 'colunaCasasDecimais' => '6', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'style' => 'font-weight:bold', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Total_Custo', 'colunaTitulo' => 'Total', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Total_Desconto', 'colunaTitulo' => 'ICMS ST', 'callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_ICMS_Substituicao', 'colunaTitulo' => 'Total IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Valor_ICMS', 'colunaTitulo' => 'ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Aliquota_ICMS', 'colunaTitulo' => 'Aliq. ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Percentual_Reducao_ICMS', 'colunaTitulo' => 'Red. ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T014_Aliquota_IPI', 'colunaTitulo' => 'Aliq. IPI', 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_tabela_T014', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T012_T011_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T013_Flag_Travar_Nota', 'colunaTitulo' => 'OC', 'colunaExibir' => false),
            array('colunaValor' => 'T014_D009_Id', 'colunaTitulo' => 'IdProd', 'colunaExibir' => false),
            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'IdEmpr', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),		
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "T014";
		
		$extra = <<<EOT
			left join T013 on T013_Id=T014_T013_Id
            left join D024 on D024_Id=T013_D024_Id
			left join T012 on T012_Id=T014_T012_Id
			left join D009 on D009_Id=T014_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D037 on D037_Id=D001_D037_Id
			left join D082 on D082_Id=D049_D082_Id
			left join T001 on T001_T014_Id=T014_Id
			left join D006 on D006_Id=T014_D006_Id
			where D024_Id='{$r_D024_Id}'
              and T013_C004_Id='{$g['empresaAtual']}'
			order by T014_Id
EOT;
        /**
        * Ação ao clicar em uma linha do grid
        */
    	$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'FONT') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T014_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Item', '/est/est004/content/est004ContentAssociarItem/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [400,150]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});			
		} </script></div>
EOT;

		/**
		* Filtro do modo correto, porém está dando um looping, verificar com o tiago
		* Problema resolvido, o primeiro parametro enviado para o gridFiltroExtra deve ser o mesmo dado ao array do filtro
		*/
		$gridFiltro = array(
            'T013_Numero_Controle' => array('titulo' => 'Controle', 'tipo' => 'texto'),
            'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'texto'),
            'T013_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T013_Data_Entrada' => array('titulo' => 'Entrada', 'tipo' => 'data'),
            'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'T014_Codigo_Produto' => array('titulo' => 'Código produto', 'tipo' => 'texto'),
            'T014_Descricao_Produto' => array('titulo' => 'Descrição produto', 'tipo' => 'texto'),
            'T013_Flag_Cancelada' => array('titulo' => 'NF Cancelada', 'tipo' => 'select', 'select' => array(
                	array('title' => 'TODAS', 'value' => ''),
                	array('title' => 'SIM', 'value' => 'S'),
                	array('title' => 'NÃO', 'value' => 'N'),
              	),
            ),
            'T014_Valor_ICMS_Substituicao' => array('titulo' => 'ICMS ST', 'tipo' => 'numero'),
			'T014_Valor_Frete' => array('titulo' => 'Frete', 'tipo' => 'numero'),
			'T014_Valor_IPI' => array('titulo' => 'IPI', 'tipo' => 'numero'),
			'T014_Valor_ICMS' => array('titulo' => 'ICMS', 'tipo' => 'numero'),
        );

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		function gridLinha($array) {
			if($array['todosCampos']['T013_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}
		}

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Quantidade', 'valor' => 'T014_Quantidade', 'titulo' => 'Quantidade', 'exibir'=>false),
			array('nome' => 'Valor_Total','valor' => 'T014_Valor_Custo_Unitario', 'titulo' => 'Valor Total'),
			array('nome' => 'Valor_ST', 'valor' => 'T014_Valor_ICMS_Substituicao', 'titulo' => 'Total ICMS ST'),
			array('nome' => 'Valor_Frete', 'valor' => 'T014_Valor_Frete', 'titulo' => 'Total Frete'),
			array('nome' => 'Valor_IPI', 'valor' => 'T014_Valor_IPI', 'titulo' => 'Total IPI'),
			array('nome' => 'Valor_ICMS_', 'valor' => 'T014_Valor_ICMS', 'titulo' => 'Total ICMS'),
        );

		/*if (!personalizacaoTotais()) {
			$valorTotal  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(distinct concat(T013_Valor_Total,T013_Id))", $from, $extra)));
			$quantidade = $g['sqlAuto']->pegarSqlCampo("count(distinct T014_Id)", $from, $extra);
			$totalICMSST = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_ICMS_Substituicao)", $from, $extra)));
			$totalFrete  = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_Frete)", $from, $extra)));
			$totalIPI    = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_IPI)", $from, $extra)));
			$totalICMS   = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Valor_ICMS)", $from, $extra)));
			
			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Notas Recebidas<br><input type="text" value="{$quantidade}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS ST<br><input type="text" value="{$totalICMSST}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total Frete<br><input type="text" value="{$totalFrete}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total IPI<br><input type="text" value="{$totalIPI}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ICMS<br><input type="text" value="{$totalICMS}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			$g['smarty']->assign('totais', $totais);
		}*/

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est004/content/est004ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
				// });
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
					function colunaEditarCallbackJS_tabela_T014(T014_Id, campoEditado, valorAntigo, novoValor) {
						T014_Id = T014_Id.replace(/T014_Id-/, '');
						if (campoEditado == 'T014_Quantidade') {
							$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																						      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																						      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																						      '&novoValor='			+ encodeURIComponent(novoValor) +  
																						      '&callback=?', function(request) {
								if (request.code) {
									//divRefresh('{$g['divId']}');
									//alert("NFe Excluída");
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
						} else if (campoEditado == 'T014_Valor_Custo_Unitario') {
							$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																						      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																						      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																						      '&novoValor='			+ encodeURIComponent(novoValor) +  
																						      '&callback=?', function(request) {
								if (request.code) {
									//divRefresh('{$g['divId']}');
									//alert("NFe Excluída");
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
						} else if (campoEditado == 'T014_Aliquota_IPI') {
							$.getJSON('/est/est004/grid_func-ajax/atualizarTotal/?ajax=true&T014_Id='      			+ encodeURIComponent(T014_Id) + 
																						      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																						      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																						      '&novoValor='			+ encodeURIComponent(novoValor) +  
																						      '&callback=?', function(request) {
								if (request.code) {
									//divRefresh('{$g['divId']}');
									//alert("NFe Excluída");
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
						}
					}
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

  	case 'srm001GridNotasFiscaisReceber':				
		$grid = array(
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria'=>true, 'colunaExibir'=>false),
			array('colunaValor'=> 'receberNota', 'colunaTitulo'=>'Receber', 'colunaTipo'=>'livre', 'callback'=>'receberNota','style'=>'padding-left:15px', 'callbackParameter' => array('T007_Id', $g['divId'])),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número' ),			
			array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback'=>'validaTipo'),			
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'T007_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos','colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total','colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
		);

		/**
		 * Definição da SQL
		 */
		$from = "T007";
		
		$extra = <<<EOT
			LEFT join D024 on D024_Id=T007_D024_Id
			LEFT join D006 on D006_Id=T007_D006_Id
			LEFT join C004 on C004_Id=T007_C004_Id
			WHERE T007_D024_Id='{$g['C004']['C004_D024_Id']}'
			AND T007_Flag_Emitida_Recebida!='R'
			AND T007_Numero_Nota_Fiscal NOT IN (SELECT T013_Numero_Nota_Fiscal FROM T013 WHERE T013_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal)				    
				    
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),			
		);
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#{$g['divId']} .grid').css('width', '1%');
			} </script></div>
EOT;	
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
		
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Notas Pendentes<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
		$novoIdTelaForm = uniqid();
		
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

	case 'srm001GridItensOrdemCompra':  
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
        /**
         * Definição do Grid (Tabela)
         */
		$grid = array(
            array('colunaValor' => 'T011_Id', 'colunaTitulo' => 'O.C.', 'chavePrimaria' => true),
            array('colunaValor' => 'T012_Id', 'colunaTitulo' => 'T012_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T086_Id', 'colunaTitulo' => 'T086_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T086_T012_Id', 'colunaTitulo' => 'T086_T012_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T086_T013_Id', 'colunaTitulo' => 'T086_T013_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'Req.'),
            array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Cot.'),
            array('colunaValor' => 'T011_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Cód Produto'),
            array('colunaValor' => 'T012_Descricao_Produto', 'colunaTitulo' => 'Descrição Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T012_Quantidade_Falta_Lancar(T012_Id,T086_Id) as T012_Quantidade_Pendente', 'colunaTitulo' => 'Qtd. Pendente', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
            array('colunaValor' => 'ifnull(T086_Quantidade,0) as T086_Quantidade', 'colunaTitulo' => 'Recebido', 'callback' => 'editarT086_Quantidade', 'callbackParameter' => ''),
            array('colunaValor' => 'T012_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor Unitário', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T012_Valor_Total_Custo', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'T012_Quantidade', 'colunaTitulo' => 'Qtd. OC', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaAlinhar' => 'right'),
        );

        /**
         *
         * Definição da SQL
         */
        $from = 'T012';
        
        //Verificar se é assim que verifica se os itens estão pendendtes e incluir na linha em branco da variável $extra: and (T086_T013_Id is null or T086_T013_Id=0 or T086_T013_Id='')
        $extra = <<<EOT
            left join D009 on D009_Id=T012_D009_Id 
            left join D049 on D049_Id=D009_D049_Id 
            left join D082 on D082_Id=D049_D082_Id 
            left join D001 on D049_D001_Id=D001_Id 
            left join T011 on T011_Id=T012_T011_Id  
            left join D024 on D024_Id=T011_D024_Id
            left join T086 on T086_T012_Id=T012_Id
            left join T059 on T059_Id=T012_T059_Id
            left join T189 on T189_Id=T059_T189_Id or T189_Id=T012_T189_Id
			left join T010 on T010_Id=T059_T010_Id
			left join T014 on T014_T012_Id=T012_Id
			left join T013 on T013_Id=T014_T013_Id
			left join C007 on C007_Id=T189_C007_Id
			left join C004 on C004_Id=T011_C004_Id
            where ifnull(T012_Quantidade_Falta_Lancar(T012_Id,T086_Id),0) >0 
            and T011_Flag_Cancelada!='S'
            AND D024_Id='{$r_D024_Id}'
            AND (T012_C004_Id='{$g['empresaAtual']}')
            AND T011_Status_Aprovacao=2
EOT;

//            order by substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))),floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20))
    
        /**
        * Filtro
        */
        $gridFiltro = array(
            'T011_Id' => array('titulo' => 'N° OC', 'tipo' => 'numero'),
            'T011_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T012_Codigo_Produto' => array('titulo' => 'Cód Produto', 'tipo' => 'numero'),
            'T012_Descricao_Produto' => array('titulo' => 'Descrição Produto', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        
        $sql_ = urlencode(base64_encode($extra));        
        echo <<<EOT
         <div class="buttonsBottomTop">
            <button type="button" id="est004GerarNF">Gerar NF Recebida</button>
         </div>
EOT;
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        
    
        /**
        * Botões (Versão Acima do GRID)
        */
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        echo <<<EOT
         <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
         </div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#est004GerarNF').unbind('click').bind('click', function(data) {
                $('#{$g['divId']}').showLoading();
                $.getJSON('/est/est004/grid_func-ajax/est004VerificaItens/?ajax=true&extra={$sql_}&callback=?', function(request) {
                    $('#{$g['divId']}').hideLoading();
                    if (request.code) {
                        var \$buttons = {
                            "Sim": function() {
                                $('#{$g['divId']}').showLoading();
                                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Gerar NF', '/est/est004/content/est004ContentCadastroNotaFiscal/', '&GerarNFOC=true&extra={$sql_}', [650,450]);
                                $('#{$g['divId']}').hideLoading();
                                $(this).dialog("destroy").remove();
                            },
                            "Não": function() { $(this).dialog("destroy").remove(); } 
                        };
                        dialogConfirm('Confirmação', request.data, \$buttons);
                    }else{
                        var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
                        dialogConfirm("Erro!", request.data, buttons)
                    }
                });
            });
        } </script></div>
EOT;
        }

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T011_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Ordem de Compra: ' + id, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,600]);
			});
		} </script></div>
EOT;

        /*
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         *
        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T012_Id-/, '');
            });
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
        } </script></div>
EOT;

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

    case 'srm001GridFornecedores':
		global $g;

		$filtroObrigatorio=true;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'CONCAT("<font style=\"font-weight:bold\">",D024_Id," - ",substr(D024_Nome_Empresa,1,20),"</font>","<br/><span style=\"font-size: 0.9em\">",D020_Nome_Cidade,"/",D018_UF," - ",D024_Cnpj,"</span>") as Empresa', 'colunaTitulo' => 'Razão Social', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			//array('colunaValor' => 'GROUP_CONCAT(DISTINCT C007_Primeiro_Nome SEPARATOR "<br />")', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%;'),
			array('colunaValor' => 'SUM(T013_Valor_Total)', 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero'),
			// T007_Valor_Total_Comissao
			// C007_Primeiro_Nome
		);

		/**
		* Definição da SQL
		*/
		$from = "T013 AS T013A";

		if ($g['perfilUsuario']=='26') {
			$extra = <<<EOT
				left join D024 on D024_Id=T013_D024_Id
				left join D018 on D024_D018_Id=D018_Id
				left join D020 on D024_D020_Id=D020_Id
				left join C007 on C007_Id=D024_C007_Vendedor_Interno
				left join C004 on C004_Id=T013_C004_Id
				left join D006 on D006_Id=T013_D006_Id
				where T013_Numero_Nota_Fiscal != ''
				and T013_Flag_Cancelada!='S'
				and C007_Id='{$g['usuarioAtual']}'
				GROUP BY D024_Id
				ORDER BY D024_Nome_Empresa
EOT;
		} else {
			$extra = <<<EOT
				left join D024 on D024_Id=T013_D024_Id
				left join D018 on D024_D018_Id=D018_Id
				left join D020 on D024_D020_Id=D020_Id
				left join C007 on C007_Id=D024_C007_Vendedor_Interno
				left join C004 on C004_Id=T013_C004_Id
				left join D006 on D006_Id=T013_D006_Id
				where T013_Numero_Nota_Fiscal != ''
				and T013_Flag_Cancelada!='S'
				GROUP BY D024_Id
				ORDER BY D024_Nome_Empresa
EOT;
			}

		/**
         * Botões Almir
         */
        $botoes = array(
			);
		echo Botoes::processar($botoes);


		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T013_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
			'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'text'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'text'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Usar mesmo extra na subquery
		$extraSub = explode('WHERE', $extra);
		$extraSub = $extraSub[1];
		$extraSub = explode('GROUP BY', $extraSub);
		$extraSub = $extraSub[0];
		$extraSub = preg_replace("#T013_Data_Emissao >= '.* 00:00:00' AND T013_Data_Emissao <= '.* 23:59:59'#", '1=1', $extraSub);

		// Loop para criar colunas com as datas
		$T013_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T013_Data_Emissao');
		if (!empty($T013_Data_Emissao[0]) && !empty($T013_Data_Emissao[1])) {
			$inicio = gCorrigeDataInverte($T013_Data_Emissao[0]);
			$fim = gCorrigeDataInverte($T013_Data_Emissao[1]);
			$data = strtotime($inicio);
			$fim = strtotime($fim);
			$dataInicial = $data;
			$dataFinal = $fim;
			
			while($data < $fim){
				if($data == $dataInicial){
					$data1 = $data;
				} else {
					$data1 = strtotime(date('Y-m-1', $data));
				}
				
				if(strtotime(date('Y-m-t', $data)) > $fim){
					$data2 = $fim;	
				} else {
					$data2 = strtotime(date('Y-m-t', $data));
				}		
				//echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";
				
				$data1str = date('Y-m-d', $data1);
				$data2str = date('Y-m-d', $data2);
						
				$subSql = <<<EOT
					IFNULL((
						SELECT 
							SUM(T013_Valor_Total) 
						FROM 
							T013 AS T013B 
						left join D024 on D024_Id=T013B.T013_D024_Id 
						left join D018 on D024_D018_Id=D018_Id 
						left join D020 on D024_D020_Id=D020_Id 
						left join C007 on C007_Id=D024_C007_Vendedor_Interno 
						left join C004 on C004_Id=T013B.T013_C004_Id 
						left join D006 on D006_Id=T013B.T013_D006_Id 
						WHERE 
							({$extraSub}) 
							AND T013B.T013_D024_Id = T013A.T013_D024_Id 
							AND T013B.T013_Data_Emissao >= '{$data1str} 00:00:00' 
							AND T013B.T013_Data_Emissao <= '{$data2str} 23:59:59'
							),0)
EOT;
				$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
				$data = strtotime("+1 month", $data);
			}
		}
		$extraTotal = gRetirarGroupBy($extra);
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Fornecedores<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

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
					//if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[1].replace('D024_Id-', '');
					// Ação: abrir nova janela
		    		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'SRM - Fornecedor', '/srm/srm001/content/srm001ContentPrincipalSRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

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

	case 'srm001GridContasPagar':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'T015_Flag_Cancelada', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T015_Id', $g['divId'])),
            array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
            array('colunaValor' => 'Extrato_Cliente', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'validaCampoExtratoCliente', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D024_Cnpj,D024_Cpf)', 'colunaTitulo' => 'CNPJ/CPF'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta) as D014_SubConta', 'colunaTitulo' => 'Conta'),
            array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'N.Dupli', 'callback' => 'gMostraMaisNotificacao', 'callbackParameter' => array('15', ',')),
            array('colunaValor' => 'concat(T015_Numero_Parcela,\'/\',T015_Total_Parcelas) as Parcelas', 'colunaTitulo' => 'Parc'),
            array('colunaValor' => 'T015_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao) as Prazo', 'colunaTitulo' => 'Prazo'),
            array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T015_Valor_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador'),
            array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo'),
            array('colunaValor' => 'T015_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'T015_T013_Id', 'colunaTitulo' => 'T013_Id'),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T015';

		$extra = <<<EOT
				left join D024 on D024_Id=T015_D024_Id
				left join D073 on D073_Id=T015_D073_Id
				left join D031 on D031_Id=T015_D031_Id
				left join D014 on D014_Id=T015_D014_Id
				LEFT JOIN D154 ON D154_Id=D014_D154_Id
				left join T007 on T007_Id=T015_T007_Id
				left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				where 
				(T015_C004_Id={$g['empresaAtual']})
                and (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T015_Data_Pagamento']!='0000-00-00'){
				return 'linhaCinza';
			} 
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
            'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'texto'),
            'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
            'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
            'T015_Valor_Duplicata,T015_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda'),
            'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda'),
            'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
            'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);
		
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
		 * Totalizadores
		*/
		$T015_Data_Vencimento = $g['ui']->getGridFiltroCampoBusca('T015_Data_Vencimento');
		$T015_Data_Vencimento0 = gCorrigeDataInverte($T015_Data_Vencimento[0]);
		$T015_Data_Vencimento1 = gCorrigeDataInverte($T015_Data_Vencimento[1]);
		$extraProjetado = ''; 
		if(!empty($T015_Data_Vencimento0) && empty($T015_Data_Vencimento1)){
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') >= '{$T015_Data_Vencimento0}'";
		} else if(empty($T015_Data_Vencimento0) && !empty($T015_Data_Vencimento1)) {
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') <= '{$T015_Data_Vencimento1}'";
		} else if(!empty($T015_Data_Vencimento0) && !empty($T015_Data_Vencimento1)) {
			$extraProjetado .= "WHERE STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') >= '{$T015_Data_Vencimento0}' AND STR_TO_DATE(CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento), '%Y-%m-%d') <= '{$T015_Data_Vencimento1}'";
		}
		if($extraProjetado != ''){
			$extraProjetado = gInsertExtraWhere($extraProjetado, "T018_C004_Id = '{$g['empresaAtual']}' AND T018_Valor_Saldo > 0 AND (T018_Ano > 0 AND T018_Mes > 0 AND D014_Dia_Vencimento > 0)");
		}
		$sqlProjetado = mysql_query("SELECT SUM(T018_Valor_Saldo) as Saldo FROM T018 LEFT JOIN D014 ON T018_D014_Id=D014_Id {$extraProjetado}");
		$projetado = mysql_fetch_array($sqlProjetado);
		if($extraProjetado == ''){ $projetado['Saldo'] = 0; }
		$valorProjetado = gCorrigeNumero($projetado['Saldo']);
		$extraProjetado = base64_encode($extraProjetado);
		if($projetado['Saldo'] > 0){
			$valorProjetadoAttr = <<<EOT
				style="background-color: var(--BGVermelho); color: white; cursor: pointer;"
				onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Projeção Orçamentária', '/fin/fin002/content/fin002ContentProjecoes/', '&extra=' + encodeURIComponent('{$extraProjetado}'), [800,500]);"
EOT;
		}
		$valorTotalPago= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T015_Valor_Pago)", $from, $extra));
		$valorTotalVencido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento<curdate(),T015_Valor_Saldo,0))", $from, $extra));
		$valorAVencer = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento>=curdate(),T015_Valor_Saldo,0))", $from, $extra));
		$valorTotalAPagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T015_Valor_Saldo)", $from, $extra) + $projetado['Saldo']);
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		/**
		* Botões (Versão Acima do GRID)
		*/
        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {

            echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="11" readonly></span>-->
				<span class="item" style="text-align:right">Pago<br><input type="text" class="whiteBg" id="inputPago" size="11" value="{$valorTotalPago}" readonly></span>
				<span class="item" style="text-align:right">Vencido<br><input type="text" class="whiteBg" id="inputVencido" value="{$valorTotalVencido}" size="11" readonly></span>
				<span class="item" style="text-align:right">A vencer<br><input type="text" class="whiteBg" id="inputAPagar" value="{$valorAVencer}" size="11" readonly></span>
				<span class="item" style="text-align:right">Projetado<br><input type="text" class="whiteBg" id="inputProjetado" size="11" value="{$valorProjetado}" readonly {$valorProjetadoAttr}></span>
				<span class="item" style="text-align:right">Total pagar<br><input type="text" class="whiteBg" id="inputTotalPagar" size="11" value="{$valorTotalAPagar}" readonly></span>
				<div id="fin002ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002ExtraBtns').data('extra', '{$extra__}');
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
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T015_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,500]);
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
