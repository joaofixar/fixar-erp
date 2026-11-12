<?php
namespace hardness;

switch($g['r'][3]){
	/**
	* Definição do Grid (Tabela)
	*/
	case 'cad025GridMarcas':
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array(
                 'D082_Id', $g['divId']),
            ),
            array('colunaValor' => 'D082_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D082_Codigo_Padrao', 'colunaTitulo' => 'Código', 'colunaExibir' => false),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D082_C008_Id', 'colunaTitulo' => 'IdTipo', 'colunaExibir' => false),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo'),
            array('colunaValor' => 'D082_Percentual_Acrescimo', 'colunaTitulo' => 'Acréscimo(%)', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D082_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'flagAtivo', 'colunaEditarGeraSelect' =>
            	array(
					array('title' => 'SIM', 'value' => 'S'),
					array('title' => 'NÃO', 'value' => 'N')
				)
            ),
            array('colunaValor' => 'if(D082_Flag_Ativo = "S", "SIM", "NÃO") as flagAtivo', 'colunaTitulo' => 'IdTipo', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = "D082";

		$extra = <<<EOT
		left join C008 on C008_Id=D082_C008_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad025Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
		/*<div class="buttonsBottomTop">
			<button type="button" id="cad025Incluir">Incluir</button>
			
		</div>*/
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad025Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Marca', '/cad/cad025/content/cad025contentCadastroMarca/','', [400,250]);
			});
		} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D082_Codigo_Padrao' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'D082_Percentual_Acrescimo' => array('titulo' => 'Acréscimo', 'tipo' => 'moeda'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
			var id = $(this).closest('tr').attr('id').replace(/D082_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Marca - ID: ' + id, '/cad/cad025/content/cad025contentCadastroMarca/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [400,200]);
		});
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
