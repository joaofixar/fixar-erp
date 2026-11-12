<?php
namespace hardness;

require_once('prog/etc/etc008/grid_func-callback.php');

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 04/01/2012
	*/                 
	case 'etc009GridContadoresClientes':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C026_Id', $g['divId'])),
			array('colunaValor' => 'C026_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
			array('colunaValor' => 'C026_Razao_Social', 'colunaTitulo' => 'Razão Social'),
			array('colunaValor' => 'C026_Nome_Contador', 'colunaTitulo' => 'Contador'),
			array('colunaValor' => 'C026_Nome_Contato', 'colunaTitulo' => 'Contato'),
			array('colunaValor' => 'C026_Telefone', 'colunaTitulo' => 'Telefone'),
			array('colunaValor' => 'C026_Observacao', 'colunaTitulo' => 'Observação'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'C026';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc009FormContadorClienteBtnIncluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc009FormContadorClienteBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Contador', '/etc/etc009/content/etc009ContentFormCotadorClientesIncluir/', '&tab=geral', [400,300]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C026_Razao_Social' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'C026_Nome_Contador' => array('titulo' => 'Contador', 'tipo' => 'texto'),
			'C026_Nome_Contato' => array('titulo' => 'Contato', 'tipo' => 'texto'),
			'C026_Telefone' => array('titulo' => 'Telefone', 'tipo' => 'texto'),
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
					var chavePrimaria = 'C026_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Contador - ID: ' + acaoId, '/etc/etc009/content/etc009ContentFormCotadorClientesIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
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
