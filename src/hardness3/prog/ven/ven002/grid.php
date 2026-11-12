<?php
namespace hardness;
// Classes
//require_once('bibliotecas/classes/VEN002.php');
//$VEN002 = new VEN002();
// Carrega funções callback
require_once('prog/ven/ven002/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/ven/ven002/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * AÃ§Ãµes
 */
switch($g['r'][3]){
    case 'ven002GridNFDebitoCredito':
        $T005_IdNFDC = isset($_REQUEST['T005_Id']) ? (int) $_REQUEST['T005_Id'] : 0;
        $pedidoNFDC = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id='{$T005_IdNFDC}' AND T005_C004_Id='{$g['empresaAtual']}'");
        if (!$pedidoNFDC || mysql_num_rows($pedidoNFDC) !== 1) {
            echo gGeraAlertMsg('NF de Débito', 'Pedido não encontrado para a empresa atual.');
            break;
        }
        $casasQuantidadeNFDC = isset($g['c020']['C020_Casas_Decimais_Quantidade']) ? (int) $g['c020']['C020_Casas_Decimais_Quantidade'] : 2;
        $casasQuantidadeNFDC = max(0, min(6, $casasQuantidadeNFDC));
        $quantidadeGeradaNFDC = "IFNULL((SELECT SUM(T008_Quantidade)
            FROM T008 INNER JOIN T007 ON T007_Id=T008_T007_Id
            INNER JOIN T007A ON T007A_T007_Id=T007_Id
            WHERE T008_T006_Id=T006.T006_Id
              AND T007_Flag_Finalidade_NFe='6' AND T007A_Tipo_NF_Debito='06'
              AND IFNULL(T007_Flag_Cancelada,'N')!='S'
              AND IFNULL(T007_Flag_Inutilizado,'N')!='S'), 0)";
        $grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'T006_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtd. pedido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $casasQuantidadeNFDC),
            array('colunaValor' => $quantidadeGeradaNFDC . ' AS Quantidade_Debito', 'colunaTitulo' => 'Qtd. débito já gerada', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $casasQuantidadeNFDC),
            array('colunaValor' => 'NFDC_Nova_Quantidade', 'colunaTitulo' => 'Nova quantidade', 'colunaTipo' => 'livre', 'callback' => 'ven002NFDCQuantidadePedido', 'colunaAlinhar' => 'right', 'style' => 'width:1%')
        );
        $from = 'T006';
        // O SQL original fica disponível na aba Extra, incluindo o filtro do pedido.
        $extra = " WHERE T006_T005_Id='{$T005_IdNFDC}' ORDER BY T006_Id";
        $extra = $g['ui']->gridFiltroExtra(true, array(), $extra);
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('gridOptNoShow', false);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        if (isset($r_generateHTML)) {
            ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl');
        } else {
            $g['smarty']->display('grid.tpl');
            echo '<div class="divHidden"><script type="text/javascript">$("#' . $g['divId'] . '").trigger("nfdcGridCarregado");</script></div>';
        }
    break;


	/**
	* Grid Principal
	* Chamado em: ven002tab01
	*/
	case 'ven002grid01':
		// Mantém no grid personalizado as definições atuais das colunas do PHP.
		$r_personalizacaoModoAntigo = false;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'campoExclusao', 'colunaTipo' => 'livre', 'callback' => 'campoExclusao', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'campoCancelamento', 'colunaTipo' => 'livre', 'callback' => 'campoCancelamento', 'style' => 'width:1%;'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'Cód. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'style' => 'font-weight:normal', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total_Servicos', 'colunaTitulo' => 'Servicos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Troca', 'colunaTitulo' => 'Trocas', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0,"MP","PE"))) FROM T006 WHERE T006_T005_Id=T005_Id) as Estoque', 'colunaTitulo' => 'Estoque', 'style' => 'width:1%;'),
            array('colunaValor' => 'T005_Flag_Status_Financeiro', 'colunaTitulo' => 'Status Financeiro', 'callback' => 'statusFinanceiro', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as Status', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
	            // NF débito/crédito no Status foi desativada: a subquery correlacionada era executada no carregamento do grid.
	            // Commit 0e0b6fc7: array('colunaValor' => 'group_concat("<span style=\\"\\">", IF(IFNULL(T005_Flag_Status,0)=0,COALESCE((SELECT IF(NFDC.T007_Flag_Finalidade_NFe=5,CONVERT("NF CRÉDITO GERADA" USING latin1),CONVERT("NF DÉBITO GERADA" USING latin1)) FROM T007 NFDC WHERE NFDC.T007_Flag_Finalidade_NFe IN (5,6) AND IFNULL(NFDC.T007_Flag_Cancelada,"N")!="S" AND IFNULL(NFDC.T007_Flag_Inutilizado,"N")!="S" AND EXISTS (SELECT 1 FROM T008 INFD JOIN T006 PNFD ON PNFD.T006_Id=INFD.T008_T006_Id WHERE INFD.T008_T007_Id=NFDC.T007_Id AND PNFD.T006_T005_Id=T005.T005_Id) ORDER BY NFDC.T007_Id DESC LIMIT 1),T005_Status_Pedido(T005_Flag_Status,1)),T005_Status_Pedido(T005_Flag_Status,1)), "</span> ", ifnull((SELECT date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1), ""), "<br />", if(T005_Flag_Status=9, T005_Motivo_Nao_Liberado,"")) as Status2', 'colunaTitulo' => 'Status', 'style' => 'width:1%'),
	            array('colunaValor' => 'group_concat("<span style=\\"\\">", T005_Status_Pedido(T005_Flag_Status,1), "</span> ", ifnull((SELECT date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1), ""), "<br />", if(T005_Flag_Status=9, T005_Motivo_Nao_Liberado,"")) as Status2', 'colunaTitulo' => 'Status', 'style' => 'width:1%'),
	            // NF débito/crédito no grid foi desativada: a subquery e o callback criavam consultas repetidas por pedido.
	            // Commit 0e0b6fc7: array('colunaValor' => 'CONCAT_WS(", ",NULLIF(group_concat(distinct if(T007_Numero_Nota_Fiscal="","Finalizar",T007_Numero_Nota_Fiscal) separator ", "),""),(SELECT GROUP_CONCAT(IF(IFNULL(NFDC.T007_Numero_Nota_Fiscal,"")="",CONCAT("ID ",NFDC.T007_Id),NFDC.T007_Numero_Nota_Fiscal) ORDER BY NFDC.T007_Id SEPARATOR ", ") FROM T007 NFDC WHERE NFDC.T007_Flag_Finalidade_NFe IN (5,6) AND IFNULL(NFDC.T007_Flag_Cancelada,"N")!="S" AND IFNULL(NFDC.T007_Flag_Inutilizado,"N")!="S" AND EXISTS (SELECT 1 FROM T008 INFD JOIN T006 PNFD ON PNFD.T006_Id=INFD.T008_T006_Id WHERE INFD.T008_T007_Id=NFDC.T007_Id AND PNFD.T006_T005_Id=T005.T005_Id))) as NF', 'colunaTitulo' => 'NF', 'callback' => 'ven002CampoNFPedido'),
	            array('colunaValor' => 'group_concat(distinct if(T007_Numero_Nota_Fiscal="","Finalizar",T007_Numero_Nota_Fiscal) separator ", ") as NF', 'colunaTitulo' => 'NF', 'callback' => 'validaCampoNFPedidos'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaExibir' => 'N'),
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
			left join T007 on T007_T005_Id=T005_Id and IFNULL(T007_Flag_Finalidade_NFe,'1')='1' and T007_Flag_Cancelada!='S' and T007_Flag_Inutilizado!='S' and IFNULL(T007_Protocolo_Inutilizacao,'')=''
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
			left join T007 on T007_T005_Id=T005_Id and IFNULL(T007_Flag_Finalidade_NFe,'1')='1' and T007_Flag_Cancelada!='S' and T007_Flag_Inutilizado!='S' and IFNULL(T007_Protocolo_Inutilizacao,'')=''
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
            'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
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
	case 'ven002grid02':

		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'V');

		$sql = "SELECT T005_Casas_Decimais_Preco,
					   T005A_Flag_Conversao_Unidade
				  FROM T005 
			 LEFT JOIN T005A ON T005_Id = T005A_T005_Id
				 WHERE T005_Id = '{$r_T005_Id}'";
		$resT005 = mysql_query($sql);
		$rowT005 = mysql_fetch_assoc($resT005);

		// Valida colunas exibir ao usar preço bruto
		$colunaPrecoLiquido       = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPrecoTabela        = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPercentualDesconto = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPrecoTotal         = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? false : true;
		$colunaPrecoBruto         = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? true : false;
		// Fim preço Bruto

		// Valida colunas exibir para conversão de unidade
		$colunaPrecoComercial                = false;
		$colunaUnidadeComercial              = false;
		$colunaQuantidadeComercial 	         = false;	
		$colunaQuantidade                    = true;
		// Só valida conversão se for diferente de preço bruto, primeira versão da conversão de unidade trata apenas para liquido (Felipe 26/06/2023)
		if($g['C031']['precopadraoVendaProduto'] != 'bruto' && $rowT005['T005A_Flag_Conversao_Unidade'] == 'S'){
			// Colunas T004A que são utilizadas na conversão
			$colunaPrecoComercial                = true ;
			$colunaUnidadeComercial              = true ;
			$colunaQuantidadeComercial 	         = true;
			//Colunas T004 que são usado no padrão para calculos
			$colunaQuantidade                    = false;
			$colunaPrecoLiquido                  = false;
		}

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirProduto'),
            array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Item'),
            array('colunaValor' => 'T006_D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T005_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Codigo_Local', 'colunaTitulo' => 'Ambiente', 'colunaAlinhar' => 'left', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item'),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto', 'callback' => 'campoDescricaoItemPedido', 'colunaExibir' => false),
            array('colunaValor' => 'SUBSTRING(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%'),
			array('colunaValor' => 'unPadrao.D037_Id', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'unPadrao.D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'font-size:12px'),
			array('colunaValor' => 'unConversao.D037_Unidade', 'colunaTitulo' => 'Un Conversão', 'colunaAlinhar' => 'center', 'style' => 'font-size:12px', 'colunaExibir' => false),
			array('colunaValor' => 'T006A_D184_Id', 'colunaTitulo' => 'Un Conversão', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'callback' => 'T006A_D184_Id_Unidade', 'callbackParameter' => array('divReload' => $r_divReload), 'colunaExibir' => $colunaUnidadeComercial),
			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Quantidade_OP', 'colunaTitulo' => 'OP', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Produção', '/ven/ven001/content/ven001contentProdutoOP/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Est.Tab', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>false),
            //array('colunaValor' => "(IF(T006_Quantidade_Estoque< T006_Quantidade, CONCAT('<span style=\"color: var(--BGVermelho);\">',REPLACE( REPLACE( REPLACE( FORMAT( T006_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ','),'</span>'), REPLACE( REPLACE( REPLACE( FORMAT( T006_Quantidade_Estoque, 2), '.', '|'), ',', '.'), '|', ',')))", 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array(false, $r_acaoId)),
            array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares - '.$r_T005_Id, '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true, "", array('T005_Id' => 'T005_Id')), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Quantidade_Estoque as Pedido', 'colunaTitulo' => 'Estoque', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T006_Quantidade')),
            array('colunaValor' => 'T006_C004_Id_Estoque', 'colunaTitulo' => 'CD', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'montaComboEstoque', 'colunaExibir' => false),
            /*array('colunaValor' => 'T006_Flag_Reservar_Estoque', 'colunaTitulo' => 'Reservado', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array(
                 'T006_Flag_Reservar_Estoque', 'N|S'),
            ),*/
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'T006_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006A_Id', 'colunaTitulo' => 'T006A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'T004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Qte_Embalagem', 'colunaTitulo' => 'Qtde Caixa', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Quantidade', 'colunaExibir' => $colunaQuantidade),
			array('colunaValor' => 'T006A_Quantidade_Comercial', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Quantidade', 'colunaExibir' => $colunaQuantidadeComercial),	
			array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T006_Valor_Frete_Unitario', 'colunaTitulo' => 'Frete', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Preco_Original', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaPedido', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'T006_Id', 'right', $rowT005['T005_Casas_Decimais_Preco'], 'T006'), 'colunaExibir' => $colunaPrecoTabela),
            array('colunaValor' => 'T006_Valor_Preco_Tabela', 'colunaTitulo' => 'Forn', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoFornecedorPedido', 'callbackParameter' => array('Demonstrativo Preço Fornecedor', '/ven/ven001/outro/demonstrativoPrecoFornecedor/', 'T006_Id', 'right', 4, 'T006'), 'colunaExibir' => false),
			array('colunaValor' => "(SELECT D009_Id FROM D009 WHERE D009_D049_Id=D049_Id AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}') as D009_Id_Transferencia", 'colunaTitulo' => 'D009_Id_Transferencia Id', 'colunaExibir' => false),
			array('colunaValor' => "'{$g['C031']['C004PrecoTransferenciaGridProdutos']}' as C004_Id_Transferencia", 'colunaTitulo' => 'C004_Id_Transferencia Id', 'colunaExibir' => false),
			array('colunaValor' => 'T006_Demonstrativo_Preco_Transferencia', 'colunaTitulo' => 'T006_Demonstrativo_Preco_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'T006_Valor_Custo_Transferencia', 'colunaTitulo' => 'T006_Valor_Custo_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'T006_Flag_Origem_Produto_Transferencia', 'colunaTitulo' => 'T006_Flag_Origem_Produto_Transferencia', 'colunaExibir' => false),
			array('colunaValor' => 'T006_Valor_Preco_Transferencia', 'colunaTitulo' => 'Transf', 'colunaAlinhar' => 'right', 'callback' => 'retornaPrecoTransferencia', 'callbackParameter' => array(false, $r_acaoId, false, 'T006_Id', 'D009_Id_Transferencia', 'C004_Id_Transferencia'), 'colunaExibir' => false),
            array('colunaValor' => 'T006_Percentual_Desconto', 'colunaTitulo' => '% Desc', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Percentual_Desconto', 'colunaExibir' => $colunaPercentualDesconto),
            array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $rowT005['T005_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Percentual_Desconto', 'colunaExibir' => $colunaPrecoLiquido),
			array('colunaValor' => 'T006A_Preco_Unitario_Comercial', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => $rowT005['T005_Casas_Decimais_Preco'], 'colunaEditar' => true, 'style' => 'background: var(--celula-verde); ;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Percentual_Desconto', 'colunaExibir' => $colunaPrecoComercial),
			array('colunaValor' => 'T006_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => $colunaPrecoTotal),
            array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora', 'colunaExibir' => false),
            array('colunaValor' => 'T208_Rota', 'colunaTitulo' => 'Rota', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Hora_Entrega', 'colunaTitulo' => 'Entrega', 'colunaTempo' => 'S', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_T208_Id', 'colunaTitulo' => 'Rota', 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'T208_Rota', 'colunaEditarTodos' => true, 'colunaEditar' => true, 'colunaExibir' => false,
				'colunaEditarGeraSelect' => array('T208', 'T208_Id', 'T208_Rota', "ORDER BY T208_Rota")
			),
            array('colunaValor' => 'T006_D022_Id', 'colunaTitulo' => 'Transportador', 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D022_Nome_Empresa', 'colunaEditarTodos' => true, 'colunaEditar' => true, 'colunaExibir' => false,
				'colunaEditarGeraSelect' => array('D022', 'D022_Id', 'D022_Nome_Empresa', "ORDER BY D022_Nome_Empresa")
			),
            array('colunaValor' => 'T006_Valor_Preco_Original_Bruto', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'right', 'callback' => 'campoPrecoTabelaPedido', 'callbackParameter' => array('Demonstrativo Preço de Venda', '/ven/ven001/outro/demonstrativoPrecoVenda/', 'T006_Id', 'right', 4, 'T006'), 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => 'T006_Percentual_Desconto_Bruto', 'colunaTitulo' => '% Desc', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Percentual_Desconto', 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => 'T006_Valor_Preco_Bruto', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => $rowT005['T005_Casas_Decimais_Preco'], 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Percentual_Desconto', 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => '(T006_Valor_Preco_Bruto*T006_Quantidade) as T006_Valor_Total_Bruto', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => $colunaPrecoBruto),
            array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario+(T006_Valor_IPI/T006_Quantidade)+(T006_Valor_ICMS_Substituicao_Retencao/T006_Quantidade) as Preco_Bruto', 'colunaTitulo' => 'Preço Bruto', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'IF(D009_Data_Alteracao_Custo > D009_Data_Ultima_Entrada, D009_Data_Alteracao_Custo, D009_Data_Ultima_Entrada)', 'colunaTitulo' => 'Ult. Alt. Custo', 'callback' => 'gCorrigeData', 'callbackParameter' => 'true', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Total_Desconto', 'colunaTitulo' => 'R$ Desc', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'concat(replace(replace(round(((T006_Valor_Preco_Unitario-T006_Valor_Custo_Unitario)/T006_Valor_Preco_Unitario)*100),",",""),".",","),"","%")', 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => "T006_Percentual_Margem", 'colunaTitulo' => '% Marg', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaCasasDecimais' => 2, 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T006_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width:1%;background-color: var(--celula-verde);', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T006_IPV_Sem_Lucro', 'colunaTitulo' => 'IPV SL', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Lucro', 'colunaTitulo' => 'R$ Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => '(T006_Valor_Lucro/((T006_Valor_Custo_Unitario*T006_IPV)*T006_Quantidade))*100', 'colunaTitulo' => '% Lucro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Entg.dias', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload', 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Observacao', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'style' => 'font-weight:normal;background: var(--celula-amarelo-claro);', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'concat(D006_Codigo_CFOP," (",D006_Id,")") as D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T006_D006_Id_Info', 'Demonstrativo CFOP', 'T006_Id', 'left', '', '700')),
            array('colunaValor' => 'T006_D006_Id_Info', 'colunaExibir' => false),
            array('colunaValor' => 'IF(IFNULL(D149_Id,0) > 0, concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao), "????????") as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('T006_D005_Id_Info', 'Demonstrativo NCM', 'T006_Id', 'left', '', '500')),
            array('colunaValor' => 'T006_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px'),
            array('colunaValor' => 'T006_Flag_Destino_Produto', 'colunaTitulo' => 'Dest', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarGeraSelect' => $destinoProdutos, 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),
            array('colunaValor' => 'T006_Aliquota_IPI', 'colunaTitulo' => 'IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T006_Aliquota_ICMS', 'colunaTitulo' => 'ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T006_Percentual_Reducao_ICMS', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => '((T006_Valor_ICMS_Substituicao_Retencao/T006_Quantidade)/T006_Valor_Preco_Unitario)*100', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T006A_Percentual_FCP', 'colunaTitulo' => '%FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T006_Aliquota_PIS', 'colunaTitulo' => 'PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T006A_Percentual_Reducao_PIS', 'colunaTitulo' => '%Red PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T006_Aliquota_COFINS', 'colunaTitulo' => 'COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T006A_Percentual_Reducao_COFINS', 'colunaTitulo' => '%Red PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);;'),
			array('colunaValor' => 'T006_Aliquota_IRPJ', 'colunaTitulo' => 'IRPJ', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T006_Aliquota_CSLL', 'colunaTitulo' => 'CSLL', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
			array('colunaValor' => 'T006_Percentual_Partilha_ICMS_Destino', 'colunaTitulo' => 'Difal Dest', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T006_Percentual_Partilha_ICMS_Origem', 'colunaTitulo' => 'Difal Orig', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            //array('colunaValor' => 'T006_Aliquota_ICMS_ST', 'colunaTitulo' => '% ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T006_Valor_Total_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_IRPJ', 'colunaTitulo' => 'IRPJ R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_CSLL', 'colunaTitulo' => 'CSLL R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_Partilha_ICMS_Destino', 'colunaTitulo' => 'Difal Dest R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_Partilha_ICMS_Origem', 'colunaTitulo' => 'Difal Orig R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_ICMS_Desonerado', 'colunaTitulo' => 'ICMS Deson. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006A_Valor_FCP_ST', 'colunaTitulo' => 'FCP ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006A_Valor_FCP', 'colunaTitulo' => 'FCP R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),				
			array('colunaValor' => 'T006_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T006_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T006_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T006_Percentual_Reducao_ICMS_ST', 'colunaTitulo' => '%Red', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
			array('colunaValor' => 'T006_ST_VA', 'colunaTitulo' => 'MVA', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);'),
			array('colunaValor' => 'T006_Preco_Pauta_ST', 'colunaTitulo' => 'Pauta', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            //array('colunaValor' => 'T006_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);'),
            array('colunaValor' => 'T006_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Base_ICMS', 'colunaTitulo' => 'Base ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_Base_Partilha_ICMS', 'colunaTitulo' => 'Base Difal', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Base_IRPJ', 'colunaTitulo' => 'Base IRPJ', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Base_CSLL', 'colunaTitulo' => 'Base CSLL', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006A_Valor_Base_FCP_ST', 'colunaTitulo' => 'Base FCP ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006A_Valor_Base_FCP', 'colunaTitulo' => 'Base FCP', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Item_Pedido_Compra', 'colunaTitulo' => 'Item.OC', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
            array('colunaValor' => 'T006_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => '((T006_Valor_Preco_Unitario/T006_Valor_Custo_Unitario)-1)*100', 'colunaTitulo' => '% Margem', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré Cadastro', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            //array('colunaValor' => 'D049_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Percentual_Comissao', 'colunaTitulo' => '%Comiss', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Comissao', 'colunaTitulo' => 'Comissão', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'T004_T219_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D070_Data_Vencimento', 'colunaTitulo' => 'Validade', 'colunaExibir' => false, 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaExibirNivel' => 4, 'callback' => 'abrirSeparacao'),
            //array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cotação', 'style' => 'width:1%;'),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cotação', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação de compra', '/est/est015/content/est015ContentEditarOrcamento/', 'T218_Id', '990,605'), 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T006_T206_Id', 'colunaTitulo' => 'T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T225_Id', 'colunaTitulo' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'IF((T006_T219_Id > 0 OR T006_T225_Id > 0 OR T006_Demonstrativo_Preco_Cotacao != "") AND T006_Valor_Preco_Cotacao > 0, 675, 450) as width', 'colunaTitulo' => 'width', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T006_Id_Dividir', 'colunaTitulo' => 'Id Div', 'colunaExibir' => false),
            //array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Transferencia', 'colunaTitulo' => 'T006_Flag_Transferencia', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Tabela', 'colunaTitulo' => 'D009_Valor_Custo_Tabela', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Aliquota_ICMS_Credito', 'colunaTitulo' => '%ICM Créd', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_ICMS_Credito', 'colunaTitulo' => 'Valor ICM Créd', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D184_Quantidade_Conversao', 'colunaTitulo' => 'D184_Quantidade_Conversao', 'colunaExibir' => false),
			array('colunaValor' => 'D184_Tipo_Operacao', 'colunaTitulo' => 'D184_Tipo_Operacao', 'colunaExibir' => false),
			array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'D009_C004_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T005_C004_Id', 'colunaTitulo' => 'T005_C004_Id', 'colunaExibir' => false),

        );

		$extraConversao = '';
		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			array_push($grid, array('colunaValor' => 'T006A_Quantidade_Conversao', 'colunaTitulo' => 'Qtde Conv.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Quantidade'));
			array_push($grid, array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'Peso Unitário KG', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'));
			array_push($grid, array('colunaValor' => 'D002A_Utiliza_Conversao', 'colunaTitulo' => 'D002A_Utiliza_Conversao', 'colunaExibir' => false));

			$extraConversao  = ' left join D002 on D002_Id = D001_D002_Id ';
			$extraConversao .= ' left join D002A on D002_Id = D002A_D002_Id ';
		}

		/**
		*
		* Definição da SQL
		*/
		$from = 'T006';

		$extra = <<<EOT
			-- JOIN
				left join T005 on T005_Id=T006_T005_Id
				left join D024 on D024_Id=T006_D024_Id
				left join D037 on D037_Id=T006_D037_Id
				left join D009 on D009_Id=T006_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D006 on D006_Id=T006_D006_Id
				left join D005 on D005_Id=T006_D005_Id
				LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=T005_C004_Id
				LEFT JOIN T066 on T006_T066_Id=T066_Id
				LEFT JOIN D004 on D004_Id=T066_D004_Id
				LEFT JOIN T004 ON T004_Id=T006_T004_Id
				left join T219 ON T219_Id=T006_T219_Id
				left join T218 ON T218_Id=T219_T218_Id
				LEFT JOIN D069 on D069_Id=D009_D069_Id AND D069_Flag_Tabela_Principal='S'
				LEFT JOIN D070 on D070_Id=D069_D070_Id
				left join D022 on D022_Id=T006_D022_Id
				left join T208 on T208_Id=T006_T208_Id
				left join T006A on T006_Id=T006A_T006_Id
				left join D184 on D184_Id=T006A_D184_Id
				left join D037 as unPadrao on unPadrao.D037_Id=T006_D037_Id
				left join D037 as unConversao on unConversao.D037_Id=D184_D037_Id
				{$extraConversao}
			-- WHERE
				where T006_T005_Id = '{$r_acaoId}'
			-- GROUP BY
				group by T006_Id 
				
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

		function gridLinha($array) {
			global $g;
			if(($array['todosCampos']['D009_C004_Id'] != $array['todosCampos']['T005_C004_Id']) && $g['C031']['venderEstoqueMatriz'] == 'N'){
				return 'linhaError';				
			}elseif ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S' or $array['todosCampos']['T006_D009_Id']<=0) {
				return 'linhaVermelho';
			} 
		}

		//Define ação do click de abertura (ao clicar em uma linha do grid)

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T006_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/ven/ven002/content/ven002ContentEditarProduto/', '&acaoId=' + encodeURIComponent(acaoId) + '&T005_Id=' + encodeURIComponent('{$r_acaoId}') + '&tabela={$from}', [650,500]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});

			function colunaEditarCallbackJSReload() {
				divRefresh('{$r_divReload}');
			}
			
			// Função JS executada como callback do editar campo (colunaEditarCallbackJS)
			function colunaEditarCallbackJS_T006_Percentual_Desconto(T006_Id, campoEditado, valorAntigo, novoValor) {
					T006_Id = T006_Id.replace(/T006_Id-/, '');
					if(campoEditado == 'T006_Percentual_Desconto' || campoEditado == 'T006_Valor_Preco_Bruto'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																					      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					      '&novoValor='			+ encodeURIComponent(novoValor) +  
																					      '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
								divRefresh('{$r_divReload}');
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
								divRefresh('{$r_divReload}');
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
								divRefresh('{$r_divReload}');
								//alert("NFe Excluída");
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					} else if(campoEditado == 'T006A_Preco_Unitario_Comercial'){
						$.getJSON('/ven/ven002/grid_func-ajax/ven002aplicarPercentualDesconto/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																				           '&campoEditado='     + encodeURIComponent(campoEditado) +
																						   '&valorAntigo='      + encodeURIComponent(valorAntigo) +
																						   '&novoValor='        + encodeURIComponent(novoValor) +  
																						   '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
								divRefresh('{$r_divReload}');
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
					//divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
					divRefresh($('#{$g['divId']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
				}

				function colunaEditarCallbackJS_T006_Item(T006_Id, campoEditado, valorAntigo, novoValor) {
					T006_Id = T006_Id.replace(/T006_Id-/, '');
					$.getJSON('/ven/ven002/grid_func-ajax/processarNumeroItemPedido/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
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

				function colunaEditarCallbackJS_T006_Quantidade(T006_Id, campoEditado, valorAntigo, novoValor) {
					T006_Id = T006_Id.replace(/T006_Id-/, '');
					$.getJSON('/ven/ven002/grid_func-ajax/aplicarQuantidade/?ajax=true&T006_Id='    + encodeURIComponent(T006_Id) + 
																					  '&campoEditado='		+ encodeURIComponent(campoEditado) +
																					  '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																					  '&novoValor='			+ encodeURIComponent(novoValor) +  
																					  '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}', true, false, false, T006_Id, 'T006_Id'); // grid
							divRefresh('{$r_divReload}');
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
		
		case 'ven002GridPagamento':
			global $g;
			$sqlT005 = "SELECT T005_Pgto_Data, T005_Pgto_C007_Id, C007_Login FROM T005 LEFT JOIN C007 ON C007_Id=T005_Pgto_C007_Id WHERE T005_Id='$r_acaoId'";
			$resultT005 = mysql_query($sqlT005);
			$linhaT005 = mysql_fetch_array($resultT005);
			$dataGerada = gCorrigeData($linhaT005['T005_Pgto_Data']);
			$user = strtoupper($linhaT005['C007_Login']);
			if(($user != "") || (!empty($user))){
				echo "Parcelas Geradas por <b>".$user."</b> em <b>".$dataGerada."</b>";
			}

			$CNPJ_Empresa_Corrente=substr($g['C004']['C004_Cnpj'], 0, -8);

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T090_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T090_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T090_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T090_Prazos_Dias"),
				array('colunaValor' => 'T090_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T090_Prazos_Dias"),
				array('colunaValor' => 'ven002_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "ven002_Dia_Semana"),
				array('colunaValor' => 'T090_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T090_Prazos_Dias"),
				array('colunaValor' => 'T090_Taxa_Juros', 'colunaTitulo' => '%Juros', 'style'=>'width:1px'),
				//array('colunaValor' => 'T090_Valor_Juros', 'colunaTitulo' => 'Juros'),
				array('colunaValor' => 'T090_D027_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D027', 'D027_Id', 'D027_Portador', 'LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE D027_Flag_Ativo = "S" AND D027_Flag_Exibe_CRM = "S" AND ((D007_Flag_Layout="1" and D007_Flag_Ativo="S" AND C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%") OR ifnull(D027_D007_Id,0) = 0) order by D027_Portador', true), 'callback' => 'T090_D027_Id_Portador'),
				array('colunaValor' => 'D027_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'D027_Flag_Tipo', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				array('colunaValor' => 'T090_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true),
				array('colunaValor' => 'ven002_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "ven002_Detalhes"),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T090';

			$extra = <<<EOT
				left join D027 on D027_Id=T090_D027_Id
				WHERE T090_T005_Id = {$r_acaoId}
EOT;

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
					var id = $(this).closest('tr').attr('id').replace(/T090_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T090_Prazos_Dias(T090_Id, campoEditado, valorAntigo, novoValor) {
					T090_Id = T090_Id.replace(/T090_Id-/, '');
					$.getJSON('/ven/ven002/grid_func-ajax/atualizarParcelas/?ajax=true&T090_Id='      		+ encodeURIComponent(T090_Id) + 
																				      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				      '&novoValor='			+ encodeURIComponent(novoValor) +  
																				      '&callback=?', function(request) {
						if (!request.code) {
							dialogConfirm('Erro', request.data);
						}
						divRefresh('{$g['divId']}');
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
			array('colunaValor' => 'T167_Nome_Arquivo', 'colunaTitulo' => 'Arquivo', 'callback' => 'gridDocumentosArquivo'),
			array('colunaValor' => 'T167_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T167_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
			array('colunaValor' => 'visualizarArquivo', 'colunaTitulo' =>'Visualizar', 'colunaTipo'=>'livre', 'callback'=>'visualizarArquivo', 'callbackParameter' => array('T167_Id', 'documentoPedido'), 'style' => 'width: 1%'),
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
                AND T005_Flag_Status!='8'
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
			array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
	        array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;'),
	        array('colunaValor' => 'T006_Descricao_Produto', 'colunaExibir' => false),
	        array('colunaValor' => 'descricao', 'colunaTitulo' => 'Descrição', 'colunaTipo' => 'livre', 'callback' => 'validaCampoCotacaoPedido'),
	        array('colunaValor' => 'substr(D082_Marca,1,15)', 'colunaTitulo' => 'Marca', 'style' => 'width:1%;font-size:11px'),
	        array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
	        array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;width:1%', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T006_Percentual_Desconto'),
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
	        array('colunaValor' => 'T006_Flag_Nao_Comprar', 'colunaTitulo' => 'Não<br>Comprar', 'callback' => 'gCheckbox', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'callbackParameter' => array('T006_Flag_Nao_Comprar', 'N|S'), 'colunaEditarTodos' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
	        array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
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
			LEFT JOIN T219 ON T219_Id = T006_T219_Id
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
				return 'color:blue;font-size:13px';
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

		$sqlT005 = mysql_query("SELECT T005_Id,
									   T005_Valor_Total_Produtos, 
									   T005_Flag_Lista_Preco, 
									   T005_Valor_Total_ICMS, 
									   T005_Valor_Total_IRPJ, 
									   T005_Valor_Total_CSLL, 
									   T005_Valor_Total_PIS, 
									   T005_Valor_Total_COFINS, 
									   T005_Valor_Total_Produtos 
								  FROM T005 
								 WHERE T005_Id = '{$r_acaoId}'");
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

		$T005_Valor_Total_Produtos = $mT005['T005_Valor_Total_Produtos']-$impostos;
		
        if ($mT005['T005_Flag_Lista_Preco']=='2'){
			$T005_Valor_Total_Produtos = $T005_Valor_Total_Produtos * 1.70;
		}

		$sqlT006 = mysql_query("SELECT SUM(D009_Valor_Custo_Compra*T006_Quantidade) custo from T006 left join D009 on D009_Id=T006_D009_Id where T006_T005_Id='{$r_acaoId}'");
		$mT006 = mysql_fetch_array($sqlT006);
		$custo = $mT006['custo'];
		if($custo<=0){
			$custo=0;
		}

    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T251_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => true),
			array('colunaValor' => 'concat(concat(T251_Descricao," ",if(T251_Flag_Altera_Preco="S","(p)",""))," ",if(T251_Flag_Campo_Calculado="S","(c)",""))', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T251_Aliquota', 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'atualizarTotaisIPV', 'colunaTotal' => true, 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);;width: 15%', 'colunaExibir' => false),
			array('colunaValor' => 'T251_Aliquota_Editavel', 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaTotal' => true, 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);width: 15%', 'colunaExibir' => true),
		    array('colunaValor' => "(if(T251_Grupo='IPV',$T005_Valor_Total_Produtos,$custo)*(T251_Aliquota/100))", 'colunaTitulo' => 'Valor R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'width: 15%', 'colunaTotal' => true),
			array('colunaValor' => 'T251_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
        );		

		$filtro = "";
		if($g['nivelAlcada']==2){
			$filtro="AND (T251_Nivel_Acesso=2 OR T251_Nivel_Acesso=1)";
		}else if($g['nivelAlcada']==1){
			$filtro="AND T251_Nivel_Acesso=1";
		}

		/*
		* Definição da SQL
		*/
		$from = 'T251';
		
		$extra = <<<EOT
			LEFT JOIN T005 ON T005_Id=T251_T005_Id
			WHERE T251_T005_Id = '{$r_acaoId}'
			{$filtro}
EOT;

		$extraICF = <<<EOT
			LEFT JOIN T005 ON T005_Id=T251_T005_Id
			WHERE T251_T005_Id = '{$r_acaoId}' and T251_Grupo='ICF'
EOT;

		$extraIPV = <<<EOT
			LEFT JOIN T005 ON T005_Id=T251_T005_Id
			WHERE T251_T005_Id = '{$r_acaoId}' and T251_Grupo='IPV' and ifnull(T251_Tipo,'')!=4
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
        $aliquotasIPV = $g['sqlAuto']->pegarSqlCampo("sum(T251_Aliquota)", $from, $extraIPV );
        $ICF 		  = $g['sqlAuto']->pegarSqlCampo("(100/(100-SUM(T251_Aliquota))) AS IPV", $from, $extraICF);
		$IPV          = 100 / ( 100 - $aliquotasIPV );
		$IPV 		  = gCorrigeNumero($IPV, 4);
		$ICF 		  = gCorrigeNumero($ICF, 4);

		echo <<<EOT
			<div class="buttonsBottomTop">			
EOT;

		echo <<<EOT
			<span class="item" style="text-align:right; float: left;">ICF<br><input type="text" value="{$ICF}" class="whiteBg" id="" size="11" readonly></span>
			<span class="item" style="text-align:right; float: left;">IPV<br><input type="text" value="{$IPV}" class="whiteBg" id="" size="11" readonly></span>
			<div style="clear:both;"></div>
			</div>
EOT;
		echo <<<EOT
           
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				function atualizarTotaisIPV(T251_Id, campoEditado, valorAntigo, novoValor) {
					divRefresh('{$g['divId']}');
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

    case 'ven002GridMontagemIPVTotal':

		/*
		* Definição da SQL
		*/
		$from = 'T251';
		
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

    	// Recupera o filtro do grid CRM -> Pedidos
		$file = $g['pathDadosUsuario'] . 'gridFiltro/e9a153be7b1d43540ab4f30c60f8c09e';
		if (file_exists($file)) {
			$gridFiltroPedidos = unserialize(file_get_contents($file));
		}

		/*
		* Definição da SQL
		*/
		$Pedidos = mysql_query("SELECT T005_Id 
		                          FROM T005
							 LEFT JOIN T251 ON T251_T005_Id=T005_Id
							 LEFT JOIN D024 ON D024_Id=T005_D024_Id
							 LEFT JOIN D018 ON D018_Id=D024_D018_Id
							 LEFT JOIN D020 ON D020_Id=D024_D020_Id
						  	 LEFT JOIN D013 ON D013_Id=T005_D013_Id
							 LEFT JOIN T003 ON T003_Id=T005_T003_Id
							 LEFT JOIN C007 AS Vendedor ON Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
							 LEFT JOIN C007 AS Externo ON Externo.C007_Id=T005_C007_Id_Vendedor_Externo
							 LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=T005_Liberou_Alcada_C007_Id
							 LEFT JOIN C007 AS LiberouOrcamento ON LiberouOrcamento.C007_Id=T003_Liberou_Alcada_C007_Id
							 LEFT JOIN C007 AS Supervisor ON Supervisor.C007_Id=Vendedor.C007_C007_Id
							 LEFT JOIN C007 AS Supervisor2 ON Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
							 LEFT JOIN C007 AS Substituto ON Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
							 LEFT JOIN T007 ON T007_T005_Id=T005_Id
							 LEFT JOIN D006 ON D006_Id=T005_D006_Id
							 LEFT JOIN T006 ON T006_T005_Id=T005_Id
							 LEFT JOIN D021 ON D021_Id=T005_D021_Id
								 WHERE (T005_C004_Id='{$g['empresaAtual']}' 
								   AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL))
								       {$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
								   AND {$gridFiltroPedidos['extra']}
							  GROUP BY T005_Id");
		
		while ($mPedidos = mysql_fetch_array($Pedidos)){

			$r_acaoId = $mPedidos['T005_Id'];

			$sqlT005 = mysql_query("SELECT T005_Valor_Total_Produtos,
										   T005_Flag_Lista_Preco, 
										   T005_Valor_Total_ICMS, 
										   T005_Valor_Total_IRPJ, 
										   T005_Valor_Total_CSLL, 
										   T005_Valor_Total_PIS, 
										   T005_Valor_Total_COFINS, 
										   T005_Valor_Total_Produtos
									  FROM T005 
									 WHERE T005_Id = '{$r_acaoId}'");

			$mT005 = mysql_fetch_array($sqlT005);

			$impostos = 0;
			$impostos += ($g['C031']['somarICMSPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_ICMS'] : 0;
	        $impostos += ($g['C031']['somarPISPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_PIS'] : 0;
	        $impostos += ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_COFINS'] : 0;
	        $impostos += ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_IRPJ'] : 0;
	        $impostos += ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_CSLL'] : 0;

	        $T005_Valor_Total_Produtos = $mT005['T005_Valor_Total_Produtos'] - $impostos;

			$sqlT006 = mysql_query("SELECT SUM(T006_Valor_Custo_Unitario*T006_Quantidade) AS custo 
									 FROM T006 
							    LEFT JOIN D009 ON D009_Id=T006_D009_Id 
								    WHERE T006_T005_Id='{$r_acaoId}'");

			$mT006 = mysql_fetch_array($sqlT006);
			$custo = $mT006['custo'];

			$extra = <<<EOT
				LEFT JOIN T005 ON T005_Id=T251_T005_Id
				WHERE T251_T005_Id = '{$r_acaoId}'
EOT;
			$extraSemLucro = <<<EOT
				LEFT JOIN T005 ON T005_Id=T251_T005_Id
				WHERE T251_T005_Id = '{$r_acaoId}' and ifnull(T251_Tipo,0)!='3' and IFNULL(T251_Tipo,0)!='4' and T251_Grupo='IPV'
EOT;
			/**
	        * Totais
	        */
	        $tributos            = $g['sqlAuto']->pegarSqlCampo("{$impostos}", $from, $extra);
			$ipvSemLucro         = $g['sqlAuto']->pegarSqlCampo("SUM($T005_Valor_Total_Produtos*(T251_Aliquota/100))", $from, $extraSemLucro);
			$aliquotaIpvSemLucro = $g['sqlAuto']->pegarSqlCampo("SUM(T251_Aliquota)", $from, $extraSemLucro);

	        $valorLucro = $T005_Valor_Total_Produtos - $custo - $ipvSemLucro;

		    $T251 = mysql_query("SELECT T251_Id,T251_Descricao,
			                            T251_Aliquota,
								        (IF(T251_Grupo='IPV',IF(T251_Tipo='4',$T005_Valor_Total_Produtos/2,$T005_Valor_Total_Produtos),$custo)*(T251_Aliquota/100)) AS valor, 
										T251_Grupo 
								   FROM T251 
								  WHERE T251_T005_Id = '{$r_acaoId}' 
							   ORDER BY T251_Grupo ASC");

		    while ($mT251 = mysql_fetch_array($T251)){
		    	   $T251_Id = $mT251['T251_Descricao'];
		    	   $rowT251["$T251_Id"] += $mT251['valor'];
		    }

			$totalCusto 			 += $custo;
			$totalTributos 		     += $tributos;
			$totalipvSemLucro 		 += $ipvSemLucro;
			$totalValorLucro 		 += $valorLucro;
			$totalProdutos 			 += $T005_Valor_Total_Produtos;
			$totalAlquotaIpvSemLucro += $aliquotaIpvSemLucro; 
			$totalPedidos 			 ++;

		}

		$baseProdutos 			    = $totalProdutos;
		$baseCusto 				    = $totalCusto;
		$totalProdutosComImposto    = $totalProdutos;
		$totalProdutosSemImposto    = $totalProdutos + $totalTributos;
		$totalPercentualImpostos    = ($totalTributos / $totalProdutosSemImposto)*100;
		$totalPercentualLucro 	    = ($totalValorLucro / $totalProdutos)*100;
		$totalPercentualCusto       = ($baseCusto / $totalProdutosComImposto)*100;
		$totalPercentualIpvSemLucro = ($totalipvSemLucro / $totalProdutos)*100;

		//TRATANDO OS VALORES
		$totalPercentualLucro 	    = gCorrigeNumero($totalPercentualLucro);
		$totalPercentualImpostos 	= gCorrigeNumero($totalPercentualImpostos);
		$totalPercentualCusto       = gCorrigeNumero($totalPercentualCusto);		
		$totalTributos 		       	= gCorrigeNumero($totalTributos);
		$totalipvSemLucro 		   	= gCorrigeNumero($totalipvSemLucro);
		$totalValorLucro           	= gCorrigeNumero($totalValorLucro);
		$totalProdutosSemImposto   	= gCorrigeNumero($totalProdutosSemImposto);
		$totalProdutosComImposto  	= gCorrigeNumero($totalProdutosComImposto);
		$totalCusto 			  	= gCorrigeNumero($totalCusto);
		$totalPercentualIpvSemLucro = gCorrigeNumero($totalPercentualIpvSemLucro);
	      
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span>
					<table id='totais' style="font-size:14px;border:1px solid silver" width='100%' cellpadding='3px'>
						<tr>
							<td>Qtd Pedidos</td><td align=right>{$totalPedidos}</td>
						</tr>
						<tr style="font-weight:bold;">
							<td>Produtos</td><td align=right>R$ {$totalProdutosSemImposto}</td>
						</tr>
						<tr>
							<td>Tributos </td><td align=right>R$ {$totalTributos}</td>
							<td align=right>{$totalPercentualImpostos}%</td>
						</tr>
						<tr style="font-weight:bold;">
							<td>Produtos-Tributos</td><td align=right>R$ {$totalProdutosComImposto}</td>
						</tr>
						<tr>
							<td>Custo</td><td align=right>R$ {$totalCusto}</td>
							<td align=right>{$totalPercentualCusto}%</td>
						</tr>
						<tr>
							<td>IPV - Lucro</td><td align=right>R$ {$totalipvSemLucro}</td>
							<td align=right>{$totalPercentualIpvSemLucro}%</td>
						</tr>
						<tr style="font-weight:bold;">
							<td>Lucro</td><td align=right>R$ {$totalValorLucro}</td>
							<td align=right>{$totalPercentualLucro}%</td>
						</tr>                       
					</table>
				</span>
			</div>
EOT;

		foreach ($rowT251 as $descricao => $Valor) {

			$T251 = mysql_query("SELECT T251_Grupo
								   FROM T251 
								  WHERE T251_Descricao = '{$descricao}' 
									AND IFNULL(T251_Grupo,'')!='' LIMIT 1");
			$mT251 = mysql_fetch_array($T251);

			if ($mT251['T251_Grupo']=='IPV'){
				$aliquotaIpv += ($Valor/$baseProdutos)*100;
			} else if ($mT251['T251_Grupo']=='ICF'){
				$aliquotaIcf += ($Valor/$baseCusto)*100;
			}
		}

 		$aliquotaIpv = ($aliquotaIpv > 0) ? gCorrigeNumero((100 / (100-$aliquotaIpv)),4) : '0,0000';
		$aliquotaIcf = ($aliquotaIcf > 0) ? gCorrigeNumero((100 / (100-$aliquotaIcf)),4) : '0,0000';

		echo <<<EOT
            <div class="buttonsBottomTop">
			
				<span class="item" style="text-align:right; float: left;">ICF<br><input type="text" value="{$aliquotaIcf}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right; float: left;">IPV<br><input type="text" value="{$aliquotaIpv}" class="whiteBg" id="" size="11" readonly></span>

				<table style="font-size:14px" width='100%' cellpadding='6px'>
					<tr>
						<td style="color:white;background: rgba(65, 81, 96, 0.9); border-radius: 4px 4px 0px 0px">Tipo</td>
						<td style="color:white;background: rgba(65, 81, 96, 0.9); border-radius: 4px 4px 0px 0px">Descrição</td>
						<td style="color:white;text-align:center;background: rgba(65, 81, 96, 0.9); border-radius: 4px 4px 0px 0px">%</td>
						<td style="color:white;text-align:right;background: rgba(65, 81, 96, 0.9); border-radius: 4px 4px 0px 0px">Valor R$</td>
					</tr>
EOT;

        foreach ($rowT251 as $Id => $Valor) {
			    $T251 = mysql_query("SELECT T251_Tipo, 
											T251_Grupo, 
											T251_Descricao 
									   FROM T251 
									  WHERE T251_Descricao = '{$Id}' 
									    AND IFNULL(T251_Grupo,'')!='' LIMIT 1");
			    $mT251 = mysql_fetch_array($T251);

				if ($mT251['T251_Grupo']=='IPV'){
		       		$aliquota		   = ($Valor/$baseProdutos)*100;
					$valorTotalIPV 	  += $Valor;
					$aliquotaTotalIPV += $aliquota;
				} else {
	        		$aliquota 		   = ($Valor/$baseCusto)*100;
					$valorTotalICF	  += $Valor;
					$aliquotaTotalICF += $aliquota;
				}

				$mT251['T251_Descricao'] = strtoupper($mT251['T251_Descricao']);
        		$Valor 					 = gCorrigeNumero($Valor);
				$totalValor 			 = gCorrigeNumero($valorTotalIPV + $valorTotalICF);
				$aliquotaTotal 		     = gCorrigeNumero($aliquotaTotalIPV + $aliquotaTotalICF);
				$aliquota 		   		 = gCorrigeNumero($aliquota);

				echo <<<EOT
					<tr>
				    	<td style='background:white'>{$mT251['T251_Descricao']}</td>
				    	<td style='background:white'>{$mT251['T251_Grupo']}</td>				    
				    	<td style='background:white;text-align:center'>{$aliquota}</td>
				    	<td style='background:white;text-align:right'>{$Valor}</td>
					</tr>
EOT;
        }

		echo <<<EOT
				<tr>
					<td style='font-weight:bold;text-align:left'>Sub-total</td>
					<td style='font-weight:bold;text-align:right'></td>
					<td style='font-weight:bold;text-align:right'>{$aliquotaTotal}</td>
					<td style='font-weight:bold;text-align:right'>{$totalValor}</td>
				</tr>
			</table>
		</div>    
EOT;

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
		    LEFT JOIN D009 ON D049_Id = D009_D049_Id
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
