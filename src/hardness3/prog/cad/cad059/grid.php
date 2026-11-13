<?php
namespace hardness;

switch($g['r'][3]) {
	case 'cad059GridMoedas':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D044_Id', $g['divId']), 'style' => 'width: 1%'),
 			array('colunaValor' => 'D044_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => true),
 			array('colunaValor' => 'D044_Moeda', 'colunaTitulo' => 'Moeda'),
 			array('colunaValor' => 'D044_Simbolo', 'colunaTitulo' => 'Símbolo', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "D044";
		
		$extra = <<<EOT
EOT;
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D044_Moeda' => array('titulo' => 'Moeda', 'tipo' => 'texto'),
			'D044_Simbolo' => array('titulo' => 'Símbolo', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad059Incluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad059Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Moeda', '/cad/cad059/content/cad059ContentCadastroMoeda/','', [300,120]);
				});
			} </script></div>
EOT;

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
				var id = $(this).closest('tr').attr('id').replace(/D044_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Moeda', '/cad/cad059/content/cad059ContentCadastroMoeda/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [300,120]);
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
