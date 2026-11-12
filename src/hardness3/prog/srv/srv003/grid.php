<?php
namespace hardness;

require_once('prog/srv/srv003/grid_func-callback.php');

switch($g['r'][3]){
	/**
	* @author tiagofischer
	* @since 08/11/2011
	*/
	case 'srv003GridOperacao':
		
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T147_Id = (isset($r_T147_Id)) ? $r_T147_Id : false;
		
		$g['screen'] = 'big';
		// $sql =<<<EOT
		 // SELECT * FROM T147
		 // where (T147_Status='P') AND T147_D109_Id='{$acaoId}'
// EOT;
// 
		// echo $sql;
		// die();
		// $result = mysql_query($sql);
		// $linha = mysql_num_rows($result);
		// $linha2 = mysql_fetch_assoc($result);
		
		$grid = array(
				array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prd', 'colunaAlinhar' => 'left', 'callback'=>'validaCampoPrioridade'),
				array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço', 'colunaAlinhar' => 'left'),
				array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
				array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculoSrv003'),
				array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
				array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
				array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'center', 'callback'=>'validaCampoCliente'),
				array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T147_Pausado', 'colunaTitulo' => 'Status', 'colunaAlinhar' => 'center', 'callback'=>'validaCampoStatus'),
				array('colunaValor' => 'T147_Status', 'colunaTitulo' => 'Status', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
				array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'callback' => 'gCorrigeTempo', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
				array('colunaValor' => 'T147_Flag_Conferido', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
			);
			
			$from = 'T147';
	
			$extra = <<<EOT
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on T005_D024_Id=D024_Id
				left join D116 on D116_Id=T147_D116_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				where (T147_Status <> 'C' AND T147_Status <> 'N') AND T147_D109_Id='{$acaoId}'
				ORDER BY T147_Prioridade
EOT;
		
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		//Função que define a classe CSS para cada linha do GRID
		function gridLinha($array) {
			if ($array['todosCampos']['T147_Flag_Conferido'] == "R") {
				return 'linhaVermelho';
			}
		}
		
		
		//BLOCO COMENTADO PARA NÃO SER POSSÍVEL SELECIONAR OUTRA LINHA DO GRID.
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
		       		// Deseleciona todos, seleciona a linha
		       		//$('#{$g['divId']} .grid tr #iconesetar').remove();
		       		$('#{$g['divId']} .grid tr').removeClass('hover ui-state-highlight');
		       		var td = $(this).closest('tr').find('td:first');
		       		$(this).closest('tr').addClass('hover ui-state-highlight');
		       		// icone
		       		//var icone = $('<img>').attr({'src': '/hardness3/static/img/icone_setad.png', 'id': 'iconesetar'});
		       		//td.before(icone);
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
		$g['smarty']->display('big.grid.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		// $resposta['data'] = "/srv/srv003/content/srv003ContentExecucaoServico/?oneWindow=true&acaoId=" . urlencode($row['D109_Id']) . '&T147_Id=' . urlencode($rowT147['T147_Id']);
		$botoesEsq = $botoesDir = $botoesMid = '';
		$botoesDir .= '<button type="button" id="srv003GridOperacaoBtnSelecionar">Visualizar Serviço</button>';
		$botoesEsq .= '<button type="button" onclick="window.location = \'/srv/srv003/content/srv003ContentLogin/?oneWindow=true\'; return false;">Sair</button>';
		$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacaoPendentes/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Fila de Serviços</button>';
		$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacaoAprovar/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Aguardando aprovação</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('big.buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#srv003GridOperacaoBtnSelecionar').unbind('click').bind('click', function(data) {
				var \$tr = $('#{$g['divId']}').find('.grid-select tr.hover');
				// pega o ID da linha
				var id = \$tr.attr('id').split('|');
				var acaoId = id[0].replace('T147_Id-', '');
				window.location = '/srv/srv003/content/srv003ContentExecucaoServico/?oneWindow=true&acaoId=' + encodeURIComponent('{$acaoId}') + '&T147_Id=' + encodeURIComponent(acaoId);
		       	//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Iniciar Serviço: ' + acaoId, '/srv/srv003/outros/srv003OutrosServico/', '&acaoId=' + encodeURIComponent(acaoId), 'full');
			});			
			$('#srv003GridOperacaoBtn').unbind('click').bind('click', function(data) {
				var \$tr = $('#{$g['divId']}').find('.grid-select tr.hover');
				// pega o ID da linha
				var id = \$tr.attr('id').split('|');
				var acaoId = id[0].replace('T147_Id-', '');
				window.location = '/srv/srv003/content/srv003ContentIniciarServico/?oneWindow=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&T147_Id=' + encodeURIComponent(acaoId);
		       	//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Iniciar Serviço: ' + acaoId, '/srv/srv003/outros/srv003OutrosServico/', '&acaoId=' + encodeURIComponent(acaoId), 'full');
			});
		} </script></div>
EOT;
	break;
	
	case 'srv003GridOperacaoPendentes':
		
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		
		$g['screen'] = 'big';
		$sql =<<<EOT
		 SELECT * FROM T147
		 left join D110 on D110_Id=T147_D110_Id
		 left join D115 on D115_D110_Id=D110_Id
		 left join D109 on D109_Id=D115_D109_Id
		 where (T147_Status='P') AND D109_Id='{$acaoId}' AND T147_Prioridade <>0 ORDER BY T147_Prioridade
EOT;
		$result = mysql_query($sql);
		$linha = mysql_num_rows($result);
		
		//if($linha > 0){
			$grid = array(
				array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prd', 'colunaAlinhar' => 'left', 'callback'=>'validaCampoPrioridade'),
				array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço', 'colunaAlinhar' => 'left'),
				array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
				array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculoSrv003'),
				array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
				array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
				array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'center', 'callback'=>'validaCampoCliente'),
				array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
				array('colunaValor' => 'T147_Flag_Conferido', 'colunaTitulo' => 'Conferencia', 'colunaExibir' => false),
				array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tmp Est', 'callback' => 'gCorrigeTempo', 'colunaAlinhar' => 'center')
			);
			
			$from = 'T147';
	
			$extra = <<<EOT
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on T005_D024_Id=D024_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				
				left join D110 on D110_Id=T147_D110_Id
				left join D115 on D115_D110_Id=D110_Id
				left join D109 on D109_Id=D115_D109_Id
				left join D116 on D116_Id=T147_D116_Id
				where (T147_Status='P') AND D109_Id='{$acaoId}'
				AND (T147_D109_Id IS NULL OR T147_D109_Id='0')
				ORDER BY T147_Prioridade
EOT;
		//} 
		
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		//Função que define a classe CSS para cada linha do GRID
		function gridLinha($array) {
			if ($array['todosCampos']['T147_Flag_Conferido'] == "R") {
				return 'linhaVermelho';
			}
		}
		
		
		//BLOCO COMENTADO PARA NÃO SER POSSÍVEL SELECIONAR OUTRA LINHA DO GRID.
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
		       		// Deseleciona todos, seleciona a linha
		       		//$('#{$g['divId']} .grid tr #iconesetar').remove();
		       		$('#{$g['divId']} .grid tr').removeClass('hover ui-state-highlight');
		       		var td = $(this).closest('tr').find('td:first');
		       		$(this).closest('tr').addClass('hover ui-state-highlight');
		       		// icone
		       		//var icone = $('<img>').attr({'src': '/hardness3/static/img/icone_setad.png', 'id': 'iconesetar'});
		       		//td.before(icone);
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
		$g['smarty']->display('big.grid.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		
		// $sql = "select * from T147 WHERE T147_D109_Id='{$acaoId}' AND T147_Status='E'";
		// $result = mysql_query($sql);
		
		$sql2 = "select T147_D109_Id, T147_Status from T147";
		$result2 = mysql_query($sql2);
		
		$botoesEsq = $botoesDir = $botoesMid = '';
		$botoesDir .= '<button type="button" id="srv003GridOperacaoBtnSelecionar">Visualizar Serviço</button>';
		$botoesEsq .= '<button type="button" onclick="window.location = \'/srv/srv003/content/srv003ContentLogin/?oneWindow=true\'; return false;">Sair</button>';
		while($linha = mysql_fetch_array($result2)){
			if(($linha['T147_D109_Id'] == $acaoId) && (($linha['T147_Status']=='P') || ($linha['T147_Status']=='E'))){
				$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Meus Serviços</button>';
				$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacaoAprovar/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Aguardando Aprovação</button>';
				break;
			}
		}
		
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('big.buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#srv003GridOperacaoBtnSelecionar').unbind('click').bind('click', function(data) {
				var \$tr = $('#{$g['divId']}').find('.grid-select tr.hover');
				// pega o ID da linha
				var id = \$tr.attr('id').split('|');
				var acaoId = id[0].replace('T147_Id-', '');
				window.location = '/srv/srv003/content/srv003ContentIniciarServico/?oneWindow=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&T147_Id=' + encodeURIComponent(acaoId);
		       	//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Iniciar Serviço: ' + acaoId, '/srv/srv003/outros/srv003OutrosServico/', '&acaoId=' + encodeURIComponent(acaoId), 'full');
			});			
		} </script></div>
EOT;
	break;
	
	case 'srv003GridProcessosServicos':
	
		$T147_Id = (isset($r_T147_Id)) ? $r_T147_Id : false;
	
		$g['screen'] = 'big';
		$grid = array(
			array('colunaValor' => 'T148_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
			array('colunaValor' => 'T148_Titulo', 'colunaTitulo' => 'Serviço'),
			array('colunaValor' => 'T148_Descricao', 'colunaTitulo' => 'Descrição'),
			//array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado')
		);
		
		$from = 'T148';
//left join T147 on T148_T147_Id
		$extra = <<<EOT
			WHERE T148_T147_Id='{$T147_Id}'
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
	   $novoIdTelaForm = uniqid();
	   echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		       var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
		       		// Deseleciona todos, seleciona a linha
		       		$('#{$g['divId']} .grid tr').removeClass('hover ui-state-highlight');
		       		$(this).closest('tr').addClass('hover ui-state-highlight');
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
		$g['smarty']->display('big.grid.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		//$botoesDir .= '<button type="button" id="srv003GridOperacaoBtnSelecionar">Selecionar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('big.buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#srv003GridOperacaoBtnSelecionar').unbind('click').bind('click', function(data) {
				var \$tr = $('#{$g['divId']}').find('.grid-select tr.hover');
				// pega o ID da linha
				var id = \$tr.attr('id').split('|');
				var acaoId = id[0].replace('T147_Id-', '');
		       	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Iniciar Serviço: ' + acaoId, '/srv/srv003/outros/srv003OutrosServico/', '&acaoId=' + encodeURIComponent(acaoId), 'full');
			});			
		} </script></div>
EOT;
	break;
	
	case 'srv003GridMateriaisServicos':
	
		$T147_Id = (isset($r_T147_Id)) ? $r_T147_Id : false;
	
		$g['screen'] = 'big';
		$grid = array(
			array('colunaValor' => 'T149_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
			//array('colunaValor' => 'D111_Id', 'colunaTitulo' => 'ID'),
			array('colunaValor' => 'D111_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'T149_Quantidade', 'colunaTitulo' => 'Quantidade'),
			//array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado')
		);
		
		$from = 'T149';
//left join T147 on T148_T147_Id
		$extra = <<<EOT
			LEFT JOIN D111 ON D111_Id=T149_D111_Id
			WHERE T149_T147_Id='{$T147_Id}'
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
	   $novoIdTelaForm = uniqid();
	   echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		       var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
		       		// Deseleciona todos, seleciona a linha
		       		$('#{$g['divId']} .grid tr').removeClass('hover ui-state-highlight');
		       		$(this).closest('tr').addClass('hover ui-state-highlight');
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
		$g['smarty']->display('big.grid.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		//$botoesDir .= '<button type="button" id="srv003GridOperacaoBtnSelecionar">Selecionar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('big.buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#srv003GridOperacaoBtnSelecionar').unbind('click').bind('click', function(data) {
				var \$tr = $('#{$g['divId']}').find('.grid-select tr.hover');
				// pega o ID da linha
				var id = \$tr.attr('id').split('|');
				var acaoId = id[0].replace('T147_Id-', '');
		       	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Iniciar Serviço: ' + acaoId, '/srv/srv003/outros/srv003OutrosServico/', '&acaoId=' + encodeURIComponent(acaoId), 'full');
			});			
		} </script></div>
EOT;
	break;
	
	case 'srv003GridOperacaoAprovar':
		
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		
		$g['screen'] = 'big';
		$sql =<<<EOT
		 SELECT * FROM T147
		 left join D110 on D110_Id=T147_D110_Id
		 left join D115 on D115_D110_Id=D110_Id
		 left join D109 on D109_Id=D115_D109_Id
		 where (T147_Status='P') AND D109_Id='{$acaoId}' AND T147_Prioridade <>0 ORDER BY T147_Prioridade
EOT;
		$result = mysql_query($sql);
		$linha = mysql_num_rows($result);
		
		//if($linha > 0){
			$grid = array(
				array('colunaValor' => 'T147_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'T147_Prioridade', 'colunaTitulo' => 'Prd', 'colunaAlinhar' => 'left', 'callback'=>'validaCampoPrioridade'),
				array('colunaValor' => 'T147_Titulo', 'colunaTitulo' => 'Serviço', 'colunaAlinhar' => 'left'),
				array('colunaValor' => 'Produto', 'colunaTitulo' => 'Produto', 'colunaTipo'=>'livre', 'callback'=>'validaCampoProduto'),
				array('colunaValor' => 'D116_Modelo', 'colunaTitulo' => 'Veículo', 'callback'=>'validaCampoVeiculoSrv003'),
				array('colunaValor' => 'D116_Cor', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
				array('colunaValor' => 'D116_Placa', 'colunaTitulo' => 'Veículo', 'colunaExibir'=>false),
				array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'center', 'callback'=>'validaCampoCliente'),
				array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'center'),
				array('colunaValor' => 'T147_T006_Id', 'colunaTitulo' => 'idItensPedido', 'colunaExibir' => false),
				array('colunaValor' => 'T147_Tempo_Estimado', 'colunaTitulo' => 'Tmp Est', 'callback' => 'gCorrigeTempo', 'colunaAlinhar' => 'center')
			);
			
			$from = 'T147';
	
			$extra = <<<EOT
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on T005_D024_Id=D024_Id
				left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
				left join D110 on D110_Id=T147_D110_Id
				
				left join D116 on D116_Id=T147_D116_Id
				where (T147_Flag_Conferido<>'S' AND T147_Status='C')
				ORDER BY T147_Prioridade
EOT;
		//} 
		
		// left join D115 on D115_D110_Id=D110_Id
		// left join D109 on D109_Id=D115_D109_Id
		
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		
		//BLOCO COMENTADO PARA NÃO SER POSSÍVEL SELECIONAR OUTRA LINHA DO GRID.
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
		       		// Deseleciona todos, seleciona a linha
		       		//$('#{$g['divId']} .grid tr #iconesetar').remove();
		       		$('#{$g['divId']} .grid tr').removeClass('hover ui-state-highlight');
		       		var td = $(this).closest('tr').find('td:first');
		       		$(this).closest('tr').addClass('hover ui-state-highlight');
		       		// icone
		       		//var icone = $('<img>').attr({'src': '/hardness3/static/img/icone_setad.png', 'id': 'iconesetar'});
		       		//td.before(icone);
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
		$g['smarty']->display('big.grid.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		
		// $sql = "select * from T147 WHERE T147_D109_Id='{$acaoId}' AND T147_Status='E'";
		// $result = mysql_query($sql);
		
		$sql2 = "select T147_D109_Id, T147_Status from T147";
		$result2 = mysql_query($sql2);
		
		$botoesEsq = $botoesDir = $botoesMid = '';
		$botoesDir .= '<button type="button" id="srv003GridOperacaoBtnSelecionarAprovar">Visualizar Serviço</button>';
		$botoesEsq .= '<button type="button" onclick="window.location = \'/srv/srv003/content/srv003ContentLogin/?oneWindow=true\'; return false;">Sair</button>';
		while($linha = mysql_fetch_array($result2)){
			if(($linha['T147_D109_Id'] == $acaoId) && (($linha['T147_Status']=='P') || ($linha['T147_Status']=='E'))){
				$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacao/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Meus Serviços</button>';
				$botoesEsq .= '<button class="btnFechar" onclick="window.location=\'/srv/srv003/content/srv003ContentOperacaoPendentes/?oneWindow=true&acaoId='.$r_acaoId.'\'; return false;">Fila de Serviços</button>';
				break;
			}
		}
		
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('big.buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#srv003GridOperacaoBtnSelecionarAprovar').unbind('click').bind('click', function(data) {
				var \$tr = $('#{$g['divId']}').find('.grid-select tr.hover');
				// pega o ID da linha
				var id = \$tr.attr('id').split('|');
				var acaoId = id[0].replace('T147_Id-', '');
				window.location = '/srv/srv003/content/srv003ContentAprovarServico/?oneWindow=true&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&T147_Id=' + encodeURIComponent(acaoId);
		       	//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Iniciar Serviço: ' + acaoId, '/srv/srv003/outros/srv003OutrosServico/', '&acaoId=' + encodeURIComponent(acaoId), 'full');
			});			
		} </script></div>
EOT;
	break;
}
?>
