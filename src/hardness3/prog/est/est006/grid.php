<?php
namespace hardness;
/**
* Weiberlan
* 29/11/2011
*/
// Classes
//require_once('bibliotecas/classes/CAD001.php');
//$CAD001 = new CAD001();
// Carrega funções callback
require_once('prog/est/est006/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est006/grid_func-js.php');


switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'est006GridOrdensCompra':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
 			array('colunaValor' => 'T011_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style'=>'width:1%;'),
 			array('colunaValor' => 'T011_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'colunaAlinhar'=>'center', 'style'=>'width:1%;'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Cód', 'style'=>'width:1%;'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Fornecedor' ),
			array('colunaValor' => 'T011_Valor_Total_Custo', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T011_Valor_Total_Pendente', 'colunaTitulo' => 'Valor Pendente', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'T011_Flag_Tipo', 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => '(select count(*) from T012 where T012_T011_Id=T011_Id) as Itens', 'colunaTitulo' => 'Itens', 'colunaAlinhar'=>'center', 'style'=>'width:1%;'), 
			//array('colunaValor' => '(select group_concat(distinct T059_T010_Id) from T059 left join T012 on T012_T059_Id=T059_Id where T012_T011_Id=T011_Id) as Cotacao','colunaTitulo' => 'Cotação'),
			array('colunaValor' => 'T011_T010_Id','colunaTitulo' => 'Cotação', 'style'=>'width:1%;'),
			array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa','colunaExibir' => false),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'T011_Status_Aprovacao', 'colunaTitulo' => 'Status', 'callback'=>'validaCampoStatus'),
			array('colunaValor' => 'T011_C007_Id_Aprovador', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
      );
		
		/**
		 * Definição da SQL
		 */
		$from = "T011";
		
		$extra = <<<EOT
		    left join D024 on D024_Id=T011_D024_Id
		    left join C007 on C007_Id=T011_C007_Id
            left join C004 on C004_Id=T011_C004_Id
		    where T011_C004_Id='[[g-empresaAtual]]'
EOT;

/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est006IncluirNotaFiscal_Btn">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est006IncluirNotaFiscal_Btn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Ordem de Compra', '/est/est006/content/est006ContentIncluirOrdemCompra/', '&tab=geral', [450,330]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T011_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'T011_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T011_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T011_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra: ' + id, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,600]);
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
  	
  	case 'est006GridEditarOrdemCompra':
			
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('T012_Id', $g['divId'])),
			array('colunaValor' => 'T012_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T012_Codigo_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'D049_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Cód. Fornecedor'),
			array('colunaValor' => 'concat(T012_Descricao_Produto, if(D001_Flag_Pre_Cadastro="S"," (PRE)","")) as Produto', 'colunaTitulo' => 'Descrição Produto'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T012_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T012_Quantidade_Falta_Lancar(T012_Id,0) as T012_Quantidade_Pendente', 'colunaTitulo' => 'Pendente', 'colunaAlinhar' => 'right', 'callback'=>'validaCampoPendente'),
			//array('colunaValor' => 'D049_Valor_Preco_Tabela', 'colunaTitulo' => 'Preço Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'D049_Percentual_Desconto_Tabela', 'colunaTitulo' => '% Desc Tabela', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T012_Desconto_Tabela', 'colunaTitulo'=>'% Desconto', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T012_Valor_Custo_Unitario', 'colunaTitulo'=>'Valor Unitário', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T012_Valor_Total_Custo', 'colunaTitulo'=>'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T012_Aliquota_ICMS', 'colunaTitulo'=>'ICMS', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T012_Aliquota_IPI', 'colunaTitulo'=>'IPI', 'colunaEditar' => true, 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo'=>'Pedido Venda'),
			array('colunaValor' => 'T059_T010_Id', 'colunaTitulo'=>'Cotação Compra'),
			array('colunaValor' => 'agregados', 'colunaTitulo'=>'Agregados', 'callback' => 'campoAgregadosOrdemCompra', 'style' => 'width: 1%', 'colunaTipo' => 'livre', 'colunaExibir' => false),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T012';

		$extra = <<<EOT
			left join D009 on D009_Id=T012_D009_Id 
			left join D049 on D049_Id=D009_D049_Id 
			left join D082 on D082_Id=D049_D082_Id 
			left join D001 on D049_D001_Id=D001_Id 
			left join T011 on T011_Id=T012_T011_Id 
			left join D037 on D037_Id=D001_D037_Id 
			left join T006 on T006_Id=T012_T006_Id 
			left join T059 on T059_Id=T012_T059_Id 
			where T012_T011_Id='{$acaoId}'
			order by substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))),floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20))
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
				var id = $(this).closest('tr').attr('id').replace(/T012_Id-/, '');
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
	
	case 'est006GridEmail':
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
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T011_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
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
		
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est006EnviarEmail">Enviar E-mail</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est006EnviarEmail').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T011_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
			});
		} </script></div>
EOT;
	break;
	
	case 'est006GridPagamento':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				array('colunaValor' => 'T190_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
				array('colunaValor' => 'T190_Numero_Parcela', 'colunaTitulo' => 'Parcela'),
				array('colunaValor' => 'T190_Prazos_Dias', 'colunaTitulo' => 'Prazos', 'colunaEditar' => true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T190_Prazos_Dias"),
				array('colunaValor' => 'T190_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'colunaEditar'=>true, 'colunaEditarJSCallback' => "colunaEditarCallbackJS_T190_Prazos_Dias"),
				array('colunaValor' => 'est006_Dia_Semana', 'colunaTitulo' => 'Dia da Semana', 'colunaTipo'=>'livre' ,'callback' => "est006_Dia_Semana"),
				array('colunaValor' => 'T190_Valor_Parcela', 'colunaTitulo' => 'Valor Parcela', 'colunaEditar' => true, 'colunaCasasDecimais'=>'2',  'colunaEditarJSCallback' => "colunaEditarCallbackJS_T190_Prazos_Dias"),
				array('colunaValor' => 'T190_D031_Id', 'colunaTitulo' => 'Portador', 'colunaEditar' => true,'colunaEditarJSCallback' => "colunaEditarCallbackJS_T190_Prazos_Dias", 'colunaEditarGeraSelect' => array('D031', 'D031_Id', 'D031_Portador', 'order by D031_Portador', true), 'callback' => 'T190_D031_Id_Portador'),
				array('colunaValor' => 'D031_Portador', 'colunaTitulo' => 'Portador', 'colunaExibir' => false),
				//array('colunaValor' => 'ven002_Detalhes', 'colunaTitulo' => '', 'colunaTipo'=>'livre' ,'callback' => "ven012_Detalhes"),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T190';

			$extra = <<<EOT
				left join D031 on D031_Id=T190_D031_Id
				WHERE T190_T011_Id = {$r_T011_Id}
EOT;

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
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T190_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
				});
				
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
					return false;
				});
				
				// Função JS executada como callback do editar campo (colunaEditarJSCallback)
				function colunaEditarCallbackJS_T190_Prazos_Dias(T190_Id, campoEditado, valorAntigo, novoValor) {
					T190_Id = T190_Id.replace(/T190_Id-/, '');
					if (campoEditado == 'T190_Prazos_Dias') {
						$.getJSON('/est/est006/grid_func-ajax/atualizarParcelas/?ajax=true&T190_Id='      		+ encodeURIComponent(T190_Id) + 
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
					} else if (campoEditado == 'T190_Data_Vencimento') {
						$.getJSON('/est/est006/grid_func-ajax/atualizarParcelas/?ajax=true&T190_Id='      		+ encodeURIComponent(T190_Id) + 
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
					} else if (campoEditado == 'T190_Valor_Parcela') {
						$.getJSON('/est/est006/grid_func-ajax/atualizarParcelas/?ajax=true&T190_Id='      		+ encodeURIComponent(T190_Id) + 
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
					} else if (campoEditado == 'T190_D031_Id') {
						divRefresh('{$g['divId']}');						
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

			$novoIdTelaForm = uniqid();
   break;
   
   case 'est006GridContatos':
		/**
		 * Definição do Grid (Tabela)
		 */		
		 
		 $sql = "SELECT T011_D024_Id FROM T011 WHERE T011_Id='{$r_T011_Id}'";
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
					WHERE D013_D024_Id='{$linha['T011_D024_Id']}'
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

	case 'est006GridAgregados':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback'=>'excluirAgregadoOrdemCompra', 'style' => 'width:1%'),
		array('colunaValor' => 'T210_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir' => false),
		array('colunaValor' => 'T210_Descricao', 'colunaTitulo' => 'Descrição'),
		array('colunaValor' => 'T210_Observacao', 'colunaTitulo' => 'Observação'),
		array('colunaValor' => 'T210_Valor_Unitario', 'colunaTitulo' => 'Valor','colunaCasasDecimais' => '2'),
		array('colunaValor' => 'T209_Nome', 'colunaTitulo' => 'Tipo'),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T210';

		$extra = <<<EOT
			LEFT JOIN T209 ON T209_Id = T210_T209_Id 
			where 
			T210_T012_Id = {$r_acaoId}
EOT;
			//Define ação do click de abertura (ao clicar em uma linha do grid)

			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T210_Id-/, '');
					divSetarAcaoId('{$r_divReload}', id);
					divRefresh('{$r_divReload}');
			    });
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

}
?>
