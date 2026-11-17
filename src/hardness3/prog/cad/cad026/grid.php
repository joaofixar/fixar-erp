<?php
namespace hardness;

switch($g['r'][3]){
		/**
		* Definição do Grid (Tabela)
		*/
	case 'cad026GridLinhas':
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'style' => 'width:1%', 'callback' => 'gExcluirLinha', 'callbackParameter' => array(
                 'D003_Id', $g['divId']),
            ),
            array('colunaValor' => 'D003_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'style' => 'width:1%'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo'),
            array('colunaValor' => 'D003_Codigo_Padrao', 'colunaTitulo' => 'Código', 'colunaExibir' => false),
            array('colunaValor' => 'Imagem', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Imagem', 'style' => 'width:50px;text-align: center;', 'callback' => 'mostrarImagemProdutosLinhas', 'colunaExibir' => false),
            array('colunaValor' => 'D003_Descricao_Padrao', 'colunaTitulo' => 'Descrição padrão', 'colunaExibir' => false),
            array('colunaValor' => 'D003_C008_Id', 'colunaTitulo' => 'IdTipo', 'colunaExibir' => false),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
            array('colunaValor' => 'D003_D082_Id', 'colunaTitulo' => 'IdMarca', 'colunaExibir' => false),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
        );
		
		/**
		 * Definição da SQL
		 */
		$from = "D003";
		
		$extra = <<<EOT
			left join C008 on C008_Id=D003_C008_Id
			left join D082 on D082_Id=D003_D082_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad026Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		
			/*<div class="buttonsBottomTop">
				<button type="button" id="cad026Incluir">Incluir</button>
				
			</div>*/
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad026Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir grupo', '/cad/cad026/content/cad026contentCadastroLinha/','', [600,250]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D003_Id' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
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
				var id = $(this).closest('tr').attr('id').replace(/D003_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar grupo - ID: ' + id, '/cad/cad026/content/cad026contentCadastroLinha/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,250]);
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
?>
