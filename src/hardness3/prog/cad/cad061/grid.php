<?php
namespace hardness;

require_once("old/lib/funcoes.php");
//require_once('prog/crm/crm001/grid_func-callback.php');
require_once('prog/crm/crm001/grid_func-js.php');
require_once('bibliotecas/funcoesCallback/montarInteracaoTarefa.php');
require_once('prog/cad/cad061/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/cad/cad061/grid_func-js.php');

switch($g['r'][3]){
	
	case 'cad061gridPrincipalPedidos':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'campoCancelamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamento', 'style' => 'width:1%'),
            array('colunaValor' => 'DividirPedidoCliente', 'colunaTitulo' => '', 'callback' => 'dividirPedidoCliente', 'colunaTipo' => 'livre', 'style' => 'width:1%;'),
            array('colunaValor' => 'D157_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
            array('colunaValor' => "D157_Flag_Duplicar", 'colunaTitulo'=>'Duplicar', 'callback' => 'validaCampoDuplicar', 'style'=>'width:1%;', 'colunaExibir'=>false),
            array('colunaValor' => 'D157_Numero_Pedido_Venda', 'colunaTitulo' => 'Ecommerce', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold', 'colunaExibir' => false),
            array('colunaValor' => 'D157_T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D157_Ordem_Compra', 'colunaTitulo' => 'OC.  Cli', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold', 'colunaEditar' => true),
            array('colunaValor' => 'D157_Flag_Dividido', 'colunaTitulo' => 'Div', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D157_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'D157_Data_Entrega', 'colunaTitulo' => 'Entrega', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D157_D024_Id', 'colunaTitulo' => 'Cod.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,15)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold;max-width:130px;min-width:130px;width:130px;white-space:normal;', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'font-weight:normal', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'concat(substr(D013_Nome_Contato,1,10),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:170px;white-space:normal;min-width:170px;width:170px'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D157_Valor_Total_Produtos_Sem_Desconto', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaGridPedido', 'style' => 'font-weight:normal;width:1%'),
			array('colunaValor' => '(D157_Valor_Desconto/D157_Valor_Total_Produtos_Sem_Desconto)*100', 'colunaTitulo' => '% Descto','callback' => 'gPorcentagem', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;width:1%'),
			array('colunaValor' => 'D157_Valor_Desconto', 'colunaTitulo' => 'R$ Descto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D157_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false, 'style' => 'font-weight:bold;width:1%', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'D157_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D157_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D157_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D157_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D157_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'D157_Percentual_Estoque_Disponivel', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'callback' => 'gPorcentagem'),
            array('colunaValor' => 'D157_Percentual_Margem', 'colunaTitulo' => 'Margem', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4', 'callback' => 'gPorcentagem'),
            array('colunaValor' => 'D157_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold,width:1%', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'D157_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
            array('colunaValor' => 'D157_D157_Id_Origem', 'colunaExibir' => false),
            //array('colunaValor' => 'D157_Prazos(D157_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
            //array('colunaValor' => 'group_concat(distinct "<span style=\\"font-weight:bold\\">", D157_Status_Pedido(D157_Flag_Status,1), "</span> ", if(D157_Flag_Status=8, concat("<br />", D157_Observacao_Pedido_Perdido), if(D157_Flag_Status=9, D157_Motivo_Nao_Liberado,""))) as Status2', 'colunaTitulo' => 'Status', 'style' => 'max-width:200px;white-space:normal;min-width:200px;width:200px'),
            //array('colunaValor' => 'group_concat(distinct "<span style=\\"font-weight:bold\\">", D157_Status_Pedido(D157_Flag_Status,1), "</span> ", "<br />", if(D157_Flag_Status=9, D157_Motivo_Nao_Liberado,"")) as Status3', 'colunaTitulo' => 'Status', 'style' => 'max-width:150px;white-space:normal;min-width:150px;width:150px', 'colunaExibir' => false),
            //array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'font-weight:bold;width:1%', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'colunaDimensao' => 'S', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D157_Percentual_Comissao', 'colunaTitulo' => '%Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D157_Valor_Total_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D157_Percentual_Comissao_Manual', 'colunaTitulo' => '%Manual', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'colunaEditarOculto' => (isset($g['c029Ids'][201])) ? true : false, 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaAlinhar' => 'left', 'colunaDimensao' => 'S', 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'D157_Percentual_Comissao_Representante', 'colunaTitulo' => '%Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => true, 'colunaEditarOculto' => (isset($g['c029Ids'][202])) ? true : false, 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'D157_Valor_Comissao_Representante', 'colunaTitulo' => 'Comissão R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right','colunaExibir' => true, 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'externo.C007_Primeiro_Nome', 'colunaTitulo' => 'Externo', 'colunaAlinhar' => 'left', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D157_Observacao_Comercial', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'colunaDimensao' => 'S', 'style' => 'max-width:350px;min-width:350px;width:350px;white-space:normal;'),
            array('colunaValor' => 'D157_Flag_Alcada', 'colunaTitulo' => 'Alcada', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D157_Flag_Status', 'colunaTitulo' => 'Flag Status', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D157_Flag_ACP', 'colunaTitulo' => 'D157_Flag_ACP', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D157_Flag_Status_Cotacao', 'colunaTitulo' => 'D157_Flag_Status_Cotacao', 'colunaExibir' => false),
            /*array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
        						FROM D158 
        						LEFT JOIN D009 ON D009_Id = D158_D009_Id
        						LEFT JOIN D049 ON D049_Id = D009_D049_Id
        						LEFT JOIN D082 ON D082_Id = D049_D082_Id
        						WHERE D158_D157_Id=D157_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),*/
            array('colunaValor' => 'D157_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'D157_C007_Id_Vendedor_Interno', 'colunaExibir' => false),
            array('colunaValor' => 'D157_Flag_Reenviado_Almoxarifado', 'colunaTitulo' => 'D157_Flag_Reenviado_Almoxarifado', 'colunaExibir' => false),
            array('colunaValor' => 'liberou.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista comercial pedido', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'liberouOrcamento.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista comercial orçamento', 'colunaAlinhar' => 'left'),
        );
		/**
		*
		* Definição da SQL
		*/
		$from = 'D157';
		$extra = <<<EOT
			left join D024 on D024_Id=D157_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D013 on D013_Id=D157_D013_Id
			left join T003 on T003_Id=D157_T003_Id
			left join C007 as Vendedor on Vendedor.C007_Id=D157_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=D157_C007_Id_Vendedor_Externo
			left join C007 as Liberou on Liberou.C007_Id=D157_Liberou_Alcada_C007_Id
			left join C007 as LiberouOrcamento on LiberouOrcamento.C007_Id=T003_Liberou_Alcada_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
			left join D006 on D006_Id=D157_D006_Id
			left join D158 on D158_D157_Id=D157_Id
			left join D021 on D021_Id=D157_D021_Id
			where (D157_C004_Id='{$g['empresaAtual']}' 
			and (D157_D157_Id_Agrupado<=0 or D157_D157_Id_Agrupado is null))
			{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
			group by D157_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=D157_D024_Id
			left join D013 on D013_Id=D157_D013_Id
     		left join C007 as Vendedor on Vendedor.C007_Id=D157_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=D157_C007_Id_Vendedor_Externo
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
    		left join D006 on D006_Id=D157_D006_Id
			left join D021 on D021_Id=D157_D021_Id
    		where (D157_C004_Id={$g['empresaAtual']} 
			{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
    		AND (D157_D157_Id_Agrupado<=0 or D157_D157_Id_Agrupado is null))
EOT;
		$extra_ = base64_encode(gRetirarOrderBy($extra));
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
		    if($array['todosCampos']['D157_Flag_Status']=='5'){
		        return 'linhaCinza';
		    } else if($array['todosCampos']['D157_Flag_Status']=='8'){
		        return 'linhaVermelho';
		    } else if($array['todosCampos']['D157_Flag_Status']=='26'){
		        return 'linhaLaranja';
		    } else if($array['todosCampos']['D157_Flag_Status']=='9'){
		    } else if($array['todosCampos']['D157_Flag_Status']=='14'){
		       return 'linhaVerde';
		  		// } else if ($array['todosCampos']['D157_Flag_Reenviado_Almoxarifado'] == 'S') {
		    } else if($array['todosCampos']['D157_Flag_Status']<=0){
		       return 'linhaAzul';
		    }
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad061IncluirPedido'),
			array('titulo' => 'Processar', 'id' => 'cad061ProcessarPeriodicos'),
		);
		echo Botoes::processar($botoes);

        echo <<<EOT
        <div class="buttonsBottomTop">
EOT;
		//if($g['C031']['usarBotaoIncluirPedido'] == "S"){
		echo <<<EOT
        	<!--<button type="button" id="cad061IncluirPedido">Incluir</button>
        	<button type="button" id="cad061ProcessarPeriodicos">Processar</button>-->
EOT;
		//}
		if($g['nivelAlcada'] == 4){
			echo <<<EOT
	            <!--<button type="button" id="crm001AnaliseCompras">Análise compra emergência</button>-->
EOT;
		}
		if($g['C031']['diasVencPedido'] > 0){
			echo <<<EOT
				<!--<button type="button" id="ven002ProcessarPedidosVencidos" onclick="processarPedidosVencidos('{$g['divId']}');">Processar Vencidos</button>-->
EOT;
		}
		echo <<<EOT
            <!--<button type="button" id="ven002DuplicarPedidos">Duplicar pedidos</button>-->
            <!--<button type="button" id="ven002AgruparPedidos">Agrupar pedidos</button>-->
            <!--<button type="button" id="ven002CancelarPedidos">Cancelar pedidos</button>-->
            <!--<button type="button" id="ven002MudarEmpresaPedidos">Mudar empresa</button>-->
            <!--<button type="button" id="ven002SelecionarTodos">Selecionar Todos</button>-->
            <!--<button type="button" id="ven002DesmarcarTodos">Desmarcar Todos</button>-->
        </div>
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		//$msg = gGeraAlertMsg('Erro!', 'Não existem itens para serem duplicados!');

        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var D157_Id = id[0].replace(/D157_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido', '/cad/cad061/content/cad061content02/', '&acaoId=' + encodeURIComponent(D157_Id) + '&tabela={$from}', [990,605]);            
			});
		} </script></div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#ven002DuplicarPedidos').bind('click', function(data) {
				$.getJSON('/crm/crm001/grid_func-ajax/crm001DuplicarPedido/?ajax=true&callback=?', function(request) {
					if (request.code) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Duplicar Pedidos', '/crm/crm001/content/crm001ContentSelecionarDiasDuplicar/', '&extra=' + encodeURIComponent('{$extra_}'), [300,150]);
					}else{
						dialogConfirm('Erro', 'Não existem pedidos selecionados para serem duplicados!');
					}
				});
			});
			$('#ven002AgruparPedidos').bind('click', function(data) {
				$.getJSON('/crm/crm001/grid_func-ajax/crm001VerificaAgruparPedidos/?ajax=true&callback=?', function(request) {
					if (request.code) {
						var \$buttons = {
							"Sim": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/crm/crm001/grid_func-ajax/crm001AgruparPedidos/?ajax=true&callback=?', function(request) {
									if (request.code) {
										dialogConfirm('Operação Ok!', 'Processo concluído com sucesso');
										divRefresh('{$g['divId']}');
										$('#' + '{$g['divId']}').hideLoading();
									}else{
										dialogConfirm('Erro', request.data);
										$('#' + '{$g['divId']}').hideLoading();
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Você tem certeza?', 'Atenção, este processo irá gerar um novo pedido com todos os produtos dos pedidos selecionados.<br /><br />Você tem certeza?', \$buttons);
					}else{
						dialogConfirm('Erro', 'Por favor, selecione pedidos do mesmo cliente');
					}
				});
			});
			$('#cad061ProcessarPeriodicos').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Processar pedidos periódicos', '/cad/cad061/content/cad061ContentProcessarPedidos/', '', [330,120]);
			});

			$('#cad061IncluirPedido').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Pedido', '/cad/cad061/content/cad061contentIncluirPedido/', '', [1000,600]);
			});
			$('#ven002MudarEmpresaPedidos').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Mudar Pedidos de Empresa', '/ven/ven002/content/ven002ContentMudarPedidoEmpresa/', '&D157_Id=' + encodeURIComponent(acaoId) + '&extra=' + encodeURIComponent('{$extra_}'), [300,200]);            
			});
			$('#crm001AnaliseCompras').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Análise de Compras', '/crm/crm001/content/crm001ContentAnaliseCompras/', '', 'full');
			});
			$('#ven002CancelarPedidos').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cancelar Pedidos', '/crm/crm001/content/crm001ContentCancelarPedidos/', '&extra=' + encodeURIComponent('{$extra_}'), [700,500]);
			});
		} </script></div>
EOT;   
 }

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'D157_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'D157_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome,externo.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'ifnull(D157_Flag_Status[[virgula]]\'0\')' => array('titulo' => 'Cancelado', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => '8'),
                array('title' => 'Não', 'value' => '!8'),
            ), 'naoGerarExtra' => '', 'exibir' => false),
            'D157_Valor_Total' => array('titulo' => 'Valor total', 'tipo' => 'moeda', 'exibir' => false),
            'D157_Nome_Status' => array('titulo' => 'Status Pedido', 'tipo' => 'texto', 'exibir' => false),
            '(SELECT GROUP_CONCAT(DISTINCT(IF(D158_T075_Id>0[[virgula]]"MP"[[virgula]]"PE"))) FROM D158 WHERE D158_D157_Id=D157_Id)' => array('titulo' => 'Estoque', 'tipo' => 'texto', 'exibir' => false),
            '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
        							FROM D158 
        							LEFT JOIN D009 ON D009_Id = D158_D009_Id
        							LEFT JOIN D049 ON D049_Id = D009_D049_Id
        							LEFT JOIN D082 ON D082_Id = D049_D082_Id
        							WHERE D158_D157_Id=D157_Id)' => array('titulo' => 'Marca', 'tipo' => 'texto', 'exibir' => false),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo))
        							FROM D158 
        							LEFT JOIN D009 ON D009_Id = D158_D009_Id
        							LEFT JOIN D049 ON D049_Id = D009_D049_Id
        							LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                                                  LEFT JOIN C008 ON C008_Id = D001_C008_Id
        							WHERE D158_D157_Id=D157_Id)' => array('titulo' => 'Tipo produto', 'tipo' => 'texto', 'exibir' => false),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data', 'exibir' => false, 'exibir' => false),
            'D157_Ordem_Compra' => array('titulo' => 'OC. Cliente', 'tipo' => 'texto', 'exibir' => false),
            'D158_Descricao_Produto,D158_Codigo_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto', 'exibir' => false),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto', 'exibir' => false),
            'D157_Percentual_Margem' => array('titulo' => '% Margem', 'tipo' => 'moeda', 'exibir' => false),
            'D157_IPV' => array('titulo' => 'IPV', 'tipo' => 'moeda', 'exibir' => false),
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
        );

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$sqlGridFiltro = base64_encode($geradoSql);
		$extra_ = base64_encode(gRetirarOrderBy($extra));

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven002SelecionarTodos').unbind('click').bind('click', function(data) {
					$.getJSON('/crm/crm001/grid_func-ajax/crm001SelecionarTodos/?ajax=true&extra=' + encodeURIComponent('{$sqlGridFiltro}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						}
					});
				});
				
				$('#ven002DesmarcarTodos').unbind('click').bind('click', function(data) {
					$.getJSON('/crm/crm001/grid_func-ajax/crm001DesmarcarTodos/?ajax=true&extra=' + encodeURIComponent('{$sqlGridFiltro}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						}
					});
				});
			} </script></div>
EOT;
        }

		/**
		 * Totalizadores
		 */
		/*if (!personalizacaoTotais()) {
			$campos = "COUNT(*), SUM(D157_Valor_Total_Produtos_Sem_Desconto), sum(D157_Valor_Desconto), (sum(D157_Valor_Desconto)/sum(D157_Valor_Total_Produtos_Sem_Desconto))*100, sum(D157_Valor_Total_Produtos), sum(D157_Valor_Total_Custo), ((sum(D157_Valor_Total_Produtos)-sum(D157_Valor_Total_Custo))/sum(D157_Valor_Total_Produtos))*100, sum(D157_Valor_Total_IPI), sum(D157_Valor_ICMS_Substituicao_Retencao), sum(D157_Valor_Total), sum(D157_Valor_Total_ICMS), sum(D157_Valor_Total_Comissao), (sum(D157_Valor_Total_Comissao)/sum(D157_Valor_Total_Produtos))*100";
			list($numeroRegistros, $valorTotalSemDesconto, $valorTotalDesconto,  $percentualTotalDesconto, $valorTotalProdutos, $valorTotalCusto, $valorTotalMargem, $valorTotalIPI, $valorTotalST, $valorTotal, $valorTotalICMS, $valorTotalComissao, $valorTotalPercentualComissao) = $g['sqlAuto']->pegarSqlCampos($campos, $from, $extraTotal);
			$valorTotalSemDesconto = gCorrigeNumero($valorTotalSemDesconto);
			$percentualTotalDesconto = gCorrigeNumero($percentualTotalDesconto);
			$valorTotalDesconto = gCorrigeNumero($valorTotalDesconto);
			$valorTotalProdutos = gCorrigeNumero($valorTotalProdutos);
			$valorTotalCusto = gCorrigeNumero($valorTotalCusto);
			$valorTotalMargem = gCorrigeNumero($valorTotalMargem);
			$valorTotalIPI = gCorrigeNumero($valorTotalIPI);
			$valorTotalST = gCorrigeNumero($valorTotalST);
			$valorTotal = gCorrigeNumero($valorTotal);
			$valorTotalICMS = gCorrigeNumero($valorTotalICMS);
			$valorTotalComissao = gCorrigeNumero($valorTotalComissao);
			$valorTotalPercentualComissao = gCorrigeNumero($valorTotalPercentualComissao);

			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extraTotal);			
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" size="5" value="{$numeroRegistros}" readonly></span>
					<!--<span class="item" style="text-align:right">Valor produtos<br><input type="text" class="whiteBg"  size="11" value="{$valorTotalSemDesconto}" readonly></span>-->
					<!--<span class="item" style="text-align:right">%Desconto<br><input type="text" class="whiteBg"  size="5" value="{$percentualTotalDesconto}%" readonly></span>-->
					<!--<span class="item" style="text-align:right">Desconto R$<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalDesconto}" readonly></span>-->
					<!--<span class="item" style="text-align:right">Valor produtos<br><input type="text" class="whiteBg" style='font-weight:bold' size="11" value="{$valorTotalProdutos}" readonly></span>-->
					<!--<span class="item" style="text-align:right">Valor custo<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalCusto}" readonly></span>-->
					<!--<span class="item" style="text-align:right">%Margem<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalMargem}%" readonly></span>-->
					<!--<span class="item" style="text-align:right">Valor IPI<br><input type="text" class="whiteBg"  size="9" value="{$valorTotalIPI}" readonly></span>-->
					<!--<span class="item" style="text-align:right">Valor ST<br><input type="text" class="whiteBg"  size="9" value="{$valorTotalST}" readonly></span>-->
					<!--<span class="item" style="text-align:right">Valor total<br><input type="text" class="whiteBg"  style='font-weight:bold' size="10" value="{$valorTotal}" readonly></span>-->
					<!--<span class="item" style="text-align:right">Valor ICM<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalICMS}" readonly></span>-->
			        <!--<span class="item" style="text-align:right">%Comissão<br><input type="text" class="whiteBg"  size="5" value="{$valorTotalPercentualComissao}" readonly></span>-->
			        <!--<span class="item" style="text-align:right">Valor comissão<br><input type="text" class="whiteBg"  size="10" value="{$valorTotalComissao}" readonly></span>-->
					<div style="clear:both;"></div>
				</div>
EOT;
		}*/
		
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
	
	case 'crm001GridPrincipalPedidosProdutos':
		$filtroObrigatorio = true;
		$sql = "SELECT T005_Casas_Decimais_Preco FROM T005 limit 1";
		$resT005 = mysql_query($sql);
		$rowT005 = mysql_fetch_assoc($resT005);

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T006_D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:normal', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'font-weight:bold;width:1%', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_Nome_Status', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold', 'callback' => 'abrirJanela', 'callbackParameter' => array(
                 'Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),
            ),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:bold', 'callback' => 'campoDescricaoItemPedido', 'colunaExibir' => false),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array(
                 'Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true),
              'style' => 'font-weight:normal;width:1%'),
            //array('colunaValor' => "(IF(T006_Quantidade_Estoque< T006_Quantidade, CONCAT('<span style=\"color: red;\">',REPLACE( REPLACE( REPLACE( FORMAT( T006_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ','),'</span>'), REPLACE( REPLACE( REPLACE( FORMAT( T006_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ',')))", 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Quantidade_Estoque as Pedido', 'colunaTitulo' => 'Estoq', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T006_Quantidade')),
            array('colunaValor' => 'T006_C004_Id_Estoque', 'colunaTitulo' => 'CD', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'montaComboEstoque', 'colunaExibir' => false),
            /*array('colunaValor' => 'T006_Flag_Reservar_Estoque', 'colunaTitulo' => 'Reservado', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array(
                 'T006_Flag_Reservar_Estoque', 'N|S'),
            ),*/
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'T006_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'T004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;background: var(--celula-verde);'),
            array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T006_Valor_Preco_Original', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T006_Valor_Preco_Original', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaPedido', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'T006_Id', 'right', 4, 'T006'), 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T006_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $rowT005['T005_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;background: var(--celula-verde);'),
            array('colunaValor' => 'IF(D009_Data_Alteracao_Custo > D009_Data_Ultima_Entrada, D009_Data_Alteracao_Custo, D009_Data_Ultima_Entrada)', 'colunaTitulo' => 'Ult. Alt. Custo', 'callback' => 'gCorrigeData', 'callbackParameter' => 'true', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Preco_Bruto', 'colunaTitulo' => 'Preço Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario+(T006_Valor_IPI/T006_Quantidade)+(T006_Valor_ICMS_Substituicao_Retencao/T006_Quantidade) as Preco_Bruto', 'colunaTitulo' => 'Preço Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => '(T006_Valor_Preco_Bruto*T006_Quantidade) as T006_Valor_Total_Bruto', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Entg.dias', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Observacao', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T006_D006_Id_Info', 'Demonstrativo CFOP', 'T006_Id', 'left', '', '500')),
            array('colunaValor' => 'T006_D006_Id_Info', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T006_D005_Id_Info', 'Demonstrativo NCM', 'T006_Id', 'left', '', '500')),
            array('colunaValor' => 'T006_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px'),
            array('colunaValor' => 'T006_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditarGeraSelect' => array(
                array('title' => '...', 'value' => '', 'selected' => true),
                array('title' => '1', 'value' => '1'),
                array('title' => '2', 'value' => '2'),
                array('title' => '3', 'value' => '3'),
              ),
              'style' => 'background: var(--celula-vermelho);;font-size:12px'),
            array('colunaValor' => 'T006_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T006_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T006_Percentual_Reducao_ICMS', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T006_Aliquota_ICMS_ST', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T006_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T006_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T006_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T006_ST_VA', 'colunaTitulo' => 'MVA', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            //array('colunaValor' => 'T006_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T006_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Item_Pedido_Compra', 'colunaTitulo' => 'Item.OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Total_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => '((T006_Valor_Preco_Unitario/T006_Valor_Custo_Unitario)-1)*100', 'colunaTitulo' => '% Margem', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré Cadastro', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Percentual_Comissao', 'colunaTitulo' => '%Comiss', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T004_T219_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cotação', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', 'T218_Id', '990,605'), 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T006_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T006_Id_Dividir', 'colunaTitulo' => 'Id Div', 'colunaExibir' => false),
            array('colunaValor' => 'IF((T006_T219_Id > 0 OR T006_T225_Id > 0) AND T006_Valor_Preco_Cotacao > 0, 675, 450) as width', 'colunaTitulo' => 'width', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Flag status', 'colunaExibir' => false),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'T006';

		$extra = <<<EOT
				left join T005 on T005_Id=T006_T005_Id
				left join D024 on D024_Id=T005_D024_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D037 on D037_Id=T006_D037_Id
				left join D009 on D009_Id=T006_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D006 on D006_Id=T006_D006_Id
				left join D005 on D005_Id=T006_D005_Id
				LEFT JOIN T066 on T066_D009_Id=D009_Id
				LEFT JOIN D004 on D004_Id=T066_D004_Id
				LEFT JOIN T004 ON T004_Id=T006_T004_Id
				left join T219 ON T219_Id=T006_T219_Id
				left join T218 ON T218_Id=T219_T218_Id
				left join T007 ON T007_T005_Id=T005_Id
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberou_Alcada_C007_Id
				left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
				left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
				where (T005_C004_Id='{$g['empresaAtual']}' 
				and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null))
				and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor2.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
				group by T006_Id 
				order by T005_Id desc 
EOT;
		
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
		    if($array['todosCampos']['T005_Flag_Status']=='5'){
		        return 'linhaCinza';
		    } else if($array['todosCampos']['T005_Flag_Status']=='8'){
		        return 'linhaVermelho';
		    } else if($array['todosCampos']['T005_Flag_Status']=='26'){
		        return 'linhaLaranja';
		    } else if($array['todosCampos']['T005_Flag_Status']=='9'){
		    } else if($array['todosCampos']['T005_Flag_Status']=='14'){
		       return 'linhaVerde';
		  		// } else if ($array['todosCampos']['T005_Flag_Reenviado_Almoxarifado'] == 'S') {
		    } else if($array['todosCampos']['T005_Flag_Status']<=0){
		       return 'linhaAzul';
		    }
		}

		//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T005_Id = id[0].replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [990,605]);            
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
			
			// Função JS executada como callback do editar campo (colunaEditarCallbackJS)
			function T006_Calculo_Valor_Unitario(T006_Id, campoEditado, valorAntigo, novoValor) {
				T006_Id = T006_Id.replace(/T006_Id-/, '');
					if(campoEditado == 'T006_Valor_Preco_Bruto'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								//divRefresh('{$g['divId']}');
								divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
								divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
			}
			
			function colunaEditarCallbackJS_T006_Percentual_Desconto(T006_Id, campoEditado, valorAntigo, novoValor) {
					T006_Id = T006_Id.replace(/T006_Id-/, '');
					if(campoEditado == 'T006_Percentual_Desconto'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if(campoEditado == 'T006_Percentual_Desconto_Bruto'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if(campoEditado == 'T006_Valor_Preco_Sem_Desconto_Unitario'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
					//divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
					divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
				}
				function gridTheadCallback() {
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
							.removeAttr('onclick')
							.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/grid_func-ajax/ven002MarcarTodosExcluir/?ajax=true&T005_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/grid_func-ajax/ven002DesmarcarTodosExcluir/?ajax=true&T005_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {
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
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Fantasia,D024_Nome_Empresa,D024_Id' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'ifnull(T005_Flag_Status[[virgula]]\'0\')' => array('titulo' => 'Cancelado', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => '8'),
                array('title' => 'Não', 'value' => '!8'),
            ), 'naoGerarExtra' => ''),
            'T005_Nome_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'T006_Percentual_Desconto' => array('titulo' => '%Desc', 'tipo' => 'numero'),
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

	case 'crm001GridPrincipalNotasFiscais':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;

		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirNFe', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback' => 'validaTipo', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,30) as D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaDimensao' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(CONVERT(substr(Pedido.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(Pedido.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(Pedido.D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:170px;white-space:normal;min-width:170px;width:170px'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaDimensao' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaDimensao' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Prazos(T007_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%', 'colunaExibirNivel' => 4, 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Valor_Total_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica' => 'S', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Percentual_Margem', 'colunaTitulo' => 'Margem', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaExibirNivel' => '4', 'callback' => 'gPorcentagem'),
            array('colunaValor' => 'T007_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold,width:1%', 'colunaExibirNivel' => '4'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaDimensao' => 'S', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'T007_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorInternoNF', 'colunaEditarNivel' => 4, 'style' => 'width:1%'),
            array('colunaValor' => 'Danfe', 'colunaTitulo' => 'Danfe', 'colunaTipo' => 'livre', 'callback' => 'ValidaCampoDanfe', 'style' => 'width:1%'),
            array('colunaValor' => 'NF', 'colunaTitulo' => 'NF', 'colunaTipo' => 'livre', 'callback' => 'validaCampoNF', 'style' => 'width:1%'),
            array('colunaValor' => 'ACP', 'colunaTitulo' => 'ACP', 'colunaTipo' => 'livre', 'callback' => 'validaCampoACP', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar'),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'eMail', 'colunaTipo' => 'Livre', 'callback' => 'validaCampoEmail', 'style' => 'width:1%'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T008_T007_Id=T007_Id) as Tipos', 'colunaTitulo' => 'Tipos', 'colunaExibir' => false),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  WHERE T008_T007_Id=T007_Id) as Marcas', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'Retorno Sefaz', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Retorno SEFAZ', 'callback' => 'chamaCampo'),
            array('colunaValor' => 'T007_Numero_Protocolo_Nfe as Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NFe'),
            array('colunaValor' => 'T007_Numero_Protocolo_Cancelamento_Nfe', 'colunaTitulo' => 'Prot. Cancel'),
            array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso', 'callback' => 'ValidaChaveAcesso'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Observacao_Cancelamento', 'colunaTitulo' => 'Observação Cancelamento', 'colunaExibir' => false),
            array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Impresso', 'colunaTitulo' => 'Impressão', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status', 'colunaTitulo' => 'Status Pedido', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Hora_Inutilizacao', 'colunaTitulo' => 'Hora Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Flag_Inutilizado', 'colunaTitulo' => 'Inutilização', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'colunaExibir' => false),
            array('colunaValor' => 'T007_Data_Envio_XML as Data_Envio_XML', 'colunaTitulo' => 'XML', 'colunaExibir' => false),
            array('colunaValor' => '(select ifnull(T127_Id,0) from T127 where T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T127_C004_Id=T007_C004_Id limit 1) as T127_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => '(select T008_Codigo_Produto from T008 where T008_T007_Id=T007_Id limit 1) as T008_Codigo_Produto', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'T007A_Flag_Contingencia_Offline', 'colunaExibir' => false),
        );
		
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}
		}
		
		/**
		 * Definição da SQL
		 */
		$from = "T007";
		
		$extra = <<<EOT
			left join T005 on T005_Id=T007_T005_Id
			left join D013 as Pedido on Pedido.D013_Id=T005_D013_Id
		    left join D024 on D024_Id=T007_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T007_D006_Id
			left join C007 as vendedor on vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
			left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
			left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
			left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
		    left join D021 on D021_Id=D024_D021_Id
			left join T007A on T007_Id = T007A_T007_Id
		    where (T007_C004_Id='{$g['empresaAtual']}'
		    and T007_Flag_Emitida_Recebida!='R')
			{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven003IncluirNotaFiscal">Incluir</button>
				<button type="button" id="ven003IncluirNotaFiscalConsumidor">PDV</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#ven003IncluirNotaFiscal').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','', [920,620]);
				});

				$('#ven003IncluirNotaFiscalConsumidor').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven003/grid_func-ajax/ven003incluirNotaFiscalConsumidor/?ajax=true&callback=?', function(request) {
				        $('#{$g['divId']}').hideLoading();
				        if (request.code) { 
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','&acaoId=' + encodeURIComponent(request.data), [920,620]);
				        } else {
				        	dialogConfirm('Erro', request.data, false);
				        }
				    });
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'Nota Fiscal', 'tipo' => 'texto'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'Pedido.D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 1', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'D006_Codigo_CFOP,D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ),
            'naoGerarExtra' => ''),
            'T007_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Origem', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
              ),
              'naoGerarExtra' => ''),
            '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                                  WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Tipo produto', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                                  FROM T008 
                                                  LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                                  LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                                  LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                                  LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                                  WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Marca produto', 'tipo' => 'texto'),
            'T007_Numero_Protocolo_Nfe' => array('titulo' => 'Protocolo', 'tipo' => 'text', 'exibir' => false),
            'T007_Flag_ACP' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'NF-e', 'value' => '6'),
                array('title' => 'NFC-e', 'value' => '8'),
                array('title' => 'CF', 'value' => '4'),
              ),
            ),
            'T007_Prazos(T007_Id)' => array('titulo' => 'Prazo', 'tipo' => 'texto'),
            'T007_Percentual_Margem' => array('titulo' => '% Margem', 'tipo' => 'moeda'),
            'T007_IPV' => array('titulo' => 'IPV', 'tipo' => 'moeda'),
            'T005_Ordem_Compra' => array('titulo' => 'OC. Cliente', 'tipo' => 'texto'),
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
		if (!personalizacaoTotais()) { 
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			$valorTotal1 = $g['sqlAuto']->pegarSqlCampo("sum(T007_Valor_Total_Produtos)", $from, $extra);
			$valorTotal2 = $g['sqlAuto']->pegarSqlCampo("sum(T007_Valor_Total) Total", $from, $extra);
			$valorTotal3 = $g['sqlAuto']->pegarSqlCampo("sum(T007_Valor_Total_Comissao) Comissao", $from, $extra);
			$valorTotal4 = $g['sqlAuto']->pegarSqlCampo("(sum(T007_Valor_Total_Custo)/sum(T007_Valor_Total_Produtos))*100", $from, $extra);
			$percentualComissao = ($valorTotal3 / $valorTotal1)*100;

			$valorTotal1 = gCorrigeNumero($valorTotal1);
			$valorTotal2 = gCorrigeNumero($valorTotal2);
			$valorTotal3 = gCorrigeNumero($valorTotal3);
			$valorTotal4 = gCorrigeNumero($valorTotal4);
			$percentualComissao = gCorrigeNumero($percentualComissao);
			
			if($g['C007']['C007_Flag_Vendedor'] == 'D'){
				echo $totais = <<<EOT
					<div class="buttonsBottomTop">
						<span class="item" style="text-align:right">Notas Emitidas..<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Valor comissões<br><input type="text" value="{$valorTotal3}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">% comissões<br><input type="text" value="{$percentualComissao}%" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">% Margem<br><input type="text" value="{$valorTotal4}%" class="whiteBg" id="" size="11" readonly></span>
						<div style="clear:both;"></div>
					</div>
EOT;
				$g['smarty']->assign('totais', $totais);
			} else {
				echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Notas Emitidas..<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor comissões<br><input type="text" value="{$valorTotal3}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">% comissões<br><input type="text" value="{$percentualComissao}%" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
				$g['smarty']->assign('totais', $totais);
			}
 		}		
		
		$novoIdTelaForm = uniqid();
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
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
	
	case 'crm001gridPrincipalProdutos':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		
		$gridModelos = array(
    		'REPOSIÇÃO' => 'REPOSIÇÃO'
    	);

		/**
		* Definição do Grid (Tabela)
		*/
		if (modoModelo('REPOSIÇÃO')) {
			$grid = array(
	            //array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T008_Id', $g['divId']), 'style' => 'width:1%;'),
	            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'T008_T007_Id', 'colunaTitulo' => 'T008_T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Depto', 'style' => 'font-weight:normal'),
		    	array('colunaValor' => 'D060_Descricao_Grupo', 'colunaTitulo' => 'Setor', 'style' => 'font-weight:normal'),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'), 'style' => 'width:1%;'),
	            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:bold'),
	            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%;'),
	            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%;'),
			    array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Físico', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
        	    array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq.Loja', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
		    	array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq.Fora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callback' => 'estoqueFora'),
	            array('colunaValor' => 'SUM(T008_Quantidade)', 'colunaTitulo' => 'Vendido', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:bold'),
	            array('colunaValor' => 'D009_Sugestao_Compra_Vendas', 'colunaTitulo' => 'Repor', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:bold', 'colunaEditar' => true, 'colunaEditarTodos' => true),
	        );
		} else {
			$grid = array(
	            //array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T008_Id', $g['divId'])),
	            array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'T008_T007_Id', 'colunaTitulo' => 'T008_T007_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
	            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
	            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
	            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'substr(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Razão Social'),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'width:1%;font-weight:bold', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Produto', 'callback' => 'abrirJanela', 'style' => 'font-weight:bold', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	            array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'width:1%;font-weight:bold'),
	            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
	            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T008_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center'),
	            array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:bold;background:var(--celula-amarelo-claro)'),
	            array('colunaValor' => 'T008_Valor_Preco_Original', 'colunaTitulo' => 'Original', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:normal'),
	            array('colunaValor' => 'T008_Percentual_Desconto', 'colunaTitulo' => '%Desc', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:normal'),
	            array('colunaValor' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:bold;background:var(--celula-amarelo-claro)'),
	            array('colunaValor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:bold'),
	            array('colunaValor' => 'T008_Percentual_Comissao', 'colunaTitulo' => '%Comiss', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:normal', 'colunaEditar' => true, 'colunaEditarNivel' => 4, 'colunaEditarTodos' => true),
	            array('colunaValor' => 'T008_Valor_Comissao', 'colunaTitulo' => 'Comissão R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;font-weight:normal'),
	            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
	            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Flag_Situacao_Tributaria', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Aliquota_ICMS', 'colunaTitulo' => '%ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	            array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
	        );
		}
		
		/**
		 * Definição da SQL
		 */
		$from = "T008";
		
		$extra = <<<EOT
			left join D009 on D009_Id=T008_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D006 on D006_Id=T008_D006_Id
            left join D037 on D037_Id=T008_D037_Id
            left join T007 on T007_Id=T008_T007_Id
            left join D024 on D024_Id=T007_D024_Id
            left join C008 on C008_Id=D001_C008_Id
            left join D060 on D060_Id=D001_D060_Id
            left join D021 on D021_Id=T007_D021_Id
            left join T006 on T006_Id=T008_T006_Id
			left join C007 as vendedor on vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
			left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
			left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
			where T007_C004_Id='{$g['empresaAtual']}'
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor2.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
			and D006_Flag_Venda_Compra_Outros='V'
			and D006_Flag_Entrada_Saida='S'	
EOT;
		
		if (modoModelo('REPOSIÇÃO')) {
			$extra .= "GROUP BY D009_Id";
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
					    // somente executa a ação ao clicar na linha ou na coluna
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						// pega o ID da linha
						var id = $(this).closest('tr').attr('id').split('|');
						var T008_T007_Id = id[1].replace(/T008_T007_Id-/, '');
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(T008_T007_Id) + '&tabela=T008', [900,550]);
				   });
				   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
            'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D006_Descricao' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'exibir' => false),
            'T008_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'T008_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C008_Id,C008_Tipo' => array('titulo' => 'Depto.', 'tipo' => 'texto'),
            'D060_Descricao_Grupo' => array('titulo' => 'Setor', 'tipo' => 'texto'),
            'T007_Flag_Cancelada' => array('titulo' => 'Cancelada?(S/N)', 'tipo' => 'texto'),
            'T008_Percentual_Desconto' => array('titulo' => '% Desconto', 'tipo' => 'numero'),
        );

		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		function gridLinha($array) {
			if($array['todosCampos']['T007_Flag_Cancelada']=='S'){
				return 'linhaVermelho';
			}
		}
		
		if (!personalizacaoTotais()) { 
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			$valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra));
			$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Quantidade)", $from, $extra));
			$valorTotal3 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Valor_Total_Preco)", $from, $extra));
			$valorTotal4 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T008_Valor_Comissao)", $from, $extra));
			
			if (modoModelo('REPOSIÇÃO')) {
				$valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("count(DISTINCT D009_Id)", $from, gRetirarGroupBy($extra)));
				echo <<<EOT
					<div class="buttonsBottomTop">
						<span class="item" style="text-align:right">Quantidade produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
						<div style="clear:both;"></div>
					</div>
EOT;
			} else {
				echo <<<EOT
					<div class="buttonsBottomTop">
						<span class="item" style="text-align:right">Qte vendas<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Qte produto<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Valor total<br><input type="text" value="{$valorTotal3}" class="whiteBg" id="" size="11" readonly></span>
						<span class="item" style="text-align:right">Valor comissões<br><input type="text" value="{$valorTotal4}" class="whiteBg" id="" size="11" readonly></span>
						<div style="clear:both;"></div>
					</div>
EOT;
			}
		}

		$novoIdTelaForm = uniqid();

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
	
	case 'crm001GridPrincipalClientesAtivos':
		global $g;

		$filtroObrigatorio=false;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;font-weight:bold'),
			array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;font-weight:normal', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%;'),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => '(select SUM(T007_Valor_Total) from T007 where T007_D024_Id=D024_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim") as Total', 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero'),
			// T007_Valor_Total_Comissao
			// C007_Primeiro_Nome
		);

		/**
		* Definição da SQL
		*/
		$from = "D024";

		$extra = <<<EOT
			left join D018 on D024_D018_Id=D018_Id
			left join D020 on D024_D020_Id=D020_Id
			left join C007 as vendedor on vendedor.C007_Id=D024_C007_Vendedor_Interno
			left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
			left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
			where D024_Flag_Comercial='A'
			and D024_Flag_Cliente = 'S'
			and D024_Flag_Ativo = 'S'
			and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Data_Emissao' => array('titulo' => 'Vendas', 'tipo' => 'data', 'naoGerarExtra' => true),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'text'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Loop para criar colunas com as datas
		$T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
		if (!empty($T007_Data_Emissao[0]) && !empty($T007_Data_Emissao[1])) {
			$inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
			$fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
			$data = strtotime($inicio);
			$dataFim = strtotime($fim);
			$dataInicial = $data;
			$dataFinal = $fim;
	
			while($data < $dataFim){
				if($data == $dataInicial){
					$data1 = $data;
				} else {
					$data1 = strtotime(date('Y-m-1', $data));
				}
				
				if(strtotime(date('Y-m-t', $data)) > $dataFim){
					$data2 = $dataFim;	
				} else {
					$data2 = strtotime(date('Y-m-t', $data));
				}		
				//echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";
				
				$data1str = date('Y-m-d', $data1);
				$data2str = date('Y-m-d', $data2);
						
				$subSql = <<<EOT
				IFNULL((
					SELECT 
						SUM(T007_Valor_Total) 
					FROM 
						T007
					left join C004 on C004_Id=T007_C004_Id 
					left join D006 on D006_Id=T007_D006_Id 
					WHERE T007_D024_Id = D024_Id 
						AND T007_Data_Emissao >= '{$data1str} 00:00:00' 
						AND T007_Data_Emissao <= '{$data2str} 23:59:59'
						),0)
EOT;
				$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
				$data = strtotime("+1 month", $data);
			}
		}
		$extraTotal = gRetirarGroupBy($extra);
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Clientes<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<button type="button" id="crm001ProcessarCarteira">Processar Carteira</button>
				<div style="clear:both;"></div>
			</div>
EOT;

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
					var acaoId = id[0].replace('D024_Id-', '');
					// Ação: abrir nova janela
		    		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });

				$('#crm001ProcessarCarteira').unbind('click').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/sistema/ajax/processarCarteiraClientes/?ajax=true&callback=?', function(request) {
				        $('#{$g['divId']}').hideLoading();
				        if (request.code) { 
				           dialogConfirm('Processar Carteira', request.data, false);
				        } else {
				            dialogConfirm('Erro', request.data, false);
				        }
				    });
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));

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
	
	case 'crm001GridPrincipalClientesInativos':
		global $g;

		$filtroObrigatorio=false;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;font-weight:bold'),
			array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;font-weight:normal', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D024_Observacao_Inativo', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaExibir' => false),
            array('colunaValor' => 'D024_C007_Vendedor_Interno', 'colunaTitulo' => 'Vendedor', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorInterno', 'style' => 'width:1%'),
			array('colunaValor' => 'Externo.C007_Primeiro_Nome', 'colunaTitulo' => 'Externo', 'colunaExibir' => false),
            array('colunaValor' => 'D024_C007_Vendedor_Externo', 'colunaTitulo' => 'Externo', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'order by C007_Primeiro_Nome', true), 'callback' => 'mostrarVendedorExterno', 'style' => 'width:1%'),
			array('colunaValor' => 'ativarCliente', 'colunaTitulo' => '', 'callback' => 'campoAtivarCliente', 'colunaTipo' => 'livre', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
			array('colunaValor' => '(select SUM(T007_Valor_Total) from T007 where T007_D024_Id=D024_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim") as Total', 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero'),
			// T007_Valor_Total_Comissao
			// C007_Primeiro_Nome
		);

		/**
		* Definição da SQL
		*/
		$from = "D024";

		$extra = <<<EOT
			left join D018 on D024_D018_Id=D018_Id
			left join D020 on D024_D020_Id=D020_Id
			left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
			left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
			left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
			where D024_Flag_Comercial!='A'
			  and D024_Flag_Ativo = 'S'
			  and D024_Nome_Empresa!=''
			  and D024_Flag_Cliente = 'S'
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Data_Emissao' => array('titulo' => 'Vendas', 'tipo' => 'data', 'naoGerarExtra' => true),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 1', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'text'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Loop para criar colunas com as datas
		$T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
		if (!empty($T007_Data_Emissao[0]) && !empty($T007_Data_Emissao[1])) {
			$inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
			$fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
			$data = strtotime($inicio);
			$dataFim = strtotime($fim);
			$dataInicial = $data;
			$dataFinal = $fim;
	
			while($data < $dataFim){
				if($data == $dataInicial){
					$data1 = $data;
				} else {
					$data1 = strtotime(date('Y-m-1', $data));
				}
				
				if(strtotime(date('Y-m-t', $data)) > $dataFim){
					$data2 = $dataFim;	
				} else {
					$data2 = strtotime(date('Y-m-t', $data));
				}		
				//echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";
				
				$data1str = date('Y-m-d', $data1);
				$data2str = date('Y-m-d', $data2);
						
				$subSql = <<<EOT
				IFNULL((
					SELECT 
						SUM(T007_Valor_Total) 
					FROM 
						T007
					left join C004 on C004_Id=T007_C004_Id 
					left join D006 on D006_Id=T007_D006_Id 
					WHERE T007_D024_Id = D024_Id 
						AND T007_Data_Emissao >= '{$data1str} 00:00:00' 
						AND T007_Data_Emissao <= '{$data2str} 23:59:59'
						),0)
EOT;
				$grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
				$data = strtotime("+1 month", $data);
			}
		}
		$extraTotal = gRetirarGroupBy($extra);
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Clientes<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<button type="button" id="crm001ProcessarCarteiraInativos">Processar Carteira</button>
				<div style="clear:both;"></div>
			</div>
EOT;

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
					var acaoId = id[0].replace('D024_Id-', '');
					// Ação: abrir nova janela
		    		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });

				$('#crm001ProcessarCarteiraInativos').unbind('click').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/sistema/ajax/processarCarteiraClientes/?ajax=true&callback=?', function(request) {
				        $('#{$g['divId']}').hideLoading();
				        if (request.code) { 
				           dialogConfirm('Processar Carteira', request.data, false);
				        } else {
				            dialogConfirm('Erro', request.data, false);
				        }
				    });
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));

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
		
	case 'crm001GridResumo':
		echo <<<EOT
			<div class="radioResumoUI" style="padding:5px;">
				<input type="radio" id="radioResumo1" class="noStyle" checked="checked" name="radio" /><label for="radioResumo1">Diario</label>
				<input type="radio" id="radioResumo2" class="noStyle" name="radio" /><label for="radioResumo2">Mensal</label>
				<input type="radio" id="radioResumo3" class="noStyle" name="radio" /><label for="radioResumo3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioResumoUI" ).buttonset();
				});
				$('#radioResumo2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoMensal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "crm001_Dia_Semana", 'colunaTitulo' => 'Dia', 'callback' => "crm001_Dia_Semana", 'colunaTipo'=>'livre', 'style' => 'width:1%'),
			array('colunaValor' => "R001_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_Clientes_Nr", 'colunaTitulo' => 'Clientes', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Clientes_Novos", 'colunaTitulo' => 'Novos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Clientes_Ativos", 'colunaTitulo' => 'Ativado', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Clientes_Inativos", 'colunaTitulo' => 'Inativado', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Clientes_Contatos", 'colunaTitulo' => 'Agenda', 'colunaAlinhar' => 'center', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "R001_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "R001_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "R001_Faturados_Nr", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R001_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "R001_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4'),
			array('colunaValor' => "R001_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaExibir' => false),
			array('colunaValor' => "R001_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R001_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "R001_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R001';

		// Somente para ter acesso ao filtro
		$gridFiltro = array(
			'R001_Data' => array('titulo' => 'Período', 'tipo' => 'data'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'Cod. CFOP', 'tipo' => 'texto'),
		);
		$g['ui']->gridFiltroExtra(true, $gridFiltro, 'WHERE (0=1)');
		$fVendedor = $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') ? '' : 'AND R001_C007_Id_Vendedor = 0';
		$fSupervisor = $g['ui']->getGridFiltroCampoBusca('Supervisor.C007_Primeiro_Nome') ? '' : 'AND R001_C007_Id_Supervisor= 0';
		$fCliente = $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') ? '' : 'AND R001_D024_Id = 0';
		$fD049 = $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') ? '' : 'AND R001_D049_Id = 0';
		$fCFOP = $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP') ? '' : 'AND R001_D006_Id = 0';

		if ($g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') || $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') || $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP')) {
			$gridFiltro = array();
			$gridFiltro['R001_Data'] = array('titulo' => 'Período', 'tipo' => 'data');
			if (!$fVendedor) {
				$gridFiltro['Vendedor.C007_Primeiro_Nome'] = array('titulo' => 'Vendedor', 'tipo' => 'texto');
			}
			if (!$fSupervisor) {
				$gridFiltro['Supervisor.C007_Primeiro_Nome'] = array('titulo' => 'Supervisor', 'tipo' => 'texto');
			}
			if (!$fCliente) {
				$gridFiltro['D024_Nome_Empresa,D024_Nome_Fantasia'] = array('titulo' => 'Cliente', 'tipo' => 'texto');
			}
			if (!$fD049) {
				$gridFiltro['D001_Codigo_Produto'] = array('titulo' => 'Cod. Produto', 'tipo' => 'texto');
				$gridFiltro['D082_Marca'] = array('titulo' => 'Marca', 'tipo' => 'texto');
			}
			if (!$fCFOP) {
				$gridFiltro['D006_Codigo_CFOP'] = array('titulo' => 'Cod. CFOP', 'tipo' => 'texto');
			}
		}
		
		$extra = <<<EOT
			# Não personalizar este EXTRA! Ele é gerado dentro do grid.php dependendo dos filtros.
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			left join D024 on D024_Id = R001_D024_Id
			left join D049 on D049_Id = R001_D049_Id
			left join D001 on D001_Id = D049_D001_Id
			left join D082 on D082_Id = D049_D082_Id
			left join D006 on D006_Id = R001_D006_Id
			WHERE 
				(1=1)
				{$fVendedor}
				{$fSupervisor}
				{$fCliente}
				{$fD049}
				{$fCFOP}
			GROUP BY 
				R001_Data
			ORDER BY R001_Data DESC
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		

		/**
		* Botões (Versão Acima do GRID)
		*/

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
		$primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Clientes atendidos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;	
	
	case 'crm001GridResumoMensal':
		echo <<<EOT
			<div class="radioResumoUI" style="padding:5px;">
				<input type="radio" id="radioResumo1" class="noStyle" name="radio" /><label for="radioResumo1">Diario</label>
				<input type="radio" id="radioResumo2" class="noStyle" checked="checked" name="radio" /><label for="radioResumo2">Mensal</label>
				<input type="radio" id="radioResumo3" class="noStyle" name="radio" /><label for="radioResumo3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioResumoUI" ).buttonset();
				});
				$('#radioResumo1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumo/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R003_Id", 'colunaTitulo' => 'R003_Id', 'colunaExibir' => false),
			array('colunaValor' => "R003_Data", 'colunaTitulo' => 'Data', 'callback'=>"retornaMes", 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R003_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R003_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R003_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "SUM(R003_Clientes_Nr) as R003_Clientes_Nr", 'colunaTitulo' => 'Clientes', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Clientes_Novos) as R003_Clientes_Novos", 'colunaTitulo' => 'Novos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Clientes_Ativos) as R003_Clientes_Ativos", 'colunaTitulo' => 'Ativado', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Clientes_Inativos) as R003_Clientes_Inativos", 'colunaTitulo' => 'Inativado', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Clientes_Contatos) as R003_Clientes_Contatos", 'colunaTitulo' => 'Agenda', 'colunaAlinhar' => 'center', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R003_Orcamentos_Nr) as R003_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Orcamentos_Clientes) as R003_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Orcamentos_Itens) as R003_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R003_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Orcamentos_Valor_Total) as R003_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R003_Pedidos_Nr) as R003_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Pedidos_Clientes) as R003_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Pedidos_Itens) as R003_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R003_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Pedidos_Valor_Total) as R003_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R003_Faturados_Nr) as R003_Faturados_Nr", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R003_Faturados_Clientes) as R003_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Faturados_Itens) as R003_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R003_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Faturados_Valor_Total) as R003_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R003_Faturados_IPV) as R003_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4'),
			array('colunaValor' => "SUM(R003_Faturados_IPV_Real) as R003_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaExibir' => false),
			array('colunaValor' => "SUM(R003_Devolucoes_Nr) as R003_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R003_Devolucoes_Clientes) as R003_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Devolucoes_Valor_Total) as R003_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R003_Compras_Nr) as R003_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Compras_Clientes) as R003_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Compras_Valor_Total) as R003_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R003';

		// Somente para ter acesso ao filtro
		$gridFiltro = array(
			'R003_Data' => array('titulo' => 'Período', 'tipo' => 'data'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'Cod. CFOP', 'tipo' => 'texto'),
		);
		$g['ui']->gridFiltroExtra(true, $gridFiltro, 'WHERE (0=1)');
		$fVendedor = $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') ? '' : 'AND R003_C007_Id_Vendedor = 0';
		$fSupervisor = $g['ui']->getGridFiltroCampoBusca('Supervisor.C007_Primeiro_Nome') ? '' : 'AND R003_C007_Id_Supervisor= 0';
		$fCliente = $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') ? '' : 'AND R003_D024_Id = 0';
		$fD049 = $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') ? '' : 'AND R003_D049_Id = 0';
		$fCFOP = $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP') ? '' : 'AND R003_D006_Id = 0';

		if ($g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') || $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') || $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP')) {
			$gridFiltro = array();
			$gridFiltro['R003_Data'] = array('titulo' => 'Período', 'tipo' => 'data');
			if (!$fVendedor) {
				$gridFiltro['Vendedor.C007_Primeiro_Nome'] = array('titulo' => 'Vendedor', 'tipo' => 'texto');
			}
			if (!$fSupervisor) {
				$gridFiltro['Supervisor.C007_Primeiro_Nome'] = array('titulo' => 'Supervisor', 'tipo' => 'texto');
			}
			if (!$fCliente) {
				$gridFiltro['D024_Nome_Empresa,D024_Nome_Fantasia'] = array('titulo' => 'Cliente', 'tipo' => 'texto');
			}
			if (!$fD049) {
				$gridFiltro['D001_Codigo_Produto'] = array('titulo' => 'Cod. Produto', 'tipo' => 'texto');
				$gridFiltro['D082_Marca'] = array('titulo' => 'Marca', 'tipo' => 'texto');
			}
			if (!$fCFOP) {
				$gridFiltro['D006_Codigo_CFOP'] = array('titulo' => 'Cod. CFOP', 'tipo' => 'texto');
			}
		}
		
		$extra = <<<EOT
			# Não personalizar este EXTRA! Ele é gerado dentro do grid.php dependendo dos filtros.
			left join C007 as Vendedor on Vendedor.C007_Id=R003_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R003_C007_Id_Supervisor
			left join D024 on D024_Id = R003_D024_Id
			left join D049 on D049_Id = R003_D049_Id
			left join D001 on D001_Id = D049_D001_Id
			left join D082 on D082_Id = D049_D082_Id
			left join D006 on D006_Id = R003_D006_Id
			WHERE 
				(1=1)
				{$fVendedor}
				{$fSupervisor}
				{$fCliente}
				{$fD049}
				{$fCFOP}
			GROUP BY 
				EXTRACT(YEAR_MONTH FROM R003_Data)
			ORDER BY EXTRACT(YEAR_MONTH FROM R003_Data) DESC
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		

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
		$primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="2" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Clientes atendidos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;

	case 'crm001GridResumoAnual':
		echo <<<EOT
			<div class="radioResumoUI" style="padding:5px;">
				<input type="radio" id="radioResumo1" class="noStyle" name="radio" /><label for="radioResumo1">Diario</label>
				<input type="radio" id="radioResumo2" class="noStyle" name="radio" /><label for="radioResumo2">Mensal</label>
				<input type="radio" id="radioResumo3" class="noStyle" checked="checked" name="radio" /><label for="radioResumo3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioResumoUI" ).buttonset();
				});
				$('#radioResumo1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumo/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoMensal/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R004_Id", 'colunaTitulo' => 'R004_Id', 'colunaExibir' => false),
			array('colunaValor' => "R004_Data", 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback'=>"retornaAno"),
			array('colunaValor' => "R004_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R004_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R004_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "SUM(R004_Clientes_Nr) as R004_Clientes_Nr", 'colunaTitulo' => 'Clientes', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Clientes_Novos) as R004_Clientes_Novos", 'colunaTitulo' => 'Novos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Clientes_Ativos) as R004_Clientes_Ativos", 'colunaTitulo' => 'Ativado', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Clientes_Inativos) as R004_Clientes_Inativos", 'colunaTitulo' => 'Inativado', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Clientes_Contatos) as R004_Clientes_Contatos", 'colunaTitulo' => 'Agenda', 'colunaAlinhar' => 'center', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R004_Orcamentos_Nr) as R004_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Orcamentos_Clientes) as R004_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Orcamentos_Itens) as R004_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R004_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Orcamentos_Valor_Total) as R004_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R004_Pedidos_Nr) as R004_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Pedidos_Clientes) as R004_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Pedidos_Itens) as R004_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R004_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Pedidos_Valor_Total) as R004_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R004_Faturados_Nr) as R004_Faturados_Nr", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R004_Faturados_Clientes) as R004_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Faturados_Itens) as R004_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R004_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Faturados_Valor_Total) as R004_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R004_Faturados_IPV) as R004_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4'),
			array('colunaValor' => "SUM(R004_Faturados_IPV_Real) as R004_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaExibir' => false),
			array('colunaValor' => "SUM(R004_Devolucoes_Nr) as R004_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R004_Devolucoes_Clientes) as R004_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Devolucoes_Valor_Total) as R004_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => "SUM(R004_Compras_Nr) as R004_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Compras_Clientes) as R004_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Compras_Valor_Total) as R004_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: var(--celula-amarelo-claro);"),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R004';

		// Somente para ter acesso ao filtro
		$gridFiltro = array(
			'R004_Data' => array('titulo' => 'Período', 'tipo' => 'data'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'Cod. CFOP', 'tipo' => 'texto'),
		);
		$g['ui']->gridFiltroExtra(true, $gridFiltro, 'WHERE (0=1)');
		$fVendedor = $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') ? '' : 'AND R004_C007_Id_Vendedor = 0';
		$fSupervisor = $g['ui']->getGridFiltroCampoBusca('Supervisor.C007_Primeiro_Nome') ? '' : 'AND R004_C007_Id_Supervisor= 0';
		$fCliente = $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') ? '' : 'AND R004_D024_Id = 0';
		$fD049 = $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') ? '' : 'AND R004_D049_Id = 0';
		$fCFOP = $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP') ? '' : 'AND R004_D006_Id = 0';

		if ($g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') || $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') || $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP')) {
			$gridFiltro = array();
			$gridFiltro['R004_Data'] = array('titulo' => 'Período', 'tipo' => 'data');
			if (!$fVendedor) {
				$gridFiltro['Vendedor.C007_Primeiro_Nome'] = array('titulo' => 'Vendedor', 'tipo' => 'texto');
			}
			if (!$fSupervisor) {
				$gridFiltro['Supervisor.C007_Primeiro_Nome'] = array('titulo' => 'Supervisor', 'tipo' => 'texto');
			}
			if (!$fCliente) {
				$gridFiltro['D024_Nome_Empresa,D024_Nome_Fantasia'] = array('titulo' => 'Cliente', 'tipo' => 'texto');
			}
			if (!$fD049) {
				$gridFiltro['D001_Codigo_Produto'] = array('titulo' => 'Cod. Produto', 'tipo' => 'texto');
				$gridFiltro['D082_Marca'] = array('titulo' => 'Marca', 'tipo' => 'texto');
			}
			if (!$fCFOP) {
				$gridFiltro['D006_Codigo_CFOP'] = array('titulo' => 'Cod. CFOP', 'tipo' => 'texto');
			}
		}
		
		$extra = <<<EOT
			# Não personalizar este EXTRA! Ele é gerado dentro do grid.php dependendo dos filtros.
			left join C007 as Vendedor on Vendedor.C007_Id=R004_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R004_C007_Id_Supervisor
			left join D024 on D024_Id = R004_D024_Id
			left join D049 on D049_Id = R004_D049_Id
			left join D001 on D001_Id = D049_D001_Id
			left join D082 on D082_Id = D049_D082_Id
			left join D006 on D006_Id = R004_D006_Id
			WHERE 
				(1=1)
				{$fVendedor}
				{$fSupervisor}
				{$fCliente}
				{$fD049}
				{$fCFOP}
			GROUP BY 
				Year(R004_Data)
			ORDER BY R004_Data DESC
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		
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
		$primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="2" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Clientes atendidos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;	
	
	case 'crm001GridABCClientes':
		// Retira a limitação de Nr. de Registros
		$g['cacheCount'] = true;
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "D024_Id", 'colunaTitulo' => 'D024_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback'=>"retornaAno"),
			array('colunaValor' => "D024_Flag_Curva_Abc", 'colunaTitulo' => 'ABC', 'style' => 'width:1%', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
			array('colunaValor' => 'D024_Flag_Classe', 'colunaTitulo' => 'Cla', 'colunaAlinhar' => 'center', 'callback' => 'colunaClasseCliente', 'callbackParameter' => array('Classe cliente', '/cad/cad001/outro/classeCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa', 'colunaAlinhar' => 'left'),
			array('colunaValor' => "D024_Nome_Fantasia", 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),			
			array('colunaValor' => "sum(R001_Orcamentos_Valor_Total) as R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Orçamentos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "sum(R001_Faturados_Valor_Total) as R001_Faturados_Valor_Total", 'colunaTitulo' => 'Faturados', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "ROUND((sum(R001_Faturados_Valor_Total)*100)/sum(R001_Orcamentos_Valor_Total), 0) as Porcentagem", 'colunaTitulo' => '% Orc./Fat.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '0', 'style' => 'width:1%'),		
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		/*$xLast12 = date('Y-m-d');
		$xLast12 = new \DateTime($xLast12);
		$xLast12->modify('-12 months');
		$xLast12 = $xLast12->format('Y-m-d');
		//AND R001_Data >= '{$xLast12}'*/

		$extra = <<<EOT
			left join D024 on D024_Id=R001_D024_Id
			WHERE 
				R001_D024_Id > 0 
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id = 0 
				AND R001_D006_Id = 0
			GROUP BY R001_D024_Id
			ORDER BY
				sum(R001_Faturados_Valor_Total) desc
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'R001_Faturados_Valor_Total' => array('titulo' => 'Valor Faturado', 'tipo' => 'numero'),
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$vazio = "";
		$extraABC = $g['ui']->gridFiltroExtra(false, $gridFiltro, $vazio);		
		$extraABC = explode('ORDER',$extraABC);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);
		/**
		* Botões (Versão Acima do GRID)
		*/
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);			
		$extraTotais = str_replace('GROUP BY R001_D024_Id', 'GROUP BY D024_Flag_Curva_ABC', $extra);
		$extraTotais = preg_split('/ORDER BY/i', $extraTotais);
		$extraTotais = $extraTotais[0];
		$curvaABC = <<<EOT
			SELECT COUNT(DISTINCT R001_D024_Id) Quantidade,
			SUM(R001_Faturados_Valor_Total) Total,
			D024_Flag_Curva_ABC
			FROM R001 
			{$extraTotais}
EOT;
		/*
			LEFT JOIN D024 ON R001_D024_Id = D024_Id
			WHERE 
				R001_D024_Id > 0 
				AND R001_Data LIKE '{$Ano}%'
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id = 0 
				AND R001_D006_Id = 0
			GROUP BY D024_Flag_Curva_ABC
		*/
		$total = 0;
		$totalQuantidade = 0;
		$result = mysql_query($curvaABC);
		/**
		* Totalizadores
		*/
		echo <<<EOT
		<script type="text/css">
		.totalizadorABC table {
			border: 1px solid #70A8D2;
			border-radius: 5px;
			margin: 3px 3px 3px 2px;	
		}
		</script>
			<div class="buttonsBottomTop">
EOT;
		while($curvaABC = mysql_fetch_array($result)) {
			$total+=$curvaABC["Total"];
			$curvaABC["Total"] = gCorrigeNumero($curvaABC["Total"]);
			$totalQuantidade+=$curvaABC["Quantidade"];
			echo <<<EOT
			 		<span class="item">Curva {$curvaABC["D024_Flag_Curva_ABC"]}<br>
			 		<span style="text-align:right">
			 		<input type="text" value="{$curvaABC["Quantidade"]}" class="whiteBg" id="" size="3" readonly>
			 		<input type="text" value="{$curvaABC["Total"]}" class="whiteBg" id="" size="8" readonly>
			 		</span></span>				
EOT;
		}
		$total = gCorrigeNumero($total);
		echo <<<EOT
				<span class="item">Total<br>
				<span style="text-align:right">
				<input type="text" value="{$totalQuantidade}" class="whiteBg" id="" size="4" readonly>
				<input type="text" value="{$total}" class="whiteBg" id="" size="10" readonly>
				</span></span>
			
			<!--<button type="button" id="processarCarteira" style="margin-top: 5px;">Processar Carteira</button>-->	
			<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#processarCarteira').unbind('click').bind('click', function(data) {
					nova_janela('../hardness3/old/lib/processa_carteira_clientes.php?data=',0,0,700,600,'no','no','no','no','yes','yes',0);
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			
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
	
	case 'crm001GridABCProdutos':
		// Retira a limitação de Nr. de Registros
		$g['cacheCount'] = true;
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "D009_Id", 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback'=>"retornaAno"),
			array('colunaValor' => "D049_Flag_Curva_Abc", 'colunaTitulo' => 'ABC', 'style' => 'width:1%', 'colunaAlinhar' => 'center', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', false, 'exibirTotal', false, '720')),
			array('colunaValor' => "D001_Descricao_Produto", 'colunaTitulo' => 'Produto', 'colunaAlinhar' => 'left'),
			array('colunaValor' => "D082_Marca", 'colunaTitulo' => 'Marca', 'colunaAlinhar' => 'left'),
			array('colunaValor' => "SUM(R001_Orcamentos_Itens) as Itens", 'colunaTitulo' => 'Orçamentos Itens', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Orcamentos_Valor_Total) as R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Orçamentos Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "width:1%; background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_Itens) as itensFaturados", 'colunaTitulo' => 'Faturados Itens', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Faturados_Valor_Total) as R001_Faturados_Valor_Total", 'colunaTitulo' => 'Faturados Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "width:1%; background-color: #D7ECF7;"),
			array('colunaValor' => "ROUND((SUM(R001_Faturados_Valor_Total)*100)/SUM(R001_Orcamentos_Valor_Total), 0) as faturadosTotal", 'colunaTitulo' => '% Orc./Fat.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '0', 'style' => 'width:1%'),
		);
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		/*$xLast12 = date('Y-m-d');
		$xLast12 = new \DateTime($xLast12);
		$xLast12->modify('-12 months');
		$xLast12 = $xLast12->format('Y-m-d');
		//AND R001_Data >= '{$xLast12}'*/
		

		$extra = <<<EOT
			left join D049 on D049_Id=R001_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D009 on D009_D049_Id=D049_Id AND D009_C004_Id = '{$g['empresaAtual']}'
			left join D082 on D082_Id=D049_D082_Id
			WHERE 
				R001_D024_Id = 0 
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id > 0 
				AND R001_D006_Id = 0
				AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null) 
			GROUP BY R001_D049_Id
			order by 
				sum(R001_Faturados_Valor_Total) desc
EOT;
		$gridFiltro = array(
			'D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'R001_Faturados_Valor_Total' => array('titulo' => 'Valor Faturado', 'tipo' => 'numero'),
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraABC = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
				
		/**
		* Botões (Versão Acima do GRID)
		*/
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);	
		$extraTotais = str_replace('GROUP BY R001_D049_Id', 'GROUP BY D049_Flag_Curva_ABC', $extra);
		$extraTotais = preg_split('/ORDER BY/i', $extraTotais);
		$extraTotais = $extraTotais[0];
		$curvaABC = <<<EOT
			SELECT COUNT(DISTINCT R001_D049_Id) Quantidade,
			SUM(R001_Faturados_Valor_Total) Total,
			D049_Flag_Curva_ABC
			FROM R001
			{$extraTotais}
EOT;
		/*
				left join D049 on D049_Id=R001_D049_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D082 on D082_Id=D049_D082_Id
			WHERE 
				R001_D024_Id = 0 
				AND R001_Data >= '{$xLast12}'
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id > 0 
				AND R001_D006_Id = 0
			GROUP BY D049_Flag_Curva_ABC
		*/
		$total = 0;
		$totalQuantidade = 0;
		$result = mysql_query($curvaABC);
		
		
		/**
		* Totalizadores
		*/
		
		echo <<<EOT
		<script type="text/css">
		.totalizadorABC table {
			border: 1px solid #70A8D2;
			border-radius: 5px;
			margin: 3px 3px 3px 2px;	
		}
		</script>
			<div class="buttonsBottomTop">
EOT;
		while($curvaABC = mysql_fetch_array($result)) {
			$total+=$curvaABC["Total"];
			$curvaABC["Total"] = gCorrigeNumero($curvaABC["Total"]);
			$totalQuantidade+=$curvaABC["Quantidade"];
			echo <<<EOT
			 		<span class="item">Curva {$curvaABC["D049_Flag_Curva_ABC"]}<br>
			 		<span style="text-align:right">
			 		<input type="text" value="{$curvaABC["Quantidade"]}" class="whiteBg" id="" size="3" readonly>
			 		<input type="text" value="{$curvaABC["Total"]}" class="whiteBg" id="" size="10" readonly>
			 		</span></span>				
EOT;
		}
		$total = gCorrigeNumero($total);
		echo <<<EOT
				<span class="item">Total<br>
				<span style="text-align:right">
				<input type="text" value="{$totalQuantidade}" class="whiteBg" id="" size="3" readonly>
				<input type="text" value="{$total}" class="whiteBg" id="" size="10" readonly>
				</span></span>
			
			<!--<button type="button" id="processarCarteira" style="margin-top: 5px;">Processar Carteira</button>-->
			<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#processarCarteira').unbind('click').bind('click', function(data) {
					nova_janela('../hardness3/old/lib/processa_carteira_clientes.php?data=',0,0,700,600,'no','no','no','no','yes','yes',0);
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
			
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
	
	case 'crm001GridFiscal':
		// Retira a limitação de Nr. de Registros
		$g['cacheCount'] = true;

		echo <<<EOT
			<div class="radioFiscalUI" style="padding:5px;">
				<input type="radio" id="radioFiscal1" class="noStyle" checked="checked" name="radio" /><label for="radioFiscal1">Diario</label>
				<input type="radio" id="radioFiscal2" class="noStyle" name="radio" /><label for="radioFiscal2">Mensal</label>
				<input type="radio" id="radioFiscal3" class="noStyle" name="radio" /><label for="radioFiscal3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioFiscalUI" ).buttonset();
				});
				$('#radioFiscal2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridFiscalMensal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFiscal3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridFiscalAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
			array('colunaValor' => "R001_Saida_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Saida_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Saida_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Saida_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Saida_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Saida_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Saida_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Saida_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Saida_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Entrada_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Entrada_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Entrada_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Entrada_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Entrada_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Entrada_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Entrada_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Entrada_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R001_Entrada_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);		

		/**
		* Definição da SQL
		*/
		$from = 'R001';

		// Somente para ter acesso ao filtro
		$gridFiltro = array(
			'R001_Data' => array('titulo' => 'Período', 'tipo' => 'data'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'Cod. CFOP', 'tipo' => 'texto'),
		);
		$g['ui']->gridFiltroExtra(true, $gridFiltro, 'WHERE (0=1)');
		$fVendedor = $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') ? '' : 'AND R001_C007_Id_Vendedor = 0';
		$fSupervisor = $g['ui']->getGridFiltroCampoBusca('Supervisor.C007_Primeiro_Nome') ? '' : 'AND R001_C007_Id_Supervisor= 0';
		$fCliente = $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') ? '' : 'AND R001_D024_Id = 0';
		$fD049 = $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') ? '' : 'AND R001_D049_Id = 0';
		$fCFOP = $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP') ? '' : 'AND R001_D006_Id = 0';

		if ($g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') || $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') || $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP')) {
			$gridFiltro = array();
			$gridFiltro['R001_Data'] = array('titulo' => 'Período', 'tipo' => 'data');
			if (!$fVendedor) {
				$gridFiltro['Vendedor.C007_Primeiro_Nome'] = array('titulo' => 'Vendedor', 'tipo' => 'texto');
			}
			if (!$fSupervisor) {
				$gridFiltro['Supervisor.C007_Primeiro_Nome'] = array('titulo' => 'Supervisor', 'tipo' => 'texto');
			}
			if (!$fCliente) {
				$gridFiltro['D024_Nome_Empresa,D024_Nome_Fantasia'] = array('titulo' => 'Cliente', 'tipo' => 'texto');
			}
			if (!$fD049) {
				$gridFiltro['D001_Codigo_Produto'] = array('titulo' => 'Cod. Produto', 'tipo' => 'texto');
				$gridFiltro['D082_Marca'] = array('titulo' => 'Marca', 'tipo' => 'texto');
			}
			if (!$fCFOP) {
				$gridFiltro['D006_Codigo_CFOP'] = array('titulo' => 'Cod. CFOP', 'tipo' => 'texto');
			}
		}

		$extra = <<<EOT
			# Não personalizar este EXTRA! Ele é gerado dentro do grid.php dependendo dos filtros.
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			left join D024 on D024_Id = R001_D024_Id
			left join D049 on D049_Id = R001_D049_Id
			left join D001 on D001_Id = D049_D001_Id
			left join D082 on D082_Id = D049_D082_Id
			left join D006 on D006_Id = R001_D006_Id
			WHERE 
				(1=1)
				{$fVendedor}
				{$fSupervisor}
				{$fCliente}
				{$fD049}
				{$fCFOP}
			order by 
				R001_Data DESC
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$vazio = "";
		$extraABC = $g['ui']->gridFiltroExtra(false, $gridFiltro, $vazio);		
		$extraABC = explode('ORDER',$extraABC);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		
		/**
		* Botões (Versão Acima do GRID)
		*/
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
         $primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="2" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="9" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais Emitidas</th>
	<th colspan="9" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais Recebidas</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;	
	
	case 'crm001GridFiscalMensal':
		// Retira a limitação de Nr. de Registros
		$g['cacheCount'] = true;

		echo <<<EOT
			<div class="radioFiscalUI" style="padding:5px;">
				<input type="radio" id="radioFiscal1" class="noStyle" name="radio" /><label for="radioFiscal1">Diario</label>
				<input type="radio" id="radioFiscal2" class="noStyle" checked="checked" name="radio" /><label for="radioFiscal2">Mensal</label>
				<input type="radio" id="radioFiscal3" class="noStyle" name="radio" /><label for="radioFiscal3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioFiscalUI" ).buttonset();
				});
				$('#radioFiscal1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridFiscal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFiscal3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridFiscalAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R003_Id", 'colunaTitulo' => 'R003_Id', 'colunaExibir' => false),
			array('colunaValor' => "R003_Data", 'colunaTitulo' => 'Mês', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => "retornaMes"),
			array('colunaValor' => "SUM(R003_Saida_Nr) as R003_Saida_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Saida_Itens) as R003_Saida_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Saida_Valor_Total_Produtos) as R003_Saida_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Saida_Tributos_ICMS) as R003_Saida_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Saida_Tributos_ICMS_ST) as R003_Saida_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Saida_Tributos_IPI) as R003_Saida_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Saida_Tributos_PIS) as R003_Saida_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Saida_Tributos_COFINS) as R003_Saida_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Saida_Valor_Total) as R003_Saida_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Entrada_Nr) as R003_Entrada_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Entrada_Itens) as R003_Entrada_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R003_Entrada_Valor_Total_Produtos) as R003_Entrada_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Entrada_Tributos_ICMS) as R003_Entrada_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Entrada_Tributos_ICMS_ST) as R003_Entrada_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Entrada_Tributos_IPI) as R003_Entrada_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Entrada_Tributos_PIS) as R003_Entrada_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Entrada_Tributos_COFINS) as R003_Entrada_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R003_Entrada_Valor_Total) as R003_Entrada_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);		
		
		/**
		* Definição da SQL
		*/
		$from = 'R003';

		// Somente para ter acesso ao filtro
		$gridFiltro = array(
			'R003_Data' => array('titulo' => 'Período', 'tipo' => 'data'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'Cod. CFOP', 'tipo' => 'texto'),
		);
		$g['ui']->gridFiltroExtra(true, $gridFiltro, 'WHERE (0=1)');
		$fVendedor = $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') ? '' : 'AND R003_C007_Id_Vendedor = 0';
		$fSupervisor = $g['ui']->getGridFiltroCampoBusca('Supervisor.C007_Primeiro_Nome') ? '' : 'AND R003_C007_Id_Supervisor= 0';
		$fCliente = $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') ? '' : 'AND R003_D024_Id = 0';
		$fD049 = $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') ? '' : 'AND R003_D049_Id = 0';
		$fCFOP = $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP') ? '' : 'AND R003_D006_Id = 0';

		if ($g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') || $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') || $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP')) {
			$gridFiltro = array();
			$gridFiltro['R003_Data'] = array('titulo' => 'Período', 'tipo' => 'data');
			if (!$fVendedor) {
				$gridFiltro['Vendedor.C007_Primeiro_Nome'] = array('titulo' => 'Vendedor', 'tipo' => 'texto');
			}
			if (!$fSupervisor) {
				$gridFiltro['Supervisor.C007_Primeiro_Nome'] = array('titulo' => 'Supervisor', 'tipo' => 'texto');
			}
			if (!$fCliente) {
				$gridFiltro['D024_Nome_Empresa,D024_Nome_Fantasia'] = array('titulo' => 'Cliente', 'tipo' => 'texto');
			}
			if (!$fD049) {
				$gridFiltro['D001_Codigo_Produto'] = array('titulo' => 'Cod. Produto', 'tipo' => 'texto');
				$gridFiltro['D082_Marca'] = array('titulo' => 'Marca', 'tipo' => 'texto');
			}
			if (!$fCFOP) {
				$gridFiltro['D006_Codigo_CFOP'] = array('titulo' => 'Cod. CFOP', 'tipo' => 'texto');
			}
		}
		
		$extra = <<<EOT
			# Não personalizar este EXTRA! Ele é gerado dentro do grid.php dependendo dos filtros.
			left join C007 as Vendedor on Vendedor.C007_Id=R003_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R003_C007_Id_Supervisor
			left join D024 on D024_Id = R003_D024_Id
			left join D049 on D049_Id = R003_D049_Id
			left join D001 on D001_Id = D049_D001_Id
			left join D082 on D082_Id = D049_D082_Id
			left join D006 on D006_Id = R003_D006_Id
			WHERE 
				(1=1)
				{$fVendedor}
				{$fSupervisor}
				{$fCliente}
				{$fD049}
				{$fCFOP}
			GROUP BY 
				EXTRACT(YEAR_MONTH FROM R003_Data)
			ORDER BY EXTRACT(YEAR_MONTH FROM R003_Data) DESC
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$vazio = "";
		$extraABC = $g['ui']->gridFiltroExtra(false, $gridFiltro, $vazio);		
		$extraABC = explode('ORDER',$extraABC);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		
		/**
		* Botões (Versão Acima do GRID)
		*/
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
         $primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="2" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="9" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais Emitidas</th>
	<th colspan="9" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais Recebidas</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;	
	
	case 'crm001GridFiscalAnual':
		// Retira a limitação de Nr. de Registros
		$g['cacheCount'] = true;

		echo <<<EOT
			<div class="radioFiscalUI" style="padding:5px;">
				<input type="radio" id="radioFiscal1" class="noStyle" name="radio" /><label for="radioFiscal1">Diario</label>
				<input type="radio" id="radioFiscal2" class="noStyle" name="radio" /><label for="radioFiscal2">Mensal</label>
				<input type="radio" id="radioFiscal3" class="noStyle" checked="checked" name="radio" /><label for="radioFiscal3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioFiscalUI" ).buttonset();
				});
				$('#radioFiscal1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridFiscal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFiscal2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridFiscalMensal/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R004_Id", 'colunaTitulo' => 'R004_Id', 'colunaExibir' => false),
			array('colunaValor' => "R004_Data", 'colunaTitulo' => 'Ano', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => "retornaAno"),
			array('colunaValor' => "SUM(R004_Saida_Nr) as R004_Saida_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Saida_Itens) as R004_Saida_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Saida_Valor_Total_Produtos) as R004_Saida_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Saida_Tributos_ICMS) as R004_Saida_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Saida_Tributos_ICMS_ST) as R004_Saida_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Saida_Tributos_IPI) as R004_Saida_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Saida_Tributos_PIS) as R004_Saida_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Saida_Tributos_COFINS) as R004_Saida_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Saida_Valor_Total) as R004_Saida_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Entrada_Nr) as R004_Entrada_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Entrada_Itens) as R004_Entrada_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R004_Entrada_Valor_Total_Produtos) as R004_Entrada_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Entrada_Tributos_ICMS) as R004_Entrada_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Entrada_Tributos_ICMS_ST) as R004_Entrada_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Entrada_Tributos_IPI) as R004_Entrada_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Entrada_Tributos_PIS) as R004_Entrada_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Entrada_Tributos_COFINS) as R004_Entrada_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R004_Entrada_Valor_Total) as R004_Entrada_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);		
		
		/**
		* Definição da SQL
		*/
		$from = 'R004';

		// Somente para ter acesso ao filtro
		$gridFiltro = array(
			'R004_Data' => array('titulo' => 'Período', 'tipo' => 'data'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'Cod. CFOP', 'tipo' => 'texto'),
		);
		$g['ui']->gridFiltroExtra(true, $gridFiltro, 'WHERE (0=1)');
		$fVendedor = $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') ? '' : 'AND R004_C007_Id_Vendedor = 0';
		$fSupervisor = $g['ui']->getGridFiltroCampoBusca('Supervisor.C007_Primeiro_Nome') ? '' : 'AND R004_C007_Id_Supervisor= 0';
		$fCliente = $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') ? '' : 'AND R004_D024_Id = 0';
		$fD049 = $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') ? '' : 'AND R004_D049_Id = 0';
		$fCFOP = $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP') ? '' : 'AND R004_D006_Id = 0';

		if ($g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('Vendedor.C007_Primeiro_Nome') || $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa,D024_Nome_Fantasia') || $g['ui']->getGridFiltroCampoBusca('D001_Codigo_Produto') || $g['ui']->getGridFiltroCampoBusca('D082_Marca') || $g['ui']->getGridFiltroCampoBusca('D006_Codigo_CFOP')) {
			$gridFiltro = array();
			$gridFiltro['R004_Data'] = array('titulo' => 'Período', 'tipo' => 'data');
			if (!$fVendedor) {
				$gridFiltro['Vendedor.C007_Primeiro_Nome'] = array('titulo' => 'Vendedor', 'tipo' => 'texto');
			}
			if (!$fSupervisor) {
				$gridFiltro['Supervisor.C007_Primeiro_Nome'] = array('titulo' => 'Supervisor', 'tipo' => 'texto');
			}
			if (!$fCliente) {
				$gridFiltro['D024_Nome_Empresa,D024_Nome_Fantasia'] = array('titulo' => 'Cliente', 'tipo' => 'texto');
			}
			if (!$fD049) {
				$gridFiltro['D001_Codigo_Produto'] = array('titulo' => 'Cod. Produto', 'tipo' => 'texto');
				$gridFiltro['D082_Marca'] = array('titulo' => 'Marca', 'tipo' => 'texto');
			}
			if (!$fCFOP) {
				$gridFiltro['D006_Codigo_CFOP'] = array('titulo' => 'Cod. CFOP', 'tipo' => 'texto');
			}
		}
		
		$extra = <<<EOT
			# Não personalizar este EXTRA! Ele é gerado dentro do grid.php dependendo dos filtros.
			left join C007 as Vendedor on Vendedor.C007_Id=R004_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R004_C007_Id_Supervisor
			left join D024 on D024_Id = R004_D024_Id
			left join D049 on D049_Id = R004_D049_Id
			left join D001 on D001_Id = D049_D001_Id
			left join D082 on D082_Id = D049_D082_Id
			left join D006 on D006_Id = R004_D006_Id
			WHERE 
				(1=1)
				{$fVendedor}
				{$fSupervisor}
				{$fCliente}
				{$fD049}
				{$fCFOP}
			GROUP BY 
				Year(R004_Data)
			ORDER BY R004_Data DESC
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$vazio = "";
		$extraABC = $g['ui']->gridFiltroExtra(false, $gridFiltro, $vazio);		
		$extraABC = explode('ORDER',$extraABC);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		
		/**
		* Botões (Versão Acima do GRID)
		*/
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
         $primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="2" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="9" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais Emitidas</th>
	<th colspan="9" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais Recebidas</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;	
	
	case 'crm001GridResumoGerencial':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "D006_Tipo_CFOP", 'colunaTitulo' => 'D006_Tipo_CFOP', 'colunaExibir' => false),
			//array('colunaValor' => "D006_Tipo_CFOP", 'colunaTitulo' => 'Tipo CFOP', 'colunaAgrupar' => true, 'style' => 'width: 1%;text-align: center;background-color: white;'), //  'colunaExibir' => false
			array('colunaValor' => "D006_Codigo_CFOP", 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "D006_Descricao", 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
			array('colunaValor' => "count(D006_Codigo_CFOP) as Qtde", 'colunaTitulo' => 'R001_Id', 'colunaTitulo' => 'Qtde', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(IF(D006_Flag_Entrada_Saida='E', R001_Entrada_Valor_Total_Produtos, R001_Saida_Valor_Total_Produtos)) as Produtos", 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => "IF(D006_Flag_Entrada_Saida='E', R001_Entrada_Itens, R001_Saida_Itens) as Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "IF(D006_Flag_Entrada_Saida='E', R001_Entrada_Tributos_ICMS, R001_Saida_Tributos_ICMS) as Itens2", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "IF(D006_Flag_Entrada_Saida='E', R001_Entrada_Tributos_ICMS_ST, R001_Saida_Tributos_ICMS_ST) as Itens3", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "IF(D006_Flag_Entrada_Saida='E', R001_Entrada_Tributos_IPI, R001_Saida_Tributos_IPI) as Itens4", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "IF(D006_Flag_Entrada_Saida='E', R001_Entrada_Tributos_PIS, R001_Saida_Tributos_PIS) as Itens5", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "IF(D006_Flag_Entrada_Saida='E', R001_Entrada_Tributos_COFINS, R001_Saida_Tributos_COFINS) as Itens6", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(IF(D006_Flag_Entrada_Saida='E', R001_Entrada_Valor_Total, R001_Saida_Valor_Total)) as Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			LEFT JOIN D006 ON D006_Id = R001_D006_Id
			WHERE
				R001_D024_Id = 0
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id = 0 
				AND R001_D006_Id != 0
			group by D006_Codigo_CFOP	
			order by D006_Codigo_CFOP
EOT;
		/**
		* Filtro
		*/
		$Todos = array('title' => 'Todos', 'value' => '');
		$D006_Tipo_CFOP = gGeraSelect('D006', 'D006_Tipo_CFOP', 'D006_Tipo_CFOP', "WHERE D006_Tipo_CFOP != '' AND D006_Tipo_CFOP IS NOT NULL GROUP BY D006_Tipo_CFOP");
		array_unshift($D006_Tipo_CFOP, $Todos);
		$gridFiltro = array(
			//'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			//'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'D006_Tipo_CFOP' => array('titulo' => 'Tipo CFOP', 'tipo' => 'select',
				'select' => $D006_Tipo_CFOP,
				'naoGerarExtra' => false
			),
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				array('title' => 'Tipo CFOP', 'campoAgrupar' => 'D006_Tipo_CFOP', 'campoMostrar' => 'D006_Tipo_CFOP', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
			)),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$vazio = "";
		$extraABC = $g['ui']->gridFiltroExtra(false, $gridFiltro, $vazio);		
		$extraABC = explode('ORDER',$extraABC);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		
		/**
		* Botões (Versão Acima do GRID)
		*/
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
		//print_r($gridTabela);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'crm001GridResumoFinanceiro':
		echo <<<EOT
			<div class="radioFinanceiroUI" style="padding:5px;">
				<input type="radio" id="radioFinanceiro1" class="noStyle" checked="checked" name="radio" /><label for="radioFinanceiro1">Diario</label>
				<input type="radio" id="radioFinanceiro2" class="noStyle" name="radio" /><label for="radioFinanceiro2">Mensal</label>
				<input type="radio" id="radioFinanceiro3" class="noStyle" name="radio" /><label for="radioFinanceiro3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioFinanceiroUI" ).buttonset();
				});
				$('#radioFinanceiro2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoFinanceiroMensal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFinanceiro3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoFinanceiroAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		global $Saldo_Projetado;
		global $Saldo_Realizado;
		global $Saldo_Real;
		
		$grid = array(
			array('colunaValor' => "R002_Id", 'colunaTitulo' => 'R002_Id', 'colunaExibir' => false),
			array('colunaValor' => "R002_C004_Id", 'colunaTitulo' => 'R002_C004_Id', 'colunaExibir' => false),	
			array('colunaValor' => "R002_Data", 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width:1%'),
			array('colunaValor' => "Dia", 'colunaTitulo' => 'Dia', 'callback' => "retornaDia", 'style' => 'width:1%', 'colunaTipo' => 'livre'),
			array('colunaValor' => "R002_Valor_Receber", 'colunaTitulo' => 'Receber', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Receber_Projecao", 'colunaTitulo' => 'Projeção (+)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pagar", 'colunaTitulo' => 'Pagar', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pagar_Projecao", 'colunaTitulo' => 'Projeção (-)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "Saldo_Projetado", 'colunaTitulo' => 'Saldo Projetado', 'callback' => 'saldoProjetado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "Saldo_Real", 'colunaTitulo' => 'Saldo Real', 'colunaCasasDecimais' => '2', 'callback' => 'saldoReal', 'colunaTipo'=>'livre', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Recebido", 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pago", 'colunaTitulo' => 'Pago', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "{$Saldo_Realizado}", 'colunaTitulo' => 'Saldo', 'callback' => 'saldoRealizado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R002';

		$extra = <<<EOT
			LEFT JOIN C004 ON C004_Id = R002_C004_Id
			WHERE R002_C004_Id = 0
			ORDER BY R002_Data ASC
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'R002_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
				
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);
	
		/**
		* Botões (Versão Acima do GRID)
		*/
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
	
	case 'crm001GridResumoFinanceiroMensal':
		echo <<<EOT
			<div class="radioFinanceiroUI" style="padding:5px;">
				<input type="radio" id="radioFinanceiro1" class="noStyle" name="radio" /><label for="radioFinanceiro1">Diario</label>
				<input type="radio" id="radioFinanceiro2" class="noStyle" checked="checked" name="radio" /><label for="radioFinanceiro2">Mensal</label>
				<input type="radio" id="radioFinanceiro3" class="noStyle" name="radio" /><label for="radioFinanceiro3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioFinanceiroUI" ).buttonset();
				});
				$('#radioFinanceiro1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoFinanceiro/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFinanceiro3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoFinanceiroAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		global $Saldo_Projetado;
		global $Saldo_Realizado;
		global $Saldo_Real;
		
		$grid = array(
			array('colunaValor' => "R002_Id", 'colunaTitulo' => 'R002_Id', 'colunaExibir' => false),
			array('colunaValor' => "R002_C004_Id", 'colunaTitulo' => 'R002_C004_Id', 'colunaExibir' => false),	
			array('colunaValor' => "R002_Data", 'colunaTitulo' => 'Mês', 'callback' => "retornaMes", 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R002_Valor_Receber) as R002_Valor_Receber", 'colunaTitulo' => 'Receber', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Receber_Projecao) as R002_Valor_Receber_Projecao", 'colunaTitulo' => 'Projeção (+)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Pagar) as R002_Valor_Pagar", 'colunaTitulo' => 'Pagar', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Pagar_Projecao) as R002_Valor_Pagar_Projecao", 'colunaTitulo' => 'Projeção (-)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SaldoProjetado", 'colunaTitulo' => 'Saldo Projetado', 'callback' => 'saldoProjetado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SaldoReal", 'colunaTitulo' => 'Saldo Real', 'colunaCasasDecimais' => '2', 'callback' => 'saldoReal', 'colunaTipo'=>'livre', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Recebido) as R002_Valor_Recebido", 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Pago) as R002_Valor_Pago", 'colunaTitulo' => 'Pago', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SaldoRealizado", 'colunaTitulo' => 'Saldo', 'callback' => 'saldoRealizado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R002';

		$extra = <<<EOT
			LEFT JOIN C004 ON C004_Id = R002_C004_Id
			WHERE R002_C004_Id = 0
			GROUP BY EXTRACT(YEAR_MONTH FROM R002_Data)
			ORDER BY EXTRACT(YEAR_MONTH FROM R002_Data) ASC
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
					'R002_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);
	
		/**
		* Botões (Versão Acima do GRID)
		*/
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
	
	case 'crm001GridResumoFinanceiroAnual':
		echo <<<EOT
			<div class="radioFinanceiroUI" style="padding:5px;">
				<input type="radio" id="radioFinanceiro1" class="noStyle" name="radio" /><label for="radioFinanceiro1">Diario</label>
				<input type="radio" id="radioFinanceiro2" class="noStyle" name="radio" /><label for="radioFinanceiro2">Mensal</label>
				<input type="radio" id="radioFinanceiro3" class="noStyle" checked="checked" name="radio" /><label for="radioFinanceiro3">Anual</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioFinanceiroUI" ).buttonset();
				});
				$('#radioFinanceiro1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoFinanceiro/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFinanceiro2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/crm/crm001/grid/crm001GridResumoFinanceiroMensal/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		global $Saldo_Projetado;
		global $Saldo_Realizado;
		global $Saldo_Real;
		
		$grid = array(
			array('colunaValor' => "R002_Id", 'colunaTitulo' => 'R002_Id', 'colunaExibir' => false),
			array('colunaValor' => "R002_C004_Id", 'colunaTitulo' => 'R002_C004_Id', 'colunaExibir' => false),	
			array('colunaValor' => "R002_Data", 'colunaTitulo' => 'Mês', 'callback' => "retornaMes", 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R002_Valor_Receber) as R002_Valor_Receber", 'colunaTitulo' => 'Receber', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Receber_Projecao) as R002_Valor_Receber_Projecao", 'colunaTitulo' => 'Projeção (+)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Pagar) as R002_Valor_Pagar", 'colunaTitulo' => 'Pagar', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Pagar_Projecao) as R002_Valor_Pagar_Projecao", 'colunaTitulo' => 'Projeção (-)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SaldoProjetado", 'colunaTitulo' => 'Saldo Projetado', 'callback' => 'saldoProjetado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SaldoReal", 'colunaTitulo' => 'Saldo Real', 'colunaCasasDecimais' => '2', 'callback' => 'saldoReal', 'colunaTipo'=>'livre', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Recebido) as R002_Valor_Recebido", 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R002_Valor_Pago) as R002_Valor_Pago", 'colunaTitulo' => 'Pago', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SaldoRealizado", 'colunaTitulo' => 'Saldo', 'callback' => 'saldoRealizado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R002';

		$extra = <<<EOT
			LEFT JOIN C004 ON C004_Id = R002_C004_Id
			WHERE R002_C004_Id = 0
			GROUP BY YEAR(R002_Data)
			ORDER BY YEAR(R002_Data) ASC
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
					'R002_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);
	
		/**
		* Botões (Versão Acima do GRID)
		*/
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
	
	case 'crm001GridCatalogo':
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		//mysql_query("DELETE FROM T198");
		$C008_Id = $r_acaoId;
	
/*          array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'IDD009', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T198_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'D001_D003_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Id', 'colunaTitulo' => 'C004_Id', 'chavePrimaria' => '', 'colunaExibir' => false),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'style' => 'width:1%'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'callback' => 'verPedidosDoProduto', 'style' => 'width:1%;'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'PE', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%', 'callback' => 'estoqueMP', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueLongoPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'LP', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueLP', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Estoque_Defeito', 'colunaTitulo' => 'DF', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Estoque_Mostruario', 'colunaTitulo' => 'DM', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preço 1', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%;font-weight:bold'),
            array('colunaValor' => 'D009_Preco_2', 'colunaTitulo' => 'Preço 2', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Preco_3', 'colunaTitulo' => 'Preço 3', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'concat(C004_Id,\'-\',C004_Nome_Abreviado) as C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Observacao_Venda', 'colunaTitulo' => 'Observação', 'style' => 'width:25%;color: var(--BGVermelho);font-weight:bold;', 'colunaEditar' => true),
            array('colunaValor' => 'concat(D082_Marca, \' - \', D003_Nome_Linha) as TituloMarcaLinha', 'colunaTitulo' => 'TituloMarcaLinha', 'style' => 'width:5%;', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D049_D082_Id, D001_D003_Id) as MarcaLinha', 'colunaTitulo' => 'MarcaLinha', 'style' => 'width:5%;', 'colunaExibir' => false),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'D003_Nome_Linha', 'colunaExibir' => false),
*/

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'Foto', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Imagem', 'colunaAgrupar' => '1', 'style' => 'width: 100px;text-align: center;background-color: white;', 'callback' => 'mostrarImagemD003', 'colunaExibir' => false),
            array('colunaValor' => 'CampoBx', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Orç', 'callback' => 'checkboxGerarOrcamentoT198', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'D003_Imagem', 'colunaTitulo' => 'D003_Imagem', 'colunaExibir' => false),
		    array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;'),
		    array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049 Id', 'chavePrimaria' => false, 'colunaExibir' => false, 'chavePrimaria' => true),
		    array('colunaValor' => 'T198_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
		    array('colunaValor' => 'D001_C008_Id', 'colunaTitulo' => 'Depto', 'style' => 'font-weight:normal','colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('C008', 'C008_Id', 'C008_Id, C008_Tipo', "ORDER BY C008_Id", true), 'colunaExibir' => false),
		    array('colunaValor' => 'D001_D060_Id', 'colunaTitulo' => 'Setor', 'style' => 'font-weight:normal','colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('D060', 'D060_Id', 'D060_Id, D060_Descricao_Grupo', "ORDER BY D060_Id", true), 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:bold'),
		    array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'style' => 'font-weight:normal', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2),
		    array('colunaValor' => 'D001_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
		    array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
		    array('colunaValor' => 'D049_Flag_Curva_ABC', 'colunaTitulo' => 'ABC', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
		    array('colunaValor' => 'D001_Codigo_Barra1', 'colunaTitulo' => 'EAN Fab', 'style' => 'font-weight:normal;width:1%', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2, 'colunaExibir' => false),
		    array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Modelo', 'style' => 'font-weight:normal', 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referência', 'style' => 'font-weight:normal', 'colunaEditar' => true, 'colunaEditarNivel' => 2, 'colunaExibir' => false),
		    array('colunaValor' => 'ifnull(D069_D070_Id,"_")', 'colunaTitulo' => 'Tab', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2, 'callback' => 'abrirJanela', 'callbackParameter' => array("Cadastro Produto", "/cad/cad002/content/form2/", "D001_Id", "1000,600", false, false, "&abrirTab=tabela", false)),
		    array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:normal', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2),
		    array('colunaValor' => "if(D009_Flag_Promocao='S','<font style=\"background-color: var(--celula-verde);color:darkgreen;padding: 1px 3px 1px 3px\">P</font>','')", 'colunaTitulo' => 'P', 'style' => 'font-weight:bold', 'colunaAlinhar' => 'center' ),
		    array('colunaValor' => 'D049_Codigo_Produto', 'colunaTitulo' => 'Descrição inglês', 'colunaExibir' => false),
		    array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Depto', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
		    array('colunaValor' => 'D060_Descricao_Grupo', 'colunaTitulo' => 'Setor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
		    array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
		    array('colunaValor' => 'D002_Descricao_Produto', 'colunaTitulo' => 'Sub-Grupo', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
			array('colunaValor' => 'D049_D082_Id', 'colunaTitulo' => 'Marca', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D082_Marca', 'colunaEditarNivel' => 2,
				'colunaEditarGeraSelect' => array('D082', 'D082_Id', 'D082_Marca', "ORDER BY D082_Marca", true)
			),
			array('colunaValor' => 'substr(D082_Marca,1,15) as D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
		    array('colunaValor' => 'D049_Origem_Mercadoria', 'colunaTitulo' => 'Origem', 'colunaEditarTodos' => true, 'colunaEditarOculto' => true, 'colunaAlinhar' => 'center', 'colunaEditarNivel' => 2, 'style' => 'width:1%'),
		    array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as NCM', 'colunaTitulo' => 'NCM', 'colunaExibir' => false),
			array('colunaValor' => 'D001_D005_Id', 'colunaTitulo' => 'NCM', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'NCM', 'colunaEditarNivel' => 2,
				'colunaEditarGeraSelect' => array('D005', 'D005_Id', "D005_Classificacao_Fiscal,D005_Flag_Aplicacao", "ORDER BY D005_Classificacao_Fiscal", true)
			),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaExibir' => false),
			array('colunaValor' => 'D001_D037_Id', 'colunaTitulo' => 'Un', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D037_Unidade', 'colunaEditarNivel' => 2,
				'colunaEditarGeraSelect' => array('D037', 'D037_Id', "D037_Unidade", "ORDER BY D037_Unidade", true)
			),
		    array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 0)),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Loja', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
		    array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueFora'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'O.C.', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_DF', 'colunaTitulo'=>'DF', 'callback' => 'estoqueDF', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_DM', 'colunaTitulo'=>'DM', 'callback' => 'estoqueDM', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
		    array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'colunaCasasDecimais' => 2, 'colunaExibirNivel' => 4, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Ultima_Entrada', 'colunaTitulo' => 'Ult.Entrada', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'gCorrigeData'),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'Ult.Saída', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'gCorrigeData'),
		    array('colunaValor' => "if(D149_Flag_ST='S','ST','') as ST", 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => "(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo' => 'Local', 'colunaExibir' => false),
		    array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Último Fornecedor', 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
		    array('colunaValor' => 'D009_IPV_1', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaEditarTodos' => true, 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'colunaEditarNivel' => 4, 'colunaExibirNivel' => 2, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
		    array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'colunaExibir' => false),
			array('colunaValor' => 'D009_C007_Id_Comprador', 'colunaTitulo' => 'Comprador', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome', 'colunaEditarNivel' => 2,
				'colunaEditarGeraSelect' => array('C007', 'C007_Id', "C007_Nome", "ORDER BY C007_Nome", true)
			),
		    
		    array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'concat(D082_Marca, \' - \', D003_Nome_Linha) as TituloMarcaLinha', 'colunaTitulo' => 'TituloMarcaLinha', 'style' => 'width:5%;', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D049_D082_Id, D001_D003_Id) as MarcaLinha', 'colunaTitulo' => 'MarcaLinha', 'style' => 'width:5%;', 'colunaExibir' => false),
        );
		$from = 'D001';
		$extra = <<<EOT
			left join D083 on D083_D001_Id=D001_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D024 on D024_Id=D049_D024_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join D037 on D001_D037_Id=D037_Id
			left join D005 on D001_D005_Id=D005_Id
			left join D072 on D072_D001_Id=D001_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			left join C007 on C007_Id=D009_C007_Id_Comprador
			left join D069 on D069_D009_Id=D009_Id and D069_Flag_Tabela_Principal='S'
			left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
			left join T198 on T198_D009_Id=D009_Id
			where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			Group by D009_Id
EOT;
		
		/**
	    * Filtro
	    */
	    //Sempre que mudar esta variável, caso o grid/filtro esteja customizado, deve-se também mudar o valor no grid/filtro
	    $diasMP = $g['C031']['diasMedioPrazo'];
		$data = strtotime("+$diasMP day", strtotime("now"));
		$dataMP = date("Y-m-d", $data);	
	    
	    $gridFiltro = array(
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                array('title' => 'Modelo', 'campoAgrupar' => 'MarcaLinha', 'campoMostrar' => 'TituloMarcaLinha', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
              ),
            ),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D083_Codigo_Produto_Fornecedor' => array('titulo' => 'Referência', 'tipo' => 'texto'),
            'D001_Codigo_Barras,D001_Codigo_Barra1,D083_Codigo_Barras' => array('titulo' => 'Cód. Barras', 'tipo' => 'texto'),
            "ifnull(D082_Marca[[VIRGULA]]'')" => array('titulo' => 'Marca', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad025/grid/cad025GridMarcas/', 'campo' => 'D082_Marca', 'tamanho' => 20)),
            'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad023/grid/cad023GridTipos/', 'campo' => 'C008_Tipo', 'tamanho' => 20)),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad027/grid/cad027GridGrupos/', 'campo' => 'D060_Descricao_Grupo', 'tamanho' => 20)),
            'D003_Id,D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad026/grid/cad026GridLinhas/', 'campo' => 'D003_Nome_Linha', 'tamanho' => 20)),
            'D002_Id,D002_Descricao_Produto' => array('titulo' => 'Sub-grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad028/grid/cad028GridSubGrupos/', 'campo' => 'D002_Descricao_Produto', 'tamanho' => 20)),
            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.loja', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde.OC', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'D009_Data_Ultima_Entrada' => array('titulo' => 'Ult.Compra', 'tipo' => 'data'),
            'D009_Data_Alteracao_Custo' => array('titulo' => 'Alt.Custo', 'tipo' => 'data'),
            'D009_Data_Ultima_Venda' => array('titulo' => 'Ult.Venda', 'tipo' => 'data'),
            'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
            'D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D069_D070_Id' => array('titulo' => 'Tabela Fornecedor', 'tipo' => 'texto', 'exibir' => false),
            'D049_Modelo' => array('titulo' => 'Modelo', 'tipo' => 'texto'),
            'D049_Origem_Mercadoria' => array('titulo' => 'Origem', 'tipo' => 'texto'),
            'D049_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
            'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'naoGerarExtra' => ''),
            'D009_Valor_Custo_Unitario' => array('titulo' => 'Custo R$', 'tipo' => 'numero'),
            'D049_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
        );

	    $g['ui']->gridFiltroExtra(false, $gridFiltro, false); // somente para ter acesso a função getGridFiltroCampoBusca(): carregar o que foi filtrado
		$ItensSelecionados = $g['ui']->getGridFiltroCampoBusca('ItensSelecionados');

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="crm001GerarOrcamentoTabCatalogo">Gerar Orçamento</button>
				<button type="button" id="crm001MarcarTodos">Marcar Todos</button>
				<button type="button" id="crm001DesmarcarTodosCheck">Desmarcar todos</button> 
			<!--<button type="button" id="cad002JuntarProdutos">Juntar Produtos</button>-->
			<!--<button type="button" id="cad002TrocarCodigos">Trocar Códigos</button>-->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#crm001DesmarcarTodosCheck').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$.getJSON('/crm/crm001/grid_func-ajax/crm001checkDesmarcarTodos/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    divRefresh('{$g['divId']}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    dialogConfirm('Erro', request.data, \$buttons);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Você tem certeza?', 'Você tem certeza que deseja desmacar todos os produtos selecionados?', \$buttons);
				});
				$('#crm001GerarOrcamentoTabCatalogo').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_CRM=' + encodeURIComponent('{$D024_Id}') + '&tabCatalogoCRM=true', [650,480]);
				});
	            
			} </script></div>
EOT;
	
		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
		        return 'linhaCinza';
		    } else if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
		        return 'linhaVermelho';
		    } else if ($array['todosCampos']['C004_Id']=='2') {
		        return 'linhaAzul';
		    }
		}

		/**
	    * Filtro
	    */
		// Gera o filtro e acrescenta ao $extra
		$filtroObrigatorio = true; // torna o filtro obrigatório
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	    
		// Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		if (!personalizacaoTotais()) {
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		/*
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
	    $valorTotalPE = abs($g['sqlAuto']->pegarSqlCampo("D009_Quantidade_Estoque_Liquido", $from, $extra, false, true, true));
	    $valorTotalDF = abs($g['sqlAuto']->pegarSqlCampo("D009_Saldo_DF(D009_Id)", $from, str_replace('GROUP BY D009_Id','',$extra)));
	    $valorTotalMP = abs($g['sqlAuto']->pegarSqlCampo("(SELECT 
            IFNULL((
                SELECT 
                    SUM(T055_Quantidade) 
                FROM 
                    T055 
                LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                WHERE 
                    T055_D009_Id=D009_Id 
                    AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$diasMP} DAY))
                    AND T075_Data_Previsao>0
                    AND IFNULL(T075_Data_Estoque,'0000-00-00') = '0000-00-00'
            ),0)-
            IFNULL((
                SELECT 
                    SUM(T006_Quantidade) 
                FROM 
                    T006 
                LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                WHERE T006_D009_Id=D009_Id 
                    AND T005_Flag_Status!='5'
                    AND T005_Flag_Status!='7' 
                    AND T005_Flag_Status!='8' 
                    AND T005_Flag_Status!='11' 
                    AND T006_T075_Id IN 
                (
                    SELECT 
                        T075_Id 
                    FROM 
                        T075 
                    LEFT JOIN T055 ON T075_Id=T055_T075_Id 
                    LEFT JOIN D009 ON T055_D009_Id=D009_Id
                    WHERE 
                        T055_D009_Id=D009_Id 
                        AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$diasMP} DAY))
                        AND T075_Data_Previsao > 0
                        AND IFNULL(T075_Data_Estoque,'0000-00-00') = '0000-00-00'
                ) 
            ),0))", $from, $extra, false, true, true));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
	            <span class="item" style="text-align:right">Estoque PE<br><input type="text" value="{$valorTotalPE}" class="whiteBg" id="" size="11" readonly></span>
	            <span class="item" style="text-align:right">Estoque MP<br><input type="text" value="{$valorTotalMP}" class="whiteBg" id="" size="11" readonly></span>
	            <span class="item" style="text-align:right">Estoque DF<br><input type="text" value="{$valorTotalDF}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
		}
	
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
		$sqlGridFiltro = base64_encode($geradoSql);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
	    echo <<<EOT
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	            $('#crm001MarcarTodos').unbind('click').bind('click', function(data) {
	                var \$buttons = {
	                    "Sim": function() {
	                        $.getJSON('/crm/crm001/grid_func-ajax/crm001checkMarcarTodos/?ajax=true&sqlFiltro='+encodeURIComponent('{$sqlGridFiltro}')+'&callback=?', function(request) {
	                            if (request.code) {
	                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	                                divRefresh('{$g['divId']}', true);
	                            } else {
	                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	                                dialogConfirm('Erro', request.data, \$buttons);
	                            }
	                            $('#' + '{$g['divId']}').hideLoading();
	                        });
							$(this).dialog("destroy").remove();
	                    },
	                    "Não": function() { $(this).dialog("destroy").remove(); } 
	                };
	                dialogConfirm('Você tem certeza?', 'Você tem certeza que deseja marcar todos os produtos selecionados?', \$buttons);
	            });    
	        } </script></div>
EOT;
    
	    if($g['c007']['C007_Flag_Vendedor'] == 'D'){
		       $novoIdTelaForm = uniqid();
		       echo  <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			           var dbclickPrevent = false;
			        $('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
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
							var D024_Id = id[1].replace(/D024_Id-/, '');
			               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [1000,600]);
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
	
	case 'crm001GridCatalogoCliente':	
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		/**
		* Filtro
		*/
	
		//Sempre que mudar esta variável, caso o grid/filtro esteja customizado, deve-se também mudar o valor no grid/filtro
		$diasMP = $g['C031']['diasMedioPrazo'];
		//$data = strtotime("+$diasMP day", strtotime(now));
		//$dataMP = date("Y-m-d", $data);

		$gridFiltro = array(
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
                array('title' => 'Modelo', 'campoAgrupar' => 'MarcaLinha', 'campoMostrar' => 'TituloMarcaLinha', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
              ),
            ),
            '(SELECT 
                      IFNULL((
                          SELECT 
                              SUM(T055_Quantidade) 
                          FROM 
                              T055 
                          LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                          WHERE 
                              T055_D009_Id=D009_Id 
                              AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL 30 DAY))
                              AND T075_Data_Previsao>0
                      ),0)-
                      IFNULL((
                          SELECT 
                              SUM(T006_Quantidade) 
                          FROM 
                              T006 
                          LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                          WHERE 
                              T005_Flag_Status!=\'5\'
                              AND T006_Flag_Status!=\'3\' 
                              AND T005_Flag_Status!=\'7\' 
                              AND T005_Flag_Status!=\'8\' 
                              AND T005_Flag_Status!=\'11\' 
                              AND T006_T075_Id IN 
                          (
                              SELECT 
                                  T075_Id 
                              FROM 
                                  T075 
                              LEFT JOIN T055 ON T075_Id=T055_T075_Id 
                              LEFT JOIN D009 ON T055_D009_Id=D009_Id
                              WHERE 
                                  T055_D009_Id=D009_Id 
                                  AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL 30 DAY))
                                  AND T075_Data_Previsao > 0
                          ) 
                      ),0))' => array('titulo' => 'Estoque MP', 'tipo' => 'numero', 'exibir' => false),
            'ItensSelecionados' => array('titulo' => 'Marcados', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => ' AND T198_D009_Id IS NOT NULL AND T198_C007_Id=\'63\' '),
                array('title' => 'Não', 'value' => ' AND T198_D009_Id IS NULL OR T198_C007_Id!=\'63\' '),
              ),
              'naoGerarExtra' => true, 'exibir' => false),
            'D049_Flag_Promocao' => array('titulo' => 'Promoção', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ),
              'exibir' => false),
        );

		$g['ui']->gridFiltroExtra(false, $gridFiltro, '');
		
		$C008_Id = $r_acaoId;
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Modelo', 'colunaAgrupar' => '1', 'style' => 'width: 100px;text-align: center;background-color: white;', 'callback' => 'mostrarImagemD003', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T198_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'D001_D003_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),
            array('colunaValor' => 'CampoBx', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Orçamento', 'callback' => 'checkboxGerarOrcamentoT198', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'IF(D049_Flag_Promocao="S", concat(D001_Descricao_Produto, " <font color=\'darkgreen\' style=\'bgcolor:var(--celula-amarelo-claro);border-radius:3px\'><i>Promoção!</i></font>"), D001_Descricao_Produto) as descricao', 'colunaTitulo' => 'Descrição português', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição português', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
            array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'callback' => 'verPedidosDoProduto'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Líquido', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preço 1', 'colunaAlinhar' => 'right', 'callback' => 'validaCampoPreco1'),
            array('colunaValor' => 'D009_Preco_2', 'colunaTitulo' => 'Preço 2', 'colunaAlinhar' => 'right', 'callback' => 'validaCampoPreco2', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Preco_3', 'colunaTitulo' => 'Preço 3', 'colunaAlinhar' => 'right', 'callback' => 'validaCampoPreco3', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D049_D082_Id, D001_D003_Id) as MarcaLinha', 'colunaTitulo' => 'Obs', 'style' => 'width:5%;', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D082_Marca, \' - \', D003_Nome_Linha) as TituloMarcaLinha', 'colunaTitulo' => 'Obs', 'style' => 'width:5%;', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Flag_Promocao', 'colunaTitulo' => 'Preço 1', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
        );
		
		//left join D083 on D083_D024_Id=D024_Id and D083_D001_Id=D001_Id and D083_D049_Id=D049_Id
		$from = 'D001';
		$extra = <<<EOT
					left join D003 on D003_Id=D001_D003_Id
					left join D049 on D049_D001_Id=D001_Id
					left join D009 on D009_D049_Id=D049_Id
					left join C008 on D001_C008_Id=C008_Id
					left join D082 on D049_D082_Id=D082_Id
					left join D024 on D024_Id=D049_D024_Id
					left join D060 on D001_D060_Id=D060_Id
					left join D002 on D001_D002_Id=D002_Id
					left join D015 on D001_D015_Id=D015_Id
					left join D037 on D001_D037_Id=D037_Id
					left join D005 on D001_D005_Id=D005_Id
					left join D072 on D072_D001_Id=D001_Id
					left join T066 on T066_D009_Id=D009_Id
					left join D004 on D004_Id=T066_D004_Id
					left join D041 on D041_D049_Id=D049_Id
					left join T198 on T198_D009_Id=D009_Id
					where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
					AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
		            {$g['ui']->getGridFiltroCampoBusca('ItensSelecionados')}
					GROUP BY D009_Id ORDER BY D001_Descricao_Produto ASC
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="crm001GerarOrcamentoTabCatalogoCliente">Gerar Orçamento</button>
				<button type="button" id="crm001DesmarcarTodosCheckCliente">Desmarcar todos</button>
			<!--<button type="button" id="cad002JuntarProdutos">Juntar Produtos</button>-->
			<!--<button type="button" id="cad002TrocarCodigos">Trocar Códigos</button>-->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#crm001DesmarcarTodosCheckCliente').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/crm/crm001/grid_func-ajax/crm001checkDesmarcarTodos/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    divRefresh('{$g['divId']}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#' + '{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Você tem certeza?', 'Você tem certeza que deseja desmacar todos os produtos selecionados?', \$buttons);
				});
				
				$('#crm001GerarOrcamentoTabCatalogoCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_CRM=' + encodeURIComponent('{$D024_Id}') + '&tabCatalogoCRM=true', [650,480]);
				});
			} </script></div>
EOT;
	
		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
		    {
		       return 'linhaCinza';
		    }
		    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
		    {
		       return 'linhaVermelho';
		    }
		}	

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		// echo $geradoSql;
		// die();
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotalPE = abs($g['sqlAuto']->pegarSqlCampo("sum(D009_Quantidade_Estoque_Liquido)", $from, str_replace('GROUP BY D009_Id','',$extra)));
		$valorTotalMP = abs($g['sqlAuto']->pegarSqlCampo("sum((SELECT 
		        IFNULL((
		            SELECT 
		                SUM(T055_Quantidade) 
		            FROM 
		                T055 
		            LEFT JOIN T075 ON T075_Id=T055_T075_Id 
		            WHERE 
		                T055_D009_Id=D009_Id 
		                AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$diasMP} DAY))
		                AND T075_Data_Previsao>0
		                AND IFNULL(T075_Data_Estoque,'0000-00-00') = '0000-00-00'
		        ),0)-
		        IFNULL((
		            SELECT 
		                SUM(T006_Quantidade) 
		            FROM 
		                T006 
		            LEFT JOIN T005 ON T005_Id=T006_T005_Id 
		            WHERE T006_D009_Id=D009_Id 
		                AND T005_Flag_Status!='5'
		                AND T005_Flag_Status!='7' 
		                AND T005_Flag_Status!='8' 
		                AND T005_Flag_Status!='11' 
		                AND T006_T075_Id IN 
		            (
		                SELECT 
		                    T075_Id 
		                FROM 
		                    T075 
		                LEFT JOIN T055 ON T075_Id=T055_T075_Id 
		                LEFT JOIN D009 ON T055_D009_Id=D009_Id
		                WHERE 
		                    T055_D009_Id=D009_Id 
		                    AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$diasMP} DAY))
		                    AND T075_Data_Previsao > 0
		                    AND IFNULL(T075_Data_Estoque,'0000-00-00') = '0000-00-00'
		            ) 
		        ),0)))", $from, str_replace('GROUP BY D009_Id','',$extra)));
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
		        <span class="item" style="text-align:right">Estoque PE<br><input type="text" value="{$valorTotalPE}" class="whiteBg" id="" size="11" readonly></span>
		        <span class="item" style="text-align:right">Estoque MP<br><input type="text" value="{$valorTotalMP}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		if($g['c007']['C007_Flag_Vendedor'] == 'D'){
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
							var D024_Id = id[1].replace(/D024_Id-/, '');
			               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
			           });
			           $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
				} </script></div>
EOT;
    	}

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
	
	case 'crm001GridAgendaGeral':
		$gridModelos = array(
    		'RETORNO' => 'Retornos Pendentes'
    	);

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D085_Id', $g['divId']), 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
            array('colunaValor' => 'D085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,30)', 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => 'concat(substr(D013_Nome_Contato,1,10),\' \',ifnull(D013_DDD_Telefone_1,\'\'),\'-\',ifnull(D013_Telefone_1,\'\'),\' \',ifnull(D013_DDD_Celular,\'\'),\'-\',ifnull(D013_Celular,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:180px;white-space:normal;min-width:180px;width:180px'),
            array('colunaValor' => 'CONCAT(D085_Data, " ", D085_Hora)', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
        	array('colunaValor' => 'IF(D085_Data_Previsao_Retorno = "0000-00-00", "", CONCAT(D085_Data_Previsao_Retorno, " ", D085_Hora_Previsao_Retorno))', 'colunaTitulo' => 'Retornar', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D085_Data_Retorno', 'colunaTitulo' => 'Finalizado', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => 'Mensagem', 'style' => 'max-width:250px;white-space:normal;'),
            array('colunaValor' => 'D085_Retorno', 'colunaTitulo' => 'Retorno', 'style' => 'max-width:250px;white-space:normal;', 'colunaExibir' => false),
            array('colunaValor' => 'D085_C007_Id', 'colunaTitulo' => 'IdUsuarioAtual', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Entidade', 'colunaTitulo' => 'Entidade', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Id_Entidade', 'colunaTitulo' => 'ID Entidade', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Flag_Manual', 'colunaTitulo' => 'Manual', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Data_Previsao_Retorno', 'colunaTitulo' => 'D085_Data_Previsao_Retorno', 'colunaExibir' => false),
            array('colunaValor' => 'D085_T003_Id', 'colunaTitulo' => 'Orç.', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'D085_T003_Id', '1000,615')),
            array('colunaValor' => 'D085_T005_Id', 'colunaTitulo' => 'Ped.', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'D085_T005_Id', '1000,615')),
            array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Nota', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', 'D085_T007_Id', '1000,615')),
            array('colunaValor' => 'D085_T139_Id', 'colunaTitulo' => 'Email', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Email', '/etc/etc006/content/etc006ContentEmail/', 'D085_T139_Id', '1000,615')),
            array('colunaValor' => 'D085_T002_Id', 'colunaTitulo' => 'Orç', 'colunaExibir' => false),
            array('colunaValor' => 'D085_T007_Id', 'colunaTitulo' => 'Nota', 'colunaExibir' => false),
            array('colunaValor' => 'T002_Numero_Duplicata', 'colunaTitulo' => 'Duplicata', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Título a Receber', '/fin/fin001/content/fin001content02/', 'D085_T002_Id', '1050,583')),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D085';

		if($g['C031']['exibirRegistroAgendaManual'] != 'S'){
            $extraFlagManual = " AND D085_Flag_Manual = 'N' ";
        }

		$extra = <<<EOT
			LEFT JOIN T002 ON T002_Id=D085_T002_Id
			LEFT JOIN T007 ON T007_Id=D085_T007_Id
			left join C007 as vendedor on vendedor.C007_Id=D085_C007_Id
			left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
			LEFT JOIN D024 on D024_Id=D085_D024_Id
			LEFT JOIN D021 ON D021_Id=D085_D021_Id
			LEFT JOIN D013 ON D013_Id=D085_D013_Id
			where IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
			{$extraFlagManual}
			order by D085_Data desc,D085_Id desc
EOT;
			
		//Define ação do click de abertura (ao clicar em uma linha do grid)
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//$('.gridOptTop').remove();
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D085_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&acaoId=' + encodeURIComponent(id), [700,450]);
				});
			} </script></div>
EOT;
			
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="crm002IncluirAgenda">Incluir</button>	
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#crm002IncluirAgenda').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&tab=geral', [700,405]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D085_Id' => array('titulo' => 'ID Agenda', 'tipo' => 'texto', 'exibir' => false),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D085_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'D085_Data_Previsao_Retorno' => array('titulo' => 'Retornar', 'tipo' => 'data'),
			'D085_Data_Retorno' => array('titulo' => 'Data Retorno', 'tipo' => 'data', 'exibir' => false),
			'D085_Mensagem' => array('titulo' => 'Mensagem', 'tipo' => 'texto'),
			'D085_Retorno' => array('titulo' => 'Retorno', 'tipo' => 'texto', 'exibir' => false),
			'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto', 'exibir' => false),
		);
		
		 // Gera o filtro e acrescenta ao $extra
		 $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		 //$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
		 // Imprime o filtro do GRID (normalmente executado acima do GRID)
		 echo $g['ui']->gridFiltroPrint($gridFiltro);
		

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Botões (Versão Acima do GRID)
		*/
		if (!personalizacaoTotais()) {
			$total = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			$orcamentos = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T003_Id > 0"));
			$pedidos = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T005_Id > 0"));
			$notas = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T007_Id > 0"));
			$emails = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_T139_Id > 0"));
			$ligacoes = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gInsertExtraWhere($extra, "D085_Flag_Manual = 'S'"));

			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Registros<br><input type="text" value="{$total}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Orçamentos<br><input type="text" value="{$orcamentos}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Pedidos<br><input type="text" value="{$pedidos}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Notas Fiscais<br><input type="text" value="{$notas}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Emails<br><input type="text" value="{$emails}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Ligações<br><input type="text" value="{$ligacoes}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			$g['smarty']->assign('totais', $totais);
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

	case 'crm001GridAgendaPorOrcamento':
		/**
		* Definição do Grid (Tabela)
		*/
		//echo "<span style='margin-left:5px;color:#70A8D2;font-weight:bold;'>Agenda</span>";
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D085_Id', $g['divId'])),
            array('colunaValor' => 'D085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
            array('colunaValor' => 'CONCAT(D085_Data, " ", D085_Hora)', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'IF(D085_Data_Previsao_Retorno = "0000-00-00", "", CONCAT(D085_Data_Previsao_Retorno, " ", D085_Hora_Previsao_Retorno))', 'colunaTitulo' => 'Retornar', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D085_Data_Retorno', 'colunaTitulo' => 'Dt Retorno', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
            array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => 'Mensagem'),
            array('colunaValor' => 'D085_Retorno', 'colunaTitulo' => 'Retorno', 'colunaExibir' => false),
            array('colunaValor' => 'D085_C007_Id', 'colunaTitulo' => 'IdUsuarioAtual', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Entidade', 'colunaTitulo' => 'Entidade', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Id_Entidade', 'colunaTitulo' => 'ID Entidade', 'colunaExibir' => false),
            array('colunaValor' => 'D085_Flag_Manual', 'colunaTitulo' => 'Manual', 'colunaExibir' => false),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'D085';

		if($g['C031']['exibirRegistroAgendaManual'] != 'S'){
            $extraFlagManual = " AND D085_Flag_Manual = 'N' ";
        }

		$extra = <<<EOT
			left join C007 on C007_Id=D085_C007_Id
			LEFT JOIN D024 on D024_Id=D085_D024_Id
			LEFT JOIN D021 ON D021_Id=D085_D021_Id
			WHERE D085_T003_Id = '{$r_acaoId}'
			{$extraFlagManual}
			order by D085_Data desc,D085_Id desc
EOT;
		
		// Via Orçamento
		if (isset($r_acaoId)) {
			$sql = "SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$r_acaoId}'";
			$resT003 = mysql_query($sql);
			$rowT003 = mysql_fetch_assoc($resT003);
			$D024_Id = $r_D024_Id = $rowT003['T003_D024_Id'];
		}

		//Define ação do click de abertura (ao clicar em uma linha do grid)
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//$('.gridOptTop').remove();
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D085_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Agenda', '/crm/crm001/content/crm001contentEditarAgendaOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&T003_Id={$r_acaoId}&D024_Id={$D024_Id}', [700,450]);
				});
			} </script></div>
EOT;
			
			
		/**
		* Filtro
		*/
		$gridFiltro = array(
	        'D085_Id' => array('titulo' => 'ID Agenda', 'tipo' => 'texto', 'exibir' => false),
	        'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto', 'exibir' => false),
	        'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
	        'D085_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
	        'D085_Data_Previsao_Retorno' => array('titulo' => 'Retornar', 'tipo' => 'data'),
	        'D085_Data_Retorno' => array('titulo' => 'Data retorno', 'tipo' => 'data', 'exibir' => false),
	        'D085_Mensagem' => array('titulo' => 'Mensagem', 'tipo' => 'texto'),
	        'D085_Retorno' => array('titulo' => 'Retorno', 'tipo' => 'texto', 'exibir' => false),
	        'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto', 'exibir' => false),
	    );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="crm002IncluirAgendaPorOrcamento">Incluir</button>	
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#crm002IncluirAgendaPorOrcamento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/crm/crm001/content/crm001contentEditarAgendaOrcamento/', '&tab=geral&T003_Id={$r_acaoId}&D024_Id={$D024_Id}', [700,450]);
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
		
	case 'crm001GridAnaliseCompras':
		//Verifica restrição de linhas
		$g['cacheCount'] = false;
		$filtroObrigatorio = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			// Chave Primária
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
			array('colunaValor' => 'SUBSTRING(C007_Primeiro_Nome, 1, 12)', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'min-width: 250px; white-space:normal; word-wrap:break-word;'),
			//Compra
			array('colunaValor' => 'SUBSTRING(Fornecedor.D024_Nome_Empresa, 1, 20)', 'colunaTitulo' => 'Fornecedor', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T013_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T013_Data_Entrada', 'colunaTitulo' => 'Lançada', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight:bold; '),
			array('colunaValor' => 'T013_Data_Entrada_Estoque', 'colunaTitulo' => 'Estoque', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight:bold;'),
			array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center', 'callback' => 'abrirNovaJanela', 'callbackParameter' => array('DANFE', '/hardness3/hardness/nota_recebida/gerar_danfe.php?T013_Id=', 'T013_Id', '750|500'), 'style' => 'width: 1%; font-weight:bold;'),
			array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Qte OC', 'style' => 'background: var(--celula-amarelo);; width: 1%;', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'Qte NF', 'style' => 'background: var(--celula-amarelo);; width: 1%;', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
			//Venda
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte Ped', 'style' => 'background: var(--celula-verde);; width: 1%;', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Qte NF', 'style' => 'background: var(--celula-verde);; width: 1%;', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'right', 'style' => 'width: 1%; font-weight:bold;'),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center', 'callback' => 'abrirNovaJanela', 'callbackParameter' => array('DANFE', '/hardness3/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id=', 'T007_Id', '750|500'), 'style' => 'width: 1%; font-weight:bold;'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width: 1%; font-weight:bold;'),
			array('colunaValor' => 'SUBSTRING(Cliente.D024_Nome_Empresa, 1, 20)', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T006';
		
		$extra = <<<EOT
			LEFT JOIN T004 ON T006_T004_Id=T004_Id 
			LEFT JOIN T005 ON T006_T005_Id=T005_Id
			LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN T008 ON T008_T006_Id=T006_Id
			LEFT JOIN T007 ON T008_T007_Id=T007_Id
			LEFT JOIN D009 ON D009_Id=T006_D009_Id
			LEFT JOIN T219 FORCE INDEX (T219_Id) ON T006_T219_Id=T219_Id 
			LEFT JOIN T225 ON T225_T219_Id=T219_Id
			LEFT JOIN T014 FORCE INDEX (IDX_T014_T225_Id) ON T014_T225_Id=T225_Id 
			LEFT JOIN T224 ON T225_T224_Id=T224_Id
			LEFT JOIN T013 ON T014_T013_Id=T013_Id
			LEFT JOIN D024 AS Cliente ON Cliente.D024_Id = T005_D024_Id
			LEFT JOIN D024 AS Fornecedor ON Fornecedor.D024_Id = T013_D024_Id
			WHERE T014_Id IS NOT NULL
			AND T006_Id IS NOT NULL
			AND D009_C004_Id='{$g['empresaAtual']}'
			AND IFNULL(T005_T005_Id_Agrupado,0) <= 0
			GROUP BY T006_Id
			ORDER BY T013_Data_Entrada DESC
EOT;
		function gridLinha($array){
			if($array['todosCampos']['T013_Data_Entrada_Estoque']=='0000-00-00 00:00:00' or empty($array['todosCampos']['T013_Data_Entrada_Estoque'])){
				return "linhaAzul";
			} else if(empty($array['todosCampos']['T007_Numero_Nota_Fiscal'])){
				return "linhaVermelho";
			} else {
				// cor padrão
			}
		}

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$gridFiltro = array(
			'T008_Codigo_Produto,T008_Descricao_Produto' => array ('titulo' => 'Produto', 'tipo' => 'texto'),
			'T007_Data_Emissao' => array('titulo' => 'Data NF Venda', 'tipo' => 'data'),
			'T013_Data_Emissao' => array('titulo' => 'Data NF Compra', 'tipo' => 'data'),
			'C007_Primeiro_Nome' => array ('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'Cliente.D024_Nome_Empresa' => array ('titulo' => 'Cliente', 'tipo' => 'texto'),
			'Fornecedor.D024_Nome_Empresa' => array ('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF Venda', 'tipo' => 'texto'),
			'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF Compra', 'tipo' => 'texto'),
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'texto'),
			
		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
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

	case 'crm001GridOrcamentoRetornoPendente':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'colunaExibir' => false, 'style' => 'width:1%'),
            array('colunaValor' => 'campoCancelamentoOrcamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamentoOrcamento', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orc', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'T003_D024_Id', 'colunaTitulo' => 'Cod.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,15)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold;max-width:130px;min-width:130px;width:130px;white-space:normal;', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'concat(CONVERT(substr(orcamento.D013_Nome_Contato,1,10) USING utf8),\' \',ifnull(orcamento.D013_DDD_Telefone_1,\'\'),\'-\',ifnull(orcamento.D013_Telefone_1,\'\')) as Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left', 'style' => 'max-width:165px;white-space:normal;min-width:165px;width:165px'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T003_Valor_Total_Produtos_Sem_Desconto', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaGridOrcamento', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => '(T003_Valor_Desconto/T003_Valor_Total_Produtos_Sem_Desconto)*100', 'colunaTitulo' => 'Descto', 'callback' => 'gPorcentagem', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T003_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%', 'colunaMetrica' => 'S', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Valor_Total_IPI', 'colunaTitulo' => 'IPI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Valor_Total_ICMS', 'colunaTitulo' => 'ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Valor_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Valor_Frete', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;background-color: var(--celula-amarelo-claro);width:1%'),
            array('colunaValor' => 'T003_Valor_Pendente', 'colunaTitulo' => 'Val. Pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;width:1%;color: var(--BGVermelho)'),
//	            array('colunaValor' => 'sum(distinct if(T004_Valor_Total_Preco>ifnull(T006_Valor_Total_Preco,0),T004_Valor_Total_Preco-ifnull(T006_Valor_Total_Preco,0),0))', 'colunaTitulo' => 'Val.Pend.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);width:1%'),
            array('colunaValor' => 'T003_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Prazos(T003_Id)', 'colunaTitulo' => 'Prazo', 'style' => 'width:1%'),
            array('colunaValor' => 'if(T139_Id>0,\'Ok\',\'<font style="color: var(--BGVermelho)">--</font>\')', 'colunaTitulo' => 'Mail', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'if(D085_Id>0,\'Ok\',\'<font style="color: var(--BGVermelho)">--</font>\')', 'colunaTitulo' => 'Retorno', 'colunaExibir' => false, 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'IFNULL(IF(T003_Status_Retorno_Email=\'RETORNAR\',CONCAT(\'<font style="color: var(--BGVermelho)">\',T003_Status_Retorno_Email,\'</font>\'),T003_Status_Retorno_Email), \'<font style="color: var(--BGVermelho)">--</font>\')', 'colunaTitulo' => 'Retorno', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
            //array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - ",T005_Valor_Total) separator ", <br />") as Pedido', 'colunaTitulo' => 'Pedido', 'callback' => 'validaCampoPedido', 'style' => 'max-width:210px;white-space:normal;min-width:210px;width:210px'),
            //array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'font-weight:bold;width:1%', 'callback' => 'validaCampoNFOrcamentos'),
            //array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,12)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'colunaDimensao' => ''),
            //array('colunaValor' => 'globalStatusOrcamento as Status', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Status', 'callback' => 'campoStatus', 'style' => 'max-width:200px;white-space:normal;min-width:200px;width:200px'),
            //array('colunaValor' => 'T003_Status_Orcamento(T003_Flag_Status_Orcamento) as Status', 'colunaTitulo' => 'Status', 'colunaLargura' => 210),
            //array('colunaValor' => 'T003_Flag_Alcada', 'colunaTitulo' => 'Alcada', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'globalStatusOrcamento', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Status', 'callback' => 'campoStatus', 'colunaExibir' => false),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T003_Observacao_Orcamento_Perdido', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Flag_ACP', 'colunaTitulo' => 'T003_Flag_ACP', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaExibir' => false, 'colunaTitulo' => 'T003_Flag_Status_Orcamento'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
        						FROM T004 
        						LEFT JOIN D009 ON D009_Id = T004_D009_Id
        						LEFT JOIN D049 ON D049_Id = D009_D049_Id
        						LEFT JOIN D082 ON D082_Id = D049_D082_Id
        						WHERE T004_T003_Id=T003_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false),
            array('colunaValor' => 'T003_C007_Id_Vendedor_Interno', 'colunaTitulo' => 'T003_C007_Id_Vendedor_Interno', 'colunaExibir' => false),
            //array('colunaValor' => 'T219_T218_Id', 'colunaTitulo' => 'Cotação', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T003';
		$extra = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D020 on D020_Id=D024_D020_Id
			left join D013 as Orcamento on Orcamento.D013_Id=T003_D013_Id
			left join D013 as Cliente on Cliente.D013_D024_Id=T003_D024_Id
			left join T004 on T004_T003_Id=T003_Id
			left join T006 on T006_T004_Id=T004_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
			left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
			left join T219 on T219_Id=T004_T219_Id
			LEFT JOIN T139 ON T139_T003_Id=T003_Id 
			LEFT JOIN D085 ON D085_T003_Id=T003_Id
			WHERE T139_Id > 0 AND T139_Data_Hora < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 4 HOUR)	AND D085_Id IS NULL
			AND T003_Flag_Status_Orcamento = 'P' AND T003_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' AND T003_C004_Id='{$g['empresaAtual']}'
			GROUP BY T003_Id ORDER BY T139_Id DESC
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
            'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

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
					var id = $(this).closest('tr').attr('id').replace(/T003_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Orçamento - ID: ' + id, '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
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

	case 'crm001GridFaturarBalcao':
		$grid = array(
			array('colunaValor' => "campoOpcoesSeparacaoSelecionaAcao", 'colunaTipo' => "livre", 'callback' => 'campoOpcoesSeparacaoSelecionaAcao', 'style' => 'width: 1%; padding: 3px 1px;'),
			array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
			array('colunaValor' => "DividirPedidoCliente", 'colunaTipo' => "livre", 'callback' => 'dividirPedidoCliente', 'style' => 'width: 1%'),
			array('colunaValor' => "campoCancelamento", 'colunaTipo' => "livre", 'callback' => 'campoCancelamento', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissao', 'callback' => 'gCorrigeData'),
 			array('colunaValor' => 'T005_T003_Id', 'colunaTitulo' => 'Orcamento'),
 			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
 			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'if(T005_Flag_Dividido="N","","S") as T005_Flag_Dividido', 'colunaTitulo' => 'Div'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2' ),
			array('colunaValor' => 'T005_Prazos(T005_Id) as Prazos', 'colunaTitulo' => 'Prazos', 'colunaCasasDecimais' => '2' ),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante'),
			array('colunaValor' => 'D021_Percentual_Comissao', 'colunaTitulo' => '% Comis'),
			array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Liberou', 'colunaTitulo' => 'Liberou'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
			 	left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
				where T005_C004_Id={$g['empresaAtual']}
                  and T005_Flag_Separacao='S'  
                  and T005_Flag_Status='22'  
                  and T005_Flag_Separacao_Liberar='N'  
                  and T005_Flag_Expedicao=2	
                group by T005_Id 
				order by T005_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		
		if ($g['conf']->ini('expedicaoDeMercadoria') == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,615]);            
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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

	case 'crm001GridFaturarComercial':
		$grid = array(
			array('colunaValor' => "campoOpcoesSeparacaoSelecionaAcao", 'colunaTipo' => "livre", 'callback' => 'campoOpcoesSeparacaoSelecionaAcao', 'style' => 'width: 1%; padding: 3px 1px;'),
			array('colunaValor' => 'campoAlterarStatusPedido', 'colunaTipo' => 'livre','callback' => 'campoAlterarStatusPedido', 'style' => 'width:30px; padding: 3px 1px 3px 10px;', 'colunaExibir' => false),
			array('colunaValor' => "DividirPedidoCliente", 'colunaTipo' => "livre", 'callback' => 'dividirPedidoCliente', 'style' => 'width: 1%'),
			array('colunaValor' => "campoCancelamento", 'colunaTipo' => "livre", 'callback' => 'campoCancelamento', 'style' => 'width: 1%'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissao', 'callback' => 'gCorrigeData'),
 			array('colunaValor' => 'T005_T003_Id', 'colunaTitulo' => 'Orcamento'),
 			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true),
 			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'if(T005_Flag_Dividido="N","","S") as T005_Flag_Dividido', 'colunaTitulo' => 'Div'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2' ),
			array('colunaValor' => 'T005_Prazos(T005_Id) as Prazos', 'colunaTitulo' => 'Prazos', 'colunaCasasDecimais' => '2' ),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => 'D021_Nome_Empresa', 'colunaTitulo' => 'Representante'),
			array('colunaValor' => 'D021_Percentual_Comissao', 'colunaTitulo' => '% Comis'),
			array('colunaValor' => 'Liberou.C007_Primeiro_Nome as Liberou', 'colunaTitulo' => 'Liberou'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extra = <<<EOT
			 	left join D024 on D024_Id=T005_D024_Id
				left join D020 on D020_Id=D024_D020_Id
				left join D018 on D018_Id=D024_D018_Id
				left join D021 on D021_Id=T005_D021_Id
				left join D022 on D022_Id=T005_D022_Id
				left join D006 on D006_Id=T005_D006_Id
				left join T006 on T005_Id=T006_T005_Id
				left join T055 on T055_D009_Id=T006_D009_Id
				left join T075 on T075_Id=T055_T075_Id 
				left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				left join C004 on C004_Id=T005_C004_Id
				left join D036 on T005_D036_Id=D036_Id 
				left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
				left join C007 as Liberou on Liberou.C007_Id=T005_Liberacao_Credito_Id
				left join C007 as Separou on Separou.C007_Id=T005_C007_Id_Separou
				where T005_C004_Id={$g['empresaAtual']}
                  and T005_Flag_Separacao='S'  
                  and T005_Flag_Status='22'  
                  and T005_Flag_Separacao_Liberar='N'  
                  and T005_Flag_Expedicao=1	
                group by T005_Id 
				order by T005_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Data_Saida' => array('titulo' => 'Saída', 'tipo' => 'data'),
			'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'Liberou.C007_Primeiro_Nome as Nome_Liberou' => array('titulo' => 'Liberou', 'tipo' => 'texto'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();
		
		
		if ($g['conf']->ini('expedicaoDeMercadoria') == 'normal') {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentPedido/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);

					  abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,615]);            
				});
			} </script></div>
EOT;
		} else {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
 				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Pedido: ' + id  + ' - ' + $(this).closest('tr').find('td:eq(3)').text(), '/est/est002/content/est002ContentProdutosEmSeparacao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,500]);
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

	case 'crm001GridTarefas':
		$filtroObrigatorio = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T259_Id', 'colunaTitulo' => 'Tarefa', 'chavePrimaria' => true, 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T260_Id', 'chavePrimaria' => true, 'colunaExibir' => false), //NAO REMOVER CHAVE PRIMARIA
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => "CONCAT(substr(D024_Nome_Empresa,1,40),' (',COUNT(DISTINCT D085_Id),')')", 'colunaTitulo' => 'Empresa', 'style' => 'width:1%'),
            array('colunaValor' => "GROUP_CONCAT(DISTINCT SUBSTRING(D085_Mensagem,1,25) ORDER BY D085_Data DESC, D085_Hora DESC SEPARATOR ', ')", 'colunaTitulo' => 'Assunto'),
            array('colunaValor' => 'T259_Data_Hora', 'colunaTitulo' => 'Último contato', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T260_Flag_Lido', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T259';

		$extra = <<<EOT
			LEFT JOIN D085 ON D085_T259_Id=T259_Id
			LEFT JOIN C007 as vendedor on vendedor.C007_Id=T259_C007_Id
			LEFT JOIN C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
			LEFT JOIN T260 ON T260_T259_Id=T259_Id AND T260_C007_Id = '{$g['usuarioAtual']}'
			LEFT JOIN D024 on D024_Id=T259_D024_Id
			WHERE IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
			GROUP BY T259_Id
			ORDER BY IF(T260_Flag_Lido = 'S', 1, 0) ASC, T259_Data_Hora DESC
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T260_Flag_Lido'] == 'N' || $array['todosCampos']['T260_Id'] <= 0){
				return 'linhaTarefaNaoLida';
			} else {
				return 'linhaTarefaLida';
			}
		}

		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="incluirTarefa">Incluir</button>
				<button type="button" id="processarTarefas">Processar</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#incluirTarefa').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/crm/crm001/content/crm001contentEditarAgenda/', '&tab=geral', [700,405]);
				});
				$('#processarTarefas').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/crm/crm001/grid_func-ajax/processarTarefas/?ajax=true&callback=?', function(request) {
								$('#' + '{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}');
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Deseja processar as tarefas sem vínculo?', \$buttons);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'D085_Mensagem' => array('titulo' => 'Assunto', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'T259_Id' => array('titulo' => 'Tarefa', 'tipo' => 'numero'),
			'T259_Data_Hora' => array('titulo' => 'Data', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		//Define ação do click de abertura (ao clicar em uma linha do grid)
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//$('.gridOptTop').remove();
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					$(this).closest('tr').removeClass('linhaTarefaNaoLida').addClass('linhaTarefaLida');
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T259_Id = id[0].replace(/T259_Id-/, '');
					var T260_Id = id[1].replace(/T260_Id-/, '');
					var D024_Id = id[2].replace(/D024_Id-/, '');

					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Negociação - ' + T259_Id, '/crm/crm001/content/crm001ContentTarefa/', '&acaoId=' + encodeURIComponent(T259_Id) + '&T260_Id=' + encodeURIComponent(T260_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [900,600]);
				});
			} </script></div>
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		if (!personalizacaoTotais()) {
			$total = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, gRetirarGroupBy($extra));
			echo $totais = <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Tarefas<br><input type="text" value="{$total}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
			$g['smarty']->assign('totais', $totais);
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

	case 'crm001GridTarefa':
		if($r_T260_Id > 0){
			mysql_query("UPDATE T260 SET T260_Flag_Lido = 'S' WHERE T260_Id = '{$r_T260_Id}'");
		} else {
			mysql_query("INSERT INTO T260 (T260_Flag_Lido, T260_C007_Id, T260_T259_Id, T260_Data_Hora) VALUES ('S', '{$g['usuarioAtual']}', '{$r_acaoId}', CURRENT_TIMESTAMP())");
		}
		echo <<<EOT
  			<div id="divTarefas" style="box-sizing:border-box; padding: 10px; overflow-y: scroll;">
EOT;
		$sqlD085 = mysql_query("SELECT * FROM D085 LEFT JOIN C007 ON C007_Id=D085_C007_Id LEFT JOIN D013 ON D013_Id=D085_D013_Id WHERE D085_T259_Id = '{$r_acaoId}' ORDER BY CONCAT(D085_Data, ' ', D085_Hora) ASC");
  		while($rowD085 = mysql_fetch_array($sqlD085)){
  			$mensagemOculta = '';
  			if($rowD085['D085_Entidade'] == '' && $rowD085['D085_T139_Id'] > 0){
  				$sql = mysql_query("SELECT T139_Id, T139_De, T139_Pasta, T139_Mensagem_Corpo FROM T139 WHERE T139_Id = '{$rowD085['D085_T139_Id']}'");
  				$row = mysql_fetch_assoc($sql);
  				$rowD085['D085_Entidade'] = 'T139';
  				$rowD085['D085_Id_Entidade'] = $rowD085['D085_T139_Id'];
  				$row['T139_Id'] = 0;
  				$rowD085['C007_Primeiro_Nome'] = strtoupper($row['T139_De']);
  				$mensagemOculta = '';//utf8_decode($row['T139_Mensagem_Corpo']);
  				$contato = '';
  			} else if($rowD085['D085_Entidade'] == 'T003' || $rowD085['D085_Entidade'] == 'T005' || $rowD085['D085_Entidade'] == 'T007'){
  				$sql = mysql_query("SELECT T139_Id FROM T139 WHERE T139_{$rowD085['D085_Entidade']}_Id = '{$rowD085['D085_Id_Entidade']}' ORDER BY T139_Id DESC LIMIT 1");
  				$row = mysql_fetch_assoc($sql);
  				$contato = array($rowD085['D013_Nome_Contato'], $rowD085['D013_E_Mail'], $rowD085['D013_DDD_Telefone_1'], $rowD085['D013_Telefone_1'], $rowD085['D013_DDD_Celular'], $rowD085['D013_Celular']);
  			}

  			$conteudo = array(
  				'mensagem' => $rowD085['D085_Mensagem'],
  				'mensagemOculta' => $mensagemOculta,
  				'usuario' => $rowD085['C007_Primeiro_Nome'],
  				'contato' => $contato,
  				'data' => date("d/m/Y H:i:s", strtotime($rowD085['D085_Data'].' '.$rowD085['D085_Hora'])),
  				'T139_Id' => $row['T139_Id'],
  				'entidade' => array($rowD085['D085_Entidade'], $rowD085['D085_Id_Entidade'])
  			);
  			echo montarInteracaoTarefa($conteudo, ($row['T139_Pasta'] == 'E') ? 'E' : 'R');
  		}
  		echo <<<EOT
  			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
  				resizeGridTarefa();

				function resizeGridTarefa(){
					setTimeout(function() {
						var outerHeight = parseFloat($('#{$g['divId']}').parent().css('height'));
						var outerWidth = parseFloat($('#{$g['divId']}').parent().css('width'));
						$('#divTarefas').css({'height': outerHeight - 128});
						$('#{$g['divId']}').css({'height': outerHeight - 128});
						$('#{$r_divForm}').css({'height': 125});

						$('#{$r_divForm} #D085_Mensagem').css({'width': outerWidth-45});
						$('#{$r_divForm} .formEditor-crm001FormInserirAgendaTarefa_btn').css({'left': outerWidth-77});

						$("#divTarefas").animate({ scrollTop: $('#divTarefas')[0].scrollHeight}, 1000);
					}, 50);
				}

				$('.ui-dialog:last .ui-icon-newwin').bind("click", function(data, ui) {
					setTimeout(function() {
						resizeGridTarefa();
					}, 50);
				});
			} </script></div>
EOT;
	break;

	case 'crm001GridPrincipalOrdemCompra':
		$filtroObrigatorio = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaExibir' => false),
            //array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'SUBSTRING(D024_Nome_Empresa,1,20)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'width:1%; font-weight:bold', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data Ped.', 'colunaAlinhar' => 'center', 'style' => 'width:1%;background:#A1FCA9', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T005_T003_Id', 'colunaTitulo' => 'Orç.', 'colunaAlinhar' => 'center', 'style' => 'width:1%; font-weight:bold;background:#A1FCA9', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T005_T003_Id', '990,605'), 'colunaExibirCallbackNivel' => '4'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%; font-weight:bold;background:#A1FCA9', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T218_Id', '990,605'), 'colunaExibirCallbackNivel' => '4'),
            //array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            //array('colunaValor' => 'group_concat(distinct "<span style=\\"font-weight:bold\\">", T005_Status_Pedido(T005_Flag_Status,1), "</span> ", ifnull((SELECT CONCAT(date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>"), if(T005_Flag_Status=8, concat("<br />", T178_Observacao), "")) FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1), ""), "<br />", if(T005_Flag_Status=9, T005_Motivo_Nao_Liberado,"")) as Status2', 'colunaTitulo' => 'Status', 'style' => 'max-width:200px;white-space:normal;min-width:200px;width:200px'),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'width:1%;background:#A1FCA9', 'callback' => 'validaCampoNFOrcamentos'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde Ped.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%;background:#A1FCA9'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'width:1%;'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade_Estoque as Pedido', 'colunaTitulo' => 'Estoq', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T006_Quantidade')),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Flag status', 'colunaExibir' => false),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Data O.C.', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:normal;background: var(--celula-amarelo);', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cotação', 'colunaAlinhar' => 'center', 'style' => 'width:1%; font-weight:bold;background: var(--celula-amarelo);', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', 'T218_Id', '990,605'), 'colunaExibirCallbackNivel' => '4'),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'O.C.', 'colunaAlinhar' => 'center', 'style' => 'width:1%; font-weight:bold;background: var(--celula-amarelo);', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605'), 'colunaExibirCallbackNivel' => '4'),
            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Qtde. O.C.', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'colunaAlinhar' => 'left', 'style' => 'width:1%;background: var(--celula-amarelo);'),
            array('colunaValor' => 'T225_Observacao', 'colunaTitulo' => 'Entrega dias', 'colunaAlinhar' => 'left', 'style' => 'width:1%;background: var(--celula-amarelo);'),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'T225';

		$extra = <<<EOT
				LEFT JOIN T224 ON T224_Id=T225_T224_Id
				LEFT JOIN T219 ON T219_Id=T225_T219_Id
				LEFT JOIN T218 ON T218_Id=T219_T218_Id
				LEFT JOIN T006 on T219_Id=T006_T219_Id
				LEFT JOIN T005 on T005_Id=T006_T005_Id
				LEFT JOIN D024 on D024_Id=T005_D024_Id
				LEFT JOIN D018 on D018_Id=D024_D018_Id
				LEFT JOIN D037 on D037_Id=T006_D037_Id
				LEFT JOIN D009 on D009_Id=T006_D009_Id
				LEFT JOIN D049 on D049_Id=D009_D049_Id
				LEFT JOIN D082 on D082_Id=D049_D082_Id
				LEFT JOIN D001 on D001_Id=D049_D001_Id
				LEFT JOIN D006 on D006_Id=T006_D006_Id
				LEFT JOIN D005 on D005_Id=T006_D005_Id
				LEFT JOIN T066 on T066_D009_Id=D009_Id
				LEFT JOIN D004 on D004_Id=T066_D004_Id
				LEFT JOIN T004 ON T004_Id=T006_T004_Id
				LEFT JOIN T007 ON T007_T005_Id=T005_Id
				LEFT JOIN C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				LEFT JOIN C007 as Liberou on Liberou.C007_Id=T005_Liberou_Alcada_C007_Id
				LEFT JOIN C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
				WHERE (T005_C004_Id='{$g['empresaAtual']}'
				AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null))
				AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
				group by T006_Id
				order by T005_Id desc
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
		    /*if($array['todosCampos']['T005_Flag_Status']=='5'){
		        return 'linhaCinza';
		    } else if($array['todosCampos']['T005_Flag_Status']=='8'){
		        return 'linhaVermelho';
		    } else if($array['todosCampos']['T005_Flag_Status']=='26'){
		        return 'linhaLaranja';
		    } else if($array['todosCampos']['T005_Flag_Status']=='9'){
		    } else if($array['todosCampos']['T005_Flag_Status']=='14'){
		       return 'linhaVerde';
		  		// } else if ($array['todosCampos']['T005_Flag_Reenviado_Almoxarifado'] == 'S') {
		    } else if($array['todosCampos']['T005_Flag_Status']<=0){
		       return 'linhaAzul';
		    }*/
		}

		//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
					var id = $(this).closest('tr').attr('id').split('|');
					var T005_Id = id[0].replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [990,605]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T005_T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T007_Numero_Nota_Fiscal' => array('titulo' => 'Nota Fiscal', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão ped.', 'tipo' => 'data'),
            'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'numero'),
            'T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'numero'),
            'D024_Nome_Fantasia,D024_Nome_Empresa,D024_Id' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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

	case 'crm001GridPrincipalOrdemProducao':
		/* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'IF(T255_Flag_Status="99","Finalizado","Aberto")', 'colunaTitulo' => 'Status', 'style' => 'width:1%;'),
            array('colunaValor' => 'T255_Flag_Status', 'colunaTitulo' => 'Flag status', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T256_Data_Previsao_Entrega', 'colunaTitulo' => 'Previsão entrega', 'style' => 'width:1%;', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_Observacao_Producao', 'colunaTitulo' => 'Observação produção', 'style' => 'width:1%;'),
        );

		$from = 'T256';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T255 on T255_Id=T256_T255_Id
			LEFT JOIN T257 ON T256_Id=T257_T256_Id
			LEFT JOIN T001 ON T257_Id=T001_T257_Id
			LEFT JOIN D009 on D009_Id=T256_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			WHERE T255_C004_Id = '{$g['empresaAtual']}' 
			GROUP BY T256_Id
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       return 'linhaCinza';
		    } else {
		       //return 'linhaAzul';
		    }
		}	

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T255_Id' => array('titulo' => 'Ordem produção', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]"Aberto")' => array('titulo' => 'Status', 'tipo' => 'texto'),
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
				var id = $(this).closest('tr').attr('id').replace(/T256_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Entrada de produção', '/est/est017/content/est017ContentEntradaProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,400]);
			});
		} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$sql_ = str_replace("LIMIT 0,100", "", $geradoSql);
		$sql_ = explode("FROM T256", $sql_);
		$sql_ = gInsertExtraWhere($sql_[1], "T257_Id > 0 AND T001_Id IS NULL");
		$sql_ = str_replace("GROUP BY T256_Id", "GROUP BY T257_Id", $sql_);
		$sql_ = base64_encode($sql_);

		if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est017ProcessarProducao">Processar</button>
				</div>
EOT;
		}	

		// Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est017ProcessarProducao').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est017/grid_func-ajax/est017VerificarProcessarProducao/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Processar Produção', '/est/est017/content/est017ContentProcessarProducao/', '&extra=' + encodeURIComponent("{$sql_}"), [700,650]);
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

	case 'crm001GridCancelarPedidos':
		/**
		* Este código PHP será executado ao acessar: /crm-crm001-grid-crm001GridCancelarPedidos/
		* Ele irá substituir o original (que fica dentro do .PHP, no switch/case)
		*
		* Caso seja uma AJAX, é necessário acrescentar este código:
		* No inicio: $resposta = array('code' => true, 'data' => array());
		* No final: echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
		*/

		$grid = array(
 			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Data_Entrega', 'colunaTitulo' => 'Entrega', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T005";
		
		$extraOriginal = base64_decode($r_extra); 
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T005_Flag_Marcado = 'S'"));

		$extra = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
			left join D013 on D013_Id=T005_D013_Id
    		left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     		left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
    		left join D006 on D006_Id=T005_D006_Id
			left join D021 on D021_Id=T005_D021_Id
			WHERE {$extra}
EOT;
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D134_Cor' => array('titulo' => 'Cor', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		if (!personalizacaoTotais()) { 
			$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span style="float:right; display: block; color: red; margin: 10px 200px 0 0; font-size: 12px;">ATENÇÃO: Os pedidos desta lista serão cancelados.</span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var T005_Id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [1000,615]);
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
	* Chamado em: ven002tab01
	*/
	case 'ven002grid01':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'campoCancelamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamento', 'style' => 'width:1%;'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'Cód. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total_Servicos', 'colunaTitulo' => 'Servicos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Troca', 'colunaTitulo' => 'Trocas', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0,"MP","PE"))) FROM T006 WHERE T006_T005_Id=T005_Id) as Estoque', 'colunaTitulo' => 'Estoque', 'style' => 'width:1%;'),
            array('colunaValor' => 'T005_Flag_Status_Financeiro', 'colunaTitulo' => 'Status Financeiro', 'callback' => 'statusFinanceiro', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as Status', 'colunaTitulo' => 'Status', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat("<span style=\\"font-weight:bold\\">", T005_Status_Pedido(T005_Flag_Status,1), "</span> ", ifnull((SELECT date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1), ""), "<br />", if(T005_Flag_Status=9, T005_Motivo_Nao_Liberado,"")) as Status2', 'colunaTitulo' => 'Status', 'style' => 'width:1%'),
            array('colunaValor' => 'group_concat(distinct if(T007_Numero_Nota_Fiscal="","Finalizar",T007_Numero_Nota_Fiscal) separator ", ") as NF', 'colunaTitulo' => 'NF', 'style' => 'font-weight:bold', 'callback' => 'validaCampoNFPedidos'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaExibir' => 'N'),
            array('colunaValor' => 'Status Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo' => 'livre', 'callback' => 'validaCampoStatusGridPrincipal', 'colunaExibir' => false),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'T005';
		
		/*
		left join T006 on T006_Id= T006_T005_Id
    			LEFT JOIN D009 ON D009_Id = T006_D009_Id
				LEFT JOIN D049 ON D049_Id = D009_D049_Id
				LEFT JOIN D082 ON D082_Id = D049_D082_Id
		*/
		$extra = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
    		left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S' and T007_Flag_Inutilizado!='S' and IFNULL(T007_Protocolo_Inutilizacao,'')=''
     		left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join C004 on C004_Id = T005_C004_Id
    		left join D006 on D006_Id = T005_D006_Id
    		left join D116 on D116_Id = T005_D116_Id
    		left join D021 on D021_Id = T005_D021_Id
    		where (T005_C004_Id={$g['empresaAtual']} AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null))
    		GROUP BY T005_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
    		left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S' and T007_Flag_Inutilizado!='S' and IFNULL(T007_Protocolo_Inutilizacao,'')=''
     		left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    		left join D006 on D006_Id=T005_D006_Id
    		left join D116 on D116_Id=T005_D116_Id			
    		left join D021 on D021_Id = T005_D021_Id
    		where (T005_C004_Id={$g['empresaAtual']} AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null))
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T005_Flag_Status']=='5'){
				return 'linhaCinza';
			}
			else if($array['todosCampos']['T005_Flag_Status']<=0){
				return 'linhaAzul';
			}
		}
		
		/*
		* Botao Receber Pedidos do ecommerce
		*/
		/*echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="ven002RecebePedidos">Receber Pedidos</button>
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#ven002RecebePedidos').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Receber Pedidos', '/ven/ven002/content/ven002ContentReceberPedidos/', '', [400,200]);
			});			
		} </script></div>
EOT;/**/
		echo <<<EOT
	        <div class="buttonsBottomTop">
	            <button type="button" id="ven002ProcessarPedidosVencidos" onclick="processarPedidosVencidos('{$g['divId']}');">Processar Vencidos</button>
	        </div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D024_Nome_Empresa' => array('titulo' => 'Razão Social', 'tipo' => 'texto', 'exibir' => false),
            'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'T005_Flag_Status_Financeiro' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Pendentes', 'value' => '1'),
                array('title' => 'Concluidos', 'value' => '2'),
                array('title' => 'Cancelados', 'value' => '3'),
              ), 'naoGerarExtra' => ''),
            'T005_Status_Pedido(T005_Flag_Status,1)' => array('titulo' => 'Status Pedido', 'tipo' => 'texto'),
            '(SELECT GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0,"MP","PE"))) FROM T006 WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Estoque', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Totalizadores
		 */
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extraTotal);
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T005_Valor_Total)", $from, $extraTotal));
		$valorTotalPendentes = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T005_Valor_Total)", $from, $extraTotal));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Pedidos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
				<div style="clear:both;"></div>             
			</div>
EOT;

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
				var T005_Id = id[0].replace(/T005_Id-/, '');
				var T007_Id = id[1].replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [990,605]);  //950 615
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
	case 'cad061grid02':

		$sql = "SELECT D157_Casas_Decimais_Preco FROM D157 WHERE D157_Id = '{$r_D157_Id}'";
		$resD157 = mysql_query($sql);
		$rowD157 = mysql_fetch_assoc($resD157);

		$colunaPrecoLiquido = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPrecoBruto = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? true : false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
             			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto'),
            array('colunaValor' => 'D158_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D158_Item'),
            array('colunaValor' => 'D158_D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D158_D157_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item'),
            array('colunaValor' => 'D158_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'font-weight:bold;width:1%'),
            array('colunaValor' => 'D158_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:bold', 'callback' => 'campoDescricaoItemPedido', 'colunaExibir' => false),
            array('colunaValor' => 'SUBSTRING(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:10px'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D158_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D158_Quantidade_OP', 'colunaTitulo' => 'OP', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Produção', '/ven/ven001/content/ven001contentProdutoOP/', 'D158_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            //array('colunaValor' => "(IF(D158_Quantidade_Estoque< D158_Quantidade, CONCAT('<span style=\"color: red;\">',REPLACE( REPLACE( REPLACE( FORMAT( D158_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ','),'</span>'), REPLACE( REPLACE( REPLACE( FORMAT( D158_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ',')))", 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array(false, $r_acaoId)),
            array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Quantidade_Estoque as Pedido', 'colunaTitulo' => 'Estoque', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'D158_Quantidade')),
            array('colunaValor' => 'D158_C004_Id_Estoque', 'colunaTitulo' => 'CD', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'montaComboEstoque', 'colunaExibir' => false),
            /*array('colunaValor' => 'D158_Flag_Reservar_Estoque', 'colunaTitulo' => 'Reservado', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array(
                 'D158_Flag_Reservar_Estoque', 'N|S'),
            ),*/
            array('colunaValor' => 'D158_Id', 'colunaTitulo' => 'D158_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'T004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D158_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'style' => 'font-weight:bold;background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'D158_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaExibirNivel' => 4),

            array('colunaValor' => 'D158_Valor_Preco_Original', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaPedido', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'D158_Id', 'right', 4, 'D158'), 'colunaExibir' => $colunaPrecoLiquido),

            array('colunaValor' => 'D158_Valor_Preco_Tabela', 'colunaTitulo' => 'Forn', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoFornecedorPedido', 'callbackParameter' => array('Demonstrativo Preço Fornecedor', '/ven/ven001/outro/demonstrativoPrecoFornecedor/', 'D158_Id', 'right', 4, 'D158'), 'colunaExibir' => false),
			array('colunaValor' => "(SELECT D009_Id FROM D009 WHERE D009_D049_Id=D049_Id AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}') as D009_Id_Transferencia", 'colunaTitulo' => 'D009_Id_Transferencia Id', 'colunaExibir' => false),
			array('colunaValor' => "'{$g['C031']['C004PrecoTransferenciaGridProdutos']}' as C004_Id_Transferencia", 'colunaTitulo' => 'C004_Id_Transferencia Id', 'colunaExibir' => false),
			array('colunaValor' => 'D158_Demonstrativo_Preco_Transferencia', 'colunaTitulo' => 'D158_Demonstrativo_Preco_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'D158_Valor_Custo_Transferencia', 'colunaTitulo' => 'D158_Valor_Custo_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'D158_Flag_Origem_Produto_Transferencia', 'colunaTitulo' => 'D158_Flag_Origem_Produto_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'D158_Valor_Preco_Transferencia', 'colunaTitulo' => 'Transf', 'colunaAlinhar' => 'right', 'callback' => 'retornaPrecoTransferencia', 'callbackParameter' => array(false, $r_acaoId, false, 'D158_Id', 'D009_Id_Transferencia', 'C004_Id_Transferencia'), 'colunaExibir' => false),

            array('colunaValor' => 'D158_Percentual_Desconto', 'colunaTitulo' => '% Desc', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D158_Percentual_Desconto', 'colunaExibir' => $colunaPrecoLiquido),
            array('colunaValor' => 'D158_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $rowD157['D157_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'font-weight:bold;background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D158_Percentual_Desconto', 'colunaExibir' => $colunaPrecoLiquido),
            array('colunaValor' => 'D158_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => $colunaPrecoLiquido),


            array('colunaValor' => 'D158_Valor_Preco_Original_Bruto', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaPedido', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'D158_Id', 'right', 4, 'D158'), 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => 'D158_Percentual_Desconto_Bruto', 'colunaTitulo' => '% Desc', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D158_Percentual_Desconto', 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => 'D158_Valor_Preco_Bruto', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $rowD157['D157_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'font-weight:bold;background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D158_Percentual_Desconto', 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => '(D158_Valor_Preco_Bruto*D158_Quantidade) as D158_Valor_Total_Bruto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => $colunaPrecoBruto),


            array('colunaValor' => 'D158_Valor_Preco_Sem_Desconto_Unitario+(D158_Valor_IPI/D158_Quantidade)+(D158_Valor_ICMS_Substituicao_Retencao/D158_Quantidade) as Preco_Bruto', 'colunaTitulo' => 'Preço Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'IF(D009_Data_Alteracao_Custo > D009_Data_Ultima_Entrada, D009_Data_Alteracao_Custo, D009_Data_Ultima_Entrada)', 'colunaTitulo' => 'Ult. Alt. Custo', 'callback' => 'gCorrigeData', 'callbackParameter' => 'true', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Valor_Total_Desconto', 'colunaTitulo' => 'R$ Desc', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'concat(replace(replace(round(((D158_Valor_Preco_Unitario-D158_Valor_Custo_Unitario)/D158_Valor_Preco_Unitario)*100),",",""),".",","),"","%")', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => "D158_Percentual_Margem", 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaCasasDecimais' => 2, 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'D158_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'D158_IPV_Sem_Lucro', 'colunaTitulo' => 'IPV SL', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Valor_Lucro', 'colunaTitulo' => 'R$ Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => '(D158_Valor_Lucro/((D158_Valor_Custo_Unitario*D158_IPV)*D158_Quantidade))*100', 'colunaTitulo' => '% Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Observacao_Entrega', 'colunaTitulo' => 'Entg.dias', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D158_Observacao', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('D158_D006_Id_Info', 'Demonstrativo CFOP', 'D158_Id', 'left', '', '500')),
            array('colunaValor' => 'D158_D006_Id_Info', 'colunaExibir' => false),
            array('colunaValor' => 'IF(IFNULL(D149_Id,0) > 0, concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao), "????????") as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('D158_D005_Id_Info', 'Demonstrativo NCM', 'D158_Id', 'left', '', '500')),
            array('colunaValor' => 'D158_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px'),
            array('colunaValor' => 'D158_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array(
                array('title' => '...', 'value' => '', 'selected' => true),
                array('title' => '1', 'value' => '1'),
                array('title' => '2', 'value' => '2'),
                array('title' => '3', 'value' => '3'),
                array('title' => '4', 'value' => '4'),
              ),
              'style' => 'background: var(--celula-vermelho);;font-size:12px', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'D158_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D158_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D158_Percentual_Reducao_ICMS', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => '((D158_Valor_ICMS_Substituicao_Retencao/D158_Quantidade)/D158_Valor_Preco_Unitario)*100', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D158_Aliquota_PIS', 'colunaTitulo' => 'PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D158_Aliquota_COFINS', 'colunaTitulo' => 'COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D158_Aliquota_IRPJ', 'colunaTitulo' => 'IRPJ', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D158_Aliquota_CSLL', 'colunaTitulo' => 'CSLL', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            //array('colunaValor' => 'D158_Aliquota_ICMS_ST', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D158_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_IRPJ', 'colunaTitulo' => 'IRPJ R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_CSLL', 'colunaTitulo' => 'CSLL R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'D158_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'D158_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'D158_Percentual_Reducao_ICMS_ST', 'colunaTitulo' => '%Red', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'D158_ST_VA', 'colunaTitulo' => 'MVA', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            //array('colunaValor' => 'D158_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'D158_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_Base_IRPJ', 'colunaTitulo' => 'Base IRPJ', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_Base_CSLL', 'colunaTitulo' => 'Base CSLL', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D158_Item_Pedido_Compra', 'colunaTitulo' => 'Item.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
            array('colunaValor' => 'D158_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Valor_Total_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => '((D158_Valor_Preco_Unitario/D158_Valor_Custo_Unitario)-1)*100', 'colunaTitulo' => '% Margem', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré Cadastro', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Percentual_Comissao', 'colunaTitulo' => '%Comiss', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D158_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'D158_Quantidade_Estoque', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T004_T219_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cotação', 'style' => 'width:1%;'),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cotação', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', 'T218_Id', '990,605'), 'colunaExibirNivel' => 4),
            array('colunaValor' => 'D158_T206_Id', 'colunaTitulo' => 'T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D158_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D158_T225_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'IF((D158_T219_Id > 0 OR D158_T225_Id > 0 OR D158_Demonstrativo_Preco_Cotacao != "") AND D158_Valor_Preco_Cotacao > 0, 675, 450) as width', 'colunaTitulo' => 'width', 'colunaExibir' => false),
            array('colunaValor' => 'D158_D158_Id_Dividir', 'colunaTitulo' => 'Id Div', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
            array('colunaValor' => 'D158_Flag_Transferencia', 'colunaTitulo' => 'D158_Flag_Transferencia', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Tabela', 'colunaTitulo' => 'D009_Valor_Custo_Tabela', 'colunaExibir' => false),
            array('colunaValor' => 'D070_Data_Vencimento', 'colunaTitulo' => 'D070_Data_Vencimento', 'colunaExibir' => false),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'D158';

		$extra = <<<EOT
				left join D157 on D157_Id=D158_D157_Id
				left join D024 on D024_Id=D158_D024_Id
				left join D037 on D037_Id=D158_D037_Id
				left join D009 on D009_Id=D158_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D006 on D006_Id=D158_D006_Id
				left join D005 on D005_Id=D158_D005_Id
				LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=D157_C004_Id
				LEFT JOIN T066 on T066_D009_Id=D009_Id
				LEFT JOIN D004 on D004_Id=T066_D004_Id
				LEFT JOIN T004 ON T004_Id=D158_T004_Id
				left join T219 ON T219_Id=D158_T219_Id
				left join T218 ON T218_Id=T219_T218_Id
				LEFT JOIN D069 on D069_Id=D009_D069_Id AND D069_Flag_Tabela_Principal='S'
				LEFT JOIN D070 on D070_Id=D069_D070_Id
				where D158_D157_Id = '{$r_acaoId}'
				group by D158_Id 
				
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
			if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
				return 'linhaVermelho';
			}
		}

		//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D158_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/cad/cad061/content/cad061ContentEditarProduto/', '&acaoId=' + encodeURIComponent(acaoId) + '&D157_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela={$from}', [600,400]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});

			function colunaEditarCallbackJSReload() {
				divRefresh('{$r_divReload}');
			}
			
			// Função JS executada como callback do editar campo (colunaEditarCallbackJS)
			function colunaEditarCallbackJS_D158_Percentual_Desconto(D158_Id, campoEditado, valorAntigo, novoValor) {
					D158_Id = D158_Id.replace(/D158_Id-/, '');
					if(campoEditado == 'D158_Percentual_Desconto' || campoEditado == 'D158_Valor_Preco_Bruto'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&D158_Id='    + encodeURIComponent(D158_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, D158_Id, 'D158_Id'); // grid
								divRefresh('{$r_divReload}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if(campoEditado == 'D158_Percentual_Desconto_Bruto'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&D158_Id='    + encodeURIComponent(D158_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, D158_Id, 'D158_Id'); // grid
								divRefresh('{$r_divReload}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if(campoEditado == 'D158_Valor_Preco_Sem_Desconto_Unitario'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&D158_Id='    + encodeURIComponent(D158_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, D158_Id, 'D158_Id'); // grid
								divRefresh('{$r_divReload}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
					//divRefresh('{$g['divId']}', true, false, false, D158_Id, 'D158_Id'); // grid
					divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
				}

				function colunaEditarCallbackJS_D158_Item(D158_Id, campoEditado, valorAntigo, novoValor) {
					D158_Id = D158_Id.replace(/D158_Id-/, '');
					$.getJSON('/ven/ven002/grid_func-ajax/processarNumeroItemPedido/?ajax=true&D158_Id='    + encodeURIComponent(D158_Id) + 
																				      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				      '&novoValor='			+ encodeURIComponent(novoValor) +  
																				      '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				}

				function gridTheadCallback() {
					$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
							.removeAttr('onclick')
							.click(function() {
							var \$buttons = {
								"Marcar Todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/grid_func-ajax/ven002MarcarTodosExcluir/?ajax=true&D157_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Desmarcar todos": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/grid_func-ajax/ven002DesmarcarTodosExcluir/?ajax=true&D157_Id='+encodeURIComponent('{$r_acaoId}')+'&callback=?', function(request) {
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
	case 'ven002grid03':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'excluirServico'),
            array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria' => true),
            array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'geraImagem'),
            array('colunaValor' => 'concat(T147_Titulo, \'<br />\', T147_Descricao) as Servico', 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T147_Valor_Unitario', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaEditar' => true),
            array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
            array('colunaValor' => 'T147_T145_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
            array('colunaValor' => 'ven002_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo', 'colunaExibir' => false),
            array('colunaValor' => 'T147_D116_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado'),
            array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status', 'callback' => 'validaCampoStatus'),
        );
	
		/**
			*
			* Definição da SQL
			*/
		$from = 'T147';
	
		$extra = <<<EOT
				LEFT JOIN T006 ON T006_Id=T147_T006_Id
				LEFT JOIN D009 ON D009_Id=T006_D009_Id
				LEFT JOIN D049 ON D009_D049_Id=D049_Id
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				LEFT JOIN T212 ON T147_T212_Id=T212_Id 
				WHERE T147_T005_Id='{$r_acaoId}'
EOT;
	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T147_Id-/, '');
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
	case 'ven002grid04':
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
		array('colunaValor'=> 'T149_T147_Id', 'colunaTitulo'=>'IdUnidade', 'colunaExibir' => false),
		);

		$from = "T149";

		$extra = <<<EOT
				left join D009 on D009_Id=T149_D009_Id
				LEFT JOIN D049 on D049_Id=D009_D049_Id
				LEFT JOIN D001 on D001_Id=D049_D001_Id
				LEFT JOIN D082 on D082_Id=D049_D082_Id					
	    		left join D037 on D037_Id=T149_D037_Id
	    		left join T147 on T147_Id=T149_T147_Id
	    		where
	    		T149_T147_Id='$r_T147_Id'
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
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/ven/ven009/content/ven009content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}' + '&D106_Id=' + encodeURIComponent({$r_acaoId}), [500,390]);
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
			//$botoesDir = '<button type="button" id="ven002IncluirLogistica">Incluir</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');

			// AÃ§Ãµes dos botÃµes
			$novoIdTelaForm = uniqid();
			/**
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#ven002IncluirLogistica').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Materiais', '/ven/ven009/content/ven009content02/',  '&D106_Id=' + encodeURIComponent({$r_acaoId}), [500,390]);
			});
			} </script></div>
			EOT;
			*/
		break;

		/**
			* Grid Processos
			*/
	case 'ven002grid05':

		/**
			* Definição do Grid (Tabela)
			*/
		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T148_Id', $g['divId'])),
		array('colunaValor' => 'T148_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
		array('colunaValor' => 'T148_T147_Id', 'colunaTitulo' => 'IdServico', 'colunaExibir' => false),
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
				left join T147 on T147_Id=T148_T147_Id
				left join D110 on D110_Id=T148_D110_Id
				where 
				T148_T147_Id = {$r_acaoId}
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
			var id = $(this).closest('tr').attr('id').replace(/T147_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/ven/ven010/content/ven010content03/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,260]);
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
		
		case 'cad061GridPagamento':
			global $g;
			$sqlD157 = "SELECT D157_Pgto_Data, D157_Pgto_C007_Id, C007_Login FROM D157 LEFT JOIN C007 ON C007_Id=D157_Pgto_C007_Id WHERE D157_Id='$r_acaoId'";
			$resultD157 = mysql_query($sqlD157);
			$linhaD157 = mysql_fetch_array($resultD157);
			$dataGerada = gCorrigeData($linhaD157['D157_Pgto_Data']);
			$user = strtoupper($linhaD157['C007_Login']);
			if(($user != "") || (!empty($user))){
				echo "Parcelas Geradas por <b>".$user."</b> em <b>".$dataGerada."</b>";
			}
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'D159_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'D159_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'D159_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_D159_Prazos_Dias"),
				array('colunaValor' => 'D159_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_D159_Prazos_Dias"),
				array('colunaValor' => 'ven002_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "ven002_Dia_Semana"),
				array('colunaValor' => 'D159_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_D159_Prazos_Dias"),
				array('colunaValor' => 'D159_Taxa_Juros', 'colunaTitulo' => '%Juros', 'style'=>'width:1px'),
				//array('colunaValor' => 'D159_Valor_Juros', 'colunaTitulo' => 'Juros'),
				array('colunaValor' => 'D159_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true,'colunaEditarJSCallback' => "colunaEditarCallbackJS_D159_Prazos_Dias", 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'order by D027_Portador', true), 'callback' => 'D159_D027_Id_Portador'),
				array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'ven002_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "ven002_Detalhes"),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'D159';

			$extra = <<<EOT
				left join D027 on D027_Id=D159_D027_Id
				WHERE D159_D157_Id = {$r_acaoId}
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
					var id = $(this).closest('tr').attr('id').replace(/D159_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_D159_Prazos_Dias(D159_Id, campoEditado, valorAntigo, novoValor) {
					D159_Id = D159_Id.replace(/D159_Id-/, '');
					if (campoEditado == 'D159_Prazos_Dias') {
						$.getJSON('/ven/ven002/grid_func-ajax/atualizarParcelas/?ajax=true&D159_Id='      		+ encodeURIComponent(D159_Id) + 
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
					} else if (campoEditado == 'D159_Data_Vencimento') {
						$.getJSON('/ven/ven002/grid_func-ajax/atualizarParcelas/?ajax=true&D159_Id='      		+ encodeURIComponent(D159_Id) + 
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
					} else if (campoEditado == 'D159_Valor_Parcela') {
						$.getJSON('/ven/ven002/grid_func-ajax/atualizarParcelas/?ajax=true&D159_Id='      		+ encodeURIComponent(D159_Id) + 
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
					} else if (campoEditado == 'D159_D027_Id') {
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
			
		case 'ven002GridEmail':
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
			where T139_T003_Id='{$r_acaoId}'
EOT;

/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc006EnviarEmail2">Enviar Email</button>
				<button type="button" id="etc006VerificarEmailsEnviadosVen001">Verificar Novos Emails</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc006EnviarEmail2').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T005_Id=' + encodeURIComponent('$r_acaoId'), [900,550]);
				});
				$('#etc006VerificarEmailsEnviadosVen001').unbind('click').bind('click', function(data) {
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
		
		case 'ven002GridDocumentos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T167_Id', $g['divId'])),
			array('colunaValor' => 'T167_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T167_T005_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
			array('colunaValor' => 'T167_Nome_Arquivo', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'T167_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T167_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T167';

			$extra = <<<EOT
				left join C007 on C007_Id=T167_C007_Id
				where 
				(T167_T005_Id = {$r_acaoId})
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
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T167_Id-/, '');
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

			$novoIdTelaForm = uniqid();
		break;
		
		case 'ven002GridEntregaServicos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T171_Id', $g['divId'])),
			array('colunaValor' => 'T171_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T171_D116_Id', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
			array('colunaValor' => 'D116_Id', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculo'),
			array('colunaValor' => 'T171_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir'=>false),
			array('colunaValor' => 'T171_T147_Id', 'colunaTitulo' => 'Serviço', 'colunaExibir'=>false),
			array('colunaValor' => 'T171_Data_combinada', 'colunaTitulo' => 'Data Combinada', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
			array('colunaValor' => 'T171_Hora_combinada', 'colunaTitulo' => 'Hora Combinada', 'callback'=>'gCorrigeTempo', 'colunaEditar'=>true),
			array('colunaValor' => 'T171_Observação', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T171';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		left join D116 on D116_Id=T171_D116_Id
		WHERE T171_T005_Id={$r_acaoId}
		
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
	
	case 'ven002GridHistorico':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T178_Id', $g['divId'])),
			array('colunaValor' => 'T178_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T178_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir'=>false),
			array('colunaValor' => 'T178_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData','style' => 'width:105px;'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T178_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T178_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T178';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		left join C007 on C007_Id=T178_C007_Id
		WHERE T178_T005_Id={$r_acaoId}
		order by T178_Id,T178_Data desc
		
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
	
	case 'ven002gridTrocas':
		
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T138_Id', $g['divId'])),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:normal'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T138_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'style' => 'font-weight:normal',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'T138_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'font-weight:normal',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_Grid_Refresh"),
			array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback'=>'calculaPrecoTotalTroca'),
			//array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'callback'=>'retornaOperacaoTroca'),						
			array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'colunaEditar' => true,'colunaEditarGeraSelect' => array(array('title' => 'ENTRADAS', 'value' => 'P'),array('title' => 'DEVOLUÇÕES', 'value' => 'I')), 'callback' => 'retornaOperacaoTroca'),
			array('colunaValor' => 'T138_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar'=>true),
		);
		
		
								

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T138';

		$extra = <<<EOT
            LEFT JOIN D009 on D009_Id=T138_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
			WHERE T138_T005_Id = '{$r_acaoId}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		/*
		function gridLinha($array) {
			if($array['todosCampos']['T006_T005_Id']>0){
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
    
    case 'ven002GridProdutosPedido':
        $acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		$grid = array(
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'ID Item', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Cliente', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cliente', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição produto', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '0', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '0'),
            array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T006_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'concat(D116_Placa," ",D116_Modelo," ",D116_Cor) as Veiculo', 'colunaTitulo' => 'Veículo', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1)', 'colunaTitulo' => 'Status'),
        );
        
        /**
         * Definição da SQL
         */
        $from = "T006";
        
        $extra = <<<EOT
                    left join D009 on D009_Id=T006_D009_Id
                    left join D049 on D049_Id=D009_D049_Id
                    left join D001 on D001_Id=D049_D001_Id
                    left join D082 on D082_Id=D049_D082_Id
                    left join D006 on D006_Id=T006_D006_Id
                    left join D037 on D037_Id=T006_D037_Id
                    left join T005 on T005_Id=T006_T005_Id
                    left join T003 on T003_Id=T005_T003_Id
                    left join D024 on D024_Id=T005_D024_Id
                    left join D018 on D018_Id=D024_D018_Id
                    left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
                    left join D021 on D021_Id=D024_D021_Id
                    left join C004 on C004_Id=T005_C004_Id
                    left join D116 on D116_Id=T005_D116_Id
EOT;
        /*
        * Pode ser definido uma classe para cada linha do grid
        
        function gridLinha($array) {
            if($array['todosCampos']['T005_Flag_Cancelada']=='S'){
                return 'linhaVermelho';
            }
        }
        */
        /**
        * Filtro
        */
		$gridFiltro = array(
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
              ), 'naoGerarExtra' => ''),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'T005_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
            'T006_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'T006_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'T005_Status_Pedido(T005_Flag_Status,1)' => array('titulo' => 'Status Pedido', 'tipo' => 'texto'),
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
        $valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T006_Quantidade) Total", $from, $extra));
        $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T006_Valor_Total_Preco_Sem_Desconto)", $from, $extra));
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Quantidade total<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor produtos total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;

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
						var D001_Id = id[1].replace(/D001_Id-/, '');
  					    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id), [1000,600]);
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
    
    case 'ven002GridServicosPedido':
        /**
        * Definição do Grid (Tabela)
        */
		$grid = array(
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'OS', 'chavePrimaria' => true),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'concat(T147_Titulo, \' \', T147_Descricao) as Servico', 'colunaTitulo' => 'Serviço'),
            array('colunaValor' => 'ven002_Veiculo', 'colunaTitulo' => 'Veículo', 'colunaTipo' => 'livre', 'callback' => 'buscaVeiculo', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T147_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaExibir' => false),
            array('colunaValor' => 'T147_T005_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
            array('colunaValor' => 'T147_T145_Id', 'colunaTitulo' => 'IdOrçameto', 'colunaExibir' => false),
            array('colunaValor' => 'T147_D116_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado'),
            array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status', 'callback' => 'validaCampoStatus'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
        );
    
        /**
            *
            * Definição da SQL
            */
        $from = 'T147';
    
        $extra = <<<EOT
                LEFT JOIN T005 ON T005_Id=T147_T005_Id
                left join D006 on D006_Id=T005_D006_Id
                left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
                LEFT JOIN D024 ON D024_Id=T005_D024_Id
                left join D018 on D018_Id=D024_D018_Id
                LEFT JOIN T006 ON T006_Id=T147_T006_Id
                LEFT JOIN D009 ON D009_Id=T006_D009_Id
                LEFT JOIN D049 ON D009_D049_Id=D049_Id
                LEFT JOIN D001 ON D001_Id=D049_D001_Id
EOT;
    

        /*
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        
        $novoIdTelaForm = uniqid();
        echo <<<EOT
         <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').replace(/T147_Id-/, '');
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
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
              ), 'naoGerarExtra' => ''),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'T147_Id' => array('titulo' => 'OS', 'tipo' => 'texto'),
            'T147_Titulo,T147_Descricao' => array('titulo' => 'Serviço', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'numero'),
            'T147_Status' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Pendente', 'value' => 'P'),
                array('title' => 'Execução', 'value' => 'E'),
                array('title' => 'Concluído', 'value' => 'C'),
                array('title' => 'Cancelado', 'value' => ' "" '),
              ), 'naoGerarExtra' => ''),
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
        $valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T147_Quantidade) Total", $from, $extra));
        $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T147_Valor_Unitario)", $from, $extra));
        
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
        
    case 'ven002GridProdutosTrocaPedido':
        
        $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T138_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T138_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'T138_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal'),
            array('colunaValor' => 'Valor_Total_Preco_Trocas', 'colunaTitulo' => 'Preço Total', 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback' => 'calculaPrecoTotalTroca'),
            array('colunaValor' => 'T138_Operacao', 'colunaTitulo' => 'Operação', 'callback' => 'retornaOperacaoTroca'),
            array('colunaValor' => 'T138_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
        );
                                

        /**
         *
         * Definição da SQL
         */
        $from = 'T138';

        $extra = <<<EOT
            LEFT JOIN T005 ON T005_Id=T138_T005_Id
            left join D006 on D006_Id=T005_D006_Id
            left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
            LEFT JOIN D024 ON D024_Id=T005_D024_Id
            LEFT JOIN D018 ON D018_Id=D024_D018_Id
            LEFT JOIN D009 on D009_Id=T138_D009_Id
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
EOT;

        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        /*
        function gridLinha($array) {
            if($array['todosCampos']['T006_T005_Id']>0){
                return 'linhaCinza';
            }
        }
        */

        /*
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
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            'D006_Descricao, D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Entrada', 'value' => 'E'),
                array('title' => 'Saída', 'value' => 'S'),
              ),
              'naoGerarExtra' => ''),
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
        $valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T138_Quantidade) Total", $from, $extra));
        $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T138_Valor_Preco_Unitario)", $from, $extra));
        
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
    
    
    case 'ven002GridAgregados':
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
                T204_T006_Id = {$r_acaoId}
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
        
        case 'ven002GridPedidoProdutosDefeito':            
            /**
            * Definição do Grid (Tabela)
            */
            $grid = array(	            
	            array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Pedido', 'style' => "width: 1%;"),
	            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => "width: 1%;"),
	            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
	            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente', 'colunaExibir' => false),
	            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Quantidade', 'style' => "width: 1%;", 'colunaCasasDecimais' => '2'),
            );

            /**
            *
            * Definição da SQL
            */
            $from = 'T006';

            $extra = <<<EOT
            	left join T005 on T005_Id = T006_T005_Id
            	left join D024 on D024_Id = T005_D024_Id
                where T006_T206_Id = {$r_acaoId}
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

    case 'ven002GridCompras':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
	        array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'T006_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	        array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'T006_T005_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'T006_D009_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'Req', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
	        array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;font-size:11px'),
	        array('colunaValor' => 'T006_Descricao_Produto', 'colunaExibir' => false),
	        array('colunaValor' => 'descricao', 'colunaTitulo' => 'Descrição', 'colunaTipo' => 'livre', 'callback' => 'validaCampoCotacaoPedido'),
	        array('colunaValor' => 'substr(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%;font-size:11px'),
	        array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	        array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'font-weight:bold;background: var(--celula-verde);;width:1%', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Percentual_Desconto'),
	        array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Entreg<br>dias', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;width:1%'),
	        array('colunaValor' => 'IF ((T006_Quantidade - IF(T006_Quantidade_Estoque<0,0,T006_Quantidade_Estoque)) <= 0, 0.00, T006_Quantidade - IF(T006_Quantidade_Estoque<0,0,T006_Quantidade_Estoque))', 'colunaTitulo' => 'Requisitar', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'colunaCasasDecimais' => '2'),
	        array('colunaValor' => 'campoCotar', 'callback' => 'validaCampoCotarPedido', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Cotar', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        array('colunaValor' => 'campoComprar', 'callback' => 'validaCampoComprar', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Comprar', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        array('colunaValor' => 'T006_Flag_Cotar', 'callback' => 'checkboxCotarPedido', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaExibir' => false),
	        array('colunaValor' => 'T006_Flag_Comprar', 'callback' => 'checkboxComprar', 'colunaTitulo' => 'Comprar', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaExibir' => false),
	        array('colunaValor' => 'T189_Flag_Status', 'colunaExibir' => false),
	        array('colunaValor' => 'T219_Valor_Preco_Venda', 'colunaTitulo' => 'Preço<br>cotação', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => 'background-color: var(--celula-azul);width:1%'),
	        array('colunaValor' => 'T219_Valor_Preco_Venda_Negociado-T219_Valor_Preco_Venda', 'colunaTitulo' => 'Sobra', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);width:1%'),
	        array('colunaValor' => 'T219_Observacao_Entrega', 'colunaTitulo' => 'Entrg<br>dias', 'colunaAlinhar' => 'center', 'style' => 'background-color: var(--celula-azul);width:1%'),
	        array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'colunaEditar' => true),
	        array('colunaValor' => 'T219_T218_Id', 'colunaTitulo' => 'Cot', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	        array('colunaValor' => 'substr(C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'width:1%;font-size:11px'),
	    );

		/**
		* Definição da SQL
		*/
		$from = 'T006';

		$extra = <<<EOT
			LEFT JOIN T189 ON T189_T006_Id = T006_Id
			LEFT JOIN D009 ON D009_Id = T006_D009_Id
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id
			LEFT JOIN D082 ON D082_Id = D049_D082_Id
			LEFT JOIN T004 ON T004_Id = T006_T004_Id 
			LEFT JOIN T219 ON T219_Id = T006_T219_Id OR T219_Id = T004_T219_Id 
			LEFT JOIN T225 ON T225_T219_Id=T219_Id
			LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprador
			WHERE T006_T005_Id = '{$r_acaoId}'
EOT;

		/*
		* Botões (Versão Acima do GRID)
		*
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven002CotarProdutos">Cotar Produtos Selecionados</button>
				<button type="button" id="ven002ComprarProdutos">Comprar Produtos Selecionados</button>
				<button type="button" id="ven002ModificarProdutos">Gerar Serviço Produtos</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven002CotarProdutos').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven002/grid_func-ajax/cotarProdutosSelecionados/?ajax=true&T005_Id='+ encodeURIComponent('{$r_acaoId}') +'&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if(request.code){
							divRefresh('{$g['divId']}', true);
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				});

				$('#ven002ComprarProdutos').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven002/grid_func-ajax/comprarProdutosSelecionados/?ajax=true&T005_Id='+ encodeURIComponent('{$r_acaoId}') +'&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if(request.code){
							divRefresh('{$g['divId']}', true);
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				});

				$('#ven002ModificarProdutos').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven002/grid_func-ajax/ven002modificarProdutosSelecionados/?ajax=true&T005_Id='+ encodeURIComponent('{$r_acaoId}') +'&callback=?', function(request) {
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
								$.getJSON('/ven/ven002/grid_func-ajax/ven002MarcarTodosCotar/?ajax=true&T005_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Desmarcar todos": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven002/grid_func-ajax/ven002DesmarcarTodosCotar/?ajax=true&T005_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
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
								$.getJSON('/ven/ven002/grid_func-ajax/ven002MarcarTodosComprar/?ajax=true&T005_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Desmarcar todos": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven002/grid_func-ajax/ven002DesmarcarTodosComprar/?ajax=true&T005_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
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
*/
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T219_Valor_Preco_Venda']>0){
				return 'color:blue;font-weight:bold;font-size:13px';
			}else if($array['todosCampos']['D009_Quantidade_Estoque_Liquido']<$array['todosCampos']['T006_Quantidade']){
				//return 'linhaVermelho';
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

    case 'ven002GridMontagemIPV':

		$sqlT005 = mysql_query("SELECT T005_Valor_Total_Produtos, T005_Flag_Lista_Preco, T005_Valor_Total_ICMS, T005_Valor_Total_IRPJ, T005_Valor_Total_CSLL, T005_Valor_Total_PIS, T005_Valor_Total_COFINS, T005_Valor_Total_Produtos FROM T005 WHERE T005_Id = '{$r_acaoId}'");
		$mT005 = mysql_fetch_array($sqlT005);

		$impostos = 0;
		$impostos += ($g['C031']['somarICMSPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_ICMS'] : 0;
        $impostos += ($g['C031']['somarPISPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_PIS'] : 0;
        $impostos += ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_COFINS'] : 0;
        $impostos += ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_IRPJ'] : 0;
        $impostos += ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_CSLL'] : 0;

        $IPV = mysql_query("select sum(T251_Aliquota) from T251 where T251_T005_Id='{$r_acaoId}' and T251_Grupo='IPV'");
        $mIPV = mysql_fetch_array($IPV);
        $IPV = $mIPV[0]/100;

        $T005_Valor_Total_Produtos = $mT005['T005_Valor_Total_Produtos'];
        if ($mT005['T005_Flag_Lista_Preco']=='2'){
			$T005_Valor_Total_Produtos = $T005_Valor_Total_Produtos * 2;
		}

		$sqlT006 = mysql_query("SELECT SUM(D009_Valor_Custo_Compra*T006_Quantidade) custo from T006 left join D009 on D009_Id=T006_D009_Id where T006_T005_Id='{$r_acaoId}'");
		$mT006 = mysql_fetch_array($sqlT006);
		$custo = $mT006['custo'];

    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T251_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
 			array('colunaValor' => 'T251_Descricao', 'colunaTitulo' => 'Descrição'),
 			array('colunaValor' => 'T251_Aliquota', 'colunaTitulo' => 'Alíquota %', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'atualizarTotaisIPV', 'colunaTotal' => true, 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold;background: var(--celula-verde);;width: 15%'),
			array('colunaValor' => "(if(T251_Grupo='IPV',if(T251_Tipo='4',$T005_Valor_Total_Produtos/2,$T005_Valor_Total_Produtos),$custo)*(T251_Aliquota/100))", 'colunaTitulo' => 'Valor R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width: 15%', 'colunaTotal' => true),
 			array('colunaValor' => 'T251_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
        );		

		/*
		* Definição da SQL
		*/
		$from = 'T251';
		
		$extra = <<<EOT
			LEFT JOIN T005 ON T005_Id=T251_T005_Id
			WHERE T251_T005_Id = '{$r_acaoId}'
EOT;
		$extraSemLucro = <<<EOT
			LEFT JOIN T005 ON T005_Id=T251_T005_Id
			WHERE T251_T005_Id = '{$r_acaoId}' and ifnull(T251_Tipo,0)!='3' and IFNULL(T251_Tipo,0)!='4'
EOT;
		$extraICF = <<<EOT
			LEFT JOIN T005 ON T005_Id=T251_T005_Id
			WHERE T251_T005_Id = '{$r_acaoId}' and T251_Grupo='ICF'
EOT;
		$extraIPV = <<<EOT
			LEFT JOIN T005 ON T005_Id=T251_T005_Id
			WHERE T251_T005_Id = '{$r_acaoId}' and T251_Grupo='IPV' and ifnull(T251_Tipo,'')!=4
EOT;
		$extraTributos = <<<EOT
			LEFT JOIN T005 ON T005_Id=T251_T005_Id
			WHERE T251_T005_Id = '{$r_acaoId}' and T251_Grupo='IPV' and T251_Tipo='4'
EOT;


		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => 
				array(
	            	array('title' => 'Grupo', 'campoAgrupar' => 'T251_Grupo', 'campoMostrar' => 'T251_Grupo', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
	            ),
	        ),
        );

		// Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
        * Totais
        */
        $tributos    = $g['sqlAuto']->pegarSqlCampo("{$impostos}", $from, $extra);
        $ipvSemLucro = $g['sqlAuto']->pegarSqlCampo("SUM((if(T251_Grupo='IPV',$T005_Valor_Total_Produtos,$custo)*(T251_Aliquota/100)))", $from, $extraSemLucro);
		//$valorMinimo = $g['sqlAuto']->pegarSqlCampo("sum((if(T251_Grupo='IPV',if(T251_Tipo='4',$T005_Valor_Total_Produtos/2,$T005_Valor_Total_Produtos),$custo)*(T251_Aliquota/100)))", $from, $extra );
        $valorMinimo = $g['sqlAuto']->pegarSqlCampo("sum($custo*(T251_Aliquota/100))", $from, $extraICF );
        $IPV         = $g['sqlAuto']->pegarSqlCampo("sum(T251_Aliquota)", $from, $extraIPV );
        $IPVTributos = $g['sqlAuto']->pegarSqlCampo("sum(T251_Aliquota)", $from, $extraTributos );

		if ($IPVTributos<=0){
			$IPVTributos = ($tributos / $mT005['T005_Valor_Total_Produtos']) * 100;
		}

		$IPV         = number_format( 100 / ( 100 - $IPV ) , 4 );
		$IPVTributos = number_format( 100 / ( 100 - $IPVTributos ) , 4 );

		$valorMinimo = $valorMinimo + $custo;

		$valorMinimo2 = $valorMinimo;
        if ($mT005['T005_Flag_Lista_Preco']=='2'){
			$valorMinimo2 = $valorMinimo / 2;
		}

		$valorMinimo = ( $valorMinimo * $IPV );

		if ($IPVTributos > 0){
			$valorMinimo += ( ( $valorMinimo2 * $IPVTributos ) - $valorMinimo2 );
		}

        $valorLucro 	 = $T005_Valor_Total_Produtos - $custo - $ipvSemLucro - $tributos;
		$tributos        = gCorrigeNumero($tributos);
        $ipvSemLucro     = gCorrigeNumero($ipvSemLucro);
		$percentualLucro = gCorrigeNumero(($valorLucro/$custo)*100);
        $custo           = gCorrigeNumero($custo);
        $valorLucro 	 = gCorrigeNumero($valorLucro);
		$T005_Valor_Total_Produtos = gCorrigeNumero($T005_Valor_Total_Produtos);
		$valorMinimo     = gCorrigeNumero($valorMinimo);
		
		echo <<<EOT
            <div class="buttonsBottomTop">
            	<button type="button" id="ven002AtualizarMontagemIPV" style="float: left; margin: 5px 10px 0 0;">Atualizar</button>
				<table style="font-size:14px;border:1px solid silver" width='185px' cellpadding='2px'>
				  <tr>
				    <td>Custo R$</td><td align=right>{$custo}</td>
				  </tr>
				  <tr>
				    <td>Tributos R$</td><td align=right>{$tributos}</td>
				  </tr>
				  <tr>
                    <td>ICF + IPV R$</td><td align=right>{$ipvSemLucro}</td>
                  </tr>
				  <tr>
				    <td>Lucro R$</td><td align=right>{$valorLucro}</td>
				  </tr>
                  <tr>
				    <td>Lucro %</td><td align=right>{$percentualLucro}</td>
				  </tr>
				  <tr>
				    <td>Total R$</td><td align=right><b>{$T005_Valor_Total_Produtos}</b></td>
				  <tr>
				    <td>Mínimo R$</td><td align=right>{$valorMinimo}</td>
				  </tr>
				 </table>
            </div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				function atualizarTotaisIPV(T251_Id, campoEditado, valorAntigo, novoValor) {
					divRefresh('{$g['divId']}');
				}

				$('#ven002AtualizarMontagemIPV').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/grid_func-ajax/atualizarMontagemIPV/?ajax=true&T005_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}');
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atualizar montagem do IPV', '<span style="color: red"><center>ATENÇÃO</center><br>Todos as varíaveis do IPV serão removidas e atualizadas conforme a configuração padrão.</span><br><br>Deseja atualizar as varíaveis do IPV?', \$buttons);
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

    case 'ven002GridComposicaoProdutoProduzido':
    	/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T261_Id', $g['divId'])),
			array('colunaValor' => 'T261_Id', 'colunaTitulo' => 'T261_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T261_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;', 'colunaCasasDecimais' => '5'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T261';
		$extra = <<<EOT
			LEFT JOIN D001 ON D001_Id = T261_D001_Id_Composicao 
		    LEFT JOIN D049 ON D001_Id = D049_D001_Id
		    LEFT JOIN D037 ON D037_Id = D001_D037_Id
			LEFT JOIN D037 ON D037_Id = D001_D037_Id
			WHERE T261_T006_Id = '{$r_acaoId}'
			AND D009_C004_Id='{$g['empresaAtual']}'
EOT;

		$count = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		/**
		* Botões (Versão Acima do GRID)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Itens<br><input type="text" value="{$count}" class="whiteBg" id="" size="4" readonly></span>
				<button style="margin-top: 3px;" id="incluirProdutoComposicaoOP">Incluir</button>
				<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#incluirProdutoComposicaoOP').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Itens Composição', '/est/est017/content/est017ContentProdutoComposicao/', '&T256_Id={$r_T256_Id}', [360,110]);
				});
			} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(data.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T261_Id = id[0].replace(/T261_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Itens Composição', '/est/est017/content/est017ContentProdutoComposicao/', '&acaoId=' + encodeURIComponent(T261_Id), [360,110]);
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
		$g['smarty']->display('grid.tpl');
    break;

    case 'ven002GridProcessosProdutoProduzido':
    	/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T262_Id', $g['divId'])),
			array('colunaValor' => 'T262_Id', 'colunaTitulo' => 'id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'T262_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
			array('colunaValor' => 'D079_Nome', 'colunaTitulo' => 'Máquina', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D079_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D078_Nome', 'colunaTitulo' => 'Função', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D078_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T262_Descricao', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T262_Lead_Time', 'colunaTitulo' => 'Lead Time', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais' => '0'),
			array('colunaValor' => 'T262_Numero_Funcionarios', 'colunaTitulo' => 'Funcionários', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'materialConsumoOP', 'colunaTipo' => "livre", 'callback' => 'materialConsumoOP', 'style' => 'width: 1px;'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T262';
		$extra = <<<EOT
			LEFT JOIN D078 on D078_Id = T262_D078_Id
			LEFT JOIN D079 on D079_Id = T262_D079_Id
			WHERE T262_T006_Id = '{$r_acaoId}'
			ORDER BY T262_Ordem
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		$count = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Processos<br><input type="text" value="{$count}" class="whiteBg" id="" size="4" readonly></span>
				<button type="button" style="margin-top: 3px;" id="incluirProcessoProdutoOP">Incluir</button>
				<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#incluirProcessoProdutoOP').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processo', '/est/est017/content/est017ContentProcessos/', '&T256_Id=' + encodeURIComponent('{$r_T256_Id}'), [355,250]);
				});
			} </script></div>
EOT;
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(data.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T262_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Processo', '/est/est017/content/est017ContentProcessos/', '&acaoId=' + encodeURIComponent(id), [355,250]);
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
		$g['smarty']->display('grid.tpl');
    break;
}
