<?php
namespace hardness;
/**
 * CAD - CAD008 - Cadastro de Certificados
 */
// Classes
//require_once('bibliotecas/classes/CAD007.php');
//$CAD007 = new CAD007();
// Carrega funções callback
require_once('prog/cad/cad007/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/cad/cad007/grid_func-js.php');

// Imagem para exclusão da linha

/**
 * Ações
 */
switch($g['r'][3]){
	/**
	 * Grid Principal
	 * Chamado em: cad007tab01
	 */
	case 'cad007grid01':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'campoExclusao'),
			array('colunaValor' => 'T140_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true),
			array('colunaValor' => 'T140_D024_Id', 'colunaTitulo' => 'Id Fornecedor', 'colunaExibir' => false),
			array('colunaValor' => 'T140_D001_Id', 'colunaTitulo' => 'Id Produto', 'colunaExibir' => false),
			array('colunaValor' => 'T140_Tipo_Certificado', 'colunaTitulo' => 'Tipo'),
			array('colunaValor' => 'T140_Certificadora', 'colunaTitulo' => 'Certificadora'),
			array('colunaValor' => 'T140_Data_Emissao', 'colunaTitulo' => 'Emissão'),
			array('colunaValor' => 'T140_Data_Validade', 'colunaTitulo' => 'Validade'),
			//array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'ifnull(group_concat(concat(D0011.D001_Codigo_Produto,\' - \',D0011.D001_Descricao_Produto) SEPARATOR \'<br>\'),concat(D0012.D001_Codigo_Produto,\' - \',D0012.D001_Descricao_Produto)) as produto', 'colunaTitulo' => 'Produtos'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'IF(T140_Link != "", T140_Link, T140_Nome_Arquivo)', 'colunaTitulo' => 'Arquivo'),	
			array('colunaValor' => 'visualizarArquivo', 'colunaTitulo' => 'Visualizar', 'colunaTipo'=>'livre', 'callback'=>'visualizarArquivo', 'callbackParameter' => array('T140_Id', 'documentoProduto'), 'style' => 'width: 1%'),
			array('colunaValor' => '', 'colunaTitulo' =>'Certificado', 'colunaTipo'=>'livre', 'callback'=>'abrirCertificado'),
		);

		/**
		 *
		 * Definição da SQL
		 */
		$from = 'T140';

		$extra = <<<EOT
				left join D024 ON D024_Id=T140_D024_Id
				left join T303 ON T140_Id=T303_T140_Id
				left join D001 AS D0011 ON D0011.D001_Id=T303_D001_Id
				left join D001 AS D0012 ON D0012.D001_Id=T140_D001_Id
				GROUP BY T140_Id
EOT;

		/**
		 * Botões
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad007Incluir'),
		);
		echo Botoes::processar($botoes);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="cad007Incluir">Incluir</button>
			<div class="divHidden">-->
			<script type="text/javascript"> if (typeof jQuery != 'undefined')
			 {		
				
				$('#cad007Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Certificados', '/cad/cad007/content/cad007content02/', '', [520,280]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T140_Tipo_Certificado' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'T140_Certificadora' => array('titulo' => 'Certificadora', 'tipo' => 'texto'),
			'T140_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T140_Data_Validade' => array('titulo' => 'Validade', 'tipo' => 'data'),
			'D0011.D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
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
		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Certificados<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/	
		/**
		 * Totais
		 */
		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

			
		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T140_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Certificado - ID: ' + id, '/cad/cad007/content/cad007content02Editar/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1200,700]);
			});
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

	case 'cad007GridItensDocumento':
		/**
		 * Definição do grid tabela
		 */

		$T303 = mysql_query("SELECT T303_Id FROM T303 WHERE T303_T140_Id = '{$r_acaoId}'");
		if(mysql_num_rows($T303) <= 0){
			$T140 = mysql_query("SELECT T140_D001_Id FROM T140 WHERE T140_Id = '{$r_acaoId}'");
			$mT140 = mysql_fetch_assoc($T140);
			if(!empty($mT140['T140_D001_Id']) || $mT140['T140_D001_Id'] > 0){
				mysql_query("INSERT INTO T303 (T303_T140_Id,T303_D001_Id) VALUES ('{$r_acaoId}','{$mT140['T140_D001_Id']}')");
			}
		}

		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T303_Id', $g['divId'])),
			array('colunaValor' => 'T303_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
		);
		$from = 'T303';
		$extra = <<<EOT
				left join D001 on D001_Id=T303_D001_Id
				where T303_T140_Id='{$r_acaoId}' 
	EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
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
		$g['smarty']->display('grid.tpl');
	break;
}
?>
