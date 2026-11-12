<?php
namespace hardness;
// Classes
//require_once('bibliotecas/classes/est016.php');
//$est016 = new est016();
// Carrega funções callback
require_once('prog/est/est016/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est016/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * AÃ§Ãµes
 */
switch($g['r'][3]){
	/**
	* Grid Principal
	* Chamado em: est016tab01
	*/
	case 'est016grid01':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoCancelamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamento', 'style' => 'width:1%;', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'dividirOrdemCompraFornecedor', 'colunaTipo' => 'livre', 'callback' => 'dividirOrdemCompraFornecedor', 'style' => 'width:1%;'),
            array('colunaValor' => 'T224_Numero_Pedido_Venda', 'colunaTitulo' => 'Pedido Ecommerce', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'T224_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_D024_Id', 'colunaTitulo' => 'Cód.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
            array('colunaValor' => 'concat(convert(substr(D024_Nome_Empresa,1,30) using utf8),\'<br><font style="font-size:10px"><i>\',convert(ifnull(T224_Observacao_Pedido_Perdido,\'\') using utf8))', 'colunaTitulo' => 'Razão Social', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            //COM CELULAR array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\'),\' \',ifnull(D013_DDD_Celular,\'\'),\'-\',ifnull(D013_Celular,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;min-width:190px;width:190px;white-space:normal;'),
            array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',convert(ifnull(D013_DDD_Telefone_1,\'\') using utf8),\'-\',convert(ifnull(D013_Telefone_1,\'\') using utf8)) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;min-width:190px;width:190px;white-space:normal;'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'T224_Valor_Pendente', 'colunaTitulo' => 'Valor pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);color: var(--BGVermelho);width:1%'),
            array('colunaValor' => 'T224_Prazos(T224_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
            array('colunaValor' => 'D163_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false, 'style' => 'width:1%'),
            //array('colunaValor' => 'sum(distinct IF(T225_Quantidade_Pendente_3(T225_Id) <= 0, 0, concat(T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id),T225_Id))) as valorPendente', 'colunaCasasDecimais' => 2, 'colunaTitulo' => 'Valor pend.', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-amarelo-claro);color: var(--BGVermelho);width:1%'),
            //array('colunaValor' => '(sum(distinct concat(ifnull(T014_Quantidade,\'0.00000\'),T014_Id))/sum(distinct concat(T225_Quantidade,T225_Id)))*100', 'colunaTitulo' => '%Receb', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            //MOSTRA TODOS EMAILS array('colunaValor' => 'concat(T224_Nome_Status,\'<font style="font-size:10px;"> \',ifnull(group_concat(DISTINCT \'Mail:\',T139_Data_Hora),\'\'),\'<br><font style="font-size:11px;">\',ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\'))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            array('colunaValor' => 'concat(convert(T224_Nome_Status using utf8),\'<br><font style="font-size:10px"><i>\',convert(ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\') using utf8))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;width:220px;white-space:normal;'),
            //array('colunaValor' => 'concat(\'<font style="font-size:10px;"> \',ifnull(T139_Data_Hora,\'\'),\'<font style="font-size:11px;">\')', 'colunaTitulo' => 'Mail', 'style' => 'width:1%'),
            array('colunaValor' => 'if(T139_Id>0,\'Ok\',\'<font style="color: var(--BGVermelho)">--</font>\')', 'colunaTitulo' => 'Mail', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT T013.T013_Id SEPARATOR \'|\') FROM T225 LEFT JOIN T014 ON T014.T014_T225_Id = T225.T225_Id AND T014.T014_Flag_Cancelada != \'S\' LEFT JOIN T013 ON T013.T013_Id = T014.T014_T013_Id AND T013.T013_Flag_Cancelada != \'S\' WHERE T225.T225_T224_Id = T224_Id)', 'colunaTitulo' => 'NF', 'callback' => 'danfeNFsDaOrdemDeCompra', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Nome_Status as Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            //array('colunaValor' => 'group_concat("<span style=\\"\\">", T224_Nome_Status, "</span> ", ifnull((SELECT date_format(T228_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T228 WHERE T228_T224_Id=T224_Id order by T228_Id desc LIMIT 1), ""), "<br />", if(T224_Flag_Status=9, T224_Motivo_Nao_Liberado,"")) as Status2', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(IF(T225_T075_Id>0,"MP","PE"))) FROM T225 WHERE T225_T224_Id=T224_Id) as Estoque', 'colunaTitulo' => 'Estoque', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) FROM T225 LEFT JOIN D009 ON D009_Id = T225_D009_Id LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D082 ON D082_Id = D049_D082_Id WHERE T225_T224_Id=T224_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'downloadXMLPedido', 'colunaTipo' => 'livre', 'colunaExibir' => false),
            array('colunaValor' => 'analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),
            array('colunaValor' => 'T224_Liberou_Alcada_Data', 'colunaTitulo' => 'Data análise', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => "(SELECT GROUP_CONCAT(DISTINCT T013.T013_Numero_Nota_Fiscal) FROM T225 LEFT JOIN T014 ON T014.T014_T225_Id = T225.T225_Id AND T014.T014_Flag_Cancelada != 'S' LEFT JOIN T013 ON T013.T013_Id = T014.T014_T013_Id AND T013.T013_Flag_Cancelada != 'S' WHERE T225.T225_T224_Id = T224_Id) as T013_Numero_Nota_Fiscal", 'colunaTitulo' => 'T013_Numero_Nota_Fiscal', 'colunaExibir' => false),
			array('colunaValor' => 'T224_Observacao', 'colunaTitulo' => 'Fornecedor', 'colunaEditar' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T224_Observacao_Comercial', 'colunaTitulo' => 'Comercial Interna', 'colunaEditar' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T224_Observacao_Nota_Fiscal', 'colunaTitulo' => 'Nota Fiscal', 'colunaEditar' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T224_Observacao_1', 'colunaTitulo' => 'Almoxarifado', 'colunaEditar' => true, 'colunaExibir' => false),
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
			LEFT JOIN D024 ON D024_Id=T224_D024_Id
			LEFT JOIN D018 ON D018_Id=D024_D018_Id
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=T224_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS analista ON analista.C007_Id=T224_Liberou_Alcada_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=comprador.C007_C007_Id
			LEFT JOIN C004 ON C004_Id = T224_C004_Id
			LEFT JOIN D116 ON D116_Id = T224_D116_Id
			LEFT JOIN D021 ON D021_Id = T224_D021_Id
			LEFT JOIN D022 ON D022_Id = T224_D022_Id
			left join D163 on D163_Id = T224_D163_Id
			LEFT JOIN D013 ON D013_Id=T224_D013_Id
			LEFT JOIN T139 ON T139_T224_Id=T224_Id
			LEFT JOIN D006 ON D006_Id = T224_D006_Id
			WHERE (T224_C004_Id={$g['empresaAtual']} 
			AND (T224_T224_Id_Agrupado<=0 or T224_T224_Id_Agrupado is null))
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
			GROUP BY T224_Id
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T224_Flag_Status'] <= 0){
				return 'linhaAzul';
			} else if($array['todosCampos']['T224_Flag_Status'] == '10'){
				return 'linhaCinza';
			} else if($array['todosCampos']['T224_Flag_Status'] == '11'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T224_Flag_Status'] == '12'){
				return 'linhaLaranja';
			} else if($array['todosCampos']['T224_Flag_Status'] == '3'){
				return 'linhaVerde';
			} else if($array['todosCampos']['T224_Flag_Status'] == '1'){
				return 'linhaPink';
			}
		}
		

		/*	
		echo <<<EOT
		<div class="buttonsBottomTop">
			<!--<button type="button" id="est016RecebePedidos">Receber Pedidos</button>-->
EOT;
		if($g['C031']['habilitarbotaoIncluirOC']=='S'){
			echo <<<EOT
				<button type="button" id="est016IncluirOC">Incluir</button>
EOT;
		}

		echo <<<EOT
			<button type="button" id="est016ProcessarOCVencida">Processar Vencidos</button>
		</div>
EOT;	*/

		/**
		* Filtro
		*/

		$exFiltro = "FROM T225
				LEFT JOIN D009 ON D009_Id = T225.T225_D009_Id
				LEFT JOIN D049 ON D049_Id = D009_D049_Id
				LEFT JOIN D001 ON D001_Id = D049_D001_Id
			WHERE T225.T225_T224_Id = T224_Id)";
		
		$gridFiltro = array(
		    'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
		    'T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'numero'),
		    'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		    'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
		    'D006_Codigo_CFOP,D006_Descricao,D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
		    'T224_Nome_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
		    'T224_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
		    'T224_Valor_Pendente' => array('titulo' => 'Valor Pend.', 'tipo' => 'moeda'),
			"(SELECT GROUP_CONCAT(DISTINCT T013_Numero_Nota_Fiscal) FROM T225 LEFT JOIN T014 ON T014_T225_Id = T225.T225_Id AND T014_Flag_Cancelada != 'S' LEFT JOIN T013 ON T013_Id = T014_T013_Id AND T013_Flag_Cancelada != 'S' WHERE T225.T225_T224_Id = T224_Id)" => array('titulo' => 'NF', 'tipo' => 'texto'),
			"(SELECT GROUP_CONCAT(DISTINCT D001_Codigo_Produto) {$exFiltro}, (SELECT GROUP_CONCAT(DISTINCT D001_Descricao_Produto) {$exFiltro}" => array('titulo' => 'Produto', 'tipo' => 'texto'),
		    'T224_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
		    	array('title' => 'Todos', 'value' => ''),
		    	array('title' => 'COMPRA', 'value' => '1'),
		    	array('title' => 'TRANSFERÊNCIA', 'value' => '2', )
		    )),
		    'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
		    'analista.C007_Primeiro_Nome' => array('titulo' => 'Analista', 'tipo' => 'texto'),
		    'D163_Status' => array('titulo' => 'Status', 'tipo' => 'texto', 'exibir' => ''),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$extra_ = base64_encode($extra);

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

			$('#xest016IncluirOC').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir ordem de compra', '/est/est016/content/est016contentIncluirOC/', '', [905,470]);
			});

			$('#est016RecebePedidos').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Receber Pedidos', '/est/est016/content/est016ContentReceberPedidos/', '', [400,200]);
			});

			$('#est016ProcessarOCVencida').unbind('click').bind('click', function(data) {
				var prazoVencer = {$g['C031']['PrazoVencerOrdemCompra']};
				var \$buttons = {
					"Todas": function() {
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
					"Apenas filtradas": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/grid_func-ajax/est016ProcessarOCVencida/?ajax=true&sql=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
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
				dialogConfirm('Processar ordens de compra vencidas', 'Irá verificar as ordens de compra com data de emissão a mais de <b>' + prazoVencer +' dias</b> que não estejam <b>FINALIZADAS</b> ou <b>AGUARDANDO MERCADORIA</b>', \$buttons);
			});
		} </script></div>
EOT;

		/**
        * Totais
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
		
		//Botões
		$botoes = array(
            array('titulo' => 'Incluir', 'id' => 'est016IncluirOC', 'janelaUrl' => '/est/est016/content/est016contentIncluirOC/', 'janelaTamanho' => '[905,470]', 'exibirCallback' => 'return $g["C031"]["habilitarbotaoIncluirOC"] == "S";'),
			array('titulo' => 'Processar Vencidos', 'id' => 'est016ProcessarOCVencida'),
        );
        echo Botoes::processar($botoes);
		
		/*
		if (!personalizacaoTotais()) { 
			$total = mysql_query("select count(T224_Id) as itens, sum(T224_Valor_Total) as total, sum(T224_Valor_Pendente) as pendente from T224 where T224_Id in (select T224_Id from T224 $extra)");
			$mTotal = mysql_fetch_array($total);
			$numeroRegistros = $mTotal['itens'];
			$valorTotal = gCorrigeNumero($mTotal['total']);
			$valorPendente = gCorrigeNumero($mTotal['pendente']);
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

	/**
	* Grid Produtos
	*/
	case 'est016grid02':
		/**
		* Definição do Grid (Tabela)
		*/

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'C');

		$T225 = mysql_query("select T225_Item,T225_Id from T225 where T225_T224_Id='{$r_acaoId}' order by T225_Item,T225_Id");
		$item=1;
		while ($mT225=mysql_fetch_array($T225)){
			if ($mT225['T225_Item']!=$item){
			  	mysql_query("update T225 set T225_Item='$item' where T225_Id='{$mT225['T225_Id']}'");
			}
			$item++;
		}

		$sqlT224 = mysql_query("SELECT T224_Flag_Calcular_ST, T224_Flag_Calcular_IPI, T224_Flag_Calcular_ICMS FROM T224 WHERE T224_Id = '{$r_acaoId}'");
		$T224 = mysql_fetch_array($sqlT224);
		
		$editarIPI = ($T224['T224_Flag_Calcular_IPI'] == 'N') ? true : false;
		$editarST = ($T224['T224_Flag_Calcular_ST'] == 'N') ? true : false;
		$editarICMS = ($T224['T224_Flag_Calcular_ICMS'] == 'N') ? true : false;

		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Flag_Cancelada', 'callback' => 'cancelarProdutoOC'),
            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T225_D009_Id', 'colunaExibir' => false, 'colunaTitulo' => 'D009_Id'),
            array('colunaValor' => 'T225_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Item'),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
            array('colunaValor' => 'T225_Codigo_Produto', 'colunaTitulo' => 'Codigo', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'style' => 'width:1%;'),
            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referencia', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Codigo_Substituto', 'colunaTitulo' => 'Cod.Forn', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
            array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'SUBSTRING(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%'),
            //array('colunaValor' => 'concat(D006_Codigo_CFOP,\'(\',D006_Id,\')\')', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);', 'extra' => 'title="teste"'),
            array('colunaValor' => 'concat(D006_Codigo_CFOP,\'(\',D006_Id,\')\')', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T225_D006_Id_Info', 'Demonstrativo CFOP', 'T225_Id', 'left', '', '500')),
            array('colunaValor' => 'T225_D006_Id_Info', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'callback' => 'campoInfo', 'callbackParameter' => array('T225_D005_Id_Info', 'Demonstrativo NCM', 'T225_Id', 'left', '', '500')),
            array('colunaValor' => 'T225_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'background: var(--celula-vermelho);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'T225_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => $destinoProdutos, 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload', 'colunaExibir'=>true),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Est.Tab', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T225_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'T225_T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'callback' => 'estoqueMP', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
            array('colunaValor' => 'sum(T014_Quantidade)*count(distinct T014_Id)/count(*)', 'colunaTitulo' => 'Qte NF', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center', 'callback' => 'qtePedidos', 'style' => 'width:1%'),
            array('colunaValor' => 'T225_Quantidade_Pendente_3(T225_Id)', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_Preco_Original', 'colunaTitulo' => 'Prc.Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Valor_Preco_Original_Bruto', 'colunaTitulo' => 'Original Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Percentual_Desconto_Bruto', 'colunaTitulo' => 'Desc % Bruto', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Valor_Preco_Unitario', 'colunaTitulo' => 'Unitario', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => '', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
            array('colunaValor' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preco Unitario', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => $editarIPI, 'colunaEditarTodos' => $editarIPI, 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
			array('colunaValor' => 'T225_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => $editarICMS, 'colunaEditarTodos' => $editarICMS, 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
			array('colunaValor' => 'T225_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_Percentual_Reducao_ICMS', 'colunaTitulo' => '%Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => '(T225_Valor_ICMS_Substituicao_Retencao/(T225_Valor_Total_Preco+T225_Valor_IPI))*100', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T225A_Aliquota_FCP', 'colunaTitulo' => '%FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T225_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T225_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);'),
			array('colunaValor' => 'T225_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T225_Valor_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => 'DIFAL ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => $editarST, 'colunaEditarTodos' => $editarST, 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'T225_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),           
			array('colunaValor' => 'T225A_Valor_FCP', 'colunaTitulo' => 'FCP R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),            
			array('colunaValor' => 'T225A_Valor_FCP_ST', 'colunaTitulo' => 'FCP ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),         
			array('colunaValor' => 'ifnull(T225_Valor_Frete,0)+ifnull(T225_Valor_Total_Frete,0)', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T225_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'style' => 'background:#A1FCA9'),
            array('colunaValor' => 'T225_Valor_Preco_Venda', 'colunaTitulo' => 'Preço venda', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'Demonstrativo', 'right', 4, 'T00X')),
            //array('colunaValor' => 'T225_Valor_Preco_Venda', 'colunaTitulo' => 'Preço venda', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outros/demonstrativoPrecoVenda/', 'Demonstrativo', 'right', 4, 'T00X', '', '-1')),
            array('colunaValor' => 'IF(T006_Id>0,CONCAT("T006-",T006_Id),CONCAT("T004-",T004_Id)) as Demonstrativo', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'IF(T006_Id > 0, IF(T006_T219_Id > 0 AND T006_Valor_Preco_Cotacao > 0, 675, 450), IF(T004_T219_Id > 0 AND T004_Valor_Preco_Cotacao > 0, 675, 450)) as width', 'colunaTitulo' => 'width', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Valor_Preco_Venda_Negociado', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);'),
            array('colunaValor' => 'if(T225_Valor_Preco_Venda_Negociado>0,T225_Valor_Preco_Venda_Negociado-T225_Valor_Preco_Venda,0)', 'colunaTitulo' => 'Sobra', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);'),
            array('colunaValor' => 'concat(replace(replace(round(((T225_Valor_Preco_Venda/T225_Valor_Custo_Unitario)-1)*100),",",""),".",","),"","%")', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tab', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo Medio', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right'),
            array('colunaValor' => '(T225_Valor_Preco_Bruto*T225_Quantidade) as T225_Valor_Total_Bruto', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Percentual_Frete', 'colunaTitulo' => '%Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'T225_Percentual_Financeiro', 'colunaTitulo' => '%Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'T225_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_Percentual_ST', 'colunaTitulo' => '% ST', 'colunaAlinhar' => 'center', 'colunaExibir'=>true, 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar'=>true),
            array('colunaValor' => 'T225_ICMS_Inter_Estadual', 'colunaTitulo' => '%ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_ST_ICMS_Interno', 'colunaTitulo' => '%ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_Percentual_Reducao_ICMS_ST', 'colunaTitulo' => '% Red ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225A_Percentual_Reducao_ICMS_IE_ST', 'colunaTitulo' => '% Red ICM IE ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225A_Percentual_Reducao_ICMS_Int_ST', 'colunaTitulo' => '% Red ICM Int ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),            
			array('colunaValor' => 'T225A_Valor_Base_FCP', 'colunaTitulo' => 'Base FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T225A_Valor_Base_FCP_ST', 'colunaTitulo' => 'Base FCP ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),			
			array('colunaValor' => 'T225_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.Ped.', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T225_Item_Pedido_Compra', 'colunaTitulo' => 'Item.Ped.', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T225_Flag_Excluir', 'colunaTitulo' => 'T225_Flag_Excluir', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Dir001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct T013_Numero_Nota_Fiscal) as T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'T004_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T014_Id', 'colunaTitulo' => 'T014_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T219_T218_Id', 'colunaTitulo' => 'Cotação', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação', '/est/est015/content/est015ContentEditarOrcamento/', 'T219_T218_Id', '1000,583')),
            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'Empresa', 'colunaAlinhar' => 'center'),
        );

		if($g['C031']['habilitaComprasImportacao'] == 'S'){
			$sqlFlagImport    = mysql_query("select T224A_Flag_Importacao from T224A where T224A_T224_Id='{$r_T224_Id}'");
			$resultFlagImport = mysql_fetch_array($sqlFlagImport);

			if($resultFlagImport['T224A_Flag_Importacao'] == 'S'){
				array_push($grid, array('colunaValor' => 'T225A_Valor_Dolar_Unitario', 'colunaTitulo' => 'Dólar Unitario', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => false), array('colunaValor' => 'T225A_Valor_Dolar_Total', 'colunaTitulo' => 'Dólar Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => false));
			}
		}

		/**
		*
		* Definição da SQL
		*/
		$from = 'T225';
		// or T004_T219_Id=T219_Id 
		// or T006_T219_Id=T219_Id 
		$extra = <<<EOT
			left join T224 on T224_Id=T225_T224_Id
			left join D024 on D024_Id=T225_D024_Id
			left join D037 on D037_Id=T225_D037_Id
			left join D009 on D009_Id=T225_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=T225_D006_Id
			left join D005 on D005_Id=T225_D005_Id
			left join T014 on T014_T225_Id=T225_Id AND T014_Flag_Cancelada != 'S'
			left join T013 on T013_Id=T014_T013_Id and T013_Flag_Cancelada != 'S'
			left join T219 on T219_Id=T225_T219_Id
			LEFT JOIN T004 on T219_T004_Id=T004_Id
			LEFT JOIN T006 on T219_T006_Id=T006_Id
			LEFT JOIN T225A on T225A_T225_Id=T225_Id
			where 
			T225_T224_Id = '{$r_acaoId}'
			group by T225_Id
EOT;
		if (!empty($xConsulta_Clausula_Filtro)) {
			$extra .= <<<EOT
				      and $xConsulta_Clausula_Filtro
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
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		function gridLinha($array) {
			if(!empty($array['todosCampos']['T013_Numero_Nota_Fiscal'])){
		    	return 'linhaCinza';  
			} else if($array['todosCampos']['T225_Flag_Cancelada']=='S'){
		    	return 'linhaVermelhoClaro';  
			} else if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		    	return 'linhaVermelho';  
			}
		}
		//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T225_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/est/est016/content/est016ContentEditarProduto/', '&acaoId=' + encodeURIComponent(acaoId) + '&T224_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela={$from}', [600,400]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});

			function colunaEditarCallbackJSReload() {
				divRefresh('{$r_divReload}');
			}
			
			// Função JS executada como callback do editar campo (colunaEditarCallbackJS)
			function T225_Calculo_Valor_Unitario(T225_Id, campoEditado, valorAntigo, novoValor) {
				T225_Id = T225_Id.split('|');
				T225_Id = T225_Id[0];
				T225_Id = T225_Id.replace(/T225_Id-/, '');
					if(campoEditado == 'T225_Valor_Preco_Bruto'){
						$.getJSON('/est/est016/grid_func-ajax/est016aplicarPercentualDesconto/?ajax=true&T225_Id='    + encodeURIComponent(T225_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								//divRefresh('{$g['divId']}');
								divRefresh('{$g['divId']}', true, false, false, T225_Id, 'T225_Id'); // grid
								divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columnsPedido:eq(1)').attr('id'), true, false, 2); // totais
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
			}
			
			function colunaEditarCallbackJS_T225_Percentual_Desconto(T225_Id, campoEditado, valorAntigo, novoValor) {
					T225_Id = T225_Id.split('|');
					T225_Id = T225_Id[0];
					T225_Id = T225_Id.replace(/T225_Id-/, '');
					if(campoEditado == 'T225_Percentual_Desconto'){
						$.getJSON('/est/est016/grid_func-ajax/est016aplicarPercentualDesconto/?ajax=true&T225_Id='    + encodeURIComponent(T225_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if(campoEditado == 'T225_Percentual_Desconto_Bruto'){
						$.getJSON('/est/est016/grid_func-ajax/est016aplicarPercentualDesconto/?ajax=true&T225_Id='    + encodeURIComponent(T225_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if(campoEditado == 'T225_Valor_Preco_Sem_Desconto_Unitario'){
						$.getJSON('/est/est016/grid_func-ajax/est016aplicarPercentualDesconto/?ajax=true&T225_Id='    + encodeURIComponent(T225_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if(campoEditado == 'T225_Valor_Preco_Unitario'){
						$.getJSON('/est/est016/grid_func-ajax/est016aplicarPercentualDesconto/?ajax=true&T225_Id='    + encodeURIComponent(T225_Id) + 
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

					divRefresh('{$g['divId']}', true, false, false, T225_Id, 'T225_Id'); // grid
					divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columnsPedido:eq(1)').attr('id'), true, false, 2); // totais
				}
				function gridTheadCallback() {
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
							.removeAttr('onclick')
							.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est016/grid_func-ajax/est016MarcarTodosExcluir/?ajax=true&T224_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
										divRefresh('{$r_divReload}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est016/grid_func-ajax/est016DesmarcarTodosExcluir/?ajax=true&T224_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {
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
				function colunaEditarCallbackJS_T225_Item(T225_Id, campoEditado, valorAntigo, novoValor) {
					T225_Id = T225_Id.replace(/T225_Id-/, '');
					$.getJSON('/est/est016/grid_func-ajax/processarNumeroItemOrdemCompra/?ajax=true&T225_Id='    + encodeURIComponent(T225_Id) + 
																				      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				      '&novoValor='			+ encodeURIComponent(novoValor) +  
																				      '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}', true, false, false, T225_Id, 'T225_Id'); // grid
							divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columnsPedido:eq(1)').attr('id'), true, false, 2); // totais
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
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
		break;

		/**
		* Grid Serviços
		*/
		case 'est016grid03':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'excluirServico'),
			array('colunaValor' => 'T230_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true),
			array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
			array('colunaValor' => "concat(T230_Titulo, '<br />', T230_Descricao) as Servico", 'colunaTitulo' => 'Serviço'),
			array('colunaValor' => 'T212_Nome', 'colunaTitulo' => 'Tipo'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			//array('colunaValor' => 'T230_Quantidade', 'colunaTitulo' => 'Quantidade'),
			array('colunaValor' => 'T230_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true),
			//array('colunaValor' => 'T230_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'T230_T224_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
			array('colunaValor' => 'T230_T223_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
			//array('colunaValor' => 'T230_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'est016_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
			array('colunaValor' => 'T230_D116_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T230_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado'),
			//array('colunaValor' => 'T230_Status', 'colunaTitulo' => 'Status'),
			array('colunaValor' => 'T230_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
			);
	
			/**
				*
				* Definição da SQL
				*/
			$from = 'T230';
	
			$extra = <<<EOT
					LEFT JOIN T225 ON T225_Id=T230_T225_Id
					LEFT JOIN D009 ON D009_Id=T225_D009_Id
					LEFT JOIN D049 ON D009_D049_Id=D049_Id
					LEFT JOIN D001 ON D001_Id=D049_D001_Id
					LEFT JOIN T212 ON T230_T212_Id=T212_Id 
					WHERE T230_T224_Id='{$r_acaoId}'
EOT;
	

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T230_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
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

		/**
		* Grid Materiais
		*/
	case 'est016grid04':
		/**
		* Definição do Grid (Tabela)
		*/

		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre",  'callback' => 'gExcluirLinha', 'callbackParameter' => array('T149_Id', $g['divId'])),
		array('colunaValor'=> 'T149_Id', 'colunaTitulo'=>'ID', 'chavePrimaria'=>true, 'colunaExibir' => false),
		array('colunaValor'=> 'T149_D009_Id', 'colunaTitulo'=>'IdEquip', 'colunaExibir' => false),		
		array('colunaValor'=> 'T149_Quantidade', 'colunaTitulo'=>'Quantidade','colunaEditar'=>true),
		array('colunaValor'=> 'T149_D037_Id', 'colunaTitulo' => 'Unidade', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true), 'callback' => 'buscaUnidade'),
		array('colunaValor'=> 'D037_Unidade', 'colunaTitulo'=>'Unidade','colunaExibir'=>false),
		array('colunaValor'=> 'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição'),
		array('colunaValor'=> 'D082_Marca', 'colunaTitulo'=>'Marca'),
		array('colunaValor'=> 'T149_T230_Id', 'colunaTitulo'=>'IdUnidade', 'colunaExibir' => false),
		);

		$from = "T149";

		$extra = <<<EOT
				left join D009 on D009_Id=T149_D009_Id
				LEFT JOIN D049 on D049_Id=D009_D049_Id
				LEFT JOIN D001 on D001_Id=D049_D001_Id
				LEFT JOIN D082 on D082_Id=D049_D082_Id					
	    		left join D037 on D037_Id=T149_D037_Id
	    		left join T230 on T230_Id=T149_T230_Id
	    		where
	    		T149_T230_Id='$r_T230_Id'
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

		/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			var id = $(this).closest('tr').attr('id').replace(/T143_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/est/est009/content/est009content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&D106_Id=' + encodeURIComponent({$r_acaoId}), [500,390]);
			});
			} </script></div>
			EOT;
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

		/**
			* BotÃµes

			// definiÃ§ão dos botÃµes
			//$botoesDir = '<button type="button" id="est016IncluirLogistica">Incluir</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');

			// AÃ§Ãµes dos botÃµes
			$novoIdTelaForm = uniqid();
			/**
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est016IncluirLogistica').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Materiais', '/est/est009/content/est009content02/',  '&D106_Id=' + encodeURIComponent({$r_acaoId}), [500,390]);
			});
			} </script></div>
			EOT;
			*/
		break;

		/**
			* Grid Processos
			*/
	case 'est016grid05':

		/**
			* Definição do Grid (Tabela)
			*/
		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T148_Id', $g['divId'])),
		array('colunaValor' => 'T148_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
		array('colunaValor' => 'T148_T230_Id', 'colunaTitulo' => 'IdServico', 'colunaExibir' => false),
		array('colunaValor' => 'T148_D110_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
		array('colunaValor' => 'T148_Titulo', 'colunaTitulo' => 'Título', 'colunaEditar'=>true),
		array('colunaValor' => 'T148_Descricao', 'colunaTitulo' => 'Descrição', 'colunaEditar'=>true),
		);

		/**
			*
			* Definição da SQL
			*/
		$from = 'T148';

		$extra = <<<EOT
				left join T230 on T230_Id=T148_T230_Id
				left join D110 on D110_Id=T148_D110_Id
				where 
				T148_T230_Id = {$r_acaoId}
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

		/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			var id = $(this).closest('tr').attr('id').replace(/T230_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/est/est010/content/est010content03/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,260]);
			});
			} </script></div>
			EOT;
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
		$novoIdTelaForm = uniqid();
	break;
		
		case 'est016GridPagamento':
			global $g;
			$sqlT224 = "SELECT T224_Pgto_Data, T224_Pgto_C007_Id, C007_Login FROM T224 LEFT JOIN C007 ON C007_Id=T224_Pgto_C007_Id WHERE T224_Id='$r_acaoId'";
			$resultT224 = mysql_query($sqlT224);
			$linhaT224 = mysql_fetch_array($resultT224);
			$dataGerada = gCorrigeData($linhaT224['T224_Pgto_Data']);
			$user = strtoupper($linhaT224['C007_Login']);
			if(($user != "") || (!empty($user))){
				echo "Parcelas Geradas por <b>".$user."</b> em <b>".$dataGerada."</b>";
			}
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T226_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T226_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T226_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T226_Prazos_Dias"),
				array('colunaValor' => 'T226_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T226_Prazos_Dias", 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'est016_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "est016_Dia_Semana"),
				array('colunaValor' => 'T226_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T226_Prazos_Dias"),
				array('colunaValor' => 'T226_Taxa_Juros', 'colunaTitulo' => '%Juros', 'style'=>'width:1px'),
				//array('colunaValor' => 'T226_Valor_Juros', 'colunaTitulo' => 'Juros'),
				array('colunaValor' => 'T226_D031_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true,'colunaEditarJSCallback' => "colunaEditarCallbackJS_T226_Prazos_Dias", 'colunaEditarGeraSelect' => array('D031', 'D031_Id', 'D031_Portador', 'ORDER BY D031_Portador', true), 'callback' => 'T226_D031_Id_Portador'),
				array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'est016_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "est016_Detalhes"),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T226';

			$extra = <<<EOT
				left join D031 on D031_Id=T226_D031_Id
				WHERE T226_T224_Id = {$r_acaoId}
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
					var id = $(this).closest('tr').attr('id').replace(/T226_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T226_Prazos_Dias(T226_Id, campoEditado, valorAntigo, novoValor) {
					T226_Id = T226_Id.replace(/T226_Id-/, '');
					if (campoEditado == 'T226_Prazos_Dias') {
						$.getJSON('/est/est016/grid_func-ajax/atualizarParcelas/?ajax=true&T226_Id='      		+ encodeURIComponent(T226_Id) + 
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
					} else if (campoEditado == 'T226_Data_Vencimento') {
						$.getJSON('/est/est016/grid_func-ajax/atualizarParcelas/?ajax=true&T226_Id='      		+ encodeURIComponent(T226_Id) + 
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
					} else if (campoEditado == 'T226_Valor_Parcela') {
						$.getJSON('/est/est016/grid_func-ajax/atualizarParcelas/?ajax=true&T226_Id='      		+ encodeURIComponent(T226_Id) + 
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
					} else if (campoEditado == 'T226_D031_Id') {
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
			
		case 'est016GridEmail':
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
				<button type="button" id="etc006VerificarEmailsEnviadosest001">Verificar Novos Emails</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006EnviarEmail2').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T224_Id=' + encodeURIComponent('$r_acaoId'), [900,550]);
				});
				$('#etc006VerificarEmailsEnviadosest001').unbind('click').bind('click', function(data) {
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
		
		case 'est016GridDocumentos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T227_Id', $g['divId'])),
			array('colunaValor' => 'T227_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T227_T224_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
			array('colunaValor' => 'T227_Nome_Arquivo', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'T227_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T227_Data_Hora_Upload', 'colunaTitulo' => 'Data Upload', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T227';

			$extra = <<<EOT
				left join C007 on C007_Id=T227_C007_Id
				where 
				(T227_T224_Id = {$r_acaoId})
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
					var id = $(this).closest('tr').attr('id').replace(/T227_Id-/, '');
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
		
		case 'est016GridEntregaServicos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T231_Id', $g['divId'])),
			array('colunaValor' => 'T231_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T231_D116_Id', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
			array('colunaValor' => 'D116_Id', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculo'),
			array('colunaValor' => 'T231_T224_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir'=>false),
			array('colunaValor' => 'T231_T230_Id', 'colunaTitulo' => 'Serviço', 'colunaExibir'=>false),
			array('colunaValor' => 'T231_Data_combinada', 'colunaTitulo' => 'Data Combinada', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
			array('colunaValor' => 'T231_Hora_combinada', 'colunaTitulo' => 'Hora Combinada', 'callback'=>'gCorrigeTempo', 'colunaEditar'=>true),
			array('colunaValor' => 'T231_Observação', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T231';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		left join D116 on D116_Id=T231_D116_Id
		WHERE T231_T224_Id={$r_acaoId}
		
EOT;

		// apenas para o grid não colar nas tabs
		echo <<<EOT
			<div style="margin-top:30px">			
			</div>
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="cad047FormCadastroTransportesTipos">Incluir</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#cad047FormCadastroTransportesTipos').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Tipos de Transporte', '/cad/cad047/content/cad047ContentTransportesTiposIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		
		/**
		* Filtro
		*/
		// $gridFiltro = array(
			// //'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			// 'D036_Tipo_Transporte' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		// );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
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
					// var chavePrimaria = 'D036_Id';	
					// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// var id = $(this).closest('tr').attr('id').split('|');
					// var acaoId = id[0].replace(chavePrimaria + '-', '');
					// // Ação: abrir nova janela
	        		// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Tipos de Transporte - ID: ' + acaoId, '/cad/cad047/content/cad047ContentTransportesTiposIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
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
	
	case 'est016GridHistorico':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T228_Id', $g['divId'])),
			array('colunaValor' => 'T228_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T228_T224_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir'=>false),
			array('colunaValor' => 'T228_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData','style' => 'width:105px;'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T228_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T228_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T228';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		left join C007 on C007_Id=T228_C007_Id
		WHERE T228_T224_Id={$r_acaoId}
		order by T228_Data,T228_Id desc
		
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="cad047FormCadastroTransportesTipos">Incluir</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#cad047FormCadastroTransportesTipos').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Tipos de Transporte', '/cad/cad047/content/cad047ContentTransportesTiposIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		// $gridFiltro = array(
			// //'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			// 'D036_Tipo_Transporte' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		// );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
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
					// var chavePrimaria = 'D036_Id';	
					// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// var id = $(this).closest('tr').attr('id').split('|');
					// var acaoId = id[0].replace(chavePrimaria + '-', '');
					// // Ação: abrir nova janela
	        		// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Tipos de Transporte - ID: ' + acaoId, '/cad/cad047/content/cad047ContentTransportesTiposIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
		        // });
				// $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			// } </script></div>
// EOT;
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
	
	case 'est016gridTrocas':
		
		$T224_Id = (isset($r_T224_Id)) ? $r_T224_Id : false;
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
			WHERE T222_T224_Id = '{$r_acaoId}'
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
    
    case 'est016GridProdutosPedido':

		$filtroObrigatorio = true;

    	$gridModelos = array(
    		'FOLLOWUP' => 'FOLLOW-UP',
    		'RECEBIMENTO' => 'RECEBIMENTO NFE'
    	);

        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

        // --------------------------------------------------------------------------------------------------------
		// Data: 30/08/2018
		// Programador: OSNI
		// Método: est016GridProdutosPedido
		// Chamado: 32799
		// Descricao: Criar uma coluna em ESTOQUE > SRM > ORDEM COMPRA > PRODUTOS (GRID PADRÃO) com o parâmetro de Locação
		// --------------------------------------------------------------------------------------------------------

        if (modoModelo('FOLLOWUP')) {
	        $grid = array(
	            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T225_Id', $g['divId']), 'colunaExibirNivel' => 4, 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T225_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Codigo_Produto', 'colunaTitulo' => 'Codigo', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array(
	                 'Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),
	            ),
	            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referencia', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Codigo_Substituto', 'colunaTitulo' => 'Cod.Fornec', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Produto'),
	            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
	            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
	            array('colunaValor' => 'T225_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_T206_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
	            array('colunaValor' => 'T225_Quantidade_Pendente_3(T225_Id)', 'colunaTitulo' => 'Qtde Pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'color: var(--BGVermelho)'),
	            array('colunaValor' => 'T225_Valor_Preco_Unitario', 'colunaTitulo' => 'Unitario', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => '', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
	            array('colunaValor' => 'T225_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Valor_Preco_Unitario*T225_Quantidade_Pendente_3(T225_Id)', 'colunaTitulo' => 'Valor pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaTotal' => 'S', 'style' => 'background-color: var(--celula-amarelo-claro);color: var(--BGVermelho)'),
	            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Observacao_Entrega', 'colunaTitulo' => 'Entreg<br>dias', 'style' => 'background: var(--celula-amarelo-claro);;', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
	            array('colunaValor' => 'datediff(curdate(),T224_Data_Emissao)', 'colunaTitulo' => 'Prazo<br>real', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => '(T224_Data_Emissao + INTERVAL T225_Observacao_Entrega DAY) as dataPrevisao', 'colunaTitulo' => 'Previsão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T225_Observacao', 'colunaTitulo' => 'Observação OC', 'style' => 'background: var(--celula-amarelo-claro);', 'colunaEditar' => true),
	            array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T013_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T013_Data_Entrada', 'colunaTitulo' => 'Entrada', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibirNivel' => 4, 'colunaExibir' => false),
	            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Flag_Excluir', 'colunaTitulo' => 'T225_Flag_Excluir', 'colunaExibir' => false),
	            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibirNivel' => 4, 'colunaExibir' => false),
	            array('colunaValor' => 'Comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
	            array('colunaValor' => 'concat(\'OC: \',T224_Id,\'-\',ifnull(D024_Nome_Empresa,\'\'), \' - \',ifnull(D018_UF,\'\'),\'-\',convert(substr(ifnull(D013_Nome_Contato,\'\'),1,10) USING utf8),\'-\',ifnull(D013_E_Mail,\'\'),\' - \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\'),\' \',ifnull(D013_DDD_Celular,\'\'),\'-\',ifnull(D013_Celular,\'\'),\' (\',ifnull(Comprador.C007_Primeiro_Nome,\'\'),\')\') as oc', 'colunaTitulo' => 'oc', 'colunaExibir' => false),
	            array('colunaValor' => 'concat(convert(T224_Nome_Status using utf8),\'<br><font style="font-size:10px"><i>\',convert(ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\') using utf8))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;width:220px;white-space:normal;'),
	        	array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center', 'callback' => 'qtePedidos', 'style' => 'width:1%'),
	        );
		} else if (modoModelo('RECEBIMENTO')) {
			$grid = array(
	            array('colunaValor' => 'T225_T014_Id', 'colunaTitulo' => 'T225_T014_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T014_T225_Id', 'colunaTitulo' => 'T014_T225_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
	            array('colunaValor' => 'T014_Id', 'colunaTitulo' => 'T014_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'Comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Cód. Fornec.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
	            
	            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM OC', 'style' => 'background:#F4F9CA', 'callback' => 'T014_Classificacao_Fiscal'),
				array('colunaValor' => 'T014_Classificacao_Fiscal', 'colunaTitulo' => 'NCM NF', 'style' => 'background:#F4F9CA'),

				array('colunaValor' => 'D6OC.D006_Codigo_CFOP as D6OC_Codigo', 'colunaTitulo' => 'CFOP OC', 'style' => 'background:#F4F9CA', 'callback' => 'D006_Codigo_CFOP'),
				array('colunaValor' => 'D6NF.D006_Codigo_CFOP as D6NF_Codigo', 'colunaTitulo' => 'CFOP NF', 'style' => 'background:#F4F9CA'),
	            
	            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	            
	            array('colunaValor' => 'T225_Quantidade+0', 'colunaTitulo' => 'Qte OC', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2'),
				array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Qte NF', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2'),
	            //array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Quant Pend', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
	            //array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarJSCallback' => 'T225_Gravar_Totalizacao'),
	            //array('colunaValor' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Unitário OC', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right', 'style' => 'background:#B6F9B6', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Unitário OC', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-verde)'),
		        array('colunaValor' => 'T014_Valor_Desconto_Unitario*-1', 'colunaTitulo' => 'Desconto', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T014_Valor_Custo_Unitario', 'colunaTitulo' => 'Unitário NF', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right', 'style' => 'background:#B6F9B6'),
	            array('colunaValor' => 'T014_Valor_Total_Preco_Recebida', 'colunaTitulo' => 'Total OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_Total_Preco_Recebida', 'style' => 'background:#FCFAD9'),
	            array('colunaValor' => 'T014_Valor_Total_Custo', 'colunaTitulo' => 'Total NF', 'colunaAlinhar' => 'right', 'style' => 'background:#FCFAD9'),
	            //array('colunaValor' => '(SELECT SUM(T014_Valor_ICMS_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014A.T014_Id OR T014B.T014_Id_Duplicado=T014A.T014_Id OR T014B.T014_Id=T014A.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014A.T014_Id_Duplicado) as T014_Valor_ICMS_Recebida', 'colunaTitulo' => 'ICMS OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_ICMS_Recebida', 'style' => 'background:#DCD6F9'),
				array('colunaValor' => 'T014_Valor_ICMS_Recebida', 'colunaTitulo' => 'ICMS OC R$', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_ICMS_Recebida', 'style' => 'background:#DCD6F9'),
				array('colunaValor' => 'T014_Valor_ICMS', 'colunaTitulo' => 'ICMS NF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:#DCD6F9'),
	            //array('colunaValor' => '(SELECT SUM(T014_Valor_IPI_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014A.T014_Id OR T014B.T014_Id_Duplicado=T014A.T014_Id OR T014B.T014_Id=T014A.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014A.T014_Id_Duplicado) as T014_Valor_IPI_Recebida', 'colunaTitulo' => 'IPI OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_IPI_Recebida', 'style' => 'background:#CDF7F5'),
				array('colunaValor' => 'T014_Valor_IPI_Recebida', 'colunaTitulo' => 'IPI OC R$', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_IPI_Recebida', 'style' => 'background:#CDF7F5'),
				array('colunaValor' => 'T014_Valor_IPI', 'colunaTitulo' => 'IPI NF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:#CDF7F5'),
	            //array('colunaValor' => '(SELECT SUM(T014_Valor_ICMS_Substituicao_Retencao_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014A.T014_Id OR T014B.T014_Id_Duplicado=T014A.T014_Id OR T014B.T014_Id=T014A.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014A.T014_Id_Duplicado) as T014_Valor_ICMS_Substituicao_Retencao_Recebida', 'colunaTitulo' => 'ST OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_ICMS_Substituicao_Recebida', 'style' => 'background:#CDF7D8'),
				array('colunaValor' => 'T014_Valor_ICMS_Substituicao_Retencao_Recebida', 'colunaTitulo' => 'ST OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_ICMS_Substituicao_Recebida', 'style' => 'background:#CDF7D8'),
				array('colunaValor' => 'T014_Valor_ICMS_Substituicao', 'colunaTitulo' => 'ST NF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:#CDF7D8'),
	            array('colunaValor' => 'T014_Valor_Total_Frete_Recebida', 'colunaTitulo' => 'Frete OC', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:#FCFAC7;'),
		        array('colunaValor' => 'T014_Valor_Frete', 'colunaTitulo' => 'Frete NF', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:#FCFAC7;'),
	            array('colunaValor' => 'T014_Valor_Custo_Unitario_Recebida', 'colunaTitulo' => 'Custo OC', 'colunaAlinhar' => 'right', 'callback' => 'T014_Valor_Custo_Unitario_Recebida', 'style' => 'background:lightblue'),
	            array('colunaValor' => 'T014_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo NF', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '6', 'style' => 'background:lightblue'),
	            array('colunaValor' => 'T014_Flag_Divergencia', 'colunaTitulo' => 'Divergência', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'concat(\'NF: \',T013_Numero_Nota_Fiscal,\'-\',D024_Nome_Empresa,\'-\',D018_UF) as nf', 'colunaTitulo' => 'nf', 'colunaExibir' => false),
	            array('colunaValor' => 'concat(convert(T224_Nome_Status using utf8),\'<br><font style="font-size:10px"><i>\',convert(ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\') using utf8))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;width:220px;white-space:normal;'),
	        	array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center', 'callback' => 'qtePedidos', 'style' => 'width:1%'),
			);
			if($g['nivelAlcada'] == '4'){
				$grid[] = array('colunaValor' => 'T225_Flag_Autorizado', 'colunaTitulo' => 'Ok', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array('T225_Flag_Autorizado', 'N|S'));
			} else {
				$grid[] = array('colunaValor' => 'T225_Flag_Autorizado', 'colunaTitulo' => 'Ok', 'colunaAlinhar' => 'center');
			}
			
		} else {
			$grid = array(
	            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T225_Id', $g['divId']), 'colunaExibirNivel' => 4),
	            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left'),
	            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T225_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Codigo_Produto', 'colunaTitulo' => 'Codigo', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
	            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referencia', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Codigo_Substituto', 'colunaTitulo' => 'Cod.Fornec', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Produto'),
	            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
	            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
	            array('colunaValor' => 'T225_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_T206_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'callback' => 'estoqueMP', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
	            array('colunaValor' => 'T225_Valor_Preco_Original', 'colunaTitulo' => 'Prc.Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Valor_Preco_Original_Bruto', 'colunaTitulo' => 'Original Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Percentual_Desconto_Bruto', 'colunaTitulo' => 'Desc % Bruto', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
	            array('colunaValor' => 'T225_Valor_Preco_Unitario', 'colunaTitulo' => 'Unitario', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => '', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto'),
	            array('colunaValor' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preco Unitario', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Percentual_Desconto', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
	            array('colunaValor' => 'T225_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'style' => 'background: var(--celula-amarelo-claro);'),
	            array('colunaValor' => 'T225_Observacao', 'colunaTitulo' => 'Observação', 'style' => 'background: var(--celula-amarelo-claro);'),
	            array('colunaValor' => 'T225_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
	            array('colunaValor' => 'T225_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
	            array('colunaValor' => 'T225_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
	            array('colunaValor' => 'T225_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
	            array('colunaValor' => 'T225_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 4),
	            array('colunaValor' => 'T225_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo Cot', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaExibirNivel' => 4),
	            array('colunaValor' => 'T225_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#BADCFC', 'colunaExibirNivel' => 4),
	            array('colunaValor' => 'T225_Valor_Preco_Venda', 'colunaTitulo' => 'Preço venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaExibirNivel' => 4),
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
				array('colunaValor' => 'T225_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
				array('colunaValor' => 'D006_Flag_Somar_DIFAL_Custo', 'colunaTitulo' => 'Calcular DIFAL', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
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
	            array('colunaValor' => 'concat(convert(T224_Nome_Status using utf8),\'<br><font style="font-size:10px"><i>\',convert(ifnull(T224_Liberou_Alcada_Observacao_Nao_Liberado,\'\') using utf8))', 'colunaTitulo' => 'Status', 'style' => 'max-width:220px;width:220px;white-space:normal;'),
	        	array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center', 'callback' => 'qtePedidos', 'style' => 'width:1%'),
	            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação'),
	        );
		}
        
        /**
         * Definição da SQL
         */
        $from = "T225";
    
    	if (modoModelo('FOLLOWUP')) {
	    	$extra = <<<EOT
	    		left join T224 on T224_Id=T225_T224_Id
				left join D024 on D024_Id=T224_D024_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D037 on D037_Id=T225_D037_Id
				left join D009 on D009_Id=T225_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D006 on D006_Id=T225_D006_Id
				left join D005 on D005_Id=T225_D005_Id
				left join T014 on T014_T225_Id=T225_Id
				left join T013 on T013_Id=T014_T013_Id
				left join D013 on D013_Id=T224_D013_Id
				left join C007 as Comprador on Comprador.C007_Id=T224_C007_Id_Vendedor_Interno
				left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
				where T225_Quantidade_Pendente_3(T225_Id)>0
				and T224_C004_Id = '{$g['empresaAtual']}'
				and T225_Flag_Cancelada!='S'
				and T224_Flag_Status!=8
				and T224_Flag_Status!=2
				and T224_Flag_Status!=1
				and T224_Flag_Tipo!=2
				and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
EOT;
		} else if (modoModelo('RECEBIMENTO')) {
			$extra = <<<EOT
				left join D009 on D009_Id=T225_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D037 on D037_Id=T225_D037_Id
				left join T224 on T224_Id=T225_T224_Id
				left join T218 on T218_Id=T224_T218_Id
				left join D024 on D024_Id=T224_D024_Id
				left join D018 on D018_Id=D024_D018_Id
				left join C007 as Comprador on Comprador.C007_Id=T224_C007_Id_Vendedor_Interno
				left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
				left join D021 on D021_Id=D024_D021_Id
				left join C004 on C004_Id=T224_C004_Id
				left join D005 on D005_Id=T225_D005_Id
				left join T014 T014A on T014_T225_Id=T225_Id
				left join T013 on T013_Id=T014_T013_Id
				left join D083 on D083_D049_Id=D049_Id and D083_D024_Id=T224_D024_Id
				left join D006 as D6OC on T225_D006_Id=D6OC.D006_Id
				left join D006 as D6NF on T014_D006_Id_Original=D6NF.D006_Id
				where T014_Flag_Divergencia='S'
				and T224_C004_Id = '{$g['empresaAtual']}'
				and T013_Flag_Travar_Nota!='S'
				and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
				group by T014_Id
EOT;
		} else {
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
				left join T066 on T066_D009_Id=D009_Id
				left join D004 on D004_Id=T066_D004_Id
				left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
				left join C007 as Comprador on Comprador.C007_Id=T224_C007_Id_Vendedor_Interno
				left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
				where T224_C004_Id = '{$g['empresaAtual']}'
				and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
				group by T225_Id
				
EOT;
			$extraTotal = <<<EOT
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
				left join T066 on T066_D009_Id=D009_Id
				left join D004 on D004_Id=T066_D004_Id
				left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
				left join C007 as Comprador on Comprador.C007_Id=T224_C007_Id_Vendedor_Interno
				left join C007 as Supervisor on Supervisor.C007_Id=Comprador.C007_C007_Id
				where T224_C004_Id = '{$g['empresaAtual']}'
				and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', Comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (Supervisor.C007_Id='{$g['usuarioAtual']}' or Comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
				
EOT;
		}

        /*
        * Pode ser definido uma classe para cada linha do grid
        */
        if (modoModelo('FOLLOWUP')) {
        	function gridLinha($array) {
        		global $g;
        		if($array['todosCampos']['datediff(curdate(),T224_Data_Emissao)']==$array['todosCampos']['T225_Observacao_Entrega']){
        			return 'linhaAzul';
        		} else if($array['todosCampos']['datediff(curdate(),T224_Data_Emissao)']<$array['todosCampos']['T225_Observacao_Entrega']){
        			return 'linhaVerde';
        		} else if($array['todosCampos']['datediff(curdate(),T224_Data_Emissao)']>$array['todosCampos']['T225_Observacao_Entrega']){
        			return 'linhaVermelho';
        		}
        	}
        } else if (modoModelo('RECEBIMENTO')) {
        	function gridLinha($array) {
        		global $g;
        		if(!empty($array['todosCampos']['T013_Numero_Nota_Fiscal'])){
      				//return 'linhaCinza';
        		}
        	}
        } else {
	        function gridLinha($array) {
				if($array['todosCampos']['T224_Flag_Status']=='5'){
					return 'linhaCinza';
				} else if(!empty($array['todosCampos']['T013_Numero_Nota_Fiscal'])){
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
        if (modoModelo('FOLLOWUP')) {
	        $gridFiltro = array(
	            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
	            'T224_Id' => array('titulo' => 'OC', 'tipo' => 'numero'),
	            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
	            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
	            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
	            'Comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
	            'T225_Codigo_Produto' => array('titulo' => 'Código produto', 'tipo' => 'numero'),
	            'T225_Descricao_Produto' => array('titulo' => 'Descrição produto', 'tipo' => 'texto'),
	            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
	            '(T224_Data_Emissao + INTERVAL T225_Observacao_Entrega DAY)' => array('titulo' => 'Data previsão', 'tipo' => 'data'),
	            'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
	            'oc' => array('titulo' => 'oc', 'campos' => array(
	                array('title' => 'oc', 'campoAgrupar' => 'oc', 'campoMostrar' => 'oc', 'campoOrdem' => '', 'campoSelecionado' => true),
	            ), 'tipo' => 'agrupar', 'exibir' => false),
	        );
		} else if (modoModelo('RECEBIMENTO')) {
			$gridFiltro = array(
	            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
	            'T224_Id' => array('titulo' => 'OC', 'tipo' => 'numero'),
	            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
	            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
	            'D6OC.D006_Descricao,D6OC.D006_Tipo_CFOP,D6NF.D006_Descricao,D6NF.D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
	            'Comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
	            'T225_Codigo_Produto' => array('titulo' => 'Código produto', 'tipo' => 'numero'),
	            'T225_Descricao_Produto' => array('titulo' => 'Descrição produto', 'tipo' => 'texto'),
	            'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
	            'T225_Flag_Autorizado' => array('titulo' => 'Autorizado? (S/N)', 'tipo' => 'texto'),
	            'nf' => array('titulo' => 'nf', 'campos' => array(
	                array('title' => 'nf', 'campoAgrupar' => 'nf', 'campoMostrar' => 'nf', 'campoOrdem' => '', 'campoSelecionado' => true),
	            ),
	            'tipo' => 'agrupar', 'exibir' => false),
	        );
		} else {
			$gridFiltro = array(
	            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
	            'T224_Id' => array('titulo' => 'OC', 'tipo' => 'numero'),
	            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
	            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
	            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
	            'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo', 'tipo' => 'select', 'naoGerarExtra' => ''),
	            'Comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
	            'T225_Codigo_Produto' => array('titulo' => 'Código produto', 'tipo' => 'numero'),
	            'T225_Descricao_Produto' => array('titulo' => 'Descrição produto', 'tipo' => 'texto'),
	            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
	            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
	        );
		}
        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
		/**
         * Totais
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
        //$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extraTotal);
        //$valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T225_Quantidade) Total", $from, $extraTotal));
        //$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T225_Valor_Total_Preco_Sem_Desconto)", $from, $extraTotal));
        
		// --------------------------------------------------------------------------------------------------------
		// FIM - OSNI - 32799 - est016GridProdutosPedido
		// --------------------------------------------------------------------------------------------------------

		/*
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Quantidade total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor produtos total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;	*/

		if (modoModelo('FOLLOWUP')) {
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
						var id = chavePrimaria($(this).closest('tr').attr('id'), 'T224_Id');
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T224&naoAtualizarLinha=1', [990,605]);  //950 615
					});
					$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		} else if (modoModelo('RECEBIMENTO')) {
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
						var id = chavePrimaria($(this).closest('tr').attr('id'), 'T013_Id');
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Nota Fiscal: ' + id, '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela=T013', [990,550]);
					});
					$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		} else {
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
    
    case 'est016GridServicosPedido':

            /**
            * Definição do Grid (Tabela)
            */
            $grid = array(
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center' ),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T230_Id', 'colunaTitulo' => 'OS', 'chavePrimaria'=>true),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => "concat(T230_Titulo, ' ', T230_Descricao) as Servico", 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'est016_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T230_Quantidade', 'colunaTitulo' => 'Quantidade','colunaExibir' => false),
            //array('colunaValor' => 'T230_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => 'T230_T224_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
            array('colunaValor' => 'T230_T223_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
            //array('colunaValor' => 'T230_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T230_D116_Id', 'colunaExibir'=>false),
            array('colunaValor' => 'T230_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado'),
            //array('colunaValor' => 'T230_Status', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'T230_Status', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
            );
    
            /**
                *
                * Definição da SQL
                */
            $from = 'T230';
    
            $extra = <<<EOT
                    LEFT JOIN T224 ON T224_Id=T230_T224_Id
                    left join D006 on D006_Id=T224_D006_Id
                    left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
                    LEFT JOIN D024 ON D024_Id=T224_D024_Id
                    left join D018 on D018_Id=D024_D018_Id
                    LEFT JOIN T225 ON T225_Id=T230_T225_Id
                    LEFT JOIN D009 ON D009_Id=T225_D009_Id
                    LEFT JOIN D049 ON D009_D049_Id=D049_Id
                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
EOT;
    

        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T230_Id-/, '');
                divSetarAcaoId('{$r_divReload}', id);
                divRefresh('{$r_divReload}');
            });
            } </script></div>
EOT;
        */
        
        /**
        * Filtro
        */
        $gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
            'T224_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            //'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo (E/S)', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
            ), 'naoGerarExtra' => false),    
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),            
            'T230_Id' => array('titulo' => 'OS', 'tipo' => 'texto'),
            'T230_Titulo,T230_Descricao' => array('titulo' => 'Serviço', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'numero'),
            'T230_Status' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Pendente', 'value' => 'P'),
                array('title' => 'Execução', 'value' => 'E'),
                array('title' => 'Concluído', 'value' => 'C'),
                array('title' => 'Cancelado', 'value' => ' "" '),
            ), 'naoGerarExtra' => false),               
        );
        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        
        /**
        * Botões (Versão Acima do GRID)
        */
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        $valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T230_Quantidade) Total", $from, $extra));
        $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T230_Valor_Unitario)", $from, $extra));
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Quantidade total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor serviços total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
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

        $novoIdTelaForm = uniqid();
        break;
        
        case 'est016GridProdutosTrocaPedido':
        
        $T224_Id = (isset($r_T224_Id)) ? $r_T224_Id : false;
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center' ),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T222_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T222_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
            array('colunaValor' => 'T222_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
            //array('colunaValor' => 'T222_Operacao', 'colunaTitulo' => 'Operação', 'callback'=>'retornaOperacaoTroca'),                        
            array('colunaValor' => 'T222_Operacao', 'colunaTitulo' => 'Operação', 'callback' => 'retornaOperacaoTroca'),
            array('colunaValor' => 'T222_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
        );
        
        
                                

        /**
         *
         * Definição da SQL
         */
        $from = 'T222';

        $extra = <<<EOT
            LEFT JOIN T224 ON T224_Id=T222_T224_Id
            left join D006 on D006_Id=T224_D006_Id
            left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
            LEFT JOIN D024 ON D024_Id=T224_D024_Id
            LEFT JOIN D018 ON D018_Id=D024_D018_Id
            LEFT JOIN D009 on D009_Id=T222_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
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
        $gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
            'T224_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            //'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo (E/S)', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
            ), 'naoGerarExtra' => false),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),            
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
        );
        //Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
        
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
        * Botões (Versão Acima do GRID)
        */
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        $valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T222_Quantidade) Total", $from, $extra));
        $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T222_Valor_Preco_Unitario)", $from, $extra));
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Quantidade total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor produtos total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
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
    
    
    case 'est016GridAgregados':
            /**
            * Definição do Grid (Tabela)
            */
            $grid = array(
            array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T204_Id', $g['divId'])),
            array('colunaValor' => 'T204_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
            array('colunaValor' => 'T204_Descricao', 'colunaTitulo' => 'Descrição','colunaEditar' => true),
            array('colunaValor' => 'T204_Observacao', 'colunaTitulo' => 'Observação','colunaEditar' => true),
            array('colunaValor' => 'T204_Valor_Unitario', 'colunaTitulo' => 'Valor','colunaCasasDecimais' => '2','colunaEditar' => true),
            array('colunaValor' => 'T209_Nome', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
            );

            /**
            *
            * Definição da SQL
            */
            $from = 'T204';

            $extra = <<<EOT
            	LEFT JOIN T209 ON T209_Id = T204_T209_Id 
                where 
                T204_T225_Id = {$r_acaoId}
EOT;
            //Define ação do click de abertura (ao clicar em uma linha do grid)

            $novoIdTelaForm = uniqid();
            echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
               // $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                   // var id = $(this).closest('tr').attr('id').replace(/T204_Id-/, '');
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
        
        case 'est016GridPedidoProdutosDefeito':            
            /**
            * Definição do Grid (Tabela)
            */
            $grid = array(	            
	            array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'Pedido', 'style' => "width: 1%;"),
	            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => "width: 1%;"),
	            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Quantidade', 'style' => "width: 1%;", 'colunaCasasDecimais' => '2'),
            );

            /**
            *
            * Definição da SQL
            */
            $from = 'T225';

            $extra = <<<EOT
            	left join T224 on T224_Id = T225_T224_Id
            	left join D024 on D024_Id = T224_D024_Id
                where T225_T206_Id = {$r_acaoId}
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

        case 'est016GridCompras':
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(	            
			    array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'T225_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			    array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'T225_T224_Id', 'colunaExibir' => false),
			    array('colunaValor' => 'T225_D009_Id', 'colunaTitulo' => 'T225_D009_Id', 'colunaExibir' => false),
			    array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'T189_Id', 'colunaExibir' => false),
			    array('colunaValor' => 'T225_Codigo_Produto', 'colunaTitulo' => 'Código'),
			    array('colunaValor' => 'T225_Descricao_Produto', 'colunaExibir' => false),
			    array('colunaValor' => 'descricao', 'colunaTitulo' => 'Descrição', 'colunaTipo' => 'livre', 'callback' => 'validaCampoCotacaoPedido'),
				array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
				array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'center'),
			    array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'center'),
			    array('colunaValor' => 'campoCotar', 'callback' => 'validaCampoCotarPedido', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Cotar', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'campoComprar', 'callback' => 'validaCampoComprar', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Comprar', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T225_Flag_Cotar', 'callback' => 'checkboxCotarPedido', 'colunaTitulo' => 'Cotar', 'colunaAlinhar' => 'center', 'colunaEditarTodos'=>true),
				array('colunaValor' => 'T225_Flag_Comprar', 'callback' => 'checkboxComprar', 'colunaTitulo' => 'Comprar', 'colunaAlinhar' => 'center', 'colunaEditarTodos'=>true),
				array('colunaValor' => 'T225_Flag_Modificar', 'callback' => 'checkboxModificar', 'colunaTitulo' => 'Produzir', 'colunaAlinhar' => 'center', 'colunaEditarTodos'=>true),
				array('colunaValor' => 'T189_Flag_Status', 'colunaExibir' => false),
				array('colunaValor' => 'T189_Flag_Tipo', 'colunaExibir' => false),
			);

			/**
			* Definição da SQL
			*/
			$from = 'T225';

			$extra = <<<EOT
				LEFT JOIN T189 ON T189_T225_Id = T225_Id
            	LEFT JOIN D009 ON D009_Id = T225_D009_Id
            	LEFT JOIN D049 ON D049_Id = D009_D049_Id
            	LEFT JOIN D001 ON D001_Id = D049_D001_Id
            	LEFT JOIN D082 ON D082_Id = D049_D082_Id
				WHERE T225_T224_Id = {$r_acaoId}
EOT;

			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est016CotarProdutos">Cotar Produtos Selecionados</button>
					<button type="button" id="est016ComprarProdutos">Comprar Produtos Selecionados</button>
					<button type="button" id="est016ModificarProdutos">Gerar Serviço Produtos</button>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#est016CotarProdutos').unbind('click').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/grid_func-ajax/cotarProdutosSelecionados/?ajax=true&T224_Id='+ encodeURIComponent('{$r_acaoId}') +'&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if(request.code){
								divRefresh('{$g['divId']}', true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					});

					$('#est016ComprarProdutos').unbind('click').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/grid_func-ajax/comprarProdutosSelecionados/?ajax=true&T224_Id='+ encodeURIComponent('{$r_acaoId}') +'&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if(request.code){
								divRefresh('{$g['divId']}', true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					});

					$('#est016ModificarProdutos').unbind('click').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/grid_func-ajax/est016modificarProdutosSelecionados/?ajax=true&T224_Id='+ encodeURIComponent('{$r_acaoId}') +'&callback=?', function(request) {
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
									$.getJSON('/est/est016/grid_func-ajax/est016MarcarTodosCotar/?ajax=true&T224_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est016/grid_func-ajax/est016DesmarcarTodosCotar/?ajax=true&T224_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
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
					$('.gridTheadTh > .ui-icon-pencil:eq(1)')
						.removeAttr('onclick')
						.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est016/grid_func-ajax/est016MarcarTodosComprar/?ajax=true&T224_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est016/grid_func-ajax/est016DesmarcarTodosComprar/?ajax=true&T224_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
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
				if($array['todosCampos']['D009_Quantidade_Estoque_Liquido']<$array['todosCampos']['T225_Quantidade']){
					return 'linhaVermelho';
				}
			}

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

    case 'est016GridPrincipalAnalise':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            //array('colunaValor' => 'naoLiberar', 'colunaTipo' => 'livre', 'callback' => 'est016NaoLiberar', 'style' => 'background:#FFCECE', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'Liberar', 'colunaTipo' => 'livre', 'callback' => 'est016Liberar', 'style' => 'background:var(--celula-verde)', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cód', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'T224_Nome_Status T224_Status', 'colunaTitulo' => 'Situação', 'colunaExibir' => false),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T224_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T224_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T224_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T224_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T224_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',convert(ifnull(D013_DDD_Telefone_1,\'\') using utf8),\'-\',convert(ifnull(D013_Telefone_1,\'\') using utf8)) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:180px;min-width:180px;width:180px;white-space:normal;'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Vendedor', 'colunaTitulo' => 'Comprador'),
            array('colunaValor' => 'T224_Observacao_Comercial', 'colunaTitulo' => 'Observação Comercial'),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T224';

		$extra = <<<EOT
			left join D024 on D024_Id=T224_D024_Id
			left join T225 on T224_Id=T225_T224_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D013 on D013_Id=T224_D013_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T224_C007_Id_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			where T224_C004_Id='{$g['empresaAtual']}' and T224_Flag_Status='1'
			AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'C', T224_Flag_Alcada = 'C', IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (T224_Flag_Alcada = 'C' OR T224_Flag_Alcada = 'S'), 1=1)) 
			group by T224_Id
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
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T224_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			"if(T224_Flag_ACP='1','NF',if(T224_Flag_ACP='2','Romaneio',if(T224_Flag_ACP='3','ACP',if(T224_Flag_ACP='5','D1',if(T224_Flag_ACP='6','NFe','CF'))))) Tipo_Document" => array('titulo' => 'Documento', 'tipo' => 'texto'),
			'T224_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			//'T224_Flag_Status_Pedido' => array('titulo' => 'Status(F/P/C/V)', 'tipo' => 'texto'),
			'T224_Flag_Status_Pedido' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
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
				var id = $(this).closest('tr').attr('id').replace(/T224_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra - ID: ' + id, '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [950,605]);
			});
		} </script></div>
		<div id="gridAnaliseOrdemDeCompra" style="width: 0; height: 0; border: 0; padding: 0; margin: 0;"></div>
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

	case 'est016GridOrdensCompraGrupo':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'T224_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_D024_Id', 'colunaTitulo' => 'Cód.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(convert(substr(D024_Nome_Empresa,1,30) using utf8),\'<br><font style="font-size:10px"><i>\',convert(ifnull(T224_Observacao_Pedido_Perdido,\'\') using utf8))', 'colunaTitulo' => 'Razão Social', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            array('colunaValor' => 'concat(convert(substr(D013_Nome_Contato,1,10) USING utf8),\' \',convert(ifnull(D013_DDD_Telefone_1,\'\') using utf8),\'-\',convert(ifnull(D013_Telefone_1,\'\') using utf8)) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:190px;min-width:190px;width:190px;white-space:normal;'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'count(distinct T225_Id)', 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T224_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T224_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'if(T139_Id>0,\'Ok\',\'<font style="color: var(--BGVermelho)">--</font>\')', 'colunaTitulo' => 'Mail', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'T224';
		
		$extra = <<<EOT
			LEFT JOIN T225 ON T224_Id=T225_T224_Id
			LEFT JOIN D024 ON D024_Id=T224_D024_Id
			LEFT JOIN D013 ON D013_Id=T224_D013_Id
			LEFT JOIN D018 ON D018_Id=D024_D018_Id
			LEFT JOIN D020 ON D020_Id=D024_D020_Id
			LEFT JOIN T139 ON T224_Id=T139_T224_Id
			WHERE T224_Grupo = '{$r_T224_Grupo}'
			GROUP BY T224_Id
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

	case 'est016gridDividirOrdemCompra':
		/**
        * Definição do Grid (Tabela)
        */
		$grid = array(
            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T225_T225_Id_Dividir', 'colunaTitulo' => 'Id div', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T225_Flag_Comissao', 'colunaTitulo' => 'Comissão', 'callback' => 'ven012CampoComissao', 'callbackParameter' => 'T225', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Flag_Dividir', 'colunaTitulo' => 'Dividir', 'callback' => 'ven012CampoDividir', 'callbackParameter' => 'T225', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Qte OC', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:lightblue'),
            array('colunaValor' => 'T225_Quantidade_Separacao', 'colunaTitulo' => 'Separ.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq.Loja', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq.Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
            array('colunaValor' => 'T225_Quantidade_Dividir', 'colunaTitulo' => 'Qte novo pedido', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T225_Quantidade_Dividir'),
            array('colunaValor' => 'T224_Flag_Dividido', 'colunaTitulo' => 'Dividido', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Descricao Produto'),
            array('colunaValor' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço unitário', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T225_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'if(T225_Quantidade_Estoque<T225_Quantidade,T225_Quantidade_Estoque,T225_Quantidade)*T225_Valor_Preco_Unitario', 'colunaTitulo' => 'Valor dispnível', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibir' => false),
            array('colunaValor' => 'T225_Quantidade_Dividir*T225_Valor_Preco_Unitario', 'colunaTitulo' => 'Valor dividir', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:#caffe4', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'style' => 'background:orange'),
            array('colunaValor' => '(T224_Valor_Desconto/T224_Valor_Total_Produtos)*(T225_Valor_Preco_Unitario*T225_Quantidade)', 'colunaTitulo' => 'Desconto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => '(T225_Valor_Total_Preco*(T225_Aliquota_IPI/100))', 'colunaTitulo' => 'Valor IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T004_T003_Id', 'colunaTitulo' => 'Orç.', 'style' => 'width:1%;'),
            array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Ped.', 'style' => 'width:1%;'),
        );
        /**
            *
            * Definição da SQL
            */
        $from = 'T225';

        $extra = <<<EOT
            LEFT JOIN T224 ON T224_Id=T225_T224_Id
			LEFT JOIN D024 ON D024_Id=T225_D024_Id
			LEFT JOIN D037 ON D037_Id=T225_D037_Id
			LEFT JOIN D009 ON D009_Id=T225_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D006 ON D006_Id=T225_D006_Id
			LEFT JOIN D005 ON D005_Id=T225_D005_Id
			LEFT JOIN C008 ON C008_Id=D001_C008_Id
			LEFT JOIN C007 ON C007_Id=T224_C007_Id_Vendedor_Interno
			LEFT JOIN T219 ON T219_Id=T225_T219_Id
			LEFT JOIN T004 ON T004_Id=T219_T004_Id
			LEFT JOIN T006 ON T006_Id=T219_T006_Id
			WHERE T224_D024_Id = '{$r_acaoId}'
			AND T225_Quantidade > 0
			AND T224_C004_Id='{$g['empresaAtual']}'
			AND (T224_Flag_Status='' OR T224_Flag_Status='0' OR T224_Flag_Status='2' OR T224_Flag_Status='3' OR T224_Flag_Status='12')
			AND ifnull(T224_Flag_Dividido,'N') != 'S'
			AND T225_Flag_Cancelada != 'S'
EOT;
        /**
        * Filtro
        */
		$gridFiltro = array(
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'T225_Codigo_Produto' => array('titulo' => 'Código Produto', 'tipo' => 'texto', 'exibir' => false),
            'T225_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto', 'exibir' => false),
            'T224_Data_Emissao' => array('titulo' => 'Data de Emissão', 'tipo' => 'data', 'exibir' => false),
            'T224_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	            $('#{$g['divId']} .gridOptTop');
	            $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
	                return false;
	            });

	            function colunaEditarCallbackJS_T225_Quantidade_Dividir(T225_Id, campoEditado, valorAntigo, novoValor) {
	            	T225_Id = T225_Id.replace(/T225_Id-/, '');
	            	$.getJSON('/est/est016/grid_func-ajax/est016ValidaQuantidadeDividirOC/?ajax=true&T225_Id='    + encodeURIComponent(T225_Id) +
	                                                                                          '&novoValor='       + encodeURIComponent(novoValor) +
	                                                                                          '&valorAntigo='        + encodeURIComponent(valorAntigo) +
	                                                                                          '&callback=?', function(request) {
	                    if(request.code != true){
	            			dialogConfirm('Erro', request.data);
	            			//divRefresh('{$g['divId']}');
	            		}
	            		divRefresh('{$g['divId']}', true, false, false, T225_Id, 'T225_Id');
	                });
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
}
