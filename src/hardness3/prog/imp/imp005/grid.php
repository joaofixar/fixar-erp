<?php
namespace hardness;

// Carrega funções callback
require_once('prog/cad/cad016/grid_func-callback.php');
require_once('prog/imp/imp005/grid_func-callback.php');
// Carrega funções do ajax
require_once('prog/imp/imp005/grid_func-js.php');
require_once('bibliotecas/classes/IMP005.php');
$IMP005 = new IMP005();

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 22/11/2011
	*/
	case 'imp005GridDi':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T295_Id', $g['divId']), 'style' => 'width:1%'),
            array('colunaValor' => 'T295_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T295_Flag_MP', 'colunaTitulo' => 'Vender?', 'callback' => 'gCheckbox', 'colunaAlinhar' => 'center', 'callbackParameter' => array('T295_Flag_MP', '0|1'), 'style' => 'width:1%', 'colunaExibir' => false),
            //array('colunaValor' => 'T295_Data_Inicio_Demurrage', 'colunaTitulo' => 'Inicio demurrage', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T295_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T295_Data_Previsao', 'colunaTitulo' => 'Previsão chegada', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            //array('colunaValor' => 'T295_Data_Estoque', 'colunaTitulo' => 'Data estoque', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            //array('colunaValor' => 'T295_Numero_DI', 'colunaTitulo' => 'Número DI', 'style' => 'width:1%'),
            array('colunaValor' => 'T295_Ordem_Compra', 'colunaTitulo' => 'Purchase Order', 'style' => 'width:1%'),
            array('colunaValor' => 'SUM(T296_Quantidade)', 'colunaTitulo' => 'Total Itens', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            //array('colunaValor' => 'T295_Data_Embarque', 'colunaTitulo' => 'Data Embarque', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            //array('colunaValor' => 'T295_Data_Licenca_Importacao', 'colunaTitulo' => 'Data LI', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T295_D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'FORNECEDOR.D024_Nome_Empresa as Fornecedor', 'colunaTitulo' => 'Fornecedor', 'style' => 'width:1%'),
			array('colunaValor' => 'D030_Nome_Pais', 'colunaTitulo' => 'País'),
			array('colunaValor' => 'T295_Flag_Transporte_DI', 'colunaTitulo' => 'Forma transporte','callback' => 'tipoModalFrete'),
            array('colunaValor' => 'T295_D119_Id', 'colunaTitulo' => 'Despachante', 'colunaExibir' => false),
            array('colunaValor' => 'T295_D118_Id', 'colunaTitulo' => 'Portos', 'colunaExibir' => false),
            array('colunaValor' => 'T295_Valor_VMLE', 'colunaTitulo' => 'Produtos ME', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T295_Valor_VMLE*T295_Cotacao_ME as Produtos_Real', 'colunaTitulo' => 'Produtos R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'sum(T296_Valor_Total_Preco_Nota_Fiscal) as T296_Valor_Total_Preco_Nota_Fiscal', 'colunaTitulo' => 'Valor NFe R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'sum(T296_Valor_Total_Custo_Final) as T296_Valor_Total_Custo_Final', 'colunaTitulo' => 'Custo R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => "(SELECT (T300_Valor * IF(T300_Cotacao_ME > 0, T300_Cotacao_ME,1)) FROM T300 LEFT JOIN D191 ON D191_Id = T300_D191_Id WHERE T300_T295_Id = T295_Id AND D191_Descricao_Despesa = 'FRETE INTERNACIONAL' LIMIT 1) as freteTotal", 'colunaTitulo' => 'Frete R$','colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => true),
			array('colunaValor' => 'SUM(T296_Peso_Kg_Liquido_Total) as pesoTotal', 'colunaTitulo' => 'Peso total', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "'cancelar'", 'colunaTitulo' => '', 'callback' => 'campoCancelamentoPO'),
			array('colunaValor' => 'T295_Flag_Cancelado', 'colunaTitulo' => 'T295_Flag_Cancelado', 'colunaExibir' => false),
			array('colunaValor' => 'T295_Nome_Status', 'colunaTitulo' => 'Status'),
			array('colunaValor' => 'T295_Flag_Status', 'colunaTitulo' => 'T295_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'analista.C007_Primeiro_Nome', 'colunaTitulo' => 'Analista'),
			array('colunaValor' => 'conferiu.C007_Primeiro_Nome', 'colunaTitulo' => 'Conferência'),
			array('colunaValor' => 'T295_Liberou_Alcada_Data', 'colunaTitulo' => 'Data análise', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'group_concat(distinct T075_Id SEPARATOR \'|\')', 'colunaTitulo' => 'DI', 'callback' => 'abrirDeclaracaoImportacao', 'style' => 'width:1%'),
            //array('colunaValor' => 'checklist', 'colunaTitulo' => 'Checklist', 'callback' => 'geraCheckList', 'colunaTipo' => 'livre', 'style' => 'text-align:center'),
            array('colunaValor' => 'T295_Data_Atualizacao_Custo', 'colunaTitulo' => 'Att. Custo', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaExibir' => false),
        	//array('colunaValor' => 'T295_Codigo_URF_Entrada', 'colunaTitulo' => 'URF Entrada', 'colunaExibir' => true),
			//array('colunaValor' => 'T295_Codigo_URF_Despacho', 'colunaTitulo' => 'URF Despacho', 'colunaExibir' => true),
			//array('colunaValor' => 'T295_Codigo_Incoterms', 'colunaTitulo' => 'Incoterms', 'colunaExibir' => true),
			array('colunaValor' => 'T295_Condicao_Mercadoria', 'colunaTitulo' => 'Mercadoria', 'colunaExibir' => true),
			array('colunaValor' => 'T295_Enquadramento_Material_Usado', 'colunaTitulo' => 'Enquadramento do Material Usado', 'colunaExibir' => true),
			array('colunaValor' => 'T295_Operacao_Enquadramento_Material_Usado', 'colunaTitulo' => 'Operação Enquadramento', 'colunaExibir' => true),
			//array('colunaValor' => 'T295_Orgao_Desembaraco', 'colunaTitulo' => 'Orgão Desembaraço', 'colunaExibir' => true, 'colunaEditar'=>true),
			array('colunaValor' => 'IF((SELECT T296_Id FROM T296 WHERE T296_T295_Id = T295_Id AND T296_Flag_Produto_Divergencia = "S" LIMIT 1) > 0, "S", "N") as temDivergencia', 'colunaTitulo' => 'Tem divergência', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T295';
		
		$extra = <<<EOT
			left join D024 as FORNECEDOR on FORNECEDOR.D024_Id=T295_D024_Id
			left join D030 on D030_Id = FORNECEDOR.D024_D030_Id
			left join D024 as DESPACHANTE on DESPACHANTE.D024_Id=T295_D119_Id
			left join D119 on D119_Id=T295_D119_Id
			left join D118 on D118_Id=T295_D118_Id
			left join D044 on D044_Id=T295_D044_Id
			left join T296 on T296_T295_Id=T295_Id
			left join T055 on T055_T296_Id=T296_Id
			left join T075 on T055_T075_Id=T075_Id
			left join C007 as comprador on comprador.C007_Id=T295_C007_Id
			left join C007 as analista on analista.C007_Id=T295_Liberou_Alcada_C007_Id
			left join C007 as conferiu on conferiu.C007_Id=T295_C007_Id_Conferiu
			WHERE T295_C004_Id='{$g['empresaAtual']}'
			GROUP BY T295_Id
EOT;
		$extra_totalizacao = <<<EOT
			left join D024 as FORNECEDOR on FORNECEDOR.D024_Id=T295_D024_Id
			left join D024 as DESPACHANTE on DESPACHANTE.D024_Id=T295_D119_Id
			left join D119 on D119_Id=T295_D119_Id
			left join D118 on D118_Id=T295_D118_Id
			left join D044 on D044_Id=T295_D044_Id
			left join T296 on T296_T295_Id=T295_Id
			left join C007 on C007_Id=T295_C007_Id
			WHERE T295_C004_Id='{$g['empresaAtual']}'
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T295_Flag_Cancelado'] == 'S'){
		       return 'linhaVermelho';
		    } else if($array['todosCampos']['temDivergencia'] == 'S'){
				return 'linhaFundoAmareloClaro';
			} else if($array['todosCampos']['T295_Flag_Status'] == '5'){
		        return 'linhaCinza';
			} else if($array['todosCampos']['T295_Flag_Status'] == '1'){
				return 'linhaPink';
		 	} else if($array['todosCampos']['T295_Flag_Status'] == '3'){
				return 'linhaVerde';
			} else if($array['todosCampos']['T295_Flag_Status'] <= '0'){
		       return 'linhaAzul';
		    }
		}
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'imp005FormDIBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="imp005FormDIBtnIncluir">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp005FormDIBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Purchase Order', '/imp/imp005/content/imp005ContentDiIncluir/', '&tab=geral', [1000,600]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T295_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
			'T295_Ordem_Compra' => array('titulo' => 'Purchase Order', 'tipo' => 'texto'),
			'FORNECEDOR.D024_Nome_Empresa,FORNECEDOR.D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'T295_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T075_Numero_DI,T075_Id' => array('titulo' => 'D.I', 'tipo' => 'texto'),
            'IF((SELECT T296_Id FROM T296 WHERE T296_T295_Id = T295_Id AND T296_Flag_Produto_Divergencia = "S" LIMIT 1) > 0[[virgula]]"S"[[virgula]]"N")' => array('titulo' => 'Divergência', 'tipo' => 'select', 'select' => array(
					array('title' => 'Todos', 'value' => ''),
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N'),
				),
			),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_totalizacao = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra_totalizacao);
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
		* Botões (Versão Acima do GRID)
		*/
		/*if (!personalizacaoTotais()) {
			$sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(DISTINCT T295_Id),
													   SUM(T295_Valor_VMLE),
													   SUM(T295_Valor_Frete),
													   SUM(T295_Valor_THC),
													   SUM(T295_Valor_VMLD),
													   SUM(T295_Valor_Imposto_Importacao),
													   SUM(T295_Valor_IPI),
													   SUM(T295_Valor_PIS),
													   SUM(T295_Valor_COFINS),
													   SUM(T295_Valor_ICMS)",
		        	        	                       $from,
		        	            	                   $extra_totalizacao);

			list($valorTotal,
				 $totalVMLE,
				 $totalFrete,
				 $totalTHC,
				 $totalVMLD,
				 $totalImportacao,
				 $totalIPI,
				 $totalPIS,
				 $totalCOFINS,
				 $totalICMS) = $sqlTotal;

			$totalVMLE = gCorrigeNumero($totalVMLE);
			$totalFrete = gCorrigeNumero($totalFrete);
			$totalTHC = gCorrigeNumero($totalTHC);
			$totalVMLD = gCorrigeNumero($totalVMLD);
			$totalImportacao = gCorrigeNumero($totalImportacao);
			$totalIPI = gCorrigeNumero($totalIPI);
			$totalPIS = gCorrigeNumero($totalPIS);
			$totalCOFINS = gCorrigeNumero($totalCOFINS);
			$totalICMS = gCorrigeNumero($totalICMS);

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">D.I's<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}*/
		
/*		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">D.I's<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total VMLE<br><input type="text" value="{$totalVMLE}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total Frete<br><input type="text" value="{$totalFrete}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total THC<br><input type="text" value="{$totalTHC}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total VMLD<br><input type="text" value="{$totalVMLD}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total II<br><input type="text" value="{$totalImportacao}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total IPI<br><input type="text" value="{$totalIPI}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total PIS<br><input type="text" value="{$totalPIS}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total COFINS<br><input type="text" value="{$totalCOFINS}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Total ICMS<br><input type="text" value="{$totalICMS}" class="whiteBg" id="" size="11" readonly></span> 
				<div style="clear:both;"></div>
			</div>
EOT; */

		
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
					var chavePrimaria = 'T295_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Purchase Order - Purchase Order: ' + acaoId, '/imp/imp005/content/imp005ContentDiIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [990,560]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

echo <<<EOT
   <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
         $('#{$g['divId']} .grid').css('width', '1%');
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
	* @author AndreySliwinski
	* @since 22/11/2011
	*/                 
	case 'imp005GridProdutoDI':
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T296_Id', $g['divId'])),
			array('colunaValor' => 'T295_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T295_Numero_DI', 'colunaTitulo' => 'Número DI', 'style' => 'width:1%,'),
			array('colunaValor' => 'T295_D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%'),
			array('colunaValor' => 'FORNECEDOR.D024_Nome_Empresa', 'colunaTitulo' => 'Exportador', 'style' => 'width:1%', 'colunaDimensao'=>'S'),
			array('colunaValor' => 'T296_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T296_D009_Id', 'colunaTitulo' => 'Produto', 'colunaExibir'=>false),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'T296_Codigo_Produto', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaDimensao'=>'S'),
			array('colunaValor' => 'T296_Descricao_Produto', 'colunaTitulo' => 'Descrição Inglês', 'colunaDimensao'=>'S'),
			array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'NCM', 'colunaAlinhar' => 'center', 'colunaDimensao'=>'S'),
			array('colunaValor' => 'T296_Peso_Kg_Liquido_Unitario', 'colunaTitulo' => 'Peso Kg unit', 'colunaEditar' => true, 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T296_Quantidade', 'colunaTitulo' => 'Quantidade','colunaCasasDecimais' => '3', 'colunaEditar' => true),
			//array('colunaValor' => 'T296_Flag_ME', 'colunaTitulo' => 'ME'),
			array('colunaValor' => 'T296_Valor_Custo_Unitario', 'colunaTitulo' => 'Unitário ME', 'colunaCasasDecimais' => '7', 'colunaAlinhar' => 'right', 'colunaEditar' => true ),
			array('colunaValor' => 'T296_Valor_Total_Custo', 'colunaTitulo' => 'Total ME', 'tipo' => 'moeda', 'colunaCasasDecimais' => '7', 'colunaAlinhar' => 'right', 'colunaMetrica'=>'S' ),
			array('colunaValor' => 'T296_Valor_Preco_Unitario_Nota_Fiscal', 'colunaTitulo' => 'NFe unit R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'T296_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo unit R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => '' ),
			array('colunaValor' => 'T296_Peso_Kg_Liquido_Total', 'colunaTitulo' => 'Peso Kg total', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right', 'colunaMetrica'=>'S'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
			//array('colunaValor' => 'T296_Valor_Total_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Valor_Total_Despesas', 'colunaTitulo' => 'Despesas', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Valor_Imposto_Importacao', 'colunaTitulo' => 'II R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'T296_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Valor_ICMS_Presumido', 'colunaTitulo' => 'ICMS Pres. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Valor_ICMS_Pagar', 'colunaTitulo' => 'ICMS Pagar R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Peso_Kg_Liquido_Unitario', 'colunaTitulo' => 'Peso Kg unit', 'colunaEditar' => true, 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T296_Aliquota_Imposto_Importacao', 'colunaTitulo' => '% II', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true ),
			//array('colunaValor' => 'T296_Aliquota_IPI', 'colunaTitulo' => '% IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true ),
			//array('colunaValor' => 'T296_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true ),
			//array('colunaValor' => 'T296_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true ),
			//array('colunaValor' => 'T296_Valor_Adicao', 'colunaTitulo' => 'Adição', 'colunaEditar' => true, 'colunaAlinhar' => 'center' ),
			//array('colunaValor' => 'T296_Flag_Frete', 'colunaTitulo' => 'Frete', 'colunaEditar' => true, 'colunaAlinhar' => 'center' ),
		);

		
		/**
		* Definição da SQL
		*/
		$from = 'T296';
		
		$extra = <<<EOT
			left join T295 on T295_Id=T296_T295_Id
			left join D024 as FORNECEDOR on FORNECEDOR.D024_Id=T295_D024_Id
			left join D119 on D119_Id=T295_D119_Id
			left join D118 on D118_Id=T295_D118_Id
			left join D044 on D044_Id=T295_D044_Id
			left join C007 on C007_Id=T295_C007_Id
			left join D009 on D009_Id=T296_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D005 on D005_Id=T296_D005_Id
			WHERE T295_C004_Id='{$g['empresaAtual']}'
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
			'FORNECEDOR.D024_Nome_Empresa' => array('titulo' => 'Exportador', 'tipo' => 'texto'),
			'T295_Numero_DI' => array('titulo' => 'Número DI', 'tipo' => 'numero'),
			'D118_Nome' => array('titulo' => 'Porto', 'tipo' => 'texto'),
			'T295_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T296_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'T296_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
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
			array('nome' => 'Preco_Total', 'valor' => 'T296_Valor_Total_Custo', 'titulo' => 'Total ME'),
			array('nome' => 'Peso_Total', 'valor' => 'T296_Peso_Kg_Liquido_Total', 'titulo' => 'Peso total'),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Botões (Versão Acima do GRID)
		*/
		/*if (!personalizacaoTotais()) {
			$contador = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			$precoTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T296_Valor_Total_Custo)", $from, $extra));
            $pesoTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T296_Peso_Kg_Liquido_Total) Total", $from, $extra));

			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" value="{$contador}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Total ME<br><input type="text" value="{$precoTotal}" class="whiteBg" id="" size="11" readonly></span>
                    <span class="item" style="text-align:right">Peso total<br><input type="text" value="{$pesoTotal}" class="whiteBg" id="" size="11" readonly></span>
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

		/**
		* Botões
		* Obs.: os IDs dos botões devem possuir nomes únicos
		* Obs2.: os botões são 'movidos' automaticamente para a parte inferior do elemente TAB caso fornecido um ID (botoesId = divId)
		*/
		$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="imp005FormDIBtnIncluir">Inserir</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'imp005GridProduto':

		$importacaoDados = array('T296_T295_Id' => $r_acaoId);

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T296_Id', $g['divId']), 'colunaExibir' => false),
			array('colunaValor' => 'T296_Flag_Excluir',  'colunaTitulo' => '', 'callback' => 'checkboxExcluirItemPO'),
			array('colunaValor' => 'T296_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T296_Valor_Adicao', 'colunaTitulo' => 'Adição', 'colunaEditar' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T296_Adicao_Item', 'colunaTitulo' => 'Item', 'colunaEditar' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T296_T295_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T296_D009_Id', 'colunaTitulo' => 'Produto', 'colunaExibir' => false),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
            array('colunaValor' => 'T296_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;font-size:12px', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
			array('colunaValor' => 'T296_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Cód.Forn', 'colunaEditar' => true),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T296_Descricao_Produto', 'colunaTitulo' => 'Descrição Inglês', 'colunaEditarOculto' => true),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T296_Peso_Kg_Liquido_Unitario', 'colunaTitulo' => 'Peso Kg unit', 'colunaEditar' => true, 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T296_Peso_Kg_Liquido_Total', 'colunaTitulo' => 'Peso Kg total', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T296_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T296_Quantidade_Saldo', 'colunaTitulo' => 'Saldo','colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T296_Valor_Custo_Unitario', 'colunaTitulo' => 'Unitário ME', 'colunaCasasDecimais' => '7', 'colunaAlinhar' => 'right', 'colunaEditar' => true),
            array('colunaValor' => 'T296_Valor_Total_Custo', 'colunaTitulo' => 'Total ME', 'tipo' => 'moeda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T296_Valor_Preco_Unitario_Nota_Fiscal', 'colunaTitulo' => 'NFe unit R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'T296_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo unit R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#bbdcf7'),
			array('colunaValor' => 'T296_Valor_Custo_Unitario_ME_Origem', 'colunaTitulo' => 'Uni. ME Origem', 'colunaCasasDecimais' => '7', 'colunaAlinhar' => 'right', 'colunaEditar' => true,'style' => 'background:#f7c0c5', 'colunaExibir' => false),
            array('colunaValor' => 'T296_Valor_Custo_Total_ME_Origem', 'colunaTitulo' => 'Total ME Origem', 'tipo' => 'moeda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right','style' => 'background:#f7c0c5','colunaExibir' => false),
			array('colunaValor' => 'T296_T066_Id', 'colunaTitulo' => 'Local', 'callback' => 'T296_T066_Id_Local', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T296_Quantidade_Container', 'colunaTitulo' => 'Qtde Container', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
            array('colunaValor' => 'T296_Quantidade/T296_Quantidade_Container', 'colunaTitulo' => 'Total Container', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
            //array('colunaValor' => 'T296_Valor_Total_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            //array('colunaValor' => 'T296_Valor_Total_Seguro', 'colunaTitulo' => 'Seguro R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T296_Valor_Total_Despesas', 'colunaTitulo' => 'Despesas R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            //array('colunaValor' => 'T296_Valor_Total_Despesas_Aduaneira', 'colunaTitulo' => 'SISCOMEX R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            //array('colunaValor' => 'T296_Valor_Aduaneira', 'colunaTitulo' => 'THC R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            //array('colunaValor' => 'T296_Valor_Total_AFRMM', 'colunaTitulo' => 'AFRMM R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T296_Valor_Imposto_Importacao', 'colunaTitulo' => 'II R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T296_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T296_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T296_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T296_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T296_Valor_ICMS_Presumido', 'colunaTitulo' => 'ICMS Pres. R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T296_Valor_ICMS_Pagar', 'colunaTitulo' => 'ICMS Pagar R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            //array('colunaValor' => 'T296_Valor_Total_Multa', 'colunaTitulo' => 'Multa R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            //array('colunaValor' => 'T296_Valor_Juros_Multa', 'colunaTitulo' => 'Juros R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            //array('colunaValor' => 'T296_Valor_Dumping', 'colunaTitulo' => 'Dumping R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'T296_Aliquota_Imposto_Importacao', 'colunaTitulo' => '%II', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T296_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T296_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T296_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T296_Aliquota_ICMS', 'colunaTitulo' => '%ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T296_Aliquota_ICMS_Presumido', 'colunaTitulo' => '%ICMS Recolher', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            //array('colunaValor' => 'T296_Percentual_Acrescimo_Impostos', 'colunaTitulo' => '%Acréscimo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right', 'colunaEditarTodos'=>true),
            array('colunaValor' => 'T296_Valor_Base_Imposto_Importacao', 'colunaTitulo' => 'Base II R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T296_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T296_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T296_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T296_Valor_Base_ICMS_Sem_Reducao', 'colunaTitulo' => 'Base ICMS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background: var(--celula-amarelo);;'),
            array('colunaValor' => 'T296_Valor_Base_ICMS_Normal', 'colunaTitulo' => 'Base ICMS R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            //array('colunaValor' => 'T296_Flag_Calculo_Manual', 'colunaTitulo' => 'M', 'callback' => 'gCheckbox', 'callbackParameter' => array('T296_Flag_Calculo_Manual', 'N|S'),'colunaAlinhar' => 'center', 'style' => 'background:#f7c0c5'),
            //array('colunaValor' => 'T296_Flag_Frete', 'colunaTitulo' => 'Frete', 'colunaEditar' => true, 'colunaAlinhar' => 'center', 'style' => 'background:#f7c0c5'),
            //array('colunaValor' => 'Pedidos', 'colunaTitulo' => 'Qte. Pedidos', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback' => 'campoPedidos', 'colunaExibir' => false),
            //array('colunaValor' => 'Preco', 'colunaTitulo' => 'Vlr Liq', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback' => 'campoPrecoLiquido', 'colunaExibir' => false),
            //array('colunaValor' => 'PrecoBruto', 'colunaTitulo' => 'Vlr Brt', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback' => 'campoPrecoBruto', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            //array('colunaValor' => 'T296_Cotacao_CC', 'colunaTitulo' => 'Cotação CC', 'colunaExibir' => false, 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            //array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir' => false, 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T006_T005_Id', '1000,615')),
			array('colunaValor' => 'T296_Flag_Finalizar_Compra', 'colunaTitulo' => 'Finalizar Compra', 'callback' => 'gCheckbox', 'callbackParameter' => array('T296_Flag_Finalizar_Compra', 'N|S'),'colunaAlinhar' => 'center', 'style' => 'background:#f7c0c5'),
			array('colunaValor' => 'T296_Observacao_Divergencia', 'colunaTitulo' => 'Obs Divergência','colunaAlinhar' => 'left', 'colunaEditarOculto' => true),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'LocalOculto', 'colunaExibir' => false),
			array('colunaValor' => 'T296_Flag_Produto_Divergencia', 'colunaTitulo' => 'T296_Flag_Produto_Divergencia', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T296';
		
		$extra = <<<EOT
			LEFT JOIN D009 ON D009_Id=T296_D009_Id
			LEFT JOIN T066 ON T066_Id=T296_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN D005 ON D005_Id=T296_D005_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN T225 ON T225_Id=T296_T225_Id
			LEFT JOIN T219 ON T219_Id=T225_T219_Id
			LEFT JOIN T006 ON T006_Id=T219_T006_Id
			LEFT JOIN T270 ON T270_Id=T296_T270_Id
			WHERE T296_T295_Id='{$r_acaoId}'
EOT;	

		function gridLinha($array) {
			if($array['todosCampos']['T296_Flag_Produto_Divergencia'] == 'S'){
				return 'linhaFundoAmareloClaro';    
			}  else if($array['todosCampos']['T296_Flag_Finalizar_Compra'] == 'S'){
				return 'linhaVermelhoClaro';    
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
				var id = $(this).closest('tr').attr('id').replace(/T296_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
			});
			} </script></div>
EOT;
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		// Chamando o usleep, tem alguns casos o grid carregava antes do form e desconfigurava a tela
        usleep(500000);

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
		* Botões
		* Obs.: os IDs dos botões devem possuir nomes únicos
		* Obs2.: os botões são 'movidos' automaticamente para a parte inferior do elemente TAB caso fornecido um ID (botoesId = divId)
		*/
		$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="imp005FormDIBtnIncluir">Inserir</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'pedidosProduto':		
		
	$sql = mysql_query("select T296_D009_Id,T296_T295_Id from T296 where T296_Id='{$r_acaoId}'");
	$T296 = mysql_fetch_array($sql);	
		
    //$sql = mysql_query("select distinct T006_T005_Id from T006 where T006_D009_Id='{$T296['T296_D009_Id']}' and T006_T295_Id='{$T296['T296_T295_Id']}'");
    //$row = mysql_fetch_array($sql);
    
    
	/**
	 * Definição do grid tabela
	 */
	$grid = array(		
		array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' =>'Data', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'T005_Id', 'colunaTitulo' =>'Nº Pedido', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' =>'Cliente'),		
		array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' =>'Quantidade', 'colunaAlinhar' => 'right'),		
		array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' =>'Separado', 'colunaAlinhar' => 'right'),
/*
 		array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' =>'IPV', 'callback' => 'validaIpvPedidos', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'T006_Flag_Tipo_Produto', 'colunaTitulo' =>'Tp', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' =>'Quantidade', 'colunaAlinhar' => 'right'),		
		array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' =>'Estoque', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' =>'IPV', 'callback' => 'validaIpvPedidos', 'colunaAlinhar' => 'right'),	
		array('colunaValor' => 'group_concat(distinct T007_Id) as NF', 'colunaTitulo' =>'NF'),
*/		
		array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' =>'Vendedor'),
		array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as Status', 'colunaTitulo' =>'Status'),
		array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		//array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' =>'', 'colunaExibir'=>false),
	);


	$from = 'T005';

	$extra = <<<EOT
		    left join D024 on D024_Id=T005_D024_Id
		    left join T006 on T006_T005_Id=T005_Id
		    left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
	        where T006_Id in (select T006_Id from T006 where T006_D009_Id={$T296['T296_D009_Id']} and T006_T295_Id={$T296['T296_T295_Id']})	
		    and T005_Flag_Status!=5
		    and T006_Flag_Status!='3'
		    and T005_Flag_Status!='7'
		    and T005_Flag_Status!='8'
		    and T005_Flag_Status!='11'	
		                    

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
	$g['smarty']->display('grid.tpl');

break;
	
	/**
	* @author AndreySliwinski
	* @since 22/11/2011
	*/                 
	case 'imp005GridAdicoes':
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'floor(T296_Valor_Adicao) as T296_Valor_Adicao', 'colunaTitulo' => 'Adição', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'count(*) as itens', 'colunaTitulo' => 'itens', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'sum(T296_Valor_Total_Custo*T295_Cotacao_ME) as T296_Valor_Total_Custo', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T296_Valor_Total_Frete) as T296_Valor_Total_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T296_Valor_Aduaneira) as T296_Valor_Total_Aduaneira', 'colunaTitulo' => 'THC', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T296_Valor_Seguro) as T296_Valor_Seguro', 'colunaTitulo' => 'Seguro', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(((T296_Valor_Total_Custo+T296_Valor_Total_Frete)*T295_Cotacao_ME)+T296_Valor_Aduaneira) as Subtotal', 'colunaTitulo' => 'Sub-total', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'T296_Aliquota_Imposto_Importacao', 'colunaTitulo' => '% II', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'sum(T296_Valor_Imposto_Importacao) as T296_Valor_Imposto_Importacao', 'colunaTitulo' => 'II R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T296_Aliquota_IPI', 'colunaTitulo' => '% IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'sum(T296_Valor_IPI) as T296_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'T296_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'sum(T296_Valor_PIS) as T296_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'T296_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'sum(T296_Valor_COFINS) as T296_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T296_Valor_ICMS) as T296_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T296_Valor_ICMS_Presumido) as T296_Valor_ICMS_Presumido', 'colunaTitulo' => 'ICMS Pres. R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T296_Valor_ICMS_Pagar) as T296_Valor_ICMS_Pagar', 'colunaTitulo' => 'ICMS Pagar R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T296_Peso_Kg_Liquido_Unitario) as T296_Peso_Kg_Liquido_Unitario', 'colunaTitulo' => 'Peso Liq', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right' ),
		);

		
		/**
		* Definição da SQL
		*/
		$from = 'T296';
		
		$extra = <<<EOT
			left join D009 on D009_Id=T296_D009_Id
			left join D005 on D005_Id=T296_D005_Id
			left join T295 on T295_Id=T296_T295_Id
			WHERE T296_T295_Id='{$r_acaoId}'
			GROUP BY T296_Valor_Adicao
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
		* Botões
		* Obs.: os IDs dos botões devem possuir nomes únicos
		* Obs2.: os botões são 'movidos' automaticamente para a parte inferior do elemente TAB caso fornecido um ID (botoesId = divId)
		*/
		$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="imp005FormDIBtnIncluir">Inserir</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'imp005GridDocumentos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T155_Id', $g['divId'])),
			array('colunaValor' => 'T155_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T155_T295_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
			array('colunaValor' => 'concat(T155_Nome_Arquivo,if(ifnull(T155_T168_Id,0)>0,concat(" (Despesa - id:",T155_T168_Id,")"),""))', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'T155_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T155_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
			);

			$mostrarDocDespesa = $g['C031']['MostrarDocDispesaNaDI'];	
			if($mostrarDocDespesa=='N'){
				$filtro="and ifnull(T155_T168_Id,0)=0";
			}

			/**
			*
			* Definição da SQL
			*/
			$from = 'T155';

			$extra = <<<EOT
				left join C007 on C007_Id=T155_C007_Id
				where 
				(T155_T295_Id = {$r_acaoId})
				{$filtro}


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
					var id = $(this).closest('tr').attr('id').replace(/T155_Id-/, '');
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

		case 'imp005GridDocumentosDespesa':

			$T168_Id = isset($r_T168_Id) ? $r_T168_Id : false;
			$T155_T295_Id = isset($r_T155_T295_Id) ? $r_T155_T295_Id : false;

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T155_Id', $g['divId'])),
			array('colunaValor' => 'T155_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T155_T295_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
			array('colunaValor' => 'T155_Nome_Arquivo', 'colunaTitulo' => 'Arquivo'),
			array('colunaValor' => 'T155_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T155_Data_Hora_UpLoad', 'colunaTitulo' => 'Data UpLoad', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T155';

			$extra = <<<EOT
				left join C007 on C007_Id=T155_C007_Id
				where 
				(T155_T168_Id = {$T168_Id})
				
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
					var id = $(this).closest('tr').attr('id').replace(/T155_Id-/, '');
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
		
		case 'imp005GridChecklist':
		
		/**
		* Executa o script de inserção na tabela T161 (n:m)
		*/
		$IMP005->insereRegistrosT161($r_acaoId);
		 		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T161_Id', $g['divId'])),
			array('colunaValor' => 'D122_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T161_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D122_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T161_Flag_Ok', 'colunaTitulo' => 'Ok', 'callback' => 'campoCheckboxChecklist'),
			array('colunaValor' => 'T161_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaEditar' => true),
			array('colunaValor' => 'T161_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true),
			array('colunaValor' => 'auditoriaDI', 'colunaTipo' => 'livre', 'callback' => 'auditoriaDI',  'style' => 'width: 1%;'),			
		);

		/**
		* Definição da SQL
		*/
		/*
		* put your comment there...
		* $sql="SELECT D106_Tipo FROM D106 WHERE D106_Id='{$r_acaoId}'";
		
		$resultado=mysql_query($sql);
		$row=mysql_fetch_assoc($resultado);
		*/
		$from = 'D122';
		$extra = <<<EOT
			left join T161 on T161_D122_Id=D122_Id AND T161_T295_Id='{$r_acaoId}'
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

		//where D122_Tipo = '{$row['D106_Tipo']}'
		
		// apenas para o grid não colar nas tabs
		/*
		echo <<<EOT
			<div class="buttonsBottomTop" style="margin-top: 12px">
				<span style="padding: 60px;" />
				<div style="clear:both;"></div>
			</div>
EOT;
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/*
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/*
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
					var chavePrimaria = 'D122_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Checklist - ID: ' + acaoId, '/cad/cad036/content/cad036ContentCadastroChecklistDeBens/', '&acaoId=' + encodeURIComponent(acaoId), [700,500]);
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
		
	/**
	* @author AndreySliwinski
	* @since 24/01/2012
	*/                 
	case 'imp005GridDespesas':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T300_Id', $g['divId'])),
			array('colunaValor' => 'T300_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => "width: 1%;"),
			array('colunaValor' => 'T300_T295_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T300_D190_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T300_D191_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T300_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaEditarJSCallback' => 'colunaEditarCallbackJS_T300_Ordem', 'colunaEditar' => true),
			array('colunaValor' => 'D190_Tipo_Despesa', 'colunaTitulo' => 'Pacote'),
			array('colunaValor' => 'D191_Descricao_Despesa', 'colunaTitulo' => 'Despesa'),
			array('colunaValor' => 'T300_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData','colunaAlinhar' => 'center'),
			array('colunaValor' => 'T300_Flag_Tipo_Rateio', 'colunaTitulo' => 'Rateio','colunaAlinhar' => 'center','callback' => 'tipoRateio'),
			array('colunaValor' => 'T300_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'tipoDespesa'),
			array('colunaValor' => 'T300_Aliquota', 'colunaTitulo' => 'Aliquota', 'colunaCasasDecimais' => '2','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T300_Flag_ME', 'colunaTitulo' => 'ME','colunaAlinhar' => 'center'),
			array('colunaValor' => 'T300_Cotacao_ME', 'colunaTitulo' => 'Cot.ME', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaEditar' => true, 'colunaCasasDecimais' => '4'),
			array('colunaValor' => 'T300_Valor', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true),			
			array('colunaValor' => 'T300_Flag_Soma_Base_IPI', 'colunaTitulo' => 'Soma B.IPI','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T300_Flag_Soma_Base_ICMS', 'colunaTitulo' => 'Soma B.ICMS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T300_Flag_Soma_Base_PIS', 'colunaTitulo' => 'Soma B.PIS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T300_Flag_Soma_Base_COFINS', 'colunaTitulo' => 'Soma B.COFINS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T300_Flag_Soma_Base_II', 'colunaTitulo' => 'Soma B.II','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T300_Flag_Soma_Custo', 'colunaTitulo' => 'Soma no Custo','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T300_Flag_Soma_Preco_NFe', 'colunaTitulo' => 'Soma R$ NFe','colunaAlinhar' => 'center','colunaEditar' => true),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T300';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join D190 on D190_Id=T300_D190_Id
			left join D191 on D191_Id=T300_D191_Id
			WHERE T300_T295_Id={$r_acaoId}
			ORDER BY T300_Ordem
		
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'imp005FormCadastroDespesas'),
		);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="imp005FormCadastroDespesas">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp005FormCadastroDespesas').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Despesas', '/imp/imp005/content/imp005ContentDespesasIncluir/', '&tab=geral&T295_Id=' + {$r_acaoId}, [550,480]);
				});

				function colunaEditarCallbackJS_T300_Ordem(T300_Id, campoEditado, valorAntigo, novoValor) {
					T300_Id = T300_Id.split('|');
					T300_Id = T300_Id[0];
					T300_Id = T300_Id.replace(/T300_Id-/, '');
					$.getJSON('/imp/imp005/grid_func-ajax/processarOrdemDespesa/?ajax=true&T300_Id='    + encodeURIComponent(T300_Id) + 
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
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D191_Descricao_Despesas' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
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
					var chavePrimaria = 'T300_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Despesas - ID: ' + acaoId, '/imp/imp005/content/imp005ContentDespesasIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [550,550]);
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
	
	case 'imp005GridContasReceber':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoReceber'),
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
			array('colunaValor' => "T002_Numero_Documento", 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
			array('colunaValor' => "T002_T035_Id", 'colunaTitulo' => 'Rm', 'callback' => 'validaCampoRm', 'colunaExibir'=>false),
			array('colunaValor' => "CampoBx", 'colunaTipo' => "livre", 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx', 'colunaExibir'=>false),
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaExibir'=>false),
			array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'N.Dupl.'),
			array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "datediff(T002_Data_Vencimento,T002_Data_Emissao) Prazo", 'colunaTitulo' => 'Prz'),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) Atraso", 'colunaTitulo' => 'Ats'),
			array('colunaValor' => "T002_Data_Recebimento", 'colunaTitulo' => 'Recebimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T002_Valor_Duplicata", 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Juros", 'colunaTitulo' => 'Juros','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Desconto", 'colunaTitulo' => 'Desconto','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Recebido", 'colunaTitulo' => 'Recebido','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Total", 'colunaTitulo' => 'Total','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "T002_Valor_Saldo", 'colunaTitulo' => 'Saldo','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => "D027_Portador", 'colunaTitulo' => 'Portador'),
			array('colunaValor' => "T117_Nosso_Numero", 'colunaTitulo' => 'Nosso Número'),
			array('colunaValor' => "T002_Observacao", 'colunaTitulo' => 'Observação'),
			array('colunaValor' => "C007_Nome", 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => "IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta) as D014_SubConta", 'colunaTitulo' => 'Conta'),
			array('colunaValor' => "D046_Nome", 'colunaTitulo' => 'Grupo'),
			array('colunaValor' => "T002_T045_Id", 'colunaTitulo' => 'Lote'),
			array('colunaValor' => "T002_Flag_Baixar_Lote", 'colunaTitulo' => 'Lote', 'colunaExibir' => false),
			array('colunaValor' => "T002_Flag_Status", 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
			array('colunaValor' => "T002_Agrupamento", 'colunaTitulo' => 'Agrupamento', 'colunaExibir' => false),
			array('colunaValor' => "T002_Flag_Enviar_Banco", 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => "D024_Nome_Fantasia", 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor' => "T117_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'colunaExibir' => false),
			array('colunaValor' => "T002_Flag_Reparcelar", 'colunaTitulo' => 'Reparcelar', 'colunaExibir' => false),
			array('colunaValor' => "T002_Flag_Cancelada", 'colunaTitulo' => 'Cancelada', 'colunaExibir' => false),
			array('colunaValor' => "T035_Data_Processamento", 'colunaTitulo' => 'Processamento', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'T002';
		
		$extra = <<<EOT
			left join D024 on D024_Id=T002_D024_Id
			left join D027 on D027_Id=T002_D027_Id
			left join T117 on T117_Id=T002_T117_Id
			left join D014 on D014_Id=T002_D014_Id
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T035 on T035_Id=T002_T035_Id
			left join T103 on T002_Id=T103_T002_Id
			where (T002_C004_Id={$g['empresaAtual']}
			and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null))
			and T002_T295_Id='{$r_acaoId}'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fin001Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="fin001Incluir">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Receber', '/imp/imp005/content/imp005contentIncluirContasReceber/', '&T295_Id=' + encodeURIComponent('{$r_acaoId}'), [600,500]);
				});			
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'T002_Numero_Duplicata' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T002_Valor_Duplicata' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
			'D027_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'numero'),
			'T117_Nosso_Numero' => array('titulo' => 'Nosso Numero', 'tipo' => 'numero'),
			'C007_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'T002_T035_Id' => array('titulo' => 'Remessa', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
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
		 * Totalizadores
		 */
		$valorTotalRecebido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Recebido,0))", $from, $extra));
		$valorTotalAtrasado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0))", $from, $extra));
		$valorAReceber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00' and T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0))", $from, $extra));
		$valorTotalAReceber = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0))", $from, $extra));
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
 		/**
		* Botões (Versão Acima do GRID)
		*/
		// $extra_ = base64_encode($extra);
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTitulos}" size="11" readonly></span>
				// <span class="item" style="text-align:right">Recebido<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorTotalRecebido}" size="11" readonly></span>
				// <span class="item" style="text-align:right">Atrasado<br><input type="text" class="whiteBg" id="inputAtrasado" value="{$valorTotalAtrasado}" size="11" readonly></span>
				// <span class="item" style="text-align:right">A Receber<br><input type="text" class="whiteBg" id="inputAReceber" value="{$valorAReceber}" size="11" readonly></span>
				// <span class="item" style="text-align:right">Total Receber<br><input type="text" class="whiteBg" id="inputTotalReceber" value="{$valorTotalAReceber}" size="11" readonly></span>
				// <span class="item" style="width:10px;border-right:1px solid lightblue"> </span>
				// <span class="item" style="width:10px;"> </span>
				// <span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" size="11" readonly></span>
				// <span class="item" style="text-align:right">Reparcelar<br><input type="text" class="whiteBg" id="inputReparcelar" size="11" readonly></span>
				// <span class="item" style="text-align:right">Agrupar<br><input type="text" class="whiteBg" id="inputAgrupar" size="11" readonly></span>
				// <span class="item" style="text-align:right">Remessa<br><input type="text" class="whiteBg" id="inputRemessa" size="11" readonly></span>
				// <div id="fin001ExtraBtns" style="clear:both;"></div>
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#fin001ExtraBtns').data('extra', '{$extra_}');
				// carregaInputBaixar();
				// carregaInputReparcelar();
				// carregaInputAgrupar();
				// carregaInputRemessa();
			// } </script></div>
// EOT;
	  
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/imp/imp005/content/imp005contentIncluirContasReceber/', '&acaoId=' + encodeURIComponent(id), [600,500]);
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
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); };

	break;

	case 'imp005GridImportarItensOC':
		$grid = array(
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Emissão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T225_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'IFNULL(T225_Quantidade,0)-IFNULL(T225_Quantidade_Dividido,0)', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => ' background:lightblue'),
            array('colunaValor' => 'T225_Quantidade_Dividir', 'colunaTitulo' => 'Quantidade DI', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => ' background:#caffe4', 'colunaEditar' => true),
            array('colunaValor' => 'T225_Codigo_Produto', 'colunaTitulo' => 'Codigo', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T225_Codigo_Substituto', 'colunaTitulo' => 'Cod.Forn', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T225_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
        );

		/**
		*
		* Definição da SQL
		*/
		$from = 'T225';

		$extra = <<<EOT
			left join T224 on T224_Id=T225_T224_Id
			left join D024 on D024_Id=T224_D024_Id
			left join D037 on D037_Id=T225_D037_Id
			left join D009 on D009_Id=T225_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			WHERE D024_Id = '{$r_D024_Id}'
			AND IFNULL(T225_Quantidade,0)-IFNULL(T225_Quantidade_Dividido,0) > 0
			AND T224_Flag_Tipo = 1
			GROUP BY T225_Id			
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'texto'),
			'T225_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T225_Codigo_Produto' => array('titulo' => 'Codigo', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		function gridLinha($array) {
			if(!empty($array['todosCampos']['T013_Numero_Nota_Fiscal'])){
		    	return 'linhaCinza';  
			} else if($array['todosCampos']['T225_Flag_Cancelada']=='S'){
		    	return 'linhaVermelho';  
			} else if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		    	return 'linhaVermelho';  
			}
		}
			

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

	case 'imp005GridLI':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T270_Id', $g['divId'])),
			array('colunaValor' => 'T270_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T270_T295_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T270_Numero_LI', 'colunaTitulo' => 'Número LI'),
			array('colunaValor' => 'T270_Numero_Processo_Anuente', 'colunaTitulo' => 'Processo Anuente'),
			array('colunaValor' => 'T270_Sigla_Orgao_Anuente', 'colunaTitulo' => 'Órgão Anuente'),
			array('colunaValor' => 'T270_Regime_Acordo_Tributario', 'colunaTitulo' => 'Regime Acordo Tributário'),
			array('colunaValor' => 'T270_Tipo_Acordo_Tarifario', 'colunaTitulo' => 'Tipo Acordo Tarifário'),
			array('colunaValor' => 'T270_Numero_Dias_Limite_Pagamento', 'colunaTitulo' => 'Número de Dias Limite Pagamento'), 
			array('colunaValor' => 'T270_Codigo_Orgao_Financeiro_Internacional', 'colunaTitulo' => 'Órgão Financeiro Internacional'), 
			array('colunaValor' => 'T270_Codigo_Motivo_Sem_Cobertura', 'colunaTitulo' => 'Motivo Sem Cobertura'), 
			array('colunaValor' => 'T270_Numero_Destaque_NCM', 'colunaTitulo' => 'Destaque NCM'),
			array('colunaValor' => 'T270_Informacoes_Complementares', 'colunaTitulo' => 'Informações Complementares')
		);

		/**
		* Definição da SQL
		*/
		$from = 'T270';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		WHERE T270_T295_Id={$r_acaoId}
		
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'imp005FormCadastroLI'),
		);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="imp005FormCadastroLI">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp005FormCadastroLI').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Licença de Importação', '/imp/imp005/content/imp005ContentLIIncluir/', '&tab=geral&T295_Id=' + {$r_acaoId}, [100,500]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T270_Numero_LI' => array('titulo' => 'Número L.I.', 'tipo' => 'texto'),
			'T270_Numero_Destaque_NCM' => array('titulo' => 'Destaque NCM', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
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
					var chavePrimaria = 'T270_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar LI - ID: ' + acaoId, '/imp/imp005/content/imp005ContentLIIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [600,1500]);
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

	case 'imp005GridGerarDI':

		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => 'T296_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T295_Id', 'colunaTitulo' =>'PO', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T296_Quantidade', 'colunaTitulo' =>'Qte PO', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => ';background:lightblue'),		
			array('colunaValor' => 'T296_Quantidade_Saldo', 'colunaTitulo' =>'Saldo Disponível', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:var(--celula-amarelo-claro)'),		
			array('colunaValor' => 'T296_Quantidade_DI', 'colunaTitulo' =>'Qte nova DI', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T296_Quantidade_DI', 'style' => 'background:#caffe4;'),
			array('colunaValor' => 'T296_Codigo_Produto', 'colunaTitulo' =>'Cod. Produto', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' =>'Descrição'),
			array('colunaValor' => 'T296_Descricao_Produto', 'colunaTitulo' =>'Descrição Inglês'),
			array('colunaValor' => 'T296_Valor_Custo_Unitario', 'colunaTitulo' =>'Valor Unitário', 'colunaAlinhar' => 'right', 'tipo' => 'moeda', 'colunaCasasDecimais' => '7',),
			array('colunaValor' => 'T296_Valor_Total_Custo', 'colunaTitulo' =>'Valor Total', 'tipo' => 'moeda', 'colunaCasasDecimais' => '7', 'colunaAlinhar' => 'right'),
			array('colunaValor' => '(T296_Valor_Custo_Unitario*T296_Quantidade_DI) as Total_DI', 'colunaTitulo' =>'Valor da DI', 'tipo' => 'moeda', 'colunaCasasDecimais' => '7', 'colunaAlinhar' => 'right'),
		);	
	
		$from = 'T296';
	
		$extra = <<<EOT
			LEFT JOIN T295 ON T295_Id=T296_T295_Id
			LEFT JOIN D009 ON D009_Id=T296_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			WHERE T295_D024_Id = '{$D024_Id}' 
			AND T296_Flag_Finalizar_Compra = 'N'
			AND T296_Quantidade_Saldo > 0
			AND T295_Flag_Status != '0'
			AND T295_Flag_Status != '1'
			AND T295_Flag_Status != '2'
		EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T295_Id' => array('titulo' => 'P.O', 'tipo' => 'numero'),
			'T296_Codigo_Produto' => array('titulo' => 'Cod. Produto', 'tipo' => 'texto'),
			'T296_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),			
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Botões
		$botoes = array(
			array('titulo' => 'Gerar DI', 'id' => 'imp005GerarDIPedidoEmpresaBtn'),
			array('titulo' => 'Incluir na DI', 'id' => 'imp005IncluirItensDIBtn'),
			array('titulo' => 'Preencher quantidade', 'id' => 'imp005DefinirQuantidadeBtn'),
			array('titulo' => 'Limpar quantidade', 'id' => 'imp005LimparQuantidadeBtn'),
		);
		echo Botoes::processar($botoes);
		

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
		
		$novoIdTelaForm = uniqid();
		if (empty($r_linhaGridId)) {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {        
					$('#imp005GerarDIPedidoEmpresaBtn').bind('click', function(data) {
						$.getJSON('/imp/imp005/grid_func-ajax/imp005VerificaItensGerarDI/?sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
							if(request.code){
								var \$buttons = {
									"Gerar": function() {
										$('#' + '{$g['divId']}').showLoading();
										$.getJSON('/imp/imp005/grid_func-ajax/imp005GerarDI/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&T295_Id=' + encodeURIComponent('{$r_acaoId}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
											console.log(request.code);
											if (request.code) {
												divRefresh('{$r_divRefresh}', true);
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar DI - ID: ' + request.data +'', '/imp/imp003/content/imp003ContentDiIncluir/', '&acaoId=' + request.data + '&tabela=T075', [1000,610]);   
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
												dialogConfirm('Erro', request.data, \$buttons);
											}
											$('#' + '{$g['divId']}').hideLoading();
											reloadUI('{$g['divId']}');
										});
										$(this).dialog("destroy").remove();
									},
									"Fechar": function() { $(this).dialog("destroy").remove(); } 
								};
								dialogConfirm('Gerar DI', 'Deseja gerar esta DI?', \$buttons);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
						});
					});

					$('#imp005IncluirItensDIBtn').bind('click', function(data) {
						$.getJSON('/imp/imp005/grid_func-ajax/imp005VerificaItensGerarDI/?sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
							if(request.code){
								var \$buttons = {
									"Adicionar": function() {
										var numeroDI = $(this).find('input').val();
										if(!(numeroDI == "")){
											$.getJSON('/imp/imp005/grid_func-ajax/adicionarProdutosDI/?ajax=true&numeroDI=' + numeroDI + '&D024_Id=' + encodeURIComponent('{$D024_Id}') +'&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
												if (request.code) {
													var \$buttons = {
														"Abrir D.I": function() {
															abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar DI - ID: ' + request.T075_Id +'', '/imp/imp003/content/imp003ContentDiIncluir/', '&acaoId=' + request.T075_Id + '&tabela=T075', [1000,610]);
															$(this).dialog("destroy").remove();
														},
														"Fechar": function() { $(this).dialog("destroy").remove(); }
													};
													dialogConfirm('Operação realizada',request.data, \$buttons);
													divRefresh('{$r_divRefresh}', true);
												} else {
													var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
													dialogConfirm('Erro', request.data, \$buttons);
												}
												$('#' + '{$g['divId']}').hideLoading();
												reloadUI('{$g['divId']}');
											});
										}else{
											dialogConfirm('Erro', 'Não foi informado o ID ou número da DI');
										}
										$(this).dialog("destroy").remove();
										$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
									},
									"Cancelar": function() { $(this).dialog("destroy").remove(); } 
								};
								var novoId = dialogConfirm('Incluir itens na DI','Digite o <b>número da DI</b> para adicionar os produtos selecionados.<br /><br />DI: ', \$buttons);
								$('<input>').attr('id', 'numeroDI').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
								return false;
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
						});
					});

					$('#imp005DefinirQuantidadeBtn').bind('click', function(data) {
						console.log('{$g['divId']}');
						var \$buttons = {
							"Quantidade DI": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/imp/imp005/grid_func-ajax/imp005DefinirQuantidadeDI/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
									if (request.code) {
										divRefresh('{$r_divRefresh}', true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#' + '{$g['divId']}').hideLoading();
									reloadUI('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Fechar": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Definir quantidade', 'Deseja definir as quatidades para a DI com base na quantidade da PO?', \$buttons);
					});
					
					$('#imp005LimparQuantidadeBtn').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/imp/imp005/grid_func-ajax/imp005LimparQuantidades/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', "Operação realizada com sucesso!", \$buttons);
										divRefresh('{$r_divRefresh}', true);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#' + '{$g['divId']}').hideLoading();
									reloadUI('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Atanção!', 'Este processo ira zerar a quantidade para a DI. Você tem certeza?', \$buttons);
					});

					function colunaEditarCallbackJS_T296_Quantidade_DI(T296_Id, campoEditado, valorAntigo, novoValor) {
						T296_Id = T296_Id.replace(/T296_Id-/, '');
						$.getJSON('/imp/imp005/grid_func-ajax/imp005validaQuantidadeDI/?ajax=true&T296_Id='    + encodeURIComponent(T296_Id) +
																								'&novoValor='       + encodeURIComponent(novoValor) +
																								'&valorAntigo='        + encodeURIComponent(valorAntigo) +  
																								'&callback=?', function(request) {
							if(request.code != true){
								dialogConfirm('Erro', request.data);
							}
							divRefresh('{$g['divId']}', true, false, false, T296_Id, 'T296_Id');
						});     	
					}
				}
				</script></div>
EOT;
		}

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
		$g['smarty']->display('grid.tpl');
	
	break;

	case 'imp005GridOrdemCompraGrupo':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T295_Id', 'colunaTitulo' => 'P.O', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'T295_D024_Id', 'colunaTitulo' => 'Cod.', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,28)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left', 'style' => 'max-width:220px;min-width:220px;width:220px;white-space:normal;'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'count(distinct T296_Id)', 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T295';
		
		$extra = <<<EOT
			LEFT JOIN T296 ON T295_Id=T296_T295_Id
		    LEFT JOIN D024 ON D024_Id=T295_D024_Id
			LEFT JOIN D018 ON D018_Id=D024_D018_Id
			LEFT JOIN D020 ON D020_Id=D024_D020_Id
			WHERE T295_Grupo = '{$r_T295_Grupo}'
			GROUP BY T295_Id
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
				var id = $(this).closest('tr').attr('id').replace(/T295_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar PO ', '/imp/imp005/content/imp005ContentDiIncluir/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
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

	case 'imp005GridPrincipalAnalise':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'T295_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T295_Id', 'colunaTitulo' => 'ID', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cód', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'T295_Ordem_Compra', 'colunaTitulo' => 'Purchase Order','colunaAlinhar' => 'center'),
            array('colunaValor' => 'T295_Nome_Status T295_Status', 'colunaTitulo' => 'Situação', 'colunaExibir' => false),
            array('colunaValor' => 'T295_Valor_Total', 'colunaTitulo' => 'Total Produtos ME', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Vendedor', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome Vendedor', 'colunaTitulo' => 'Supervisor'),
			array('colunaValor' => 'T295_Flag_Alcada', 'colunaTitulo' => 'Alçada', 'colunaAlinhar'=> 'center'),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T295';

		$extra = <<<EOT
			left join D024 on D024_Id=T295_D024_Id
			left join T296 on T295_Id=T296_T295_Id
			left join D018 on D018_Id=D024_D018_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T295_C007_Id
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			where T295_C004_Id='{$g['empresaAtual']}'
			AND T295_Flag_Status = '1'
			AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'C', T295_Flag_Alcada = 'C', IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (T295_Flag_Alcada = 'C' OR T295_Flag_Alcada = 'S'), 1=1)) 
			GROUP BY T295_Id
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
			'T295_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T295_Id' => array('titulo' => 'ID', 'tipo' => 'numero'),
			'T295_Ordem_Compra' => array('titulo' => 'Purchase Order', 'tipo' => 'texto'),
			'T295_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
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
				var id = $(this).closest('tr').attr('id').replace(/T295_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + id, '/imp/imp005/content/imp005ContentDiIncluir/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}');
			});
		} </script></div>
		<div id="gridAnalisePO" style="width: 0; height: 0; border: 0; padding: 0; margin: 0;"></div>
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
}
