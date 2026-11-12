<?php
namespace hardness;

require_once('prog/cad/cad065/grid_func-callback.php');

switch($g['r'][3]){
	/**
	* @author Ricardo Norio
	*/
	case 'cad065GridLacres':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D171_Id', $g['divId'])),
			array('colunaValor' => 'D171_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D171_Numero_Lacre', 'colunaTitulo' => 'Identificação'),
            array('colunaValor' => 'D171_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D171_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center', 'callback' => 'validaCampoTipo'),
            array('colunaValor' => 'D171_Flag_Ativo', 'colunaTitulo' => 'Status', 'colunaAlinhar' => 'center', 'callback' => 'validaCampoStatus')
        );

        /**
		* Definição da SQL
		*/
		$from = 'D171';
		$sql_ = urlencode(base64_encode($geradoSql));
		$extra = <<<EOT
			WHERE D171_Flag_Ativo = 'S'
			AND D171_C004_Id = '{$g['empresaAtual']}'
			ORDER BY D171_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad065GridLacresBtnIncluir">Incluir</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad065GridLacresBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Lacre', '/cad/cad065/content/cad065LacresCadastro/', '&tab=geral',  [600,300]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D171_Numero_Lacre' => array('titulo' => 'Identificação', 'tipo' => 'texto'),
			'D171_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D171_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => 
				array(
					array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => '1 - Transporte', 'value' => '1'),
					array('title' => '2 - Carga', 'value' => '2'),
					array('title' => '3 - MDF-e', 'value' => '3'),
					array('title' => '4 - Rodoviário', 'value' => '4')
				)
            ),		
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		// Totalizador de registros
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

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
					var chavePrimaria = 'D171_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Lacre: ' + acaoId, '/cad/cad065/content/cad065LacresCadastro/', '&acaoId=' + encodeURIComponent(acaoId), [600,300]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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

	case 'cad065GridLacresInativos':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D171_Id', $g['divId'])),
			array('colunaValor' => 'D171_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D171_Numero_Lacre', 'colunaTitulo' => 'Identificação'),
            array('colunaValor' => 'D171_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D171_Tipo', 'colunaTitulo' => 'Tipo','callback' => 'validaCampoTipo'),
            array('colunaValor' => 'D171_Flag_Ativo', 'colunaTitulo' => 'Status', 'colunaAlinhar' => 'center', 'callback' => 'validaCampoStatus')
        );

		/**
		* Definição da SQL
		*/
		$from = 'D171';
		$extra = <<<EOT
			WHERE D171_Flag_Ativo = 'N'
			ORDER BY D171_Id
EOT;
        /**
        * Função que define classes CSS para cada linha do grid
        */
        function gridLinha($array) {
            if ($array['todosCampos']['D171_Flag_Ativo'] == 'N') {
               return 'linhaCinza';
            }
        }

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D171_Numero_Lacre' => array('titulo' => 'Identificação', 'tipo' => 'texto'),
			'D171_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D171_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => 
				array(
					array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => '1 - Transporte', 'value' => '1'),
					array('title' => '2 - Carga', 'value' => '2'),
					array('title' => '3 - MDF-e', 'value' => '3'),
					array('title' => '4 - Rodoviário', 'value' => '4')
				)
            ),		
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
		// Totalizador de registros
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D171_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Alterar Lacre: ' + id, '/cad/cad065/content/cad065LacresCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,300]);
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