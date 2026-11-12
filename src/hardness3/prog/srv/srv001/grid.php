<?php
namespace hardness;
require_once('grid_func-callback.php');
require_once('grid_func-js.php');

	switch($g['r'][3]){
		case 'srv001grid001':
		$grid = array(
					array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D112_Id', $g['divId'])),
					array('colunaValor'=>'D112_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
					array('colunaValor'=>'D112_Titulo', 'colunaTitulo'=>'Título'),
					array('colunaValor'=>'D112_Descricao', 'colunaTitulo'=>'Descrição'),
					array('colunaValor'=>'D112_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'callback' => 'gCorrigeTempo'),
					array('colunaValor'=>'D112_Valor_Unitario', 'colunaTitulo'=>'Valor', 'callback'=>'gCorrigeNumero'),
					array('colunaValor'=>'D112_D110_Id', 'colunaTitulo'=>'Competência', 'colunaExibir'=>false),
					array('colunaValor'=>'D110_Titulo', 'colunaTitulo'=>'Competência'),
					array('colunaValor'=>'T212_Nome', 'colunaTitulo'=>'Tipo'),
				);
		
		$from = 'D112';

		$extra = <<<EOT
					left join D110 on D110_Id=D112_D110_Id
					left join T212 on T212_Id=D112_T212_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="srv001Incluir">Incluir</button>
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#srv001Incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Serviços', '/srv/srv001/content/form2/', '', [500,350]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D112_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
			'D112_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D110_Titulo' => array('titulo' => 'Competência', 'tipo' => 'texto'),
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
				<span class="item" style="text-align:right">Serviço<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       $novoIdTelaForm = uniqid();
       echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(/D112_Id-/, '');
	               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Serviço - ID: ' + acaoId, '/srv/srv001/content/form1/', '&acaoId=' + encodeURIComponent(acaoId), [700,600]);
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
	
	case 'srv001gridProcessos':
		
		$grid = array(
					array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D113_Id', $g['divId'])),
					array('colunaValor'=>'D113_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir'=>false),
					array('colunaValor'=>'D113_Titulo', 'colunaTitulo'=>'Título', 'colunaEditar'=>true),
					array('colunaValor'=>'D113_Descricao', 'colunaTitulo'=>'Descrição', 'colunaEditar'=>true),
					//array('colunaValor'=>'D113_D110_Id', 'colunaTitulo'=>'Competencia', 'callback'=>'validaCompetencia'),
				);
		
		$from = 'D113';

		$extra = <<<EOT
				where D113_D112_Id='{$r_acaoId}'
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       // $novoIdTelaForm = uniqid();
       // echo  <<<EOT
		// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	           // var dbclickPrevent = false;
				//$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					//if (dbclickPrevent) { return false; }
					//dbclickPrevent = true;
					//setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// // somente executa a ação ao clicar na linha ou na coluna
					// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// // pega o ID da linha
					// var id = $(this).closest('tr').attr('id').split('|');
					// var acaoId = id[0].replace(/D112_Id-/, '');
	               // abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Serviço - ID: ' + acaoId, '/srv/srv001/content/form1/', '&acaoId=' + encodeURIComponent(acaoId), [500,500]);
	           // });
		// } </script></div>
// EOT;
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
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões

	break;
	
	case 'srv001gridMateriais':
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D112_Id = (isset($r_D112_Id)) ? $r_D112_Id : false;
		
		$grid = array(
					//array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'validaCampoExcluirProcessos'),
					array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha', 'callbackParameter'=>array('D114_Id', $g['divId'])),
					array('colunaValor'=>'D114_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir'=>false),
					array('colunaValor'=>'D114_Quantidade', 'colunaTitulo'=>'Qte', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2'),										
					array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Unidade', 'colunaExibir'=>false),
					array('colunaValor'=>'D114_D037_Id', 'colunaTitulo' => 'Unidade', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true), 'callback' => 'buscaUnidade'),					
					array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição'),
					array('colunaValor'=>'D082_Marca', 'colunaTitulo'=>'Marca'),					
				);
		
		$from = 'D114';

		$extra = <<<EOT
					LEFT JOIN D009 on D009_Id=D114_D009_Id										
					LEFT JOIN D049 on D049_Id=D009_D049_Id
					LEFT JOIN D001 on D001_Id=D049_D001_Id
					LEFT JOIN D082 on D082_Id=D049_D082_Id
					left join D037 on D037_Id=D114_D037_Id
					where D114_D112_Id='{$D112_Id}'
				
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
       // $novoIdTelaForm = uniqid();
       // echo  <<<EOT
		// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	            //var dbclickPrevent = false;
				//$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					//if (dbclickPrevent) { return false; }
					//dbclickPrevent = true;
					//setTimeout(function() { dbclickPrevent = false; }, 500);
	               	// // somente executa a ação ao clicar na linha ou na coluna
					// if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// // pega o ID da linha
					// var id = $(this).closest('tr').attr('id').split('|');
					// var acaoId = id[0].replace(/D112_Id-/, '');
	               // abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Serviço - ID: ' + acaoId, '/srv/srv001/content/form1/', '&acaoId=' + encodeURIComponent(acaoId), [500,500]);
	           // });
		// } </script></div>
// EOT;
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
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');

	break;
	}
?>
