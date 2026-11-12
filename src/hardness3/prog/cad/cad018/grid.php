<?php
namespace hardness;

// Carrega funções callback
require_once('prog/cad/cad016/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 22/11/2011
	*/                 
	case 'cad018GridDespachantes':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D119_Id', $g['divId'])),
			array('colunaValor' => 'D119_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
			array('colunaValor' => 'D119_Nome', 'colunaTitulo' => 'Despachante'),
			array('colunaValor' => 'D119_Telefone', 'colunaTitulo' => 'Telefone'),
			array('colunaValor' => 'D119_Email', 'colunaTitulo' => 'E-Mail'),
			array('colunaValor' => 'D119_Endereco', 'colunaTitulo' => 'Endereço'),
			array('colunaValor' => 'D119_D020_Id', 'colunaTitulo' => 'Cidade', 'colunaExibir'=>false),
			array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
			array('colunaValor' => 'D119_D018_Id', 'colunaTitulo' => 'Estado', 'colunaExibir'=>false),
			array('colunaValor' => 'D018_Estado', 'colunaTitulo' => 'Estado'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D119';
		
		$extra = <<<EOT
			left join D020 on D020_Id=D119_D020_Id
			left join D018 on D018_Id=D119_D018_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad018FormDespachantesBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad018FormDespachantesBtnIncluir">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad018FormDespachantesBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Despachantes', '/cad/cad018/content/cad018ContentDespachantesIncluir/', '&tab=geral', [600,350]);
				});
			} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D119_Nome' => array('titulo' => 'Despachante', 'tipo' => 'texto'),
			'D119_Telefone' => array('titulo' => 'Telefone', 'tipo' => 'texto'),
			'D119_Email' => array('titulo' => 'E-Mail', 'tipo' => 'texto'),
			'D119_Endereco' => array('titulo' => 'Endereço', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D018_Estado' => array('titulo' => 'Estado', 'tipo' => 'texto'),
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
					var chavePrimaria = 'D119_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Despachantes - ID: ' + acaoId, '/cad/cad018/content/cad018ContentDespachantesIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [500,250]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;	
		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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
