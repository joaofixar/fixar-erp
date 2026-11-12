<?php
namespace hardness;
             /**
 * VEN - VEN001- Pedidos
 */
// Classes
//require_once('bibliotecas/classes/VEN013.php');
//$VEN013 = new VEN013();
// Carrega funções callback
require_once('prog/est/est012/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est012/grid_func-js.php');
// Imagem para exclusão da linha
/**
 * Ações
 */
switch($g['r'][3]){
	case 'est012GridPrincipal':
		/**
		 * Definição do Grid (Tabela)
		 */	
		
		$grid = array(
			array('colunaValor' => 'naoLiberar', 'colunaTipo' => 'livre', 'callback' => 'est012NaoLiberar', 'style'=>'width:1%;'),
			array('colunaValor' => 'Liberar', 'colunaTipo' => 'livre', 'callback' => 'est012Liberar', 'style'=>'width:1%;'),		 	
 			array('colunaValor' => 'T010_Data', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style'=>'width:1%;'),
 			array('colunaValor' => 'T173_Id', 'colunaTitulo' => 'Número', 'chavePrimaria' => true, 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
 			array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'T010_Id', 'chavePrimaria' => true, 'style'=>'width:1%;', 'colunaAlinhar'=>'center', 'colunaExibir'=>false),
			array('colunaValor' => 'D024_Nome_Empresa','colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'ValorCotacao', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Valor Cotação','callback' => 'campoValorCotacao'),
			//array('colunaValor' => 'T173_Valor_Pretendido_Alcada', 'colunaTitulo' => 'Valor Pretendido','callback' => 'gCorrigeNumero'),
			array('colunaValor' => '(select count(*) from T059 where T059_T010_Id=T010_Id)', 'colunaTitulo' => 'Itens', 'style'=>'width:1%;', 'colunaAlinhar'=>'center'),
			array('colunaValor' => 'T010_Flag_Tipo', 'colunaTitulo' => 'Tipo'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
		);
		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T173';

		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T173_D024_Id
			left join T010 on T010_Id=T173_T010_Id
			LEFT JOIN C007 on C007_Id=T010_C007_Id
			WHERE T010_C004_Id='{$g['empresaAtual']}'
			AND T173_Flag_Alcada_Nivel='{$g['c007']['C007_Flag_Vendedor']}'
			AND T173_Flag_Alcada_Status='L'
			AND (T010_C004_Id='{$g['empresaAtual']}')
			ORDER BY T010_Data DESC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T010_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
			'T010_Data' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T010_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
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
				var id = $(this).closest('tr').attr('id').split('|');
				var T173_Id = id[0].replace(/T173_Id-/, '');
				var T010_Id = id[1].replace(/T010_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cotação de Compra - Número: ' + T010_Id, '/est/est008/content/est008contentCadastroCotacaoCompra/', '&acaoId=' + encodeURIComponent(T010_Id) + '&tabela={$from}', [900,500]);
			});
		} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Aguardando liberação<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
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
