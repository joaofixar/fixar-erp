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
require_once("old/lib/funcoes.php");

//Carrega funções callback
require_once('prog/erp/erp001/grid_func-callback.php');

//Carrega funções do ajax
require_once('prog/erp/erp001/grid_func-js.php');

switch($g['r'][3]){
	case 'erp001GridPrincipalOrcamentos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' =>'campoExclusao'),
			array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T003_D024_Id', 'colunaTitulo' => 'Cod. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold' ),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold' ),
			array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaExibir' => false),
			array('colunaValor' => 'T003_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
			array('colunaValor' => 'globalStatusOrcamento as Status', 'colunaTipo' => 'livre','colunaTitulo' => 'Status', 'callback' => 'campoStatus'),
			array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - ",T005_Valor_Total) separator ", ") as Pedido','colunaTitulo' => 'Pedidos', 'callback'=>'validaCampoPedido', 'style' => 'width:1%'),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF', 'style' => 'font-weight:bold','callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'concat(D013_Nome_Contato," ",D013_Telefone_1) as D013_Nome_Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left' ),
			array('colunaValor' => 'Status as Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipal'),			
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T003_Observacao_Orcamento_Perdido', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			//array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaTitulo' => 'T003_Flag_Status_Orcamento','colunaExibir'=>false),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
						FROM T004 
						LEFT JOIN D009 ON D009_Id = T004_D009_Id
						LEFT JOIN D049 ON D049_Id = D009_D049_Id
						LEFT JOIN D082 ON D082_Id = D049_D082_Id
						WHERE T004_T003_Id=T003_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false)
		);

		/**
		* Definição da SQL
		*/
		$from = 'T003';
		$extra = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
			left join T004 on T004_T003_Id=T003_Id
			left join T006 on T006_T004_Id=T004_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id and T005_Flag_Status!='8'
		    left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}')
		    group by T003_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}')
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalAberto = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		      and T003_Flag_Perdido!='S'
		      and (select T005_Id from T005 where T005_T003_Id=T003_Id limit 1) is null)
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalFechado = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
			left join T004 on T004_T003_Id=T003_Id
			left join T006 on T006_T004_Id=T004_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
		    left join T005 on T005_T003_Id=T003_Id
		    left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		      and T006_Id > 0)
		      GROUP BY T003_Id
EOT;

		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalPerdido = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		      and T003_Flag_Perdido = 'S')
EOT;

		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T005_Flag_Status']=='5'){
				return 'linhaCinza';
			}
			else if($array['todosCampos']['T003_Flag_Perdido']=='S'){
				return 'linhaVermelho';
			}
			else if($array['todosCampos']['T005_Flag_Status']<=0){
				if ($array['todosCampos']['T003_Flag_Status_Orcamento']=='V') {
					return 'linhaFundoAmareloClaro linhaAzul';
				} else {
					return 'linhaAzul';
				}
			}
		}

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven001GridPrincipalOrcamentos">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				//divId = {$g['divId']}
				setTimeout(function(){
					$('#Procura_Cliente_Pesquisa').focus();
				}, 200);
					
				$('#ven001GridPrincipalOrcamentos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '', [650,480]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'D013_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
			'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
			'T003_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			//'T003_Flag_Status_Orcamento' => array('titulo' => 'Status(F/P/C/V)', 'tipo' => 'texto'),
			'T003_Flag_Status_Orcamento' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Finalizado', 'value' => 'F'),
				array('title' => 'Pendente', 'value' => 'P'),
				array('title' => 'Cancelado', 'value' => 'C'),
				array('title' => 'Vencido', 'value' => 'V'),
			), 'naoGerarExtra' => false),		
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
							FROM T004 
							LEFT JOIN D009 ON D009_Id = T004_D009_Id
							LEFT JOIN D049 ON D049_Id = D009_D049_Id
							LEFT JOIN D082 ON D082_Id = D049_D082_Id
							WHERE T004_T003_Id=T003_Id)' => array('titulo'=>'Marca', 'tipo'=>'texto')
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
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		* Totais(Versão Acima do GRID)
		*/
		/**
		 * Totalizadores
		 */
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotal);
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T003_Valor_Total)", $from, $extraTotal));
		$registrosAberto = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotalAberto);
		$valorTotalAberto = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T003_Valor_Total)", $from, $extraTotalAberto));
		$registrosFechado = $g['sqlAuto']->pegarSqlCampo("count(distinct T005_Id)", $from, $extraTotalFechado, $extraTotalFechado);
		$valorTotalFechado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T006_Valor_Total_Preco + T006_Valor_IPI)", $from,  str_replace('GROUP BY T003_Id', '', $extraTotalFechado)));
		$registrosPerdido = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotalPerdido);
		$valorTotalPerdido = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T003_Valor_Total)", $from, $extraTotalPerdido));
		//T003_Flag_Perdido='S'
		//echo "Registros: <br />" . "count(*)" . $from . $extra . "<br />    Valor Total: <br />" . "sum(T003_Valor_Total)" . $from . $extra;
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Orçamentos<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosAberto}" readonly></span>
				<span class="item" style="text-align:right">Valor Total Abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalAberto}" readonly></span>
				<span class="item" style="text-align:right">Fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosFechado}" readonly></span>
				<span class="item" style="text-align:right">Valor Total Fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalFechado}" readonly></span>
				<span class="item" style="text-align:right">Perdidos<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosPerdido}" readonly></span>
				<span class="item" style="text-align:right">Valor Total Perdidos<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalPerdido}" readonly></span>
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
	
	case 'erp001GridPrincipalPedidos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoPedido', 'colunaExibir'=>false),
			array('colunaValor' => "campoCancelamento", 'colunaTipo' => "livre", 'callback' => 'campoCancelamento', 'style'=>'width:1%;'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'Cód. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'style' => 'font-weight:normal', 'colunaExibir' => false),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as Status_Pedido', 'colunaTitulo' => 'Status', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'group_concat("<span style=\"font-weight:bold\">",T005_Status_Pedido(T005_Flag_Status,1),"</span> ", ifnull((SELECT date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1),"")) as Status2', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF', 'style' => 'font-weight:bold','callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),                                
			array('colunaValor' => 'supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor'),
			array('colunaValor' => 'Status as Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipal'),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0,"MP","PE"))) FROM T006 WHERE T006_T005_Id=T005_Id) as Estoque', 'colunaTitulo' => 'Estoque', 'style'=>'width:1%;', 'colunaExibir'=>false),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
						FROM T006 
						LEFT JOIN D009 ON D009_Id = T006_D009_Id
						LEFT JOIN D049 ON D049_Id = D009_D049_Id
						LEFT JOIN D082 ON D082_Id = D049_D082_Id
						WHERE T006_T005_Id=T005_Id) as Marca', 'colunaTitulo' => 'Marcas', 'colunaExibir' => false)
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T005';
		$extra = <<<EOT
				left join D024 on D024_Id=T005_D024_Id
    			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     		    left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    			left join D006 on D006_Id=T005_D006_Id
    			where (T005_C004_Id={$g['empresaAtual']})
    			GROUP BY T005_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
				left join D024 on D024_Id=T005_D024_Id
    			left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     		    left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
		        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    			left join D006 on D006_Id=T005_D006_Id
    			where (T005_C004_Id={$g['empresaAtual']})
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

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Valor_Total' => array('titulo' => 'Valor Total', 'tipo' => 'moeda'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'(SELECT GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0[[virgula]]"MP"[[virgula]]"PE"))) FROM T006 WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Estoque', 'tipo' => 'texto', 'exibir'=>false),
			'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
									FROM T006 
									LEFT JOIN D009 ON D009_Id = T006_D009_Id
									LEFT JOIN D049 ON D049_Id = D009_D049_Id
									LEFT JOIN D082 ON D082_Id = D049_D082_Id
									WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Marca', 'tipo'=>'texto'),
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + T005_Id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T005_Id) + '&tabela={$from}', [950,615]);            
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
	
	case 'erp001GridPrincipalNotasFiscais':
		
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'excluirNFe'),
			array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria'=>true),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'Número' ),
			array('colunaValor' => 'T007_Flag_ACP', 'colunaTitulo' => 'Tipo', 'callback'=>'validaTipo'),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'T007_Valor_Total_Produtos', 'colunaTitulo' => 'Produtos','colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T007_Valor_Total', 'colunaTitulo' => 'Total','colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
			array('colunaValor' => 'substr(D021_Nome_Empresa,1,10) as D021_Nome_Empresa', 'colunaTitulo' => 'Representante'),
			array('colunaValor' => 'Danfe', 'colunaTitulo' => 'Danfe', 'colunaTipo'=>'livre', 'callback'=>'ValidaCampoDanfe'),
			array('colunaValor' => 'NF', 'colunaTitulo' => 'NF', 'colunaTipo'=>'livre', 'callback'=>'validaCampoNF'),
			array('colunaValor' => 'ACP', 'colunaTitulo' => 'ACP', 'colunaTipo'=>'livre', 'callback'=>'validaCampoACP'),
			//array('colunaValor' => 'Inutilizar', 'colunaTitulo' => 'Inutilizar', 'colunaTipo'=>'livre', 'callback'=>'validaCampoInutilizar'),
			array('colunaValor' => 'T007_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback'=>'validaCampoCancelar'),
			//array('colunaValor' => 'Almoxarifado', 'colunaTitulo' => 'Almoxarifado', 'colunaTipo'=>'livre', 'callback'=>'validaCampoAlmoxarifado'),
			array('colunaValor' => 'T007_Numero_Protocolo_Nfe', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'T007_Data_Envio_XML', 'colunaTitulo' => 'eMail', 'colunaTipo'=>'Livre', 'callback'=>'validaCampoEmail'),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                        FROM T008 
                                        LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                        WHERE T008_T007_Id=T007_Id) as Tipos', 'colunaTitulo' => 'Tipos','colunaExibir' => false),
            array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                        FROM T008 
                                        LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        WHERE T008_T007_Id=T007_Id) as Marcas', 'colunaTitulo' => 'Marcas','colunaExibir' => false),
			array('colunaValor' => 'Retorno Sefaz', 'colunaTipo'=>'livre', 'colunaTitulo' =>'Retorno SEFAZ', 'colunaTipo'=>'livre', 'callback'=>'chamaCampo'),
			array('colunaValor' => 'T007_Numero_Protocolo_Nfe as Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NFe'),
			array('colunaValor' => 'T007_Numero_Protocolo_Cancelamento_Nfe', 'colunaTitulo' => 'Prot. Cancel'),
			array('colunaValor' => 'T007_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso', 'callback'=>'ValidaChaveAcesso'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Versao_Nfe', 'colunaTitulo' => 'Versão', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'Status', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Flag_Impresso', 'colunaTitulo' => 'Impressão', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status', 'colunaTitulo' => 'Status Pedido', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Data_Hora_Inutilizacao', 'colunaTitulo' => 'Hora Inutilização', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Flag_Inutilizado', 'colunaTitulo' => 'Inutilização', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Numero_Recibo_Nfe', 'colunaTitulo' => 'Recibo', 'colunaExibir'=>false),
			array('colunaValor' => 'T007_Data_Envio_XML as Data_Envio_XML', 'colunaTitulo' => 'XML', 'colunaExibir'=>false),
			array('colunaValor' => '(select ifnull(T127_Id,0) from T127 where T127_Numero_Nota_Fiscal=T007_Numero_Nota_Fiscal and T127_C004_Id=T007_C004_Id limit 1) as T127_Id', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => '(select T008_Codigo_Produto from T008 where T008_T007_Id=T007_Id limit 1) as T008_Codigo_Produto', 'colunaTitulo' => '', 'colunaExibir'=>false),
		);
		//retira a coluna de exclusão da NF-e caso o usuário não seja o 63(root)
		if($g['usuarioAtual'] != 63){
			unset($grid[0]);
		}
		
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
				    left join D024 on D024_Id=T007_D024_Id
				    left join D018 on D018_Id=D024_D018_Id
				    left join D006 on D006_Id=T007_D006_Id
				    left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
				    left join D021 on D021_Id=D024_D021_Id
				    where (T007_C004_Id='{$g['empresaAtual']}'
				    and T007_Flag_Emitida_Recebida!='R')
				    
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven003IncluirNotaFiscal">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#ven003IncluirNotaFiscal').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','', [900,550]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D006_Descricao,D006_Tipo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			//'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo (E/S)', 'tipo' => 'texto'),
			'D006_Flag_Entrada_Saida' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todas', 'value' => ''),
				array('title' => 'Entrada', 'value' => 'E'),
				array('title' => 'Saída', 'value' => 'S'),
			), 'naoGerarExtra' => false),
			'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),			
			'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),			
			'T007_Valor_Total' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo)) as Tipo
                                        FROM T008 
                                        LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                                        WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Tipos', 'tipo' => 'texto','exibir' => false),
            '(SELECT GROUP_CONCAT(DISTINCT(D082_Marca)) as Marca
                                        FROM T008 
                                        LEFT JOIN D009 ON D009_Id=T008_D009_Id 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                        LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                        WHERE T008_T007_Id=T007_Id)' => array('titulo' => 'Marcas', 'tipo' => 'texto','exibir' => false),
            //'T007_Flag_Cancelada' => array('titulo' => 'Cancelada (S/N)', 'tipo' => 'texto'),
			'T007_Flag_Cancelada' => array('titulo' => 'Cancelada', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todas', 'value' => ''),
				array('title' => 'Sim', 'value' => 'S'),
				array('title' => 'Não', 'value' => 'N'),
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
		$valorTotal1 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T007_Valor_Total_Produtos)", $from, $extra));
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T007_Valor_Total) Total", $from, $extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Notas Emitidas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		
		$novoIdTelaForm = uniqid();
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T007_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: ' + id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [900,550]);
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
	
	case 'erp001gridPrincipalProdutos':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T008_Id', $g['divId'])),
			array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF'),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Data Emissão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor' => 'T008_Codigo_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'T008_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T008_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T008_Flag_Situacao_Tributaria', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T008_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Aliquota_ICMS', 'colunaTitulo' => '% ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T008_Aliquota_IPI', 'colunaTitulo' => '% IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T008_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T008_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T008_St_Va', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T008_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T008";
		
		$extra = <<<EOT
		            left join D009 on D009_Id=T008_D009_Id
		            left join D049 on D049_Id=D009_D049_Id
		            left join D082 on D082_Id=D049_D082_Id
		            left join D001 on D001_Id=D009_D001_Id
		            left join D006 on D006_Id=T008_D006_Id
		            left join D037 on D037_Id=T008_D037_Id
		            left join T007 on T007_Id=T008_T007_Id
		            left join D024 on D024_Id=T007_D024_Id
		            left join C008 on C008_Id=D001_C008_Id
		            left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno            	
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);	
		
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
						var D001_Id = id[1].replace(/D001_Id-/, '');
						var D024_Id = id[2].replace(/D024_Id-/, '');
					   abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [1000,600]);
				   });
				   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF', 'tipo' => 'numero'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T008_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
		);
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
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
	
	case 'erp001GridPrincipalClientesAtivos':
		/**
		 * Definição do Grid (Tabela)
		 */		
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D024_Id', $g['divId'])),
			array('colunaValor'=>'CampoCadastro', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'mostraImagemCadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;'),
			array('colunaValor'=>'D085_Data', 'colunaTitulo'=>'Último Contato', 'callback' => 'mostraImagemUltimoContato', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;'),
			array('colunaValor'=>'campoDoc', 'colunaTipo'=>'livre', 'colunaTitulo'=>'Doc','callback'=>'mostraImagemDoc', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;'),
			array('colunaValor'=>'D024_Flag_Curva_ABC', 'colunaTitulo'=>'ABC', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
			array('colunaValor'=>'D024_Flag_Classe', 'colunaTitulo' => 'Cla', 'colunaAlinhar' => 'center', 'callback' => 'colunaClasseCliente', 'callbackParameter' => array('Classe cliente', '/cad/cad001/outro/classeCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
			array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'Código', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
			array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Razão Social'),
			array('colunaValor'=>'D024_Nome_Fantasia', 'colunaTitulo'=>'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Bairro', 'colunaTitulo'=>'Bairro'),
			array('colunaValor'=>'D020_Nome_Cidade', 'colunaTitulo'=>'Cidade'),
			array('colunaValor'=>'D018_UF', 'colunaTitulo'=>'UF'),
			array('colunaValor'=>'substr(D021_Nome_Empresa,1,10) as D021_Nome_Empresa', 'colunaTitulo'=>'Representante'),
			array('colunaValor'=>'D046_Nome', 'colunaTitulo'=>'Grupo'),
			//array('colunaValor'=>'D084_Regiao', 'colunaTitulo'=>'Região'),
			array('colunaValor'=>'D024_CNPJ', 'colunaTitulo'=>'CNPJ'),
			array('colunaValor'=>'D024_CPF', 'colunaTitulo'=>'CPF'),
			array('colunaValor'=>'D024_RG', 'colunaTitulo'=>'RG'),
			array('colunaValor'=>'D024_Data_Cadastro', 'colunaTitulo'=>'Cadastro', 'callback' => 'gCorrigeData'),
			array('colunaValor'=>'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo'=>'Vendedor'),
			array('colunaValor'=>'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo'=>'Supervisor'),
			array('colunaValor'=>'D024_Flag_Pre_Cadastro', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Flag_Comercial', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Flag_Tempo', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D084_Regiao', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'(select D087_Id from D087 where D087_D024_Id=D024_Id limit 1) as D087_Id', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D085_Mensagem', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'datediff(current_date(),D085_Data) as D085_Dias', 'colunaTitulo'=>'', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D024';
		$extra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D084 on D084_Id=D020_D084_Id
			left join D085 on D085_Id=D024_D085_Id
			left join D046 on D046_Id=D024_D046_Id
			left join D021 on D021_Id=D024_D021_Id
			left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			where D024_Flag_Ativo='S'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
EOT;
		if (isset($g['c029Ids'][103])) {
		echo <<<EOT
				<button type="button" id="cad001Incluir">Incluir</button>
EOT;
		}
		echo <<<EOT
				<button type="button" id="cad001EtiquetasClientes">Etiquetas</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [950,550]);
				});
				$('#cad001EtiquetasClientes').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Etiquetas', '/cad/cad001/content/cad001ContentImprimirClientes/','', [300,200]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa, D024_Nome_Fantasia' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Id' => array('titulo' => 'Código', 'tipo' => 'Numero'),
			'D085_Data' => array('titulo' => 'Último Contato', 'tipo' => 'data'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D024_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'D024_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
			'D024_RG' => array('titulo' => 'RG', 'tipo' => 'texto'),
			'D024_Data_Cadastro' => array('titulo' => 'Data cadastro', 'tipo' => 'data'),
			'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Empresas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [960,583]);
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
	
	case 'erp001GridPrincipalClientesInativos':

		/**
		 * Definição do Grid (Tabela)
		 */		

		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D024_Id', $g['divId'])),
			array('colunaValor'=>'CampoCadastro', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'mostraImagemCadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;'),
			array('colunaValor'=>'D085_Data', 'colunaTitulo'=>'Último Contato', 'callback' => 'mostraImagemUltimoContato', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;'),
			array('colunaValor'=>'campoDoc', 'colunaTipo'=>'livre', 'colunaTitulo'=>'Doc','callback'=>'mostraImagemDoc', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;'),
			array('colunaValor'=>'D024_Flag_Curva_ABC', 'colunaTitulo'=>'ABC', 'colunaAlinhar' => 'center', 'callback' => 'colunaCurvaABC', 'callbackParameter' => array('Estatisticas venda cliente', '/cad/cad001/outro/curvaABCCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
			array('colunaValor'=>'D024_Flag_Classe', 'colunaTitulo' => 'Cla', 'colunaAlinhar' => 'center', 'callback' => 'colunaClasseCliente', 'callbackParameter' => array('Classe cliente', '/cad/cad001/outro/classeCliente/', 'D024_Id', 'right', false, false, false, 'auto')),
			array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'Código', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
			array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Razão Social'),
			array('colunaValor'=>'D024_Nome_Fantasia', 'colunaTitulo'=>'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Bairro', 'colunaTitulo'=>'Bairro'),
			array('colunaValor'=>'D020_Nome_Cidade', 'colunaTitulo'=>'Cidade'),
			array('colunaValor'=>'D018_UF', 'colunaTitulo'=>'UF'),
			array('colunaValor'=>'substr(D021_Nome_Empresa,1,10) as D021_Nome_Empresa', 'colunaTitulo'=>'Representante'),
			array('colunaValor'=>'D046_Nome', 'colunaTitulo'=>'Grupo'),
			//array('colunaValor'=>'D084_Regiao', 'colunaTitulo'=>'Região'),
			array('colunaValor'=>'D024_CNPJ', 'colunaTitulo'=>'CNPJ'),
			array('colunaValor'=>'D024_CPF', 'colunaTitulo'=>'CPF'),
			array('colunaValor'=>'D024_RG', 'colunaTitulo'=>'RG'),
			array('colunaValor'=>'D024_Data_Cadastro', 'colunaTitulo'=>'Cadastro', 'callback' => 'gCorrigeData'),
			array('colunaValor'=>'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo'=>'Vendedor'),
			array('colunaValor'=>'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo'=>'Supervisor'),
			array('colunaValor'=>'D024_Flag_Pre_Cadastro', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Flag_Comercial', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D024_Flag_Tempo', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D084_Regiao', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'(select D087_Id from D087 where D087_D024_Id=D024_Id limit 1) as D087_Id', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'D085_Mensagem', 'colunaTitulo'=>'', 'colunaExibir' => false),
			array('colunaValor'=>'datediff(current_date(),D085_Data) as D085_Dias', 'colunaTitulo'=>'', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'D024';
		$extra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id
			left join D018 on D018_Id=D024_D018_Id
			left join D084 on D084_Id=D020_D084_Id
			left join D085 on D085_Id=D024_D085_Id
			left join D046 on D046_Id=D024_D046_Id
			left join D021 on D021_Id=D024_D021_Id
			left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			where D024_Flag_Ativo='N'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad001Incluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [950,550]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa, D024_Nome_Fantasia' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Id' => array('titulo' => 'Código', 'tipo' => 'Numero'),
			'D085_Data' => array('titulo' => 'Último Contato', 'tipo' => 'data'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
			'D018_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
			'D024_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
			'D024_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
			'D024_RG' => array('titulo' => 'RG', 'tipo' => 'texto'),
			'D024_Data_Cadastro' => array('titulo' => 'Data cadastro', 'tipo' => 'data'),
			'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
			'D021_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Empresas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/D024_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [950,553]);
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
		
	case 'erp001GridResumo':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoMensal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoAnual/', '');
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
			array('colunaValor' => "erp001_Dia_Semana", 'colunaTitulo' => 'Dia', 'callback' => "erp001_Dia_Semana", 'colunaTipo'=>'livre', 'style' => 'width:1%'),
			array('colunaValor' => "R001_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_Clientes_Nr", 'colunaTitulo' => 'Clientes', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Clientes_Novos", 'colunaTitulo' => 'Novos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Clientes_Ativos", 'colunaTitulo' => 'Ativos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Clientes_Inativos", 'colunaTitulo' => 'Inativos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Clientes_Contatos", 'colunaTitulo' => 'Contatos', 'colunaAlinhar' => 'center', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Faturados_Nr", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R001_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "R001_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "R001_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R001_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "R001_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "R001_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			WHERE 
				R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0
			 	AND R001_D049_Id = 0 
			 	AND R001_D024_Id = 0 
			 	AND R001_D006_Id = 0
			ORDER BY R001_Data
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
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
		$primeiraLinhaGridHead = <<<EOT
<tr>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Clientes</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;	
	
	case 'erp001GridResumoMensal':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumo/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'callback'=>"retornaMes", 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "R001_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "SUM(R001_Clientes_Nr) as R001_Clientes_Nr", 'colunaTitulo' => 'Clientes', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Clientes_Novos) as R001_Clientes_Novos", 'colunaTitulo' => 'Novos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Clientes_Ativos) as R001_Clientes_Ativos", 'colunaTitulo' => 'Ativos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Clientes_Inativos) as R001_Clientes_Inativos", 'colunaTitulo' => 'Inativos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Clientes_Contatos) as R001_Clientes_Contatos", 'colunaTitulo' => 'Contatos', 'colunaAlinhar' => 'center', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Orcamentos_Nr) as R001_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Clientes) as R001_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Itens) as R001_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Valor_Total) as R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Pedidos_Nr) as R001_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Clientes) as R001_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Itens) as R001_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Valor_Total) as R001_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_Nr) as R001_Faturados_Nr", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Faturados_Clientes) as R001_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Faturados_Itens) as R001_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Faturados_Valor_Total) as R001_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_IPV) as R001_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(R001_Faturados_IPV_Real) as R001_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(R001_Devolucoes_Nr) as R001_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Devolucoes_Clientes) as R001_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Devolucoes_Valor_Total) as R001_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Compras_Nr) as R001_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Compras_Clientes) as R001_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Compras_Valor_Total) as R001_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			WHERE 
				R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0
				AND R001_D049_Id = 0 
				AND R001_D024_Id = 0 
				AND R001_D006_Id = 0
			GROUP BY 
				Month(R001_Data)
			ORDER BY R001_Data
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);
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
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Clientes</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;	
		case 'erp001GridResumoAnual':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumo/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioResumo2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoMensal/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback'=>"retornaAno"),
			array('colunaValor' => "R001_C007_Id_Vendedor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_C007_Id_Supervisor", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "R001_D024_Id", 'colunaTitulo' => 'Data', 'colunaExibir' => false),
			array('colunaValor' => "SUM(R001_Clientes_Nr) as R001_Clientes_Nr", 'colunaTitulo' => 'Clientes', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Clientes_Novos) as R001_Clientes_Novos", 'colunaTitulo' => 'Novos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Clientes_Ativos) as R001_Clientes_Ativos", 'colunaTitulo' => 'Ativos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Clientes_Inativos) as R001_Clientes_Inativos", 'colunaTitulo' => 'Inativos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Clientes_Contatos) as R001_Clientes_Contatos", 'colunaTitulo' => 'Contatos', 'colunaAlinhar' => 'center', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Orcamentos_Nr) as R001_Orcamentos_Nr", 'colunaTitulo' => 'Orçamentos', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Clientes) as R001_Orcamentos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Itens) as R001_Orcamentos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Orcamentos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Orcamentos_Valor_Total) as R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Pedidos_Nr) as R001_Pedidos_Nr", 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Clientes) as R001_Pedidos_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Itens) as R001_Pedidos_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Pedidos_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Pedidos_Valor_Total) as R001_Pedidos_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_Nr) as R001_Faturados_Nr", 'colunaTitulo' => 'Notas', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Faturados_Clientes) as R001_Faturados_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Faturados_Itens) as R001_Faturados_Itens", 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => "R001_Faturados_Itens_Diferentes", 'colunaTitulo' => 'Itens Dif.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Faturados_Valor_Total) as R001_Faturados_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_IPV) as R001_Faturados_IPV", 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(R001_Faturados_IPV_Real) as R001_Faturados_IPV_Real", 'colunaTitulo' => 'IPV Real', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => "SUM(R001_Devolucoes_Nr) as R001_Devolucoes_Nr", 'colunaTitulo' => 'Devoluções', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => "SUM(R001_Devolucoes_Clientes) as R001_Devolucoes_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Devolucoes_Valor_Total) as R001_Devolucoes_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Compras_Nr) as R001_Compras_Nr", 'colunaTitulo' => 'Compras', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Compras_Clientes) as R001_Compras_Clientes", 'colunaTitulo' => 'Clientes', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Compras_Valor_Total) as R001_Compras_Valor_Total", 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "background-color: #D7ECF7;"),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);
		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			WHERE 
				R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0
				AND R001_D049_Id = 0 
				AND R001_D024_Id = 0 
				AND R001_D006_Id = 0
			GROUP BY 
				Year(R001_Data)
			ORDER BY R001_Data
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'R001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
		);
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
	<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Clientes</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Orçamentos</th>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Pedidos</th>
	<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Notas Fiscais</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Devoluções</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Compras</th>
</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;	
	
	case 'erp001GridABCClientes':
		
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
			array('colunaValor' => "ROUND((sum(R001_Faturados_Valor_Total)*100)/sum(R001_Orcamentos_Valor_Total), 0) as Porcentagem", 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '0', 'style' => 'width:1%'),		
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		$xLast12 = date('Y-m-d');
		$xLast12 = new \DateTime($xLast12);
		$xLast12->modify('-12 months');
		$xLast12 = $xLast12->format('Y-m-d');

		$extra = <<<EOT
			left join D024 on D024_Id=R001_D024_Id
			WHERE 
				R001_D024_Id > 0 
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id = 0 
				AND R001_D006_Id = 0
				AND R001_Data >= '{$xLast12}'
				
			group by 
				R001_D024_Id
			order by 
				sum(R001_Faturados_Valor_Total) desc
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
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
		$curvaABC = <<<EOT
			SELECT COUNT(DISTINCT R001_D024_Id) Quantidade,
			SUM(R001_Faturados_Valor_Total) Total,
			D024_Flag_Curva_ABC
			FROM R001 LEFT JOIN D024 ON R001_D024_Id = D024_Id
			WHERE 
				R001_D024_Id > 0 
				AND R001_Data LIKE '{$Ano}%'
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id = 0 
				AND R001_D006_Id = 0
			GROUP BY D024_Flag_Curva_ABC
EOT;
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
	
	case 'erp001GridABCProdutos':
		
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback'=>"retornaAno"),
			array('colunaValor' => "D049_Flag_Curva_Abc", 'colunaTitulo' => 'ABC', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "D001_Descricao_Produto", 'colunaTitulo' => 'Produto', 'colunaAlinhar' => 'left'),
			array('colunaValor' => "D082_Marca", 'colunaTitulo' => 'Marca', 'colunaAlinhar' => 'left'),
			array('colunaValor' => "SUM(R001_Orcamentos_Itens)", 'colunaTitulo' => 'Orçamentos Itens', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
			array('colunaValor' => "sum(R001_Orcamentos_Valor_Total) as R001_Orcamentos_Valor_Total", 'colunaTitulo' => 'Orçamentos Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "width:1%; background-color: #D7ECF7;"),
			array('colunaValor' => "SUM(R001_Faturados_Itens)", 'colunaTitulo' => 'Faturados Itens', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%'),
			array('colunaValor' => "sum(R001_Faturados_Valor_Total) as R001_Faturados_Valor_Total", 'colunaTitulo' => 'Faturados Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => "width:1%; background-color: #D7ECF7;"),
			array('colunaValor' => "ROUND((sum(R001_Faturados_Valor_Total)*100)/sum(R001_Orcamentos_Valor_Total), 0)", 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '0', 'style' => 'width:1%'),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		$xLast12 = date('Y-m-d');
		$xLast12 = new \DateTime($xLast12);
		$xLast12->modify('-12 months');
		$xLast12 = $xLast12->format('Y-m-d');

		$extra = <<<EOT
			left join D049 on D049_Id=R001_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			WHERE 
				R001_D024_Id = 0 
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id > 0 
				AND R001_D006_Id = 0
				AND R001_Data >= '{$xLast12}'
			group by 
				R001_D049_Id
			order by 
				sum(R001_Faturados_Valor_Total) desc
EOT;
		$gridFiltro = array(
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraABC = $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
				
		/**
		* Botões (Versão Acima do GRID)
		*/
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);	
		$curvaABC = <<<EOT
			SELECT COUNT(DISTINCT R001_D049_Id) Quantidade,
			SUM(R001_Faturados_Valor_Total) Total,
			D049_Flag_Curva_ABC
			FROM R001 
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
EOT;
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
	
	case 'erp001GridFiscal':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridFiscalMensal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFiscal3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridFiscalAnual/', '');
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
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			WHERE 
				R001_D024_Id = 0
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id = 0 
				AND R001_D006_Id = 0
			order by 
				R001_Data
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			//'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
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
	
	case 'erp001GridFiscalMensal':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridFiscal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFiscal3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridFiscalAnual/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Mês', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => "retornaMes"),
			array('colunaValor' => "SUM(R001_Saida_Nr) as R001_Saida_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Saida_Itens) as R001_Saida_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Saida_Valor_Total_Produtos) as R001_Saida_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_ICMS) as R001_Saida_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_ICMS_ST) as R001_Saida_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_IPI) as R001_Saida_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_PIS) as R001_Saida_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_COFINS) as R001_Saida_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Valor_Total) as R001_Saida_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Nr) as R001_Entrada_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Entrada_Itens) as R001_Entrada_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Entrada_Valor_Total_Produtos) as R001_Entrada_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_ICMS) as R001_Entrada_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_ICMS_ST) as R001_Entrada_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_IPI) as R001_Entrada_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_PIS) as R001_Entrada_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_COFINS) as R001_Entrada_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Valor_Total) as R001_Entrada_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			WHERE 
				R001_D024_Id = 0
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id = 0 
				AND R001_D006_Id = 0
			group by 
				Month(R001_Data)
			ORDER BY R001_Data
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			//'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
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
	
	case 'erp001GridFiscalAnual':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridFiscal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFiscal2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridFiscalMensal/', '');
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "R001_Id", 'colunaTitulo' => 'R001_Id', 'colunaExibir' => false),
			array('colunaValor' => "R001_Data", 'colunaTitulo' => 'Ano', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => "retornaAno"),
			array('colunaValor' => "SUM(R001_Saida_Nr) as R001_Saida_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Saida_Itens) as R001_Saida_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Saida_Valor_Total_Produtos) as R001_Saida_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_ICMS) as R001_Saida_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_ICMS_ST) as R001_Saida_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_IPI) as R001_Saida_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_PIS) as R001_Saida_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Tributos_COFINS) as R001_Saida_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Saida_Valor_Total) as R001_Saida_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Nr) as R001_Entrada_Nr", 'colunaTitulo' => 'Notas', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Entrada_Itens) as R001_Entrada_Itens", 'colunaTitulo' => 'Itens', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "SUM(R001_Entrada_Valor_Total_Produtos) as R001_Entrada_Valor_Total_Produtos", 'colunaTitulo' => 'Produtos', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_ICMS) as R001_Entrada_Tributos_ICMS", 'colunaTitulo' => 'ICMS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_ICMS_ST) as R001_Entrada_Tributos_ICMS_ST", 'colunaTitulo' => 'ICMS ST', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_IPI) as R001_Entrada_Tributos_IPI", 'colunaTitulo' => 'IPI', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_PIS) as R001_Entrada_Tributos_PIS", 'colunaTitulo' => 'PIS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Tributos_COFINS) as R001_Entrada_Tributos_COFINS", 'colunaTitulo' => 'COFINS', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "SUM(R001_Entrada_Valor_Total) as R001_Entrada_Valor_Total", 'colunaTitulo' => 'Total', 'style' => 'width:1%; background-color: #D7ECF7;', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Supervisor', 'colunaExibir' => false),
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome Supervisor', 'colunaTitulo'=>'Vendedor', 'colunaExibir' => false),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R001';
		
		$extra = <<<EOT
			left join C007 as Vendedor on Vendedor.C007_Id=R001_C007_Id_Vendedor
			left join C007 as Supervisor on Supervisor.C007_Id=R001_C007_Id_Supervisor
			WHERE 
				R001_D024_Id = 0
				AND R001_C007_Id_Vendedor = 0 
				AND R001_C007_Id_Supervisor = 0 
				AND R001_D049_Id = 0 
				AND R001_D006_Id = 0
			group by 
				YEAR(R001_Data)
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'Supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			//'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
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
	
	case 'erp001GridResumoGerencial':
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
	
	case 'erp001GridResumoFinanceiro':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoFinanceiroMensal/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFinanceiro3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoFinanceiroAnual/', '');
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
			array('colunaValor' => "$Saldo_Projetado", 'colunaTitulo' => 'Saldo Projetado', 'callback' => 'saldoProjetado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "$Saldo_Real", 'colunaTitulo' => 'Saldo Real', 'colunaCasasDecimais' => '2', 'callback' => 'saldoReal', 'colunaTipo'=>'livre', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Recebido", 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pago", 'colunaTitulo' => 'Pago', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "$Saldo_Realizado", 'colunaTitulo' => 'Saldo', 'callback' => 'saldoRealizado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
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
	
	case 'erp001GridResumoFinanceiroMensal':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoFinanceiro/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFinanceiro3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoFinanceiroAnual/', '');
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
			array('colunaValor' => "R002_Valor_Receber", 'colunaTitulo' => 'Receber', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Receber_Projecao", 'colunaTitulo' => 'Projeção (+)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pagar", 'colunaTitulo' => 'Pagar', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pagar_Projecao", 'colunaTitulo' => 'Projeção (-)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "$Saldo_Projetado", 'colunaTitulo' => 'Saldo Projetado', 'callback' => 'saldoProjetado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "$Saldo_Real", 'colunaTitulo' => 'Saldo Real', 'colunaCasasDecimais' => '2', 'callback' => 'saldoReal', 'colunaTipo'=>'livre', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Recebido", 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pago", 'colunaTitulo' => 'Pago', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "$Saldo_Realizado", 'colunaTitulo' => 'Saldo', 'callback' => 'saldoRealizado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R002';

		$extra = <<<EOT
			LEFT JOIN C004 ON C004_Id = R002_C004_Id
			WHERE R002_C004_Id = 0
			GROUP BY MONTH(R002_Data)
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
	
	case 'erp001GridResumoFinanceiroAnual':
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
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoFinanceiro/', '');
					divRefresh('{$g['divId']}');
				});
				$('#radioFinanceiro2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp001/grid/erp001GridResumoFinanceiroMensal/', '');
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
			array('colunaValor' => "R002_Data", 'colunaTitulo' => 'Ano', 'callback' => "retornaAno", 'style' => 'width:1%'),
			array('colunaValor' => "R002_Valor_Receber", 'colunaTitulo' => 'Receber', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Receber_Projecao", 'colunaTitulo' => 'Projeção (+)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pagar", 'colunaTitulo' => 'Pagar', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pagar_Projecao", 'colunaTitulo' => 'Projeção (-)', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "$Saldo_Projetado", 'colunaTitulo' => 'Saldo Projetado', 'callback' => 'saldoProjetado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "$Saldo_Real", 'colunaTitulo' => 'Saldo Real', 'colunaCasasDecimais' => '2', 'callback' => 'saldoReal', 'colunaTipo'=>'livre', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Recebido", 'colunaTitulo' => 'Recebido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "R002_Valor_Pago", 'colunaTitulo' => 'Pago', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => "$Saldo_Realizado", 'colunaTitulo' => 'Saldo', 'callback' => 'saldoRealizado', 'colunaTipo'=>'livre', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		);		
		/**
		* Definição da SQL
		*/
		$from = 'R002';

		$extra = <<<EOT
			LEFT JOIN C004 ON C004_Id = R002_C004_Id
			WHERE R002_C004_Id = 0
			GROUP BY YEAR(R002_Data)
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
	
	case 'erp001GridCatalogo':
		
	$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
	
	//mysql_query("DELETE FROM T198");
    
    /**
    * Filtro
    */
    //Sempre que mudar esta variável, caso o grid/filtro esteja customizado, deve-se também mudar o valor no grid/filtro
    $diasMP = $g['C031']['diasMedioPrazo'];
    //$data = strtotime("+$diasMP day", strtotime(now));
    //$dataMP = date("Y-m-d", $data);
    
    $gridFiltro = array(
        'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
        "D001_Codigo_Produto" => array('titulo' => 'Código', 'tipo' => 'numero'),
        'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
        /*'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
        'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
            array('title' => 'Todos', 'value' => ''),
            array('title' => 'Sim', 'value' => 'S'),
            array('title' => 'Não', 'value' => 'N'),
        ), 'naoGerarExtra' => false),
        //'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
        'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
        'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'numero'),
        'D001_Foto' => array('titulo' => 'IMPA/FANN', 'tipo' => 'numero'),*/
        'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
            array('title' => 'Modelo', 'campoAgrupar' => 'D001_D003_Id', 'campoMostrar' => 'D003_Nome_Linha', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
        )),
        "(SELECT 
            IFNULL((
                SELECT 
                    SUM(T055_Quantidade) 
                FROM 
                    T055 
                LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                WHERE 
                    T055_D009_Id=D009_Id 
                    AND T075_Data_Previsao<=DATE(DATE_ADD(NOW()[[virgula]] INTERVAL {$diasMP} DAY))
                    AND IFNULL(T075_Data_Estoque[[virgula]]'0000-00-00') = '0000-00-00'
            )[[virgula]]0)-
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
                        AND T075_Data_Previsao<=DATE(DATE_ADD(NOW()[[virgula]] INTERVAL {$diasMP} DAY))
                        AND T075_Data_Previsao > 0
                        AND IFNULL(T075_Data_Estoque[[virgula]]'0000-00-00') = '0000-00-00'
                ) 
            )[[virgula]]0))" => array('titulo' => 'Estoque MP', 'tipo' => 'numero','exibir' => false),
        'ItensSelecionados' => array('titulo' => 'Marcados', 'tipo' => 'select', 'select' => array(
            array('title' => 'Todos', 'value' => ''),
            array('title' => 'Sim', 'value' => " AND T198_D009_Id IS NOT NULL AND T198_C007_Id='{$g['usuarioAtual']}' "),
            array('title' => 'Não', 'value' => " AND T198_D009_Id IS NULL OR T198_C007_Id!='{$g['usuarioAtual']}' "),
        ), 'naoGerarExtra' => true,'exibir' => false),
    );
    $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
	
	$C008_Id = $r_acaoId;
  
    
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor'=>"Foto", 'colunaTipo'=>'livre', 'colunaTitulo' => 'Modelo', 'colunaAgrupar' => true, 'style' => 'width: 1%;text-align: center;background-color: white;', 'callback'=>'mostrarImagemD003'),
		array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
		array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D009_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'T198_Id', 'colunaTitulo'=>'ID', 'colunaExibir' => false),
        array('colunaValor'=>'D049_Id', 'colunaTitulo'=>'ID', 'colunaExibir' => false),
		array('colunaValor'=>'D001_D003_Id', 'colunaTitulo'=>'ID',  'colunaExibir' => false),
		// array('colunaValor'=>'C008_Tipo', 'colunaTitulo'=>'Tipo', 'colunaAlinhar' => 'left'),
		// array('colunaValor'=>'D001_Flag_Pre_Cadastro', 'colunaTitulo'=>'Pré', 'colunaAlinhar' => 'center'),
		// array('colunaValor'=>'D001_Flag_Ativo', 'colunaTitulo'=>'Ativo', 'colunaAlinhar' => 'center'),
		array('colunaValor' => "CampoBx", 'colunaTipo' => "livre", 'colunaTitulo' => 'Orçamento', 'callback' => 'checkboxGerarOrcamentoT198', 'colunaAlinhar' => 'center', 'style'=>'width:1%;'),
		array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código', 'style' => 'font-weight:bold', 'style'=>'width:1%;'),
        	// array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN'),
		array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição português', 'style' => 'font-weight:bold'),
		//array('colunaValor'=>'D001_Descricao_Ingles', 'colunaTitulo'=>'Descrição inglês'),
		array('colunaValor'=>'D082_Marca', 'colunaTitulo'=>'Marca'),
		//array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center'),
		//array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'center'),
		//array('colunaValor'=>'D009_Valor_Custo_Unitario', 'colunaTitulo'=>'Custo', 'colunaAlinhar' => 'right'),
		array('colunaValor'=>"D009_Quantidade_Estoque", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
		array('colunaValor'=>'D009_Quantidade_Estoque_Real', 'colunaTitulo'=>'Estoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false ),
		array('colunaValor'=>'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'callback' => 'verPedidosDoProduto'),
		array('colunaValor'=>'D009_Quantidade_Estoque_Liquido', 'colunaTitulo'=>'Líquido', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false ),
		array('colunaValor'=>"estoqueMedioPrazo",'colunaTipo'=>'livre', 'colunaTitulo'=>'MP','colunaAlinhar' => 'right', 'style' => 'font-weight:normal','callback'=>'estoqueMP', 'colunaExibir' => false),
		array('colunaValor'=>"estoqueLongoPrazo",'colunaTipo'=>'livre', 'colunaTitulo'=>'LP','colunaAlinhar' => 'right', 'style' => 'font-weight:normal','callback'=>'estoqueLP', 'colunaExibir' => false),		
		array('colunaValor'=>'D009_Estoque_Defeito', 'colunaTitulo'=>'Estoque defeito', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false ),
		array('colunaValor'=>'D009_Estoque_Mostruario', 'colunaTitulo'=>'Estoque mostruario', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false ),
		array('colunaValor'=>'D009_Preco_1', 'colunaTitulo'=>'Preço 1', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2'),
		array('colunaValor'=>'D009_Preco_2', 'colunaTitulo'=>'Preço 2', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2'),
		array('colunaValor'=>'D009_Preco_3', 'colunaTitulo'=>'Preço 3', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2'),
		array('colunaValor'=>'D003_Nome_Linha', 'colunaTitulo' => 'Linha', 'colunaExibir'=>false),
		array('colunaValor'=>'D003_Imagem', 'colunaTitulo' => 'Imagem', 'colunaExibir'=>false),
		array('colunaValor'=>"D009_Observacao_Venda", 'colunaTitulo'=>'Obs', 'style' => 'width:5%;', 'colunaEditar'=>true, 'colunaExibir'=>false),
	        //array('colunaValor'=>"if(D005_Flag_ST='S','ST','') as ST", 'colunaTitulo'=>'ST', 'colunaAlinhar' => 'center'),
	        //array('colunaValor'=>'D049_Flag_Tipo', 'colunaTitulo'=>'TP', 'colunaAlinhar' => 'center'),
		//array('colunaValor'=>"(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo'=>'Local'),
		//array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Último Fornecedor'),
		//array('colunaValor'=>'D015_Nome_Grupo', 'colunaTitulo' => 'Aplicação'),
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
				left join T198 on T198_D009_Id=D009_Id AND T198_C007_Id='{$g['usuarioAtual']}'
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
			<button type="button" id="erp001GerarOrcamentoTabCatalogo">Gerar Orçamento</button>
			<button type="button" id="erp001MarcarTodos">Marcar Todos</button>
			<button type="button" id="erp001DesmarcarTodosCheck">Desmarcar todos</button> 
		<!--<button type="button" id="cad002JuntarProdutos">Juntar Produtos</button>-->
		<!--<button type="button" id="cad002TrocarCodigos">Trocar Códigos</button>-->
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#erp001DesmarcarTodosCheck').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#' + '{$g['divId']}').showLoading();
						$.getJSON('/erp/erp001/grid_func-ajax/erp001checkDesmarcarTodos/?ajax=true&callback=?', function(request) {
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
			$('#erp001GerarOrcamentoTabCatalogo').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_erp=' + encodeURIComponent('{$D024_Id}') + '&tabCatalogoerp=true', [650,480]);
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
	$filtroObrigatorio = true;
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	// Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	echo $g['ui']->gridFiltroPrint($gridFiltro);
		
	/**
	* Botões (Versão Acima do GRID)
	*/
	$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
	$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
	echo <<<EOT
		<div class="buttonsBottomTop">
			<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
			<div style="clear:both;"></div>
		</div>
EOT;
	
	/**
	* Geração: Monta o SQL e retorna o dados
	*/
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
	
	$sqlGridFiltro = base64_encode($geradoSql);
	
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
						var D001_Id = id[0].replace(/D001_Id-/, '');
						var D024_Id = id[1].replace(/D024_Id-/, '');
		               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
		           });
		           $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		           $('#erp001MarcarTodos').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#' + '{$g['divId']}').showLoading();
								$.getJSON('/erp/erp001/grid_func-ajax/erp001checkMarcarTodos/?ajax=true&sqlFiltro='+encodeURIComponent('{$sqlGridFiltro}')+'&callback=?', function(request) {
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
	
	case 'erp001GridCatalogoCliente':
		
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
        "D001_Codigo_Produto" => array('titulo' => 'Código', 'tipo' => 'numero'),
        'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
        // 'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
        // 'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
            // array('title' => 'Todos', 'value' => ''),
            // array('title' => 'Sim', 'value' => 'S'),
            // array('title' => 'Não', 'value' => 'N'),
        // ), 'naoGerarExtra' => false),
        // //'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
        // 'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
        // 'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'numero'),
            // 'D001_Foto' => array('titulo' => 'IMPA/FANN', 'tipo' => 'numero'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
            array('title' => 'Modelo', 'campoAgrupar' => 'D001_D003_Id', 'campoMostrar' => 'D003_Nome_Linha', 'campoOrdem' => 'DESC', 'campoSelecionado' => true)
        )),
        "(SELECT 
            IFNULL((
                SELECT 
                    SUM(T055_Quantidade) 
                FROM 
                    T055 
                LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                WHERE 
                    T055_D009_Id=D009_Id 
                    AND T075_Data_Previsao<=DATE(DATE_ADD(NOW()[[virgula]] INTERVAL {$diasMP} DAY))
            )[[virgula]]0)-
            IFNULL((
                SELECT 
                    SUM(T006_Quantidade) 
                FROM 
                    T006 
                LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                WHERE 
                    T005_Flag_Status!='5'
                    AND T006_Flag_Status!='3' 
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
                        AND T075_Data_Previsao<=DATE(DATE_ADD(NOW()[[virgula]] INTERVAL {$diasMP} DAY))
                        AND T075_Data_Previsao > 0
                ) 
            )[[virgula]]0))" => array('titulo' => 'Estoque MP', 'tipo' => 'numero','exibir' => false),
        'ItensSelecionados' => array('titulo' => 'Marcados', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => " AND T198_D009_Id IS NOT NULL AND T198_C007_Id='{$g['usuarioAtual']}' "),
                array('title' => 'Não', 'value' => " AND T198_D009_Id IS NULL OR T198_C007_Id!='{$g['usuarioAtual']}' "),
            ), 'naoGerarExtra' => true,'exibir' => false),
    );
    $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
    
	$C008_Id = $r_acaoId;
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor'=>"D003_Nome_Linha", 'colunaTitulo' => 'Modelo', 'colunaAgrupar' => true, 'style' => 'width: 100px;text-align: center;background-color: white;', 'callback'=>'mostrarImagemD003'), //  'colunaExibir' => false
		array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
		array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D009_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'T198_Id', 'colunaTitulo'=>'ID', 'colunaExibir' => false),
		array('colunaValor'=>'D001_D003_Id', 'colunaTitulo'=>'ID',  'colunaExibir' => false),
		// array('colunaValor'=>'C008_Tipo', 'colunaTitulo'=>'Tipo', 'colunaAlinhar' => 'left'),
		// array('colunaValor'=>'D001_Flag_Pre_Cadastro', 'colunaTitulo'=>'Pré', 'colunaAlinhar' => 'center'),
		// array('colunaValor'=>'D001_Flag_Ativo', 'colunaTitulo'=>'Ativo', 'colunaAlinhar' => 'center'),
		array('colunaValor' => "CampoBx", 'colunaTipo' => "livre", 'colunaTitulo' => 'Orçamento', 'callback' => 'checkboxGerarOrcamentoT198', 'colunaAlinhar' => 'center', 'style'=>'width:1%;'),
		array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código', 'style' => 'font-weight:bold', 'style'=>'width:1%;'),
        	// array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN'),
		array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição português', 'style' => 'font-weight:bold'),
		//array('colunaValor'=>'D001_Descricao_Ingles', 'colunaTitulo'=>'Descrição inglês'),
		array('colunaValor'=>'D082_Marca', 'colunaTitulo'=>'Marca'),
		//array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center'),
		//array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'center'),
		//array('colunaValor'=>'D009_Valor_Custo_Unitario', 'colunaTitulo'=>'Custo', 'colunaAlinhar' => 'right'),
		array('colunaValor'=>"D009_Quantidade_Estoque_Real", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
		array('colunaValor'=>'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'callback' => 'verPedidosDoProduto'),
		//array('colunaValor'=>'D009_Quantidade_Estoque_Real', 'colunaTitulo'=>'Estoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
		array('colunaValor'=>'D009_Quantidade_Estoque_Liquido', 'colunaTitulo'=>'Líquido', 'colunaAlinhar' => 'right', 'colunaExibir'=>false),
		array('colunaValor'=>'D009_Preco_1', 'colunaTitulo'=>'Preço 1', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2'),
		array('colunaValor'=>'D009_Preco_2', 'colunaTitulo'=>'Preço 2', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2'),
		array('colunaValor'=>'D009_Preco_3', 'colunaTitulo'=>'Preço 3', 'colunaAlinhar' => 'right', 'colunaCasasDecimais'=>'2'),
	        //array('colunaValor'=>"if(D005_Flag_ST='S','ST','') as ST", 'colunaTitulo'=>'ST', 'colunaAlinhar' => 'center'),
	        //array('colunaValor'=>'D049_Flag_Tipo', 'colunaTitulo'=>'TP', 'colunaAlinhar' => 'center'),
		//array('colunaValor'=>"(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo'=>'Local'),
		//array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Último Fornecedor'),
		//array('colunaValor'=>'D015_Nome_Grupo', 'colunaTitulo' => 'Aplicação'),
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
			<button type="button" id="erp001GerarOrcamentoTabCatalogoCliente">Gerar Orçamento</button>
			<button type="button" id="erp001DesmarcarTodosCheckCliente">Desmarcar todos</button>
		<!--<button type="button" id="cad002JuntarProdutos">Juntar Produtos</button>-->
		<!--<button type="button" id="cad002TrocarCodigos">Trocar Códigos</button>-->
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#erp001DesmarcarTodosCheckCliente').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#' + '{$g['divId']}').showLoading();
						$.getJSON('/erp/erp001/grid_func-ajax/erp001checkDesmarcarTodos/?ajax=true&callback=?', function(request) {
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
			
			$('#erp001GerarOrcamentoTabCatalogoCliente').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_erp=' + encodeURIComponent('{$D024_Id}') + '&tabCatalogoerp=true', [650,480]);
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
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
	
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
						var D001_Id = id[0].replace(/D001_Id-/, '');
						var D024_Id = id[1].replace(/D024_Id-/, '');
		               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
		           });
		           $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

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
	
	case 'erp001GridAgendaGeral':

			/**
			* Definição do Grid (Tabela)
			*/
			//echo "<span style='margin-left:5px;color:#70A8D2;font-weight:bold;'>Agenda</span>";
			$grid = array(
				array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D085_Id', $g['divId'])),
				array('colunaValor' => 'D085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
				array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Empresa'),
				array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
				array('colunaValor' => 'D085_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'D085_Data_Retorno', 'colunaTitulo' => 'Data Retorno', 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => 'Mensagem'),
				array('colunaValor' => 'D085_C007_Id', 'colunaTitulo' => 'IdUsuarioAtual', 'colunaExibir' => false),
				array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'D085';

			$extra = <<<EOT
				left join C007 on C007_Id=D085_C007_Id
				LEFT JOIN D024 on D024_Id=D085_D024_Id
				order by D085_Data desc,D085_Id desc
				
EOT;
			
			//Define ação do click de abertura (ao clicar em uma linha do grid)
			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('.gridOptTop').remove();
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D085_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Agenda - ID: ' + id, '/erp/erp001/content/erp001contentEditarAgenda/', '&acaoId=' + encodeURIComponent(id), [700,400]);
				});
			} </script></div>
EOT;
			
			 echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="erp002IncluirAgenda">Incluir</button>	
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#erp002IncluirAgenda').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/erp/erp001/content/erp001contentEditarAgenda/', '&tab=geral', [500,500]);
					});
				} </script></div>
EOT;
			
			/**
			* Filtro
			*/
			$gridFiltro = array(
				'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
				'D085_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
				'D085_Data_Retorno' => array('titulo' => 'Data Retorno', 'tipo' => 'data'),
				'D085_Mensagem' => array('titulo' => 'Mensagem', 'tipo' => 'texto'),
				'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
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
		
		case 'erp001GridEmail':
			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
				// Chave Primária
				array('colunaValor' => 'T139_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'T139_Pasta', 'colunaTitulo' => 'P'),
				array('colunaValor' => 'T139_De', 'colunaTitulo' => 'De'),
				array('colunaValor' => 'T139_Para', 'colunaTitulo' => 'Para'),
				array('colunaValor' => 'T139_Titulo', 'colunaTitulo' => 'Assunto'),
				array('colunaValor' => 'T139_Data_Hora', 'colunaTitulo' => 'Data/Hora', 'callback'=>'gCorrigeData'),
			);
	
			/**
			* Definição da SQL
			*/
			$from = 'T139';
			
			if($g['c007']['C007_Flag_Vendedor'] != 'D'){
				$extra = <<<EOT
				WHERE 
				T139_C007_Id='{$g['usuarioAtual']}'
EOT;
			}


			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="etc006EnviarEmail2">Novo Email</button>
					<button type="button" id="etc006VerificarEmailsEnviadosCad001">Verificar Novos Emails</button>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#etc006EnviarEmail2').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&D024_Id=' + encodeURIComponent('$r_acaoId'), [900,550]);
					});
					$('#etc006VerificarEmailsEnviadosCad001').unbind('click').bind('click', function(data) {
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
		    $gridFiltro = array(
				'T139_De' => array ('titulo' => 'De', 'tipo' => 'texto'),
		    	'T139_Data_Hora' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		    	
		    );
			$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
	
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
}

?>
