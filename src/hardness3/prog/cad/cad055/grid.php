<?php
namespace hardness;

switch($g['r'][3]) {
	case 'principal':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D142_Id', $g['divId']), 'style' => 'width: 1%'),
 			array('colunaValor' => 'D142_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
 			array('colunaValor' => 'D142_Atalho', 'colunaTitulo' => 'Tecla de Atalho', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
 			array('colunaValor' => 'D142_Nome', 'colunaTitulo' => 'Nome'),
 			array('colunaValor' => 'C029_Descricao_Acesso', 'colunaTitulo' => 'Permissão'),
			//array('colunaValor' => 'D142_Descricao', 'colunaTitulo' => 'Descrição'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "D142";
		
		$extra = <<<EOT
LEFT JOIN C029 ON C029_Id = D142_C029_Id
EOT;
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="cad055Incluir">Incluir</button>
				
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad055Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Tecla de Atalho', '/cad/cad055/content/cadastro/','', [700,300]);
			});
		} </script></div>
EOT;
			
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D142_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
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
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D142_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Tecla de Atalho - ID: ' + id, '/cad/cad055/content/cadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [700,330]);
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