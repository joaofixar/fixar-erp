<?php
namespace hardness;
/**
 * CAD - CAD010 - Bens
 */
// Classes
//require_once('bibliotecas/classes/CAD012.php');
//$CAD012 = new CAD012();
// Carrega funções callback
require_once('prog/cad/cad012/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/cad/cad012/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: cad012tab01
	 */
	case 'cad012grid01':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D111_Id', $g['divId'])),
			array('colunaValor' => 'D111_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D111_D037_Id', 'colunaTitulo' => 'Unidade', 'colunaExibir' => false),
			array('colunaValor' => 'D111_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D111_Qte_Estoque', 'colunaTitulo' => 'Qte Estoque'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D111';
		$extra = <<<EOT
			left join D037 on D037_Id=D111_D037_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad012Incluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad012Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Materiais', '/cad/cad012/content/cad012content02/', '', [500,200]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D111_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D111_Qte_Estoque' => array('titulo' => 'Qte Estoque', 'tipo' => 'numero'),
			'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Materiais<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/D111_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cadastro - ID: ' + id, '/cad/cad012/content/cad012content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,200]);
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
