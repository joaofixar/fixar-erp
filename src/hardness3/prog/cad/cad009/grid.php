<?php
namespace hardness;
/**
 * CAD - CAD009 - Catálogos
 */
// Classes
//require_once('bibliotecas/classes/cad009.php');
//$cad009 = new cad009();
// Carrega funções callback
require_once('prog/cad/cad009/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/cad/cad009/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: cad009tab01
	 */
	case 'cad009grid01':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
			array('colunaValor' => 'D107_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
			array('colunaValor' => 'D107_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D107_Observacao', 'colunaTitulo' => 'Observação'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'D107';

		$extra = <<<EOT
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad009Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad009Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Listas', '/cad/cad009/content/cad009content02/', '', [500,250]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D107_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D107_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Total Catálogos<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/D107_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Lista Fornecedor - ID: ' + id, '/cad/cad009/content/cad009content03/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,600]);
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
		
	/**
	* Demostrativo de Acrescimos e Descontos
	* Chamado em: fin001form02
	*/
	case 'cad009grid02':		
			
		 /**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusaoItem'),
			array('colunaValor' => "D108_Id", 'colunaTitulo' => 'ID','chavePrimaria'=>true),			
			array('colunaValor' => "D108_D107_Id", 'colunaExibir' => false),
			array('colunaValor' => "D108_Codigo", 'colunaTitulo' => 'Código'),
			//array('colunaValor' => "D001_Descricao_Produto", 'colunaTitulo' => 'Produto'),
			array('colunaValor' => "D108_Descricao_Pt", 'colunaTitulo' => 'Descrição Português', 'callback' => 'gSubstr', 'callbackParameter' => '50'),
			array('colunaValor' => "D108_Descricao_En", 'colunaTitulo' => 'Descrição Inglês', 'callback' => 'gSubstr', 'callbackParameter' => '50'),
			//array('colunaValor' => "D108_Observacao", 'colunaTitulo' => 'Observação'),
		);
			
		/**
		* Definição da SQL
		*/
		$from = 'D108';
		
		$extra = <<<EOT
					left join D001 on D001_Id=D108_D001_Id
					where (D108_D107_Id={$_REQUEST['acaoId']})
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D108_Codigo' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D108_Descricao_Pt' => array('titulo' => 'Português', 'tipo' => 'texto'),
			'D108_Descricao_En' => array('titulo' => 'Inglês', 'tipo' => 'texto'),
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
}
?>
