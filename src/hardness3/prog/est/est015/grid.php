<?php
namespace hardness;
/**
 * est - est015- Orçamentos
 */
// Classes
//require_once('bibliotecas/classes/est015.php');
//$est015 = new est015();
// Carrega funções callback
require_once('prog/est/est015/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est015/grid_func-js.php');
// Imagem para exclusão da linha
/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: est015TabPrincipal
	 */
	case 'est015GridPrincipalOrcamentos':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'campoCancelamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamento', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Grupo', 'colunaTitulo' => 'Grupo', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T218A_Hora_Emissao', 'colunaTitulo' => 'Hora', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T218_D024_Id', 'colunaTitulo' => 'Cod.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,28)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            //COM CELULAR array('colunaValor' => 'concat(CONVERT(substr(orcamento.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(orcamento.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(orcamento.D013_Telefone_1,\'\'),\' \',ifnull(orcamento.D013_DDD_Celular,\'\'),\'-\',ifnull(orcamento.D013_Celular,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;white-space:normal;min-width:190px;width:190px'),
            array('colunaValor' => 'concat(substr(Orcamento.D013_Nome_Contato,1,10), \' \',ifnull(Orcamento.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(Orcamento.D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;white-space:normal;min-width:190px;width:190px', 'callback' => 'utf8Encode'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'count(distinct T219_Id)', 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T218_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Valor_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T218_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'T218_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
            array('colunaValor' => 'if(T139_Id>0,\'Ok\',\'<font style="color: var(--BGVermelho)">--</font>\')', 'colunaTitulo' => 'Mail', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T218A_Data_Hora_Ultima_Resposta_Venda', 'colunaTitulo' => 'Ult.Resposta Venda', 'callback' => 'gCorrigeData','colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct concat(T224_Id," - ",ifnull(T224_Nome_Status,"")," - ",T224_Valor_Total) separator ", ") as Pedido', 'colunaTitulo' => 'O.C.', 'callback' => 'validaCampoPedido', 'style' => 'width:1%;max-width:170px;white-space:normal;min-width:170px;width:170px;word-wrap: break-word; overflow-wrap: break-word;'),
            array('colunaValor' => 'group_concat(distinct T013_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'width:1%', 'callback' => 'validaCampoNFCotacoes'),
            array('colunaValor' => 'T218_Data_Finalizado_Fornecedor', 'colunaTitulo' => 'Retorno link', 'callback' => 'gCorrigeData', 'style' => 'width:1%;color:blue'),
            array('colunaValor' => 'Comprador.C007_Primeiro_Nome as comprador', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left'),
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
            array('colunaValor' => "if(T224_Id is not NULL,T218_Valor_Total,0) as Valor_Total_Fechado", 'colunaTitulo' => 'Valor_Total_Aberto', 'colunaExibir' => false),
            array('colunaValor' => "if(T224_Id is NULL,T218_Valor_Total,0) as Valor_Total_Aberto", 'colunaTitulo' => 'Valor_Total_Fechado', 'colunaExibir' => false),
			array('colunaValor' => "if(T224_Id is not NULL,1,0) as Qte_Total_Fechado", 'colunaTitulo' => 'Qte_Total_Fechado', 'colunaExibir' => false, 'casasDecimais' => '0'),
            array('colunaValor' => "if(T224_Id is NULL,1,0) as Qte_Total_Aberto", 'colunaTitulo' => 'Qte_Total_Aberto', 'colunaExibir' => false, 'casasDecimais' => '0'),
			
			//array('colunaValor' => 'group_concat(distinct T006_T005_Id)', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
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
			left join C007 as Comprador on Comprador.C007_Id=T218_C007_Id_Vendedor_Interno
			left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
			left join T224 on T224_T218_Id=T218_Id
			left join T014 on T014_T225_Id = T225_Id
			left join T013 on T013_Id=T014_T013_Id
			left join D006 on D006_Id=T218_D006_Id
			left join D018 on D018_Id=D024_D018_Id
			left join T004 on T004_Id=T219_T004_Id
			left join T139 on T139_T218_Id=T218_Id
			left join T218A ON T218_Id = T218A_T218_Id
			where (T218_C004_Id='{$g['empresaAtual']}')
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
			group by T218_Id
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T218_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D013 on D013_Id=T218_D013_Id
		    left join C007 as Comprador on Comprador.C007_Id=T218_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
		    left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
			left join D006 on D006_Id=T218_D006_Id
		    where (T218_C004_Id='{$g['empresaAtual']}')
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalAberto = <<<EOT
			left join D024 on D024_Id=T218_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D013 on D013_Id=T218_D013_Id
		    left join C007 as Comprador on Comprador.C007_Id=T218_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
		    left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
			left join D006 on D006_Id=T218_D006_Id
		    where (T218_C004_Id='{$g['empresaAtual']}'
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
		    and T218_Flag_Perdido!='S'
		    and (select T224_Id from T224 where T224_T218_Id=T218_Id limit 1) is null)
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalFechado = <<<EOT
			left join D024 on D024_Id=T218_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D013 on D013_Id=T218_D013_Id
			left join T219 on T219_T218_Id=T218_Id
			left join T225 on T225_T219_Id=T219_Id
		    left join C007 as Comprador on Comprador.C007_Id=T218_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
		    left join T224 on T224_T218_Id=T218_Id
			left join D006 on D006_Id=T218_D006_Id
		    where (T218_C004_Id='{$g['empresaAtual']}'
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
		    and T225_Id > 0)
	        GROUP BY T218_Id
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalPerdido = <<<EOT
			left join D024 on D024_Id=T218_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D013 on D013_Id=T218_D013_Id
		    left join C007 as Comprador on Comprador.C007_Id=T218_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
		    left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
			left join D006 on D006_Id=T218_D006_Id
		    where (T218_C004_Id='{$g['empresaAtual']}'
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
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

		/*
	    echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="est015GridPrincipalOrcamentos">Incluir</button>
			<button type="button" id="est015ProcessarCotacaoVencida">Processar Vencidos</button>
		</div>
		*/


		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#xest015GridPrincipalOrcamentos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cotação de compra', '/est/est015/content/est015ContentOrcamento/', '', [650,500]);
				});
				$('#est015ProcessarCotacaoVencida').unbind('click').bind('click', function(data) {
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
		    'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
		    'T218_Grupo' => array('titulo' => 'Grupo', 'tipo' => 'numero'),
		    'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'numero'),
		    'T218_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		    'T218_Grupo' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
		    'Comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
		    'D006_Codigo_CFOP,D006_Descricao,D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
		    'T218_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
		    'T218_Flag_Status_Orcamento' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
		        array('title' => 'Todos', 'value' => ''),
		        array('title' => 'Finalizado', 'value' => 'F'),
		        array('title' => 'Pendente', 'value' => 'P'),
		        array('title' => 'Cancelado', 'value' => 'C'),
		        array('title' => 'Vencido', 'value' => 'V'),
		      ),
		      'naoGerarExtra' => ''),
		   	'T218_Flag_Perdido' => array('titulo' => 'Cancelado', 'tipo' => 'select', 'select' => array(
		        array('title' => 'TODAS', 'value' => ''),
		        array('title' => 'SIM', 'value' => 'S'),
		        array('title' => 'NÃO', 'value' => 'N'),
		      ),
		    ),
		    'D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
		    'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto', 'exibir' => false),
		    'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Totais(Versão Acima do GRID)
		*/
		//$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal, false, false, true);
		//$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
		//$extraTotalFechado = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalFechado, false, false, true);
		//$extraTotalPerdido = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalPerdido, false, false, true);
		//$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct T218_Id)", $from, $extraTotal);
		//$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T218_Valor_Total)", $from, $extraTotal));
		//$registrosAberto = $g['sqlAuto']->pegarSqlCampo("count(distinct T218_Id)", $from, $extraTotalAberto);
		//$valorTotalAberto = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T218_Valor_Total)", $from, $extraTotalAberto));
		//$registrosFechado = $g['sqlAuto']->pegarSqlCampo("count(distinct T224_Id)", $from, $extraTotalFechado, $extraTotalFechado);
		//$valorTotalFechado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T225_Valor_Total_Preco + T225_Valor_IPI)", $from, str_replace('GROUP BY T218_Id', '', $extraTotalFechado)));

		//$registrosPerdido = $g['sqlAuto']->pegarSqlCampo("count(distinct T218_Id)", $from, $extraTotalPerdido);
		//$valorTotalPerdido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T218_Valor_Total)", $from, str_replace('GROUP BY T218_Id', '', $extraTotalPerdido)));
		//T218_Flag_Perdido='S'
		//echo "Registros: <br />" . "count(*)" . $from . $extra . "<br />    Valor Total: <br />" . "sum(T218_Valor_Total)" . $from . $extra;
		/*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--span class="item" style="text-align:right">Cotações<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span-->
				<!--span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span-->
				<span class="item" style="text-align:right">Abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosAberto}" readonly></span>
				<span class="item" style="text-align:right">Valor abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalAberto}" readonly></span>
				<span class="item" style="text-align:right">Fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosFechado}" readonly></span>
				<span class="item" style="text-align:right">Valor fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalFechado}" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/

		 /**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Valor_Total', 'valor' => 'T218_Valor_Total', 'titulo' => 'Valor total'),
			array('nome' => 'Qte_Total_Fechado', 'valor' => "Qte_Total_Fechado", 'titulo' => 'Qte fechado', 'casasDecimais' => 0),
			array('nome' => 'Valor_Total_Fechado', 'valor' => "Valor_Total_Fechado", 'titulo' => 'Total fechado'),
			array('nome' => 'Qte_Total_Aberto', 'valor' => "Qte_Total_Aberto", 'titulo' => 'Qte aberto', 'casasDecimais' => 0),
			array('nome' => 'Valor_Total_Aberto', 'valor' => "Valor_Total_Aberto", 'titulo' => 'Total aberto'),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$botoes = array(
            array('titulo' => 'Incluir', 'id' => 'est015GridPrincipalOrcamentos', 'janelaUrl' => '/est/est015/content/est015ContentOrcamento/', 'janelaTamanho' => '[650,500]'),
			array('titulo' => 'Processar Vencidos', 'id' => 'est015ProcessarCotacaoVencida'),
        );
        echo Botoes::processar($botoes);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
// 		echo <<<EOT
// 		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// 			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
// 				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
// 				var id = $(this).closest('tr').attr('id').replace(/T218_Id-/, '');
// 				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
// 			});
// 		} </script></div>
// EOT;

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {            
            acaoClickLinha('{$g['divId']}', function(data, array) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Editar Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T218_Id']) + '&tabela={$from}', [990,605]);
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
	
	/**
	 * Grid Principal
	 * Chamado em: est015tab01
	 */
	case 'est015GridProdutos':

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'C');

		$T218_Id = (isset($r_T218_Id)) ? $r_T218_Id : false;
		
		/**
		* Definição das Tabelas
		*/
		$from = 'T219';
		$fromParent = 'T218_Id';
		$fromParentValor = $r_T218_Id;

		$T219 = mysql_query("select T219_Item,T219_Id from T219 where T219_T218_Id='{$T218_Id}' order by T219_Item,T219_Id");
		$item=1;
		while ($mT219=mysql_fetch_array($T219)){
			if ($mT219['T219_Item']!=$item){
			  	mysql_query("update T219 set T219_Item='$item' where T219_Id='{$mT219['T219_Id']}'");
			}
			$item++;
		}

		$sqlT218 = mysql_query("SELECT T218_Flag_Calcular_ST, T218_Flag_Calcular_IPI, T218_Flag_Calcular_ICMS FROM T218 WHERE T218_Id = '{$T218_Id}'");
		$T218 = mysql_fetch_array($sqlT218);

		$editarIPI  = ($T218['T218_Flag_Calcular_IPI'] == 'N')  ? true : false;
		$editarST   = ($T218['T218_Flag_Calcular_ST'] == 'N')   ? true : false;
		$editarICMS = ($T218['T218_Flag_Calcular_ICMS'] == 'N') ? true : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto', 'style' => 'width:1px'),
            array('colunaValor' => 'horizontal', 'colunaTipo' => 'livre', 'callback' => 'horizontal'),
            array('colunaValor' => 'excluirCheckbox', 'colunaTipo' => 'livre', 'callback' => 'checkboxExcluirItemOrcamento', 'colunaEditarTodos' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'T219_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T219_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'T004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Item'),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
            array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-size:12px', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referência', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Codigo_Substituto', 'colunaTitulo' => 'Cód.Forn', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'font-size:12px', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_Geral'),
            array('colunaValor' => 'T219_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-size:12px'),
            array('colunaValor' => 'SUBSTRING(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(D006_Codigo_CFOP," (",D006_Id,")") as D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T219_D006_Id_Info', 'Demonstrativo CFOP', 'T219_Id', 'left', '', '500')),
            array('colunaValor' => 'T219_D006_Id_Info', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T219_D005_Id_Info', 'Demonstrativo NCM', 'T219_Id', 'left', '', '500')),
            array('colunaValor' => 'T219_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => $destinoProdutos , 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload', 'colunaExibir'=>true),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Est.Tab', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T219_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'T219_T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'callback' => 'estoqueMP', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Quantidade_Confirmada', 'colunaTitulo' => 'Conf', 'colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Valor_Preco_Original', 'colunaTitulo' => 'Prc.Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Valor_Preco_Original_Bruto', 'colunaTitulo' => 'Original Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Percentual_Desconto_Bruto', 'colunaTitulo' => 'Desc % Bruto', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => '', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Valor_Preco_Bruto', 'colunaTitulo' => 'Preço Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'T219_Calculo_Valor_Unitario', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'O.C.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            //array('colunaValor' => 'T219_Flag_Comprar', 'colunaTitulo' => 'O.C.', 'callback' => 'validaCampoComprar', 'style' => 'width:1%', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
            array('colunaValor' => 'T219_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => $editarIPI, 'colunaEditarTodos' => $editarIPI, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => $editarICMS, 'colunaEditarTodos' => $editarICMS, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_Percentual_Reducao_ICMS', 'colunaTitulo' => '%Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => '(round(T219_Valor_ST,2)/(round(T219_Valor_Total_Preco,2)+round(T219_Valor_IPI,2)))*100', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T219A_Aliquota_FCP', 'colunaTitulo' => '%FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T219_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Observacao', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => 'DIFAL ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T219_Valor_ST_Digitado', 'colunaTitulo' => 'ST Forn R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => $editarST, 'colunaExibir' => false),
            array('colunaValor' => 'T219_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => $editarST, 'colunaEditarTodos' => $editarST, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),  
			array('colunaValor' => 'T219A_Valor_FCP', 'colunaTitulo' => 'FCP R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),            
			array('colunaValor' => 'T219A_Valor_FCP_ST', 'colunaTitulo' => 'FCP ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),            
            array('colunaValor' => '(T219_Valor_Frete+T219_Valor_Frete_Total)', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T219_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'style' => 'background-color: var(--celula-verde);'),           
            array('colunaValor' => 'T219_Valor_Preco_Venda', 'colunaTitulo' => 'Venda', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'Demonstrativo', 'right', 4, 'T00X')),
            //array('colunaValor' => 'T219_Valor_Preco_Venda', 'colunaTitulo' => 'Venda', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outros/demonstrativoPrecoVenda/', 'Demonstrativo', 'right', 4, 'T00X', '', '-1')),
            array('colunaValor' => 'IF(T006_Id>0,CONCAT("T006-",T006_Id),CONCAT("T004-",T004_Id)) as Demonstrativo', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'IF(T006_Id > 0, IF(T006_T219_Id > 0 AND T006_Valor_Preco_Cotacao > 0, 675, 450), IF(T004_T219_Id > 0 AND T004_Valor_Preco_Cotacao > 0, 675, 450)) as width', 'colunaTitulo' => 'width', 'colunaExibir' => false),
            array('colunaValor' => 'if(T003_Flag_Perdido=\'S\',\'<font style="color: var(--BGVermelho)">Cancelado</font>\',T219_Valor_Preco_Venda_Negociado)', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => 4, 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);'),
            array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'if(T003_Flag_Perdido=\'S\',0,T219_Valor_Preco_Venda_Negociado-(T219_Valor_Preco_Venda*0.96))', 'colunaTitulo' => 'Sobra', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);'),
            array('colunaValor' => 'concat(replace(replace(round(((T219_Valor_Preco_Venda/T219_Valor_Custo_Unitario)-1)*100),",",""),".",","),"","%")', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'concat(replace(replace(round(((T219_Valor_Preco_Venda_Negociado/T219_Valor_Custo_Unitario)-1)*100),",",""),".",","),"","%")', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tab', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo Médio', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Preco_Bruto*T219_Quantidade as T219_Valor_Total_Bruto', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D149_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Percentual_Frete', 'colunaTitulo' => '%Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Percentual_Financeiro', 'colunaTitulo' => '%Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T219_Percentual_Desconto'),
            array('colunaValor' => 'T219_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_Percentual_ST', 'colunaTitulo' => '% ST Manual', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar'=>true),
            array('colunaValor' => 'T219_ICMS_Inter_Estadual', 'colunaTitulo' => '%ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_ST_ICMS_Interno', 'colunaTitulo' => '%ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_Percentual_Reducao_ICMS_ST', 'colunaTitulo' => '% Red ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T219A_Percentual_Reducao_ICMS_IE_ST', 'colunaTitulo' => '% Red ICM IE ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background-color: var(--celula-amarelo);'),
            array('colunaValor' => 'T219A_Percentual_Reducao_ICMS_Int_ST', 'colunaTitulo' => '% Red ICM Int ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background-color: var(--celula-amarelo);'),
            array('colunaValor' => 'T219_Valor_Base_ST', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T219A_Valor_Base_FCP', 'colunaTitulo' => 'Base FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T219A_Valor_Base_FCP_ST', 'colunaTitulo' => 'Base FCP ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.Ped.', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T219_Item_Pedido_Compra', 'colunaTitulo' => 'Item.Ped.', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T219_Valor_Frete_Total', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'left', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'LinkProduto', 'colunaTitulo' => 'Link', 'colunaAlinhar' => 'center', 'colunaTipo' => 'livre', 'callback' => 'campoLinkProduto', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Flag_Excluir', 'colunaTitulo' => 'T219_Flag_Excluir', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'colunaAlinhar' => 'center', 'style' => 'background-color: var(--celula-vermelho);', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
			array('colunaValor' => 'T219A_Data_Hora_Responta_Venda', 'colunaTitulo' => 'Resposta Venda', 'callback' => 'gCorrigeData', 'colunaExibir' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'background-color: var(--celula-azul);', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T006_T005_Id', '990,605')),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'background-color: var(--celula-azul);'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'background-color: var(--celula-azul);'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'titulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'Empresa', 'colunaAlinhar' => 'center'),
        );

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T219_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T219_D037_Id
			LEFT JOIN D005 on D005_Id=T219_D005_Id
			left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
			LEFT JOIN D006 on D006_Id=T219_D006_Id
			LEFT JOIN T225 on T225_T219_Id=T219_Id
			LEFT JOIN T004 on T219_T004_Id=T004_Id
			LEFT JOIN T006 on T219_T006_Id=T006_Id
			LEFT JOIN T003 on T003_Id=T004_T003_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=IFNULL(T003_D024_Id,T005_D024_Id)
			LEFT JOIN C007 on C007_Id=T003_C007_Id_Vendedor_Interno OR C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN T219A on T219A_T219_Id=T219_Id
			WHERE T219_T218_Id = '[[r_acaoId]]'
			GROUP BY T219_Id
EOT;
		
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['D001_Flag_Pre_Cadastro'] == 'S'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T225_T224_Id'] > 0){
				return 'linhaCinza';
			}
		}

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function colunaEditarCallbackJSReload() {
				divRefresh('{$r_divReload}');
			}
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T219_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/est/est015/content/est015ContentEditarProduto/', '&acaoId=' + encodeURIComponent(acaoId) + '&T218_Id=' + encodeURIComponent('{$T218_Id}') + '&tabela=T219', [600,400]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});

			// Função JS executada como callback do editar campo (colunaEditarCallbackJS)
			function T219_Calculo_Valor_Unitario(T219_Id, campoEditado, valorAntigo, novoValor) {
				T219_Id = T219_Id.replace(/T219_Id-/, '');
					if(campoEditado == 'T219_Valor_Preco_Bruto'){
						$.getJSON('/est/est015/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T219_Id='    + encodeURIComponent(T219_Id) +
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T219_Id, 'T219_Id'); // grid
								divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columnsOrcamento:eq(1)').attr('id'), true, false, 2); // totais
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
			}

			function colunaEditarCallbackJS_Geral(T219_Id, campoEditado, valorAntigo, novoValor) {
					T219_Id = T219_Id.replace(/T219_Id-/, '');
	                divRefresh('{$g['divId']}', true, false, false, T219_Id, 'T219_Id'); // grid
			}

			function colunaEditarCallbackJS_T219_Percentual_Desconto(T219_Id, campoEditado, valorAntigo, novoValor) {
					T219_Id = T219_Id.replace(/T219_Id-/, '');
					if(campoEditado == 'T219_Percentual_Desconto'){
						$.getJSON('/est/est015/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T219_Id='    + encodeURIComponent(T219_Id) +
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +
																					      '&callback=?', function(request) {
							if (request.code) {
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T219_Quantidade'){
					}else if(campoEditado == 'T219_Quantidade_Confirmada'){
						$.getJSON('/est/est015/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T219_Id='    + encodeURIComponent(T219_Id) +
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +
																					      '&callback=?', function(request) {
							if (request.code) {
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T219_Valor_Preco_Unitario'){
						$.getJSON('/est/est015/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T219_Id='    + encodeURIComponent(T219_Id) +
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +
																					      '&callback=?', function(request) {
							if (request.code) {
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}else if(campoEditado == 'T219_Percentual_Desconto_Bruto'){
						$.getJSON('/est/est015/grid_func-ajax/aplicarPercentualDesconto/?ajax=true&T219_Id='    + encodeURIComponent(T219_Id) +
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +
																					      '&callback=?', function(request) {
							if (request.code) {
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
					divRefresh('{$g['divId']}', true, false, false, T219_Id, 'T219_Id'); // grid
					divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columnsOrcamento:eq(1)').attr('id'), true, false, 2); // totais
				}
				function colunaEditarCallbackJS_T219_Item(T219_Id, campoEditado, valorAntigo, novoValor) {
					T219_Id = T219_Id.replace(/T219_Id-/, '');
					$.getJSON('/est/est015/grid_func-ajax/processarNumeroItemCotacao/?ajax=true&T219_Id='    + encodeURIComponent(T219_Id) + 
																				      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				      '&novoValor='			+ encodeURIComponent(novoValor) +  
																				      '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}', true, false, false, T219_Id, 'T219_Id'); // grid
							divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columnsOrcamento:eq(1)').attr('id'), true, false, 2); // totais
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				}
				/*
				function gridTheadCallback() {
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
						.removeAttr('onclick')
						.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est015/grid_func-ajax/est015MarcarTodosExcluir/?ajax=true&T218_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
										divRefresh('{$r_divReload}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est015/grid_func-ajax/est015DesmarcarTodosExcluir/?ajax=true&T218_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
										divRefresh('{$r_divReload}');
									});
									$(this).dialog("destroy").remove();
								} ,
								"Cancelar": function() { $(this).dialog("destroy").remove(); }
							};
							dialogConfirm('Marcar/Desmarcar Todos', 'Você deseja marcar ou desmarcar todos? ', \$buttons);
							return false;
					});
				}
				*/
		} </script></div>
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
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

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
		* Botões (Versão Acima do GRID)
		*/
		//$extra_ = urlencode(base64_encode($extra));
		$sql_ = urlencode(base64_encode($geradoSql));
		if (!personalizacaoBotoes()) {
			echo <<<EOT
				<div class="buttonsBottomTop">
					<!--<button type="button" id="est015GerarOC">Gerar O.C.</button>-->
					<!--<button type="button" id="est015AtualizarCalculoEstoque">Atualizar Estoque</button>-->
				</div>
EOT;
		}

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
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est015/grid_func-ajax/est015GerarOC/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
								if (request.code) {
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
					dialogConfirm('Gerar Ordens de compra', 'Gostaria de gerar as O.C.´s? ', \$buttons);
				});
			} </script></div>
EOT;
        }

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

	case 'est015GridTrocas':
		$T218_Id = (isset($r_T218_Id)) ? $r_T218_Id : false;
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T222_Id', $g['divId'])),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T222_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T222_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true,  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'T222_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true,  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
			//array('colunaValor' => 'T222_Operacao', 'colunaTitulo' => 'Operação', 'callback'=>'retornaOperacaoTroca'),						
			array('colunaValor' => 'T222_Operacao', 'colunaTitulo' => 'Operação', 'colunaEditar' => true,'colunaEditarGeraSelect' => array(array('title' => 'PARTE PAGAMENTO', 'value' => 'P', 'selected' => true),array('title' => 'TROCA', 'value' => 'T')), 'callback' => 'retornaOperacaoTroca'),
			array('colunaValor' => 'T222_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);
		
		
								

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T222';

		$extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T222_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
			WHERE T222_T218_Id = '{$r_acaoId}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		/*
		function gridLinha($array) {
			if($array['todosCampos']['T225_T224_Id']>0){
				return 'linhaCinza';
			}
		}
		*/

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
			//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {			
			function colunaEditarCallbackJS_Grid_Refresh() {
				divRefresh('{$g['divId']}');
			}
		} </script></div>
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		
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
	
	case 'est015GridServicos':
			
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'excluirServico'),
			array('colunaValor' => 'geraImagem', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
			array('colunaValor' => 'T223_Id', 'colunaTitulo' => 'OS', 'chavePrimaria' => true, 'colunaExibir'=>true),
			// array('colunaValor' => 'T223_Quantidade_Confirmada', 'colunaTitulo' => 'Qtd Confirmada', 'colunaEditar' => true),
			// array('colunaValor' => 'T223_Quantidade', 'colunaTitulo' => 'Qtde'),
			//array('colunaValor' => 'T223_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),			
			array('colunaValor' => 'T223_Valor_Unitario', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true),			
			array('colunaValor' => "concat(T223_Titulo, '<br />', T223_Descricao) as Servico", 'colunaTitulo' => 'Serviço'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			//array('colunaValor' => 'T223_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'est015_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
			array('colunaValor' => 'T223_Tempo_Estimado', 'colunaTitulo' => 'Tempo'),			
			array('colunaValor' => 'T223_D110_Id', 'colunaTitulo'=>'Competência', 'colunaExibir'=>false),
			array('colunaValor' => 'T223_D112_Id', 'colunaExibir'=>false),				
			//array('colunaValor' => 'D110_Titulo', 'colunaTitulo'=>'Competência'),
			array('colunaValor' => 'T223_D116_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'statusServicos', 'colunaTitulo' => 'Status Serviços', 'callback'=>'validaCampoStatusServicos', 'colunaTipo'=>'livre'),
			// array('colunaValor' => 'T223_Hora_Inicio', 'colunaTitulo' => 'Início', 'callback'=>'gCorrigeData'),
			// array('colunaValor' => 'T223_Hora_Fim', 'colunaTitulo' => 'Fim', 'callback'=>'gCorrigeData'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T223';

		$extra = <<<EOT
			LEFT JOIN T219 ON T219_Id=T223_T219_Id
			LEFT JOIN D009 ON D009_Id=T219_D009_Id
			LEFT JOIN D049 ON D009_D049_Id=D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			WHERE T223_T218_Id='{$acaoId}'
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T223_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
		
		case 'est015grid05Processos':
			
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T146_Id', $g['divId'])),
			array('colunaValor' => 'T146_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor' => 'T146_T223_Id', 'colunaTitulo' => 'IdServico', 'colunaExibir' => false),
			array('colunaValor' => 'T146_D110_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
			array('colunaValor' => 'T146_Titulo', 'colunaTitulo' => 'Título', 'colunaEditar'=>true),
			array('colunaValor' => 'T146_Descricao', 'colunaTitulo' => 'Descrição', 'colunaEditar'=>true),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T146';

			$extra = <<<EOT
				where 
				T146_T223_Id = {$r_T223_Id}
EOT;

			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			/* retirada a ação q faz o registro do grid ser carregado no form acima
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T146_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
			} </script></div>
EOT;
			*/
			
			/**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);
			

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

			$novoIdTelaForm = uniqid();
		break;	
		
		case 'est015GridMateriais':
			
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T152_Id', $g['divId'])),
				array('colunaValor' => 'T152_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T152_D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),				
				array('colunaValor' => 'T152_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditar'=>true),
				array('colunaValor' => 'T152_D037_Id', 'colunaTitulo' => 'Unidade', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true), 'callback' => 'buscaUnidade'),
				array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade','colunaExibir'=>false),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
				array('colunaValor' => 'D082_Marca', 'colunaTitulo'=>'Marca'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T152';

			$extra = <<<EOT
				left join D037 on D037_Id=T152_D037_Id
				left join D009 on D009_Id=T152_D009_Id
				LEFT JOIN D049 on D049_Id=D009_D049_Id
				LEFT JOIN D001 on D001_Id=D049_D001_Id
				LEFT JOIN D082 on D082_Id=D049_D082_Id				
				where 
				T152_T223_Id = {$r_T223_Id}
EOT;

			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			/* retirada ação que carrega o registro selecionado no form acima
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T152_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
			} </script></div>
EOT;
			*/
			
			/**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);
			

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

			$novoIdTelaForm = uniqid();
		break;
		
		case 'est015GridPagamentos':
			global $g;
			$sqlT218 = "SELECT T218_Pgto_Data, T218_Pgto_C007_Id, C007_Login FROM T218 LEFT JOIN C007 ON C007_Id=T218_Pgto_C007_Id WHERE T218_Id='$r_acaoId'";
			$resultT218 = mysql_query($sqlT218);
			$linhaT218 = mysql_fetch_array($resultT218);
			$dataGerada = gCorrigeData($linhaT218['T218_Pgto_Data']);
			$user = strtoupper($linhaT218['C007_Login']);
			if(($user != "") || (!empty($user))){
				echo "Parcelas Geradas por <b>".$user."</b> em <b>".$dataGerada."</b>";
			}
			
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T220_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T220_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T220_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T220_Prazos_Dias"),
				array('colunaValor' => 'T220_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaAlinhar' => 'center', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T220_Prazos_Dias"),
				array('colunaValor' => 'est015_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "est015_Dia_Semana"),
                array('colunaValor' => 'T220_Taxa_Juros', 'colunaTitulo' => 'Taxa Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2','colunaExibir' => false),
                array('colunaValor' => 'T220_Valor_Juros', 'colunaTitulo' => 'Valor Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2','colunaExibir' => false),
                array('colunaValor' => 'T220_Valor_Parcela_Original', 'colunaTitulo' => 'Valor Original', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2','colunaExibir' => false),
                array('colunaValor' => 'T220_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T220_Prazos_Dias"),
				array('colunaValor' => 'T220_D031_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true,'colunaEditarJSCallback' => "colunaEditarCallbackJS_T220_Prazos_Dias", 'colunaEditarGeraSelect' => array('D031', 'D031_Id', 'D031_Portador', 'ORDER BY D031_Portador', true), 'callback' => 'T220_D031_Id_Portador'),
				array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				//array('colunaValor' => 'est015_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "est015_Detalhes"),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T220';

			$extra = <<<EOT
				left join D031 on D031_Id=T220_D031_Id
				WHERE T220_T218_Id = {$r_acaoId}
EOT;
			/**
			 * Botões Almir
			 */
			$botoes = array(
			);
			echo Botoes::processar($botoes);

			/**
			* Filtro
			
			// Gera o filtro e acrescenta ao $extra
			$gridFiltro = array(
				
			);
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
            */
			
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T220_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T220_Prazos_Dias(T220_Id, campoEditado, valorAntigo, novoValor) {
					T220_Id = T220_Id.replace(/T220_Id-/, '');
					if (campoEditado == 'T220_Prazos_Dias') {
						$.getJSON('/est/est015/grid_func-ajax/atualizarParcelas/?ajax=true&T220_Id='      		+ encodeURIComponent(T220_Id) + 
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
					} else if (campoEditado == 'T220_Data_Vencimento') {
						$.getJSON('/est/est015/grid_func-ajax/atualizarParcelas/?ajax=true&T220_Id='      		+ encodeURIComponent(T220_Id) + 
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
					} else if (campoEditado == 'T220_Valor_Parcela') {
						$.getJSON('/est/est015/grid_func-ajax/atualizarParcelas/?ajax=true&T220_Id='      		+ encodeURIComponent(T220_Id) + 
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
					} else if (campoEditado == 'T220_D031_Id') {
						divRefresh('{$g['divId']}');						
					}
				}
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

			$novoIdTelaForm = uniqid();
		break;
		
		case 'est015GridEmail':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			// Chave Primária
			array('colunaValor' => 'T139_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T139_Data_Hora', 'colunaTitulo' => 'Data/Hora', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T139_Pasta', 'colunaTitulo' => 'Pasta'),
			array('colunaValor' => 'T139_Titulo', 'colunaTitulo' => 'Assunto'),
			array('colunaValor' => 'T139_De', 'colunaTitulo' => 'De'),
			array('colunaValor' => 'T139_Para', 'colunaTitulo' => 'Para'),
			array('colunaValor' => 'T139_Numero_Registros', 'colunaTitulo' => 'Ítens'),
			array('colunaValor' => 'T139_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T139';
		$extra = <<<EOT
			where T139_T218_Id='{$r_acaoId}'
EOT;

/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc006EnviarEmail2">Enviar Email</button>
				<button type="button" id="etc006VerificarEmailsEnviadosest015">Verificar Novos Emails</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006EnviarEmail2').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T218_Id=' + encodeURIComponent('$r_acaoId'), [900,550]);
				});
				$('#etc006VerificarEmailsEnviadosest015').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/etc/etc006/grid_func-ajax/atualizarEmails/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						var buttons = {
							"Ok": function() {
								$(this).dialog("destroy").remove(); 
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								}
							}
						}
						dialogConfirm('Atualizar Emails', request.data, buttons);
					});
				});
			} </script></div>
EOT;
	
			/**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($grid);
	
			/**
			 * Define ação do click de abertura (ao clicar em uma linha do grid)
			 */
			echo <<<EOT
			<script type="text/javascript">
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace('T139_Id-', '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Email', '/etc/etc006/content/etc006ContentEmail/', '&pasta=S&acaoId=' + encodeURIComponent(id), [900,550]);
				});
			</script>
			
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
		
		case 'est015GridPrincipalOrcamentosCancelados':
			global $g;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'count(distinct T218_Id) as Total', 'colunaTitulo' => 'Orçamentos'),
			array('colunaValor' => 'D047_Descricao_Motivo', 'colunaTitulo' => 'Motivo'),
			array('colunaValor' => 'sum(T218_Valor_Total) as Valor_Total', 'colunaTitulo' => 'Valor Total', 'callback'=>'gCorrigeNumero'),
		);
		/**
		* Definição da SQL
		*/
		$from = 'T218';
		
		$extra = <<<EOT
		left join D024 on D024_Id=T218_D024_Id
		left join D013 on D013_Id=T218_D013_Id
	    left join D047 on D047_Id=T218_D047_Id
	    left join C007 as Vendedor on Vendedor.C007_Id=T218_C007_Id_Vendedor_Interno
	    left join C007 as Liberou on Liberou.C007_Id=T218_Liberou_Alcada_C007_Id
	    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	    where T218_C004_Id='{$g['empresaAtual']}'
	    and T218_Flag_Perdido='S'
	    group by T218_D047_Id
		
EOT;
		
		// /**
		// * Botões (Versão Acima do GRID)
		// */
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="cad046FormCadastroMotivosCanecelamento">Incluir</button>
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#cad046FormCadastroMotivosCanecelamento').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Motivos Cancelamento do Orçamento', '/cad/cad046/content/cad046ContentMotivosCancelamentoIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			//'D047_Descricao_Motivo' => array('titulo' => 'Motivo', 'tipo' => 'texto'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'T218_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T218_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		// echo <<<EOT
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// var dbclickPrevent = false;
				//$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					//if (dbclickPrevent) { return false; }
					//dbclickPrevent = true;
					//setTimeout(function() { dbclickPrevent = false; }, 500);
					// // (substituir pelo mesmo ID da chave primária do grid)
					// var chavePrimaria = 'D047_Id';	
					// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// var id = $(this).closest('tr').attr('id').split('|');
					// var acaoId = id[0].replace(chavePrimaria + '-', '');
					// // Ação: abrir nova janela
	        		// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Motivos Cancelamento do Orçamento - ID: ' + acaoId, '/cad/cad046/content/cad046ContentMotivosCancelamentoIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
		        // });
				// $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			// } </script></div>
// EOT;

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
	
	case 'est015GridDocumentos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T221_Id', $g['divId'])),
			array('colunaValor' => 'T221_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T221_T218_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
			array('colunaValor' => 'T221_Nome_Arquivo', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'T221_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T221_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T221';

			$extra = <<<EOT
				left join C007 on C007_Id=T221_C007_Id
				where 
				(T221_T218_Id = '{$r_acaoId}')
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
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T221_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
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

			$novoIdTelaForm = uniqid();
		break;
        
        case 'est015GridAgregados':
            
            
            /**
            * Definição do Grid (Tabela)
            */
            $grid = array(
            array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T202_Id', $g['divId'])),
            array('colunaValor' => 'T202_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
            array('colunaValor' => 'T202_Descricao', 'colunaTitulo' => 'Descrição','colunaEditar' => true),
            array('colunaValor' => 'T202_Observacao', 'colunaTitulo' => 'Observação','colunaEditar' => true),
            array('colunaValor' => 'T202_Valor_Unitario', 'colunaTitulo' => 'Valor','colunaCasasDecimais' => '2','colunaEditar' => true),
            );

            /**
            *
            * Definição da SQL
            */
            $from = 'T202';

            $extra = <<<EOT
                where 
                T202_T219_Id = {$r_acaoId}
EOT;

            
            //Define ação do click de abertura (ao clicar em uma linha do grid)

            $novoIdTelaForm = uniqid();
            echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
               // $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                   // var id = $(this).closest('tr').attr('id').replace(/T202_Id-/, '');
                    //divSetarAcaoId('{$r_divReload}', id);
                   // divRefresh('{$r_divReload}');
                //});
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;
            
            
            /**
            * Filtro
            */
            // Gera o filtro e acrescenta ao $extra
            $extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
            // Imprime o filtro do GRID (normalmente executado acima do GRID)
            //echo $g['ui']->gridFiltroPrint($grid);
            

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

            $novoIdTelaForm = uniqid();
    break;
        
    case 'est015GridPrincipalProdutos':

		$filtroObrigatorio = true;

    	$gridModelos = array(
    		'COT' => 'POR COTAÇÃO',
    		'FOR' => 'POR FORNECEDOR'
    	);

        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
        
        if (modoModelo('COT')) {
        	$grid = array(
	            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto', 'style' => 'width:1px'),
	            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T218_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
	            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cód', 'colunaExibir' => false),
	            array('colunaValor' => 'concat(substr(D024_Nome_Empresa,1,20),\'-\',D018_UF)', 'colunaTitulo' => 'Fornecedor', 'style' => 'background:#FCF592', 'colunaExibir' => false),
	            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	            array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'Produto', 'style' => ' width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	            array('colunaValor' => 'T219_Descricao_Produto', 'colunaTitulo' => 'Descrição produto', 'style' => 'width:300px;max-width:300px;min-width:300px;white-space:normal;background:#FCF592'),
	            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
	            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Quant', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:#FCF592'),
	            array('colunaValor' => 'T219_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Observacao_Entrega', 'colunaTitulo' => 'Entrega', 'colunaAlinhar' => 'left'),
	            array('colunaValor' => 'T219_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);width:1%'),
	            array('colunaValor' => 'T219_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
	            array('colunaValor' => 'T219_Valor_Preco_Venda', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
	            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Médio', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
         	    array('colunaValor' => 'T219_Valor_ST_Digitado', 'colunaTitulo' => 'ST Forn R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
				array('colunaValor' => 'T219_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'Comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
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
				array('colunaValor' => 'T219_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
				array('colunaValor' => 'D006_Flag_Somar_DIFAL_Custo', 'colunaTitulo' => 'Calcular DIFAL', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
				array('colunaValor' => 'T219_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'concat(T218_Id,\' - \',D024_Nome_Empresa,\'-\',D018_UF,\' (\',D024_Id,\')\') as cotacao', 'colunaTitulo' => 'cotacao', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
				array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido venda'),
				array('colunaValor' => 'if(T225_Id is not null,1,0) as Itens_Comprado', 'colunaTitulo' => 'Itens_Comprado', 'colunaExibir' => false),
				array('colunaValor' => 'if(T225_Id is not null,T225_Quantidade,0) as Qtd_Comprado', 'colunaTitulo' => 'Qtd_Comprado', 'colunaExibir' => false),
				array('colunaValor' => 'if(T225_Id is not null,T225_Valor_Total_Preco_Sem_Desconto,0) as Total_Comprado', 'colunaTitulo' => 'Total_Comprado', 'colunaExibir' => false),
			);
        } else if (modoModelo('FOR')) {
        	$grid = array(
	            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto', 'style' => 'width:1px'),
	            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T218_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
	            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cód', 'colunaExibir' => false),
	            array('colunaValor' => 'concat(substr(D024_Nome_Empresa,1,20),\'-\',D018_UF)', 'colunaTitulo' => 'Fornecedor', 'style' => 'background:#FCF592', 'colunaExibir' => false),
	            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir' => false),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	            array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'Produto', 'style' => ' width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	            array('colunaValor' => 'T219_Descricao_Produto', 'colunaTitulo' => 'Descrição produto', 'style' => 'width:300px;max-width:300px;min-width:300px;white-space:normal;background:#FCF592'),
	            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
	            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Quant', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:#FCF592'),
	            array('colunaValor' => 'T219_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Observacao_Entrega', 'colunaTitulo' => 'Entrega', 'colunaAlinhar' => 'left'),
	            array('colunaValor' => 'T219_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);width:1%'),
	            array('colunaValor' => 'T219_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
	            array('colunaValor' => 'T219_Valor_Preco_Venda', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
	            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Médio', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
        	    array('colunaValor' => 'T219_Valor_ST_Digitado', 'colunaTitulo' => 'ST Forn R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
				array('colunaValor' => 'T219_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T219_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'Comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
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
				array('colunaValor' => 'T219_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
				array('colunaValor' => 'D006_Flag_Somar_DIFAL_Custo', 'colunaTitulo' => 'Calcular DIFAL', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
	            array('colunaValor' => 'T219_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'concat(D024_Id,\' - \',D024_Nome_Empresa,\'-\',D024_Cnpj,\'-\',D018_UF) as fornecedor', 'colunaTitulo' => 'fornecedor', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
				array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido venda'),
				array('colunaValor' => 'if(T225_Id is not null,1,0) as Itens_Comprado', 'colunaTitulo' => 'Itens_Comprado', 'colunaExibir' => false),
				array('colunaValor' => 'if(T225_Id is not null,T225_Quantidade,0) as Qtd_Comprado', 'colunaTitulo' => 'Qtd_Comprado', 'colunaExibir' => false),
				array('colunaValor' => 'if(T225_Id is not null,T225_Valor_Total_Preco_Sem_Desconto,0) as Total_Comprado', 'colunaTitulo' => 'Total_Comprado', 'colunaExibir' => false),
	        );
        } else {
			$grid = array(
	            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto', 'style' => 'width:1px'),
	        	array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
	        	array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
	        	array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
	        	array('colunaValor' => 'T218_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
	        	array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	        	array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cód'),
	        	array('colunaValor' => 'concat(substr(D024_Nome_Empresa,1,20),\'-\',D018_UF)', 'colunaTitulo' => 'Fornecedor', 'style' => 'background:#FCF592'),
	        	array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaExibir' => false),
	        	array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	        	array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'Produto', 'colunaExibir' => false),
	        	array('colunaValor' => 'T219_Descricao_Produto', 'colunaTitulo' => 'Descrição produto', 'style' => 'max-width:300px;min-width:300px;white-space:normal;', 'colunaExibir' => false),
	        	array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
	        	array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        	array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        	array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Quant', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:#FCF592'),
	        	array('colunaValor' => 'T219_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right'),
	        	array('colunaValor' => 'T219_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	        	array('colunaValor' => 'T219_Observacao_Entrega', 'colunaTitulo' => 'Entrega', 'colunaAlinhar' => 'center', 'style' => 'color: var(--BGVermelho)'),
	        	array('colunaValor' => 'T219_Flag_Comprar', 'colunaTitulo' => '', 'callback' => 'validaCampoComprar', 'style' => 'width:1%', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        	array('colunaValor' => 'T219_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);width:1%'),
	        	array('colunaValor' => 'T219_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
	        	array('colunaValor' => 'T219_Valor_Preco_Venda', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
	        	array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	        	array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Médio', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	        	array('colunaValor' => 'T219_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	        	array('colunaValor' => 'T219_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	        	array('colunaValor' => 'T219_Valor_ST_Digitado', 'colunaTitulo' => 'ST Forn R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
				array('colunaValor' => 'T219_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	        	array('colunaValor' => 'T219_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
	        	array('colunaValor' => 'T219_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
	        	array('colunaValor' => 'Comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
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
				array('colunaValor' => 'T219_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background:#F9C5C5'),
				array('colunaValor' => 'D006_Flag_Somar_DIFAL_Custo', 'colunaTitulo' => 'Calcular DIFAL', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
	        	array('colunaValor' => 'T219_Flag_Cancelado', 'colunaTitulo' => 'Cancelado', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        	array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'concat(T219_Codigo_Produto,\' \',ifnull(D049_Modelo,\'\'),\' - \',T219_Descricao_Produto) as produto', 'colunaTitulo' => 'Produto', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
				array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido venda'),
				array('colunaValor' => 'if(T225_Id is not null,1,0) as Itens_Comprado', 'colunaTitulo' => 'Itens_Comprado', 'colunaExibir' => false),
				array('colunaValor' => 'if(T225_Id is not null,T225_Quantidade,0) as Qtd_Comprado', 'colunaTitulo' => 'Qtd_Comprado', 'colunaExibir' => false),
				array('colunaValor' => 'if(T225_Id is not null,T225_Valor_Total_Preco_Sem_Desconto,0) as Total_Comprado', 'colunaTitulo' => 'Total_Comprado', 'colunaExibir' => false),
	        );
		}
        
        /**
         * Definição da SQL
         */
        $from = "T219";

       
        $extra = <<<EOT
                    left join D009 on D009_Id=T219_D009_Id
                    left join D049 on D049_Id=D009_D049_Id
                    left join D001 on D001_Id=D049_D001_Id
                    left join D082 on D082_Id=D049_D082_Id
                    left join D006 on D006_Id=T219_D006_Id
                    left join D037 on D037_Id=T219_D037_Id
					left join T218 on T218_Id=T219_T218_Id
					LEFT JOIN T006 ON T006_Id=T219_T006_Id
					LEFT JOIN T005 ON T005_Id=T006_T005_Id
                    left join T225 on T225_T219_Id=T219_Id
                    left join D024 on D024_Id=T218_D024_Id
					left join D018 on D018_Id=D024_D018_Id
                    left join C007 on C007_Id=T218_C007_Id_Vendedor_Interno
                    left join C007 as Comprador on Comprador.C007_Id=T218_C007_Id_Vendedor_Interno
					left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
					LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
                    left join D021 on D021_Id=D024_D021_Id
                    left join C004 on C004_Id=T218_C004_Id
                    left join D116 on D116_Id=T218_D116_Id
                    where (T218_C004_Id='{$g['empresaAtual']}')
					and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
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
			);
		echo Botoes::processar($botoes);
        
       	//Gera o filtro e acrescenta ao $extra
       	/**
        * Filtro
        */
		$gridFiltro = array(
			'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'numero'),
			'T218_Grupo' => array('titulo' => 'Grupo', 'tipo' => 'numero'),
			'T218_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'T219_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'T219_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D006_Descricao,D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'Comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'T219_Flag_Cancelado' => array('titulo' => 'Cancelado(S/N)', 'tipo' => 'texto'),
			'T225_T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'numero'),
			'T219_Valor_Preco_Sem_Desconto_Unitario' => array('titulo' => 'Preço', 'tipo' => 'numero'),
			'Produto' => array('titulo' => 'Produto', 'campos' => array(
				array('title' => 'produto', 'campoAgrupar' => 'produto', 'campoMostrar' => 'produto', 'campoOrdem' => '', 'campoSelecionado' => true),
			), 'tipo' => 'agrupar', 'exibir' => false),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'T005_Id' => array('titulo' => 'Pedido venda', 'tipo' => 'texto'),
			'D006_Flag_Somar_DIFAL_Custo' => array('titulo' => 'Calcular DIFAL', 'tipo' => 'texto'),
			'T219_Aliquota_ICMS_Diferenca_Aliquotas' => array('titulo' => '% DIFAL', 'tipo' => 'numero'),
		);
       	
       	if (modoModelo('COT')) {
       		unset($gridFiltro['Produto']);
       		$gridFiltro['Cotacao'] = array('titulo' => 'Cotacao', 'campos' => array(
                array('title' => 'cotacao', 'campoAgrupar' => 'cotacao', 'campoMostrar' =>'cotacao', 'campoOrdem' => '', 'campoSelecionado' => true),
            ), 'tipo' => 'agrupar', 'exibir' => false);
       	} else if (modoModelo('FOR')) {
       		unset($gridFiltro['Produto']);
       		$gridFiltro['Fornecedor'] = array('titulo' => 'Fornecedor', 'campos' => array(
                array('title' => 'fornecedor', 'campoAgrupar' => 'fornecedor', 'campoMostrar' =>'fornecedor', 'campoOrdem' => '', 'campoSelecionado' => true),
            ), 'tipo' => 'agrupar', 'exibir' => false);
       	}

        
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

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
                <!--span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span-->
                <!--span class="item" style="text-align:right">Qtd total cotados<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span-->
                <!--span class="item" style="text-align:right">Valor total cotados<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span-->
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

    case 'est015GridProdutosDividir':
        /**
            * Definição do Grid (Tabela)
            */
            
        //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Produtos</div>';
        $grid = array(
            //array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'excluirProduto'),
            array('colunaValor' => 'T219_Id', 'lunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            //array('colunaValor' => 'T219_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'est012CampoComissao','callbackParameter' => 'T219', 'style' => 'width:1%'),
            array('colunaValor' => 'T219_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'est015CampoDividir','callbackParameter' => 'T219', 'style' => 'width:1%'),
            //array('colunaValor' => 'T219_Item', 'colunaTitulo' => 'Item', 'colunaAlinhar' => 'center' ),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'left', 'colunaExibir' => false ),
            array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left' ),
            //array('colunaValor' => 'T219_Codigo_Substituto', 'colunaTitulo' => 'Código Cli.', 'colunaAlinhar' => 'left'),
            //array('colunaValor' => 'T219_Numero_Pedido_Compra', 'colunaTitulo' => 'Num. OC', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'T219_Item_Pedido_Compra', 'colunaTitulo' => 'Item OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T219_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3' ),
            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Qtd', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2','colunaEditar' => true),
            //array('colunaValor' => 'T219_Valor_Preco_Original', 'colunaTitulo' => 'Original', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            //array('colunaValor' => 'T219_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Total_Preco', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'if(T013_Id>0 and (T013_Numero_Nota_Fiscal<0 or T013_Numero_Nota_Fiscal is null or T013_Numero_Nota_Fiscal=0),"FINALIZAR",T013_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'est012CampoNF','style' => 'width:1%;'),
            //array('colunaValor' => 'Duplicar', 'colunaTitulo' => '','colunaTipo' => 'livre', 'style'=>'width:1%;', 'callback'=>'est012DuplicarProduto'),            
            //array('colunaValor' => 'T219_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T219_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T219_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir'=>false),
            // array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T219_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => 'T219_Valor_ICMS_Substituicao_Retencao/T219_Quantidade as T219_Valor_ICMS_Substituicao', 'colunaTitulo' => 'Valor ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
        );

        /**
            *
            * Definição da SQL
            */
        $from = 'T219';

        $extra = <<<EOT
                left join T218 on T218_Id=T219_T218_Id
                left join D024 on D024_Id=T219_D024_Id
                left join D037 on D037_Id=T219_D037_Id
                left join D009 on D009_Id=T219_D009_Id
                left join D049 on D049_Id=D009_D049_Id
                left join D001 on D001_Id=D049_D001_Id
                left join D082 on D082_Id=D049_D082_Id
                left join D006 on D006_Id=T219_D006_Id
                left join D005 on D005_Id=T219_D005_Id
                left join C008 on C008_Id=D001_C008_Id
                where 
                T219_T218_Id = '{$r_acaoId}'
                
EOT;
        if (!empty($xConsulta_Clausula_Filtro)) {
            $extra .= <<<EOT
                      and $xConsulta_Clausula_Filtro
EOT;
        }
        
        /**
        * Filtro
        */
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($grid);

            
        //Define ação do click de abertura (ao clicar em uma linha do grid)
        $novoIdTelaForm = uniqid();
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                // (substituir pelo mesmo ID da chave primária do grid)
                    var chavePrimaria = 'T219_Id';    
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace(chavePrimaria + '-', '');
                    // Ação: abrir nova janela
            });
            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
                return false;
            });
            
            // Função JS executada como callback do editar campo (colunaEditarCallbackJS)
            function colunaEditarCallbackJS_T219_Percentual_Desconto(T219_Id, campoEditado, valorAntigo, novoValor) {
                    T219_Id = T219_Id.replace(/T219_Id-/, '');
                    if(campoEditado == 'T219_Percentual_Desconto'){
                        $.getJSON('/est/est015/grid_func-ajax/est015aplicarPercentualDesconto/?ajax=true&T219_Id='    + encodeURIComponent(T219_Id) + 
                                                                                          '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                          '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                          '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                          '&callback=?', function(request) {
                            if (request.code) {
                                divRefresh('{$g['divId']}');
                                //alert("NFe Excluída");
                            } else {
                                dialogConfirm('Erro', request.data);
                            }
                        });
                    }else if(campoEditado == 'T219_Valor_Preco_Sem_Desconto_Unitario'){
                        $.getJSON('/est/est015/grid_func-ajax/est015aplicarPercentualDesconto/?ajax=true&T219_Id='    + encodeURIComponent(T219_Id) + 
                                                                                          '&campoEditado='        + encodeURIComponent(campoEditado) +
                                                                                          '&valorAntigo='        + encodeURIComponent(valorAntigo) +
                                                                                          '&novoValor='            + encodeURIComponent(novoValor) +  
                                                                                          '&callback=?', function(request) {
                            if (request.code) {
                                divRefresh('{$g['divId']}');
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

	case 'est015GridTrocasDividir':
        
        $T218_Id = (isset($r_acaoId)) ? $r_acaoId : false;
        /**
         * Definição do Grid (Tabela)
         */
         //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Trocas</div>';
        $grid = array(
            //array('colunaValor' => 'T222_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'est012CampoComissao','callbackParameter' => 'T222', 'style' => 'width:1%'),
            array('colunaValor' => 'T222_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'est015CampoDividir','callbackParameter' => 'T222', 'style' => 'width:1%'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left' ),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Troca'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T222_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            //array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaAlinhar' => 'left', 'colunaExibir' => false ),
            array('colunaValor' => 'T222_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
            array('colunaValor' => 'T222_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
            array('colunaValor' => 'T222_Operacao', 'colunaTitulo' => 'Operação', 'callback' => 'retornaOperacaoTroca'),
            //array('colunaValor' => 'if(T013_Id>0 and (T013_Numero_Nota_Fiscal<0 or T013_Numero_Nota_Fiscal is null or T013_Numero_Nota_Fiscal=0),"FINALIZAR",T013_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'est012CampoNF','style' => 'width:1%;'),
            array('colunaValor' => 'T222_Observacao', 'colunaTitulo' => 'Observação'),
        );
        
        
                                

        /**
         *
         * Definição da SQL
         */
        $from = 'T222';

        $extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T222_D009_Id
            left join T014 on T014_T222_Id=T222_Id
            left join T013 on T013_Id=T014_T013_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
            WHERE T222_T218_Id = '{$T218_Id}'
EOT;

        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        /*
        function gridLinha($array) {
            if($array['todosCampos']['T225_T224_Id']>0){
                return 'linhaCinza';
            }
        }
        */

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
            //Define ação do click de abertura (ao clicar em uma linha do grid)

        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        	$('#{$g['divId']} .gridOptTop');
        } </script></div>
EOT;

        /**
        * Filtro
        */
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($grid);
        
        
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

    case 'est015GridServicosDividir':

            /**
            * Definição do Grid (Tabela)
            */
            //echo '<div style="margin-top: -2px; margin-left: 5px; font-size: 1.1em;">Serviços</div>';
            $grid = array(
            array('colunaValor' => 'T223_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'est015CampoDividir','callbackParameter' => 'T223', 'style' => 'width:1%'),
            array('colunaValor' => 'T223_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true, 'colunaExibir'=>false),
            //array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
            array('colunaValor' => "concat(T223_Titulo, ' ', T223_Descricao) as Descricao", 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            //array('colunaValor' => 'T223_Quantidade', 'colunaTitulo' => 'Quantidade'),
            array('colunaValor' => 'T223_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2'),
            //array('colunaValor' => 'T223_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
            //array('colunaValor' => 'T223_T224_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
            //array('colunaValor' => 'T223_T223_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
            //array('colunaValor' => 'T223_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'est002_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
            array('colunaValor' => 'T223_D116_Id', 'colunaExibir'=>false),
            array('colunaValor' => 'T223_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'colunaExibir'=>false),
            //array('colunaValor' => 'if(T013_Id>0 and (T013_Numero_Nota_Fiscal<0 or T013_Numero_Nota_Fiscal is null or T013_Numero_Nota_Fiscal=0),"FINALIZAR",T013_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'callback' => 'est012CampoNF','style' => 'width:1%;'),
            //array('colunaValor' => 'T223_Status', 'colunaTitulo' => 'Status'),
            //array('colunaValor' => 'T223_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
            //array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir'=>false),
            );
    
            /**
                *
                * Definição da SQL
                */
            $from = 'T223';
    
            $extra = <<<EOT
                    LEFT JOIN T219 ON T219_Id=T223_T219_Id
                    LEFT JOIN D009 ON D009_Id=T219_D009_Id
                    LEFT JOIN D049 ON D009_D049_Id=D049_Id
                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    WHERE T223_T218_Id='{$r_acaoId}'
EOT;
    

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 		 $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T230_Id-/, '');
                divSetarAcaoId('{$g['divId']}', id);
                divRefresh('{$g['divId']}');
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
        $novoIdTelaForm = uniqid();
    break;

    case 'est015GridCompras':
            /**
            * Definição do Grid (Tabela)
            */
            $grid = array(	            
				array('colunaValor' => 'T219_Id', 'chavePrimaria' => true, 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
				array('colunaValor' => 'T219_T218_Id', 'colunaTitulo' => 'T219_T218_Id', 'colunaExibir' => false),
				array('colunaValor' => 'T219_D009_Id', 'colunaTitulo' => 'T219_D009_Id', 'colunaExibir' => false),
				array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'T189_Id', 'colunaExibir' => false),
				array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T219_Descricao_Produto', 'colunaExibir' => false),
				array('colunaValor' => 'descricao', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left', 'callback' => 'validaCampoCotacaoOrcamento', 'colunaTipo' => 'livre'),
				array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
				array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'campoCotar', 'colunaTitulo' => 'Cotar', 'callback' => 'validaCampoCotarOrcamento', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T219_Flag_Comprar', 'colunaTitulo' => 'Cotar', 'callback' => 'checkboxCotarOrcamento', 'colunaAlinhar' => 'center', 'colunaEditarTodos'=>true),
				array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'D049_Fag_Tipo','colunaAlinhar' => 'center'),
            );
    
            /**
            * Definição da SQL
            */
            $from = 'T219';
            $extra = <<<EOT
            	LEFT JOIN T225 ON T225_T219_Id = T225_Id
            	LEFT JOIN T224 ON T224_Id = T225_T224_Id
            	LEFT JOIN T189 ON T189_T219_Id = T219_Id OR T189_T225_Id = T225_Id
            	LEFT JOIN D009 ON D009_Id = T219_D009_Id
            	LEFT JOIN D049 ON D049_Id = D009_D049_Id
            	LEFT JOIN D001 ON D001_Id = D049_D001_Id
            	LEFT JOIN D082 ON D082_Id = D049_D082_Id
				WHERE T219_T218_Id='{$r_T218_Id}' GROUP BY T219_Id
EOT;
    	$extra_ = urlencode(base64_encode(gRetirarOrderBy($extra)));
		$extra__ = base64_encode(gRetirarOrderBy($extra));
    	/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est015CotarProdutos">Cotar Produtos Selecionados</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est015CotarProdutos').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est015/grid_func-ajax/cotarProdutosSelecionados/?ajax=true&T218_Id='+ encodeURIComponent('{$r_T218_Id}') +'&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if(request.code){
							divRefresh('{$g['divId']}', true);
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				});

				function gridTheadCallback() {
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
						.removeAttr('onclick')
						.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est015/grid_func-ajax/est015MarcarTodosCotar/?ajax=true&T218_Id=' + encodeURIComponent('{$r_T218_Id}') + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est015/grid_func-ajax/est015DesmarcarTodosCotar/?ajax=true&T218_Id=' + encodeURIComponent('{$r_T218_Id}') + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								} ,
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Marcar/Desmarcar Todos', 'Você deseja marcar ou desmarcar todos? ', \$buttons);
							return false;
					});
				}
			} </script></div>
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['D009_Quantidade_Estoque_Liquido']<$array['todosCampos']['T219_Quantidade']){
				return 'linhaVermelho';
			}
		}

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 		 $('#{$g['divId']} .gridOptTop');
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T219_Id-/, '');                
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
        $novoIdTelaForm = uniqid();
    break;

    case 'est015GridPrincipalAnalise':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'naoLiberar', 'colunaTipo' => 'livre', 'callback' => 'est015NaoLiberar'),
			array('colunaValor' => 'Liberar', 'colunaTipo' => 'livre', 'callback' => 'est015Liberar'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct T225_T224_Id) as Pedido', 'colunaTitulo' => 'Pedido', 'colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal) as Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false),
			array('colunaValor' => 'T224_Nome_Status as T224_Status', 'colunaTitulo' => 'Situação', 'colunaExibir' => false),
			array('colunaValor' => 'T218_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Número', 'chavePrimaria'=>true),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => "if(T218_Flag_ACP='1','NF',if(T218_Flag_ACP='2','Romaneio',if(T218_Flag_ACP='3','ACP',if(T218_Flag_ACP='5','D1',if(T218_Flag_ACP='6','NFe','CF'))))) as Tipo_Documento", 'colunaTitulo' => 'Doc'),
			array('colunaValor' => 'T218_Valor_Total', 'colunaTitulo' => 'Valor','colunaCasasDecimais' => '2'),
	        //array('colunaValor' => 'T218_Motivo_Pedido_Liberacao', 'colunaTitulo' => 'Motivo'),
			array('colunaValor' => 'T218_IPV', 'colunaTitulo' => 'IPV','colunaCasasDecimais' => '4'),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor'),
			array('colunaValor' => 'T218_Observacao_Comercial', 'colunaTitulo' => 'Observação Comercial'),
		);


		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T218';

		$extra = <<<EOT

				left join D024 on D024_Id=T218_D024_Id
				left join T219 on T219_T218_Id=T218_Id
				left join T225 on T225_T219_Id=T219_Id
				left join T224 on T224_Id=T225_T224_Id
				left join T007 on T007_T224_Id=T224_Id
				left join C007 as Vendedor on Vendedor.C007_Id=T218_C007_Id_Vendedor_Interno
				left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
				where T218_C004_Id='{$g['empresaAtual']}'
EOT;

		if ($g['c007']['C007_Flag_Vendedor']!='D')
			$extra.=" and T218_Flag_Alcada='{$g['c007']['C007_Flag_Vendedor']}'";
		
		$extra.= <<<EOT
				and T218_Flag_Perdido!='S'
				and T218_Flag_Status_Liberacao='1'
				group by T218_Id
				
EOT;

		/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est015LiberacaoAtivarMsg">Ativar Mensagens</button>
					<button type="button" id="est015LiberacaoDesativarMsg">Desativar Mensagens</button>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
					$('#est015LiberacaoAtivarMsg').unbind('click').bind('click', function(data) {
						setCookie('liberacaoCreditoFlag', 2, 0, true);
						dialogConfirm('Notificações', 'Ativado!<br />Você irá receber notificações de análise pendente em todo o sistema.');
					});
					$('#est015LiberacaoDesativarMsg').unbind('click').bind('click', function(data) {
						setCookie('liberacaoCreditoFlag', 1, 0, true);
						dialogConfirm('Notificações', 'Desativado!<br />Você não irá mais receber notificações de análise pendente em todo o sistema.');
					});
				} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T218_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T218_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T218_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'T218_Flag_Status_Orcamento' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Finalizado', 'value' => 'F'),
				array('title' => 'Pendente', 'value' => 'P'),
				array('title' => 'Cancelado', 'value' => 'C'),
				array('title' => 'Vencido', 'value' => 'V'),
			), 'naoGerarExtra' => false),		
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T218_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Orçamento - ID: ' + id, '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [970,605]);
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

	case 'est015GridPrincipalProdutosVsFornecedor':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(T219_Codigo_Produto," ", ifnull(D049_Modelo,""), " - ", T219_Descricao_Produto) as Produto', 'colunaTitulo' => 'Produto', 'style' => 'width: 1%;'),
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
			GROUP BY T219_Codigo_Produto
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
				return 'linhaVermelho';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'numero'),
            'T218_Grupo' => array('titulo' => 'Grupo', 'tipo' => 'numero'),
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
		if (mysql_numrows($res) > 50) {
			die("<p style='margin: 30px; font-size: 1.3em; text-align: center;'>Consulta retornou muitos registros.<br />Por favor, utilize o filtro.</p>");
		}
		$cacheLimit = array();
		while($row = mysql_fetch_assoc($res)) {
			$subSqlExtra = str_replace('T219A', 'T219B', $extraSub);
			$subSqlExtra = str_replace('T218A', 'T218B', $subSqlExtra);
			$subSql = <<<EOT
				IFNULL((SELECT
						GROUP_CONCAT(T219_Valor_Custo_Unitario, ' - ')
					FROM
						T219 AS T219B
					{$subSqlExtra}
					AND {$row['D024_Id']}=T218B.T218_D024_Id
					AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto
					ORDER BY T219_Id ASC
					)
				,0)
EOT;
			$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => substr($row['D024_Nome_Empresa'], 0, 10), 'colunaAlinhar' => 'center', 'callback' => 'custosPorFornecedor', 'forcarExibirImpressao' => true, 'style' => 'color:blue');
		}

		// Coluna melhor preço
		$subSql = <<<EOT
		(SELECT
			CONCAT(ifnull(T219_Valor_Custo_Unitario,0), '|',ifnull(D024_Nome_Empresa,''), '|',ifnull(T219_Observacao_Entrega,''))
		FROM
			T219 AS T219B
		{$subSqlExtra}
		AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto
		AND T219B.T219_Valor_Custo_Unitario != 0
		ORDER BY T219B.T219_Valor_Custo_Unitario ASC
		LIMIT 0,1)
EOT;
		$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => 'Preço - Fornecedor - Prazo de Entrega', 'colunaAlinhar' => 'left', 'callback' => 'custosPorFornecedorMelhorPreco', 'forcarExibirImpressao' => true,'style' => 'border-left:1px solid silver');
		/*
			$inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
			$fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
			$data = strtotime($inicio);
			$fim = strtotime($fim);
			for ($i = 0; $i<12; $i++) {
				// o final só é final no último
				if (date('m', $fim) == date('m', $data)) {
					$data2 = $fim;
					$i = 12;
				} else {
					$data2 = strtotime(date('Y-m-t', $data)); // t = último dia do mês
				}
				// o inicio só é inicio no primeiro
				if ($i == 0) {
					$data1 = $data;
				} else {
					$data1 = strtotime(date('Y-m-1', $data));
				}
				//echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";
				$data1str = date('Y-m-d', $data1);
				$data2str = date('Y-m-d', $data2);
				$subSql = <<<EOT
		IFNULL((
			SELECT 
				SUM(T007_Valor_Total) 
			FROM 
				T007 AS T007B 
			left join D024 on D024_Id=T007B.T007_D024_Id 
			left join D018 on D024_D018_Id=D018_Id 
			left join D020 on D024_D020_Id=D020_Id 
			left join C007 on C007_Id=T007B.T007_C007_Id_Vendedor_Interno 
			left join C004 on C004_Id=T007B.T007_C004_Id 
			left join D006 on D006_Id=T007B.T007_D006_Id 
			WHERE 
				({$extraSub}) 
				AND T007B.T007_D024_Id = T007A.T007_D024_Id 
				AND T007B.T007_Data_Emissao >= '{$data1str} 00:00:00' 
				AND T007B.T007_Data_Emissao <= '{$data2str} 23:59:59'
		),0)
		EOT;
				$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
				$data = strtotime("+1 month", $data);
			}
		}
		*/

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

	case 'est015GridPrincipalComparar':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T219_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array(
                 'Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),
            ),
            array('colunaValor' => 'T219_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré-cadastro', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T004_T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T219_Id', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = "T219 AS T219A";

		if(!empty($r_T218_Grupo)) {
			$extraGrupo = "WHERE T218_Grupo = '{$r_T218_Grupo}' AND T218_C004_Id = '{$g['empresaAtual']}' AND T218_Flag_Perdido != 'S'";
			mysqli_query("UPDATE T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id SET T219_Flag_Melhor_Preco = 'N' WHERE T218_Grupo = '{$r_T218_Grupo}' AND T218_Flag_Perdido != 'S'");
			$arrayT218 = array();
			$sqlGrupo = mysql_query("SELECT DISTINCT T218_Id FROM T218 WHERE T218_Grupo = '{$r_T218_Grupo}'");
			$countCotacao = 0;
			while($rowGrupo = mysql_fetch_array($sqlGrupo)){
				$countCotacao++;
				$arrayT218[] = $rowGrupo['T218_Id'];
			}

			if($countCotacao == 1){
				// Se tiver apenas uma cotação no grupo, lista todos os resultados mesmo que o código se repita
				// Precisa arrumar o processo por completo, até a geração da OC, em algum momento esta agrupando para gerar a OC
				//$groupBy = 'GROUP BY T219_Id, IF(T219_T006_Id > 0 OR T219_T004_Id > 0, IF(T219_T006_Id > 0, T219_T006_Id, T219_T004_Id), T219_Id)';
				$groupBy = 'GROUP BY T219_Codigo_Produto, IF(T219_T006_Id > 0 OR T219_T004_Id > 0, IF(T219_T006_Id > 0, T219_T006_Id, T219_T004_Id), T219_Codigo_Produto)';
			} else {
				// Se for mais de uma cotação, agrupa por código
				$groupBy = 'GROUP BY T219_Codigo_Produto, IF(T219_T006_Id > 0 OR T219_T004_Id > 0, IF(T219_T006_Id > 0, T219_T006_Id, T219_T004_Id), T219_Codigo_Produto)';
			}
			$arrayT218 = json_encode($arrayT218);
			
		} else {
			$extraGrupo = "WHERE T218_C004_Id = '{$g['empresaAtual']}' AND T218_Flag_Perdido != 'S'";
			$groupBy = 'GROUP BY T219_Codigo_Produto, IF(T219_T006_Id > 0 OR T219_T004_Id > 0, IF(T219_T006_Id > 0, T219_T006_Id, T219_T004_Id), T219_Codigo_Produto)';
		}

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
			left join T004 on T219_T004_Id=T004_Id
			left join T006 on T219_T006_Id=T006_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			{$extraGrupo}
			{$groupBy}
EOT;

		if(!empty($r_T218_Grupo)){
/*			$extraSemGroupBy = gRetirarGroupBy($extra);
			$sqlMenorPreco = <<<EOT
				SELECT * FROM (
					SELECT 
						T219_Id,
					    T219_Codigo_Produto,
					    T219_Valor_Custo_Unitario,
					    T219_T006_Id,
					    T219_T004_Id 
					FROM {$from}
    					{$extraSemGroupBy}
    					AND T219_Valor_Custo_Unitario > 0
					ORDER BY IF(T219_Valor_Preco_Venda>0, T219_Valor_Preco_Venda, T219_Valor_Custo_Unitario) ASC
        		) AS T1 GROUP BY T219_Codigo_Produto
EOT;*/

/*			$sqlMenorPreco = <<<EOT
				SELECT * FROM (
					SELECT 
						T219_Id,
					    T219_Codigo_Produto,
					    T219_Valor_Custo_Unitario,
					    T219_T006_Id,
					    T219_T004_Id 
					FROM {$from}
    					{$extraSemGroupBy}
    					AND T219_Valor_Custo_Unitario > 0
					ORDER BY IF(T219_T006_Id > 0 OR T219_T004_Id > 0, T219_Valor_Preco_Venda, T219_Valor_Custo_Unitario) ASC, IFNULL(T219_Observacao_Entrega,'') ASC
        		) AS T1 GROUP BY T219_Codigo_Produto, IF(T219_T006_Id > 0 OR T219_T004_Id > 0, IF(T219_T006_Id > 0, T219_T006_Id, T219_T004_Id), T219_Codigo_Produto)
EOT;*/
			mysqli_query("UPDATE T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id SET T219_Flag_Melhor_Prazo = 'N', T219_Flag_Melhor_Preco = 'N' WHERE T218_Grupo ='{$r_T218_Grupo}' AND T218_Flag_Perdido != 'S';");
			$extraSemGroupBy = gRetirarGroupBy($extra);

			/*$sqlPreco = mysql_query("SELECT D009_Id
									   FROM {$from}
					    			  	    {$extraSemGroupBy}
					    		   GROUP BY D009_Id");

			log("sqlPreco = ".$sqlPreco);

			while($rowPreco = mysql_fetch_assoc($sqlPreco)) {
			 	  $sqlMelhorPreco = mysql_query("SELECT T219_Id
												   FROM {$from}
								    		  		    {$extraSemGroupBy}
								    			    AND D009_Id = '{$rowPreco['D009_Id']}'
								    			    AND T219_Valor_Custo_Unitario > 0
											   ORDER BY IF(T219_Valor_Preco_Venda>0, T219_Valor_Preco_Venda, T219_Valor_Custo_Unitario) ASC
											      LIMIT 1");
			 	  $rowMelhorPreco = mysql_fetch_assoc($sqlMelhorPreco);

			 	  log("sqlMelhorPreco = SELECT T219_Id
												   FROM {$from}
								    		  		    {$extraSemGroupBy}
								    			    AND D009_Id = '{$rowPreco['D009_Id']}'
								    			    AND T219_Valor_Custo_Unitario > 0
											   ORDER BY IF(T219_Valor_Preco_Venda>0, T219_Valor_Preco_Venda, T219_Valor_Custo_Unitario) ASC
											      LIMIT 1");

				  mysqli_query("UPDATE T219 SET T219_Flag_Melhor_Preco = 'S' WHERE T219_Id = '{$rowMelhorPreco['T219_Id']}'");
			}*/

			// Seleciona os produtos que contém o melhor preço para serem marcados. O agrupamento é pelo T219_Codigo_Produto, T219_T006_Id e T219_T004_Id e não mais pelo D009
			// Chamado 38195

/*			$sqlPreco = <<<EOT
							  SELECT T219_Id 
						FROM (SELECT T219_Id,
								     T219_Codigo_Produto,
								     T219_Valor_Custo_Unitario,
								     T219_T006_Id,
								     T219_T004_Id 
							    FROM {$from}
								     {$extraSemGroupBy}
								 AND T219_Valor_Custo_Unitario > 0 
						    ORDER BY IF(T219_Valor_Preco_Venda>0, 
								       T219_Valor_Preco_Venda, 
								       T219_Valor_Custo_Unitario
								      ) ASC)  AS T1
							GROUP BY T219_Codigo_Produto,
								 IF( T219_T006_Id > 0 OR T219_T004_Id > 0,
								    IF(T219_T006_Id > 0,
								       T219_T006_Id,
								       T219_T004_Id
								    ),
								    T219_Codigo_Produto
								  )
EOT;*/


			$sqlPreco = <<<EOT
							  SELECT (SELECT T219A.T219_Id
									    FROM {$from}
										     {$extraSemGroupBy}
									     AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto
									     AND IFNULL(T219A.T219_Valor_Custo_Unitario, 0) > 0     
									     AND IFNULL(T219A.T219_T006_Id,0) = IFNULL(T219B.T219_T006_Id,0) 
									     AND IFNULL(T219A.T219_T004_Id,0) = IFNULL(T219B.T219_T004_Id,0) 
								    ORDER BY IF(T219_Valor_Preco_Venda>0, 
										       T219_Valor_Preco_Venda, 
										       T219_Valor_Custo_Unitario
										      ) ASC LIMIT 1)  AS T219_Id
								FROM T219 AS T219B
						   LEFT JOIN T218 AS T218B ON T218B.T218_ID = T219B.T219_T218_ID 
							   		 {$extraGrupo}
							     AND T219B.T219_Valor_Custo_Unitario > 0 
							GROUP BY T219B.T219_Codigo_Produto,
								 IF( T219B.T219_T006_Id > 0 OR T219B.T219_T004_Id > 0,
								    IF(T219B.T219_T006_Id > 0,
								       T219B.T219_T006_Id,
								       T219B.T219_T004_Id
								    ),
								    T219B.T219_Codigo_Produto
								  )
EOT;

			$sqlMelhorPreco = mysql_query($sqlPreco);
			while($rowMelhorPreco = mysql_fetch_array($sqlMelhorPreco)){
				mysqli_query("UPDATE T219 SET T219_Flag_Melhor_Preco = 'S' WHERE T219_Id = '{$rowMelhorPreco['T219_Id']}'");
			}
			$extraSemGroupBy = gRetirarGroupBy($extra);

			// Seleciona os produtos que contém o melhor prazo para serem marcados. 
			$sqlMenorPrazo = <<<EOT
									SELECT T219_Id 
				  			 FROM (SELECT  T219_Id,
										   T219_Codigo_Produto,
										   T219_Valor_Custo_Unitario,
										   T219_T006_Id,
										   T219_T004_Id
									  FROM {$from}
					    			       {$extraSemGroupBy}
					    			   AND T219_Valor_Custo_Unitario != 0
						   ORDER BY IFNULL(T219_Observacao_Entrega,'') ASC, 
						            IF(T219_T006_Id > 0 OR T219_T004_Id > 0, 
						               T219_Valor_Preco_Venda, 
						               T219_Valor_Custo_Unitario
						             ) ASC
					        	  ) AS T1 
					          GROUP BY T219_Codigo_Produto, 
					                IF(T219_T006_Id > 0 OR T219_T004_Id > 0, 
						                IF(T219_T006_Id > 0, 
						                   T219_T006_Id, 
						                   T219_T004_Id
						                ), 
					                	T219_Codigo_Produto
					                )
EOT;
			$sqlMenorPrazo = mysql_query($sqlMenorPrazo);
			while($rowMenorPrazo = mysql_fetch_array($sqlMenorPrazo)){
				mysqli_query("UPDATE T219 SET T219_Flag_Melhor_Prazo = 'S' WHERE T219_Id = '{$rowMenorPrazo['T219_Id']}'");
			}
		}		

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
				return 'linhaVermelho';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'numero'),
            'T218_Grupo' => array('titulo' => 'Grupo', 'tipo' => 'numero'),
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

		$primeiraLinhaGridHead = "<tr><th colspan=\"6\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2;\">Produto</th>";

		// Usar mesmo extra na subquery
		$extraSub = gRetirarGroupBy($extra);

		// Loop para criar colunas com os fornecedores
		$sql = <<<EOT
		SELECT 
			DISTINCT D024_Id, D024_Id, D024_Nome_Empresa
		FROM
			T219 AS T219A
		{$extraSub}
		GROUP BY T218A.T218_Id
EOT;
		$res = mysql_query($sql);
		if (mysql_numrows($res) > 50) {
			die("<p style='margin: 30px; font-size: 1.3em; text-align: center;'>Consulta retornou muitos registros.<br />Por favor, utilize o filtro.</p>");
		}
		$cacheLimit = array();
		while($row = mysql_fetch_assoc($res)) {
			$subSqlExtra = str_replace('T219A', 'T219B', $extraSub);
			$subSqlExtra = str_replace('T218A', 'T218B', $subSqlExtra);
			$subSql = <<<EOT
				IFNULL((SELECT
						T219_Valor_Preco_Unitario
					FROM
						T219 AS T219B
					{$subSqlExtra}
					AND {$row['D024_Id']}=T218B.T218_D024_Id
					AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto AND IF(T219A.T219_T006_Id > 0 OR T219A.T219_T004_Id > 0, (T219A.T219_T006_Id=T219B.T219_T006_Id OR T219A.T219_T004_Id=T219B.T219_T004_Id), T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto)
					ORDER BY T219_Id ASC
					LIMIT 0,1)
				,0)
EOT;
			$subSql2 = <<<EOT
				IFNULL((SELECT
						if(T219_Flag_Melhor_Preco='S',CONCAT('<span style="color: blue;">',REPLACE(REPLACE(REPLACE(FORMAT(T219_Valor_Custo_Unitario, 5), ".", "@"), ",", "."), "@", ","),'</span>'),REPLACE(REPLACE(REPLACE(FORMAT(T219_Valor_Custo_Unitario, 5), ".", "@"), ",", "."), "@", ","))
					FROM
						T219 AS T219B
					{$subSqlExtra}
					AND {$row['D024_Id']}=T218B.T218_D024_Id
					AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto AND IF(T219A.T219_T006_Id > 0 OR T219A.T219_T004_Id > 0, (T219A.T219_T006_Id=T219B.T219_T006_Id OR T219A.T219_T004_Id=T219B.T219_T004_Id), T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto)
					ORDER BY T219_Id ASC
					LIMIT 0,1)
				,'-')
EOT;
			$subSql3 = <<<EOT
					(SELECT
						CONCAT(IFNULL(T219_Id,0),'-',CONCAT(T219_Flag_Melhor_Preco,'|',IFNULL(T219_Valor_Preco_Venda, 0)))
					FROM
						T219 AS T219B
					{$subSqlExtra}
					AND {$row['D024_Id']}=T218B.T218_D024_Id
					AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto AND IF(T219A.T219_T006_Id > 0 OR T219A.T219_T004_Id > 0, (T219A.T219_T006_Id=T219B.T219_T006_Id OR T219A.T219_T004_Id=T219B.T219_T004_Id), T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto)
					ORDER BY T219_Id ASC
					LIMIT 0,1)
EOT;
			$subSql4 = <<<EOT
				(SELECT T219_T218_Id FROM T219 AS T219B
					{$subSqlExtra}
					AND {$row['D024_Id']}=T218B.T218_D024_Id
					AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto AND IF(T219A.T219_T006_Id > 0 OR T219A.T219_T004_Id > 0, (T219A.T219_T006_Id=T219B.T219_T006_Id OR T219A.T219_T004_Id=T219B.T219_T004_Id), T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto)
					ORDER BY T219_Id ASC
					LIMIT 0,1) AS T218_Id
EOT;
			$subSql5 = <<<EOT
					(SELECT
						trim(concat(if(T219_Flag_Melhor_Prazo='S',CONCAT('<span style="color: blue;">',ifnull(T219_Observacao_Entrega,0),'<span>'),ifnull(T219_Observacao_Entrega,0)),' ','<font style="font-size:11px;color: var(--BGVermelho)">',substring(ifnull('',''),1,20),'</font>'))
					FROM
						T219 AS T219B
					{$subSqlExtra}
					AND {$row['D024_Id']}=T218B.T218_D024_Id
					AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto
					ORDER BY T219_Id ASC
					LIMIT 0,1)
EOT;
			if(!empty($r_T218_Grupo)){
				$sqlT219 = <<<EOT
					(SELECT 
						CONCAT(ifnull(T225_Id,0),'|',T219B.T219_Id,'|',ifnull(T219B.T219_Flag_Comprar,''),'|',ifnull(T225_T224_Id,0)) 
					FROM T219 AS T219B
						{$subSqlExtra} 
						AND {$row['D024_Id']}=T218B.T218_D024_Id 
						AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto AND IF(T219A.T219_T006_Id > 0 OR T219A.T219_T004_Id > 0, (T219A.T219_T006_Id=T219B.T219_T006_Id OR T219A.T219_T004_Id=T219B.T219_T004_Id), T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto) LIMIT 0,1) as Coluna{$uniqid}
EOT;
//AND T219A.T219_Id=T219B.T219_Id AND IF(T219A.T219_T006_Id > 0 OR T219A.T219_T004_Id > 0, (T219A.T219_T006_Id=T219B.T219_T006_Id OR T219A.T219_T004_Id=T219B.T219_T004_Id), T219A.T219_Id=T219B.T219_Id) LIMIT 0,1) as Coluna{$uniqid}
				$grid[] = array('colunaValor' => $sqlT219, 'colunaTitulo' => 'OC', 'callback' => 'validaCampoComprarAnalise', 'style' => 'width:1%;border-left:1px solid silver', 'colunaAlinhar' => 'center');
			}
			$subSql4 = str_replace("\n", " ", $subSql4); // CORREÇÃO CHAMADO ELETRICA CEIGON C - 42810(10/02/2023) gerar CSV não funcionando
			$grid[] = array('colunaValor' => $subSql4, 'colunaAlinhar' => 'center', 'colunaTitulo' => 'Cot.', 'callback' => 'abrirJanela', 'callbackParameter' => array('Editar Cotação de Compra', '/est/est015/content/est015ContentEditarOrcamento/', "campoAtual", '990,605', false, false, '&tabela=T218'), 'style' => 'width:1%');
			//$grid[] = array('colunaValor' => $subSql, 'colunaAlinhar' => 'center', 'forcarExibirImpressao' => true, 'colunaTitulo' => 'Preço FOB', 'colunaCasasDecimais' => '5', 'colunaExibir' => false, 'style' => 'width:1%');
			$subSql5 = str_replace("\n", " ", $subSql5); // CORREÇÃO CHAMADO ELETRICA CEIGON C - 42810(10/02/2023) gerar CSV não funcionando
			$grid[] = array('colunaValor' => $subSql5, 'colunaAlinhar' => 'center', 'forcarExibirImpressao' => true, 'colunaTitulo' => 'Prz', 'colunaExibir' => true, 'style' => 'width:1%;max-width:150px;white-space:normal;');
			$subSql2 = str_replace("\n", " ", $subSql2); // CORREÇÃO CHAMADO ELETRICA CEIGON C - 42810(10/02/2023) gerar CSV não funcionando
			$grid[] = array('colunaValor' => $subSql2, 'colunaAlinhar' => 'right', 'forcarExibirImpressao' => true, 'colunaTitulo' => 'Custo', 'style' => 'width:1%');
			$subSql3 = str_replace("\n", " ", $subSql3); // CORREÇÃO CHAMADO ELETRICA CEIGON C - 42810(10/02/2023) gerar CSV não funcionando
			$grid[] = array('colunaValor' => $subSql3, 'colunaAlinhar' => 'right', 'forcarExibirImpressao' => true, 'colunaTitulo' => 'Venda', 'style' => 'width:1%', 'callback' => 'abrirNotificacaoCotacao', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/est/est015/outro/demonstrativoPrecoVenda/', 'right', false, '235'));
			if(!empty($r_T218_Grupo)){
				$primeiraLinhaGridHead .= "<th colspan=\"5\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2; \">".substr($row['D024_Nome_Empresa'], 0, 20)."</th>";
			} else {
				$primeiraLinhaGridHead .= "<th colspan=\"4\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: center; z-index: 2; \">".substr($row['D024_Nome_Empresa'], 0, 20)."</th>";
			}
		}

		// Coluna melhor preço
		$subSql = <<<EOT
		(SELECT
			CONCAT(ifnull(T219_Valor_Custo_Unitario,0), '|',ifnull(D024_Nome_Empresa,''), '|',ifnull(T219_Observacao_Entrega,''))
		FROM
			T219 AS T219B
		{$subSqlExtra}
		AND T219A.T219_Codigo_Produto=T219B.T219_Codigo_Produto
		AND T219B.T219_Valor_Custo_Unitario != 0
		GROUP BY T218B.T218_D024_Id 
		ORDER BY T219B.T219_Valor_Custo_Unitario ASC
		LIMIT 0,1)
EOT;
		$subSql = str_replace("\n", " ", $subSql); // CORREÇÃO CHAMADO ELETRICA CEIGON C - 42810(10/02/2023) gerar CSV não funcionando
		$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => 'Preço - Fornecedor - Prazo de Entrega', 'colunaAlinhar' => 'left', 'callback' => 'custosPorFornecedorMelhorPreco', 'forcarExibirImpressao' => true,'style' => 'border-left:1px solid silver');
		$primeiraLinhaGridHead .= "<th colspan=\"1\" class=\"ui-widget-header ui-corner-all\" style=\"text-align: left; z-index: 2;\">Melhor preço</th>";
		/*
			$inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
			$fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
			$data = strtotime($inicio);
			$fim = strtotime($fim);
			for ($i = 0; $i<12; $i++) {
				// o final só é final no último
				if (date('m', $fim) == date('m', $data)) {
					$data2 = $fim;
					$i = 12;
				} else {
					$data2 = strtotime(date('Y-m-t', $data)); // t = último dia do mês
				}
				// o inicio só é inicio no primeiro
				if ($i == 0) {
					$data1 = $data;
				} else {
					$data1 = strtotime(date('Y-m-1', $data));
				}
				//echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";
				$data1str = date('Y-m-d', $data1);
				$data2str = date('Y-m-d', $data2);
				$subSql = <<<EOT
		IFNULL((
			SELECT 
				SUM(T007_Valor_Total) 
			FROM 
				T007 AS T007B 
			left join D024 on D024_Id=T007B.T007_D024_Id 
			left join D018 on D024_D018_Id=D018_Id 
			left join D020 on D024_D020_Id=D020_Id 
			left join C007 on C007_Id=T007B.T007_C007_Id_Vendedor_Interno 
			left join C004 on C004_Id=T007B.T007_C004_Id 
			left join D006 on D006_Id=T007B.T007_D006_Id 
			WHERE 
				({$extraSub}) 
				AND T007B.T007_D024_Id = T007A.T007_D024_Id 
				AND T007B.T007_Data_Emissao >= '{$data1str} 00:00:00' 
				AND T007B.T007_Data_Emissao <= '{$data2str} 23:59:59'
		),0)
		EOT;
				$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
				$data = strtotime("+1 month", $data);
			}
		}
		*/

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 10000);

		$sql_ = urlencode(base64_encode("WHERE ".gPegarParteWhere($geradoSql)));
		if (!empty($r_T218_Grupo)) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est015GerarOCAnalise">Gerar O.C.</button>
					<button type="button" id="est015MarcarMelhorPreco">Marcar Melhor Preço</button>
					<button type="button" id="est015MarcarMelhorPrazo">Marcar Melhor Prazo</button>
					<button type="button" id="est015AtualizarCustoGrupo">Atualizar Custo</button>
				</div>
EOT;
		}

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est015GerarOCAnalise').unbind('click').bind('click', function(data) {
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

				$('#est015MarcarMelhorPreco').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est015/grid_func-ajax/est015MarcarMelhorPreco/?ajax=true&T218_Grupo=' + encodeURIComponent("{$r_T218_Grupo}") + '&callback=?', function(request) {
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
					dialogConfirm('Marcar melhor preço', 'Confirma marcar melhor preço ?', \$buttons);
				});

				$('#est015MarcarMelhorPrazo').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est015/grid_func-ajax/est015MarcarMelhorPrazo/?ajax=true&T218_Grupo=' + encodeURIComponent("{$r_T218_Grupo}") + '&callback=?', function(request) {
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
					dialogConfirm('Marcar melhor prazo', 'Confirma marcar melhor prazo ?', \$buttons);
				});

				var arrayT218 = $.parseJSON('{$arrayT218}');
				var retorno = '';
				var total = arrayT218.length;
				var indexTotal = 0;
				$('#est015AtualizarCustoGrupo').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Apenas a venda": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									atualizarCustoLoop('atualizarCustoVenda');
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar o custo apenas para esta venda?', \$buttons2);
						},
						"Atualizar o produto": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									atualizarCustoLoop('atualizarCustoProduto');
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar o custo dos produtos selecionados?', \$buttons2);
						},
						"Atualizar tabela fornecedor": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									atualizarCustoLoop('atualizarTabelaFornecedor');
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar a tabela do fornecedor dos produtos selecionados?', \$buttons2);
						},
						"Cancelar": function() { $(this).dialog("destroy").remove(); }
					};
					var dialog = dialogConfirm('Atualizar Custos', '<b>Atenção!</b><br />Você deseja atualizar o custo:', \$buttons);
					var buttons = $('#dc-' + dialog).parent().find('.ui-dialog-buttonpane button')
					var botao0 = $(buttons[0]);
					var botao1 = $(buttons[1]);
					var botao2 = $(buttons[2]);
					if('{$g['nivelAlcada']}' < '{$g['C031']['nivelAlcadaAtualizarCustoVenda']}'){
						$(botao0).attr('disabled', 'disabled').addClass('ui-state-disabled');
					}
					if('{$g['nivelAlcada']}' < '{$g['C031']['nivelAlcadaAtualizarCustoProduto']}'){
						$(botao1).attr('disabled', 'disabled').addClass('ui-state-disabled');
					}
					if('{$g['nivelAlcada']}' < '{$g['C031']['nivelAlcadaAtualizarCustoFornecedor']}'){
						$(botao2).attr('disabled', 'disabled').addClass('ui-state-disabled');
					}
				});

				function atualizarCustoLoop(tipo){
					if(indexTotal === total){
						if(retorno){
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Atenção', retorno, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
						return;
					}

				    var T218_Id = arrayT218[indexTotal];
				    $.ajax({
						url: '/est/est015/form_func-ajax/'+tipo+'/',
						dataType: 'json',
						data: 'ajax=true&T218_Id=' + encodeURIComponent(T218_Id) + '&callback=?',
						success: function(request) {
							if (request.code) {
								retorno += '<b>COTAÇÃO ' + T218_Id + '</b><br>' + request.data + '<br><br>';
							} else {
								retorno += '<b>COTAÇÃO ' + T218_Id + '</b><br>' + request.data.replace("ATENÇÃO!!! <BR /><BR />", "") + '<br><br>';
							}
						}
					}).done(function(data) {
						indexTotal++;
						atualizarCustoLoop(tipo);
					});
				}

				setTimeout(function(){
					$('#{$g['divId']} .gridTheadTh').parent().sortable("destroy");
					$('#{$g['divId']} .gridTheadTh').resizable("destroy");
				}, 500);
			} </script></div>
EOT;
        }

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'est015GridCotacoesGrupo':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'T218_D024_Id', 'colunaTitulo' => 'Cod.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,28)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(CONVERT(substr(D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;white-space:normal;min-width:190px;width:190px'),
            array('colunaValor' => 'count(distinct T219_Id)', 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T218_Valor_Total', 'colunaTitulo' => 'Valor total', 'style' => 'width:1%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
            array('colunaValor' => 'group_concat(distinct T004_T003_Id)', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'if(T139_Id>0,\'Ok\',\'<font style="color: var(--BGVermelho)">--</font>\')', 'colunaTitulo' => 'Mail', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T218';
		
		$extra = <<<EOT
			LEFT JOIN T219 ON T218_Id=T219_T218_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
		    LEFT JOIN D024 ON D024_Id=T218_D024_Id
			LEFT JOIN D013 ON D013_Id=T218_D013_Id
			LEFT JOIN D018 ON D018_Id=D024_D018_Id
			LEFT JOIN D020 ON D020_Id=D024_D020_Id
			LEFT JOIN T139 ON T218_Id=T139_T218_Id
			WHERE T218_Grupo = '{$r_T218_Grupo}'
			GROUP BY T218_Id
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

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
}
