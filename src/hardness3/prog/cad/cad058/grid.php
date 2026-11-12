<?php
namespace hardness;

switch($g['r'][3]) {
	case 'cad058GridConfiguracoes':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
 			array('colunaValor' => 'C031_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
 			array('colunaValor' => "CONCAT('(',C031_C004_Id,') - ', C004_Nome_Abreviado)", 'colunaTitulo' => 'Empresa', 'style' => 'width: 1%', 'colunaAlinhar' => 'left'),
 			array('colunaValor' => 'C031_Campo', 'colunaTitulo' => 'Campo', 'style' => 'width: 1%', 'colunaAlinhar' => 'left'),
 			array('colunaValor' => 'C031_Valor_Padrao', 'colunaTitulo' => 'Valor Padrão', 'style' => 'width: 1%', 'colunaAlinhar' => 'left'),
 			array('colunaValor' => 'C031_Valor', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'left', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'width: 1%'),
 			array('colunaValor' => 'C031_Observacao', 'colunaTitulo' => 'Observação', 'colunaAlinhar' => 'left'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "C031";
		
		$extra = <<<EOT
			LEFT JOIN C004 ON C004_Id=C031_C004_Id
EOT;
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'C031_Campo' => array('titulo' => 'Campo', 'tipo' => 'texto'),
			'C031_C004_Id,C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
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
				<button type="button" id="cad057Incluir">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad057Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Impressora', '/cad/cad057/content/cad057Impressoras/','', [400,100]);
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
				var id = $(this).closest('tr').attr('id').replace(/D147_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Banco', '/cad/cad057/content/cad057Impressoras/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [400,100]);
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
