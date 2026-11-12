<?php
namespace hardness;

/**
* Ações
*/
	switch($g['r'][3]){
		/**
		* Grid Principal
		*/
		case 'cad068GridParticipanteXML':
			/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
           	array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('C043_Id', $g['divId'])),
            array('colunaValor' => 'C043_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'C043_C004_Id', 'colunaTitulo' => 'C043_C004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'C043_Nome_Autorizado', 'colunaTitulo' => 'Nome'),
            array('colunaValor' => 'C043_CNPJ', 'colunaTitulo' => 'CNPJ','style' => 'width:1px'),
			array('colunaValor' => 'C043_CPF', 'colunaTitulo' => 'CPF', 'style' => 'width:1px'),
        );

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'C043';

		$extra = <<<EOT
			where C043_C004_Id = '{$g['empresaAtual']}'
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'C043_Nome_Autorizado' => array('titulo' => 'Nome', 'tipo' => 'texto'),
			'C043_CPF' => array('titulo' => 'CPF', 'tipo' => 'texto'),
            'C043_CNPJ' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);


		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad068IncluirParticipante', 'janelaUrl' => '/cad/cad068/content/cad068ContentIncluirParticipante/', 'janelaTamanho' => '[400,180]'),
		);
		echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
 		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').split('|');
				var id = $(this).closest('tr').attr('id').replace(/C043_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cadastro - ID: ' + id, '/cad/cad068/content/cad068ContentIncluirParticipante/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [400,180]);
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
