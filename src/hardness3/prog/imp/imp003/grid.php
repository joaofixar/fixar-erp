<?php
namespace hardness;

// Carrega funções callback
require_once('prog/cad/cad016/grid_func-callback.php');
require_once('prog/imp/imp003/grid_func-callback.php');
// Carrega funções do ajax
require_once('prog/imp/imp003/grid_func-js.php');
require_once('bibliotecas/classes/IMP003.php');
$IMP003 = new IMP003();

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 22/11/2011
	*/
	case 'imp003GridDi':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            //array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T075_Id', $g['divId']), 'style' => 'width:1%'),
			array('colunaValor' => 'exclusaoDI', 'colunaTipo' => 'livre', 'callback' => 'exclusaoDI', 'colunaExibir' => true, 'style' => 'width:1%'),
			array('colunaValor' => 'T075_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_Flag_MP', 'colunaTitulo' => 'Vender?', 'callback' => 'gCheckbox', 'colunaAlinhar' => 'center', 'callbackParameter' => array('T075_Flag_MP', '0|1'), 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T075_Data_Inicio_Demurrage', 'colunaTitulo' => 'Inicio demurrage', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_Data_Emissao_DI', 'colunaTitulo' => 'Data DI', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_Data_Previsao', 'colunaTitulo' => 'Previsão chegada', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_Data_Estoque', 'colunaTitulo' => 'Data estoque', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_Numero_DI', 'colunaTitulo' => 'Número DI', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_Ordem_Compra', 'colunaTitulo' => 'Purchase Order', 'style' => 'width:1%'),
            array('colunaValor' => 'SUM(T055_Quantidade)', 'colunaTitulo' => 'Total Itens', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'notaFiscal'),
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Data NF-e', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_Data_Embarque', 'colunaTitulo' => 'Data Embarque', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_Data_Licenca_Importacao', 'colunaTitulo' => 'Data LI', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => "(SELECT SUM(T015_Valor_Duplicata) FROM T015 WHERE T015_T075_Id=T075_Id and T015_Data_Pagamento != '0000-00-00' and (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL) and T015_Flag_Cancelada != 'S') as Pago", 'colunaTitulo' => 'Saldo pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => "(SELECT SUM(T015_Valor_Duplicata) FROM T015 WHERE T015_T075_Id=T075_Id and T015_Data_Pagamento = '0000-00-00' and (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL) and T015_Flag_Cancelada != 'S') as Pagar", 'colunaTitulo' => 'Saldo pagar', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T075_D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'FORNECEDOR.D024_Nome_Empresa as Fornecedor', 'colunaTitulo' => 'Fornecedor', 'style' => 'width:1%'),
            array('colunaValor' => 'T075_D119_Id', 'colunaTitulo' => 'Despachante', 'colunaExibir' => false),
            array('colunaValor' => 'T075_D118_Id', 'colunaTitulo' => 'Portos', 'colunaExibir' => false),
            array('colunaValor' => 'T075_Valor_VMLE', 'colunaTitulo' => 'Produtos ME', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T075_Valor_VMLE*T075_Cotacao_ME as Produtos_Real', 'colunaTitulo' => 'Produtos R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'sum(T055_Valor_Total_Preco_Nota_Fiscal) as T055_Valor_Total_Preco_Nota_Fiscal', 'colunaTitulo' => 'Valor NFe R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'sum(T055_Valor_Total_Custo_Final) as T055_Valor_Total_Custo_Final', 'colunaTitulo' => 'Custo R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'checklist', 'colunaTitulo' => 'Checklist', 'callback' => 'geraCheckList', 'colunaTipo' => 'livre', 'style' => 'text-align:center'),
            array('colunaValor' => 'T075_Data_Atualizacao_Custo', 'colunaTitulo' => 'Att. Custo', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaExibir' => false),
			array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXMLDi', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T075_Codigo_URF_Entrada', 'colunaTitulo' => 'URF Entrada', 'colunaExibir' => true),
			array('colunaValor' => 'T075_Codigo_URF_Despacho', 'colunaTitulo' => 'URF Despacho', 'colunaExibir' => true),
			array('colunaValor' => 'T075_Codigo_Incoterms', 'colunaTitulo' => 'Incoterms', 'colunaExibir' => true),
			array('colunaValor' => 'T075_Condicao_Mercadoria', 'colunaTitulo' => 'Mercadoria', 'colunaExibir' => true),
			array('colunaValor' => 'T075_Enquadramento_Material_Usado', 'colunaTitulo' => 'Enquadramento do Material Usado', 'colunaExibir' => true),
			array('colunaValor' => 'T075_Operacao_Enquadramento_Material_Usado', 'colunaTitulo' => 'Operação Enquadramento', 'colunaExibir' => true),
			array('colunaValor' => 'T075_Orgao_Desembaraco', 'colunaTitulo' => 'Orgão Desembaraço', 'colunaExibir' => true, 'colunaEditar'=>true),
			array('colunaValor' => 'IF((SELECT T055_Id FROM T055 WHERE T055_T075_Id = T075_Id AND T055_Flag_Produto_Divergencia = "S" LIMIT 1) > 0, "S", "N") as temDivergencia', 'colunaTitulo' => 'Tem divergência', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T075';
		
		$extra = <<<EOT
			left join D024 as FORNECEDOR on FORNECEDOR.D024_Id=T075_D024_Id
			left join D024 as DESPACHANTE on DESPACHANTE.D024_Id=T075_D119_Id
			left join D119 on D119_Id=T075_D119_Id
			left join D118 on D118_Id=T075_D118_Id
			left join D044 on D044_Id=T075_D044_Id
			left join T055 on T055_T075_Id=T075_Id
			left join C007 on C007_Id=T075_C007_Id
			left join T007 on T007_T075_Id=T075_Id and T007_Flag_Cancelada!='S'
			left join T296 on T296_Id = T055_T296_Id
			left join T295 on T295_Id = T296_T295_Id
			WHERE T075_C004_Id='{$g['empresaAtual']}'
			GROUP BY T075_Id
EOT;
		$extra_totalizacao = <<<EOT
			left join D024 as FORNECEDOR on FORNECEDOR.D024_Id=T075_D024_Id
			left join D024 as DESPACHANTE on DESPACHANTE.D024_Id=T075_D119_Id
			left join D119 on D119_Id=T075_D119_Id
			left join D118 on D118_Id=T075_D118_Id
			left join D044 on D044_Id=T075_D044_Id
			left join T055 on T055_T075_Id=T075_Id
			left join C007 on C007_Id=T075_C007_Id
			left join T007 on T007_T075_Id=T075_Id and T007_Flag_Cancelada!='S'
			WHERE T075_C004_Id='{$g['empresaAtual']}'
EOT;

	/**
	* Função que define classes CSS para cada linha do grid
	*/
	function gridLinha($array) {
		if($array['todosCampos']['temDivergencia'] == 'S'){
			return 'linhaFundoAmareloClaro';
		} else if ($array['todosCampos']['T007_Numero_Nota_Fiscal']>0) {
  	       return 'linhaCinza';
 	    }
	}	
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'imp003FormDIBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="imp003FormDIBtnIncluir">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp003FormDIBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Declaração de Importação', '/imp/imp003/content/imp003ContentDiIncluir/', '&tab=geral', [1000,600]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'FORNECEDOR.D024_Nome_Empresa' => array('titulo' => 'Exportador', 'tipo' => 'texto'),
			'T075_Numero_DI' => array('titulo' => 'Número DI', 'tipo' => 'texto'),
            'T075_Ordem_Compra' => array('titulo' => 'Purchase Order', 'tipo' => 'texto'),
			'D118_Nome' => array('titulo' => 'Porto', 'tipo' => 'texto'),
			'T075_Data_Emissao_DI' => array('titulo' => 'Data DI', 'tipo' => 'data'),
			'T075_Data_Inicio_Demurrage' => array('titulo' => 'Inicio demurrage', 'tipo' => 'data'),
			'T075_Orgao_Desembaraco' => array('titulo' => 'Orgão Desembaraço', 'tipo' => 'texto'),
			'IF((SELECT T055_Id FROM T055 WHERE T055_T075_Id = T075_Id AND T055_Flag_Produto_Divergencia = "S" LIMIT 1) > 0[[virgula]]"S"[[virgula]]"N")' => array('titulo' => 'Divergência', 'tipo' => 'select', 'select' => array(
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
		* Botões (Versão Acima do GRID)
		*/
		/*if (!personalizacaoTotais()) {
			$sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(DISTINCT T075_Id),
													   SUM(T075_Valor_VMLE),
													   SUM(T075_Valor_Frete),
													   SUM(T075_Valor_THC),
													   SUM(T075_Valor_VMLD),
													   SUM(T075_Valor_Imposto_Importacao),
													   SUM(T075_Valor_IPI),
													   SUM(T075_Valor_PIS),
													   SUM(T075_Valor_COFINS),
													   SUM(T075_Valor_ICMS)",
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
					var chavePrimaria = 'T075_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar DI - ID: ' + acaoId, '/imp/imp003/content/imp003ContentDiIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [1300,560]);
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
	case 'imp003GridProdutoDI':

		$filtroObrigatorio = true;
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T055_Id', $g['divId'])),
			array('colunaValor' => 'T075_Data_Emissao_DI', 'colunaTitulo' => 'Data DI', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T075_Numero_DI', 'colunaTitulo' => 'Número DI', 'style' => 'width:1%,'),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF-e', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T075_D024_Id', 'colunaTitulo' => 'Código', 'style' => 'width:1%'),
			array('colunaValor' => 'FORNECEDOR.D024_Nome_Empresa', 'colunaTitulo' => 'Exportador', 'style' => 'width:1%', 'colunaDimensao'=>'S'),
			array('colunaValor' => 'T055_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T055_D009_Id', 'colunaTitulo' => 'Produto', 'colunaExibir'=>false),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'T055_Codigo_Produto', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'T055_Descricao_Produto', 'colunaTitulo' => 'Produto', 'colunaDimensao'=>'S'),
			array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'NCM', 'colunaAlinhar' => 'center', 'colunaDimensao'=>'S'),
			array('colunaValor' => 'T055_Peso_Kg_Liquido_Unitario', 'colunaTitulo' => 'Peso Kg unit', 'colunaEditar' => true, 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T055_Quantidade', 'colunaTitulo' => 'Quantidade','colunaCasasDecimais' => '3', 'colunaEditar' => true),
			//array('colunaValor' => 'T055_Flag_ME', 'colunaTitulo' => 'ME'),
			array('colunaValor' => 'T055_Valor_Custo_Unitario', 'colunaTitulo' => 'Unitário ME', 'colunaCasasDecimais' => '7', 'colunaAlinhar' => 'right', 'colunaEditar' => true ),
			array('colunaValor' => 'T055_Valor_Total_Custo', 'colunaTitulo' => 'Total ME', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaMetrica'=>'S' ),
			array('colunaValor' => 'T055_Valor_Preco_Unitario_Nota_Fiscal', 'colunaTitulo' => 'NFe unit R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'T055_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo unit R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => '' ),
			array('colunaValor' => 'T055_Peso_Kg_Liquido_Total', 'colunaTitulo' => 'Peso Kg total', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right', 'colunaMetrica'=>'S'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
			//array('colunaValor' => 'T055_Valor_Total_Frete', 'colunaTitulo' => 'Frete', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Valor_Total_Despesas', 'colunaTitulo' => 'Despesas', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Valor_Imposto_Importacao', 'colunaTitulo' => 'II R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'T055_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Valor_ICMS_Presumido', 'colunaTitulo' => 'ICMS Pres. R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Valor_ICMS_Pagar', 'colunaTitulo' => 'ICMS Pagar R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Peso_Kg_Liquido_Unitario', 'colunaTitulo' => 'Peso Kg unit', 'colunaEditar' => true, 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right' ),
			//array('colunaValor' => 'T055_Aliquota_Imposto_Importacao', 'colunaTitulo' => '% II', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true ),
			//array('colunaValor' => 'T055_Aliquota_IPI', 'colunaTitulo' => '% IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true ),
			//array('colunaValor' => 'T055_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true ),
			//array('colunaValor' => 'T055_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true ),
			//array('colunaValor' => 'T055_Valor_Adicao', 'colunaTitulo' => 'Adição', 'colunaEditar' => true, 'colunaAlinhar' => 'center' ),
			//array('colunaValor' => 'T055_Flag_Frete', 'colunaTitulo' => 'Frete', 'colunaEditar' => true, 'colunaAlinhar' => 'center' ),
		);

		
		/**
		* Definição da SQL
		*/
		$from = 'T055';
		
		$extra = <<<EOT
			left join T075 on T075_Id=T055_T075_Id
			left join D024 as FORNECEDOR on FORNECEDOR.D024_Id=T075_D024_Id
			left join D119 on D119_Id=T075_D119_Id
			left join D118 on D118_Id=T075_D118_Id
			left join D044 on D044_Id=T075_D044_Id
			left join C007 on C007_Id=T075_C007_Id
			left join T007 on T007_T075_Id=T075_Id and T007_Flag_Cancelada!='S'
			left join D009 on D009_Id=T055_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D005 on D005_Id=T055_D005_Id
			WHERE T075_C004_Id='{$g['empresaAtual']}'
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
			'T075_Numero_DI' => array('titulo' => 'Número DI', 'tipo' => 'numero'),
			'D118_Nome' => array('titulo' => 'Porto', 'tipo' => 'texto'),
			'T075_Data_Emissao_DI' => array('titulo' => 'Data DI', 'tipo' => 'data'),
			'T055_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'T055_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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
			array('nome' => 'Preco_Total', 'valor' => 'T055_Valor_Total_Custo', 'titulo' => 'Total ME'),
			array('nome' => 'Peso_Total', 'valor' => 'T055_Peso_Kg_Liquido_Total', 'titulo' => 'Peso total'),
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
			$precoTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T055_Valor_Total_Custo)", $from, $extra));
            $pesoTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T055_Peso_Kg_Liquido_Total) Total", $from, $extra));

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
		//$botoesDir .= '<button type="submit" id="imp003FormDIBtnIncluir">Inserir</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'imp003GridProduto':

		$importacaoDados = array('T055_T075_Id'=> $r_acaoId);

		$exibirVinculoXML = false;
		$T300 = mysql_query("SELECT T310_Id FROM T310 WHERE T310_T075_Id = '{$r_acaoId}'");
		if(mysql_num_rows($T300)){
			$exibirVinculoXML = true;
		}

		//Para manter a compatibilidade com o modelo antigo da DI
		$T075  = mysql_query("SELECT T075_Modelo_Calculos,T075_Arquivo_XML FROM T075 WHERE T075_Id = '{$r_acaoId}' ");
		$mT075 = mysql_fetch_array($T075);

		$exibir           = ($mT075['T075_Modelo_Calculos'] == 2) ? true : false;
		$valorSiscomex    = ($mT075['T075_Modelo_Calculos'] == 2) ? 'T055_Valor_Total_Siscomex' : 'T055_Valor_Total_Despesas_Aduaneira';
		$valorTHC         = ($mT075['T075_Modelo_Calculos'] == 2) ? 'T055_Valor_Total_THC' : 'T055_Valor_Aduaneira';
		$exibirDesvinular = (empty($mT075['T075_Arquivo_XML'])) ? false : true; 

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T055_Id', $g['divId']), 'colunaExibir' => false),
			array('colunaValor' => 'T055_Flag_Excluir',  'colunaTitulo' => 'Exc', 'callback' => 'checkboxExcluirItemDI'),
            array('colunaValor' => 'T055_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T055A_Id', 'colunaTitulo' => 'T055A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T055_Valor_Adicao', 'colunaTitulo' => 'Adição', 'colunaEditar' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T055_Adicao_Item', 'colunaTitulo' => 'Item', 'colunaEditar' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T270_Numero_LI', 'colunaTitulo' => 'L.I.', 'colunaExibir' => true),
            array('colunaValor' => 'T055_T075_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T055_D009_Id', 'colunaTitulo' => 'Produto', 'colunaExibir' => false),
			array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'Produto', 'colunaExibir' => false),
            array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'OC', 'colunaExibir' => false, 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T225_T224_Id', '990,605', false, false, '&tabela=T224')),
            array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'desvincularProduto', 'colunaTitulo' => '', 'callback' => 'desvincularProduto', 'colunaTipo' => 'livre', 'colunaExibir' => $exibirDesvinular),			
			array('colunaValor' => 'T055_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;font-size:12px', 'callback' => 'vincularProduto'),
			array('colunaValor' => 'produtoDI', 'colunaTitulo' => 'Item XML','colunaTipo' => 'livre', 'callback' => 'verificaProdutoImportadoXML', 'colunaExibir' => $exibirVinculoXML, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T055_Descricao_Produto', 'colunaTitulo' => 'Descrição','callback' => 'coloreLinhaProdutos'),
			array('colunaValor' => 'T055_Descricao_Produto_Ingles', 'colunaTitulo' => 'Descrição Inglês', 'colunaEditarOculto' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T055_Peso_Kg_Liquido_Unitario', 'colunaTitulo' => 'Peso Kg unit', 'colunaEditar' => true, 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Valor_Custo_Unitario', 'colunaTitulo' => 'Unitário ME', 'colunaCasasDecimais' => '7', 'colunaAlinhar' => 'right', 'colunaEditar' => true),
            array('colunaValor' => 'T055_Valor_Total_Custo', 'colunaTitulo' => 'Total ME', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Valor_Preco_Unitario_Nota_Fiscal', 'colunaTitulo' => 'NFe unit R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo unit R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-azul);'),
			array('colunaValor' => 'T296_Valor_Custo_Final_Unitario', 'colunaTitulo' => 'Custo PO unit R$ ', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-azul);', 'colunaExibir' => false),
			array('colunaValor' => '((T055_Valor_Custo_Final_Unitario - T296_Valor_Custo_Final_Unitario)/T055_Valor_Custo_Final_Unitario)*100', 'colunaTitulo' => '%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-azul);', 'colunaExibir' => false),
			array('colunaValor' => 'T055_T066_Id', 'colunaTitulo' => 'Local', 'callback' => 'T055_T066_Id_Local', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T055A_Data_Validade', 'colunaTitulo' => 'Validade', 'callback' => 'gCorrigeData', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T055A_Data_Validade', 'colunaEditar' => true),
			array('colunaValor' => 'T055_Valor_Total_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_Total_Seguro', 'colunaTitulo' => 'Seguro R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
			array('colunaValor' => "{$valorSiscomex} as {$valorSiscomex}", 'colunaTitulo' => 'Siscomex R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
			array('colunaValor' => "{$valorTHC}", 'colunaTitulo' => 'THC R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
			array('colunaValor' => 'T055_Valor_Total_AFRMM', 'colunaTitulo' => 'AFRMM R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_Total_Despesas', 'colunaTitulo' => 'Outras R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
			array('colunaValor' => 'T055A_Valor_Outros_Acrescimos', 'colunaTitulo' => 'Outros Acresc. R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'T055_Valor_Total_Despesas_Aduaneira', 'colunaTitulo' => 'Aduaneiras R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => $exibir),
            array('colunaValor' => 'T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal', 'colunaTitulo' => 'Acessórias R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);','colunaExibir' => $exibir),
            array('colunaValor' => 'T055_Valor_Imposto_Importacao', 'colunaTitulo' => 'II R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_ICMS_Presumido', 'colunaTitulo' => 'ICMS Pres. R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_ICMS_Pagar', 'colunaTitulo' => 'ICMS Pagar R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T055_Valor_Total_Multa', 'colunaTitulo' => 'Multa R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'T055_Valor_Juros_Multa', 'colunaTitulo' => 'Juros R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'T055_Valor_Dumping', 'colunaTitulo' => 'Dumping R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'T055_Aliquota_Imposto_Importacao', 'colunaTitulo' => '%II', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Aliquota_ICMS', 'colunaTitulo' => '%ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Aliquota_ICMS_Presumido', 'colunaTitulo' => '%ICMS Recolher', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055_Percentual_Acrescimo_Impostos', 'colunaTitulo' => '%Acréscimo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-azul);', 'colunaAlinhar' => 'right', 'colunaEditarTodos'=>true),
            array('colunaValor' => 'T055_Valor_Base_Imposto_Importacao', 'colunaTitulo' => 'Base II R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T055_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T055_Valor_Base_PIS', 'colunaTitulo' => 'Base PIS R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T055_Valor_Base_COFINS', 'colunaTitulo' => 'Base COFINS R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T055_Valor_Base_ICMS_Sem_Reducao', 'colunaTitulo' => 'Base ICMS sem red R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true, 'style' => 'background: var(--celula-amarelo);;', 'colunaExibir' => false),
            array('colunaValor' => 'T055_Valor_Base_ICMS_Normal', 'colunaTitulo' => 'Base ICMS Nomral R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T055_Valor_Base_ICMS_Presumido', 'colunaTitulo' => 'Base ICMS Presumido R$', 'colunaCasasDecimais' => '4', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'T055_Flag_Calculo_Manual', 'colunaTitulo' => 'Tipo cálculo', 'callback' => 'campoFlagCalculoManualDI', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho)', 'colunaEditar' => 'S', 'colunaEditarGeraSelect' => array(
                array('title' => 'NORMAL', 'value' => 'N'),
                array('title' => 'DUIMP', 'value' => 'I'),
                array('title' => 'MANUAL', 'value' => 'S')
            )),
            array('colunaValor' => 'T055_Flag_Frete', 'colunaTitulo' => 'Frete', 'colunaEditar' => true, 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho)'),
			array('colunaValor' => 'T055A_Observacao_Divergencia', 'colunaTitulo' => 'Obs Divergência', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'Pedidos', 'colunaTitulo' => 'Qte. Pedidos', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback' => 'campoPedidos', 'colunaExibir' => false),
            array('colunaValor' => 'Preco', 'colunaTitulo' => 'Vlr Liq', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback' => 'campoPrecoLiquido', 'colunaExibir' => false),
            array('colunaValor' => 'PrecoBruto', 'colunaTitulo' => 'Vlr Brt', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center', 'callback' => 'campoPrecoBruto', 'colunaExibir' => false),
            array('colunaValor' => 'T055_Cotacao_CC', 'colunaTitulo' => 'Cotação CC', 'colunaExibir' => false, 'colunaEditarOculto' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Pedido', 'colunaExibir' => false, 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T006_T005_Id', '1000,615')),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'LocalOculto', 'colunaExibir' => false),
			array('colunaValor' => 'T066A_Data_Validade', 'colunaTitulo' => 'T066A_Data_Validade', 'colunaExibir' => false),
			array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'T066_Quantidade_Estoque', 'colunaExibir' => false),
			array('colunaValor' => 'T055_Flag_Produto_Divergencia', 'colunaTitulo' => 'T055_Flag_Produto_Divergencia', 'colunaExibir' => false),
			array('colunaValor' => 'T310_Id', 'colunaTitulo' => 'T310_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T055A_Flag_Item_Importado_XML', 'colunaTitulo' => 'T055A_Flag_Item_Importado_XML', 'colunaExibir' => false),
            array('colunaValor' => 'T055_Valor_Total_Nota_Fiscal', 'colunaTitulo' => 'Total NFe', 'colunaCasasDecimais' => '6', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T055A_Flag_Credita_Estoque', 'colunaTitulo' => 'Credita Estq?', 'callback' => 'gCheckbox', 'callbackParameter' => array('T055A_Flag_Credita_Estoque', 'N|S'), 'colunaAlinhar' => 'center'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T055';
		
		$extra = <<<EOT
			-- JOIN
			LEFT JOIN D009 ON D009_Id=T055_D009_Id
			LEFT JOIN T066 ON T066_Id=T055_T066_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			LEFT JOIN T066A ON T066_Id=T066A_T066_Id
			LEFT JOIN D005 ON D005_Id=T055_D005_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN T225 ON T225_Id=T055_T225_Id
			LEFT JOIN T219 ON T219_Id=T225_T219_Id
			LEFT JOIN T006 ON T006_Id=T219_T006_Id
			LEFT JOIN T270 ON T270_Id=T055_T270_Id
			LEFT JOIN T296 ON T296_Id=T055_T296_Id
			LEFT JOIN T055A ON T055_Id=T055A_T055_Id
			LEFT JOIN T310 ON T055_Id=T310_T055_Id
			-- WHERE
			WHERE T055_T075_Id='{$r_acaoId}'
EOT;	

		function gridLinha($array) {
			if($array['todosCampos']['T055_Flag_Produto_Divergencia'] == 'S'){
				return 'linhaFundoAmareloClaro';    
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

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {        
			function colunaEditarCallbackJS_T055A_Data_Validade(T055A_Id) {
				T055A_Id = T055A_Id.split('|');
				T055A_Id = T055A_Id[1];
				T055A_Id = T055A_Id.replace(/T055A_Id-/, '');
				$.getJSON('/imp/imp003/grid_func-ajax/imp003verificaValidadeItem/?ajax=true&T055A_Id='+ encodeURIComponent(T055A_Id) + '&callback=?', function(request) {																
					if(request.code != true){
						dialogConfirm('Erro', request.data);
					}
					divRefresh('{$g['divId']}');
				});     	
			}
		}
		</script></div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T055_Id-/, '');
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
		//$botoesDir .= '<button type="submit" id="imp003FormDIBtnIncluir">Inserir</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'pedidosProduto':		
		
	$sql = mysql_query("select T055_D009_Id,T055_T075_Id from T055 where T055_Id='{$r_acaoId}'");
	$T055 = mysql_fetch_array($sql);	
		
    //$sql = mysql_query("select distinct T006_T005_Id from T006 where T006_D009_Id='{$T055['T055_D009_Id']}' and T006_T075_Id='{$T055['T055_T075_Id']}'");
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
	        where T006_Id in (select T006_Id from T006 where T006_D009_Id={$T055['T055_D009_Id']} and T006_T075_Id={$T055['T055_T075_Id']})	
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
	case 'imp003GridAdicoes':

		//Para manter a compatibilidade com o modelo antigo da DI
		$T075  = mysql_query("SELECT T075_Modelo_Calculos FROM T075 WHERE T075_Id = '{$r_acaoId}' ");
		$mT075 = mysql_fetch_array($T075);

		$exibir        = ($mT075['T075_Modelo_Calculos'] == 2) ? true : false;
		$valorSiscomex = ($mT075['T075_Modelo_Calculos'] == 2) ? 'T055_Valor_Total_Siscomex' : 'T055_Valor_Total_Despesas_Aduaneira';
		$valorTHC      = ($mT075['T075_Modelo_Calculos'] == 2) ? 'T055_Valor_Total_THC' : 'T055_Valor_Aduaneira';
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'floor(T055_Valor_Adicao) as T055_Valor_Adicao', 'colunaTitulo' => 'Adição', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'count(*) as itens', 'colunaTitulo' => 'itens', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'sum(T055_Valor_Total_Custo*T075_Cotacao_ME) as T055_Valor_Total_Custo', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T055_Valor_Total_Frete)', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'sum(T055_Valor_Total_Seguro)', 'colunaTitulo' => 'Seguro R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => "sum({$valorSiscomex}) as $valorSiscomex", 'colunaTitulo' => 'Siscomex R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => "sum({$valorTHC})", 'colunaTitulo' => 'THC R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
            array('colunaValor' => 'sum(T055_Valor_Total_AFRMM)', 'colunaTitulo' => 'AFRMM R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditarOculto' => true),
            array('colunaValor' => 'sum(T055_Valor_Total_Despesas_Aduaneira)', 'colunaTitulo' => 'Desp.Aduaneiras R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => $exibir),
            array('colunaValor' => 'sum(T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal)', 'colunaTitulo' => 'Desp.Acessórias R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => $exibir),
            array('colunaValor' => 'sum(T055_Valor_Total_Despesas)', 'colunaTitulo' => 'Outras Desp.R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'sum(((T055_Valor_Total_Custo+T055_Valor_Total_Frete)*T075_Cotacao_ME)+T055_Valor_Aduaneira) as Subtotal', 'colunaTitulo' => 'Sub-total', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'T055_Aliquota_Imposto_Importacao', 'colunaTitulo' => '% II', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'sum(T055_Valor_Imposto_Importacao) as T055_Valor_Imposto_Importacao', 'colunaTitulo' => 'II R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T055_Aliquota_IPI', 'colunaTitulo' => '% IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'sum(T055_Valor_IPI) as T055_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'T055_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'sum(T055_Valor_PIS) as T055_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'T055_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'sum(T055_Valor_COFINS) as T055_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T055_Valor_ICMS) as T055_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T055_Valor_ICMS_Presumido) as T055_Valor_ICMS_Presumido', 'colunaTitulo' => 'ICMS Pres. R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T055_Valor_ICMS_Pagar) as T055_Valor_ICMS_Pagar', 'colunaTitulo' => 'ICMS Pagar R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right' ),
			array('colunaValor' => 'sum(T055_Peso_Kg_Liquido_Unitario) as T055_Peso_Kg_Liquido_Unitario', 'colunaTitulo' => 'Peso Liq', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right' ),
		);

		
		/**
		* Definição da SQL
		*/
		$from = 'T055';
		
		$extra = <<<EOT
			left join D009 on D009_Id=T055_D009_Id
			left join D005 on D005_Id=T055_D005_Id
			left join T075 on T075_Id=T055_T075_Id
			WHERE T055_T075_Id='{$r_acaoId}'
			GROUP BY T055_Valor_Adicao
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
		//$botoesDir .= '<button type="submit" id="imp003FormDIBtnIncluir">Inserir</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'imp003GridDocumentos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T155_Id', $g['divId'])),
			array('colunaValor' => 'T155_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T155_T075_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
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
				(T155_T075_Id = {$r_acaoId})
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

		case 'imp003GridDocumentosDespesa':

			$T168_Id = isset($r_T168_Id) ? $r_T168_Id : false;
			$T155_T075_Id = isset($r_T155_T075_Id) ? $r_T155_T075_Id : false;
			log("grid T155_T075_Id=".$T155_T075_Id);
			log("grid documentos T168_Id=".$r_T168_Id);			

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T155_Id', $g['divId'])),
			array('colunaValor' => 'T155_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T155_T075_Id', 'colunaTitulo' => 'DI', 'colunaExibir' => false),
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
		
		case 'imp003GridChecklist':
		
		/**
		* Executa o script de inserção na tabela T161 (n:m)
		*/
		$IMP003->insereRegistrosT161($r_acaoId);
		 		
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
			left join T161 on T161_D122_Id=D122_Id AND T161_T075_Id='{$r_acaoId}'
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
	case 'imp003GridDespesas':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T168_Id', $g['divId'])),
			array('colunaValor' => 'T168_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
			array('colunaValor' => 'T168_T075_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T168_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T168_Valor', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais'=>'2', 'colunaEditar'=> true),
			array('colunaValor' => 'T168_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'T168_Data_Pagamento', 'colunaTitulo' => 'Data Pagamento', 'callback'=>'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'T168_Flag_ME', 'colunaTitulo' => 'ME', 'callback'=>'validaCampoME'),
			array('colunaValor' => 'incluirDespesa', 'colunaTitulo' => '', 'callback' => 'incluirDespesa', 'style' => 'width: 1%', 'colunaTipo' => 'livre'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T168';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		WHERE T168_T075_Id={$r_acaoId}
		
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'imp003FormCadastroDespesas'),
		);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="imp003FormCadastroDespesas">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp003FormCadastroDespesas').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Despesas', '/imp/imp003/content/imp003ContentDespesasIncluir/', '&tab=geral&T075_Id=' + {$r_acaoId}, [500,300]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'T168_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T168_Valor' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
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
					var chavePrimaria = 'T168_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Despesas - ID: ' + acaoId, '/imp/imp003/content/imp003ContentDespesasIncluir/', '&acaoId=' + encodeURIComponent(acaoId) + '&T155_T075_Id=' + {$r_acaoId} + '&T075_Id=' + {$r_acaoId}, [500,300]);
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

	/**
	* @author AndreySliwinski
	* @since 24/01/2012
	*/                 
	case 'imp003GridDespesasNovo':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T168_Id', $g['divId'])),
			array('colunaValor' => 'T168_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
			array('colunaValor' => 'T168_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaEditarJSCallback' => 'colunaEditarCallbackJS_T168_Ordem', 'colunaEditar' => true),
			array('colunaValor' => 'T168_T075_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'D190_Tipo_Despesa', 'colunaTitulo' => 'Pacote', 'style' => 'width: 1%'),
			array('colunaValor' => 'T168_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T168_Tipo_Despesa', 'colunaTitulo' => 'Tipo Despesa','colunaAlinhar' => 'center', 'callback' => 'tipoDespesaComex','style' => 'font-weight:normal;background: var(--celula-amarelo-claro);'),
			array('colunaValor' => 'T168_Tipo', 'colunaTitulo' => 'Tipo','colunaAlinhar' => 'center', 'callback' => 'tipoComex','style' => 'font-weight:normal;background: var(--celula-amarelo-claro);'),
			array('colunaValor' => 'T168_Flag_Tipo_Rateio', 'colunaTitulo' => 'Rateio','colunaAlinhar' => 'center','callback' => 'tipoRateio'),
			array('colunaValor' => 'T168_Flag_Base_Rateio', 'colunaTitulo' => 'Base rateio','colunaAlinhar' => 'center','callback' => 'baseRateio'),
			array('colunaValor' => 'T168_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'tipoDespesa'),
			array('colunaValor' => 'T168_Aliquota', 'colunaTitulo' => 'Aliquota', 'colunaCasasDecimais' => '2','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T168_Flag_ME', 'colunaTitulo' => 'ME','colunaAlinhar' => 'center'),
			array('colunaValor' => 'T168_Cotacao_ME', 'colunaTitulo' => 'Cot.ME', 'colunaAlinhar' => 'center', 'style' => 'width:1%','colunaEditar' => true, 'colunaCasasDecimais' => '4'),
			array('colunaValor' => 'T168_Valor', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true),	
			array('colunaValor' => 'T168_Valor_Confirmado', 'colunaTitulo' => 'Valor Confirmado R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-verde);;'),		
			array('colunaValor' => 'T168_Flag_Soma_Base_IPI', 'colunaTitulo' => 'Soma B.IPI','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T168_Flag_Soma_Base_ICMS', 'colunaTitulo' => 'Soma B.ICMS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T168_Flag_Soma_Base_PIS', 'colunaTitulo' => 'Soma B.PIS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T168_Flag_Soma_Base_COFINS', 'colunaTitulo' => 'Soma B.COFINS','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T168_Flag_Soma_Base_II', 'colunaTitulo' => 'Soma B.II','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T168_Flag_Soma_Custo', 'colunaTitulo' => 'Soma no Custo','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T168_Flag_Soma_Preco_NFe', 'colunaTitulo' => 'Soma R$ NFe','colunaAlinhar' => 'center','colunaEditar' => true),
			array('colunaValor' => 'T168_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'T168_Data_Pagamento', 'colunaTitulo' => 'Data Pagamento', 'callback'=>'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'incluirDespesa', 'colunaTitulo' => '', 'callback' => 'incluirDespesa', 'style' => 'width: 1%', 'colunaTipo' => 'livre'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T168';
		
		$extra = <<<EOT
		LEFT JOIN D190 ON T168_D190_Id = D190_Id
		WHERE T168_T075_Id={$r_acaoId}
		
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'imp003FormCadastroDespesas'),
			array('titulo' => 'Incluir Pacote Despesas', 'id' => 'imp003importarPacoteDespesas'),
			array('titulo' => 'Confirmar Valores Despesas', 'id' => 'imp003confirmarValoresDespesas'),
		);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="imp003FormCadastroDespesas">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp003FormCadastroDespesas').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Despesas', '/imp/imp003/content/imp003ContentDespesasIncluir/', '&tab=geral&T075_Id=' + {$r_acaoId}, [550,480]);
				});

				$('#imp003importarPacoteDespesas').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/imp/imp003/form_func-ajax/imp003verificaPacoteDespesas/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&coleta=S&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Excluir e incluir": function() {
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pacote Despesas', '/imp/imp003/content/imp003ContentPacoteDespesas/', '&tab=geral&excluirDespesas=true&T075_Id=' + {$r_acaoId}, [500,180]);
									$(this).dialog("destroy").remove();
								},
								"Incluir": function() {
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pacote Despesas', '/imp/imp003/content/imp003ContentPacoteDespesas/', '&tab=geral&T075_Id=' + {$r_acaoId}, [500,180]);
									$(this).dialog("destroy").remove();
								},
								"Fechar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Pacote de despesas', 'Existe despesas cadastradas na D.I, oque deseja fazer?', \$buttons);
						} else {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pacote Despesas', '/imp/imp003/content/imp003ContentPacoteDespesas/', '&tab=geral&T075_Id=' + {$r_acaoId}, [500,180]);
						}
					});
				});

				$('#imp003confirmarValoresDespesas').bind('click', function(data) {
					var \$buttons = {
						"Confirmar": function() {
							$('#' + '{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/grid_func-ajax/imp003ConfirmaValoresDespesas/?T075_Id=' + encodeURIComponent('{$r_acaoId}') + '&ajax=true&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Definir quantidade', 'Deseja confirmar os valores das despesas?', \$buttons);
				});

				function colunaEditarCallbackJS_T168_Ordem(T168_Id, campoEditado, valorAntigo, novoValor) {
					T168_Id = T168_Id.split('|');
					T168_Id = T168_Id[0];
					T168_Id = T168_Id.replace(/T168_Id-/, '');
					$.getJSON('/imp/imp003/grid_func-ajax/processarOrdemDespesa/?ajax=true&T168_Id='    + encodeURIComponent(T168_Id) + 
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
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'T168_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T168_Valor' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T168_Tipo_Despesa' => array('titulo' => 'Tipo Despesa', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Outros', 'value' => '1'),
                array('title' => 'THC', 'value' => '2'),
				array('title' => 'Seguro', 'value' => '3'),
				array('title' => 'Siscomex', 'value' => '4'),
				array('title' => 'AFRMM', 'value' => '5'),
				array('title' => 'Frete', 'value' => '6'),
				array('title' => 'Desp.Acessória', 'value' => '7'),
				array('title' => 'Desp.Aduaneira', 'value' => '8'),
              ),
              'naoGerarExtra' => ''),
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
					var chavePrimaria = 'T168_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Despesas - ID: ' + acaoId, '/imp/imp003/content/imp003ContentDespesasIncluir/', '&acaoId=' + encodeURIComponent(acaoId) + '&T155_T075_Id=' + {$r_acaoId} , [550,480]);
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
			array('nome' => 'Valor_Total_Confirmado', 'valor' => 'T168_Valor_Confirmado', 'titulo' => 'Confirmado'),
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
	
	case 'imp003GridContasPagar':
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
			array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
			//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
			array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta) as D014_SubConta', 'colunaTitulo' => 'Conta'),
			array('colunaValor' => 'T015_Numero_Duplicata', 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'T015_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'datediff(T015_Data_Vencimento,T015_Data_Emissao)', 'colunaTitulo' => 'Prazo'),
			array('colunaValor' => 'T015_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T015_Data_Pagamento', 'colunaTitulo' => 'Pagamento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T015_Valor_Duplicata', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Juros', 'colunaTitulo' => 'Juros', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Desconto', 'colunaTitulo' => 'Desconto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Pago', 'colunaTitulo' => 'Pago', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Total', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T015_Valor_Cambio_Moeda', 'colunaTitulo' => 'Cotação ME', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
			array('colunaValor' => 'T015_Valor_Duplicata_Moeda', 'colunaTitulo' => 'Valor ME', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador'),
			array('colunaValor' => 'D073_Nome', 'colunaTitulo' => 'Centro Custo'),
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
				WHERE T015_C004_Id={$g['empresaAtual']}
				AND T015_T075_Id='{$acaoId}'
				AND (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL)
				AND T015_Flag_Cancelada != 'S'
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'fin002Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		//<button type="button" id="fin002Baixar">Baixar</button>
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="fin002Incluir">Incluir</button>
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/imp/imp003/content/imp003contentIncluirContasPagar/', '&T075_Id=' + encodeURIComponent('{$acaoId}'), [600,500]);
				});			
				$('#fin002Baixar').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin002/grid_func-ajax/verificaBaixar/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
				    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Baixar', '/fin/fin002/content/fin002content03/', '', [1100,500]);
				    } else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Baixa de Títulos', request.data, buttons);							
						}
					});
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'numero'),
			'T015_Numero_Duplicata' => array('titulo' => 'Duplicata', 'tipo' => 'numero'),
			'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
			'T015_Valor_Duplicata' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T015_Valor_Pago' => array('titulo' => 'Pago', 'tipo' => 'moeda'),
			'T015_Valor_Total' => array('titulo' => 'Total', 'tipo' => 'moeda'),
			'D031_Portador' => array('titulo' => 'Portador', 'tipo' => 'texto'),
			'D073_Nome' => array('titulo' => 'Centro de Custo', 'tipo' => 'texto'),
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
		$valorTotalPago= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T015_Valor_Pago)", $from, $extra));
		$valorTotalVencido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento<curdate(),T015_Valor_Saldo,0))", $from, $extra));
		$valorAVencer = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(if(T015_Data_Pagamento='0000-00-00' and T015_Data_Vencimento>=curdate(),T015_Valor_Saldo,0))", $from, $extra));
		$valorTotalAPagar = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T015_Valor_Saldo)", $from, $extra));
		$totalTitulos = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="11" readonly></span>
				// <span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" size="11" readonly></span>
				// <span class="item" style="text-align:right">Pago<br><input type="text" class="whiteBg" id="inputPago" size="11" value="{$valorTotalPago}" readonly></span>
				// <span class="item" style="text-align:right">Vencido<br><input type="text" class="whiteBg" id="inputVencido" value="{$valorTotalVencido}" size="11" readonly></span>
				// <span class="item" style="text-align:right">A vencer<br><input type="text" class="whiteBg" id="inputAPagar" value="{$valorAVencer}" size="11" readonly></span>
				// <span class="item" style="text-align:right">Total pagar<br><input type="text" class="whiteBg" id="inputTotalPagar" size="11" value="{$valorTotalAPagar}" readonly></span>
				// <div style="clear:both;"></div>
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				// carregaInputBaixar();
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
				var id = $(this).closest('tr').attr('id').replace(/T015_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + id, '/imp/imp003/content/imp003contentIncluirContasPagar/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,500]);
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
	
	
	case 'imp003GridContasReceber':
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
			array('colunaValor' => "T007_Id", 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false)
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
			left join T007 on T007_Id=T002_T007_Id
			left join T035 on T035_Id=T002_T035_Id
			left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
			left join T103 on T002_Id=T103_T002_Id
			where (T002_C004_Id={$g['empresaAtual']}
			and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null))
			and T002_T075_Id='{$r_acaoId}'
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
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Receber', '/imp/imp003/content/imp003contentIncluirContasReceber/', '&T075_Id=' + encodeURIComponent('{$r_acaoId}'), [600,500]);
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/imp/imp003/content/imp003contentIncluirContasReceber/', '&acaoId=' + encodeURIComponent(id), [600,500]);
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

	case 'imp003GridImportarItensOC':
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

	case 'imp003GridLI':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T270_Id', $g['divId'])),
			array('colunaValor' => 'T270_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T270_T075_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
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
		WHERE T270_T075_Id={$r_acaoId}
		
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'imp003FormCadastroLI'),
		);
		echo Botoes::processar($botoes);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="imp003FormCadastroLI">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp003FormCadastroLI').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Licença de Importação', '/imp/imp003/content/imp003ContentLIIncluir/', '&tab=geral&T075_Id=' + {$r_acaoId}, [100,500]);
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
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar LI - ID: ' + acaoId, '/imp/imp003/content/imp003ContentLIIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [600,1500]);
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

	case 'imp003GridItensImportadosDI':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T310_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T310_T075_Id', 'colunaTitulo' => 'T300_T075_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T310_T055_Id', 'colunaTitulo' => 'T310_T055_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T310_Valor_Adicao', 'colunaTitulo' => 'Adição','colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Adicao_Item', 'colunaTitulo' => 'Item adição','colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Classificacao_Fiscal', 'colunaTitulo' => 'NCM','colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'callback' => 'descricaoItemXML'),
			array('colunaValor' => 'T310_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'left', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Aliquota_Imposto_Importacao', 'colunaTitulo' => '%II', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'), 
		);

		/**
		* Definição da SQL
		*/
		$from = 'T310';
		
		$extra = <<<EOT
		WHERE T310_T075_Id={$r_T075_Id}
		  AND IFNULL(T310_T055_Id,0) = 0 		
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
			'T310_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T310_Valor_Adicao' => array('titulo' => 'Adição', 'tipo' => 'texto'),
			'T310_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
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
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T310_Id = id[0].replace(/T310_Id-/, '');
				var T055_Id = $r_T055_Id;					
				var \$buttons = {
					"Sim": function() {
						$.getJSON('/imp/imp003/grid_func-ajax/associarItemXML/?ajax=true&T055_Id=' + encodeURIComponent(T055_Id) + '&T310_Id=' + encodeURIComponent(T310_Id) + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								// fechar janela e atualizar parent
								dialogConfirm('Ok', request.data);
								$(this).dialog("destroy").remove();
								divRefresh('{$g['divIdParent']}');
								fecharJanela('{$g['divId']}');
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, buttons);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Vincular', 'Você tem certeza que deseja vincular o item? ', \$buttons);					
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

	case 'imp003GridItemVinculadoXMLDI':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'T310_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T310_T075_Id', 'colunaTitulo' => 'T300_T075_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T310_T055_Id', 'colunaTitulo' => 'T310_T055_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T310_Valor_Adicao', 'colunaTitulo' => 'Adição','colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Adicao_Item', 'colunaTitulo' => 'Item adição','colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'callback' => 'descricaoItemXML'),
			array('colunaValor' => 'T310_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'left', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Classificacao_Fiscal', 'colunaTitulo' => 'NCM','colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'),
			array('colunaValor' => 'T310_Aliquota_Imposto_Importacao', 'colunaTitulo' => '%II', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'style' => 'width:1%'), 
		);

		/**
		* Definição da SQL
		*/
		$from = 'T310';
		
		$extra = <<<EOT
		WHERE T310_T055_Id='{$r_T055_Id}'
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
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T310_Id = id[0].replace(/T310_Id-/, '');
				var T055_Id = $r_T055_Id;		
				var \$buttons = {
					"Sim": function() {
						$.getJSON('/imp/imp003/grid_func-ajax/desvincularItemXML/?ajax=true&T055_Id=' + encodeURIComponent(T055_Id) + '&T310_Id=' + encodeURIComponent(T310_Id) + '&callback=?', function(request) {
							if (request.code) {
								// fechar janela e atualizar parent
								dialogConfirm('Ok', request.data);
								divRefresh('{$g['divIdParent']}');
								$(this).dialog("destroy").remove();
								fecharJanela('{$g['divId']}');
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, buttons);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('desvincular', 'Você tem certeza que deseja desvincular o item? ', \$buttons);			
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
