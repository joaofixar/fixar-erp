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
	case 'etc010GridUsuariosClientes':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C025_Id', $g['divId'])),
			array('colunaValor' => 'C025_Id', 'colunaTitulo' => 'ID', 'colunaExibir'=>false, 'chavePrimaria'=>true),
			array('colunaValor' => 'C025_Banco_Dados', 'colunaTitulo' => 'BD'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
			array('colunaValor' => 'C026_Razao_Social', 'colunaTitulo' => 'Contabilidade'),
			array('colunaValor' => 'IF(C025_Flag_Ativo="N","NÃO","SIM") as flagAtivo', 'colunaExibir' => false),
			array('colunaValor' => 'C025_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => 
				array(
					array('title' => 'SIM', 'value' => 'S'), 
					array('title' => 'NÃO', 'value' => 'N')
				), 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'callback' => 'campoMostraValor', 'callbackParameter' => 'flagAtivo'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'C025';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join C026 on C025_C026_Id=C026_Id
		    left join D024 on D024_Id=C025_D024_Id
		    order by C025_Banco_Dados
		
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="etc010FormUsuariosClienteBtnIncluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#etc010FormUsuariosClienteBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Usuários Clientes', '/etc/etc010/content/etc010ContentFormUsuariosClientesIncluir/', '&tab=geral', [400,300]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C025_Banco_Dados' => array('titulo' => 'Banco de Dados', 'tipo' => 'texto'),
			'D024_Nome_Empresa' => array('titulo' => 'Razão Social', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Nome Fantasia', 'tipo' => 'texto'),
			'C026_Razao_Social' => array('titulo' => 'Contabilidade', 'tipo' => 'texto'),
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
					var chavePrimaria = 'C025_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Usuários Clientes - ID: ' + acaoId, '/etc/etc010/content/etc010ContentFormUsuariosClientesIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
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
