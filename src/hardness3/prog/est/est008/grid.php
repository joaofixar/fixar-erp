<?php
namespace hardness;
/**
* Weiberlan
* 24/02/2012
*/

/**
* Classes
*/
require_once('bibliotecas/classes/EST008.php');
$EST008 = new EST008();

$totais = array();

//--Carrega funções callback
	require_once('prog/est/est008/grid_func-callback.php');
//--Carrega funções do ajax
	require_once('prog/est/est008/grid_func-js.php');



switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'est008GridCotacaoCompra':

		 $usarAlcada = $g['conf']->ini('usarAlcadaCompra');
		
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T010_Id', $g['divId'])),
 			array('colunaValor' => 'T010_Data', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style'=>'width:1%;'),
 			array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			//array('colunaValor' => 'T173_T010_Id', 'colunaTitulo' => 'T173_Id','colunaExibir' => false),
			//array('colunaValor' => 'T173_Id', 'colunaTitulo' => 'T173_Id'),
			//array('colunaValor' => 'concat(D024_Nome_Empresa," - ",(SELECT D024_Nome_Empresa FROM T173 LEFT JOIN D024 ON T173_D024_Id=D024_Id WHERE T173_T010_Id=T010_Id))', 'colunaTitulo' => 'Fornecedor1' ),
			array('colunaValor' => 'D024_Id', 'colunaTipo' => 'livre','colunaTitulo' => 'Cód','callback' => 'campoCodFornecedor','style' => 'width:1%;'),
            array('colunaValor' => 'T173_D024_Id', 'colunaTipo' => 'livre','colunaTitulo' => 'Fornecedor / Alçada','callback' => 'campoFornecedor'),
			array('colunaValor' => 'ValorCotacao', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Cotação','callback' => 'campoValorCotacao', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => 'ValorCotacaoMaisQuantidae', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Cotação','callback' => 'campoValorCotacaoMaisQUantidade', 'colunaAlinhar'=>'right', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => '','callback' => 'validaCampoEmailEnviadoForn', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => 'ValorConfirmado', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Confirmado','callback' => 'campoValorConfirmado', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => '(select count(*) from T059 where T059_T010_Id=T010_Id) as ProdutosCP', 'colunaTitulo' => 'Itens', 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T010_Flag_Tipo', 'colunaTitulo' => 'Tipo'),
			array('colunaValor' => 'T010_Flag_Alcada_Status', 'colunaTitulo' => 'Alçada', 'colunaExibir'=>$usarAlcada, 'callback'=>'retornaStatusAlcada', 'colunaTipo'=>'livre'),
			//array('colunaValor' => "if((select count(*) from T059 left join T012 on T012_T059_Id=T059_Id where T059_T010_Id=T010_Id and T012_Id is null)>0,'PENDENTE',if(T010_Flag_Cotacao_Finalizada='S','Finalizado','Concluido')) as Status", 'colunaTitulo' => 'Status','callback' => 'colunaStatus'),
			array('colunaValor' => "T010_Flag_Cotacao_Finalizada", 'colunaTitulo' => 'Status','callback' => 'colunaStatusNovo'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'T010_Motivo_Nao_Liberado', 'colunaTitulo' => 'T010_Motivo_Nao_Liberado', 'colunaExibir'=>false),

			//$cClausula .= " (select count(*) from T059 where T059_T010_Id=T010_Id) Itens, ";
			
     );
     
		
		/**
		 * Definição da SQL
		 */
		$from = "T010";
		
		$extra = <<<EOT
			left join C007 on C007_Id=T010_C007_Id
			where T010_C004_Id='{$g['empresaAtual']}'
EOT;
			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est008Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est008Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Cotação de Compra', '/est/est008/content/est008contentCadastroCotacaoCompra/','', [500,350]);
				});
			} </script></div>
EOT;
			
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T010_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T010_Data' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T010_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
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
			* Botões (Versão Acima do GRID)
			*/
			$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Cotação de Compra<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T010_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de Compra - Número: ' + id, '/est/est008/content/est008contentCadastroCotacaoCompra/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,500]);
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
  	
  	case 'est008GridProdutos':
			
		$acaoId = (isset($r_T010_Id)) ? $r_T010_Id : false;
		
		echo $EST008->insereRegistrosT174($acaoId);
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T059_Id', $g['divId'])),
			array('colunaValor' => 'T059_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T059_D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T059_Codigo_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'concat(T059_Descricao_Produto, if(D001_Flag_Pre_Cadastro="S"," (PRE)","")) as Produto', 'colunaTitulo' => 'Descrião Produto'),
			//array('colunaValor' => 'T059_Descricao_Produto', 'colunaTitulo' => 'Descrição Produto'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'campoIncluirProd', 'colunaTitulo' => '', 'colunaTipo'=>'livre', 'callback'=>'validaCampoIncluirProd', 'style'=>'width:1%;'),
			array('colunaValor' => 'campoDuplicar', 'colunaTitulo' => '', 'colunaTipo'=>'livre', 'callback'=>'validaCampoDuplicar', 'style'=>'width:1%;'),
			// array('colunaValor' => 'T059_Valor_Custo_Unitario', 'colunaTitulo'=>'Valor Unitário', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			// array('colunaValor' => 'T059_Valor_Total_Custo', 'colunaTitulo'=>'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			// array('colunaValor' => 'T059_Aliquota_ICMS', 'colunaTitulo'=>'ICMS', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			// array('colunaValor' => 'T059_Aliquota_IPI', 'colunaTitulo'=>'IPI', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			// array('colunaValor' => 'T059_T010_Id', 'colunaTitulo'=>'Cotação Compra'),		
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T059';

		$extra = <<<EOT
			left join D009 on D009_Id=T059_D009_Id 
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join T010 on T010_Id=T059_T010_Id 
			where T059_T010_Id='{$acaoId}'
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
				var id = $(this).closest('tr').attr('id').replace(/T059_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
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
  	
  	 case 'est008GridFornecedores':
			
		$acaoId = (isset($r_T010_Id)) ? $r_T010_Id : false;
		
		echo $EST008->insereRegistrosT174($acaoId);
		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(                       
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T173_Id', $g['divId'])),
			array('colunaValor' => 'T173_T010_Id', 'colunaTitulo' => 'T010_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T173_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'T173_D024_Id', 'colunaTitulo' => 'Cód'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'Total Cotado', 'colunaTitulo' => 'Total Cotado', 'colunaTipo' => 'livre', 'callback' => 'validaCampoTotalPorFornecedor', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'Total Cotado 2', 'colunaTitulo' => 'Total Cotado', 'colunaTipo' => 'livre', 'callback' => 'validaCampoTotalPorFornecedorMaisQunt', 'colunaAlinhar'=>'right', 'colunaExibir'=>false),
			array('colunaValor' => 'Total Confirmado', 'colunaTitulo' => 'Total Confirmado', 'colunaTipo' => 'livre', 'callback' => 'validaCampoTotalPorFornecedorCotado', 'colunaAlinhar'=>'right'),
			array('colunaValor' => 'T173_Frete', 'colunaTitulo' => 'Frete', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T173_Desconto', 'colunaTitulo' => 'Desconto', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T173_ICMS', 'colunaTitulo'=>'ICMS', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T173_Flag_EMail_Enviado', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor' => 'E-mail Link', 'colunaTitulo' => 'E-mail Link', 'colunaTipo' => 'livre', 'callback' => 'campoeMail', 'style'=>'width:1%;'),
			array('colunaValor' => 'E-mail PDF', 'colunaTitulo' => 'E-mail PDF', 'colunaTipo' => 'livre', 'callback' => 'campoeMail2', 'style'=>'width:1%;'),
			array('colunaValor' => 'Cotação Link', 'colunaTitulo' => 'Cotação Link', 'colunaTipo' => 'livre', 'callback' => 'campoeMail3', 'style'=>'width:1%;'),
			array('colunaValor' => 'Imprimir', 'colunaTitulo' => 'Imprimir', 'colunaTipo' => 'livre', 'callback' => 'validaCampoImprimir', 'colunaAlinhar'=>'center', 'style'=>'width:1%;'),
			array('colunaValor' => 'OC', 'colunaTitulo' => 'OC', 'colunaTipo' => 'livre', 'callback' => 'validaCampoOCGeradaGridFornecedores', 'colunaAlinhar'=>'center', 'style'=>'width:1%;'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T173';

		$extra = <<<EOT
			left join D024 on D024_Id=T173_D024_Id 
			where T173_T010_Id='{$acaoId}'
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T173_Id = id[0].replace(/T173_Id-/, '');
				//var D001_Id = id[1].replace(/D001_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Produtos Cotação de Compra - ID: ' + {$acaoId}, '/est/est008/content/est008ContentFornecedoresOCForn/', '&T010_Id=' + encodeURIComponent($acaoId) + '&T173_Id=' + encodeURIComponent(T173_Id), [950,450]);
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
  	
  	case 'est008GridIniciarCotacao':
		$acaoId = (isset($r_T010_Id)) ? $r_T010_Id : false;
		echo $EST008->insereRegistrosT174($acaoId);
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T059_Id', $g['divId']),'colunaExibir' => false),
			array('colunaValor' => 'T059_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'D001_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D009_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T010_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaExibir' => false),
			array('colunaValor' => 'T059_Codigo_Produto', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T059_Descricao_Produto', 'colunaTitulo' => 'Descrição Produto', 'callback'=>'validaCampoDescProd'),
			//array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			//array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T059_T010_Id', 'colunaExibir' => false),
		);
		$sql = "SELECT T010_Flag_Quantidade_Individual FROM T010 WHERE T010_Id='{$acaoId}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		
		if($linha['T010_Flag_Quantidade_Individual'] == "N"){
			$grid[] = array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => "Quantidade",'colunaAlinhar' => 'center', 'colunaEditar'=>true, 'colunaCasasDecimais' => '2', 'colunaEditarJSCallback'=>'atualizarTela');
		}
		$grid[] = array('colunaValor' => 'MelhorPreco', 'colunaTitulo' => "Melhor Preço",'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'callback'=>'validaCampoPrecoUnit', 'colunaTipo'=>'livre');
		$grid[] = array('colunaValor' => 'Percentual', 'colunaTitulo' => "%",'colunaAlinhar' => 'right', 'callback'=>'validaCampoPercentual', 'colunaTipo'=>'livre');
		$grid[] = array('colunaValor' => 'Total', 'colunaTitulo' => "Total",'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'callback'=>'validaCampoMelhorPreco', 'colunaTipo'=>'livre');
		$grid[] = array('colunaValor' => 'MelhorFornecedor', 'colunaTitulo' => "Melhor Fornecedor",'colunaAlinhar' => 'left', 'callback'=>'validaCampoMelhorFornecedor', 'colunaTipo'=>'livre');
		$grid[] = array('colunaValor' => 'FornecedorMarcado', 'colunaTitulo' => "Fornecedores Marcados",'colunaAlinhar' => 'left', 'callback'=>'validaCampoFornecedoresMarcados', 'colunaTipo'=>'livre');
		//$grid[] = array('colunaValor' => 'T173_Forma_Pagamento', 'colunaTitulo' => "Forma Pgto",);
        
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T059';

		$extra = <<<EOT
			left join D009 on D009_Id=T059_D009_Id 
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join T010 on T010_Id=T059_T010_Id
			where T059_T010_Id='{$acaoId}'
EOT;
			
			/*
			* Botões (Versão Acima do GRID)
			
			//Marcar Melhor Preço
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est008MarcarMelhorPreco">Marcar Melhor Preço</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est008MarcarMelhorPreco').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();				
					$.getJSON('/est/est008/grid_func-ajax/marcarMelhorPreco/?ajax=true&T010_Id=' + encodeURIComponent({$acaoId}) + '&callback=?', function(request) {
					divRefresh('{$g['divId']}');
					$('#{$g['divId']}').hideLoading();
					});
				});
			} </script></div>
EOT;
*/
	//Gerar OC
echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est008GerarOCGridPrecos">Gerar OC</button>
				<button type="button" id="est008MarcarMelhoresPrecos">Marcar Melhores Preços</button>
				<button type="button" id="est008DesmarcarMelhoresPrecos">Desmarcar Melhores Preços</button>
				<button type="button" id="est008DesmarcarTodos">Desmarcar Todos</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est008GerarOCGridPrecos').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar OC', '/est/est008/content/est008ContentGerarOC/','&T010_Id=' + encodeURIComponent({$acaoId}), [600,400]);
				});
				$('#est008MarcarMelhoresPrecos').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est008/grid_func-ajax/est008MarcarMelhoresPrecos/?ajax=true&T010_Id=' + encodeURIComponent({$acaoId}) + '&callback=?', function(request) {
						if(request.code){
							$('#{$g['divId']}').hideLoading();
							divRefresh('{$g['divId']}', true);
						}
					});
				});
				$('#est008DesmarcarMelhoresPrecos').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est008/grid_func-ajax/est008DesmarcarMelhoresPrecos/?ajax=true&T010_Id=' + encodeURIComponent({$acaoId}) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#est008DesmarcarTodos').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est008/grid_func-ajax/est008DesmarcarTodos/?ajax=true&T010_Id=' + encodeURIComponent({$acaoId}) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
			} </script></div>
EOT;
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			function atualizarTela(){
				divRefresh('{$g['divId']}', true);
			}
			$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T059_Id = id[0].replace(/T059_Id-/, '');
				var D001_Id = id[1].replace(/D001_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id), [980,550]);
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Fornecedores Cotação de Compra - ID: ' + {$acaoId}, '/est/est008/content/est008ContentFornecedoresOC/', '&acaoId=' + encodeURIComponent($acaoId) + '&T059_Id=' + encodeURIComponent(T059_Id), [850,450]);
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
  	
  	case 'est008GridGerarOC':
			
		$acaoId = (isset($r_T010_Id)) ? $r_T010_Id : false;
		
		echo $EST008->insereRegistrosT174($acaoId);
		
		$usarAlcada = $g['conf']->ini('usarAlcadaCompra');
		if ($usarAlcada){
			$meuPerfil=$g['C007']['C007_Flag_Vendedor'];
			if ($meuPerfil=='C')
				$minhaAlcada = gCorrigeNumero(str_replace(",", ".", $g['conf']->ini('alcadaCompraComprador')))." (COMPRADOR)";
			else if ($meuPerfil=='S')
				$minhaAlcada = gCorrigeNumero(str_replace(",", ".", $g['conf']->ini('alcadaCompraSupervisor')))." (SUPERVISOR)";
			else if ($meuPerfil=='D')
				$minhaAlcada = "DIRETOR";			
			else
				$minhaAlcada = "SEM ALÇADA";
			
			echo '<div style="padding:3px;">Alçada: <b>'.$minhaAlcada.'</b></div>';
		}		
		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'T010_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T173_Id', 'colunaExibir' => false, 'colunaTitulo' => 'T173_Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'D024_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T174_Id', 'colunaTitulo' => 'T174_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T173_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'quantidadeItens', 'colunaTitulo' => 'Itens', 'colunaTipo' => 'livre', 'callback' => 'calculaItens'),
            array('colunaValor' => 'T173_Frete', 'colunaExibir' => false),
            array('colunaValor' => 'T010_Flag_Alcada_Status', 'colunaTitulo' => 'Alçada', 'callback' => 'retornaStatusAlcadaNovo', 'colunaExibir' => false),
            array('colunaValor' => '', 'colunaTitulo' => '', 'callback' => 'preVisualiza', 'colunaTipo' => 'livre', 'style' => 'width:1%;', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
        );
		/*
		$sql = "SELECT T173_Id,D024_Id,T010_Flag_Marca_Individual FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id LEFT JOIN T010 ON T010_Id=T173_T010_Id WHERE T173_T010_Id='{$acaoId}' ";
		$resultado = mysql_query($sql);
		//$row = mysql_fetch_array($resultado);
		while ($row = mysql_fetch_array($resultado)){
			$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> Valor Unit.",'colunaAlinhar' => 'center', 'callback' => 'campoValorUnitario','callbackParameter' => "{$row['T173_Id']}");
			$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> %",'colunaAlinhar' => 'center', 'callback' =>'campoIPI','callbackParameter' => "{$row['T173_Id']}");
			$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> Total",'colunaAlinhar' => 'center', 'callback' =>'campoTotal','callbackParameter' => "{$row['T173_Id']}");
			$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> OC",'colunaAlinhar' => 'center', 'callback' =>'campoOC','callbackParameter' => "{$row['T173_Id']}");
		}
        */
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T173';

		$extra = <<<EOT
			left join T010 on T010_Id=T173_T010_Id
			left join D024 on D024_Id=T173_D024_Id
			LEFT JOIN T174 ON T174_T173_Id=T173_Id
			where T173_T010_Id='{$acaoId}' AND T174_Flag_Gerar_OC='1'
			group by D024_Id
EOT;
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T173_Id="";
				var T010_Id="";
				if (id != ""){
					T173_Id = id[1].replace(/T173_Id-/, '');
					T010_Id = id[0].replace(/T010_Id-/, '');
				}
				// caso o usuário clique na última linha do grid
				if(T010_Id == 0 || T173_Id == 0 || id == ""){
					var \$buttons = {
					"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est008/grid_func-ajax/est008geraOCMarcados/?ajax=true&callback=?' + '&T010_Id=' + encodeURIComponent({$acaoId}) + '&T173_Id=' + encodeURIComponent(T173_Id), function(request) {
								if (request.code === true) {
									divRefresh('{$g['divId']}', true);
									dialogConfirm('Operação OK!', request.data);
									divRefresh('{$g['divIdParent']}', true);
								} else if (request.code == 2) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', "Falha ao gerar Ordem de Compra (cabeçalho)", \$buttons);
								} else if (request.code == 3) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', "Falha ao gerar Ordem de Compra (produtos)", \$buttons);
								} else if (request.code == 4) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', "Você não tem permissão para gerar OC", \$buttons);
								} else if (request.code == 5){
									var \$buttons = {
										"Sim": function() {
											$('#{$g['divId']}').showLoading();
											$.getJSON('/est/est008/grid_func-ajax/est008liberacaoAlcada/?ajax=true&callback=?' + '&T010_Id=' + encodeURIComponent({$acaoId}) + '&T173_Id=' + encodeURIComponent(T173_Id), function(request) {
												if (request.code === true) {
													divRefresh('{$g['divId']}', true);
													dialogConfirm('Operação OK!', request.data);
													divRefresh('{$g['divIdParent']}', true);
												} else {
													var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    				dialogConfirm('Erro', request.data, \$buttons);
												}
												$('#{$g['divId']}').hideLoading();
											});
											$('#{$g['divId']}').hideLoading();
											$(this).dialog("destroy").remove();
										},
										"Não": function() { $(this).dialog("destroy").remove(); } 
									};
									dialogConfirm('Confirmação', 'A geração desta OC exige maior alçada, pedir liberação?(1)', \$buttons);
								} else if(request.code == 6) {
									var \$buttons = {
										"Sim": function() {
											$('#{$g['divId']}').showLoading();
											$.getJSON('/est/est008/grid_func-ajax/est008liberacaoAlcada/?ajax=true&callback=?' + '&T010_Id=' + encodeURIComponent({$acaoId}) + '&T173_Id=' + encodeURIComponent(T173_Id), function(request) {
												if (request.code === true) {
													divRefresh('{$g['divId']}', true);
													dialogConfirm('Operação OK!', request.data);
													divRefresh('{$g['divIdParent']}', true);
												} else {
													var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								    				dialogConfirm('Erro', request.data, \$buttons);
												}
												$('#{$g['divId']}').hideLoading();
											});
											$('#{$g['divId']}').hideLoading();
											$(this).dialog("destroy").remove();
										},
										"Não": function() { $(this).dialog("destroy").remove(); } 
									};
									dialogConfirm('Confirmação', 'A geração desta OC exige maior alçada, pedir liberação?(2)', \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar Ordem de Compra', 'Gostaria de gerar Ordem de Compra para os fornecedores selecionados? ', \$buttons);
				// senão gera ordem de compra do fornecedor selecionado na linha do grid
				} else {
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar OC', '/est/est008/content/est008ContentSelecionarEmpresa/', '&T010_Id=' + encodeURIComponent({$acaoId}) + '&T173_Id=' + encodeURIComponent(T173_Id), [450,150]);
				}
			});
		} </script></div>
EOT;



		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$itens = 55;
		$total = 66;
		
    	/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);

		$numItens=$EST008->calculaNumItens($acaoId);
		$valItens=$EST008->calculaValItens($acaoId);
		/* linha de totais, para gerar dos selecionados (comentada por enquanto)
		$gridTabela['tbody'][] = array(
			'dados' => array(
				5 => 'Itens Marcados (melhores preços)',
				6 => $numItens,
				7 => $valItens
			)
		);		
		*/
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
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
  	break;
	
	case 'est008GridFornecedoresOC':
			
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$sql = "SELECT T010_Flag_Quantidade_Individual, T010_Flag_Marca_Individual FROM T010 WHERE T010_Id='{$acaoId}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);		
				
		$res = mysql_query("SELECT DISTINCT D001_Descricao_Produto,D001_Id from T174
							LEFT JOIN T059 on T059_Id=T174_T059_Id
							LEFT JOIN D009 on D009_Id=T059_D009_Id
							LEFT JOIN D049 on D049_Id=D009_D049_Id
							LEFT JOIN D001 on D001_Id=D049_D001_Id
							WHERE T174_T010_Id='{$acaoId}' AND T174_T059_Id='$r_T059_Id'");
		$lin = mysql_fetch_array($res);		
		echo '<div style="padding:3px;">Produto: <b>'.$lin['D001_Descricao_Produto'].'</b></div>';
		//echo $EST008->insereRegistrosT174($acaoId);
		
		if($linha['T010_Flag_Marca_Individual'] == "N"){
			$disabled = "disabled=\"disabled\"";
		}else{
			$disabled="";
		}
		// echo $disabled;
		// die();
		/**
		 * Definição do Grid (Tabela)
				 */
		$grid = array(
		   array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T173_Id', $g['divId']), 'colunaExibir' => false),
		   array('colunaValor' => 'T173_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
		   array('colunaValor' => 'T174_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
		   array('colunaValor' => 'T059_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
		   array('colunaValor' => 'D024_Id', 'colunaExibir' => false),
		   array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
		   array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
		  );
		   //array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditar'=>true, 'colunaCasasDecimais' => '2'),
		   if($linha['T010_Flag_Marca_Individual'] == "N"){
		    $grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca');
		   }else{
		    $grid[] = array('colunaValor' => 'T174_D009_Id', 'colunaTitulo' => 'Marca', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D009', 'D009_Id', 'D082_Marca', 'left join D049 on D049_Id=D009_D049_Id left join D082 on D082_Id=D049_D082_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id where D049_D001_Id='.$lin['D001_Id'].' AND D009_C004_Id='.$g['empresaAtual']), 'callback' => 'buscaMarca');
		   }
		   $grid[] = array('colunaValor' => '', 'colunaTitulo' => '', 'style'=>'width:1%;', 'callback'=>'validaCampoEmailFornProd', 'colunaTipo'=>'livre');
		   if($linha['T010_Flag_Quantidade_Individual'] == "S"){
		    $grid[] = array('colunaValor' => 'T174_Quantidade', 'colunaTitulo' => "Quantidade",'colunaAlinhar' => 'center', 'colunaEditar'=>true);
		   }
		   $grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor Unitário', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right', 'style'=>'width:1%;', 'colunaEditarJSCallback'=>'atualizaTela');
		   if($g['c004']['C004_Flag_Isento_IPI'] != '1'){
		    //$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "% IPI",'colunaAlinhar' => 'center', 'callback' =>'campoIPI');
		    $grid[] = array('colunaValor' => 'T174_IPI', 'colunaTitulo' => "% IPI",'colunaAlinhar' => 'center', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2', 'style'=>'width:1%;');
		   }
		   $grid[] = array('colunaValor' => 'T174_Valor_ST', 'colunaTitulo' => "ST",'colunaAlinhar' => 'center', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2', 'style'=>'width:1%;');
		   $grid[] = array('colunaValor' => 'T174_Valor_Total', 'colunaTitulo' => 'Total', 'callback'=>'gCorrigeNumero', 'callback'=>'validaCampoTotal', 'callbackParameter'=>$acaoId, 'colunaAlinhar'=>'right');  
		   //array('colunaValor' => 'Frete', 'colunaTitulo' => 'Frete', 'callback'=>'calculaFrete', 'colunaTipo'=>'livre'),
		   $grid[] = array('colunaValor' => 'T173_Frete', 'colunaTitulo' => "% Frete", 'callback' =>'gCorrigeNumero', 'style'=>'width:1%;', 'colunaAlinhar'=>'right');
		   $grid[] = array('colunaValor' => 'T173_Desconto', 'colunaTitulo' => "% Desc", 'callback' =>'gCorrigeNumero', 'style'=>'width:1%;', 'colunaAlinhar'=>'right');
		   $grid[] = array('colunaValor' => 'T174_ICMS', 'colunaTitulo' => '% ICMS', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;', 'colunaAlinhar'=>'right');
		   $grid[] = array('colunaValor' => 'OC', 'colunaTipo' => 'livre', 'colunaTitulo' => "OC",'colunaAlinhar' => 'center', 'callback' =>'campoOC');
		   $grid[] = array('colunaValor' => 'T059_T010_Id', 'colunaTitulo' => 'T059_T010_Id', 'colunaExibir'=>false);
		   $grid[] = array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'T059_Quantidade', 'colunaExibir'=>false);
		   $grid[] = array('colunaValor' => 'T173_Forma_Pagamento', 'colunaTitulo' => 'Forma Pgto.');		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T174';

		$extra = <<<EOT
			LEFT JOIN T173 ON T174_T173_Id=T173_Id 
			LEFT JOIN T010 ON T010_Id=T173_T010_Id
			LEFT JOIN D024 ON D024_Id=T173_D024_Id 
			LEFT JOIN T059 ON T059_Id=T174_T059_Id 
			LEFT JOIN D009 ON D009_Id=T174_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			WHERE T174_T010_Id='{$acaoId}' AND T174_T059_Id='$r_T059_Id' AND T174_Flag_Excluido='N'
EOT;
		
			
			/**
			* Botões (Versão Acima do GRID)
			*/
			//Marcar Melhor Preço
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				function atualizaTela() {
					divRefresh('{$g['divId']}', true);
					divRefresh('{$g['divIdParent']}', true);
				}
			} </script></div>
EOT;

	//Gerar OC
// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="est008GerarOC">Gerar OC</button>
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				// $('#est008GerarOC').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar OC', '/est/est008/content/est008ContentGerarOC/','&T010_Id=' + encodeURIComponent({$acaoId}), [600,400]);
				// });
			// } </script></div>
// EOT;
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T059_Id = id[0].replace(/T059_Id-/, '');
				var D001_Id = id[1].replace(/D001_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id), [980,550]);
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Fornecedores Cotação de Compra - ID: ' + {$acaoId}, '/est/est008/content/est08ContentFornecedoresOC/', '&acaoId=' + encodeURIComponent($acaoId), [700,450]);
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
		
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
  	break;
	
	case 'est008GridFornecedoresOCForn':
			
		$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
		$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
		/**
		 * Definição do Grid (Tabela)
		 */
		 //echo "marca individual ".$linhaT010['T010_Flag_Marca_Individual'];
		$grid = array(
			//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T174_Id', $g['divId'])),
			array('colunaValor' => 'T173_Id', 'colunaTitulo'=>'', 'callback'=>'validaCampoExcluir', 'style'=>'width:1%;', 'chavePrimaria' => true),
			array('colunaValor' => 'T059_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T174_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D024_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T174_D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T059_Codigo_Produto', 'colunaTitulo' => 'Cód', 'style'=>'width:1%;'),
			array('colunaValor' => 'T174_Descricao_Produto', 'colunaTitulo' => 'Produto', 'callback'=>'validaCampoDescricao', 'colunaEditarJSCallback'=>'atualizaTela'),
		);
			$grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'callback'=>'validaCampoMarca', 'callbackParameter'=>$T010_Id);
			//array('colunaValor' => 'T174_D009_Id', 'colunaTitulo' => 'Marca', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D009', 'D009_Id', 'D082_Marca', 'left join D049 on D049_Id=D009_D049_Id left join D082 on D082_Id=D049_D082_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id where D009_C004_Id='.$g['empresaAtual']), 'callback' => 'buscaMarca'),						
			//array('colunaValor' => '', 'colunaTitulo' => 'Valor Unitário', 'callback'=>'campoValorUnitarioForn', 'colunaTipo'=>'livre'),
			$grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor Unitário', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right');
			$grid[] = array('colunaValor' => 'T174_IPV', 'colunaTitulo' => "IPV",'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true, 'colunaEditarJSCallback' => 'callbackCalculaCusto');
			$grid[] = array('colunaValor' => 'T174_Preco_Venda', 'colunaTitulo' => "Preço Venda",'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true, 'colunaEditarJSCallback' => 'callbackCalculaCusto');
			if($g['c004']['C004_Flag_Isento_IPI'] != '1'){
				//$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "IPI",'colunaAlinhar' => 'center', 'callback' =>'campoIPI');
				$grid[] = array('colunaValor' => 'T174_IPI', 'colunaTitulo' => "% IPI",'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true);
			}
			$grid[] = array('colunaValor' => 'T174_Valor_ST', 'colunaTitulo' => "ST",'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true);
			// $grid[] = array('colunaValor' => 'T174_Valor_Total', 'colunaTitulo' => 'Total', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right');
			// $grid[] = array('colunaValor' => 'T173_Frete', 'colunaTitulo' => '% Frete', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');
			// $grid[] = array('colunaValor' => 'T173_Desconto', 'colunaTitulo' => '% Desc', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');			
			// $grid[] = array('colunaValor' => 'T173_ICMS', 'colunaTitulo' => '% ICMS', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');			
			$grid[] = array('colunaValor' => 'T174_Prazo_Entrega_Dias', 'colunaTitulo' => "Prazo Entrega", 'colunaEditar'=>true);
			$grid[] = array('colunaValor' => 'T174_Observacao', 'colunaTitulo' => "Observação", 'colunaEditar'=>true);
			$grid[] = array('colunaValor' => 'OC', 'colunaTipo' => 'livre', 'colunaTitulo' => "OC",'colunaAlinhar' => 'center', 'callback' =>'campoOC', 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'T059_T010_Id', 'colunaTitulo' => 'T059_T010_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'T059_Quantidade', 'colunaExibir'=>false);
		
		$sql = "SELECT T010_Flag_Quantidade_Individual FROM T010 WHERE T010_Id='{$T010_Id}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
	
		if($linha['T010_Flag_Quantidade_Individual'] == "S"){
			$grid[] = array('colunaValor' => 'T174_Quantidade', 'colunaTitulo' => "Quantidade",'colunaAlinhar' => 'center', 'colunaEditar'=>true);
		}
		$grid[] = array('colunaValor' => 'T174_Flag_Indisponivel', 'colunaTitulo' => "Indisp",'colunaAlinhar' => 'center', 'callback'=>'validaCampoIndisponivel', 'style'=>'width:1%;');
		
		$grid[] = array('colunaValor' => 'T059_T189_Id', 'colunaTitulo' => "Req", 'colunaEditar'=>true, 'colunaAlinhar' => 'center');
		$grid[] = array('colunaValor' => '', 'colunaTitulo' => '', 'colunaTipo'=>'livre', 'callback'=>'gridFornecedresOcDuplicaProduto', 'style'=>'width:1%;');

		// $sql = "SELECT T173_Id,D024_Id,T010_Flag_Marca_Individual FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id LEFT JOIN T010 ON T010_Id=T173_T010_Id WHERE T173_T010_Id='{$acaoId}'";
		// $resultado = mysql_query($sql);
		// //$row = mysql_fetch_array($resultado);
		// while ($row = mysql_fetch_array($resultado)){
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> Valor Unit.",'colunaAlinhar' => 'center', 'callback' => 'campoValorUnitario','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> %",'colunaAlinhar' => 'center', 'callback' =>'campoPernc','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> Total",'colunaAlinhar' => 'center', 'callback' =>'campoTotal','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> OC",'colunaAlinhar' => 'center', 'callback' =>'campoOC','callbackParameter' => "{$row['T173_Id']}");
			// if($g['c004']['C004_Flag_Isento_IPI'] != '1'){
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> IPI",'colunaAlinhar' => 'center', 'callback' =>'campoIPI','callbackParameter' => "{$row['T173_Id']}");
			// }
		// }
        
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T174';

		$extra = <<<EOT
			LEFT JOIN T173 ON T174_T173_Id=T173_Id 
			LEFT JOIN T010 ON T010_Id=T173_T010_Id
			LEFT JOIN D024 ON D024_Id=T173_D024_Id 
			LEFT JOIN T059 ON T059_Id=T174_T059_Id 
			LEFT JOIN D009 ON D009_Id=T174_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			WHERE T174_T010_Id='{$T010_Id}' AND T174_T173_Id='{$T173_Id}' AND T174_Flag_Excluido='N'
			AND (T059_T173_Id IS NULL OR T059_T173_Id='{$T173_Id}')
EOT;
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				function atualizaTela() {
					divRefresh('{$g['divId']}', true);
				}
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'B') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T059_Id = id[1].replace('T059_Id-', '');
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Produto - Número: ' + T059_Id, '/est/est008/content/est008ContentEditarProdutoFornecedor/', '&acaoId=' + encodeURIComponent(T059_Id) + '&T010_Id=' + encodeURIComponent('{$T010_Id}') + '&T173_Id=' + encodeURIComponent('{$T173_Id}'), [550,300]);
					
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').unbind('click').bind('click', function(event) { return false; });
				
				function callbackCalculaCusto(T174_Id, campoEditado, valorAntigo, novoValor) {
				var T174 = T174_Id.split('|');
				var T174_Id = T174 = T174[2].replace('T174_Id-', '');
				$.getJSON('/est/est014/grid_func-ajax/est014aplicarPrecoVenda/?ajax=true&T174_Id='    + encodeURIComponent(T174_Id) + 
																			      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																			      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																			      '&novoValor='			+ encodeURIComponent(novoValor) +  
																			      '&callback=?', function(request) {
					if (request.code) {
						divRefresh('{$g['divId']}', true);
						//alert("NFe Excluída");
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
			}
			} </script></div>
EOT;
		
			
			//Botões (Versão Acima do GRID)
			
			//Marcar Melhor Preço
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est008IncluirProdFornecedor">Incluir Produto</button>
				<button type="button" id="est008RetornarProdFornecedor">Retornar produtos</button>
				<button type="button" id="est008PreCadastroProduto">Pré-cadastrar Produto</button>
				<!-- <button type="button" id="est008GerarOcFornecedor">Gerar OC</button> -->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est008IncluirProdFornecedor').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Produto', '/est/est008/content/est008ContentIncluirProdForn/', '&T010_Id=' + encodeURIComponent('{$T010_Id}') + '&T173_Id=' + encodeURIComponent('{$T173_Id}') + '&div=' + encodeURIComponent('{$g['divId']}'), [400,130]);
				});
				$('#est008RetornarProdFornecedor').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Retornar produtos fornecedor', '/est/est008/content/est008ContentRetornarProdForn/', '&T010_Id=' + encodeURIComponent('{$T010_Id}') + '&T173_Id=' + encodeURIComponent('{$T173_Id}'), [650,480]);
				});
				$('#est008GerarOcFornecedor').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar OC', '/est/est008/content/est008ContentGerarOC/','&T010_Id=' + encodeURIComponent({$T010_Id}), [600,400]);
				});
				$('#est008PreCadastroProduto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '', [600,430]);
				});
			} </script></div>
EOT;

		$sql = "SELECT D024_Nome_Empresa from T173 left join D024 on D024_Id=T173_D024_Id WHERE T173_Id='{$T173_Id}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		echo '<div style="padding:3px;">Fornecedor: <b>'.$linha['D024_Nome_Empresa'].'</b></div>';
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
		
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
  	break;
	
	case 'est008GridResumoCotacao':
			
		$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
		$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
		$T174_Id = (isset($r_T174_Id)) ? $r_T174_Id : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		
		$sqlT010 = "SELECT T010_Flag_Marca_Individual FROM T010 WHERE T010_Id='{$T010_Id}'";
		$resultT010 = mysql_query($sqlT010);
		$linhaT010 = mysql_fetch_array($resultT010);
		
		$sql = "SELECT D024_Nome_Empresa from T173 left join D024 on D024_Id=T173_D024_Id WHERE T173_Id='{$T173_Id}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		echo '<div style="padding:3px;">Fornecedor: <b>'.$linha['D024_Nome_Empresa'].'</b></div>';
		
		
		$sql = "SELECT T010_Flag_Quantidade_Individual FROM T010 WHERE T010_Id='{$T010_Id}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		
		// $sql = "SELECT T010_Flag_Quantidade_Individual FROM T010 WHERE T010_Id='{$acaoId}'";
		// $result = mysql_query($sql);
		// $linha = mysql_fetch_array($result);
		//echo $EST008->insereRegistrosT174($acaoId);
		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T173_Id', $g['divId']), 'colunaExibir' => false),
			array('colunaValor' => 'T173_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T174_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T059_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D024_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T174_D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T174_Descricao_Produto', 'colunaTitulo' => 'Produto'),
		);
			$grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca');
			//array('colunaValor' => 'T174_D009_Id', 'colunaTitulo' => 'Marca', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D009', 'D009_Id', 'D082_Marca', 'left join D049 on D049_Id=D009_D049_Id left join D082 on D082_Id=D049_D082_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id where D009_C004_Id='.$g['empresaAtual']), 'callback' => 'buscaMarca'),						
			$grid[] = array('colunaValor' => 'T173_Frete', 'colunaTitulo' => '% Frete', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'T173_Desconto', 'colunaTitulo' => '% Desc', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');			
			$grid[] = array('colunaValor' => 'T173_ICMS', 'colunaTitulo' => '% ICMS', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');
			//array('colunaValor' => '', 'colunaTitulo' => 'Valor Unitário', 'callback'=>'campoValorUnitarioForn', 'colunaTipo'=>'livre'),
			$grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor Unitário', 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right');
			if($g['c004']['C004_Flag_Isento_IPI'] != '1'){
				//$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "IPI",'colunaAlinhar' => 'center', 'callback' =>'campoIPI');
				$grid[] = array('colunaValor' => 'T174_IPI', 'colunaTitulo' => "% IPI",'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2');
			}
			$grid[] = array('colunaValor' => 'T174_Valor_Total', 'colunaTitulo' => 'Total', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right');			
			//$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "OC",'colunaAlinhar' => 'center', 'callback' =>'campoOC');
			$grid[] = array('colunaValor' => 'T059_T010_Id', 'colunaTitulo' => 'T059_T010_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'T174_Valor_Custo_Unitario', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'T059_Quantidade', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_IPI', 'colunaTitulo' => 'T174_IPI', 'colunaExibir'=>false);
			if($linha['T010_Flag_Quantidade_Individual'] == "S"){
				$grid[] = array('colunaValor' => 'T174_Quantidade', 'colunaTitulo' => "Qnt",'colunaAlinhar' => 'center', 'style'=>'width:1%;');
			}
		
		// $sql = "SELECT T173_Id,D024_Id,T010_Flag_Marca_Individual FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id LEFT JOIN T010 ON T010_Id=T173_T010_Id WHERE T173_T010_Id='{$acaoId}'";
		// $resultado = mysql_query($sql);
		// //$row = mysql_fetch_array($resultado);
		// while ($row = mysql_fetch_array($resultado)){
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> Valor Unit.",'colunaAlinhar' => 'center', 'callback' => 'campoValorUnitario','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> %",'colunaAlinhar' => 'center', 'callback' =>'campoPernc','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> Total",'colunaAlinhar' => 'center', 'callback' =>'campoTotal','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> OC",'colunaAlinhar' => 'center', 'callback' =>'campoOC','callbackParameter' => "{$row['T173_Id']}");
			// if($g['c004']['C004_Flag_Isento_IPI'] != '1'){
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> IPI",'colunaAlinhar' => 'center', 'callback' =>'campoIPI','callbackParameter' => "{$row['T173_Id']}");
			// }
		// }
        
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T174';

		$extra = <<<EOT
			LEFT JOIN T173 ON T174_T173_Id=T173_Id 
			LEFT JOIN T010 ON T010_Id=T173_T010_Id
			LEFT JOIN D024 ON D024_Id=T173_D024_Id 
			LEFT JOIN T059 ON T059_Id=T174_T059_Id 
			LEFT JOIN D009 ON D009_Id=T174_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			WHERE T174_T010_Id='{$T010_Id}' AND T174_T173_Id='$T173_Id'
			AND T174_Flag_Gerar_OC='1'
EOT;
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				function atualizaTela() {
					divRefresh('{$g['divId']}', true);
				}
			} </script></div>
EOT;
		
			
			/**
			* Botões (Versão Acima do GRID)
			
			//Marcar Melhor Preço
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est008MarcarMelhorPreco">Marcar Melhor Preço</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est008MarcarMelhorPreco').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();				
					$.getJSON('/est/est008/grid_func-ajax/marcarMelhorPreco/?ajax=true&T010_Id=' + encodeURIComponent({$acaoId}) + '&callback=?', function(request) {
					divRefresh('{$g['divId']}');
					$('#{$g['divId']}').hideLoading();
					});
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
		
		$total=0;
		 foreach ($gridTabela['tbody'] as $linha){
		 	$total+=@gCorrigeNumeroInverte($linha['dados'][13]);
		 }
		 $total = gCorrigeNumero($total);
		 // inserir o total na última linha da tabela
		 array_push($gridTabela['tbody'], array('dados'=>array('','','','','','','<b>Total</b>','<b style="text-align:right;">'.$total.'</b>')));
		
		
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
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
  	break;
	
	case 'est008GridProdutosCotacao':
			
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array();
			$grid[] = array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Cotação', 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição');
			$grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca');
			$grid[] = array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'Qnt', 'style'=>'width:1%;', 'colunaCasasDecimais' => '2');
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T059';

		$extra = <<<EOT
			left join D009 on D009_Id=T059_D009_Id 
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join T010 on T010_Id=T059_T010_Id
			left join T174 on T174_T059_Id=T059_Id and T174_T010_Id=T010_Id
			WHERE T059_C004_Id='{$g['empresaAtual']}
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
				'T010_Id' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
				'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
				'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
				'T059_Quantidade' => array('titulo' => 'Quantidade', 'tipo' => 'texto'),
			);
			
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
			
			/**
			* Botões (Versão Acima do GRID)
			
			//Marcar Melhor Preço
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est008MarcarMelhorPreco">Marcar Melhor Preço</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est008MarcarMelhorPreco').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();				
					$.getJSON('/est/est008/grid_func-ajax/marcarMelhorPreco/?ajax=true&T010_Id=' + encodeURIComponent({$acaoId}) + '&callback=?', function(request) {
					divRefresh('{$g['divId']}');
					$('#{$g['divId']}').hideLoading();
					});
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
	
	case 'est008GridRetornarProdForn':
			
		$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
		$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
		
		$sqlT010 = "SELECT T010_Flag_Marca_Individual FROM T010 WHERE T010_Id='{$T010_Id}'";
		$resultT010 = mysql_query($sqlT010);
		$linhaT010 = mysql_fetch_array($resultT010);
		
		// $sql = "SELECT T010_Flag_Quantidade_Individual FROM T010 WHERE T010_Id='{$acaoId}'";
		// $result = mysql_query($sql);
		// $linha = mysql_fetch_array($result);
		//echo $EST008->insereRegistrosT174($acaoId);
		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			//array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T174_Id', $g['divId'])),
			array('colunaValor' => 'T173_Id', 'colunaTitulo'=>'Retornar', 'colunaTipo'=>'livre', 'callback'=>'validaCheckBoxRetorna', 'style'=>'width:1%;'),
			array('colunaValor' => 'T173_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T174_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'T059_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D024_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T174_D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T059_Descricao_Produto', 'colunaTitulo' => 'Produto'),
		);
		if($linhaT010['T010_Flag_Marca_Individual'] == "N"){
			$grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca');
		} else {
			$grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'callback'=>'validaCampoMarca', 'callbackParameter'=>$T010_Id);
		}			
			//array('colunaValor' => 'T174_D009_Id', 'colunaTitulo' => 'Marca', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D009', 'D009_Id', 'D082_Marca', 'left join D049 on D049_Id=D009_D049_Id left join D082 on D082_Id=D049_D082_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id where D009_C004_Id='.$g['empresaAtual']), 'callback' => 'buscaMarca'),						
			//array('colunaValor' => '', 'colunaTitulo' => 'Valor Unitário', 'callback'=>'campoValorUnitarioForn', 'colunaTipo'=>'livre'),
			$grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'Valor Unitário', 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right');
			if($g['c004']['C004_Flag_Isento_IPI'] != '1'){
				//$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "IPI",'colunaAlinhar' => 'center', 'callback' =>'campoIPI');
				$grid[] = array('colunaValor' => 'T174_IPI', 'colunaTitulo' => "% IPI",'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2');
			}
			// $grid[] = array('colunaValor' => 'T174_Valor_Total', 'colunaTitulo' => 'Total', 'callback'=>'gCorrigeNumero', 'colunaAlinhar'=>'right');
			// $grid[] = array('colunaValor' => 'T173_Frete', 'colunaTitulo' => '% Frete', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');
			// $grid[] = array('colunaValor' => 'T173_Desconto', 'colunaTitulo' => '% Desc', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');			
			// $grid[] = array('colunaValor' => 'T173_ICMS', 'colunaTitulo' => '% ICMS', 'callback'=>'gCorrigeNumero', 'style'=>'width:1%;');			
			$grid[] = array('colunaValor' => 'T174_Observacao', 'colunaTitulo' => "Obs", 'colunaEditar'=>true);
			//$grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "OC",'colunaAlinhar' => 'center', 'callback' =>'campoOC', 'style'=>'width:1%;');
			$grid[] = array('colunaValor' => 'T059_T010_Id', 'colunaTitulo' => 'T059_T010_Id', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Valor_Custo_Unitario', 'colunaTitulo' => 'T174_Valor_Custo_Unitario', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'T059_Quantidade', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_IPI', 'colunaTitulo' => 'T174_IPI', 'colunaExibir'=>false);
			$grid[] = array('colunaValor' => 'T174_Flag_Excluido', 'colunaTitulo' => 'T174_Flag_Excluido', 'colunaExibir'=>false);
		
		$sql = "SELECT T010_Flag_Quantidade_Individual FROM T010 WHERE T010_Id='{$T010_Id}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
	
		if($linha['T010_Flag_Quantidade_Individual'] == "S"){
			$grid[] = array('colunaValor' => 'T174_Quantidade', 'colunaTitulo' => "Quantidade",'colunaAlinhar' => 'center', 'colunaEditar'=>true);
		}
		//$grid[] = array('colunaValor' => 'T174_Flag_Indisponivel', 'colunaTitulo' => "Indisp",'colunaAlinhar' => 'center', 'callback'=>'validaCampoIndisponivel', 'style'=>'width:1%;');
		
		// $sql = "SELECT T173_Id,D024_Id,T010_Flag_Marca_Individual FROM T173 LEFT JOIN D024 ON D024_Id=T173_D024_Id LEFT JOIN T010 ON T010_Id=T173_T010_Id WHERE T173_T010_Id='{$acaoId}'";
		// $resultado = mysql_query($sql);
		// //$row = mysql_fetch_array($resultado);
		// while ($row = mysql_fetch_array($resultado)){
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> Valor Unit.",'colunaAlinhar' => 'center', 'callback' => 'campoValorUnitario','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> %",'colunaAlinhar' => 'center', 'callback' =>'campoPernc','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> Total",'colunaAlinhar' => 'center', 'callback' =>'campoTotal','callbackParameter' => "{$row['T173_Id']}");
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> OC",'colunaAlinhar' => 'center', 'callback' =>'campoOC','callbackParameter' => "{$row['T173_Id']}");
			// if($g['c004']['C004_Flag_Isento_IPI'] != '1'){
			// $grid[] = array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => "{$row['D024_Id']} <br /> IPI",'colunaAlinhar' => 'center', 'callback' =>'campoIPI','callbackParameter' => "{$row['T173_Id']}");
			// }
		// }
        
		
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T174';

		$extra = <<<EOT
			LEFT JOIN T173 ON T174_T173_Id=T173_Id 
			LEFT JOIN T010 ON T010_Id=T173_T010_Id
			LEFT JOIN D024 ON D024_Id=T173_D024_Id 
			LEFT JOIN T059 ON T059_Id=T174_T059_Id 
			LEFT JOIN D009 ON D009_Id=T174_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			WHERE T174_T010_Id='{$T010_Id}' AND T174_T173_Id='$T173_Id' AND T174_Flag_Excluido='S'
EOT;
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				function atualizaTela() {
					divRefresh('{$g['divId']}', true);
				}
			} </script></div>
EOT;
		
			
			//Botões (Versão Acima do GRID)
			
			//Marcar Melhor Preço
			// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="est008IncluirProdFornecedor">Incluir</button>
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				// $('#est008IncluirProdFornecedor').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir produtos fornecedor', '/est/est008/content/est008ContentIncluirProdForn/', '&T010_Id=' + encodeURIComponent('{$T010_Id}') + '&T173_Id=' + encodeURIComponent('{$T173_Id}'), [650,480]);
				// });
			// } </script></div>
// EOT;

		$sql = "SELECT D024_Nome_Empresa from T173 left join D024 on D024_Id=T173_D024_Id WHERE T173_Id='{$T173_Id}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		//echo '<div style="padding:3px;">Fornecedor: <b>'.$linha['D024_Nome_Empresa'].'</b></div>';
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
		
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
  	break;
  	
  	case 'est008GridContatos':
		/**
		 * Definição do Grid (Tabela)
		 */		
		 
		 $sql = "SELECT T173_D024_Id FROM T173 WHERE T173_Id='{$r_T173_Id}'";
		 $result = mysql_query($sql);
		 $linha = mysql_fetch_array($result);
			
			$grid = array(
				array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D013_Id', $g['divId'])),
				array('colunaValor'=>'D013_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor'=>'D013_Nome_Contato', 'colunaTitulo'=>'Nome', 'colunaEditar'=>true),
				array('colunaValor'=>'D013_E_Mail', 'colunaTitulo'=>'Email', 'colunaEditar'=>true),
				array('colunaValor'=>'D013_DDD_Telefone_1', 'colunaTitulo'=>'DDD', 'style'=>'width:1%;', 'colunaEditar'=>true),
				array('colunaValor'=>'D013_Telefone_1', 'colunaTitulo'=>'Telefone', 'colunaEditar'=>true),
				array('colunaValor'=>'D013_Data_Nascimento', 'colunaTitulo'=>'Nascimento', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
				array('colunaValor'=>'D013_Tipo_Contato', 'colunaTitulo'=>'Tipo', 'colunaEditar'=>true),
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'D013';
			
					$extra = <<<EOT
					WHERE D013_D024_Id='{$linha['T173_D024_Id']}'
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D013_Id-/, '');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
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
         
        /**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesMid = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
  	break;
}
?>
