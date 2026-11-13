<?php
namespace hardness;

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 22/11/2011
	*/                 
	case 'cad041GridTiposVeiculos':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D126_Id', $g['divId'])),
			array('colunaValor' => 'D126_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D126_Descricao', 'colunaTitulo' => 'Descrição'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D126';	
		$extra = <<<EOT
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad040FormCadastroTiposVeiculosBtnIncluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad040FormCadastroTiposVeiculosBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Tipos de Veículos', '/cad/cad041/content/cad041ContentTiposVeiculo/', '&tab=geral', [500,350]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D126_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
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
					var chavePrimaria = 'D126_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Tipo Veículo - ID: ' + acaoId, '/cad/cad041/content/cad041ContentTiposVeiculo/', '&acaoId=' + encodeURIComponent(acaoId), [500,350]);
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
