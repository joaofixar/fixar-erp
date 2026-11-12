<?php
namespace hardness;
// Carrega funções callback
require_once('prog/ven/ven009/grid_func-callback.php');
// Carrega funções do ajax
require_once('prog/ven/ven009/grid_func-js.php');

/**
 * Ações
 */
	switch($g['r'][3]){
	/**
	* Grid Principal
	* Chamado em: ven009tab01
	*/
	case 'ven009grid01':
		/**
		* Definição do Grid (Tabela)
		*/			           
    		
		$grid = array(	
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T143_Id', $g['divId'])),
			array('colunaValor'=> 'T143_Id', 'colunaTitulo'=>'ID', 'chavePrimaria'=>true),
			array('colunaValor'=> 'D106_Descricao', 'colunaTitulo'=>'Bem'),
			array('colunaValor'=> 'T143_T141_Id', 'colunaTitulo'=>'Contrato','callback'=>'abreContrato'),
			array('colunaValor'=> 'D020_Nome_Cidade', 'colunaTitulo'=>'Cidade'),
			array('colunaValor'=> 'D018_Estado', 'colunaTitulo'=>'Estado'),
			array('colunaValor'=> 'D030_Nome_Pais', 'colunaTitulo'=>'País'),
			array('colunaValor'=> 'T143_Data_Transferencia', 'colunaTitulo'=>'Data Transferência', 'callback'=>'gCorrigeData'),
			array('colunaValor'=> 'T143_Data_Prev_Retorno', 'colunaTitulo'=>'Data Prev. Retorno', 'callback'=>'gCorrigeData'),
			array('colunaValor'=> 'T143_Observacao', 'colunaTitulo'=>'Observação'),
			array('colunaValor'=> 'D024_Nome_Fantasia', 'colunaTitulo'=>'D024_Nome_Fantasia', 'colunaExibir'=>false),
		);
		
		$from = "T143";
		
		//select D024_Nome_Fantasia from T141 left join D024 on D024_Id=T141_D024_Id where T141_Id='$T143_T141_Id'
		
		$extra = <<<EOT
			left join D106 on D106_Id=T143_D106_Id
	    	left join T141 on T141_Id=T143_T141_Id
	    	left join D020 on D020_Id=T143_D020_Id
	    	left join D018 on D018_Id=T143_D018_Id
	    	left join D030 on D030_Id=T143_D030_Id	
	    	left join D024 on D024_Id=T141_D024_Id
	    	where (T143_C004_Id={$g['empresaAtual']})
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="ven009Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven009Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Registro de Transferência', '/ven/ven009/content/ven009content02/', '', [500,380]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D106_Descricao' => array('titulo' => 'Bem', 'tipo' => 'texto'),
			'D024_Nome_Fantasia' => array('titulo' => 'Contrato', 'tipo' => 'texto'),
			'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
			'D018_Estado' => array('titulo' => 'Estado', 'tipo' => 'texto'),
			'D030_Nome_Pais' => array('titulo' => 'País', 'tipo' => 'texto'),
			'T143_Data_Transferencia' => array('titulo' => 'Data Transferência', 'tipo' => 'data'),
			'T143_Data_Prev_Retorno' => array('titulo' => 'Data Prev. Retorno', 'tipo' => 'data'),
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
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T143_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Atualização de Registro ID: ' + id, '/ven/ven009/content/ven009content02/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [500,380]);
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
