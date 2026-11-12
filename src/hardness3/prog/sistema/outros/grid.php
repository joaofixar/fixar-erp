<?php
namespace hardness;

	//require_once('prog/sistema/outros/grid_func-callback.php');
	//require_once('prog/sistema/outros/grid_func-js.php');

switch($g['r'][3]) {

	case 'etc006GridRetornosPendenteAgenda':
		/**
		* Definição do Grid (Tabela)
		*/
		$hoje = date("Y-m-d");

		$D085=mysql_query("SELECT * FROM D085 LEFT JOIN D024 ON D024_Id=D085_D024_Id WHERE D085_C007_Id='{$g['usuarioAtual']}' AND D085_Data_Previsao_Retorno='{$hoje}' AND 
		D085_Flag_Manual='S'
		AND (D085_Data_Retorno IS NULL OR D085_Data_Retorno='0000-00-00' OR D085_Data_Retorno='')");
		$rows=mysql_num_rows($D085);

		$grid = array(
 			//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D062_Id', $g['divId']), 'style' => 'width: 1%'),
 			array('colunaValor' => 'D085_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
 			array('colunaValor' => 'concat(D024_Nome_Empresa,"  ", " (", D024_DDD_Telefone_1, ") ",D024_Telefone_1) as Empresa', 'colunaTitulo' => 'Empresa'),
 			array('colunaValor' => 'D085_Mensagem', 'colunaTitulo' => 'Mensagem'),
 			array('colunaValor' => 'D085_Retorno', 'colunaTitulo' => 'Retorno', 'colunaEditar'=>true),
 			array('colunaValor' => 'D085_Data', 'colunaTitulo' => 'Dt Inclusão', 'callback'=>'gCorrigeData'),
 			array('colunaValor' => 'D085_Data_Previsao_Retorno', 'colunaTitulo' => 'Dt Previsão Retorno', 'callback'=>'gCorrigeData', 'colunaEditar'=>true, 'colunaEditarTodos'=>true),
 			array('colunaValor' => 'D085_Data_Retorno', 'colunaTitulo' => 'Dt Retorno', 'callback'=>'gCorrigeData', 'colunaEditar'=>true),
 			//array('colunaValor' => 'D085_Flag_Retornado', 'colunaTitulo' => 'Retornado?', 'callback'=>'validaCampoRetornado'),
		);

		echo "<span style='color:darkblue;margin:20px;font-size:16px;'><b>Você possui {$rows} retornos pendentes em sua agenda: </b></span>";
		
		/**
		 * Definição da SQL
		 */
		$from = "D085";
		
		$extra = <<<EOT
		LEFT JOIN D024 ON D024_Id=D085_D024_Id
			WHERE 
		D085_C007_Id='{$g['usuarioAtual']}' AND 
		D085_Data_Previsao_Retorno='{$hoje}' AND 
		D085_Flag_Manual='S'
		AND (D085_Data_Retorno IS NULL OR D085_Data_Retorno='0000-00-00' OR D085_Data_Retorno='')
EOT;
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D062_Nome_Banco' => array('titulo' => 'Nome', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="cad056Incluir">Incluir</button> -->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad056Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Banco', '/cad/cad056/content/cad056Bancos/','', [400,100]);
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			/*
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D062_Id-/, '');
				//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Banco', '/cad/cad056/content/cad056Bancos/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [400,100]);
			});*/
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