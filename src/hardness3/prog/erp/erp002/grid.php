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
	require_once('prog/erp/erp002/grid_func-callback.php');

//Carrega funções do ajax
	require_once('prog/erp/erp002/grid_func-js.php');



switch($g['r'][3]){
		case 'erp002GridAgenda':

			/**
			* Definição do Grid (Tabela)
			*/
			//echo "<span style='margin-left:5px;color:#70A8D2;font-weight:bold;'>Agenda</span>";
			$grid = array(
				array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D085_Id', $g['divId'])),
				array('colunaValor' => 'D085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
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
				where 
				D085_D024_Id = {$r_D024_Id}
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
				<button type="button" id="erp001IncluirAgenda">Incluir</button>	
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#erp001IncluirAgenda').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Agenda', '/erp/erp001/content/erp001contentEditarAgenda/', '&tab=geral&D024_Id=' + encodeURIComponent('{$r_D024_Id}'), [500,500]);
					});
				} </script></div>
EOT;
			
			/**
			* Filtro
			*/
			$gridFiltro = array(
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

			$novoIdTelaForm = uniqid();
		break;
		
		case 'erp002GridContatos':
			//echo "<span style='margin-left:5px;color:#70A8D2;font-weight:bold;'>Contatos</span>";
		/**
		 * Definição do Grid (Tabela)
		 */		
			
			$grid = array(
				array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D013_Id', $g['divId'])),
				array('colunaValor'=>'D013_Id', 'colunaTitulo'=>'Id', 'chavePrimaria'=>true,'colunaExibir' => false),
				array('colunaValor'=>'D013_Nome_Contato', 'colunaTitulo'=>'Nome'),
				array('colunaValor'=>'D013_E_Mail', 'colunaTitulo'=>'Email'),
				array('colunaValor'=>'D013_Telefone_1', 'colunaTitulo'=>'Telefone'),
				array('colunaValor'=>'D013_Data_Nascimento', 'colunaTitulo'=>'Nascimento', 'callback'=>'gCorrigeData'),
				array('colunaValor'=>'D013_Tipo_Contato', 'colunaTitulo'=>'Tipo'),
				
			);
			
			/**
			* Definição da SQL
			*/
			$from = 'D013';
			
					$extra = <<<EOT
					WHERE D013_D024_Id='{$r_D024_Id}'
EOT;
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('.gridOptTop').remove();
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/D013_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Contato- ID: ' + id, '/erp/erp002/content/erp002contentEditarContato/', '&acaoId=' + encodeURIComponent(id), [700,400]);
				});
			} </script></div>
EOT;
		 echo <<<EOT
		 <div class="buttonsBottomTop">
		 	<button type="button" id="erp002IncluirContato">Incluir</button>	
		 </div>
		 <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#erp002IncluirContato').unbind('click').bind('click', function(data) {	
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastrar Contato', '/erp/erp002/content/erp002contentEditarContato/', '&tab=geral&D024_Id=$r_D024_Id', [500,500]);
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
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// $novoIdTelaForm = uniqid();
		// echo <<<EOT
		// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			// $('#cad001Incluir').unbind('click').bind('click', function(data) {
				// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001content02/','', [900,550]);
			// });
		// } </script></div>
// EOT;
		break;
		
	case 'erp002GridPrincipalOrcamentosClienteAbertos':
			
			echo <<<EOT
			<div class="radioUI" style="padding:5px;">
				<input type="radio" id="radio1" class="noStyle" checked="checked" name="radioUI" /><label for="radio1">Abertos</label>
				<input type="radio" id="radio2" class="noStyle" name="radioUI" /><label for="radio2">Fechados</label>
				<input type="radio" id="radio3" class="noStyle" name="radioUI" /><label for="radio3">Cancelados</label>
				<input type="radio" id="erp002botaoIncluirOrcamento" class="noStyle" name="radioUI" /><label for="erp002botaoIncluirOrcamento">Incluir Orçamento</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#Procura_Cliente').focus();
				$(function() {
					$("#{$g['divId']} .radioUI").buttonset();
				});
				$('#radio2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalOrcamentosClienteFechados/', '&D024Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#radio3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalOrcamentosClienteCancelados/', '&D024Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#erp002botaoIncluirOrcamento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_erp=' + encodeURIComponent('{$r_D024_Id}'), [650,480]);
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' =>'campoExclusao'),
			array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T003_D024_Id', 'colunaTitulo' => 'Cod. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold' ),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold' ),
			array('colunaValor' => 'T003_Flag_Status_Orcamento as Flag_Status_Orcamento', 'colunaExibir' => false),
			array('colunaValor' => 'T003_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
			array('colunaValor' => 'globalStatusOrcamento', 'colunaTipo' => 'livre','colunaTitulo' => 'Status', 'callback' => 'campoStatus'),
			array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - ",T005_Valor_Total) separator ", ") as Pedido','colunaTitulo' => 'Pedidos', 'callback'=>'validaCampoPedido', 'style' => 'width:1%'),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF', 'style' => 'font-weight:bold' ),
			array('colunaValor' => 'concat(D013_Nome_Contato," ",D013_Telefone_1) as D013_Nome_Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left' ),
			array('colunaValor' => 'Status Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipal'),			
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T003_Observacao_Orcamento_Perdido', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaTitulo' => 'T003_Flag_Status_Orcamento','colunaExibir'=>false),
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
		    left join T005 on T005_T003_Id=T003_Id
		    left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
		    where T003_C004_Id='{$g['empresaAtual']}'
		    and T003_D024_Id='{$r_D024_Id}'
		    and T003_Flag_Status_Orcamento!='C'
		    and (select T005_Id from T005 where T005_T003_Id=T003_Id limit 1) is null
		    group by T003_Id
EOT;
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotalAberto = <<<EOT
			left join D024 on D024_Id=T003_D024_Id
			left join D013 on D013_Id=T003_D013_Id
		    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
		    left join C007 as Liberou on Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
		    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join D006 on D006_Id=T003_D006_Id
			left join T005 on T005_T003_Id=T003_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		    and T003_D024_Id='{$r_D024_Id}'
		    and T003_Flag_Perdido!='S'
		    and (select T005_Id from T005 where T005_T003_Id=T003_Id limit 1) is null)
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
		* Filtro
		*/
		 $gridFiltro = array(
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
		 	'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
		 	'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
										FROM T004 
										LEFT JOIN D009 ON D009_Id = T004_D009_Id
										LEFT JOIN D049 ON D049_Id = D009_D049_Id
										LEFT JOIN D082 ON D082_Id = D049_D082_Id
										WHERE T004_T003_Id=T003_Id)' => array('titulo'=>'Marca', 'tipo'=>'texto')
		 );
		 
		// Gera o filtro e acrescenta ao $extra
		 $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		 $extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
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
		 $registrosAberto = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotalAberto);
		 $valorTotalAberto = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T003_Valor_Total)", $from, $extraTotalAberto));
		//T003_Flag_Perdido='S'
		// //echo "Registros: <br />" . "count(*)" . $from . $extra . "<br />    Valor Total: <br />" . "sum(T003_Valor_Total)" . $from . $extra;
// 		
		 echo <<<EOT
			 <div class="buttonsBottomTop">
				 <span class="item" style="text-align:right">Abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosAberto}" readonly></span>
				 <span class="item" style="text-align:right">Valor Total Abertos<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalAberto}" readonly></span>
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
	
	case 'erp002GridPrincipalOrcamentosClienteFechados':
			
			echo <<<EOT
			<div class="radioUI" style="padding:5px;">
				<input type="radio" id="radio1" class="noStyle" name="radioUI" /><label for="radio1">Abertos</label>
				<input type="radio" id="radio2" class="noStyle" checked="checked" name="radioUI" /><label for="radio2">Fechados</label>
				<input type="radio" id="radio3" class="noStyle" name="radioUI" /><label for="radio3">Cancelados</label>
				<input type="radio" id="erp002botaoIncluirOrcamento" class="noStyle" name="radioUI" /><label for="erp002botaoIncluirOrcamento">Incluir Orçamento</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$("#{$g['divId']} .radioUI").buttonset();
				});
				$('#radio1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalOrcamentosClienteAbertos/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#radio3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalOrcamentosClienteCancelados/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#erp002botaoIncluirOrcamento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_erp=' + encodeURIComponent('{$r_D024_Id}'), [650,480]);
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' =>'campoExclusao'),
			array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T003_D024_Id', 'colunaTitulo' => 'Cod. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold' ),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold' ),
			array('colunaValor' => 'T003_Flag_Status_Orcamento as Flag_Status_Orcamento', 'colunaExibir' => false),
			array('colunaValor' => 'T003_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
			array('colunaValor' => 'globalStatusOrcamento', 'colunaTipo' => 'livre','colunaTitulo' => 'Status', 'callback' => 'campoStatus'),
			array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - ",T005_Valor_Total) separator ", ") as Pedido','colunaTitulo' => 'Pedidos', 'callback'=>'validaCampoPedido', 'style' => 'width:1%'),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF', 'style' => 'font-weight:bold','callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'concat(D013_Nome_Contato," ",D013_Telefone_1) as D013_Nome_Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left' ),
			array('colunaValor' => 'Status Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipal'),			
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T003_Observacao_Orcamento_Perdido', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaTitulo' => 'T003_Flag_Status_Orcamento','colunaExibir'=>false),
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
		    left join T005 on T005_T003_Id=T003_Id
		    left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		    AND T003_D024_Id='{$r_D024_Id}'
		    AND ifnull(T003_Flag_Perdido,'N')!='S'
			and T005_Id > 0)
		    group by T003_Id
		    order by if(T006_Id<=0 or T006_Id is null,0,1),T003_Id desc
EOT;
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
		    AND T003_D024_Id='{$r_D024_Id}'
		    AND ifnull(T003_Flag_Perdido,'N')!='S'
			and T005_Id > 0)
			GROUP BY T003_Id
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
		* Filtro
		**/
		$gridFiltro = array(
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
										FROM T004 
										LEFT JOIN D009 ON D009_Id = T004_D009_Id
										LEFT JOIN D049 ON D049_Id = D009_D049_Id
										LEFT JOIN D082 ON D082_Id = D049_D082_Id
										WHERE T004_T003_Id=T003_Id)' => array('titulo'=>'Marca', 'tipo'=>'texto'),
		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// // Gera o filtro e acrescenta ao $extra
		$extraTotalFechado = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalFechado, false, false, true);
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
		$registrosFechado = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotalFechado, $extraTotalFechado);
		// Total Fechado
		$sql = "SELECT distinct T003_Id, T003_Valor_Total FROM {$from} " . str_replace('GROUP BY T003_Id', '', $extraTotalFechado);
		$res = mysql_query($sql);
		$valorTotalFechado = 0;
		while ($row = mysql_fetch_assoc($res)) {
			$valorTotalFechado += $row['T003_Valor_Total'];
		}
		$valorTotalFechado = gCorrigeNumero($valorTotalFechado);
		//$valorTotalFechado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct T003_Id)", $from, str_replace('GROUP BY T003_Id', '', $extraTotalFechado)));
		//T003_Flag_Perdido='S'
		// //echo "Registros: <br />" . "count(*)" . $from . $extra . "<br />    Valor Total: <br />" . "sum(T003_Valor_Total)" . $from . $extra;
// 		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosFechado}" readonly></span>
				<span class="item" style="text-align:right">Valor Total Fechados<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalFechado}" readonly></span>
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
	
	case 'erp002GridPrincipalOrcamentosClienteCancelados':
			echo <<<EOT
			<div class="radioUI" style="padding:5px;">
				<input type="radio" id="radio1" class="noStyle" name="radioUI" /><label for="radio1">Abertos</label>
				<input type="radio" id="radio2" class="noStyle" name="radioUI" /><label for="radio2">Fechados</label>
				<input type="radio" id="radio3" class="noStyle" checked="checked" name="radioUI" /><label for="radio3">Cancelados</label>
				<input type="radio" id="erp002botaoIncluirOrcamento" class="noStyle" name="radioUI" /><label for="erp002botaoIncluirOrcamento">Incluir Orçamento</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$("#{$g['divId']} .radioUI").buttonset();
				});
				$('#radio1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalOrcamentosClienteAbertos/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#radio2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalOrcamentosClienteFechados/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#erp002botaoIncluirOrcamento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_erp=' + encodeURIComponent('{$r_D024_Id}'), [650,480]);
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' =>'campoExclusao'),
			array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçamento', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T003_D024_Id', 'colunaTitulo' => 'Cod. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaAlinhar' => 'left', 'style' => 'font-weight:bold' ),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T003_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold' ),
			array('colunaValor' => 'T003_Flag_Status_Orcamento as Flag_Status_Orcamento', 'colunaExibir' => false),
			array('colunaValor' => 'T003_Liberou_Alcada_Observacao_Nao_Liberado', 'colunaExibir' => false),
			array('colunaValor' => 'globalStatusOrcamento', 'colunaTipo' => 'livre','colunaTitulo' => 'Status', 'callback' => 'campoStatus'),
			array('colunaValor' => 'group_concat(distinct concat(T005_Id," - ",ifnull(T005_Status_Pedido(T005_Flag_Status,1),"")," - ",T005_Valor_Total) separator ", ") as Pedido','colunaTitulo' => 'Pedidos', 'callback'=>'validaCampoPedido', 'style' => 'width:1%'),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF', 'style' => 'font-weight:bold' ),
			array('colunaValor' => 'concat(D013_Nome_Contato," ",D013_Telefone_1) as D013_Nome_Contato', 'colunaTitulo' => 'Contato', 'colunaAlinhar' => 'left' ),
			array('colunaValor' => 'Status Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipal'),			
			array('colunaValor' => 'Vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'Supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => 'T003_Flag_Perdido', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Observacao_Orcamento_Perdido', 'colunaTitulo' => 'T003_Observacao_Orcamento_Perdido', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'T005_Id', 'colunaExibir'=>false, 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T003_Flag_Status_Orcamento', 'colunaTitulo' => 'T003_Flag_Status_Orcamento','colunaExibir'=>false),
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
		    left join T005 on T005_T003_Id=T003_Id
		    left join T007 on T007_T005_Id=T005_Id
			left join D006 on D006_Id=T003_D006_Id
		    where (T003_C004_Id='{$g['empresaAtual']}'
		    AND T003_D024_Id='{$r_D024_Id}'
		    AND T003_Flag_Perdido='S')
		    group by T003_Id
		    order by if(T006_Id<=0 or T006_Id is null,0,1),T003_Id desc
EOT;
		$extraTotalCancelado = <<<EOT
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
		      and T003_D024_Id='{$r_D024_Id}'
		      AND T003_Flag_Perdido='S')
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
		* Filtro
		*/
		$gridFiltro = array(
			'Vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
										FROM T004 
										LEFT JOIN D009 ON D009_Id = T004_D009_Id
										LEFT JOIN D049 ON D049_Id = D009_D049_Id
										LEFT JOIN D082 ON D082_Id = D049_D082_Id
										WHERE T004_T003_Id=T003_Id)' => array('titulo'=>'Marca', 'tipo'=>'texto'),
		 );
		 
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		$extraTotalCancelado = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalCancelado, false, false, true);
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
		$registrosPerdido = $g['sqlAuto']->pegarSqlCampo("count(distinct T003_Id)", $from, $extraTotalCancelado);
		$valorTotalCancelado = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T003_Valor_Total)", $from, $extraTotalCancelado));
		// //T003_Flag_Perdido='S'
		// //echo "Registros: <br />" . "count(*)" . $from . $extra . "<br />    Valor Total: <br />" . "sum(T003_Valor_Total)" . $from . $extra;
// 		
		 echo <<<EOT
			 <div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Cancelados<br><input type="text" class="whiteBg" id="" size="11" value="{$registrosPerdido}" readonly></span>
				<span class="item" style="text-align:right">Valor Total Cancelados<br><input type="text" class="whiteBg" id="" size="11" value="{$valorTotalCancelado}" readonly></span>
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
	
	case 'erp002GridPrincipalPedidosClienteAbertos':
		echo <<<EOT
			<div class="radioPedidoUI" style="padding:5px;">
				<input type="radio" id="radioPedido1" class="noStyle" checked="checked" name="radio" /><label for="radioPedido1">Abertos</label>
				<input type="radio" id="radioPedido2" class="noStyle" name="radio" /><label for="radioPedido2">Faturado</label>
				<input type="radio" id="radioPedido3" class="noStyle" name="radio" /><label for="radioPedido3">Cancelados</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioPedidoUI" ).buttonset();
				});
				$('#radioPedido2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalPedidosClienteFaturado/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#radioPedido3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalPedidosClienteCancelados/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoPedido', 'colunaExibir'=>false),
			array('colunaValor' => "campoCancelamento", 'colunaTipo' => "livre", 'callback' => 'campoCancelamento', 'style'=>'width:1%;'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'Cód. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status_Pedido', 'colunaTitulo' => 'Status', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'group_concat("<span style=\"font-weight:bold\">",T005_Status_Pedido(T005_Flag_Status,1),"</span> - ", (SELECT date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1)) as Status2', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),                                
			array('colunaValor' => 'supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor'),
			array('colunaValor' => 'Status Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipalPedido'),
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
    			where (T005_C004_Id={$g['empresaAtual']}
				and T005_Flag_Status!=4
				and T005_Flag_Status!=5
				and T005_Flag_Status!=8
				and D024_Id='$r_D024_Id')
    			GROUP BY T005_Id
    			order by T005_Id desc 
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
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
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
		//Criado extraTotal apenas para tirar o "Group By" para não influenciar sobre os totalizadores
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
    		left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     	    left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    		left join D006 on D006_Id=T005_D006_Id
    		where (T005_C004_Id={$g['empresaAtual']}
    		and T005_D024_Id='{$r_D024_Id}'
    		and T005_Flag_Status!=4
			and T005_Flag_Status!=5
			and T005_Flag_Status!=8)
EOT;
		// // Gera o filtro e acrescenta ao $extra
		 $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		 $extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// // Imprime o filtro do GRID (normalmente executado acima do GRID)
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
				 <span class="item" style="text-align:right">Pedidos Abertos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [950,615]);            
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
	
	case 'erp002GridPrincipalPedidosClienteFaturado':
		echo <<<EOT
			<div class="radioPedidoUI" style="padding:5px;">
				<input type="radio" id="radioPedido1" class="noStyle" name="radio" /><label for="radioPedido1">Abertos</label>
				<input type="radio" id="radioPedido2" class="noStyle" checked="checked" name="radio" /><label for="radioPedido2">Faturado</label>
				<input type="radio" id="radioPedido3" class="noStyle" name="radio" /><label for="radioPedido3">Cancelados</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioPedidoUI" ).buttonset();
				});
				$('#radioPedido1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalPedidosClienteAbertos/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#radioPedido3').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalPedidosClienteCancelados/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoPedido'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'Cód. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Status_Pedido', 'colunaTitulo' => 'Status', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'group_concat("<span style=\"font-weight:bold\">",T005_Status_Pedido(T005_Flag_Status,1),"</span> - ", (SELECT date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1)) as Status2', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF', 'style' => 'font-weight:bold','callback' => 'validaCampoNFOrcamentos'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),                                
			array('colunaValor' => 'supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor'),
			array('colunaValor' => 'Status Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipalPedido'),
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
    			where (T005_C004_Id={$g['empresaAtual']}
				and D024_Id='$r_D024_Id'
				and T005_Flag_Status!='8'
				and (T005_Flag_Status='4'
				or T005_Flag_Status='5'))
    			GROUP BY T005_Id
    			order by T005_Id desc
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
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'(SELECT GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0[[virgula]]"MP"[[virgula]]"PE"))) FROM T006 WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Estoque', 'tipo' => 'texto', 'exibir'=>false),
			'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
						FROM T006 
						LEFT JOIN D009 ON D009_Id = T006_D009_Id
						LEFT JOIN D049 ON D049_Id = D009_D049_Id
						LEFT JOIN D082 ON D082_Id = D049_D082_Id
						WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Marca', 'tipo'=>'texto'),
		);
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
    		left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     	    left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    		left join D006 on D006_Id=T005_D006_Id
    		where (T005_C004_Id={$g['empresaAtual']} and T005_D024_Id='{$r_D024_Id}' and (T005_Flag_Status=4 or T005_Flag_Status=5))
EOT;
		 
		// // Gera o filtro e acrescenta ao $extra
		 $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotal);
		// // Imprime o filtro do GRID (normalmente executado acima do GRID)
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
				 <span class="item" style="text-align:right">Pedidos Fechados<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [950,615]);            
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
	
	case 'erp002GridPrincipalPedidosClienteCancelados':
		echo <<<EOT
			<div class="radioPedidoUI" style="padding:5px;">
				<input type="radio" id="radioPedido1" class="noStyle" name="radio" /><label for="radioPedido1">Abertos</label>
				<input type="radio" id="radioPedido2" class="noStyle" name="radio" /><label for="radioPedido2">Faturado</label>
				<input type="radio" id="radioPedido3" class="noStyle" checked="checked" name="radio" /><label for="radioPedido3">Cancelados</label>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$(function() {
					$( "#{$g['divId']} .radioPedidoUI" ).buttonset();
				});
				$('#radioPedido1').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalPedidosClienteAbertos/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
				$('#radioPedido2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').data('href', '/erp/erp002/grid/erp002GridPrincipalPedidosClienteFaturado/', '&D024_Id=' + encodeURIComponent('{$r_D024_Id}'));
					divRefresh('{$g['divId']}');
				});
			} </script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoPedido'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'Cód. Cliente', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => 'T005_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => 'T005_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as T005_Flag_Status_Pedido', 'colunaTitulo' => 'Status', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'group_concat("<span style=\"font-weight:bold\">",T005_Status_Pedido(T005_Flag_Status,1),"</span> - ", (SELECT date_format(T178_Data,"<small><i>%d/%m/%Y %H:%i:%s</i></small>") FROM T178 WHERE T178_T005_Id=T005_Id order by T178_Id desc LIMIT 1)) as Status2', 'colunaTitulo' => 'Status', 'colunaExibir' => false),
			array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal separator ", ") as NF','colunaTitulo' => 'NF', 'style' => 'font-weight:bold'),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome as Vendedor', 'colunaTitulo' => 'Vendedor'),                                
			array('colunaValor' => 'supervisor.C007_Primeiro_Nome as Supervisor', 'colunaTitulo' => 'Supervisor'),
			array('colunaValor' => 'Status Serviços', 'colunaTitulo' => 'Status Serviços', 'colunaTipo'=>'livre', 'callback'=>'validaCampoStatusGridPrincipalPedido'),
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
    			where (T005_C004_Id={$g['empresaAtual']}
    			and D024_Id='$r_D024_Id'
				and T005_Flag_Status='8')
    			GROUP BY T005_Id
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
		$extraTotal = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
    		left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
     	    left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
	        left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    		left join D006 on D006_Id=T005_D006_Id
    		where (T005_C004_Id={$g['empresaAtual']} and T005_D024_Id='{$r_D024_Id}' and T005_Flag_Status=8)
EOT;
		$gridFiltro = array(
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
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
		// // Imprime o filtro do GRID (normalmente executado acima do GRID)
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
				 <span class="item" style="text-align:right">Pedidos Cancelados<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/T005_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [950,615]);            
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
	
	case 'erp002gridProdutosCliente':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T008_Id', $g['divId'])),
			array('colunaValor' => 'T008_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T007_Numero_Nota_Fiscal', 'colunaTitulo' => 'NF'),
			array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Data Emissão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
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
			array('colunaValor' => 'T008_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T008_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
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
		            left join D006 on D006_Id=T008_D006_Id
		            left join D037 on D037_Id=T008_D037_Id
		            left join T007 on T007_Id=T008_T007_Id
		            left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
		            where T007_D024_Id = '{$r_D024_Id}'		            	
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
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T008_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		// 
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto - ID: ' + acaoId, '/ven/ven003/content/ven003ContentProdutoEditar/', '&acaoId=' + encodeURIComponent(acaoId) + '&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [650,500]);

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
	
	case 'erp002GridNotasEmitidasCliente':
		
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
			array('colunaValor' => 'T007_Numero_Protocolo_Nfe as Protocolo_Nfe', 'colunaTitulo' => 'Protocolo NFe'),
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
				    and T007_Flag_Emitida_Recebida!='R'
				    and T007_D024_Id='{$r_D024_Id}') 
				    
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="ven003IncluirNotaFiscal">Incluir</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				// $('#ven003IncluirNotaFiscal').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/','', [900,550]);
				// });
			// } </script></div>
// EOT;

		// /**
		// * Filtro
		// */
		 $gridFiltro = array(
			 'T007_Numero_Nota_Fiscal' => array('titulo' => 'Número', 'tipo' => 'numero'),
			 'T007_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			 'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),			 			
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
		);
		// //Gera o filtro e acrescenta ao $extra
		 $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// // Imprime o filtro do GRID (normalmente executado acima do GRID)
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
	
	/**
	* Definição do Grid (Tabela)
	*/
	case 'erp002GridRequisicao':
		// $meses = $g['c020']['C020_Numero_Meses_Relatorio_Estoque'];
		// $periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T189_Id', $g['divId'])),
			array('colunaValor' => 'T189_Id', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			array('colunaValor' => 'T189_Data_Criado', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
			array('colunaValor' => 'concat(T189_Descricao_Produto, " ", if(D001_Flag_Pre_Cadastro="S","(PRÉ)",""))', 'colunaTitulo' => 'Descrição Produto'),
			array('colunaValor' => 'T189_Quantidade', 'colunaTitulo' => 'Qnt', 'style' => 'width: 1%', 'colunaCasasDecimais'=>'2', 'colunaEditar'=>true),
			array('colunaValor' => 'Tipo', 'colunaTitulo' => 'Tipo', 'callback'=>'validaCampoTipo', 'colunaTipo'=>'livre'),
			array('colunaValor' => 'T189_Flag_Cotar', 'colunaTitulo' => 'Cotar', 'callback' => 'validaCampoCotar', 'style'=>'width:1%;'),
			array('colunaValor' => 'T189_Flag_Comprar', 'colunaTitulo' => 'Comprar', 'callback'=>'validaCampoComprar', 'style'=>'width:1%;'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'T189_Observacao', 'colunaTitulo' => 'Observação'),
			array('colunaValor' => 'T189_C007_Id', 'colunaTitulo' => 'Usuário', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_Detalhes', 'colunaTitulo' => 'Detalhes'),
			array('colunaValor' => 'T189_T004_Id', 'colunaTitulo' => 'T189_T004_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_T006_Id', 'colunaTitulo' => 'T189_T006_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_D009_Id', 'colunaTitulo' => 'T189_D009_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'T189_Flag_Tipo', 'colunaTitulo' => 'T189_Flag_Tipo', 'colunaExibir'=>false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T189';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		LEFT JOIN C007 ON C007_Id=T189_C007_Id
		LEFT JOIN T012 on T012_T189_Id=T189_Id
		LEFT JOIN T059 on T059_T189_Id=T189_Id
		LEFT JOIN D009 on D009_Id=T189_D009_Id
		LEFT JOIN D049 on D049_Id=D009_D049_Id
		LEFT JOIN D001 on D001_Id=D049_D001_Id
		WHERE (T012_T189_Id IS NULL OR T012_T189_Id='' OR T012_T189_Id=0) 
		AND 
		(T059_T189_Id IS NULL OR T059_T189_Id='' OR T059_T189_Id=0) and D024_Id={$r_D024_Id}
		ORDER BY T189_Data_Criado ASC
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T189_Descricao_Produto' => array('titulo' => 'Descrição Produto', 'tipo' => 'texto'),
			'T189_Data_Criado' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T189_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T189_Detalhes' => array('titulo' => 'Detalhes', 'tipo' => 'texto'),
			'T189_Flag_Tipo' => array('titulo' => 'Tipo (C/O/P/E)', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

	echo <<<EOT
	    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    setTimeout(function() {
		         divRefresh('{$g['divId']}');
		    }, 180000);
	    } </script></div>
EOT;
		
		$sql_ = urlencode(base64_encode($geradoSql));
		// <button type="button" id="erp002Incluir">Incluir</button>
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="erp002FlagCotar">Marcar Cotar</button>
				<button type="button" id="erp002FlagDesmarcarCotar">Desmarcar Cotar</button>
				<button type="button" id="erp002FlagMarcarComprar">Marcar Comprar</button>
				<button type="button" id="erp002FlagDesmarcarComprar">Desmarcar Comprar</button>
				<button type="button" id="erp002GerarOC">Gerar OC</button>
				<button type="button" id="erp002GerarCotacao">Gerar Cotação</button>
				
			</div>
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$extra_ = urlencode(base64_encode($extra));
		// Não executar quando houver o refresh de linha
		if (empty($r_linhaGridId)) {
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#erp002FlagCotar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/erp002/grid_func-ajax/erp002FlagMarcar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#erp002FlagDesmarcarCotar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/erp002/grid_func-ajax/erp002FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#erp002FlagMarcarComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/erp002/grid_func-ajax/erp002FlagMarcar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				$('#erp002FlagDesmarcarComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/erp002/grid_func-ajax/erp002FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						divRefresh('{$g['divId']}', true);
					});
				});
				
				$('#erp002GerarOC').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/erp002/grid_func-ajax/erp002GerarOC/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar OC', '/est/est006/content/est006ContentIncluirOrdemCompra/', '&tab=geral&GerarOCEstoqueRequisicao=true', [500,300]);
												$('#{$g['divId']}').hideLoading();
												$(this).dialog("destroy").remove();
											},
											"Não": function() { $(this).dialog("destroy").remove(); } 
										};
										dialogConfirm('Confirmação', request.data, \$buttons);
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
					dialogConfirm('Gerar Ordem de Compra', 'Gostaria de gerar Ordem de Compra? ', \$buttons);
				});
				
				$('#erp002GerarCotacao').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/erp002/grid_func-ajax/erp002GerarCotacao/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est008/content/est008contentCadastroCotacaoCompraNew/', '&tab=geral&GerarCotacaoEstoqueRequsicao=true', [500,300]);
												$('#{$g['divId']}').hideLoading();
												$(this).dialog("destroy").remove();
											},
											"Não": function() { $(this).dialog("destroy").remove(); } 
										};
										dialogConfirm('Confirmação', request.data, \$buttons);
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
					dialogConfirm('Gerar Cotação', 'Gostaria de gerar Cotação? ', \$buttons);
				});
				$('#erp002Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/erp002/content/erp002ContentRequisicaoCompraEditarNovo/', '&tab=geral&', [500,300]);
				});
			} </script></div>
EOT;
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
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T189_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Requisicao de compra - Nº: ' + acaoId, '/est/erp002/content/erp002ContentRequisicaoCompraEditarNovo/', '&acaoId=' + encodeURIComponent(acaoId), [600,400]);
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
	case 'erp002GridCotacao':

		 $usarAlcada = $g['conf']->ini('usarAlcadaCompra');
		
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T010_Id', $g['divId'])),
 			array('colunaValor' => 'T010_Data', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style'=>'width:1%;'),
 			array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			//array('colunaValor' => 'T173_T010_Id', 'colunaTitulo' => 'T173_Id','colunaExibir' => false),
			//array('colunaValor' => 'T173_Id', 'colunaTitulo' => 'T173_Id'),
			//array('colunaValor' => 'concat(D024_Nome_Empresa," - ",(SELECT D024_Nome_Empresa FROM T173 LEFT JOIN D024 ON T173_D024_Id=D024_Id WHERE T173_T010_Id=T010_Id))', 'colunaTitulo' => 'Fornecedor1' ),
			array('colunaValor' => 'T173_D024_Id', 'colunaTipo' => 'livre','colunaTitulo' => 'Fornecedor / Alçada','callback' => 'campoFornecedor'),
			array('colunaValor' => 'ValorCotacao', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Cotação','callback' => 'campoValorCotacao', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => '', 'colunaTipo' => 'livre', 'colunaTitulo' => '','callback' => 'validaCampoEmailEnviadoForn', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => 'ValorConfirmado', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Confirmado','callback' => 'campoValorConfirmado', 'colunaAlinhar'=>'right', 'style'=>'width:1%;'),
			array('colunaValor' => '(select count(*) from T059 where T059_T010_Id=T010_Id)', 'colunaTitulo' => 'Itens', 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
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
			left join D024 on D024_Id=T010_D024_Id
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
			//'T010_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
			'T010_Data' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			//'T010_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
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
  	case 'erp002GridOrdem':
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T011_Id', $g['divId'])),
 			array('colunaValor' => 'T011_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style'=>'width:1%;'),
 			array('colunaValor' => 'T011_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'colunaAlinhar'=>'center', 'style'=>'width:1%;'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'CódigoFornecedor','colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Fornecedor' ),
			array('colunaValor' => 'T011_Valor_Total_Custo', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T011_Valor_Total_Pendente', 'colunaTitulo' => 'Valor Pendente', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'T011_Flag_Tipo', 'colunaTitulo' => 'Tipo' ),
			array('colunaValor' => '(select count(*) from T012 where T012_T011_Id=T011_Id) as Itens', 'colunaTitulo' => 'Itens', 'colunaAlinhar'=>'center', 'style'=>'width:1%;'), 
			//array('colunaValor' => '(select group_concat(distinct T059_T010_Id) from T059 left join T012 on T012_T059_Id=T059_Id where T012_T011_Id=T011_Id) as Cotacao','colunaTitulo' => 'Cotação'),
			array('colunaValor' => 'T011_T010_Id','colunaTitulo' => 'Cotação', 'style'=>'width:1%;'),
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
		    where T011_C004_Id={$g['empresaAtual']} and D024_Id={$r_D024_Id}
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
			'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
			'T011_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
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
  	case 'erp002GridNFRecebida':
		
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
 			array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'IdNFRecebida', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T013_Numero_Controle', 'colunaTitulo' => 'Controle', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T013_Numero_Nota_Fiscal', 'colunaTitulo' => 'N.F.', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T013_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T013_Data_Entrada', 'colunaTitulo' => 'Entrada', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'callback'=>'coloreLinha'),
			array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => "if(T013_Tipo='1','NF',if(T013_Tipo='2','ACP',if(T013_Tipo='3','NF Consumidor',if(T013_Tipo='4','NFe','CF'))) ) as Tipo_Documento", 'colunaTitulo' => 'Doc' ),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'), 
			array('colunaValor' => 'sum((T014_Quantidade*T014_Valor_Custo_Unitario)-T014_Valor_Total_Desconto) Produtos','colunaTitulo' => 'Produtos', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T013_Valor_Mao_Obra', 'colunaTitulo' => 'Serviços', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'Almoxarifado', 'colunaTitulo' => 'Estoque', 'callback' => 'validaCampoAlmoxarifado', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'Cancelar', 'colunaTitulo' => 'Cancelar', 'callback'=>'validaCampoCancelarNFRec', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'DANFE', 'colunaTitulo' => 'DANFE', 'callback'=>'validaCampoDanfeNFRec', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback'=>'validaCampoXMLNFRec', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T013_Chave_Acesso_Nfe', 'colunaTitulo' => 'Chave Acesso'),
			array('colunaValor' => 'T013_Numero_Protocolo_Nfe', 'colunaTitulo' => 'Protocoloo NFe'),
			array('colunaValor' => 'T013_Retorno_Nfe', 'colunaTitulo' => 'Retorno SEFAZ'),
			array('colunaValor' => 'T013_Flag_Cancelada', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'T013_Flag_Travar_Nota', 'colunaTitulo' => '', 'colunaExibir' => false),
      );
		
		/**
		 * Definição da SQL
		 */
		$from = "T013";
		
		$extra = <<<EOT
		    left join T014 on T014_T013_Id=T013_Id
		    left join T012 on T012_Id=T014_T012_Id
		    left join D009 on D009_Id=T014_D009_Id
		    left join D049 on D049_Id=D009_D049_Id
		    left join D001 on D001_Id=D049_D001_Id
		    left join D024 on D024_Id=T013_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T014_D006_Id OR D006_Id=T013_D006_Id
		    where T013_C004_Id='{$g['empresaAtual']}' and D024_Id={$r_D024_Id}
		    GROUP BY T013_Id,D006_Codigo_CFOP
EOT;
/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est004IncluirNotaFiscal_Btn">Incluir</button>
				<!-- <button type="button" onclick="nova_janela('/hardness/nota_recebida_lancamento/atualizar_pedidos_venda.php',0,0,820,600,'no','no','no','no','yes','yes',0);">Atualizar Pedidos Venda</button> -->
				<!-- <button type="button" id="est004AtualizarPedidosVenda_Btn">Atualizar Pedidos Venda</button> -->
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est004IncluirNotaFiscal_Btn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir nota Fiscal', '/est/est004/content/est004ContentCadastroNotaFiscal/', '&tab=geral', [990,550]);
				});
				$('#est004AtualizarPedidosVenda_Btn').unbind('click').bind('click', function(data) {
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/est/est004/grid_func-ajax/atualizarPedidosVenda/?ajax=true&callback=?', function(request) {
					if (request.code) {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Sucesso!", request.data, buttons)
			    		divRefresh('{$g['divId']}'); 
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
			    		dialogConfirm("Erro!", request.data, buttons)
					}
				});
			});
			} </script></div>
EOT;


		/**
		* Filtro do modo correto, porém está dando um looping, verificar com o tiago
		*/
		$gridFiltro = array(
			'T013_Numero_Controle' => array('titulo' => 'Controle', 'tipo' => 'texto'),
			'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'T013_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T013_Data_Entrada' => array('titulo' => 'Entrada', 'tipo' => 'data'),
			
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		//*/
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		// O extra utilizados para o total é diferente do utilizado para montar o grid
		$extraTotais1 = <<<EOT
		    left join D024 on D024_Id=T013_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T013_D006_Id
		    where T013_C004_Id='{$g['empresaAtual']}' AND T013_Flag_Cancelada!='S' and D024_Id={$r_D024_Id}
EOT;
		$extraTotais1 = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotais1);
		$extraTotais = <<<EOT
		    left join T014 on T014_T013_Id=T013_Id
		    left join T012 on T012_Id=T014_T012_Id
		    left join D009 on D009_Id=T014_D009_Id
		    left join D049 on D049_Id=D009_D049_Id
		    left join D001 on D001_Id=D049_D001_Id
		    left join D024 on D024_Id=T013_D024_Id
		    left join D018 on D018_Id=D024_D018_Id
		    left join D006 on D006_Id=T014_D006_Id
		    where T013_C004_Id='{$g['empresaAtual']}' AND T013_Flag_Cancelada!='S' and D024_Id={$r_D024_Id}
EOT;
		$extraTotais = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotais);
		$valorTotal = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T013_Valor_Total)", $from, $extraTotais1)));
		$valorTotal1 = gCorrigeNumero(($g['sqlAuto']->pegarSqlCampo("sum(T014_Quantidade*T014_Valor_Custo_Unitario)", $from, $extraTotais)));
		$valorTotal2 = $g['sqlAuto']->pegarSqlCampo("count(distinct T013_Id)", $from, $extraTotais);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Notas Recebidas<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Produtos<br><input type="text" value="{$valorTotal1}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
			
		
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'FONT') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T013_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal: ' + $(this).closest('tr').find('td:eq(1)').text(), '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,550]);
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
  	
  	case 'erp002GridConhecimentos':
		
		global $g;
		
		$grid = array(
			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T007_Id', $g['divId'])),
 			array('colunaValor' => 'T084_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'T084_Numero_Conhecimento', 'colunaTitulo' => 'Conhecimento'),
			array('colunaValor' => 'T084_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T084_Data_Lancamento', 'colunaTitulo' => 'Lançamento', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP'),
			array('colunaValor' => 'D022_Nome_Empresa', 'colunaTitulo' => 'Transportadora'),
			array('colunaValor' => 'T084_Valor_Conhecimento', 'colunaTitulo' => 'Valor','colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'T084_Data_Vencimento', 'colunaTitulo' => 'Vencimento', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T084_Flag_Fechado', 'colunaTitulo' => 'Notas Lançadas', 'callback'=>'validaNotasLancadas'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => '', 'colunaTitulo' => 'Cancelar', 'colunaTipo'=>'livre', 'callback'=>'validaCampoCancelarNF'),
			array('colunaValor' => '', 'colunaTitulo' => 'Excluir', 'colunaTipo'=>'livre', 'callback'=>'validaCampoExcluir'),
			array('colunaValor' => 'T084_C004_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T084_C007_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T084_D022_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T084_D024_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false),
			array('colunaValor' => 'T084_D006_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false)
      );
		
		/**
		 * Definição da SQL
		 */
		$from = "T084";
		
		$extra = <<<EOT
				left join C007 on C007_Id=T084_C007_Id
			    left join D022 on D022_Id=T084_D022_Id
			    left join D024 on D024_Id=T084_D024_Id
			    left join D020 on D020_Id=D024_D020_Id
			    left join D018 on D018_Id=D020_D018_Id
			    left join T015 on T015_T084_Id=T084_Id and T015_Flag_Cancelada!='S'
			    left join D006 on T084_D006_Id=D006_Id
			    where (T084_C004_Id='{$g['empresaAtual']}'  and D024_Id={$r_D024_Id} and D024_Id={$r_D024_Id})
			    
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="est005IncluirConhecimentoBtn">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est005IncluirConhecimentoBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir conhecimento', '/est/est005/content/est005ContentCadastroConhecimento/', '&tab=geral', [650,400]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T084_Numero_Conhecimento' => array('titulo' => 'Conhecimento', 'tipo' => 'numero'),
			//'T013_Numero_Nota_Fiscal' => array('titulo' => 'N.F', 'tipo' => 'numero'),
			'T084_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			//'T084_Data_Lancamento' => array('titulo' => 'Lançamento', 'tipo' => 'data'),
			//'D022_Nome_Empresa' => array('titulo' => 'Transportadora', 'tipo' => 'texto'),
			//'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			//'T084_Data_Vencimento' => array('titulo' => 'Vencimento', 'tipo' => 'data'),
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
				var id = $(this).closest('tr').attr('id').replace(/T084_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Conhecimento ' + $(this).closest('tr').find('td:eq(1)').text(), '/est/est005/content/est005ContentEditarConhecimento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,700]);
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
  		case 'erp002GridEmail':
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
			$extra = <<<EOT
			left join D024 on D024_Id=T139_D024_Id
			where (T139_D024_Id='{$r_D024_Id}')
EOT;

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
