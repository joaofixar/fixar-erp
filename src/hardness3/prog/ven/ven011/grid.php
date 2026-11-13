<?php
namespace hardness;
/**
 * FIN - VEN011 - Contas a Pagar
 */
// Classes
//require_once('bibliotecas/classes/VEN011.php');
//$VEN011 = new VEN011();
// Carrega funções callback
require_once('prog/ven/ven011/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/ven/ven011/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: ven011tab01
	 */
	case 'ven011grid01':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
		array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T142_Id', $g['divId'])),
		array('colunaValor' => 'T142_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
		array('colunaValor' => 'T142_D106_Id', 'colunaTitulo' => 'IdEquip', 'colunaExibir' => false),
		array('colunaValor' => 'D106_Descricao', 'colunaTitulo' => 'Bem'),
		array('colunaValor' => 'T142_Descricao', 'colunaTitulo' => 'Descrição'),
		array('colunaValor' => 'T142_Data_Manutencao', 'colunaTitulo' => 'Data Manutenção', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T142_Valor', 'colunaTitulo' => 'Valor Manutenção', 'colunaCasasDecimais' => '2'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T142';

		$extra = <<<EOT
				left join D106 on D106_Id=T142_D106_Id
				where 
					(T142_C004_Id={$g['empresaAtual']})
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven011Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven011Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Manutenção de Bens', '/ven/ven011/content/ven011content02/', '', [500,260]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D106_Descricao' => array('titulo' => 'Bem', 'tipo' => 'texto'),
			'T142_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T142_Data_Manutencao' => array('titulo' => 'Data Manutenção', 'tipo' => 'data'),
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
				<span class="item" style="text-align:right">Manutenções<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/T142_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar - ID: ' + id, '/ven/ven011/content/ven011content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,260]);
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
}
?>
