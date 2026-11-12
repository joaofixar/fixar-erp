<?php
namespace hardness;
/**
* Weiberlan
* 28/11/2011
*/
/** Classes
require_once('bibliotecas/classes/CAD001.php');
$CAD001 = new CAD001();
*/


//Carrega funções callback
	require_once('prog/erp/erp003/grid_func-callback.php');

//Carrega funções do ajax
	require_once('prog/erp/erp003/grid_func-js.php');



switch($g['r'][3]){

  	case 'erp003GridReceber':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoPag'),
			array('colunaValor' => "T002_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
			array('colunaValor' => "T002_Numero_Documento", 'colunaTitulo' => 'N.Doc.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "T002_T035_Id", 'colunaTitulo' => 'Rm', 'callback' => 'validaCampoRm', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "CampoBx", 'colunaTipo' => "livre", 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBxPag', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "campoExclusao2", 'colunaTipo' => "livre", 'colunaTitulo' => 'Rp', 'callback' => 'validaCampoRp', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
			array('colunaValor' => "D024_Nome_Empresa", 'colunaTitulo' => 'Empresa'),
			array('colunaValor' => "T002_Numero_Duplicata", 'colunaTitulo' => 'N.Dupl.'),
			array('colunaValor' => "T002_Data_Emissao", 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "datediff(T002_Data_Vencimento,T002_Data_Emissao) as Prazo", 'colunaTitulo' => 'Prz'),
			array('colunaValor' => "T002_Data_Vencimento", 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "datediff(if(T002_Data_Recebimento='0000-00-00',curdate(),T002_Data_Recebimento),T002_Data_Vencimento) as Atraso", 'colunaTitulo' => 'Ats'),
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
			array('colunaValor' => "D014_SubConta", 'colunaTitulo' => 'Conta'),
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
			left join D046 on D046_Id=D024_D046_Id
			left join T007 on T007_Id=T002_T007_Id
			left join T035 on T035_Id=T002_T035_Id
			left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
			left join T103 on T002_Id=T103_T002_Id
			where (T002_C004_Id={$g['empresaAtual']}
			and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) and D024_Id={$r_D024_Id})
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T002_Data_Recebimento']!='0000-00-00'){
				return 'linhaCinza';
			} 
			else if ($array['todosCampos']['T002_Flag_Status']=='2')
			{
				return 'linhaVermelho';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'T002_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T002_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T002_Data_Recebimento' => array('titulo' => 'Recebimento', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);		

		/**
		* Botões (Versão Acima do GRID)
		*/
		// Não executar quando houver o refresh de linha
		if (empty($r_linhaGridId)) {
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fin001Incluir">Incluir</button>
				<button type="button" id="fin001Remessa">Remessa</button>
				<button type="button" id="fin001Baixar">Baixar</button>
				<button type="button" id="fin001Agrupar">Agrupar</button>
				<button type="button" id="fin001Reparcelar">Reparcelar</button>	
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Receber', '/fin/fin001/content/fin001content02/', '', [600,500]);
				});			
				$('#fin001Remessa').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaRemessa/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Gerar Remessa Bancária', '/fin/fin001/content/fin001content03/', '', [325,150]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Remessa', request.data, buttons);							
						}
					});
				});				
				$('#fin001Agrupar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaAgrupar/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Agrupar', '/fin/fin001/content/fin001content04/', '&extra={$extra_}', [700,500]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Agrupamento', request.data, buttons);							
						}
					});
				});				
				$('#fin001Baixar').unbind('click').bind('click', function(data) {			    			
				    $('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaBaixar/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Baixar', '/fin/fin001/content/fin001content05/', '&extra={$extra_}', [900,550]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}		
							dialogConfirm('Baixa de Títulos', request.data, buttons);							
						}
					});
				});
				$('#fin001Reparcelar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin001/grid_func-ajax/verificaReparcelar/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code == 0) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Duplicatas para Reparcelamento', '/fin/fin001/content/fin001ContentPagamento/', '&extra={$extra_}', [950,700]);
						} else {
							var buttons = {
									"Ok": function() { $(this).dialog("destroy").remove(); }
								}
							dialogConfirm('Reparcelar', request.data, buttons);
						}
					});
				});
			} </script></div>
EOT;
		}

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);		
		
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
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTitulos}" size="11" readonly></span>
				<span class="item" style="text-align:right">Recebido<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorTotalRecebido}" size="11" readonly></span>
				<span class="item" style="text-align:right">Atrasado<br><input type="text" class="whiteBg" id="inputAtrasado" value="{$valorTotalAtrasado}" size="11" readonly></span>
				<span class="item" style="text-align:right">A Receber<br><input type="text" class="whiteBg" id="inputAReceber" value="{$valorAReceber}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total Receber<br><input type="text" class="whiteBg" id="inputTotalReceber" value="{$valorTotalAReceber}" size="11" readonly></span>
				<span class="item" style="width:10px;border-right:1px solid lightblue"> </span>
				<span class="item" style="width:10px;"> </span>
				<span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" size="11" readonly></span>
				<span class="item" style="text-align:right">Reparcelar<br><input type="text" class="whiteBg" id="inputReparcelar" size="11" readonly></span>
				<span class="item" style="text-align:right">Agrupar<br><input type="text" class="whiteBg" id="inputAgrupar" size="11" readonly></span>
				<span class="item" style="text-align:right">Remessa<br><input type="text" class="whiteBg" id="inputRemessa" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001ExtraBtns').data('extra', '{$extra__}');
				carregaInputBaixar();
				carregaInputReparcelar();
				carregaInputAgrupar();
				carregaInputRemessa();
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
				var id = $(this).closest('tr').attr('id').replace(/T002_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Receber - ID: ' + id, '/fin/fin001/content/fin001content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,500]);
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
	
	case 'erp003GridPagar':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
		array('colunaValor' => 'D014_Grupo', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
		array('colunaValor' => 'T015_Flag_Baixar_Lote', 'colunaTitulo' => 'Bx', 'callback' => 'validaCampoBx'),
		array('colunaValor' => 'T015_Id', 'colunaTitulo' => 'ID', 'chavePrimaria'=>true),
		array('colunaValor' => "D024_Id", 'colunaTitulo' => 'Cod', 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Fornecedor'),
		array('colunaValor' => 'IF(D154_Id>0,CONCAT(D154_Descricao," - ",D014_SubConta),D014_SubConta)', 'colunaTitulo' => 'Conta'),
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
				left join T007 on T007_Id=T015_T007_Id
				left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				where 
				(T015_C004_Id={$g['empresaAtual']} and D024_Id={$r_D024_Id})
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T015_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T015_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T015_Data_Pagamento' => array('titulo' => 'Pagamento', 'tipo' => 'data'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_ = urlencode(base64_encode($extra));
		$extra__ = base64_encode($extra);			

		/**
		* Botões (Versão Acima do GRID)
		*/
		// Não executar quando houver o refresh de linha
		if (empty($r_linhaGridId)) {
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fin002Incluir">Incluir</button>
				<button type="button" id="fin002Baixar">Baixar</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/fin/fin002/content/fin002content02/', '', [600,500]);
				});			
				$('#fin002Baixar').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
					$.getJSON('/fin/fin002/grid_func-ajax/verificaBaixar/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code==0) {
				    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Baixar', '/fin/fin002/content/fin002content03/', '&extra={$extra_}', [1100,500]);
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
		}
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
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
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Títulos<br><input type="text" class="whiteBg" value="{$totalTitulos}" size="11" readonly></span>
				<span class="item" style="text-align:right">Baixar<br><input type="text" class="whiteBg" id="inputBaixar" size="11" readonly></span>
				<span class="item" style="text-align:right">Pago<br><input type="text" class="whiteBg" id="inputPago" size="11" value="{$valorTotalPago}" readonly></span>
				<span class="item" style="text-align:right">Vencido<br><input type="text" class="whiteBg" id="inputVencido" value="{$valorTotalVencido}" size="11" readonly></span>
				<span class="item" style="text-align:right">A vencer<br><input type="text" class="whiteBg" id="inputAPagar" value="{$valorAVencer}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total pagar<br><input type="text" class="whiteBg" id="inputTotalPagar" size="11" value="{$valorTotalAPagar}" readonly></span>
				<div id="fin002ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fin002ExtraBtns').data('extra', '{$extra__}');
				carregaInputBaixar();
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
				var id = $(this).closest('tr').attr('id').replace(/T015_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Título a Pagar - ID: ' + id, '/fin/fin002/content/fin002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,500]);
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
	
	case 'erp003GridBoletos':
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => 'T117_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'T117_T035_Id', 'colunaTitulo' => 'ID', 'colunaExibir' => false),// se necessário utilizar o T002_T035_Id 
			array('colunaValor' => 'T117_Flag_Layout', 'colunaTitulo' => 'FlagLayout', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Flag_Cancelado', 'colunaTitulo' => 'FlagLayout', 'colunaExibir' => false),
			array('colunaValor' => 'T117_Nosso_Numero', 'colunaTitulo' => 'Nosso Número'),
			array('colunaValor' => "group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') as T002_Numero_Duplicata", 'colunaTitulo' => 'Duplicata'),
			array('colunaValor' => 'D007_Nome_Banco', 'colunaTitulo' => 'Banco'),
			array('colunaValor' => 'T117_D024_Id', 'colunaTitulo' => 'IdCliente','colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'T117_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T002_Data_Recebimento', 'colunaTitulo' => 'Recebido', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T117_Valor_Total', 'colunaTitulo' => 'Total', 'colunaCasasDecimais'=>'2'),
			array('colunaValor' => 'campoCancelar', 'colunaTipo' => 'livre', 'callback' => 'campoCancelar'),
			array('colunaValor' => 'T117_Data_Envio_Email', 'colunaTitulo' => 'eMail', 'callback' => 'campoeMail'),
			array('colunaValor' => 'D024_E_Mail', 'colunaTitulo' => 'E-mail'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'T117';
			$extra = <<<EOT
				left join D024 on D024_Id=T117_D024_Id
		 		left join T002 on T002_T117_Id=T117_Id
		 		left join D007 on D007_Id=T117_D007_Id
		 		where 
		 		T117_C004_Id='1' and D024_Id={$r_D024_Id}
		 		GROUP BY T117_Id
EOT;

			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="fin005GridPrincipalBtnIncluir">Incluir</button>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
					$('#fin005GridPrincipalBtnIncluir').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de boleto', '/fin/fin005/content/fin005ContentAbreCadastroBoleto/', '&tab=geral', [900,550]);
					});
				} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'T117_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T117_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
			'T117_Data_Pagamento' => array('titulo' => 'Recebido', 'tipo' => 'data'),
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
			$valorRegistros = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, str_replace('GROUP BY T117_Id', '', $extra));
			$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T117_Valor_Total)", $from, str_replace('GROUP BY T117_Id', '', $extra)));
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Boletos<br><input type="text" value="{$valorRegistros}" class="whiteBg" id="" size="11" readonly></span>
					<span class="item" style="text-align:right">Valor Total Geral<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
					var chavePrimaria = 'T117_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela do browser 
					// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
					nova_janela('{$confUsuario['urlRaiz']}/hardness3/hardness/boleto/gerar_boleto.php?T117_Id='+encodeURIComponent(acaoId),0,0,800,500,'no','no','no','no','yes','yes',0);
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
	
}
?>
